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
use Shopware_Components_Acl;

class Uninstaller
{
    /**
     * @var SchemaManager
     */
    private $schemaManager;

    /**
     * @var CrudService
     */
    private $crudService;

    /**
     * @var Shopware_Components_Acl
     */
    private $acl;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ModelManager
     */
    private $em;

    public function __construct(
        CrudService $crudService,
        Shopware_Components_Acl $acl,
        Connection $connection,
        ModelManager $em
    ) {
        $this->schemaManager = new SchemaManager();
        $this->crudService = $crudService;
        $this->acl = $acl;
        $this->connection = $connection;
        $this->em = $em;
    }

    /**
     * Deletes all custom database tables which are related to the plugin
     */
    public function uninstall()
    {
        $this->deleteTableAttributes();
        $this->removeTables();
        $this->uninstallCustomFacet();
    }

    /**
     * deletes all custom basket attribute columns which are related to the plugin
     */
    public function secureUninstall()
    {
        $this->deleteACLResource();
    }

    /**
     * deletes all custom attributes
     */
    private function deleteTableAttributes()
    {
        $attributes = $this->schemaManager->getCustomProductsAttributes();
        foreach ($attributes as $attribute) {
            if ($this->crudService->get($attribute['table'], $attribute['column'])) {
                $this->crudService->delete($attribute['table'], $attribute['column']);
            }
        }

        $this->em->generateAttributeModels([
            's_order_basket_attributes',
            's_media_attributes',
            's_order_details_attributes',
        ]);
    }

    /**
     * Deletes the CustomProducts ACL resource and all of its privileges.
     */
    private function deleteACLResource()
    {
        $this->acl->deleteResource('swagcustomproducts');
    }

    private function removeTables()
    {
        $file = __DIR__ . '/Assets/drop_tables.sql';

        $fileContent = file_get_contents($file);

        $this->connection->executeQuery($fileContent);
    }

    /**
     * Removes the entry for custom search facets.
     */
    private function uninstallCustomFacet()
    {
        $exists = $this->connection->getSchemaManager()->tablesExist(['s_search_custom_facet']);
        if (!$exists) {
            return;
        }

        $this->connection->executeUpdate("DELETE FROM s_search_custom_facet WHERE unique_key = 'CustomProductsFacet'");
    }
}
