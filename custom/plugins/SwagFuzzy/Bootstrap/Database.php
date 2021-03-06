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

class Database
{
    /**
     * @var Connection
     */
    private $dbalConnection;

    public function __construct(Connection $connection)
    {
        $this->dbalConnection = $connection;
    }

    /**
     * Install all tables
     */
    public function install()
    {
        $this->createProfilesTable();
        $this->createSettingsTable();
        $this->createStatisticsTable();
        $this->createSynonymGroupsTable();
        $this->createSynonymsTable();
        $this->alterSearchKeywordsTable();
        $this->fillFuzzySettings();
        $this->createUniqueKeyForStatistics();
        $this->fillFuzzyStatistics();
    }

    /**
     * helper method to set the standard fuzzy profiles
     */
    public function setDefaultFuzzySettings()
    {
        $sql = 'DELETE FROM `s_plugin_swag_fuzzy_profiles` WHERE `id` = 1';
        $this->dbalConnection->exec($sql);
        $sql = <<<'EOD'
INSERT INTO `s_plugin_swag_fuzzy_profiles` (`id`, `name`, `standard`, `settings`, `relevance`, `searchTables`) VALUES
(1, 'Standardprofil', 1, '{"keywordAlgorithm":"soundex","exactMatchAlgorithm":"levenshtein","searchDistance":20,"searchExactMatchFactor":400,"searchMatchFactor":5,"searchMinDistancesTop":20,"searchPartNameDistances":25,"searchPatternMatchFactor":200,"maxKeywordsAndSimilarWords":8,"topSellerRelevance":1000,"newArticleRelevance":500}', '["{\\"name\\":\\"Kategorie-Keywords\\",\\"relevance\\":10,\\"tableId\\":2,\\"field\\":\\"metakeywords\\"}","{\\"name\\":\\"Kategorie-\\\\u00dcberschrift\\",\\"relevance\\":70,\\"tableId\\":2,\\"field\\":\\"description\\"}","{\\"name\\":\\"Artikel-Name\\",\\"relevance\\":400,\\"tableId\\":1,\\"field\\":\\"name\\"}","{\\"name\\":\\"Artikel-Keywords\\",\\"relevance\\":10,\\"tableId\\":1,\\"field\\":\\"keywords\\"}","{\\"name\\":\\"Artikel-Bestellnummer\\",\\"relevance\\":50,\\"tableId\\":4,\\"field\\":\\"ordernumber\\"}","{\\"name\\":\\"Hersteller-Name\\",\\"relevance\\":45,\\"tableId\\":3,\\"field\\":\\"name\\"}","{\\"name\\":\\"Artikel-Name \\\\u00dcbersetzung\\",\\"relevance\\":50,\\"tableId\\":5,\\"field\\":\\"name\\"}","{\\"name\\":\\"Artikel-Keywords \\\\u00dcbersetzung\\",\\"relevance\\":10,\\"tableId\\":5,\\"field\\":\\"keywords\\"}"]', '["{\\"table\\":\\"s_articles\\",\\"referenceTable\\":\\"\\",\\"foreignKey\\":\\"\\",\\"additionalCondition\\":\\"\\"}","{\\"table\\":\\"s_categories\\",\\"referenceTable\\":\\"s_articles_categories\\",\\"foreignKey\\":\\"categoryID\\",\\"additionalCondition\\":\\"\\"}","{\\"table\\":\\"s_articles_supplier\\",\\"referenceTable\\":\\"\\",\\"foreignKey\\":\\"supplierID\\",\\"additionalCondition\\":\\"\\"}","{\\"table\\":\\"s_articles_details\\",\\"referenceTable\\":\\"s_articles_details\\",\\"foreignKey\\":\\"id\\",\\"additionalCondition\\":\\"\\"}","{\\"table\\":\\"s_articles_translations\\",\\"referenceTable\\":\\"\\",\\"foreignKey\\":\\"\\",\\"additionalCondition\\":\\"\\"}"]');
EOD;
        $this->dbalConnection->exec($sql);
    }

