<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     rubyc
 */

use NetiLanguageDetector\Service\Debug;

/**
 * Main controller for plugin LanguageDetector - Frontend
 *
 * @subpackage de.netinventors.LanguageDetector
 * @copyright  Copyright (c) 2012, Net Inventors - Agentur für digitale Medien GmbH
 * @version    $Id$
 * @author     $Author$
 */
class Shopware_Controllers_Frontend_NetiLanguageDetector extends Enlight_Controller_Action implements \Shopware\Components\CSRFWhitelistAware
{
    /**
     * @var \NetiLanguageDetector\Service\LocationInterface
     */
    protected $locationService;

    /**
     * @var $plugin
     */
    private $plugin;

    /**
     * @var \NetiLanguageDetector\Service\RedirectInterface
     */
    private $redirectInterface;

    /**
     * @var \NetiLanguageDetector\Struct\PluginConfig
     */
    private $pluginConfig;

    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @var \Shopware_Components_Config $swConfig
     */
    private $swConfig;

    /**
     * @var Debug
     */
    private $debug;

    /**
     * Returns a list with actions which should not be validated for CSRF protection
     *
     * @return string[]
     */
    public function getWhitelistedCSRFActions()
    {
        return [ 'index' ];
    }

    /**
     *
     * @throws \Exception
     */
    public function preDispatch()
    {
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $this->redirectInterface = $this->container->get('neti_language_detector.service.redirect');
        $this->locationService   = $this->container->get('neti_language_detector.service.location');
        $this->session           = $this->container->get('neti_foundation.session');
        $this->plugin            = $this->container->get('neti_foundation.plugin_manager')
            ->getPluginBootstrap('NetiLanguageDetector');
        $this->pluginConfig      = $this->container->get('neti_foundation.plugin_manager_config')
            ->getPluginConfig('NetiLanguageDetector');
        $this->swConfig          = $this->container->get('config');
        $this->debug             = $this->container->get('neti_language_detector.service.debug');
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->Response()->setHeader('Content-Type', 'application/json');

        $response = [
            'time'     => time(),
            'status'   => false,
            'modal'    => false,
            'redirect' => false,
            'language' => '',
            'session'  => true,
        ];

        $response['status'] = Enlight_Components_Session::sessionExists();

        if (false === $response['status']) {
            $this->debug->log('No session exists for current request, returning...');

            $response['session'] = false;
            $this->processResponse($response);

            return;
        }

        if (true === $this->session->netiLanguageDetector['suppressRedirect']
            && true === $this->session->netiLanguageDetector['suppressCurrencyChange']
        ) {
            $this->debug->log('Redirect and currency change suppressed by session, returning...');

            $response['status'] = false;
            $this->processResponse($response);

            return;
        }

        /** @var \Enlight_Controller_Request_RequestHttp $request */
        $request = $this->Request();

        if (
            isset($_COOKIE['disable-redirect'])
            && 1 === (int)$_COOKIE['disable-redirect']
            && $this->checkSetCookie($request)
        ) {
            $this->debug->log('Redirect suppressed by cookie, returning...');

            $response['status'] = false;
            $this->processResponse($response);

            return;
        }

        // get country of the user
        $userLocation = $this->locationService->getUsersLocation($request);

        if (empty($userLocation)) {
            $this->debug->log('Location could not be determined, returning...');

            $response['status'] = false;
            $this->processResponse($response);

            return;
        }

        list($this->plugin->struserccode, $this->plugin->strusercname) = $userLocation;

        // update currency, if function is activated
        $shopContext = $this->container->get('shopware_storefront.context_service')->getShopContext();

        if ($this->pluginConfig->isUpdatecurrency()) {
            $response['redirect'] = $this->redirectInterface->upgradeCurrency(
                $this->Response(),
                $request,
                $this->plugin->struserccode,
                $shopContext->getCurrency()->getCurrency()
            );

            if (false !== $response['redirect']) {
                $this->debug->log('updgradeCurrency returned false, returning...');

                $response['modal'] = false;
                $this->processResponse($response);

                return;
            }
        }

        // check session (this time only for the redirect)
        if ($this->session->netiLanguageDetector['suppressRedirect']) {
            $this->debug->log('Redirect suppressed by session, returning...');

            $response['status'] = false;
            $this->processResponse($response);

            return;
        }

        // get countries of the current subshop
        $shopCountries = $this->redirectInterface->getSubshopCountries();

        if (in_array($this->plugin->struserccode, $shopCountries)) {
            $this->debug->log('Current shop is set to handle user country, returning...');

            $this->setSuppressRedirect($response);

            return;
        }

        // get the fitting subshop
        $shop = $this->redirectInterface->getFittingSubshop(
            $this->plugin->struserccode,
            $shopContext->getShop()->getId()
        );

        if ($shop) {
            $response['language'] = $shop->getLocale()->getLanguage();
        } else {
            $this->debug->log('No shop for redirect found, returning...');
            // no fitting shop found, do nothing
            $this->setSuppressRedirect($response);

            return;
        }

        if ($shop->getId() === $this->container->get('Shop')->getId()) {
            $this->debug->log('Already in appropriate shop, returning...');

            // if current store is the default store for redirecting, don't redirect
            $this->setSuppressRedirect($response);

            return;
        }

        if (true === $this->pluginConfig->isSilentRedirect()) {
            $urlHelper            = $this->container->get('neti_language_detector.service.url_helper');
            $response['redirect'] = $urlHelper->modifyRequestUri(
                $request->getParam('pathname'),
                $request->getParam('search'),
                ['__shop', '__redirect'],
                [
                    '__shop'     => $shop->getId(),
                    '__redirect' => 1,
                ]
            );
        } else {
            $modal = $this->redirectInterface->prepareModal(
                $this->plugin,
                $this->View(),
                $request,
                $shop
            );

            $response['modal'] = [
                'title'   => $modal['title'],
                'content' => $modal['content'],
            ];

            $response['setCookie'] = $this->checkSetCookie($request);
        }

        $this->processResponse($response);

        $this->debug->log(sprintf('Reached bottom end of %s', __METHOD__));
    }

    /**
     * @param array $response
     */
    protected function processResponse(array $response)
    {
        echo json_encode($response);
    }

    /**
     * @param $response
     */
    public function setSuppressRedirect($response)
    {
        // if user comes from a fitting country, do nothing
        $this->session->netiLanguageDetector['suppressRedirect'] = true;
        $response['status']                                      = false;
        $this->processResponse($response);
    }

    /**
     * @param \Enlight_Controller_Request_RequestHttp $request
     *
     * @return bool
     */
    private function checkSetCookie($request)
    {
        $cookieUnsetIps = $this->pluginConfig->getUnsetCookieIp();
        if (!$cookieUnsetIps) {
            return true;
        }

        $cookieUnsetIpArr = explode(',', $cookieUnsetIps);
        $clientIps        = explode(',', $request->getClientIp(true));
        $clientIp         = $clientIps[0];

        foreach ($cookieUnsetIpArr as &$ipAddress) {
            $ipAddress = trim($ipAddress);
        }
        unset($ipAddress);

        return !in_array($clientIp, $cookieUnsetIpArr);
    }
}
