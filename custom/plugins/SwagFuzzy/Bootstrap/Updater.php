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

namespace SwagFuzzy\Bootstrap;

use Doctrine\DBAL\Connection;
use Exception;
use Shopware\Components\Model\Configuration;

class Updater
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Database
     */
    private $database;

    /**
     * @var Configuration
     */
    private $config;

    public function __construct(Connection $connection, Database $database, Configuration $config)
    {
        $this->connection = $connection;
        $this->database = $database;
        $this->config = $config;
    }

    /**
     * @param $currentVersion
     * @param $pluginId
     *
     * @return bool
     */
    public function update($currentVersion, $pluginId)
    {
        $success = true;

        if (version_compare($currentVersion, '2.0.0', '<')) {
            $sql = 'DELETE s_core_config_elements
	    			FROM s_core_config_forms
	    			INNER JOIN s_core_config_elements
	    				ON s_core_config_elements.form_id = s_core_config_forms.id
	    			WHERE s_core_config_forms.plugin_id = ?';
            $this->connection->executeUpdate($sql, [$pluginId]);
        }

        //for later updates
        switch ($currentVersion) {
            case '2.0.0':
                $success = $this->update200();
                // no break
            case '2.0.3':
                $this->update203();
                // no break
            case '2.0.4':
                $this->update204();
                // no break
            case '2.0.5':
            case '2.0.6':
            case '2.0.7':
            case '2.0.8':
                $this->update208();
        }

        return $success;
    }

    /**
     * @return bool
     */
    private function update200()
    {
        $updater = new UpdateMergeAlgorithmSettings(
            $this->connection
        );

        return $updater->update();
    }

    /**
     * Update the version 2.0.3
     */
    private function update203()
    {
        $sql = 'ALTER TABLE `s_plugin_swag_fuzzy_settings` DROP `useAndSearchLogic`;';

        try {
            $this->connection->exec($sql);
        } catch (Exception $e) {
            //column already dropped
        }

        $this->deleteMetaDataCache();
    }

    /**
     * Update the version 2.0.4
     */
    private function update204()
    {
        $this->database->fixStatisticTable();

        $sql = 'ALTER TABLE `s_plugin_swag_fuzzy_settings`
                        ADD `useCoreForNumericSearch` TINYINT(1) NOT NULL AFTER `maxKeywordsAndSimilarWords`;';
        try {
            $this->connection->exec($sql);
        } catch (Exception $e) {
            //column already exists
        }

        $this->deleteMetaDataCache();
    }

    /**
     * Update the version 2.0.5, 2.0.6, 2.0.7, 2.0.8
     */
    private function update208()
    {
        $this->database->setDefaultFuzzySettings();

        $sql = 'ALTER TABLE `s_plugin_swag_fuzzy_settings`
                        DROP `useCoreForNumericSearch`;';
        try {
            $this->connection->exec($sql);
        } catch (Exception $e) {
            //column already exists
        }

        $this->deleteMetaDataCache();
    }

    /**
     * Deletes the metadataCache if is set
     */
    private function deleteMetaDataCache()
    {
        $metaDataCache = $this->config->getMetadataCacheImpl();
        if (method_exists($metaDataCache, 'deleteAll')) {
            $metaDataCache->deleteAll();
        }
    }
}
