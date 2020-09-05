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

namespace SwagNewsletter;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use SwagNewsletter\Bootstrap\Database;
use SwagNewsletter\Bootstrap\LiveShoppingIntegrationInstaller;

class SwagNewsletter extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {
        $databaseContext = new Database(
            $this->container->get('dbal_connection')
        );

        $databaseContext->create();

        $liveShoppingIntegrationInstaller = new LiveShoppingIntegrationInstaller(
            $this->container->get('models'),
            $this->container->get('shopware_media.media_service'),
            $this->container->get('pluginlogger')
        );

        $liveShoppingIntegrationInstaller->createLiveShoppingWidget();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
        if ($context->keepUserData()) {
            return;
        }

        $databaseContext = new Database(
            $this->container->get('dbal_connection')
        );

        $databaseContext->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function update(UpdateContext $context)
    {
        $databaseContext = new Database(
            $this->container->get('dbal_connection')
        );

        $databaseContext->create();

        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }
}
