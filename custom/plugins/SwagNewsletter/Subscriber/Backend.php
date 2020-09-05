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

namespace SwagNewsletter\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs as ActionEventArgs;

class Backend implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginPath;

    /**
     * @param string $pluginPath
     */
    public function __construct($pluginPath)
    {
        $this->pluginPath = $pluginPath;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index' => 'onPostDispatchBackendIndex',
            'Enlight_Controller_Action_PostDispatch_Backend_NewsletterManager' => 'onPostDispatch',
        ];
    }

    /**
     * Extend the basic newsletter
     *
     * @param ActionEventArgs $args
     */
    public function onPostDispatch(ActionEventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        $view = $controller->View();

        // If the controller action name equals "index", we have to load all custom components which cannot
        // be loaded in the 'load' request
        if ($args->getRequest()->getActionName() === 'index') {
            $view->extendsTemplate('backend/newsletter_manager/newsletter_app.js');
        }

        // If the controller action name equals "load", we have to load all extensions of the original
        // newsletter module
        if ($args->getRequest()->getActionName() === 'load') {
            $view->extendsTemplate('backend/swag_newsletter/controller/editor.js');
            $view->extendsTemplate('backend/swag_newsletter/controller/main.js');
            $view->extendsTemplate('backend/swag_newsletter/controller/overview.js');

            $view->extendsTemplate('backend/swag_newsletter/model/mailing.js');
            $view->extendsTemplate('backend/swag_newsletter/model/settings.js');

            $view->extendsTemplate('backend/swag_newsletter/view/main/window.js');
            $view->extendsTemplate('backend/swag_newsletter/view/newsletter/editor.js');
            $view->extendsTemplate('backend/swag_newsletter/view/newsletter/window.js');
            $view->extendsTemplate('backend/swag_newsletter/view/tabs/overview.js');
        }
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchBackendIndex(ActionEventArgs $args)
    {
        $action = $args->getSubject();
        $request = $args->getRequest();
        $view = $action->View();

        if ($request->getActionName() !== 'index') {
            return;
        }

        $view->extendsTemplate('backend/index/newsletter.tpl');
    }
}
