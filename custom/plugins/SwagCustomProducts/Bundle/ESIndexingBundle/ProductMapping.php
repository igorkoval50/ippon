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

namespace SwagCustomProducts\Bundle\ESIndexingBundle;

use Shopware\Bundle\ESIndexingBundle\MappingInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * This class extends the default mapping of Shopware Product-objects to their representation in the ES index. It adds
 * fields necessary to support the sorting feature of the plugin.
 */
class ProductMapping implements MappingInterface
{
    /**
     * @var MappingInterface
     */
    private $coreMapping;

    public function __construct(MappingInterface $coreMapping)
    {
        $this->coreMapping = $coreMapping;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->coreMapping->getType();
    }

    /**
     * @return array
     */
    public function get(Shop $shop)
    {
        $mapping = $this->coreMapping->get($shop);
        $mapping['properties']['attributes']['properties']['swag_custom_product'] = [
            'properties' => [
                'is_custom_product' => ['type' => 'boolean'],
            ],
        ];

        return $mapping;
    }
}
