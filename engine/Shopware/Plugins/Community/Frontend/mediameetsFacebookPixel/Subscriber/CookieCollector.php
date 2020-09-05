<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Components_Snippet_Namespace;
use Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct;
use Shopware\Bundle\CookieBundle\Structs\CookieStruct;
use Shopware\mediameetsFacebookPixel\Components\PluginConfig;

class CookieCollector implements SubscriberInterface
{

    /**
     * Returns the subscribed events and their listeners.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'CookieCollector_Collect_Cookies' => 'addCookie',
        ];
    }

    /**
     * @return \Shopware\Bundle\CookieBundle\CookieCollection|\SwagCookieConsentManager\Bundle\CookieBundle\CookieCollection|void
     */
    public function addCookie()
    {
        $config = (new PluginConfig())->get();

        if ($config['privacyMode'] !== 'integrate') {
            return;
        }

        /** @var Enlight_Components_Snippet_Namespace $pluginNamespace */
        $pluginNamespace = Shopware()
            ->Container()
            ->get('snippets')
            ->getNamespace('frontend/plugins/mediameetsFacebookPixel');

        if (class_exists('Shopware\Bundle\CookieBundle\CookieCollection')) {
            $collection = new \Shopware\Bundle\CookieBundle\CookieCollection();
        } elseif (class_exists('SwagCookieConsentManager\Bundle\CookieBundle\CookieCollection')) {
            $collection = new \SwagCookieConsentManager\Bundle\CookieBundle\CookieCollection();
        } else {
            return;
        }

        $collection->add(new CookieStruct(
            'mmFacebookPixel',
            '/^_fbp$/',
            $pluginNamespace->get('cookie-label', 'Facebook Pixel'),
            CookieGroupStruct::STATISTICS
        ));

        return $collection;
    }
}
