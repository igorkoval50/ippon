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

/**
 * Shopware SwagFuzzy Plugin - SwagFuzzySynonyms Backend Controller
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzySynonyms extends Shopware_Controllers_Backend_Application
{
    /**
     * @var string
     */
    protected $model = SwagFuzzy\Models\SynonymGroups::class;

    /**
     * @var string
     */
    protected $alias = 'synonymGroups';

    /**
     * @var array
     */
    protected $filterFields = ['groupName'];

    /**
     * @var array
     */
    protected $sortFields = ['groupName', 'active'];

    /**
     * clones a given synonym group, changes the name and saves the new synonym group
     */
    public function cloneSynonymGroupAction()
    {
        $id = (int) $this->Request()->getParam('synonymGroupId');

        if (empty($id)) {
            $this->View()->assign(
                [
                    'success' => false,
                    'message' => 'No valid synonym group ID passed!',
                ]
            );
        }

        /** @var \SwagFuzzy\Models\SynonymGroups $synonymGroup */
        $synonymGroup = $this->get('models')->find($this->model, $id);

        $newSynonymGroup = clone $synonymGroup;
        $newSynonymGroup->setGroupName($newSynonymGroup->getGroupName() . 'Clone');

        $this->get('models')->persist($newSynonymGroup);
        $this->get('models')->flush();

        $this->View()->assign(
            [
                'success' => true,
                'synonymGroupId' => $newSynonymGroup->getId(),
            ]
        );
    }

    /**
     * overrides the parent method to join the synonyms and the shop
     *
     * {@inheritdoc}
     */
    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);

        $builder->addSelect(['synonyms', 'shop'])
            ->leftJoin('synonymGroups.synonyms', 'synonyms')
            ->leftJoin('synonymGroups.shop', 'shop');

        return $builder;
    }

    /**
     * overrides the parent method to join the shop
     *
     * {@inheritdoc}
     */
    protected function getListQuery()
    {
        $shopId = (int) $this->Request()->getParam('shopId');

        $builder = parent::getListQuery();
        $builder->leftJoin($this->alias . '.shop', 'shop')
            ->addSelect(['shop'])
            ->where($this->alias . '.shop = :shopId')
            ->setParameter('shopId', $shopId);

        return $builder;
    }

    /**
     * overrides the parent method and changes the behaviour,
     * so the search for a synonym group name works correctly
     *
     * {@inheritdoc}
     */
    protected function getFilterConditions($filters, $model, $alias, $whiteList = [])
    {
        $conditions = parent::getFilterConditions($filters, $model, $alias, $whiteList);

        foreach ($conditions as &$condition) {
            if ($condition['property'] == 'synonymGroups.groupName') {
                unset($condition['operator']);
            }
        }

        return $conditions;
    }
}
