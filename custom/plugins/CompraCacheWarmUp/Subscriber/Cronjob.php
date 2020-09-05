<?php

namespace CompraCacheWarmUp\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;
use PDO;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\HttpCache\CacheWarmer;
use Shopware\Components\HttpCache\UrlProvider\UrlProviderInterface;
use Shopware\Components\HttpCache\UrlProviderFactoryInterface;
use Shopware\Components\Routing\Context;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

class Cronjob implements SubscriberInterface{
	/**
	 * @var \Shopware_Components_Config
	 */
	private $config;

	/**
	 * @var string
	 */
	private $pluginDir;

	/**
	 * @var Connection
	 */
	private $dbal_connection;
	/**
	 * @var CacheWarmer
	 */
	private $cacheWarmer;

    /**
     * @var Container
     */
	private $container;

	/**
	 * Cronjob constructor.
	 * @param Connection $dbal_connection
	 * @param CacheWarmer $cacheWarmer
	 */
	public function __construct(string $pluginDir, Connection $dbal_connection ,CacheWarmer $cacheWarmer, \Shopware_Components_Config $config, Container $container)
	{
		$this->pluginDir = $pluginDir;
		$this->dbal_connection = $dbal_connection;
		$this->cacheWarmer = $cacheWarmer;
		$this->config = $config;
		$this->container = $container;
	}

	public static function getSubscribedEvents()
	{
		return [
			'Shopware_Components_CronJob_CompraCacheWarmUpCron' => 'onRunWarmUpCache'
		];
	}

	/**
	 * Method that gets all ShopIDs and warms all URLs assigned to them.
	 *
	 * @param \Enlight_Event_EventArgs $args
	 * @return string
	 */
	public function onRunWarmUpCache(\Enlight_Event_EventArgs $args)
	{
		//Stacksize is read from the configuration in the backend.
		$stacksize = $this->config->getByNamespace('CompraCacheWarmUp', 'stacksize');

        // check if cache warmer parameters from SW 5.5 are available
        if (version_compare(
            \Shopware::VERSION, '5.5.0', '>=')
        ) {
            $isShopware5_5 = true;
        }

		//in case that the given value in the stacksize field isn't greater than zero or isn't an int at all, the cronjob stops here
		if($isShopware5_5 && ($stacksize < 1 || !is_int($stacksize))){
			return "Die Stapelgröße muss eine Zahl größer als 0 sein.";
		}

		try {
			$time_start = microtime(true);

			/*
			 * warm up cache with CLI command
			 */
			$command = Shopware()->DocPath() . "bin/console sw:warm:http:cache";

            if ($isShopware5_5) {
                $command .= " -b=" . $stacksize;
            }
			system($command);

            $time_end = microtime(true);
            $time = $time_end - $time_start;
            //Message to display in backend cronjob configuration
            return 'Cache wurde erfolgreich aufgewärmt!' . PHP_EOL .
                'Dauer: ' . round($time, 0) . 'Sekunden';


		}
		catch (\Exception $e) {
			return $e->getMessage();
		}
	}
}
