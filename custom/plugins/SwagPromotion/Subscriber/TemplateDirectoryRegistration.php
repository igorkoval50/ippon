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

namespace SwagPromotion\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Enlight_Template_Manager as Template;

class TemplateDirectoryRegistration implements SubscriberInterface
{
    /**
     * @var Template
     */
    private $templateManager;

    /**
     * @var string
     */
    private $pluginBasePath;

    /**
     * @param string $pluginBasePath
     */
    public function __construct($pluginBasePath, Template $templateManager)
    {
        $this->templateManager = $templateManager;
        $this->pluginBasePath = $pluginBasePath;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onTemplatesCollected',
            'Enlight_Controller_Action_PreDispatch_Backend' => 'addTemplateDir',
        ];
    }

    public function onTemplatesCollected(Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();

        $dirs[] = $this->pluginBasePath . '/Resources/views';

        $args->setReturn($dirs);
    }

    public function addTemplateDir()
    {
        $this->templateManager->addTemplateDir($this->pluginBasePath . '/Resources/views');
    }
}