    /**
     * Search and remove unique key generated by doctrine for search terms and
     * create new based on shop id and search terms
     */
    public function fixStatisticTable()
    {
        $uniqueSql = "SHOW INDEX FROM `s_plugin_swag_fuzzy_statistics` WHERE Key_name LIKE 'UNIQ%'";

        try {
            $this->createUniqueKeyForStatistics();

            $uniqueKeys = $this->dbalConnection->fetchAll($uniqueSql);
            if ($uniqueKeys) {
                $sql = '';
                foreach ($uniqueKeys as $uniqueKey) {
                    $sql .= 'DROP INDEX ' . $uniqueKey['Key_name'] . ' ON `s_plugin_swag_fuzzy_statistics`;';
                }
                $this->dbalConnection->exec($sql);
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Removes all tables
     */
    public function uninstall()
    {
        $sql = '
            DROP TABLE s_plugin_swag_fuzzy_synonyms;
            DROP TABLE s_plugin_swag_fuzzy_synonym_groups;
            DROP TABLE s_plugin_swag_fuzzy_statistics;
            DROP TABLE s_plugin_swag_fuzzy_settings;
            DROP TABLE s_plugin_swag_fuzzy_profiles;
        ';

        try {
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * creates the profiles table
     */
    private function createProfilesTable()
    {
        $sql = 'CREATE TABLE `s_plugin_swag_fuzzy_profiles` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(255) NOT NULL,
                  `standard` TINYINT(1) DEFAULT NULL,
                  `settings` LONGTEXT,
                  `relevance` LONGTEXT,
                  `searchTables` LONGTEXT,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

        try {
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * creates the settings table
     */
    private function createSettingsTable()
    {
        $sql = 'CREATE TABLE `s_plugin_swag_fuzzy_settings` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `shopId` int(11) NOT NULL,
                  `keyword_algorithm` varchar(255) NOT NULL,
                  `exact_match_algorithm` varchar(255) NOT NULL,
                  `searchDistance` int(11) NOT NULL,
                  `searchExactMatchFactor` int(11) NOT NULL,
                  `searchMatchFactor` int(11) NOT NULL,
                  `searchMinDistancesTop` int(11) NOT NULL,
                  `searchPartNameDistances` int(11) NOT NULL,
                  `searchPatternMatchFactor` int(11) NOT NULL,
                  `maxKeywordsAndSimilarWords` int(11) NOT NULL,
                  `topSellerRelevance` int(11) NOT NULL,
                  `newArticleRelevance` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `shopId` (`shopId`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

        try {
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * creates the statistics table
     */
    private function createStatisticsTable()
    {
        $sql = 'CREATE TABLE `s_plugin_swag_fuzzy_statistics` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `shopId` int(11) NOT NULL,
                  `searchTerm` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `firstSearchDate` datetime NOT NULL,
                  `lastSearchDate` datetime NOT NULL,
                  `searchesCount` int(11) NOT NULL,
                  `resultsCount` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `search_terms` (`shopId`,`searchTerm`),
                  KEY `IDX_5410AE4FC9E63C48` (`shopId`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

        try {
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * creates the synonym groups table
     */
    private function createSynonymGroupsTable()
    {
        $sql = 'CREATE TABLE `s_plugin_swag_fuzzy_synonym_groups` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `shopId` int(11) NOT NULL,
                  `groupName` varchar(255) NOT NULL,
                  `active` tinyint(1) NOT NULL,
                  `normalSearchEmotionId` int(11) DEFAULT NULL,
                  `normalSearchBanner` varchar(255) DEFAULT NULL,
                  `normalSearchLink` varchar(255) DEFAULT NULL,
                  `normalSearchHeader` longtext,
                  `normalSearchDescription` longtext,
                  `ajaxSearchBanner` varchar(255) DEFAULT NULL,
                  `ajaxSearchLink` varchar(255) DEFAULT NULL,
                  `ajaxSearchHeader` longtext,
                  `ajaxSearchDescription` longtext,
                  PRIMARY KEY (`id`),
                  KEY `shopId` (`shopId`),
                  KEY `normalSearchEmotionId` (`normalSearchEmotionId`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

        try {
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * creates the synonyms table
     */
    private function createSynonymsTable()
    {
        $sql = 'CREATE TABLE `s_plugin_swag_fuzzy_synonyms` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `synonymGroupId` int(11) NOT NULL,
                  `name` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `IDX_8CF2E0F01F0BDEA8` (`synonymGroupId`),
                  CONSTRAINT `FK_8CF2E0F01F0BDEA8` FOREIGN KEY (`synonymGroupId`) REFERENCES `s_plugin_swag_fuzzy_synonym_groups` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

        try {
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * adds columns to the s_search_keywords table
     */
    private function alterSearchKeywordsTable()
    {
        $sql = 'ALTER TABLE `s_search_keywords`
                ADD `cologne_phonetic` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
                ADD `metaphone` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;';
        try {
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * helper method to set the standard fuzzy settings
     */
    private function fillFuzzySettings()
    {
        $this->setDefaultFuzzySettings();

        $sql = <<<'EOD'
INSERT IGNORE INTO `s_plugin_swag_fuzzy_settings` (`id`, `shopId`, `searchDistance`, `searchExactMatchFactor`, `searchMatchFactor`, `searchMinDistancesTop`, `searchPartNameDistances`, `searchPatternMatchFactor`, `maxKeywordsAndSimilarWords`, `topSellerRelevance`, `newArticleRelevance`, `keyword_algorithm`, `exact_match_algorithm`) VALUES
(1, 1, 20, 400, 5, 20, 25, 200, 8, 1000, 500, 'soundex', 'levenshtein');
EOD;
        $this->dbalConnection->exec($sql);
    }

    /**
     * creates unique key for the search statistics table
     */
    private function createUniqueKeyForStatistics()
    {
        try {
            $sql = 'ALTER TABLE `s_plugin_swag_fuzzy_statistics`
                      ADD UNIQUE `search_terms`(`shopId`, `searchTerm`);';
            $this->dbalConnection->exec($sql);
        } catch (Exception $e) {
        }
    }

    /**
     * helper method to fill the fuzzy statistics table
     */
    private function fillFuzzyStatistics()
    {
        $sql = 'TRUNCATE TABLE `s_plugin_swag_fuzzy_statistics`';

        $this->dbalConnection->exec($sql);

        $this->dbalConnection->update('s_statistics_search', ['shop_id' => 1], ['shop_id IS NULL']);

        $builder = $this->dbalConnection->createQueryBuilder();

        $builder->select(
            's.shop_id AS shopId',
            's.searchterm AS searchTerm',
            'MAX(s.datum) AS firstSearchDate',
            'COUNT(*) AS searchesCount',
            'MAX(results) AS resultsCount'
        );
        $builder->from('s_statistics_search', 's')
            ->addGroupBy('s.searchterm');

        $statistics = $builder->execute()->fetchAll();

        foreach ($statistics as $statistic) {
            $this->dbalConnection->insert(
                's_plugin_swag_fuzzy_statistics',
                [
                    'shopId' => $statistic['shopId'],
                    'searchTerm' => $statistic['searchTerm'],
                    'firstSearchDate' => $statistic['firstSearchDate'],
                    'lastSearchDate' => $statistic['firstSearchDate'],
                    'searchesCount' => $statistic['searchesCount'],
                    'resultsCount' => $statistic['resultsCount'],
                ]
            );
        }
    }
}
