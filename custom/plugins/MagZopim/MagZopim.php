<?php

namespace MagZopim;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class MagZopim extends Plugin
{
	/**
	 * install function
	 */
	public function install(InstallContext $context)
    {
		$context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);		
    }

	/**
	 * uninstall function
	 */
	public function uninstall(UninstallContext $context)
    {
		$context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);		
    }
}