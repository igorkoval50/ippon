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

namespace SwagBusinessEssentials\Components\TemplateVariables;

use Doctrine\DBAL\Connection;

class AssignHelper implements AssignHelperInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $dbalConnection)
    {
        $this->connection = $dbalConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateAssigns($customerGroup)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $builder */
        $builder = $this->connection->createQueryBuilder();
        $variables = $builder->select([
                'variables.variable',
                'customerGroups.groupkey',
            ])
            ->from('s_core_plugins_b2b_tpl_variables', 'variables')
            ->leftJoin('variables', 's_core_plugins_b2b_tpl_config', 'mapping', 'variables.id = mapping.variable_id')
            ->leftJoin('mapping', 's_core_customergroups', 'customerGroups', 'mapping.customergroup_id = customerGroups.id')
            ->where('customerGroups.groupkey = :customerGroup')
            ->setParameter(':customerGroup', $customerGroup)
            ->execute()
            ->fetchAll();

        // Build view compatible array with the schema [ 'variableName' => true ]
        foreach ($variables as $key => $variableConfig) {
            $variables[$variableConfig['variable']] = true;

            unset($variables[$key]);
        }

        return $variables;
    }
}
