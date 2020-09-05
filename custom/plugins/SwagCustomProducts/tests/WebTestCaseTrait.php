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

namespace SwagCustomProducts\tests;

trait WebTestCaseTrait
{
    /**
     * @before
     */
    protected function resetShopwareBefore()
    {
        \Zend_Session::$_unitTestEnabled = true;

        Shopware()->Plugins()->reset();
        Shopware()->Events()->reset();
        $container = Shopware()->Container();
        $container
            ->reset('Plugins')
            ->reset('Front')
            ->reset('Router')
            ->reset('System')
            ->reset('Modules');

        $container->load('front');
        $container->load('Plugins');

        //Disable backend authentication
        Shopware()->Plugins()->Backend()->Auth()->setNoAuth(true);
    }

    /**
     * @return \Enlight_View_Default
     */
    protected function dispatch(
        \Enlight_Controller_Request_Request $request,
        \Enlight_Controller_Response_Response $response
    ) {
        /** @var \Enlight_Controller_Front $front */
        $front = Shopware()->Container()->get('front');
        $front->setRequest($request);
        $front->setResponse($response);

        $front->dispatch();

        return Shopware()->Container()->get('template');
    }
}
