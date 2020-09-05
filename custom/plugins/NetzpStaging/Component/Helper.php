<?php namespace NetzpStaging\Component;

use NetzpStaging\Component\Task;

class Helper
{
    const SECRET = 'kTZGnbLjCDDjMR34';
	const FILENAME_CONFIG = 'config.php';
	const FILENAME_FILEMANAGER_CONFIG = 'filemanager_config.php';
	const FILENAME_ROBOTS = 'robots.txt';
	const FILENAME_LOG    = 'var/log/netzp_staging.log';

	const DISABLE_CRON 	 = 1;
	const DISABLE_MAILER = 2;
	const DISABLE_SHOP   = 3;

	const USER_PREFIX 	 = 'support_';

	const MIN_EXECUTION_TIME = 900;

	private $config;
	private $shop;

	public function __construct()
	{
		$this->config = Shopware()->Container()
						 ->get('shopware.plugin.config_reader')
						 ->getByPluginName('NetzpStaging');

        $repo = Shopware()->Container()->get('models')->getRepository('Shopware\Models\Shop\Shop');
        $this->shop = $repo->getActiveById(1);
	}

	public function getConfig($key, $default = '') 
	{
		return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
	}

	public function getShop()
	{
		return $this->shop;
	}

	public function getShopUrl()
	{
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                    '://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
        $urlParts = parse_url($url);
        $shopUrl = $urlParts['scheme'] . '://' . $urlParts['host'];
        if($this->shop->getBaseUrl() != '') {
            $shopUrl .= $this->shop->getBaseUrl();
        }

        return $shopUrl;
	}

    public function isAllowed($privilege, $resource = null, $role = null)
    {
        return Shopware()->Plugins()->Backend()->Auth()->isAllowed(array(
            'privilege' => $privilege,
            'resource' => $resource,
            'role' => $role
        ));
    }

    public function checkPrerequisites() 
    {
    	$safeModeOk = $this->checkSafeModeIsOff();
    	$maxExecOk = $this->checkMaxExecutionTime();

    	$allOk = $safeModeOk;

    	return [
    		'safemode' 			=> $safeModeOk, 
    		'maxexecutiontime' 	=> $maxExecOk, 
    		'all' 				=> $allOk
    	];
    }

    public function log($dir, $profile, $title, array $data = [], $newlineBefore = false)
    {
    	if( ! $this->getConfig('netzpStagingDebug') == 1) {
    		return;
    	}

    	$s = '';
    	if($newlineBefore) {
    		$s .= PHP_EOL;
    	}
    	$s .= '[' . date('Y-m-d H:i:s');
    	if($profile != null) {
    		$s .= ' ' . $profile['title'] . ', ' . $profile['dirname'];
    	}

    	$s .= '] ' . $title;
    	if(count($data) > 0) {
    		$s .= ': ' . implode($data, ' / ');
    	}
    	$fh = @fopen($dir . '/' . self::FILENAME_LOG, 'a');
    	@fwrite($fh, $s . PHP_EOL);
    	@fclose($fh);
    }

	public function readConfigFile($path, $readShopwareConfig = true) 
	{
        if($readShopwareConfig) {
          $path .= '/' . self::FILENAME_CONFIG;
        }
        $config = array();

        if(@file_exists($path)) {
        	@touch($path);
            $config = include($path);
            return $config;
        }

    	return false;
    }
    
	public function writeConfigFile($path, $data, $writeShopwareConfig = true, $addPhp = true) {

    	if($writeShopwareConfig) {
         	$path .= '/' . self::FILENAME_CONFIG;
        }

        $fh = @fopen($path, 'w');
		if($addPhp) {
	  		@fwrite($fh, '<?php return ' . var_export($data, true) . ';');
  		}
  		else {
  			@fwrite($fh, $data);
  		}
  		@fclose($fh);

		return true;
	}

  	function modifyConfig($profile, $dir) {

		$dbConfig = $profile['dbconfig'];
  		$config = $this->readConfigFile($dir);

		// --- write/modify config.php in staging shop
  		if( ! array_key_exists('db', $config)) {
  			$config['db'] = [];
  		}

  		$config['db']['host'] = $dbConfig['host'];
  		$config['db']['port'] = $dbConfig['port'];
  		$config['db']['username'] = $dbConfig['username'];
  		$config['db']['password'] = $dbConfig['password'];
  		$config['db']['dbname'] = $dbConfig['dbname'];

		return $this->writeConfigFile($dir, $config);
  	}

