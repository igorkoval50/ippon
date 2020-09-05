<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace KibVariantListing\Components\Compatibility;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\MediaBundle\MediaServiceInterface;
use Shopware\Bundle\StoreFrontBundle;
use Shopware\Bundle\StoreFrontBundle\Service\CategoryServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Shopware\Components\Model\ModelManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LegacyStructConverter extends \Shopware\Components\Compatibility\LegacyStructConverter
{
    private $coreConverter;

    public function __construct(
        \Shopware\Components\Compatibility\LegacyStructConverter $coreConverter,
        \Shopware_Components_Config $config,
        ContextServiceInterface $contextService,
        \Enlight_Event_EventManager $eventManager,
        MediaServiceInterface $mediaService,
        Connection $connection,
        ModelManager $modelManager,
        CategoryServiceInterface $categoryService,
        ContainerInterface $container
    )
    {
        $this->coreConverter = $coreConverter;

        parent::__construct(
            $config,
            $contextService,
            $eventManager,
            $mediaService,
            $connection,
            $modelManager,
            $categoryService,
            $container
        );
    }

    public function convertListProductStruct(StoreFrontBundle\Struct\ListProduct $product)
    {
        $return = $this->coreConverter->convertListProductStruct($product);

        if ($product->hasAttribute('kib_variant_listing')) {
            $kibConfiguratorAttribute = $product->getAttribute('kib_variant_listing')->get('kib_configurator');
            $convertedConfigurator = $this->coreConverter->convertConfiguratorStruct($product, $kibConfiguratorAttribute);
            $return['attributes']['kib_variant_listing'] = new Attribute(['kib_configurator' => $convertedConfigurator]);
        }

        return $return;
    }
}
