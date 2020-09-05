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
use Enlight_Event_EventArgs as EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Controller implements SubscriberInterface
{
    public static $controllers = [
        'Frontend_PrivateLogin' => 'Frontend/PrivateLogin.php',
        'Frontend_PrivateRegister' => 'Frontend/PrivateRegister.php',
        'Backend_SwagBusinessEssentials' => 'Backend/SwagBusinessEssentials.php',
        'Backend_SwagBEPrivateShopping' => 'Backend/SwagBEPrivateShopping.php',
        'Backend_SwagBEPrivateRegister' => 'Backend/SwagBEPrivateRegister.php',
        'Backend_SwagBETemplateVariables' => 'Backend/SwagBETemplateVariables.php',
    ];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        $events = [];
        foreach (self::$controllers as $event => $path) {
            $eventName = 'Enlight_Controller_Dispatcher_ControllerPath_' . $event;
            $events[$eventName] = 'registerController';
        }

        return $events;
    }

    /**
     * @return string
     */
    public function registerController(EventArgs $args)
    {
        $this->container->get('Template')->addTemplateDir(dirname(__DIR__) . '/Resources/views');

        $eventName = str_replace('Enlight_Controller_Dispatcher_ControllerPath_', '', $args->getName());

        return __DIR__ . '/../Controllers/' . self::$controllers[$eventName];
    }
}
