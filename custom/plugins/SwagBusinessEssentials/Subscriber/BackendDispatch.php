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
use Enlight_Controller_ActionEventArgs as ActionEventArgs;
use Enlight_Event_EventArgs as EventArgs;

class BackendDispatch implements SubscriberInterface
{
    /**
     * @var string
     */
    protected $pluginPath;

    /**
     * @param string $pluginPath
     */
    public function __construct($pluginPath)
    {
        $this->pluginPath = $pluginPath;
    }

    /**
     * Returns the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index' => [
                ['injectBackendB2BMenuEntry', 10],
                ['extendBackendWidgets', 20],
            ],
        ];
    }

    /**
     * Callback method for the Backend/Index postDispatch event.
     * Will add the B2B sprite to the menu.
     */
    public function injectBackendB2BMenuEntry(EventArgs $args)
    {
        /** @var $action \Enlight_Controller_Action */
        $action = $args->get('subject');
        $view = $action->View();

        $view->addTemplateDir($this->pluginPath . '/Resources/views/');
        $view->extendsTemplate('backend/swag_business_essentials/menu_entry.tpl');
    }

    /**
     * Extends the backend index controller to add an override of the merchant-widget.
     */
    public function extendBackendWidgets(ActionEventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $actionName = $args->getRequest()->getActionName();
        $view = $controller->View();

        if ($actionName === 'load') {
            $view->extendsTemplate('backend/index/swag_business_essentials/view/widgets/merchant.js');
        }
    }
}
