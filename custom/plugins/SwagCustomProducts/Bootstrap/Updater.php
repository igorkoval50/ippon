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

namespace SwagCustomProducts\Bootstrap;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;

class Updater
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var CrudService
     */
    private $attributeService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    public function __construct(Connection $connection, CrudService $attributeService, ModelManager $modelManager)
    {
        $this->connection = $connection;
        $this->attributeService = $attributeService;
        $this->modelManager = $modelManager;
    }

    public function update(string $version): void
    {
        if (version_compare($version, '1.0.8', '<')) {
            $this->updateToVersion108();
        }

        if (version_compare($version, '3.1.0', '<')) {
            $this->updateToVersion310();
        }

        if (version_compare($version, '4.0.0', '<')) {
            $this->updateToVersion400();
        }

        if (version_compare($version, '4.0.0', '>=')) {
            $this->installCustomFacet();
        }

        if (version_compare($version, '4.5.1', '<')) {
            $this->updateToVersion451();
        }
    }

    public function setCustomFacetActiveFlag(bool $active): void
    {
        $this->connection->createQueryBuilder()
            ->update('s_search_custom_facet')
            ->set('active', ':active')
            ->where('unique_key LIKE "CustomProductsFacet"')
            ->setParameter('active', $active)
            ->execute();
    }

    private function updateToVersion108(): void
    {
        $schemaManager = new SchemaManager();
        $attributes = $schemaManager->getCustomProductsAttributes();

        foreach ($attributes as $attribute) {
            $this->attributeService->update(
                $attribute['table'],
                $attribute['column'],
                $attribute['type'],
                [],
                null,
                false,
                $attribute['default']
            );
        }

        $this->modelManager->generateAttributeModels([
            's_order_basket_attributes',
            's_media_attributes',
            's_order_details_attributes',
        ]);
    }

    private function updateToVersion310(): void
    {
        $sql = 'ALTER TABLE `s_plugin_custom_products_price` ADD is_percentage_surcharge TINYINT(1) DEFAULT NULL;
                ALTER TABLE `s_plugin_custom_products_price` ADD percentage DOUBLE DEFAULT NULL;';

        $this->connection->exec($sql);

        $this->connection->exec(
            'ALTER TABLE s_plugin_custom_products_option MODIFY COLUMN `interval` DOUBLE NULL;'
        );
    }

    private function updateToVersion400(): void
    {
        $sql = "DELETE FROM s_core_subscribes
                WHERE listener LIKE 'Shopware_Plugins_Frontend_SwagCustomProducts_Bootstrap%'";
        $this->connection->exec($sql);
    }

    private function updateToVersion451(): void
    {
        $sql = 'ALTER TABLE `s_plugin_custom_products_template` 
                ADD `variants_on_top` TINYINT(1) 
                NOT NULL 
                DEFAULT 0
                AFTER `confirm_input`;';

        $this->connection->executeUpdate($sql);
    }

    /**
     * Registers the facet for this plugin with the custom listing feature.
     */
    private function installCustomFacet(): void
    {
        $sql = <<<SQL
INSERT IGNORE INTO s_search_custom_facet (unique_key, active, display_in_categories, position, name, facet, deletable) VALUES
('CustomProductsFacet', 0, 1, 60, 'CustomProducts Filter', '{"SwagCustomProducts\\\\\\\Bundle\\\\\\\SearchBundle\\\\\\\Facet\\\\\\\CustomProductsFacet":{"label":"Custom Products"}}', 0)
ON DUPLICATE KEY UPDATE `facet` = '{"SwagCustomProducts\\\\\\\Bundle\\\\\\\SearchBundle\\\\\\\Facet\\\\\\\CustomProductsFacet":{"label":"Custom Products"}}';
SQL;
        $this->connection->executeUpdate($sql);
    }
}
