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

namespace SwagCustomProducts\Components\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Customer\Group;
use SwagCustomProducts\Components\Calculator;
use SwagCustomProducts\Models\Price;

class PriceFactory implements PriceFactoryInterface
{
    const TABLE_TAX = 's_core_tax';
    const TABLE_CUSTOMER_GROUP = 's_core_customergroups';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->connection = $this->container->get('dbal_connection');
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultPrice($optionId = null, $valueId = null)
    {
        $price = new Price();
        $price->setSurcharge(Calculator::DEFAULT_SURCHARGE);

        if ($optionId) {
            $price->setOptionId($optionId);
        }

        if ($valueId) {
            $price->setValueId($valueId);
        }

        $defaultTaxRate = $this->getDefaultTaxRate();

        if ($defaultTaxRate && $defaultTaxRate['id']) {
            $price->setTaxId($defaultTaxRate['id']);
        }

        $defaultCustomerGroup = $this->getDefaultCustomerGroup();

        if ($defaultCustomerGroup) {
            $price->setCustomerGroupName($defaultCustomerGroup['description']);
            $price->setCustomerGroupId($defaultCustomerGroup['id']);
        }

        return [
            $price,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createPricesFromCharge(array $charge, $optionId = null, $valueId = null)
    {
        $prices = [];
        $customerGroupIds = [];
        $taxRate = $this->getDefaultTaxRate();

        foreach ($charge['items'] as $chargeItem) {
            if ((int) $chargeItem['from'] !== 1) {
                continue;
            }

            if (in_array($chargeItem['customer_group_id'], $customerGroupIds)) {
                continue;
            }

            $customerGroupIds[] = $chargeItem['customer_group_id'];

            $price = new Price();
            $price->setTaxId($taxRate['id']);
            $price->setSurcharge(
                $this->calculateNetPrice(
                    (float) $chargeItem['value'],
                    $taxRate['tax']
                )
            );

            if ($optionId) {
                $price->setOptionId($optionId);
            }

            if ($valueId) {
                $price->setValueId($valueId);
            }

            $customerGroup = $this->container->get('models')
                ->find(Group::class, $chargeItem['customer_group_id']);

            if (!$customerGroup) {
                throw new \RuntimeException(
                    sprintf(
                        'The customer group with the id %d not found',
                        $chargeItem['customer_group_id']
                    )
                );
            }

            $price->setCustomerGroupName($customerGroup->getName());
            $price->setCustomerGroupId($customerGroup->getId());

            $prices[] = $price;
        }

        return $prices;
    }

    /**
     * Calculate the netPrice
     *
     * @param float $value
     * @param float $tax
     *
     * @return float
     */
    private function calculateNetPrice($value, $tax)
    {
        $increasedBasicValue = 100 + $tax;

        return ($value / $increasedBasicValue) * 100;
    }

    /**
     * read the default customer group by groupKey "EK"
     */
    private function getDefaultCustomerGroup()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from(self::TABLE_CUSTOMER_GROUP)
            ->where('groupkey = "EK"')
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Read the default taxRate... means the first in database.
     */
    private function getDefaultTaxRate()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from(self::TABLE_TAX)
            ->orderBy('id')
            ->setMaxResults(1)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
    }
}
