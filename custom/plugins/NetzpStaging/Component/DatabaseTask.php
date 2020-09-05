<?php namespace NetzpStaging\Component;

use NetzpStaging\Component\Anonymizer;

class DatabaseTask extends Task
{
    const DIR_DBBACKUPS = 'files/_dbbackups';

    private $dbTest = null;
    private $isBackup = false;
    private $anonymize = 0;

    public function setBackup($value) {
        $this->isBackup = $value;
    }
    public function setAnonymize($value) {
        $this->anonymize = $value;
    }

    public function run() {

        $this->setState(self::STATE_RUNNING);
        $this->setProfileStatus(self::PROFILE_STATUS_CREATING);
        $this->setProgress(0, 'Start Datenbank', true);
        $this->dbTest = $this->helper->connectToDatabase($this->getDbConfig());

        try {
            if($this->isDeleting()) {
                $this->deleteDatabase();
            }

            else if($this->isBackup) {
                $this->extendRuntime();
                $exportDir = $this->getLiveDir() . '/' . self::DIR_DBBACKUPS;
                $dbTempFile = $this->exportDatabase($exportDir, true);
            }

            else {
                $this->extendRuntime();
                $this->deleteDatabase();

                $exportDir = $this->getLiveDir() . '/' . self::DIR_DBBACKUPS;
                $dbTempFile = $this->exportDatabase($exportDir);

                if($dbTempFile != '') {
                    $this->dbTest = null;
                    $this->dbTest = $this->helper->connectToDatabase($this->getDbConfig());
                    $this->extendRuntime();
                    $this->importDatabase($dbTempFile);

                    $this->helper->correctShopPaths($this->getProfile());
                }
            }
        }
        catch(Exception $ex) {
            // $this->setState(self::STATE_ABORTED);
            // $this->setProfileStatus(self::STATE_ERROR);
            $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Fehler', [$ex->getMessage()]);
        }
        finally {
            $this->extendRuntime();
            $this->setState(self::STATE_DONE);
            $this->setProfileStatus($this->isDeleting() ? self::PROFILE_STATUS_NEW : self::PROFILE_STATUS_READY);
            $this->setProgress(100, 'Datenbank: fertig.', true);

            if ( ! $this->isDeleting()) {
                $this->helper->updateConfigTest($this->getProfile(), $this->getTestDir(), false);
                $this->helper->updateShopOptions($this->getProfile(), $this->getLiveDir());
                if ( ! $this->isBackup) {
                    $this->setProfileCreationDate();
                }
            }
            $this->dbTest = null;
        }

        return '';
    }

