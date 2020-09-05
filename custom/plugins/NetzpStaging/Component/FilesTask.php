<?php namespace NetzpStaging\Component;

class FilesTask extends Task
{
    private $dbLive = null;
    private $useShell = false;
    private $useRSync = false;

    public function run() {

        if($this->helper->getConfig('netzpStagingCopyMethod') == 1) {
            $this->useShell = false;
        }
        else if($this->isEnabled('exec') && exec('echo OK') == 'OK') {
            $this->useShell = true;
            if(exec('rsync --version') != '') {
                $this->useRSync = true;
            }
        }

        $this->setState(self::STATE_RUNNING);
        $this->setProfileStatus(self::PROFILE_STATUS_CREATING);
        $this->dbLive = $this->helper->connectToDatabase($this->getDbConfigLive());

        if($this->useShell) {
            $this->processUsingShell();
        }
        else {
            $this->processUsingPHP();            
        }
    }

    function isEnabled($func) {
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        return is_callable($func) && array_search($func, $disabledFunctions) === false;
    }

    function processUsingShell() {
        $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Methode ', [
            'Shell', $this->useRSync ? 'rsync' : 'tar'
        ]);

        try {
            $dirsExcluded = $this->getExcludedDirs();
            $dirsExcluded[] = '/config.php'; // don't copy config.php with live database connection!

            $cmdDelete = 'rm -r ' . $this->_testDir;

            if($this->useRSync) {
                $cmdCopy  = 'mkdir -p ' . $this->_testDir . ';';
                $cmdCopy .= 'rsync -aq '. $this->_liveDir . '/ ' . $this->_testDir . '/';
                foreach($dirsExcluded as $exclude) {
                    $cmdCopy .= ' --exclude ' . $exclude;
                }
            }
            else { // usr tar
                $cmdCopy  = 'mkdir -p ' . $this->_testDir . ';';
                $cmdCopy .= 'cd ' . $this->_liveDir . ';';
                $cmdCopy .= 'tar ';
                foreach($dirsExcluded as $exclude) {
                    $cmdCopy .= " --exclude='." . $exclude . "'";
                }
                $cmdCopy .= ' -cf - . | ';
                $cmdCopy .= '(cd ' . $this->_testDir . ' && tar xf -)';
            }

            $this->setProgress(-1, 'Lösche Dateien Testumgebung', true);
            exec($cmdDelete);
            $this->setProgress(100, 'Lösche Testumgebung: fertig', true);

            if (! $this->isDeleting()) {
                $this->setProgress(-1, 'Kopiere Dateien', true);

                // don't copy config.php - if something fails, there wont be any live config in the test server!
                $fileDest = $this->_testDir . '/config.php';
                @mkdir(dirname($fileDest), 0777, true);
                @file_put_contents($fileDest, '<?php return[];');

                exec($cmdCopy);
                $this->createFilesIfNeeded();
                $this->setProgress(100, 'Dateien: fertig', true);
                
                $this->helper->modifyConfig($this->getProfile(), $this->getTestDir());
                $this->helper->updateConfigTest($this->getProfile(), $this->getTestDir());
            }
        }

        catch(Exception $ex) {
            $this->setState(self::STATE_ABORTED);
            $this->setProfileStatus(self::STATE_ERROR);
            $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Fehler', [$ex->getMessage()]);
        }

        finally {
            $this->extendRuntime();
            $this->setState(self::STATE_DONE);
            $this->setProfileStatus($this->isDeleting() ? self::PROFILE_STATUS_NEW : self::PROFILE_STATUS_READY);
            $this->setProgress(100, 'Dateien: fertig.', true);

            if ( ! $this->isDeleting()) {
                $this->helper->updateShopOptions($this->getProfile(), $this->getLiveDir());
                $this->setProfileCreationDate();
            }
            $this->dbLive = null;
        }
    }
    
    function processUsingPHP() {
        $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Methode ', ['PHP']);
        try {
            if ($this->isDeleting()) {
                $this->deleteFiles();
            }
            else {
                $this->collectFiles();
                $this->extendRuntime();
                $this->deleteFiles();
                $this->extendRuntime();
                $this->copyFiles();

                $this->helper->modifyConfig($this->getProfile(), $this->getTestDir());
                $this->helper->updateConfigTest($this->getProfile(), $this->getTestDir());
            }
        }

        catch(Exception $ex) {
            $this->setState(self::STATE_ABORTED);
            $this->setProfileStatus(self::STATE_ERROR);
            $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Fehler', [$ex->getMessage()]);
        }

        finally {
            $this->extendRuntime();
            $this->setState(self::STATE_DONE);
            $this->setProfileStatus($this->isDeleting() ? self::PROFILE_STATUS_NEW : self::PROFILE_STATUS_READY);
            $this->setProgress(100, 'Dateien: fertig.', true);

            if ( ! $this->isDeleting()) {
                $this->helper->updateShopOptions($this->getProfile(), $this->getLiveDir());
                $this->setProfileCreationDate();
            }
            $this->dbLive = null;
        }

        return '';
    }

    function collectFiles() {

        $this->setProgress(-1, 'Lösche Dateiliste', true);
        $this->dbLive->query('DELETE FROM netzp_staging_files WHERE profileid = ' . 
                             (int)$this->getProfileId()
        );
        $this->setProgress(100, 'Lösche Dateiliste: fertig', true);
        $this->logProgress(100, 'Dateien ermitteln');

        $liveDir = $this->getLiveDir();
        $dirsExcluded = $this->getExcludedDirs();

        // Dateien vom LIVE-Server ermitteln
        $filter = function ($file, $key, $iterator) use ($dirsExcluded, $liveDir) {

            if ($iterator->hasChildren()) {
                $path = str_replace($liveDir . '/', '', $file->getPathname());
                $include = ! in_array($path, $dirsExcluded);
                return $include;
            }

            return $file->isFile();
        };

        $iter = new \RecursiveIteratorIterator(
                    new \RecursiveCallbackFilterIterator(
                        new \RecursiveDirectoryIterator($liveDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    $filter),
                    \RecursiveIteratorIterator::SELF_FIRST,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD // ignore "Permission denied"
                );

        $n = 0;
        $statement = $this->dbLive->prepare(
            'INSERT INTO netzp_staging_files (profileid, file, size, copied) VALUES (?, ?, ?, 0)'
        );
        $this->dbLive->beginTransaction();
        try {
            foreach($iter as $name => $object) {
                $fs = 0;
                if(@is_file($name)) {
                    $fs = @filesize($name);
                }
                $statement->execute(array($this->getProfileId(), $name, $fs));

                if($n % 100 == 0) {
                    $this->checkAbort();
                    $this->setProgress(-1, 'Dateien ermitteln: ' . $n);
                }
                $n++;
            }
        }
        catch (Exception $ex) {
            $this->setState(self::STATE_ABORTED);
            $this->setProfileStatus(self::STATE_ERROR);
            $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Fehler', [$ex->getMessage()]);
        }
        finally {
            $this->dbLive->commit();
        }

        $this->setProgress(100, 'Dateien ermitteln: fertig', true);
    }

    function deleteFiles() {

        $this->setProgress('Lösche Dateien Testumgebung', '', true);

        // Dateien vom TEST-Server löschen
        $profile = $this->getProfile();
        $testDir = $this->getTestDir();
        if($testDir == $this->getLiveDir()) {
            $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Schwerer Fehler', 
                ['Das Testserver-Verzeichnis ist gleich dem Live-Verzeichnis (' . $testDir . ')']);
            die;
        }

        if(is_dir($testDir)) {

            $dirsNotSynced = [];
            if((int)$profile['creationsfiles'] > 1 && ! $this->isDeleting()) {
                $dirsNotSynced = $profile['dirsnotsynced'];
            }

            $filter = function ($file, $key, $iterator) use ($dirsNotSynced, $testDir) {
                if ($iterator->hasChildren()) {
                    $path = str_replace($testDir . '/', '', $file->getPathname());
                    $include = ! in_array($path, $dirsNotSynced);
                    return $include;
                }

                return $file->isFile();
            };
            $files = new \RecursiveIteratorIterator(
                new \RecursiveCallbackFilterIterator(
                    new \RecursiveDirectoryIterator($testDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                $filter),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            $n = 0;
            try {
                foreach ($files as $fileinfo) {
                    if($n % 1000 == 0) {
                        $this->checkAbort();
                        $this->setProgress(-1, 'Lösche Dateien Testumgebung: ' . $n);
                    }

                    $cmd = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    @$cmd($fileinfo->getRealPath());

                    $n++;
                }
                if($this->isDeleting()) {
                    rmdir($this->getTestDir());
                }
            }
            catch (Exception $ex) {
                $this->setState(self::STATE_ABORTED);
                $this->setProfileStatus(self::STATE_ERROR);
                $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Fehler', [$ex->getMessage()]);
            }

        }
        $this->setProgress(100, 'Lösche Testumgebung: fertig', true);
    }

    function copyFiles() {

        $this->setProgress('Kopiere Dateien', '', true);

        $q = $this->dbLive->query('SELECT count(id) as count FROM netzp_staging_files WHERE profileid = ' . 
                                  (int)$this->getProfileId());
        $fileCount = $q->fetchColumn();
        $n = 0;

        $sql = 'SELECT id, file FROM netzp_staging_files WHERE profileid = ' . (int)$this->getProfileId();
        foreach ($this->dbLive->query($sql) as $record) {
            $copyThisFile = true;
            $file = $record['file'];

            $newFileName = $file;
            $newFileName = str_replace($this->getLiveDir() . '/', '', $newFileName);

            if($newFileName == 'custom/plugins/NetzpStaging/lib/filemanager/filemanager.php') {
                $fileDest = $this->getTestDir() . '/filemanager.php';
            }
            else if($newFileName == 'custom/plugins/NetzpStaging/lib/filemanager/filemanager_config.php') {
                $fileDest = $this->getTestDir() . '/filemanager_config.php';
            }
            else if($newFileName == 'config.php') {
                // don't copy config.php - if something fails, there wont be any live config in the test server!
                $copyThisFile = false;
                $fileDest = $this->getTestDir() . '/' . $newFileName;
                @mkdir(dirname($fileDest), 0777, true);
                @file_put_contents($fileDest, '<?php return[];');
            }
            else {
                $fileDest = $this->getTestDir() . '/' . $newFileName;
            }

            if (@is_file($file) && $copyThisFile) {
                @mkdir(dirname($fileDest), 0777, true);
                if ( ! @copy($file, $fileDest)) {
                    $this->helper->log(
                        $this->getLiveDir(), $this->getProfile(), 'Fehler - Dateikopie', 
                        [$file, $fileDest]
                    );
                }
            }
            $this->dbLive->query('UPDATE netzp_staging_files SET copied = 1 WHERE id = ' . $record['id']);

            if($n % 10 == 0) {
                $percent = round($n / $fileCount * 100);
                $this->checkAbort();
                $this->setProgress($percent, 'Kopiere Dateien: ' . $percent . '%');
            }
            $n++;
        }

        $this->createFilesIfNeeded();
        $this->setProgress(100, 'Dateien: fertig', true);
    }

    function createFilesIfNeeded() {

        @chmod($this->getTestDir(), 0755);
        
        @mkdir($this->getTestDir() . '/media', 0777, true);
        @mkdir($this->getTestDir() . '/media/archive', 0777, true);
        @mkdir($this->getTestDir() . '/media/image', 0777, true);
        @mkdir($this->getTestDir() . '/media/music', 0777, true);
        @mkdir($this->getTestDir() . '/media/pdf', 0777, true);
        @mkdir($this->getTestDir() . '/media/temp', 0777, true);
        @mkdir($this->getTestDir() . '/media/unknown', 0777, true);
        @mkdir($this->getTestDir() . '/media/vector', 0777, true);
        @mkdir($this->getTestDir() . '/media/video', 0777, true);

        @mkdir($this->getTestDir() . '/var/cache', 0777, true);
        @mkdir($this->getTestDir() . '/var/log', 0777, true);
        @mkdir($this->getTestDir() . '/web/cache', 0777, true);
        @mkdir($this->getTestDir() . '/web/sitemap', 0777, true);

        @mkdir($this->getTestDir() . '/files/backup', 0777, true);
        @mkdir($this->getTestDir() . '/files/documents', 0777, true);
        @mkdir($this->getTestDir() . '/files/downloads', 0777, true);
        @mkdir($this->getTestDir() . '/files/update', 0777, true);

        @copy($this->getLiveDir() . '/custom/plugins/NetzpStaging/lib/filemanager/filemanager.php',
              $this->getTestDir() . '/filemanager.php');
        @copy($this->getLiveDir() . '/custom/plugins/NetzpStaging/lib/filemanager/filemanager_config.php',
              $this->getTestDir() . '/filemanager_config.php');
    }

    function getExcludedDirs() {

        $profile = $this->getProfile();
        $dbConfig = $profile['dbconfig'];

        $dirsExcluded = $profile['dirsexcluded'];
        $dirsExcluded[] = 'web/cache';
        $dirsExcluded[] = 'files/_dbbackups';
        
        if(array_key_exists('anonymize', $dbConfig) && (int)$dbConfig['anonymize'] >= 1) {
            $dirsExcluded[] = 'files/documents';
        }

        $profiles = $this->helper->getProfiles();
        foreach($profiles as $p) {
            $dirsExcluded[] = $p['dirname']; // automatically exclude all other testservers
        }

        $dirsExcluded = array_merge($dirsExcluded, 
            array_map(function($item) {
                return '/var/cache/' . basename($item);
            }, glob($this->getLiveDir() . '/var/cache/*', GLOB_ONLYDIR))
        );
        $dirsExcluded = array_unique($dirsExcluded);

        if((int)$profile['creationsfiles'] > 1) {
            $dirsNotSynced = $profile['dirsnotsynced'];
            $dirsExcluded = array_merge($dirsExcluded, $dirsNotSynced);
        }

        $dirsExcluded = array_map(function($item) {
            return '/' . trim($item, '/');
        }, $dirsExcluded);

        return $dirsExcluded;
    }
}
