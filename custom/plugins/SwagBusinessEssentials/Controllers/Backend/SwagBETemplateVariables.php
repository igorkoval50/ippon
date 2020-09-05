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

use Doctrine\ORM\QueryBuilder;
use SwagBusinessEssentials\Models\Template\TplVariables;

class Shopware_Controllers_Backend_SwagBETemplateVariables extends Shopware_Controllers_Backend_Application
{
    protected $model = TplVariables::class;
    protected $alias = 'tplVariables';

    /**
     * Overwrites the default "getListQuery"-method to add the customer-group association.
     *
     * @return QueryBuilder
     */
    protected function getListQuery()
    {
        return $this->addCustomerGroupJoin(parent::getListQuery());
    }

    /**
     * Overwrites the default "getDetailQuery"-method to add the customer-group association.
     *
     * @param int $id
     *
     * @return QueryBuilder
     */
    protected function getDetailQuery($id)
    {
        return $this->addCustomerGroupJoin(parent::getDetailQuery($id));
    }

    /**
     * @return QueryBuilder
     */
    private function addCustomerGroupJoin(QueryBuilder $builder)
    {
        $builder->addSelect('customerGroups')
            ->leftJoin($this->alias . '.customerGroups', 'customerGroups');

        return $builder;
    }
}