    function exportDatabase($dir, $compress = false) {
        require_once(__DIR__ . '/../lib/Mysqldump.php');

        $this->setProgress(-1, 'Exportiere Live-Datenbank', true);
        $dbconfigLive = $this->getDbConfigLive();
        $dumpSettings = [
            'add-drop-table' => true,
            'no-data' => []
        ];
        if($this->anonymize == 2) {
            $dumpSettings['no-data'] = [
                's_user',
                's_user_addresses',
                's_user_addresses_attributes',
                's_user_attributes',
                's_user_billingaddress',
                's_user_billingaddress_attributes',
                's_user_shippingaddress',
                's_user_shippingaddress_attributes',

                's_order',
                's_order_attributes',
                's_order_basket',
                's_order_basket_attributes',
                's_order_basket_signatures',
                's_order_billingaddress',
                's_order_billingaddress_attributes',
                's_order_comparisons',
                's_order_details',
                's_order_details_attributes',
                's_order_documents',
                's_order_documents_attributes',
                's_order_esd',
                's_order_history',
                's_order_notes',
                's_order_shippingaddress',
                's_order_shippingaddress_attributes',

                's_campaigns_mailaddresses',
                's_campaigns_maildata'
            ];
        }

        // exclude some really large & nasty tables (data only)
        $excludeTables = trim($this->helper->getConfig('netzpStagingExcludeTables'));
        if($excludeTables != '') {
            $excludeTables = explode(';', $excludeTables);
            $dumpSettings['no-data'] = array_merge($dumpSettings['no-data'], $excludeTables);
            // typical tables to exclude:
            // - bestit_amazon_cache
            // - esha_email_archive_mail
            // - s_import_export_session
        }

        $dump = new \Ifsnop\Mysqldump\Mysqldump (
            $this->helper->getPDOConnectionString($dbconfigLive), 
            $dbconfigLive['username'], $dbconfigLive['password'],
            $dumpSettings
        );

        if ( ! is_dir($dir)) {
            mkdir($dir, 0755);
        }

        $dbExportFile = 'db_export_' . date('Y_m_d_H_i') . '_' . uniqid() . '.php';
        $path = $dir . '/' . $dbExportFile;

        if($this->anonymize == 1) {
            $anonymizer = new Anonymizer();
            $dump->setTransformColumnValueHook([$anonymizer, 'anonymize']);
        }

        try {
            $dump->start($path);
            if($compress) {
                $pathInfo = pathinfo($path);
                $zipFile = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.zip';
                $zip = new \ZipArchive();
                $zip->open($zipFile, \ZipArchive::CREATE);
                $zip->addFile($path, basename($pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.sql'));
                $zip->close();
                @unlink($path);

                $this->setProgress('Live-Datenbank exportiert (ZIP-Archiv)', '', true);

                return $zipFile;
            }
            else {
                $this->setProgress('Live-Datenbank exportiert', '', true);
                return $path;
            }
        }
        catch (\Exception $ex) {
            $this->setState(self::STATE_ABORTED);
            $this->setProfileStatus(self::STATE_ERROR);
            $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Fehler', [$ex->getMessage()]);
            return '';
        }
    }

    function deleteDatabase() {

        $this->setProgress('Lösche Test-Datenbank', '', true);

        $dbConfig = $this->getDbConfig();
        $dbConfigLive = $this->getDbConfigLive();

        if($dbConfigLive['host'] == $dbConfig['host'] &&
           $dbConfigLive['dbname'] == $dbConfig['dbname'] &&
           $dbConfigLive['username'] == $dbConfig['username'] &&
           $dbConfigLive['password'] == $dbConfig['password']) 
        {
            $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Schwerer Fehler', 
                ['Die Testserver-Datenbank ist gleich der Live-Datenbank']);
            die;
        }

        $schemaTest = $this->getDbConfig()['dbname'];

        $this->dbTest->exec('SET NAMES utf8');
        $this->dbTest->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->dbTest->exec('SET SESSION wait_timeout = 28800');
        $this->dbTest->exec('SET SESSION interactive_timeout = 28800');

        $sqlCount = 'SELECT count(table_name) as count
                     FROM information_schema.tables
                     WHERE table_schema = ' . $this->dbTest->quote($schemaTest);
        $q = $this->dbTest->query($sqlCount);
        $tableCount = $q->fetchColumn();

        $sql = 'SELECT concat("DROP TABLE IF EXISTS `", table_name, "`;")
                FROM information_schema.tables
                WHERE table_schema = ' . $this->dbTest->quote($schemaTest);

        $n = 0;
        foreach($this->dbTest->query($sql) as $row) {
            if($n % 10 == 0) {
                $percent = round($n / $tableCount * 100);
                $this->checkAbort();
                $this->setProgress($percent, 'Lösche Test-Datenbank: ' . $percent . '%');
            }
            $n++;

            try {
                $ok = $this->dbTest->exec($row[0]);
                if($ok === false) {
                    $this->helper->log(
                        $this->getLiveDir(), $this->getProfile(), 'Fehler DB-Delete', 
                        [$this->dbTest->errorInfo(), $row[0]]
                    );
                }
            }
            catch (\PDOException $ex) {
                $this->setState(self::STATE_ABORTED);
                $this->setProfileStatus(self::STATE_ERROR);
                $this->helper->log(
                    $this->getLiveDir(), $this->getProfile(), 'Fehler DB-Delete', 
                    [$ex->getMessage(), $row[0]]
                );
            }
        }
        $this->setProgress(100, 'Lösche Test-Datenbank: fertig', true);

        $this->dbTest->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    function countDatabaseEntries($path) {

        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);

        return $file->key() + 1;
    }

    function importDatabase($path) {

        $this->setProgress('Importiere Live-Datenbank', '', true);
        $this->dbTest->exec('SET NAMES utf8');
        $this->dbTest->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->dbTest->exec('SET SESSION wait_timeout = 28800');
        $this->dbTest->exec('SET SESSION interactive_timeout = 28800');

        $this->dbTest->exec('SET SESSION sql_mode = (SELECT replace(@@SESSION.sql_mode,"NO_ZERO_DATE,", ""))');
        $this->dbTest->exec('SET SESSION sql_mode = (SELECT replace(@@SESSION.sql_mode,"NO_ZERO_IN_DATE,", ""))');


        $fh = new \SplFileObject($path);
        $templine = '';
        $count = $this->countDatabaseEntries($path);
        $n = 0;

        while ( ! $fh->eof()) {
            if($n % 100 == 0) {
                $percent = round($n / $count * 100);
                $this->checkAbort();
                $this->setProgress($percent, 'Importiere Datenbank: ' . $percent . '%');
            }
            if($n % 10000 == 0) {
                $this->extendRuntime();
            }
            $n++;

            $line = trim($fh->fgets());
            $sStart = substr($line, 0, 2);
            if ($line == '' || $sStart == '--' || $sStart == '/*') {
                continue;
            }

            $templine .= $line;
            if (substr($line, -1, 1) == ';') {
                $templine = str_replace(') TYPE=InnoDB', ') ENGINE=InnoDB', $templine);
                try {
                    $ok = $this->dbTest->exec($templine);
                    if($ok === false) {
                        $this->helper->log(
                            $this->getLiveDir(), $this->getProfile(), 'Fehler DB-Import', 
                            [$this->dbTest->errorInfo(), $templine]
                        );
                    }
                }
                catch (\PDOException $ex) {
                    // $this->setState(self::STATE_ABORTED);
                    // $this->setProfileStatus(self::STATE_ERROR);
                    $this->helper->log(
                        $this->getLiveDir(), $this->getProfile(), 'Fehler DB-Import', 
                        [$ex->getMessage(), $templine]
                    );
                }
                $templine = '';
            }
        }

        $this->dbTest->exec('TRUNCATE TABLE netzp_staging_profiles');
        $this->dbTest->exec('TRUNCATE TABLE netzp_staging_keystore');
        $this->dbTest->exec('TRUNCATE TABLE netzp_staging_files');
        $this->dbTest->exec('SET FOREIGN_KEY_CHECKS = 1');
        $fh = null;

        if ( ! $this->isBackup) {
            @unlink($path);
        }

        $this->setProgress(100, 'Importiere Datenbank', 'fertig');
    }
}