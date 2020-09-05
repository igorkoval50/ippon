<?php namespace NetzpStaging\Component;

class KeyCache
{
    const APCU_PREFIX = 'netzp_staging_';

    protected $_helper = null;

    protected $_apcu = false;

    public function __construct() {
        $this->_apcu = $this->checkApcuIsAvailable();
        $this->helper = Shopware()->Container()->get('netzp_staging.helper');
    }

    public function cacheStore($key, $value) {
        $key = self::APCU_PREFIX . $key;

        if($this->_apcu) {
            apcu_store($key, $value);
        }
        else {
            $sql = 'REPLACE INTO netzp_staging_keystore (cachekey, value)
                         VALUES (:key, :value)
            ';
            Shopware()->Db()->query($sql, [
                'key' => $key, 'value' => json_encode($value)
            ]);
        }
    }

    public function cacheExists($key) {
        $key = self::APCU_PREFIX . $key;

        if($this->_apcu) {
            return apcu_exists($key);
        }
        else {
            $sql = 'SELECT cachekey FROM netzp_staging_keystore WHERE cachekey = :key';
            $exists = Shopware()->Db()->fetchOne($sql, [
                'key' => $key
            ]);

            return $exists != null;
        }
    }

    public function cacheFetch($key) {
        $key = self::APCU_PREFIX . $key;

        if($this->_apcu) {
            return apcu_fetch($key);
        }
        else {
            $sql = 'SELECT value FROM netzp_staging_keystore WHERE cachekey = :key';
            $value = Shopware()->Db()->fetchOne($sql, [
                'key' => $key
            ]);

            return json_decode($value);
        }
    }

    public function cacheDelete($key) {
        $key = self::APCU_PREFIX . $key;

        if($this->_apcu) {
            return apcu_delete($key);
        }
        else {
            $sql = 'DELETE FROM netzp_staging_keystore WHERE cachekey = :key';
            Shopware()->Db()->query($sql, ['key' => $key]);
        }
    }

    public function cacheDeleteAll() {

        if($this->_apcu) {
            apcu_delete(new \APCUIterator('#^' . self::APCU_PREFIX . '#'));
        }
        else {
            $sql = 'TRUNCATE netzp_staging_keystore';
            Shopware()->Db()->query($sql);
        }
    }

    function checkApcuIsAvailable()
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }

        if ( ! extension_loaded('apcu')) {
            return false;
        }

        if ( ! ini_get('apc.enabled')) {
            return false;
        }

        return true;
    }
}