	// --- correct baseurl in staging shops
  	public function correctShopPaths($profile) {

  		$dbConfig = $profile['dbconfig'];
  		$db = $this->connectToDatabase($dbConfig);
  		$dir = $profile['dirname'];

		$sql = 'UPDATE s_core_shops 
  				   SET base_url = concat(:stagingDir, base_url)
  				 WHERE `base_url` <> ""
	  	';
		$q = $db->prepare($sql);
		$q->bindValue(':stagingDir', '/' . basename($dir));
		$q->execute();
		$db = null;
  	}

  	function updateShopConfig($db, string $key, string $value)
  	{
		$sql = 'UPDATE s_core_config_values
				   SET value = :value
				 WHERE element_id = (SELECT id 
									   FROM s_core_config_elements 
									  WHERE name = :key)';

		$q = $db->prepare($sql);
		$q->bindValue(':key', $key);
		$q->bindValue(':value', $value);
		$q->execute();

		if($q->rowCount() == 0) { // config value not present yet
			$sql2 = 'INSERT INTO s_core_config_values 
						     SET element_id = (SELECT id 
						     				   FROM   s_core_config_elements 
						     				   WHERE  name = :key),
						     	 value = :value, 
						     	 shop_id = :shop
		    ';
			$q2 = $db->prepare($sql2);
			$q2->bindValue(':key', $key);
			$q2->bindValue(':value', $value);
			$q2->bindValue(':shop', 1);
			$q2->execute();
		}
  	}

	public function checkTestDirectory(&$dirname) {

		$origdirname = $dirname;
		$dirname = trim($dirname);
	  	$dirname = str_replace(dirname($dirname), '', $dirname);
		$dirname = trim($dirname, '/');

		if('' == $dirname) {
			return -1;
		}
		else if(array_search(
				$dirname, [
					'bin', 'custom', 'engine', 'files', 'media', 'recovery', 'themes', 'var', 'vendor', 'web'
				]
			) !== false) {
			return -2;
		}

		else if($dirname != $origdirname) {
			return -3;
		}
		else {
			return 0;
		}
	}

	public function getPDOConnectionString($dbconfig) {

		return 'mysql:host=' . $dbconfig['host'] . 
							   ($dbconfig['port'] != '3306' && $dbconfig['port'] != '' ? 
							   		':'.$dbconfig['port'] : '') . 
							   ';dbname='.$dbconfig['dbname'];
	}

