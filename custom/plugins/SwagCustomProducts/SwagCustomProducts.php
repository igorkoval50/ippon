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

namespace SwagCustomProducts;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use SwagCustomProducts\Bootstrap\Installer;
use SwagCustomProducts\Bootstrap\Uninstaller;
use SwagCustomProducts\Bootstrap\Updater;

class SwagCustomProducts extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {
        $installer = new Installer(
            $this->container->get('shopware.plugin_manager')->getPluginByName('SwagCustomProducts')->getId(),
            $this->container->get('dbal_connection'),
            $this->container->get('shopware_attribute.crud_service'),
            $this->container->get('models'),
            $this->container->get('acl')
        );

        $installer->install();
    }

    /**
     * {@inheritdoc}
     */
    public function update(UpdateContext $context)
    {
        $updater = $this->getUpdater();
        $updater->update($context->getCurrentVersion());

        $this->requestClearCache($context);
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
        $unInstaller = new Uninstaller(
            $this->container->get('shopware_attribute.crud_service'),
            $this->container->get('acl'),
            $this->container->get('dbal_connection'),
            $this->container->get('models')
        );

        $updater = $this->getUpdater();
        $updater->setCustomFacetActiveFlag(false);

        if (!$context->keepUserData()) {
            $unInstaller->uninstall();
        }

        $unInstaller->secureUninstall();

        $this->requestClearCache($context);
    }

    /**
     * {@inheritdoc}
     */
    public function activate(ActivateContext $context)
    {
        $updater = $this->getUpdater();
        $updater->setCustomFacetActiveFlag(true);

        $this->requestClearCache($context);
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(DeactivateContext $context)
    {
        $updater = $this->getUpdater();
        $updater->setCustomFacetActiveFlag(false);

        $this->requestClearCache($context);
    }

    /**
     * @param InstallContext | UpdateContext | UninstallContext | ActivateContext | DeactivateContext $context
     */
    private function requestClearCache($context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * @return Updater
     */
    private function getUpdater()
    {
        return new Updater(
            $this->container->get('dbal_connection'),
            $this->container->get('shopware_attribute.crud_service'),
            $this->container->get('models')
        );
    }
}
