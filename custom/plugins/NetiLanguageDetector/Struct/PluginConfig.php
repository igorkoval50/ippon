<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     rubyc
 */

namespace NetiLanguageDetector\Struct;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class PluginConfig
 *
 * @package NetiLanguageDetector\Struct
 */
class PluginConfig extends AbstractClass
{
    /**
     * @var array - Assigned Countries
     */
    protected $countrycode;

    /**
     * @var boolean - Redirect without notification
     */
    protected $silentRedirect;

    /**
     * @var boolean - Change currency automatically
     */
    protected $updatecurrency;

    /**
     * @var string - Detection method
     */
    protected $detectionMethod;

    /**
     * @var boolean - Deactivate Subshop
     */
    protected $subShopdeactivate;

    /**
     * @var string - Cookie not set for Ip-Address
     */
    protected $unsetCookieIp;

    /**
     * @var boolean - Route to Other Subshops
     */
    protected $subShopRedirect;

    /**
     * @var boolean - Search inside other subshops before falling back to a "default" shop
     */
    protected $searchSubshopsBeforeDefault;

    /**
     * @var bool
     */
    protected $debugMode = false;

    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * @return boolean
     */
    public function isSubShopdeactivate()
    {
        return $this->subShopdeactivate;
    }

    /**
     * @return array
     */
    public function getCountrycode()
    {
        return $this->countrycode;
    }

    /**
     * @return boolean
     */
    public function isSilentRedirect()
    {
        return $this->silentRedirect;
    }

    /**
     * @return boolean
     */
    public function isUpdatecurrency()
    {
        return $this->updatecurrency;
    }

    /**
     * @return string
     */
    public function getDetectionMethod()
    {
        return $this->detectionMethod;
    }

    /**
     * @return string
     */
    public function getUnsetCookieIp()
    {
        return $this->unsetCookieIp;
    }
  
    /**
     * @return boolean
     */
    public function isSubShopRedirect()
    {
        return $this->subShopRedirect;
    }

    /**
     * @return boolean
     */
    public function isSearchSubshopsBeforeDefault()
    {
        return $this->searchSubshopsBeforeDefault;
    }
}