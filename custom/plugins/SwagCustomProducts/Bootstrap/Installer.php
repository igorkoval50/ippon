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
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Repository;
use Shopware\Models\Media\Settings as MediaSettings;
use Shopware_Components_Acl;

class Installer
{
    /**
     * All thumbnail sizes which will be used for the custom products media album
     */
    const THUMBNAIL_SIZES = '50x50;80x80;300x300;400x400';

    const FRONTEND_UPLOAD_ALBUM = 'Frontend uploads';

    /**
     * @var int
     */
    private $pluginId;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var CrudService
     */
    private $crudService;

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @var Shopware_Components_Acl
     */
    private $acl;

    /**
     * @param int $pluginId
     */
    public function __construct(
        $pluginId,
        Connection $connection,
        CrudService $crudService,
        ModelManager $em,
        Shopware_Components_Acl $acl
    ) {
        $this->pluginId = $pluginId;
        $this->connection = $connection;
        $this->crudService = $crudService;
        $this->em = $em;
        $this->acl = $acl;
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->renameOldSwagCustomizing();

        $this->createTables();
        $this->installCustomFacet();

        $mediaAlbum = $this->createMediaAlbum();
        $this->createFrontendMediaAlbum($mediaAlbum);

        $this->addACLResource();

        return true;
    }

    /**
     * Adds the CustomProducts ACL resource with the read-privilege. Additionally updates the menu-entry.
     *
     * @throws \Enlight_Exception
     */
    private function addACLResource()
    {
        try {
            $this->acl->createResource(
                'swagcustomproducts',
                ['read'],
                'SwagCustomProducts',
                $this->pluginId
            );
        } catch (\Enlight_Exception $e) {
            $message = $e->getMessage();
            $duplicateError = 'Resource swagcustomproducts already exists';
            if (strpos($message, $duplicateError) !== false) {
                // ACL resource already exists, but was not deleted correctly somehow
                // update the plugin ID
                $builder = $this->connection->createQueryBuilder();

                $builder->update('s_core_acl_resources')
                    ->set('pluginID', $this->pluginId)
                    ->where('name = "swagcustomproducts"')
                    ->execute();

                return;
            }
            throw $e;
        }
    }

    /**
     * Creates the media album inclusive thumbnail settings etc
     *
     * @return Album
     */
    private function createMediaAlbum()
    {
        /** @var Repository $mediaRepository */
        $mediaRepository = $this->em->getRepository(Album::class);

        /** @var Album $mediaAlbum */
        if ($mediaAlbum = $mediaRepository->findOneBy(['name' => 'CustomProducts'])) {
            return $mediaAlbum;
        }

        $mediaAlbum = new Album();
        $mediaAlbum->setName('CustomProducts');
        $mediaAlbum->setPosition(9);

        $mediaAlbumSettings = new MediaSettings();
        $mediaAlbumSettings->setCreateThumbnails(1);
        $mediaAlbumSettings->setIcon('sprite-box');
        $mediaAlbumSettings->setThumbnailHighDpi(false);
        $mediaAlbumSettings->setThumbnailSize(self::THUMBNAIL_SIZES);
        $mediaAlbumSettings->setThumbnailQuality(90);
        $mediaAlbumSettings->setThumbnailHighDpiQuality(60);

        $mediaAlbum->setSettings($mediaAlbumSettings);
        $mediaAlbumSettings->setAlbum($mediaAlbum);

        $this->em->persist($mediaAlbum);
        $this->em->persist($mediaAlbumSettings);
        $this->em->flush([$mediaAlbum, $mediaAlbumSettings]);

        return $mediaAlbum;
    }

    /**
     * Creates an album for frontend user uploads.
     */
    private function createFrontendMediaAlbum(Album $parentAlbum)
    {
        /** @var Repository $mediaRepository */
        $mediaRepository = $this->em->getRepository(Album::class);

        if ($mediaRepository->findOneBy(['name' => self::FRONTEND_UPLOAD_ALBUM])) {
            return;
        }

        $mediaAlbum = new Album();
        $mediaAlbum->setName(self::FRONTEND_UPLOAD_ALBUM);
        $mediaAlbum->setPosition(10);

        $mediaAlbumSettings = new MediaSettings();
        $mediaAlbumSettings->setIcon('sprite-sd-memory-card');
        $mediaAlbumSettings->setAlbum($mediaAlbum);
        $mediaAlbumSettings->setThumbnailQuality(90);
        $mediaAlbumSettings->setThumbnailHighDpiQuality(90);
        $mediaAlbumSettings->setCreateThumbnails(0);
        $mediaAlbumSettings->setThumbnailSize([]);

        $mediaAlbum->setParent($parentAlbum);

        $this->em->persist($mediaAlbum);
        $this->em->persist($mediaAlbumSettings);
        $this->em->flush([$mediaAlbum, $mediaAlbumSettings]);
    }

    /**
     * Creates all tables by loading the SQL files from /Assets/Installation/*.sql
     */
    private function createTables()
    {
        $file = __DIR__ . '/Assets/create_tables.sql';

        $fileContent = file_get_contents($file);
        $this->connection->executeQuery($fileContent);

        $schemaManager = new SchemaManager();
        $attributes = $schemaManager->getCustomProductsAttributes();

        foreach ($attributes as $attribute) {
            $this->crudService->update(
                $attribute['table'],
                $attribute['column'],
                $attribute['type'],
                [],
                null,
                false,
                $attribute['default']
            );
        }

        $this->em->generateAttributeModels([
            's_order_basket_attributes',
            's_media_attributes',
            's_order_details_attributes',
        ]);
    }

    /**
     * rename the old SwagCustomizing plugin menu label
     */
    private function renameOldSwagCustomizing()
    {
        $sql = "UPDATE s_core_menu
                SET name = 'Custom Products (v1)'
                WHERE name = 'Custom Products'
                  AND controller = 'Customizing'";
        $this->connection->executeUpdate($sql);

        $sql = "UPDATE s_core_snippets
                SET value = 'Custom Products (v1)'
                WHERE name = 'Customizing' AND dirty = 0
                  AND value = 'Custom Products'";
        $this->connection->executeUpdate($sql);
    }

    /**
     * Registers the facet for this plugin with the custom listing feature.
     */
    private function installCustomFacet()
    {
        $sql = <<<SQL
INSERT IGNORE INTO s_search_custom_facet (unique_key, active, display_in_categories, position, name, facet, deletable) VALUES
('CustomProductsFacet', 0, 1, 60, 'CustomProducts Filter', '{"SwagCustomProducts\\\\\\\Bundle\\\\\\\SearchBundle\\\\\\\Facet\\\\\\\CustomProductsFacet":{"label":"Custom Products"}}', 0)
ON DUPLICATE KEY UPDATE `facet` = '{"SwagCustomProducts\\\\\\\Bundle\\\\\\\SearchBundle\\\\\\\Facet\\\\\\\CustomProductsFacet":{"label":"Custom Products"}}';
SQL;
        $this->connection->executeUpdate($sql);
    }
}
