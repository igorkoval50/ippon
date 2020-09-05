<?php
/**
 * Copyright (c) TreoLabs GmbH
 *
 * This Software is the property of TreoLabs GmbH and is protected
 * by copyright law - it is NOT Freeware and can be used only in one project
 * under a proprietary license, which is delivered along with this program.
 * If not, see <https://treolabs.com/eula>.
 *
 * This Software is distributed as is, with LIMITED WARRANTY AND LIABILITY.
 * Any unauthorised use of this Software without a valid license is
 * a violation of the License Agreement.
 *
 * According to the terms of the license you shall not resell, sublicense,
 * rent, lease, distribute or otherwise transfer rights or usage of this
 * Software or its derivatives. You may modify the code of this Software
 * for your own needs, if source code is provided.
 */

namespace TlsVariantExtends\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use TlsVariantExtends\Components\PluginConfig;

class Frontend implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;
    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * Frontend constructor.
     * @param string $pluginDir
     * @param PluginConfig $pluginConfig
     */
    public function __construct($pluginDir, PluginConfig $pluginConfig)
    {
        $this->pluginDir = $pluginDir;
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onFrontendPostDispatch',
        ];
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onFrontendPostDispatch(Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $view = $controller->View();

        $view->assign('tlsVariantExtends', $this->pluginConfig->get());
        $view->addTemplateDir($this->pluginDir . '/Resources/views');
    }
}
