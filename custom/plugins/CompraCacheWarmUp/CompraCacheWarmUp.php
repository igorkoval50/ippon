<?php


namespace CompraCacheWarmUp;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This frontend plugin creates a cronjob to automatically warm up the cache.
 *
 *
 * @author COMPRA GmbH
 * @version 2.0.0
 */
class CompraCacheWarmUp extends Plugin
{
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('compra_cache_warm_up.plugin_dir', $this->getPath());
        parent::build($container);
    }

	/**
	 * This method can be overridden
	 *
	 * @param ActivateContext $context
	 * @throws \Enlight_Exception
	 */
	public function activate(ActivateContext $context)
	{
		$this->updateCronInfo();
	}

	/**
	 * Method that reads the next time and interval of execution of the ClearHttpCache CronJob and sets these values
	 * in the CompraCacheWarmUp CronJob initially. These values can be changed by the backend admin.
	 * @throws \Enlight_Exception
	 */
	private function updateCronInfo()
	{
		$ClearHttpCacheJob = $this->container->get('cron')->getJobByAction('Shopware_CronJob_ClearHttpCache');

		if (null === $ClearHttpCacheJob) {
			return;
		}

		$CompraCacheWarmUpJob = $this->container->get('cron')->getJobByAction('Shopware_Components_CronJob_CompraCacheWarmUpCron');

		$ClearHttpCacheJobNext = $ClearHttpCacheJob->getNext();
		$ClearHttpCacheJobInterval = $ClearHttpCacheJob->getInterval();


		if ($ClearHttpCacheJobInterval) {
			$CompraCacheWarmUpJob->setInterval($ClearHttpCacheJobInterval);
		}
		if ($ClearHttpCacheJobNext) {
			$CompraCacheWarmUpJob->setNext($ClearHttpCacheJobNext);
		}

		$this->container->get('cron')->updateJob($CompraCacheWarmUpJob);
	}
}