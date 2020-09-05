<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Basket implements BasketInterface
{
    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Basket constructor.
     *
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     * @param ContainerInterface                       $container
     */
    public function __construct(\Enlight_Components_Db_Adapter_Pdo_Mysql $db, ContainerInterface $container)
    {
        $this->db        = $db;
        $this->container = $container;
    }

    /**
     * @param array  $excludedModus
     * @param array  $includedModus
     * @param array  $filters
     * @param string $additionalSql
     * @param string $sessionID
     * @param bool   $splitByTaxRates
     * @return array|float
     * @throws ServiceNotFoundException
     * @throws ServiceCircularReferenceException
     */
    public function getBasketValue(
        $excludedModus = [],
        $includedModus = [],
        $filters = [],
        $additionalSql = '',
        $sessionID = null,
        $splitByTaxRates = false
    ) {
        // set current session ID
        if (null === $sessionID) {
            $sessionID = $this->getSession()->offsetGet('sessionId');
        }
        $params = [$sessionID];

        // set modus, that shall be excluded or included
        $modusSql = '';
        if (0 < count($excludedModus)) {
            $modusSql .= ' AND modus NOT IN (' . implode(',', $excludedModus) . ') ';
        }
        if (0 < count($includedModus)) {
            $modusSql .= ' AND modus IN (' . implode(',', $includedModus) . ') ';
        }

        // set other filters
        $filterSql = '';
        foreach ($filters as $filterType => $filterValues) {
            if ((is_array($filterValues) && empty($filterValues))
                || '' === $filterValues || '0' === $filterValues
            ) {
                continue;
            }
            $filterValues = is_array($filterValues) ? implode(',', $filterValues) : $filterValues;
            if (null === $filterValues) {
                continue;
            }
            if ('suppliers' === $filterType) {
                $filterSql .= ' AND ob.articleID IN (
                    SELECT DISTINCT a.id
                    FROM s_articles AS a
                    WHERE a.supplierID IN (' . $filterValues . ')
                ) ';
            } elseif ('categories' === $filterType) {
                $filterSql .= ' AND ob.articleID IN (
                    SELECT DISTINCT ac.articleID
                    FROM s_articles_categories AS ac
                    WHERE ac.categoryID IN (' . $filterValues . ')
                ) ';
            }
        }

        // create and execute SQL statement
        $sql = 'SELECT tax_rate, SUM(ob.quantity * (floor(ob.price * 100 + 0.55)/100)) AS totalAmount
            FROM s_order_basket AS ob
            LEFT JOIN s_order_basket_attributes AS oba
              ON oba.basketID = ob.id
            WHERE ob.sessionID = ? '
            . $additionalSql . $modusSql . $filterSql
            . ' GROUP BY sessionID, tax_rate ';

        $amountsByTaxRate = $this->db->fetchPairs($sql, $params);

        $totalAmount = 0.0;
        foreach ($amountsByTaxRate as $amount) {
            $totalAmount += (double)$amount;
        }

        if ($splitByTaxRates) {
            return $amountsByTaxRate + ['total' => $totalAmount];
        }

        return $totalAmount;
    }

    /**
     * @return \Enlight_Components_Session_Namespace
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    protected function getSession()
    {
        return $this->container->get('session');
    }
}