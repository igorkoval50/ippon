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

class Theme implements SubscriberInterface
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
            'Enlight_Controller_Action_PreDispatch' => 'onAddTemplateDir',
            'Theme_Inheritance_Template_Directories_Collected' => 'onCollectTemplateDir',
        ];
    }

    /**
     * adds the plugin views directory to the shopware template directories.
     *
     * @param ActionEventArgs $args
     */
    public function onAddTemplateDir(ActionEventArgs $args)
    {
        /** @var \Enlight_Controller_Action $subject */
        $subject = $args->getSubject();
        $view = $subject->View();

        $view->addTemplateDir($this->pluginPath . '/Resources/views/');
    }

    /**
     * This event callback is required to load the /newsletter/ templates correctly.
     *
     * @param \Enlight_Event_EventArgs $args
     *
     * @return array
     */
    public function onCollectTemplateDir(\Enlight_Event_EventArgs $args)
    {
        $directories = $args->getReturn();
        $directories[] = $this->pluginPath . '/Resources/views/';

        return $directories;
    }
}
