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
 * Shopware SwagFuzzy Plugin - SwagFuzzySettings Backend Controller
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzySettings extends Shopware_Controllers_Backend_Application
{
    /**
     * @var string
     */
    protected $model = SwagFuzzy\Models\Settings::class;

    /**
     * @var string
     */
    protected $alias = 'settings';

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
}
