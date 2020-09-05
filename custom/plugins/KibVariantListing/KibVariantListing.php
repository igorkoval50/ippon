<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace KibVariantListing;

use Exception;
use KibVariantListing\Setup\Setup;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class KibVariantListing extends Plugin
{
    /**
     * @inheritdoc
     */
    public function install(Plugin\Context\InstallContext $context)
    {
        $setup = $this->getSetup();
        $setup->install($context);
    }

    /**
     * @inheritdoc
     */
    public function uninstall(Plugin\Context\UninstallContext $context)
    {
        $setup = $this->getSetup();
        $setup->uninstall($context);

        $context->scheduleClearCache($this->getInvalidateCacheArray());
    }

    /**
     * @inheritdoc
     */
    public function update(Plugin\Context\UpdateContext $context)
    {
        $setup = $this->getSetup();
        $setup->update($context);

        $context->scheduleClearCache($this->getInvalidateCacheArray());
    }

    /**
     * @inheritdoc
     */
    public function activate(Plugin\Context\ActivateContext $context)
    {
        $context->scheduleClearCache($this->getInvalidateCacheArray());
    }

    /**
     * Helper method to return all the caches, that need to be cleared after installing/uninstalling/enabling/disabling a plugin
     *
     * @return array
     */
    private function getInvalidateCacheArray()
    {
        return array(
            InstallContext::CACHE_TAG_CONFIG,
            InstallContext::CACHE_TAG_THEME,
            InstallContext::CACHE_TAG_TEMPLATE,
            InstallContext::CACHE_TAG_HTTP,
            InstallContext::CACHE_TAG_PROXY
        );
    }

    /**
     * @inheritdoc
     */
    public function deactivate(Plugin\Context\DeactivateContext $context)
    {
        $context->scheduleClearCache($this->getInvalidateCacheArray());
    }

    private function getSetup()
    {
        return new Setup(
            $this->container->get('shopware_attribute.crud_service'),
            $this->container->get('models')
        );
    }

    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('kib_variant_listing.plugin_dir', $this->getPath());
        parent::build($container);
    }
}
