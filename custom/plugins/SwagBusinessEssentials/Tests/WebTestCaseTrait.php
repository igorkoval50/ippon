<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagBusinessEssentials\Tests;

use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use Enlight_Controller_Request_Request as RequestInterface;
use Enlight_Controller_Response_Response as ResponseInterface;

trait WebTestCaseTrait
{
    use KernelTestCaseTrait;

    /**
     * @var \Enlight_Controller_Request_RequestTestCase
     */
    private $request;

    /**
     * @var \Enlight_Controller_Response_ResponseTestCase
     */
    private $response;

    /**
     * @before
     */
    protected function disableAuthBefore()
    {
        \Zend_Session::$_unitTestEnabled = true;

        //Disable backend authentication
        self::getKernel()->getContainer()->get('plugins')->Backend()->Auth()->setNoAuth(true);
    }

    /**
     * @after
     */
    protected function resetShopwareAfter()
    {
        self::getKernel()->getContainer()->get('plugins')->reset();
        self::getKernel()->getContainer()->get('events')->reset();
        $container = self::getKernel()->getContainer();
        $container
            ->reset('Plugins')
            ->reset('Front')
            ->reset('Router')
            ->reset('System')
            ->reset('Modules')
            ->reset('Session')
            ->reset('Auth');

        $container->load('Front');
        $container->load('Plugins');
        $container->load('System');
        $container->load('Modules');

        $this->response->clearAllHeaders();
        $this->request->clearAll();
    }

    /**
     * @return \Enlight_View_Default
     */
    protected function dispatch(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        $request->setHttpHost(self::getDefaultShop()->getHost());

        /** @var \Enlight_Controller_Front $front */
        $front = self::getKernel()->getContainer()->get('front');
        $front->setRequest($request);
        $front->setResponse($response);

        $front->dispatch();

        /** @var $viewRenderer Enlight_Controller_Plugins_ViewRenderer_Bootstrap */
        $viewRenderer = $front->Plugins()->get('ViewRenderer');

        return $viewRenderer->Action()->View();
    }
}
