<?php namespace NetzpStaging\Component;

use NetzpStaging\Component\KeyCache;

class Task
{
    const MINIMUM_EXECUTION_TIME = 3600;

    const TASK_FILES         = 1;
    const TASK_DATABASE      = 2;
    const TASK_WORKER        = 9;

    const STATE_RUNNING      = 'running';
    const STATE_ABORT        = 'abort';
    const STATE_ABORTED      = 'aborted';
    const STATE_TIMEOUT      = 'timeout';
    const STATE_DONE         = 'done';
    const STATE_ERROR        = 'error';

    const SECTION_STATE      = 'state';
    const SECTION_PROGRESS   = 'progress';
    const SECTION_RESULTS    = 'results';

    const PROFILE_STATUS_NEW            = 0;
    const PROFILE_STATUS_CREATING       = 1;
    const PROFILE_STATUS_ABORTED        = 2;
    const PROFILE_STATUS_TIMEOUT        = 3;
    const PROFILE_STATUS_DELETED        = 4;
    const PROFILE_STATUS_ERROR          = 5;
    const PROFILE_STATUS_READY          = 9;

    private $_type = 0;
    private $_task = '';
    private $_profileid = 0;
    private $_cmd = '';

    protected $helper = null;
    static protected $_cache = null;

    protected $_liveDir = '';
    protected $_testDir = '';
    protected $_profile = [];
    protected $_configLive = [];

    private $isDelete = false;

    public function __construct($task, $profileid, $type, $cmd = '') {

        $this->_task = $task;
        $this->_type = $type;
        $this->_profileid = $profileid;
        $this->_cmd = $cmd;
        $this->helper = Shopware()->Container()->get('netzp_staging.helper');

        $this->extendRuntime();
        register_shutdown_function([$this, 'shutdown']);
    }

    public function extendRuntime() {
        set_time_limit(self::MINIMUM_EXECUTION_TIME);
    }

    public function setDelete($value) {
        $this->isDelete = $value;
    }

    public function isDeleting() {
        return $this->isDelete;
    }

    public function shutdown() {

        self::$_cache = null;
        $error = error_get_last();
        if ($error['type'] === E_ERROR) {
            if(strtolower(substr($error['message'], 0, 22)) == 'maximum execution time') {
                $this->setState(self::STATE_TIMEOUT);
                $this->setProfileStatus(self::PROFILE_STATUS_TIMEOUT);
                $this->logProgress('TIMEOUT');
            }
        }
    }

    public function checkAbort() {

        $cmd = $this->getCmdForTask($this->_task);
        if($cmd == 'abort') {
            $this->setCmdForTask($this->_task, '');
            if($this->getState() == self::STATE_RUNNING) {
                $this->setState(self::STATE_ABORTED);
                $this->setProfileStatus(self::PROFILE_STATUS_ABORTED);
                exit(0);
                return true;
            }
        }

        return false;
    }

    function setParams($liveDir, $testDir, $profile, $configLive = []) {

        if($liveDir . '/' == $testDir) {
            die("ACHTUNG: das Verzeichnis der Testumgebung entspricht dem des LIVE-Shops! Abbruch.");
        }
        $this->_liveDir = $liveDir;
        $this->_testDir = $testDir;
        $this->_profile = $profile;
        $this->_configLive = $configLive;
    }

    static function getCache() { 
        if(self::$_cache == null) {
            self::$_cache = new KeyCache();
        }
        return self::$_cache; 
    }
    public function getKey($type) {
        return $type . '_' . $this->_task . $this->_profileid;
    }

    public function getKeyForTask($type, $task, $profile = 0) {
        return $type . '_' . $task . ($profile != 0 ? $profile : '');
    }

    function getType() { return $this->_type; }
    function getTask() { return $this->_task; }
    function getProfileId() { return $this->_profileid; }
    function getProfile() { return $this->_profile; }
    function getDbConfig() { return $this->_profile['dbconfig']; }
    function getConfigLive() { return $this->_configLive; }
    function getDbConfigLive() { return $this->_configLive['db']; }
    function getCmd() { return $this->_cmd; }
    function getLiveDir() { return $this->_liveDir; }
    function getTestDir() { return $this->_testDir; }

    function setType($type) {
        $this->_type = $type;
    }

    function setProgress($value, $msg = '', $log = false) {
        self::getCache()->cacheStore(
            $this->getKey(self::SECTION_PROGRESS), 
            array($value, $msg, $this->isDelete)
        );
        if($log) {
            $this->logProgress($value, $msg);
        }
    }

