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

namespace SwagBusinessEssentials\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Request_Request as RequestInterface;
use Enlight_Event_EventArgs as EventArgs;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use SwagBusinessEssentials\Components\PrivateShopping\ShopAccessHelperInterface;
use SwagBusinessEssentials\Components\TemplateVariables\AssignHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FrontendDispatch implements SubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    private $pluginPath;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pluginPath = $this->container->getParameter('swag_business_essentials.plugin_dir');
    }

    /**
     * Returns the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => [
                ['registerTemplateVariables', 10],
                ['checkWhiteList', 20],
            ],
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'registerTemplateVariables',
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'checkShopAccess',
        ];
    }

    /**
     * Sets all template variables configured in the backend.
     */
    public function registerTemplateVariables(EventArgs $args)
    {
        /** @var \Enlight_View_Default $view */
        $view = $args->get('subject')->View();
        $view->addTemplateDir($this->pluginPath . '/Resources/views');

        /** @var AssignHelperInterface $assignHelper */
        $assignHelper = $this->container->get('swag_business_essentials.assign_helper');
        $assigns = $assignHelper->getTemplateAssigns($this->getUserGroupKey());

        $view->assign($assigns);
    }

    /**
     * Checks if the access to the shop is allowed.
     */
    public function checkShopAccess(EventArgs $args)
    {
        /** @var ShopAccessHelperInterface $shopAccessHelper */
        $shopAccessHelper = $this->container->get('swag_business_essentials.shop_access_helper');

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $controller->View()->addTemplateDir($this->pluginPath . '/Resources/views');

        /** @var RequestInterface $request */
        $request = $controller->Request();

        $shopContext = $this->container->get('shopware_storefront.context_service')->getShopContext();
        if ($shopContext === null) {
            return;
        }

        $accessAllowed = $shopAccessHelper->isAccessAllowed($shopContext);

        if ($accessAllowed || $this->isWhiteListed($request, $shopContext)) {
            return;
        }

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');

        $redirectParams = array_merge($request->getParams(), [
            'controller' => 'PrivateLogin',
            'action' => 'index',
            'requireReload' => $controller->Request()->isXmlHttpRequest(),
        ]);

        $controller->redirect($redirectParams);
    }

    /**
     * PostDispatch to assign a view-variable when the given controller is white-listed.
     * Even though we already check that in the pre dispatch, we cannot assign view variables in the pre dispatch
     * process, because some controllers (e.g. 'index') load a template afterwards, which will reset the assign.
     */
    public function checkWhiteList(EventArgs $args)
    {
        /** @var ShopAccessHelperInterface $shopAccessHelper */
        $shopAccessHelper = $this->container->get('swag_business_essentials.shop_access_helper');

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');

        $request = $controller->Request();

        $shopContext = $this->container->get('shopware_storefront.context_service')->getShopContext();
        if ($shopContext === null) {
            return;
        }

        $accessAllowed = $shopAccessHelper->isAccessAllowed($shopContext);

        if ($accessAllowed || !$this->isWhiteListed($request, $shopContext)) {
            return;
        }

        $controller->View()->assign('minimalView', true);
    }

    /**
     * Checks if there should be an exception because the controller / action might be white-listed.
     *
     * @return bool
     */
    private function isWhiteListed(RequestInterface $request, ShopContextInterface $shopContext)
    {
        return $this->container->get('swag_business_essentials.whitelist_helper')->isControllerWhiteListed(
            $shopContext->getCurrentCustomerGroup()->getKey(),
            $request->getControllerName(),
            $request->getActionName()
        );
    }

    /**
     * @return string
     */
    private function getUserGroupKey()
    {
        /** @var ShopContextInterface $shopContext */
        $shopContext = $this->container->get('shopware_storefront.context_service')->getShopContext();

        return $shopContext->getCurrentCustomerGroup()->getKey();
    }
}
