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

namespace SwagCustomProducts\Components\OrderNumberValidation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class OrderNumberValidationService implements OrderNumberValidationServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($orderNumber, $currentTemplateId)
    {
        $productOrderNumber = $this->checkForProductOrderNumber($orderNumber);
        if ($productOrderNumber) {
            throw new OrderNumberUsedByProductException();
        }

        $optionId = $this->checkForOptionOrderNumbers($orderNumber, $currentTemplateId);
        if ($optionId) {
            throw new OrderNumberUsedByOptionException($optionId);
        }

        $valueId = $this->checkForValueOrderNumber($orderNumber, $currentTemplateId);
        if ($valueId) {
            throw new OrderNumberUsedByValueException($valueId);
        }
    }

    /**
     * Checks if the given order number is already used by a product.
     *
     * @param string $orderNumber
     *
     * @return string|false
     */
    private function checkForProductOrderNumber($orderNumber)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->connection->createQueryBuilder();
        $builder->select('product.ordernumber')
            ->from('s_articles_details', 'product')
            ->where('product.ordernumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber);

        return $builder->execute()->fetchColumn();
    }

    /**
     * Checks if the given order number is already used by an option.
     *
     * @param string $orderNumber
     * @param int    $currentTemplateId
     *
     * @return int|false
     */
    private function checkForOptionOrderNumbers($orderNumber, $currentTemplateId)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->connection->createQueryBuilder();
        $builder->select('cpOption.id')
            ->from('s_plugin_custom_products_option', 'cpOption')
            ->where('cpOption.ordernumber = :orderNumber')
            ->andWhere('cpOption.template_id != :currentTemplateId')
            ->setParameter('orderNumber', $orderNumber)
            ->setParameter('currentTemplateId', $currentTemplateId);

        return (int) $builder->execute()->fetchColumn();
    }

    /**
     * Checks if the given order number is already used by a value.
     *
     * @param string $orderNumber
     * @param int    $currentTemplateId
     *
     * @return int|false
     */
    private function checkForValueOrderNumber($orderNumber, $currentTemplateId)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->connection->createQueryBuilder();
        $builder->select('cpValue.id')
            ->from('s_plugin_custom_products_value', 'cpValue')
            ->innerJoin('cpValue', 's_plugin_custom_products_option', 'cpOption', 'cpOption.id = cpValue.option_id')
            ->where('cpValue.ordernumber = :orderNumber')
            ->andWhere('cpOption.template_id != :currentTemplateId')
            ->setParameter('orderNumber', $orderNumber)
            ->setParameter('currentTemplateId', $currentTemplateId);

        return (int) $builder->execute()->fetchColumn();
    }
}