    function logProgress($value, $msg = '') {
        $data = [$value];
        if($msg != '') {
            array_push($data, $msg);
        }
        $this->helper->log($this->getLiveDir(), $this->getProfile(), 'Progress', $data);
    }

    function setState($value) {
        $this->setCmdForTask($this->_task, '');
        self::getCache()->cacheStore(
            $this->getKey(self::SECTION_STATE), 
            $value
        );
        $this->helper->log($this->getLiveDir(), $this->getProfile(), 'State', [$value], true);
    }

    function setCmdForTask($task, $value) {
        if($value == '') {
            self::getCache()->cacheDelete($this->getKeyForTask('cmd', $task));
        }
        else {
            self::getCache()->cacheStore(
                $this->getKeyForTask('cmd', $task), 
                $value
            );
        }
    }

    function getTasks() {
        $tasks = [];

        if(self::getCache()->cacheExists('tasks')) {
            $tasks = self::getCache()->cacheFetch('tasks');
        }
        return $tasks;        
    }

    function addTask($task) {
        $tasks = [];
        if(self::getCache()->cacheExists('tasks')) {
            $tasks = self::getCache()->cacheFetch('tasks');
        }
        if(array_search($task, $tasks) === false) {
            $tasks[] = $task;
        }

        self::getCache()->cacheStore('tasks', $tasks);
    }

    function removeTask($task = '') {

        if($task == '') {
            $task = $this->_task;
        }

        $tasks = [];
        if(self::getCache()->cacheExists('tasks')) {
            $tasks = self::getCache()->cacheFetch('tasks');
        }
        $p = array_search($task, $tasks);
        if($p !== false) {
            unset($tasks[$p]);
        }
        self::getCache()->cacheStore('tasks', $tasks);
    }

    function setResults($value) {
        self::getCache()->cacheStore(
            $this->getKey('results'), 
            $this->getResults()
        );
    }

    private function getTaskData($section, $forAllTasks = false) {

        $data = [];
        if($forAllTasks) {
            $tasks = $this->getTasks();
            foreach($tasks as $task) {
                $data[$task] = self::getCache()->cacheFetch(
                    $this->getKeyForTask($section, $task, $this->_profileid)
                );
            }
        }
        else {
            $data = self::getCache()->cacheFetch($this->getKey($section));
        }

        return $data;
    } 

    function getState($forAllTasks = false) {
        return $this->getTaskData(self::SECTION_STATE, $forAllTasks);
    }

    function getProgress($forAllTasks = false) {
        return $this->getTaskData(self::SECTION_PROGRESS, $forAllTasks);
    }

    function getResults($forAllTasks = false) {
        return $this->getTaskData(self::SECTION_RESULTS, $forAllTasks);
    }

    function getCmdForTask($task) {
        return self::getCache()->cacheFetch($this->getKeyForTask('cmd', $task));
    }

    function setCurrentProfile() {
        self::getCache()->cacheStore('profileid', $this->getProfileId());
    }

    static function getCurrentProfile() {
        return self::getCache()->cacheFetch('profileid');
    }

    function setProfileStatus($newStatus) {

        if($this->getType() == TASK::TASK_WORKER) {
            return;
        }
        
        $sql = 'UPDATE netzp_staging_profiles ';
        if($this->getType() == self::TASK_FILES) {
            $sql .= ' SET statusfiles = :status';
        }
        else if($this->getType() == self::TASK_DATABASE) {
            $sql .= ' SET statusdb = :status';
        }

        if($newStatus == self::PROFILE_STATUS_NEW) {
            if($this->getType() == self::TASK_FILES) {
                $sql .= ', creationsfiles = 0';
            }
            else if($this->getType() == self::TASK_DATABASE) {
                $sql .= ', creationsdb = 0';
            }
        }

        $sql .= ' WHERE id = :id';
        Shopware()->Db()->query($sql, ['status' => $newStatus, 'id' => $this->getProfileId()]);
        if($this->_profile != null) {
            if($this->getType() == self::TASK_FILES) {
                $this->_profile['statusfiles'] = $newStatus;
            }
            else if($this->getType() == self::TASK_DATABASE) {
                $this->_profile['statusdb'] = $newStatus;
            }
        }
    }

    function setProfileCreationDate() {

        $sql = 'UPDATE netzp_staging_profiles SET ';
        if($this->getType() == self::TASK_FILES) {
            $sql .= 'createdFiles = now(), creationsfiles = creationsfiles + 1';
        }
        else if($this->getType() == self::TASK_DATABASE) {
            $sql .= 'createdDb = now(), creationsdb = creationsdb + 1';
        }
        $sql .= ' WHERE id = :id';
        Shopware()->Db()->query($sql, ['id' => $this->getProfileId()]);
    }
}