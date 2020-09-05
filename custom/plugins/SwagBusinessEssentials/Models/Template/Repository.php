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

namespace SwagBusinessEssentials\Models\Template;

use Shopware\Components\Model\ModelRepository;

class Repository extends ModelRepository
{
    /**
     * @param int|null    $offset
     * @param int|null    $limit
     * @param string|null $filter
     *
     * @return \Doctrine\ORM\Query
     */
    public function getVariablesQuery($offset = null, $limit = null, $filter = null)
    {
        $builder = $this->getVariablesQueryBuilder($filter);

        if ($offset !== null) {
            $builder->setFirstResult($offset);
        }

        if ($limit !== null) {
            $builder->setMaxResults($limit);
        }

        return $builder->getQuery();
    }

    /**
     * @param string|null $filter
     *
     * @return \Shopware\Components\Model\QueryBuilder
     */
    public function getVariablesQueryBuilder($filter)
    {
        $builder = $this->createQueryBuilder('tplv');
        if ($filter !== null) {
            $builder->where('tplv.variable LIKE :variable');
            $builder->setParameter(':variable', '%' . $filter . '%');
        }

        return $builder;
    }
}
