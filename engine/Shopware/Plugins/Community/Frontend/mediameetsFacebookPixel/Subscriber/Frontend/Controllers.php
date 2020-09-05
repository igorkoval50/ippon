<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;

class Controllers implements SubscriberInterface
{
    /**
     * Returns the subscribed events and their listeners.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_FacebookPixelPrivacy' => 'onGetControllerPathFacebookPixelPrivacy',
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_FacebookPixelData' => 'onGetControllerPathFacebookPixelData',
        ];
    }

    /**
     * Returns the path to the FacebookPixelPrivacy controller.
     *
     * @return string
     */
    public function onGetControllerPathFacebookPixelPrivacy()
    {
        return __DIR__ . '/../../Controllers/Frontend/FacebookPixelPrivacy.php';
    }

    /**
     * Returns the path to the FacebookPixelData controller.
     *
     * @return string
     */
    public function onGetControllerPathFacebookPixelData()
    {
        return __DIR__ . '/../../Controllers/Frontend/FacebookPixelData.php';
    }
}