  	public function connectToDatabase($dbconfig) {

	    try {
	      	$db = new \PDO($this->getPDOConnectionString($dbconfig), 
	                       $dbconfig['username'], $dbconfig['password'],
	                       array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
	     	return $db;
	    }
	    catch(\PDOException $ex) {
			echo "<pre>";var_dump($ex->getMessage());echo "</pre>";
	    	return null;
	    }

	    return null;
	  }

	  public function checkDatabaseConnection($dbConfig, $liveDir, $title, $dirName, $myProfileId = 0) {

	  	$host = $dbConfig['host'];
	  	$port = (int)$dbConfig['port'];
	  	$name = $dbConfig['dbname'];
	  	$user = $dbConfig['username'];
	  	$pass = $dbConfig['password'];

	  	$liveConfig = $this->readConfigFile($liveDir);
	  	if($liveConfig === false) {
	  		return -2; // config not found
	  	}
	  	else if ($liveConfig['db']['host'] == $host &&
	  			 $liveConfig['db']['dbname'] == $name &&
	  			 $liveConfig['db']['username'] == $user &&
	  			 $liveConfig['db']['password'] == $pass) {
	  		return -3; // live db used
	  	}

	  	if($myProfileId != 0) {
		  	$sql = 'SELECT title, dirname, dbconfig FROM netzp_staging_profiles WHERE id <> :myId';
		  	$profiles = Shopware()->Db()->fetchAll($sql, ['myId' => $myProfileId]);
	  	}
	  	else {
		  	$sql = 'SELECT title, dirname, dbconfig FROM netzp_staging_profiles';
		  	$profiles = Shopware()->Db()->fetchAll($sql);
	  	}

	  	foreach($profiles as $profile) {
	  		if($profile['title'] == $title || $profile['dirname'] == $dirName) {
	  			return -4; // dirname already used in another testserver
	  		}
	  		$tmpConfig = json_decode($profile['dbconfig']);
	  		if ($tmpConfig->host == $host &&
  				$tmpConfig->dbname == $name &&
	  			$tmpConfig->username == $user &&
	  			$tmpConfig->password == $pass) {
	  			return -5; // dbconfig already used in another testserver
	  		}
	  	}

	    $db = $this->connectToDatabase($dbConfig);
	    if(null !== $db) {
	      $db = null;
	      return 0;
	    }

	    return -1; // unspecified error
  	}

	public function getProfiles($onlyCron = false) {

		$sql = 'SELECT 	 *
				FROM 	 netzp_staging_profiles
		';
		if($onlyCron) {
			$sql .= ' WHERE runfromcron = 1';
		}

		$sql .= ' ORDER BY title';
		$profiles = Shopware()->Db()->fetchAll($sql);

		foreach($profiles as &$profile) {
			$profile['dirsexcluded'] = json_decode($profile['dirsexcluded'], true);
			$profile['dirsnotsynced'] = json_decode($profile['dirsnotsynced'], true);
			$profile['dbconfig'] = json_decode($profile['dbconfig'], true);
			$profile['settings'] = $this->correctSettings(json_decode($profile['settings'], true));
		}
		
		return $profiles;
	}

	public function getProfile($id = 0, $title = '') {

		$sql = 'SELECT 	 *
				FROM 	 netzp_staging_profiles';
		$params = [];
		if($title != '') {
			$sql .= ' WHERE title = :title';
			$params = ['title' => $title];
		}
		else if(0 == $id) {
			$sql .= ' ORDER BY id LIMIT 1';
		}
		else {
			$sql .= ' WHERE id = :id';
			$params = ['id' => $id];
		}

		$profile = Shopware()->Db()->fetchRow($sql, $params);

		if($id != 0 && ($profile === false || $profile == null)) {
			die("ACHTUNG: das Profile $id ist leer. Abbruch");
		}
		$profile['dirsexcluded'] = json_decode($profile['dirsexcluded'], true);
		$profile['dirsnotsynced'] = json_decode($profile['dirsnotsynced'], true);
		$profile['dbconfig'] = json_decode($profile['dbconfig'], true);
		$profile['settings'] = $this->correctSettings(json_decode($profile['settings'], true));

		return $profile;
	}

	public function deleteProfile($id) {
		
		$sql = 'DELETE FROM netzp_staging_profiles
				WHERE 		id = :id
		';
		Shopware()->Db()->query($sql, ['id' => $id]);
	}

	public function saveProfile($profile) {

		if($profile['id'] == 0) {
			$sql = 'INSERT INTO netzp_staging_profiles 
								(title, dirname, runfromcron, dirsexcluded, dirsnotsynced, dbconfig, settings)
					VALUES (:title, :dirname, :runfromcron, :dirsexcluded, :dirsnotsynced, :dbconfig, :settings)
			';

			// default settings for new profiles
			$settings = [
				'maintenance'	=> true,
				'auth_user'		=> '',
				'auth_pass'		=> '',
				'errors1'		=> false,
				'errors2'		=> true,
				'errors3'		=> true,
				'errors4'		=> true,
				'caching1'		=> false,
				'caching2'		=> false,
				'csrf1'			=> true,
				'csrf2'			=> true,
				'norobots'		=> true,
				'nocronjobs'	=> false,
				'noemails'		=> false,
			];

			Shopware()->Db()->query($sql, [
				'title' 	=> $profile['title'],
				'dirname' 	=> $profile['dirname'],
				'runfromcron'=>$profile['runfromcron'],
				'dirsexcluded' => json_encode($profile['dirsexcluded']),
				'dirsnotsynced' => json_encode($profile['dirsnotsynced']),
				'dbconfig'	=> json_encode($profile['dbconfig']),
				'settings'	=> json_encode($settings)
			]);
		}
		else {
			$sql = 'UPDATE 	netzp_staging_profiles 
					SET  	title = :title, dirname = :dirname, runfromcron = :runfromcron,
							dirsexcluded = :dirsexcluded, dirsnotsynced = :dirsnotsynced, 
							dbconfig = :dbconfig
					WHERE  	id = :id
			';
			Shopware()->Db()->query($sql, [
				'id' => $profile['id'],
				'title' 	=> $profile['title'],
				'dirname' 	=> $profile['dirname'],
				'runfromcron'=>$profile['runfromcron'],
				'dirsexcluded' => json_encode($profile['dirsexcluded']),
				'dirsnotsynced' => json_encode($profile['dirsnotsynced']),
				'dbconfig'	=> json_encode($profile['dbconfig'])
			]);
		}

		return $profile;
	}

	public function saveSettings($profile) {

		$sql = 'UPDATE 	netzp_staging_profiles 
				SET  	settings = :settings
				WHERE  	id = :id
		';
		Shopware()->Db()->query($sql, [
			'id' => $profile['id'],
			'settings'	=> json_encode($profile['settings'])
		]);

		return $profile;
	}

	function correctSettings($settings) {

		if( ! array_key_exists('errors1', $settings)) $settings['errors1'] = 0;
		if( ! array_key_exists('errors2', $settings)) $settings['errors2'] = 0;
		if( ! array_key_exists('errors3', $settings)) $settings['errors3'] = 0;
		if( ! array_key_exists('errors4', $settings)) $settings['errors4'] = 0;

		if( ! array_key_exists('csrf1', $settings)) $settings['csrf1'] = 0;
		if( ! array_key_exists('csrf2', $settings)) $settings['csrf2'] = 0;

		if( ! array_key_exists('caching1', $settings)) $settings['caching1'] = 0;
		if( ! array_key_exists('caching2', $settings)) $settings['caching2'] = 0;

		if( ! array_key_exists('norobots', $settings)) $settings['norobots'] = 0;
		if( ! array_key_exists('nocronjobs', $settings)) $settings['nocronjobs'] = 0;
		if( ! array_key_exists('noemails', $settings)) $settings['noemails'] = 0;
		if( ! array_key_exists('maintenance', $settings)) $settings['maintenance'] = 0;

		return $settings;
	}

	public function updateShopOptions($profile, $liveDir) {

		if($profile['statusfiles'] != Task::PROFILE_STATUS_READY ||
		   $profile['statusdb'] != Task::PROFILE_STATUS_READY) {
			return;
		}

		// --- config.php settings
		$this->updateConfigTest($profile, $liveDir . $this->getDirectory($profile));

        if($profile['settings']['norobots']) {
            $this->writeConfigFile($this->getFilenameRobots($liveDir . $this->getDirectory($profile)), 
                				   'User-agent: *' . chr(13).chr(10) . 'Disallow: /' . chr(13).chr(10), 
                				   false, false);
        }
        else {
            @unlink($this->getFilenameRobots($liveDir . $this->getDirectory($profile)));
        }

		// --- CRONjobs
        $ok = $this->updateShopOption($profile['dbconfig'], self::DISABLE_CRON, 
	    							  $profile['settings']['nocronjobs']);
        // --- disable EMAILS
        $ok = $this->updateShopOption($profile['dbconfig'], self::DISABLE_MAILER, 
	    							  $profile['settings']['noemails']);
        // --- set maintenance mode
        $ok = $this->updateShopOption($profile['dbconfig'], self::DISABLE_SHOP, 
        							  $profile['settings']['maintenance']);

        $this->clearTestserverCache($profile);

        return $ok;
	}

  	public function updateShopOption($dbConfig, $type, $value) {

  		$db = $this->connectToDatabase($dbConfig);
  		if($db === null) {
  			return;
  		}

  		try {
	  		if($type == self::DISABLE_CRON) { 
	  			/* 15.12.2018 - herausgenommen, macht mehr probleme als es hilft -> der cron wird ja
	  							vermutlich ohnehin nicht extern aufgerufen beim testserver

		  		$sql1 = 'UPDATE s_core_plugins 
		  			 	 	SET active = :value 
		  			 	  WHERE namespace = "Core" 
		  			 	    AND (name = "Cron" OR name = "CronBirthday" OR name = "CronProductExport" OR 
		  				  	     name = "CronRating" OR name = "CronStock")';
				$q1 = $db->prepare($sql1);
				$q1->bindValue(':value', $value);
				$q1->execute();  		

		  		$sql2 = 'UPDATE s_crontab 
		  			 	    SET active = 0 
		  			 	  WHERE action = "NetzpStagingCron"';
				$q2 = $db->prepare($sql2);
				$q2->execute();  
				*/
			}

	  		else if($type == self::DISABLE_MAILER) { 
  				$value = $value == 1 ? serialize('file') : serialize('mail');
  				$this->updateShopConfig($db, 'mailer_mailer', $value);
			}

	  		else if($type == self::DISABLE_SHOP) { 
  				$this->updateShopConfig($db, 'setoffline', serialize($value == '1'));
  				$this->updateShopConfig($db, 'offlineIp', serialize('127.0.0.1'));
			}
		}
		catch(\PDOException $ex) {
			return false;
		}
		finally {
			$db = null;
  		}

  		return true;
  	}

	function updateConfigTest($profile, $dir, $updateAllOptions = true) {

	  	$testConfig = $this->readConfigFile($dir);
	  	if(false === $testConfig) {
	  		return false;
	  	}

	  	if($updateAllOptions) {
	        // --- phpsettings
	        if( ! array_key_exists('phpsettings', $testConfig)) { $testConfig['phpsettings'] = []; }
	        $testConfig['phpsettings']['display_errors'] = $profile['settings']['errors4'] == 1;

	        // --- csrfProtection
	        if( ! array_key_exists('csrfProtection', $testConfig)) { $testConfig['csrfProtection'] = []; }
	        $testConfig['csrfProtection']['frontend'] = $profile['settings']['csrf1'] == 1;
	        $testConfig['csrfProtection']['backend'] = $profile['settings']['csrf2'] == 1;

	        $testConfig['front']['showException'] = $profile['settings']['errors2'] == 1;
	        $testConfig['front']['noErrorHandler'] = $profile['settings']['errors1'] == 1;
	        $testConfig['front']['throwExceptions'] = $profile['settings']['errors3'] == 1;

	        // --- template
	        if( ! array_key_exists('template', $testConfig)) { $testConfig['template'] = []; }
	        $testConfig['template']['forceCompile'] = $profile['settings']['caching1'] == 1;

	        // --- cache
	        if( ! array_key_exists('httpcache', $testConfig)) { $testConfig['template'] = []; }
	        $testConfig['httpcache']['debug'] = $profile['settings']['caching2'] == 1;

	        // --- auth
	  		if( ! array_key_exists('netzpStaging', $testConfig)) { $testConfig['netzpStaging'] = []; }
	        $testConfig['netzpStaging']['istestserver'] = 1;
	        $testConfig['netzpStaging']['servername'] = $profile['title'];
	        $testConfig['netzpStaging']['authuser'] = $profile['settings']['auth_user'];
	        $testConfig['netzpStaging']['authpassword'] = $profile['settings']['auth_pass'];
	    }
       	$testConfig['netzpStaging']['anonymized'] = $profile['dbconfig']['anonymize'];
 
		$testConfig = Shopware()->Container()->get('events')->filter(
			'NetzpStaging_Component_Helper_WriteConfigFile_FilterResult', 
			$testConfig
		);
 	  	$this->writeConfigFile($dir, $testConfig);
	}

	public function clearTestserverCache($profile) {

		$url = $this->getDirectory($profile, true) . '/netzpstaging/clearcache?secret=' . self::SECRET;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_INTERFACE, '127.0.0.1');
		if($profile['settings']['auth_user'] != '' && $profile['settings']['auth_pass'] != '') {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, 
				$profile['settings']['auth_user'].':'.$profile['settings']['auth_pass']);
		}
		curl_exec($ch);
		curl_close($ch);
	}

	public function getFilenameRobots($dir = '') {
		return ($dir != '' ? $dir . '/' : '') . self::FILENAME_ROBOTS;
	}

	public function getDirectory($profile, $fullPath = false) {

		$url = '';
		if($fullPath) {
			$serverName = @$_SERVER['SERVER_NAME'];
        	$por = '';
        	if ( ! in_array(@$_SERVER['SERVER_PORT'], [80, 443])) {
            	$port = ':' . @$_SERVER['SERVER_PORT'];
        	}

        	if ( ! empty(@$_SERVER['HTTPS']) && 
        		(strtolower(@$_SERVER['HTTPS']) == 'on' || @$_SERVER['HTTPS'] == '1')) {
            	$scheme = 'https';
        	} else {
            	$scheme = 'http';
        	}
        	$url = $scheme . '://' . $serverName . $port;
		}
		
		$url .= '/' . $profile['dirname'];

		return $url;
	}

	public function getBackups($backupDir) {

		$files = [];
		$tmpFiles = glob($backupDir . '/*.zip');
		foreach($tmpFiles as $file) {
			$fileSize = $this->filesizeFormatted($file);
			$files[] = [
				'path'	   => $file,
				'filename' => basename($file),
				'filesize' => $fileSize,
				'filedate' => @filemtime($file)
			];
		}

		return $files;
	}

	public function deleteBackup($path) {

        if(@file_exists($path)) {
        	unlink($path);
        	return true;
        }

        return false;
	}

	private function checkMaxExecutionTime()
	{
		return ini_get('max_execution_time') >= self::MIN_EXECUTION_TIME;
	}

    function checkSafeModeIsOff()
    {
		return ! ini_get('safe_mode');
    }

    public function createBackendUser($liveDir, $profile, $userName, $email, $passwordHash)
    {
  		$db = $this->connectToDatabase($profile['dbconfig']);
  		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

  		if($db === null) {
  			return;
  		}

        $fullUser = self::USER_PREFIX . $userName;

  		$stm = $db->prepare('SELECT id FROM s_core_auth WHERE username = :username');
  		$stm->bindParam(':username', $fullUser, \PDO::PARAM_STR);
  		$stm->execute();
        $userId = $stm->fetchColumn();

        if ($userId !== false) {
	  		$stm = $db->prepare('DELETE FROM s_core_auth WHERE id = :userid');
  			$stm->bindParam(':userid', $userId, \PDO::PARAM_INT);
  			$stm->execute();
  		}

        $stm = $db->prepare('SELECT id FROM s_core_auth_roles WHERE name = "local_admins"');
        $stm->execute();
        $roleId = $stm->fetchColumn();

	    $localeId = $this->shop->getLocale()->getId();

        $stm = $db->prepare('INSERT INTO s_core_auth (`username`, `name`, `email`, `password`, 
        											  `lastlogin`, `lockeduntil`, `failedlogins`,
            						  				  `roleId`, `localeId`, `encoder`, `active`) 
			 				 VALUES (:username, :name, :email, :password, 
			 				 		 :lastlogin, "0000-00-00 00:00:00", 0,
			 				 		 :roleid, :localeid, :encoder, 1)');

        $fullUserName = 'Supportbenutzer ' . $userName;
        $passwordEncoder = Shopware()->PasswordEncoder()->getDefaultPasswordEncoderName();
        $lastLogin = date('Y-m-d H:i:s');

        $stm->bindParam(':username', $fullUser, \PDO::PARAM_STR);
        $stm->bindParam(':name', 	 $fullUserName, \PDO::PARAM_STR);
        $stm->bindParam(':email', 	 $email, \PDO::PARAM_STR);
        $stm->bindParam(':password', $passwordHash, \PDO::PARAM_STR);
        $stm->bindParam(':lastlogin',$lastLogin, \PDO::PARAM_STR);
        $stm->bindParam(':roleid', 	 $roleId, \PDO::PARAM_INT);
        $stm->bindParam(':localeid', $localeId, \PDO::PARAM_INT);
        $stm->bindParam(':encoder',  $passwordEncoder, \PDO::PARAM_STR);
		$stm->execute();

		return $fullUser;   
    }

    public function createFilesUser($liveDir, $profile, $userName, $email, $passwordHash)
    {
    	$userName = self::USER_PREFIX . $userName;
    	$path = $liveDir . $this->getDirectory($profile) . '/' . self::FILENAME_FILEMANAGER_CONFIG;

    	$config = $this->readConfigFile($path, false);
    	if( ! array_key_exists('auth_users', $config)) {
    		$config['auth_users'] = [];
    	}
    	if( ! array_key_exists('directories_users', $config)) {
    		$config['directories_users'] = [];
    	}
    	$config['auth_users'][$userName] = $passwordHash;
    	$config['directories_users'][$userName] = $liveDir . $this->getDirectory($profile);

		$this->writeConfigFile($path, $config, false, true);

		return $userName;
    }

    public function diffFile($liveDir, $profile, $fileName)
    {
        // see http://code.iamkate.com/php/diff-implementation/#comparing
        require_once(__DIR__ . '/../lib/diff.php');

        $fileName = urldecode($fileName);
        $file1 = $liveDir . $fileName;
        $file2 = $liveDir . $this->getDirectory($profile) . $fileName;

        $file1Size = $this->filesizeFormatted($file1);
        $file2Size = $this->filesizeFormatted($file2);

        $file1Date = date('d.m.Y H:i:s', @filectime($file1));
        $file2Date = date('d.m.Y H:i:s', @filectime($file2));

		$diff = \Diff::toTable(\Diff::compareFiles($file1, $file2));
		$header  = '<tr>';
		$header .= '<th>
						LIVE-Server &nbsp;&nbsp;
						<span class="badge badge-light">' . $file1Size . '</span>
						<span class="badge badge-info">' . $file1Date . '</span>
					</th>';
		$header .= '<th>
						TEST-Server &nbsp;&nbsp;
						<span class="badge badge-light">' . $file2Size . '</span>
						<span class="badge badge-info">' . $file2Date . '</span>
					</th>';
		$header .= '</tr>';
		$diffTable = str_replace('<table class="diff">', '<table class="diff">' . $header, $diff);

    	return [
    		'filename' => $fileName,
    		'file1' => $file1, 
    		'file2' => $file2, 
    		'diff' => $diffTable
    	];
    }

    public function diffFiles($dir, $profile)
    {
    	$diff = $this->getFileDifferences($dir, $profile);

    	return $diff;
    }

    function diffArrays($a, $b, $liveDir, $testDir) {

        $data = array();
        foreach ($a as $fileA) {
            if(in_array($fileA, $b)) {
                $sizeA = $this->filesizeFormatted($liveDir . $fileA);
                $sizeB = $this->filesizeFormatted($testDir . $fileA);
                if($sizeA != $sizeB) {
                    $timeA = filemtime($liveDir . $fileA);
                    $timeB = filemtime($testDir . $fileA);

                    $status = $timeA > $timeB ? 3 : 4;
                    $data[dirname($fileA)][] = array(
                        'status' => $status, 
                        'file'   => $fileA, 
                        'fileBasename' => basename($fileA),
                        'sizeA'  => $sizeA, 
                        'sizeB'  => $sizeB,
                        'timeA'  => date('d.m.Y H:m', $timeA), 
                        'timeB'  => date('d.m.Y H:m', $timeB),
                    );
                }
            }
            else {
                $data[dirname($fileA)][] = array(
                	'status' => 1, 
                	'file' => $fileA, 
                	'fileBasename' => basename($fileA)
                );
            }
        }
        foreach ($b as $fileB) {
            if(in_array($fileB, $a)) {
                // file size and date already checked
            }
            else {
                $data[dirname($fileB)][] = array(
                	'status' => 2, 
                	'file' => $fileB, 
                	'fileBasename' => basename($fileB)
                );
            }
        }

        return $data;
    }

	function getDirectories($root, $path, $excludes = array()) {

        $data = array();
		if(substr($path, 0, 1) != '/') {
			$path = '/' . $path;
		}

        $filter = function ($file, $key, $iterator) {
            if ($iterator->hasChildren()) {
                return true;
            }
            return $file->isFile();
        };

        $innerIterator = new \RecursiveDirectoryIterator($root . $path);
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator($innerIterator, $filter)
        );

        foreach ($iterator as $filename => $file) {
			$withoutRoot = str_replace($root, '', $filename);
			if( ! in_array(dirname($withoutRoot), $excludes)) {
	            $data[] = $withoutRoot;
    	    }
    	}

        return $data;
    }

    public function getFileDifferences($liveDir, $profile)
    {
        set_time_limit(0);

        $testDir = $liveDir . $this->getDirectory($profile);

        $a1 = $this->getDirectories($liveDir, 'themes');
        $b1 = $this->getDirectories($testDir, 'themes');

        $a2 = $this->getDirectories($liveDir, 'engine/Shopware/Plugins');
        $b2 = $this->getDirectories($testDir, 'engine/Shopware/Plugins');

        $a = array_merge($a1, $a2);
        $b = array_merge($b1, $b2);

        if(@is_dir($liveDir . '/custom')) {
            $a = array_merge($a, $this->getDirectories(
            	$liveDir, 'custom', 
            	['/custom/plugins/NetzpStaging/lib/filemanager']
            ));
            $b = array_merge($b, $this->getDirectories(
            	$testDir, 'custom',
            	['/custom/plugins/NetzpStaging/lib/filemanager']
			));
        }

        sort($a);
        sort($b);

        $diff = $this->diffArrays($a, $b, $liveDir, $testDir);
        ksort($diff);

        return $diff;
    }

    function filesizeFormatted($path)
	{
    	$size = @filesize($path);
    	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    	$power = $size > 0 ? floor(log($size, 1024)) : 0;
    	
    	return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}
}
