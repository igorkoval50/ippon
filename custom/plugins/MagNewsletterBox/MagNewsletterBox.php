<?php

namespace MagNewsletterBox;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Bundle\CookieBundle\CookieCollection;
use Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct;
use Shopware\Bundle\CookieBundle\Structs\CookieStruct;
use MagNewsletterBox\Bootstrap\DatabaseHandler;
use Shopware\Components\Model\ModelManager;
use MagNewsletterBox\Models\Product;

class MagNewsletterBox extends Plugin
{
	/**
	 * install function
	 */
	public function install(InstallContext $context)
    {
        $databaseHandler = new DatabaseHandler(
            $this->container->get('dbal_connection')
        );

        $databaseHandler->installTables();

		$context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }
	
	/**
	 * uninstall function
	 */
	public function uninstall(UninstallContext $context)
    {
		$context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);		
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'CookieCollector_Collect_Cookies' => 'addComfortCookie'
        ];
    }

    public function addComfortCookie(): CookieCollection
    {
        $collection = new CookieCollection();
        $collection->add(new CookieStruct(
            'technical',
            '/^(mag)/',
            'Matches with only "mag"',
            CookieGroupStruct::TECHNICAL
        ));

        return $collection;
    }
}