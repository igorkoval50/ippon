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

use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Customer\Group;
use SwagBusinessEssentials\Components\RequestManager\RequestManagerInterface;

class Shopware_Controllers_Backend_SwagBusinessEssentials extends Shopware_Controllers_Backend_Application
{
    protected $model = Customer::class;
    protected $alias = 'customer';

    /**
     * Action to accept a customer-request.
     */
    public function acceptRequestAction()
    {
        $viewAssign = ['success' => true];

        try {
            /** @var RequestManagerInterface $requestManager */
            $requestManager = $this->get('swag_business_essentials.request_manager');
            $requestManager->acceptCustomerRequest((int) $this->Request()->getParam('id'));
        } catch (\Exception $e) {
            $viewAssign['success'] = false;
            $viewAssign['error'] = $e->getMessage();
        }

        $this->View()->assign($viewAssign);
    }

    /**
     * Action to decline a customer-request.
     */
    public function declineRequestAction()
    {
        $viewAssign = ['success' => true];

        try {
            /** @var RequestManagerInterface $requestManager */
            $requestManager = $this->get('swag_business_essentials.request_manager');
            $requestManager->declineCustomerRequest((int) $this->Request()->getParam('id'));
        } catch (\Exception $e) {
            $viewAssign['success'] = false;
            $viewAssign['error'] = $e->getMessage();
        }

        $this->View()->assign($viewAssign);
    }

    /**
     * Action to fetch all customer-groups.
     * This custom-action is necessary to fetch the additional information whether or not the customer-group is
     * associated to a main-shop.
     */
    public function getCustomerGroupsAction()
    {
        $builder = $this->getManager()->getDBALQueryBuilder();

        $builder->select([
            'customergroups.id as id',
            'customergroups.groupkey as `key`',
            'customergroups.description as name',
            'customergroups.tax as tax',
            'customergroups.taxinput as taxInput',
            'customergroups.mode as mode',
            'customergroups.discount as discount',
            'IF(shops.id, 1, 0) as isMain',
        ])
            ->from('s_core_customergroups', 'customergroups')
            ->leftJoin('customergroups', 's_core_shops', 'shops', 'shops.customer_group_id = customergroups.id')
            ->groupBy('customergroups.id');

        $customerGroup = $this->Request()->getParam('customerGroup');
        if ($customerGroup) {
            $builder->andWhere('customergroups.groupkey != :customerGroup')
                ->setParameter(':customerGroup', $customerGroup);
        }

        $this->View()->assign($this->getResult($builder));
    }

    /**
     * Fetches all customer-groups which were configured for private register.
     */
    public function getRegisterTemplatesAction()
    {
        $builder = $this->getManager()->getDBALQueryBuilder();

        $builder->select([
            'customergroups.id as id',
            'customergroups.groupkey as `key`',
            'customergroups.description as name',
            'customergroups.tax as tax',
            'customergroups.taxinput as taxInput',
            'customergroups.mode as mode',
            'customergroups.discount as discount',
        ])
            ->from('s_core_customergroups', 'customergroups')
            ->leftJoin('customergroups', 's_core_plugins_b2b_cgsettings', 'cgSettings', 'cgSettings.customergroup = customergroups.groupkey')
            ->where('cgSettings.allowregister = 1 OR customergroups.groupkey = "H"')
            ->setFirstResult($this->Request()->getParam('start'))
            ->setMaxResults($this->Request()->getParam('limit'));

        $countBuilder = $this->getManager()->getDBALQueryBuilder();
        $countBuilder->select(['COUNT(customergroups.id)'])
            ->from('s_core_customergroups', 'customergroups')
            ->leftJoin('customergroups', 's_core_plugins_b2b_cgsettings', 'cgSettings', 'cgSettings.customergroup = customergroups.groupkey')
            ->where('cgSettings.allowregister = 1 OR customergroups.groupkey = "H"');

        $this->View()->assign(
            [
                'success' => true,
                'total' => $countBuilder->execute()->fetchColumn(),
                'data' => $builder->execute()->fetchAll(),
            ]
        );
    }

    /**
     * Enable sorting the company-field.
     *
     * {@inheritdoc}
     */
    protected function getSortConditions($sort, $model, $alias, $whiteList = [])
    {
        if (!$sort) {
            return parent::getSortConditions($sort, $model, $alias, $whiteList);
        }

        $sortProperty = $sort[0]['property'];
        $sortDirection = $sort[0]['direction'];

        if (!in_array($sortProperty, ['company', 'customer'])) {
            return parent::getSortConditions($sort, $model, $alias, $whiteList);
        }

        if ($sortProperty === 'company') {
            $sort[0]['property'] = 'billing.company';

            return $sort;
        }

        $sort = [
            [
                'property' => 'customer.firstname',
                'direction' => $sortDirection,
            ], [
                'property' => 'customer.lastname',
                'direction' => $sortDirection,
            ], [
                'property' => 'customer.id',
                'direction' => $sortDirection,
            ],
        ];

        return $sort;
    }

    /**
     * Overwrites the default "getListQuery"-method to to fetch some additional information about the customer.
     *
     * {@inheritdoc}
     */
    protected function getListQuery()
    {
        $builder = parent::getListQuery();

        $builder->select([
            'customer.id',
            'customer.firstLogin',
            'customer.firstname',
            'customer.lastname',
            'customer.groupKey',
            'customer.validation',
            'billing.company',
            'shop.name as subshopName',
            'customerGroup.name as toCustomerGroup',
        ])
            ->innerJoin(Group::class, 'customerGroup', 'WITH', 'customer.validation = customerGroup.key')
            ->innerJoin('customer.shop', 'shop')
            ->innerJoin('customer.defaultBillingAddress', 'billing');

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelFields($model, $alias = null)
    {
        $fields = parent::getModelFields($model, $alias);

        if ($model === $this->model) {
            $fields = array_merge(
                $fields,
                [
                    'subshopName' => ['alias' => 'shop.name', 'type' => 'string'],
                    'toCustomerGroup' => ['alias' => 'customerGroup.name', 'type' => 'string'],
                ]
            );
        }

        return $fields;
    }

    /**
     * Applies filters and limits to the builder and then returns the result and the total-count.
     *
     * @return array
     */
    private function getResult(DBALQueryBuilder $builder)
    {
        $builder->setFirstResult($this->Request()->getParam('start'))
            ->setMaxResults($this->Request()->getParam('limit'));

        $total = $this->get('dbal_connection')->fetchColumn('SELECT COUNT(id) FROM s_core_customergroups;');

        return [
            'success' => true,
            'total' => $total,
            'data' => $builder->execute()->fetchAll(),
        ];
    }
}
