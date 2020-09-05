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

use SwagFuzzy\Models\Repository;
use SwagFuzzy\Models\Statistics;

/**
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzyAnalytics extends Shopware_Controllers_Backend_Analytics
{
    /**
     * returns the searches with no results
     */
    public function getSearchesWithoutResultsAction()
    {
        $from = new DateTime($this->Request()->getParam('fromDate', '1970-01-01 00:00:00'));

        // https://regex101.com/r/FSj3vI/5/
        $toDateString = preg_replace(
            '/T(\d{2}:){2}\d{2}/',
            'T23:59:59',
            $this->Request()->getParam('toDate', 'now')
        );

        $to = new DateTime($toDateString);

        $result = $this->getFuzzyRepository()->getSearchesWithoutResults(
            $this->Request()->getParam('start', 0),
            $this->Request()->getParam('limit', null),
            $this->Request()->getParam(
                'sort',
                [
                    [
                        'property' => 'searchesCount',
                        'direction' => 'DESC',
                    ],
                ]
            ),
            $this->Request()->getParam('searchTerm', null),
            $this->getSelectedShopIds(),
            $this->get('dbal_connection'),
            $from,
            $to
        );

        $data = $this->container->get('swag_fuzzy.statistics_service')->applyPeriodSearchCount(
            $result->getData(),
            $from,
            $to
        );

        $this->send(
            $data,
            $result->getTotalCount()
        );
    }

    /**
     * @return Repository
     */
    private function getFuzzyRepository()
    {
        /** @var Repository $repo */
        $repo = $this->get('models')->getRepository(Statistics::class);

        return $repo;
    }

    /**
     * helper to get the selected shop ids
     * if no shop is selected the ids of all shops are returned
     *
     * return array | shopIds
     */
    private function getSelectedShopIds()
    {
        $selectedShopIds = (string) $this->Request()->getParam('selectedShops');

        if (!empty($selectedShopIds)) {
            return explode(',', $selectedShopIds);
        }

        return $this->getAllShopIds();
    }

    private function getAllShopIds(): array
    {
        return $this->container->get('dbal_connection')
            ->createQueryBuilder()
            ->select('id')
            ->from('s_core_shops')
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }
}
