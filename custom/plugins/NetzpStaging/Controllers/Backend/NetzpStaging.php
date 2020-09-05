<?php
use Shopware\Components\CSRFWhitelistAware;
use Doctrine\Common\Collections\ArrayCollection;

use NetzpStaging\Component\Task;
use NetzpStaging\Component\WorkerTask;
use NetzpStaging\Component\FilesTask;
use NetzpStaging\Component\DatabaseTask;

class Shopware_Controllers_Backend_NetzpStaging extends \Enlight_Controller_Action
                                                implements CSRFWhitelistAware
{
    private $helper = null;
    private $checksok = false;
    private $cmd = '';
    private $tab = '';
    private $oldcmd = '';
    private $msg = [];
    private $profile = [];
    private $diff = [];
    private $diffFile = [];
    private $liveDir = '';

    const DIR_DBBACKUPS = 'files/_dbbackups';
    const DIR_LOGS = 'var/log';

    public function getWhitelistedCSRFActions()
    {
        return [
            'index', 'worker', 'dirs', 'download',
            'files', 'database', 'deleteFiles', 'deleteDatabase', 'backup', 'reset'
        ];
    }

    public function preDispatch()
    {
        $this->helper = Shopware()->Container()->get('netzp_staging.helper');
        $pluginPath = Shopware()->Container()->getParameter('netzp_staging.plugin_dir');
        $this->get('template')->addTemplateDir($pluginPath . '/Resources/views/');
        $this->liveDir = getcwd();
    }

    public function workerAction()
    {
        $task = $this->Request()->getParam('task');
        $cmd = $this->Request()->getParam('cmd');
        $profileId = $this->Request()->getParam('profile');

        $workerTask = new WorkerTask($task, $profileId, TASK::TASK_WORKER, $cmd);
        $body = $workerTask->run();
        $this->setResponseData($body);
    }

    public function resetAction()
    {
        $workerTask = new WorkerTask('reset', 0, TASK::TASK_WORKER, 'reset');
        $body = $workerTask->run();
        $this->setResponseData($body);
    }

    public function filesAction()
    {
        $profileId = $this->Request()->getParam('profile');
        $profile = $this->helper->getProfile($profileId);
        $configLive = $this->helper->readConfigFile($this->liveDir);

        $filesTask = new FilesTask('files', $profileId, TASK::TASK_FILES);
        $filesTask->setParams($this->liveDir, $this->liveDir . '/' . $profile['dirname'], 
                              $profile, $configLive);

        $body = $filesTask->run();
        $this->setResponseData($body);
    }

    public function databaseAction()
    {
        $profileId = $this->Request()->getParam('profile');
        $profile = $this->helper->getProfile($profileId);
        $configLive = $this->helper->readConfigFile($this->liveDir);

        $databaseTask = new DatabaseTask('database', $profileId, TASK::TASK_DATABASE);
        $databaseTask->setAnonymize((int)$profile['dbconfig']['anonymize']);
        $databaseTask->setParams($this->liveDir, $this->liveDir . '/' . 
                                 $profile['dirname'], $profile, $configLive);

        $body = $databaseTask->run();
        $this->setResponseData($body);
    }

    public function backupAction()
    {
        $profileId = $this->Request()->getParam('profile');
        $profile = $this->helper->getProfile($profileId);
        $configLive = $this->helper->readConfigFile($this->liveDir);

        $databaseTask = new DatabaseTask('database', $profileId, TASK::TASK_DATABASE);
        $databaseTask->setBackup(true);
        $databaseTask->setAnonymize(false, false);
        $databaseTask->setParams($this->liveDir, $this->liveDir . '/' . 
                                 $profile['dirname'], $profile, $configLive);

        $body = $databaseTask->run();
        $this->setResponseData($body);
    }

    public function deleteFilesAction()
    {
        $profileId = $this->Request()->getParam('profile');
        $profile = $this->helper->getProfile($profileId);
        $configLive = $this->helper->readConfigFile($this->liveDir);

        $filesTask = new FilesTask('files', $profileId, TASK::TASK_FILES);
        $filesTask->setDelete(true);
        $filesTask->setParams($this->liveDir, $this->liveDir . '/' . $profile['dirname'], 
                              $profile, $configLive);

        $body = $filesTask->run();
        $this->setResponseData($body);
    }

    public function deleteDatabaseAction()
    {
        $profileId = $this->Request()->getParam('profile');
        $profile = $this->helper->getProfile($profileId);
        $configLive = $this->helper->readConfigFile($this->liveDir);

        $databaseTask = new DatabaseTask('database', $profileId, TASK::TASK_DATABASE);
        $databaseTask->setDelete(true);
        $databaseTask->setParams($this->liveDir, $this->liveDir . '/' . 
                                 $profile['dirname'], $profile, $configLive);

        $body = $databaseTask->run();
        $this->setResponseData($body);
    }

    public function indexAction()
    {
        if ( ! $this->helper->isAllowed('create', 'netzpStaging')) {
            return;
        }
        session_write_close(); // avoid session locks

        $this->checksok = $this->helper->checkPrerequisites();
        $this->cmd = $this->Request()->getParam('cmd');
        $this->tab = $this->Request()->getParam('tab') ? $this->Request()->getParam('tab') : 'profiles';
        $this->msg = [];

        if( ! $this->checksok['maxexecutiontime']) {
            $this->addMessage(
                'Bitte erhöhen Sie die PHP-Einstellung <i>max_execution_time</i> auf mindestens 1200.', 2
            );
            $this->executeCommand();
        }
        else {
            $this->executeCommand();
        }
        $this->assignViewData();
    }

    public function downloadAction()
    {
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();

        $fileName = trim($this->Request()->getParam('f'));
        $type = trim($this->Request()->getParam('t'));
        $fileName = str_replace('/', '', $fileName);

        if($type == '' || $type == 'backup') {
            $path = self::DIR_DBBACKUPS . '/' . $fileName;
        }
        else if($type == 'log') {
            $fileName = 'netzp_staging.log';
            $path = self::DIR_LOGS . '/' . $fileName;
        }

        if(@file_exists($path)) {
            @set_time_limit(0);
            $response = $this->Response();
            $response->setHeader('Cache-Control', 'public');
            $response->setHeader('Content-Type', 'application/zip');
            $response->setHeader('Content-Description', 'File Transfer');
            $response->setHeader('Content-Transfer-Encoding', 'binary');
            $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
            $response->setHeader('Content-Length', @filesize($fileName));
            $response->setHeader('Expires', '0');
            $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
            readfile($path);
        }
        else {
            die("ts...");
        }
    }

    public function dirsAction()
    {
        // see: https://www.jstree.com/docs/json/
        $profileId = (int)$this->Request()->getParam('profileid');
        $this->profile = $this->helper->getProfile($profileId);
        $dirsExcluded = json_decode($this->profile['dirsexcluded']);
        $dirsNotsynced = json_decode($this->profile['dirsnotsynced']);

        $id = $this->Request()->getParam('id') != null ? $this->Request()->getParam('id') : '#';
        if($id == '__dirsexcluded__') {
            $this->setResponseData($this->profile['dirsexcluded']);
        }
        else if($id == '__dirsnotsynced__') {
            $this->setResponseData($this->profile['dirsnotsynced']);
        }

        else {
            $baseDir = $this->Request()->getParam('basedir') != null ? 
                       $this->Request()->getParam('basedir') : '';

            $baseDir = trim($baseDir, '/') . ($baseDir != '' ? '/' : '');
            $baseDir = getcwd() . '/' . $baseDir;
            $dirs = glob($baseDir . '*', GLOB_ONLYDIR);

            $children = [];
            foreach($dirs as $dir) {
                $dirId = str_replace(getcwd() . '/', '', $dir);
                $dirName = basename($dir . '/');

                $children[] = [
                    'id' => $dirId,
                    'text' => $dirName,
                    'icon' => '/',
                    'children' => true,
                    'li_attr' => '',
                    'a_attr' => '',
                ];
            }

            if($id == '#') {
                $nodes = [
                    'id' => '_',
                    'text' => 'Verzeichnisauswahl',
                    'icon' => '/',
                    'state' => ['disabled' => true],
                    'children' => $children,
                    'li_attr' => '',
                    'a_attr' => '',
                ];    
                $this->setResponseData(json_encode($nodes));
            }
            else {
                $this->setResponseData(json_encode($children));
            }
        }
    }

    function executeCommand() {

        $id = $this->Request()->getParam('id');
        if($id == null || $id == '') {
            $id = 0;
        }

        $param = 0;
        $this->oldcmd = $this->cmd;
        $p = strpos($this->cmd, '.');
        if($p === false) {
            $cmd = $this->cmd;
        }
        else {
            $cmd = substr($this->cmd, 0, $p);
            $param = substr($this->cmd, $p+1);
        }

        if($cmd == 'newprofile') {
            $this->profile = [
                'id' => 0,
                'title' => '',
                'dirname' => '',
                'runfromcron' => 0,
                'dbconfig' => [ 
                    'host' => 'localhost',
                    'port' => '3306',
                    'name' => '',
                    'user' => '',
                    'pass' => ''
                ]
            ];
        }
        else if($cmd == 'save') {
            if ($this->checkProfileData()) {
                $this->helper->saveProfile($this->profile);
            }
            else {
                $cmd = 'edit';
            }
        }
        else if($cmd == 'save_settings') {
            if ($this->checkSettingsData()) {
                $this->helper->saveSettings($this->profile);
                $this->helper->updateShopOptions($this->profile, $this->liveDir);
            }
            else {
                $cmd = 'settings';
            }
        }
        else if($cmd == 'save_accessdata') {
            $this->sendAccessData();
            $cmd = '';
        }
        else if($cmd == 'edit' || $cmd == 'settings' || $cmd == 'accessdata') {
            $this->profile = $this->helper->getProfile($param);
        }
        else if($cmd == 'delete') {
            $this->profile = $this->helper->deleteProfile($param);
        }
        else if($cmd == 'delete_backup') {
            if($this->helper->deleteBackup(self::DIR_DBBACKUPS . '/' . $param)) {
                $this->addMessage('Das Datenbank-Backup wurde gelöscht.', 0);
            }
            $cmd = '';
        }
        else if($cmd == 'diff') {
            $this->profile = $this->helper->getProfile($param);
            $this->diff = $this->helper->diffFiles($this->liveDir, $this->profile);
        }
        else if($cmd == 'difffile') {
            $this->profile = $this->helper->getProfile($param);
            $this->diffFile = $this->helper->diffFile($this->liveDir, $this->profile, $param);
        }

        $this->cmd = $cmd;
    }

    function checkProfileData() {

        $ok = true;
        $id = (int)$this->sanitize($this->Request()->getParam('id'));
        $title = $this->sanitize($this->Request()->getParam('title'));
        $dirname = $this->sanitize($this->Request()->getParam('dirname'));
        $runfromcron = (int)$this->sanitize($this->Request()->getParam('runfromcron'));
        $dirsExcluded = json_decode($this->sanitize($this->Request()->getParam('dirsexcluded')));
        $dirsNotsynced = json_decode($this->sanitize($this->Request()->getParam('dirsnotsynced')));

        $dbConfig = $this->Request()->getParam('dbconfig');

        $dbCheck = $this->helper->checkDatabaseConnection($dbConfig, $this->liveDir, $title, $dirname, $id);
        if (-2 == $dbCheck) {
            $this->addMessage('Die Shopware-Konfiguration (config.php) wurde nicht gefunden.', 2);
            $ok = false;
        }
        else if (-3 == $dbCheck) {
            $this->addMessage('Sie haben die Zugangdaten des LIVE-Servers eingegeben. Bitte richten Sie eine zusätzliche Datenbank für die Testumgebung ein und geben diese Daten an.', 2);
            $ok = false;
        }
        else if (-4 == $dbCheck) {
            $this->addMessage('Es gibt bereits eine Testumgebung mit diesem Namen oder Verzeichnisnamen. Bitte geben Sie einen anderen ein.', 2);
            $ok = false;
        }
        else if (-5 == $dbCheck) {
            $this->addMessage('Es gibt bereits eine Testumgebung mit dieser Datenbank. Bitte verwenden Sie eine andere Datenbank.', 2);
            $ok = false;
        }
        else if (0 != $dbCheck) {
            $this->addMessage('Die Datenbank konnte nicht erreicht werden. Bitte prüfen Sie die Zugangsdaten.', 2);
            $ok = false;
        }
        if ('' == $title) {
            $this->addMessage('Bitte geben Sie einen Titel für die Testumgebung an.', 2);
            $ok = false;
        }

        $dirCheck = $this->helper->checkTestDirectory($dirname);
        if(-1 == $dirCheck) {
            $this->addMessage('Bitte geben Sie einen Verzeichnisnamen für die Testumgebung an.', 2);
            $ok = false;
        }
        else if (-2 == $dirCheck) {
            $this->addMessage('Sie können den angegebenen Verzeichnisnamen leider nicht für eine Testumgebung verwenden, bitte wählen Sie einen anderen.', 2);
            $ok = false;
        }
        else if(-3 == $dirCheck) {
            $this->addMessage('Der eingebene Verzeichnisname wurde korrigiert und angepasst. Bitte überprüfen Sie das Ergebnis und speichern Sie erneut.', 1);
            $ok = false;
        }

        $this->profile['id'] = $id;
        $this->profile['title'] = $title;
        $this->profile['dirname'] = $dirname;
        $this->profile['runfromcron'] = $runfromcron;
        $this->profile['dirsexcluded'] = $dirsExcluded;
        $this->profile['dirsnotsynced'] = $dirsNotsynced;
        $this->profile['dbconfig'] = $dbConfig;

        return $ok;
    }

    function checkSettingsData() {

        $ok = true;
        $id = (int)$this->sanitize($this->Request()->getParam('id'));
        $this->profile = $this->helper->getProfile($id);
        $settings = $this->Request()->getParam('settings');

        $this->profile['settings'] = $settings;

        return $ok;
    }

    function sendAccessData() {

        $id = (int)$this->sanitize($this->Request()->getParam('id'));
        $this->profile = $this->helper->getProfile($id);
        $email = $this->sanitize($this->Request()->getParam('email'));
        $name = $this->sanitize($this->Request()->getParam('name'));
        $name = strtolower(preg_replace('/[^a-zA-Z0-9-\.]/','', $name));

        $createBackendUser = $this->sanitize($this->Request()->getParam('backend') == 1);
        $createFilesUser = $this->sanitize($this->Request()->getParam('files') == 1);

        if($createBackendUser) {
            $passwordBackend = substr(hash('sha512', rand()), 0, 16);

            $encoder = Shopware()->PasswordEncoder()->getDefaultPasswordEncoderName();
            $hashBackend = Shopware()->PasswordEncoder()->encodePassword($passwordBackend, $encoder);
            $userBackend = $this->helper->createBackendUser(
                $this->liveDir, $this->profile, $name, $email, $hashBackend
            );
        }
        if($createFilesUser) {
            $passwordFiles = substr(hash('sha512', rand()), 0, 16);

            $hashFiles = password_hash($passwordFiles, PASSWORD_DEFAULT);
            $userFiles = $this->helper->createFilesUser(
                $this->liveDir, $this->profile, $name, $email, $hashFiles
            );
        }

        $shopUrl = $this->helper->getShopUrl();

        $msg = 'Guten Tag,' . PHP_EOL . PHP_EOL;
        $msg .= 'Für Sie wurde im Online-Shop ' . 
                $this->helper->getShop()->getName() . ' eine Testumgebung eingerichtet.' . 
                PHP_EOL . PHP_EOL;
                
        if($createBackendUser) {
            $msg .= 'Backend - Aufruf: ' . $shopUrl.'/'.$this->profile['dirname'].'/backend' . PHP_EOL;
            $msg .= 'Backend - Zugang: ' . $userBackend . ' / ' . $passwordBackend . PHP_EOL;
            $msg .= PHP_EOL;
        }

        if($createFilesUser) {
            $msg .= 'Dateimanager - Aufruf: '  . $shopUrl.'/'.$this->profile['dirname'].'/filemanager.php' . PHP_EOL;
            $msg .= 'Dateimanager - Zugang: ' . $userFiles . ' / ' . $passwordFiles . PHP_EOL;
            $msg .= PHP_EOL;
        }
        
        $msg .= PHP_EOL . 
                'Bitte behandeln Sie die erhaltenen Zugangsdaten absolut vertraulich und informieren uns, sobald Sie die Arbeiten beendet haben.' . PHP_EOL;
        $msg .= PHP_EOL . PHP_EOL . 
                'Mit freundlichen Grüßen,' . 
                PHP_EOL . $this->helper->getShop()->getName();

        $mail = Shopware()->Mail();
        $mail->IsHTML(0);
        $mail->From     = Shopware()->Config()->Mail;
        $mail->FromName = Shopware()->Config()->Mail;
        $mail->Subject  = 'Zugangdaten Testumgebung';
        $mail->Body     = $msg;
        $mail->ClearAddresses();
        $mail->AddAddress($email, $name);
        $mail->AddAddress(Shopware()->Config()->Mail, Shopware()->Config()->Mail);
        $mail->Send();

        $this->addMessage('Die Benutzer wurden erzeugt und die Mail mit den Zugangsdaten wurde verschickt. Sie erhalten diese in Kopie an die Shopbetreiber-Adresse.', 0);
    }

    function assignViewData() {

        $view = $this->View();

        $firstProfile = $this->helper->getProfile(0);
        $profiles = $this->helper->getProfiles();
        foreach($profiles as &$profile) {
            $profile['linkbackend'] = '/' . $profile['dirname'] . '/backend';
            $profile['linkfrontend'] = '/' . $profile['dirname'];
            $profile['statustext'] = $this->getStatusText($profile['statusfiles'], $profile['statusdb'], $color);
            $profile['statuscolor'] = $color;
        }
        $backups = $this->helper->getBackups($this->liveDir . '/' . self::DIR_DBBACKUPS);
        
        $repository = Shopware()->Container()->get('models')->getRepository('Shopware\Models\Shop\Shop');
        $shop = $repository->getActiveById(1);

        $basePath = $shop->getBasePath() != null && $shop->getBasePath() != '/' ? 
                        $shop->getBasePath() : '';
        $view->basepath = $basePath;
        $view->cmd = $this->cmd;
        $view->tab = $this->tab;
        $view->checksok = $this->checksok;
        $view->profile = $this->profile;
        $view->firstProfile = $firstProfile;
        $view->profiles = $profiles;
        $view->currentProfileId = Task::getCurrentProfile();
        $view->backups = $backups;

        $view->diff = $this->diff;
        $view->diffFile = $this->diffFile;

        $view->msg = $this->msg;
    }

    function getStatusText($statusFiles, $statusDb, &$color) {

        if($statusFiles == TASK::PROFILE_STATUS_NEW && $statusDb == TASK::PROFILE_STATUS_NEW) {
            $color = 'info';
            return 'Noch nicht erstellt';
        }
        else if($statusFiles == TASK::PROFILE_STATUS_CREATING || $statusDb == TASK::PROFILE_STATUS_CREATING) {
            $color = 'warning';
            return 'Wird erzeugt';
        }
        else if($statusFiles == TASK::PROFILE_STATUS_READY && $statusDb != TASK::PROFILE_STATUS_READY) {
            $color = 'success';
            return 'Dateien abgeschlossen';
        }
        else if($statusFiles != TASK::PROFILE_STATUS_READY && $statusDb == TASK::PROFILE_STATUS_READY) {
            $color = 'success';
            return 'Datenbank abgeschlossen';
        }
        else if($statusFiles == TASK::PROFILE_STATUS_READY && $statusDb == TASK::PROFILE_STATUS_READY) {
            $color = 'success';
            return 'Abgeschlossen';
        }
        else if($statusFiles == TASK::PROFILE_STATUS_ABORTED || $statusDb == TASK::PROFILE_STATUS_ABORTED) {
            $color = 'danger';
            return 'Abgebrochen';
        }
        else if($statusFiles == TASK::PROFILE_STATUS_TIMEOUT || $statusDb == TASK::PROFILE_STATUS_TIMEOUT) {
            $color = 'warning';
            return 'Timeout! Bitte max_execution_time erhöhen!';
        }
        else if($statusFiles == TASK::PROFILE_STATUS_ERROR || $statusDb == TASK::PROFILE_STATUS_ERROR) {
            $color = 'danger';
            return 'Ein Fehler ist aufgetreten.';
        }
        else {
            return $status;
        }
    }

    function sanitize($inputField) {
        return $inputField == null ? '' : trim($inputField);
    }

    function addMessage($msg, $type = 0) {
        $this->msg[$msg] = $type;
    }

    function setResponseData($body) {

        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $this->Response()->setHeader('Cache-Control', 'post-check=0, pre-check=0', true);
        $this->Response()->setHeader('Pragma', 'no-cache', true);
        $this->Response()->setBody($body);
    }
}