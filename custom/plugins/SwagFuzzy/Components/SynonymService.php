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

namespace SwagFuzzy\Components;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;

/**
 * Class SynonymService
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class SynonymService implements SynonymServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var MediaServiceInterface
     */
    private $mediaService;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    public function __construct(
        Connection $connection,
        MediaServiceInterface $mediaService,
        ContextServiceInterface $contextService
    ) {
        $this->connection = $connection;
        $this->mediaService = $mediaService;
        $this->contextService = $contextService;
    }

    /**
     * {@inheritdoc}
     */
    public function getSynonyms($term, $shopId)
    {
        $synonymGroups = $this->getSynonymGroups($term, $shopId);
        $synonyms = [];
        $synonymsTemp = [];

        if (!empty($synonymGroups)) {
            $sql = 'SELECT `name`
                    FROM `s_plugin_swag_fuzzy_synonyms`
                    WHERE `synonymGroupId` = :synonymGroupId';

            foreach ($synonymGroups as $group) {
                $temp = $this->connection->fetchAll($sql, ['synonymGroupId' => $group['id']]);
                $synonymsTemp = array_merge($synonymsTemp, $temp);
            }

            foreach ($synonymsTemp as $i => $synonym) {
                if (strcasecmp($synonym['name'], $term) == 0) {
                    continue;
                }
                $synonyms[] = $synonym['name'];
            }
        }

        return $synonyms;
    }

    /**
     * {@inheritdoc}
     */
    public function getSynonymGroups($term, $shopId)
    {
        $sql = 'SELECT `s_plugin_swag_fuzzy_synonym_groups`.* , `s_emotion`.`device` AS normalSearchEmotionDevices
                FROM `s_plugin_swag_fuzzy_synonym_groups`
                INNER JOIN `s_plugin_swag_fuzzy_synonyms`
                  ON `s_plugin_swag_fuzzy_synonyms`.`synonymGroupId` = `s_plugin_swag_fuzzy_synonym_groups`.`id`
                LEFT JOIN `s_emotion`
                  ON `s_plugin_swag_fuzzy_synonym_groups`.`normalSearchEmotionId` = `s_emotion`.`id`
                WHERE `s_plugin_swag_fuzzy_synonyms`.`name` LIKE :term
                  AND `shopId` = :shopId
                  AND `s_plugin_swag_fuzzy_synonym_groups`.`active` = 1';

        $synonymGroups = $this->connection->fetchAll($sql, ['term' => $term, 'shopId' => $shopId]);

        $mediaIds = array_column($synonymGroups, 'ajaxSearchBanner');
        $mediaIds = array_merge($mediaIds, array_column($synonymGroups, 'normalSearchBanner'));
        $mediaIds = array_filter($mediaIds);

        $context = $this->contextService->getShopContext();
        $medias = $this->mediaService->getList($mediaIds, $context);

        foreach ($synonymGroups as &$group) {
            $id = $group['ajaxSearchBanner'];
            if (isset($medias[$id])) {
                $group['ajaxSearchBanner'] = $medias[$id];
            }

            $id = $group['normalSearchBanner'];
            if (isset($medias[$id])) {
                $group['normalSearchBanner'] = $medias[$id];
            }
        }

        return $synonymGroups;
    }
}
