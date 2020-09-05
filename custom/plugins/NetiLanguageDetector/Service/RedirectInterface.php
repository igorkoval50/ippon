<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     rubyc
 */
namespace NetiLanguageDetector\Service;
use NetiLanguageDetector\NetiLanguageDetector;
use NetiLanguageDetector\Struct\PluginConfig;
use Shopware\Models\Shop\Shop;


/**
 * Class Redirect
 *
 * @package NetiLanguageDetector\Component
 */
interface RedirectInterface
{
    /**
     *
     * @return array
     */
    public function getSubshopCountries();

    /**
     * @param string $struserccode
     * @param int    $currentShopId
     *
     * @return bool|Shop
     */
    public function getFittingSubshop($struserccode, $currentShopId);

    /**
     * @param \Enlight_Controller_Response_ResponseHttp $response
     * @param \Enlight_Controller_Request_RequestHttp   $request
     * @param string                                    $struserccode
     * @param string                                    $currentCurrency
     *
     * @return bool
     */
    public function upgradeCurrency($response, $request, $struserccode, $currentCurrency);

    /**
     * @param NetiLanguageDetector $bootstrap
     * @param \Enlight_View_Default $view
     * @param \Enlight_Controller_Request_RequestHttp $request
     * @param \Shopware\Models\Shop\Shop $shop
     *
     * @return array
     */
    public function prepareModal($bootstrap, $view, $request, $shop);

    /**
     * @return PluginConfig
     */
    public function getConfig();
}
