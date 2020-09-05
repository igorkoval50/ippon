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
use Enlight_Components_Session_Namespace as Session;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use SwagCustomProducts\Components\Calculator;
use SwagCustomProducts\Structs\OptionStruct;
use SwagCustomProducts\Structs\OptionValueStruct;

class BasketManager implements BasketManagerInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var DateTimeService
     */
    private $dateTimeService;

    /**
     * @var TemplateServiceInterface
     */
    private $templateService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var HashManagerInterface
     */
    private $hashManager;

    /**
     * @var CustomProductsServiceInterface
     */
    private $customProductsService;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var ProductPriceGetterInterface
     */
    private $priceGetter;

    /**
     * @var LiveShoppingHelperInterface
     */
    private $liveShoppingHelper;

    public function __construct(
        Session $session,
        DateTimeServiceInterface $dateTimeService,
        TemplateServiceInterface $templateService,
        Connection $connection,
        HashManagerInterface $hashManager,
        CustomProductsServiceInterface $customProductsService,
        ContextServiceInterface $contextService,
        ProductPriceGetterInterface $priceGetter,
        LiveShoppingHelperInterface $liveShoppingHelper,
        DependencyProviderInterface $dependencyProvider
    ) {
        $this->session = $session;
        $this->dateTimeService = $dateTimeService;
        $this->templateService = $templateService;
        $this->connection = $connection;
        $this->hashManager = $hashManager;
        $this->customProductsService = $customProductsService;
        $this->contextService = $contextService;
        $this->calculator = $dependencyProvider->getCalculator();
        $this->priceGetter = $priceGetter;
        $this->liveShoppingHelper = $liveShoppingHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function addToBasket($product, $hash, $quantity)
    {
        $basketItem = $this->productIsInBasket($hash, $product->getNumber());
        if ($basketItem) {
            $this->updateQuantity($basketItem['hash'], $quantity);

            return $basketItem['basketId'];
        }

        $basketId = $this->insertInToBasket(
            $this->getArticleData($product, $quantity),
            $hash,
            false,
            BasketManagerInterface::MODE_PRODUCT
        );

        $configuration = $this->hashManager->findConfigurationByHash($hash);
        if (!$configuration) {
            $configuration = [];
        }

        $options = $this->customProductsService->getOptionsByConfiguration($configuration);

        /** @var OptionStruct $option */
        foreach ($options as $option) {
            $this->insertInToBasket(
                $this->getOptionData($option, $quantity, $product),
                $hash,
                $option->isOnceSurcharge,
                BasketManagerInterface::MODE_OPTION
            );
            foreach ($option->values as $value) {
                $this->insertInToBasket(
                    $this->getValueData($value, $quantity, $product),
                    $hash,
                    $value->isOnceSurcharge,
                    BasketManagerInterface::MODE_VALUE
                );
            }
        }

        return $basketId;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($hash, $quantity)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $ids = $this->getBasketIdsByHash($hash);

        $queryBuilder->update('s_order_basket')
            ->set('quantity', ':quantity')
            ->where('id IN (:ids)')
            ->setParameter('quantity', $quantity)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function readBasket()
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('basket.*, attr.*, hash.*')
            ->from('s_order_basket', 'basket')
            ->join('basket', 's_order_basket_attributes', 'attr', 'basket.id = attr.basketID')
            ->join('attr', 's_plugin_custom_products_configuration_hash', 'hash', 'hash.hash = attr.swag_custom_products_configuration_hash')
            ->where('basket.sessionID = :sessionId')
            ->andWhere('attr.swag_custom_products_configuration_hash IS NOT NULL')
            ->setParameter('sessionId', $this->session->get('sessionId'))
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function readBasketPosition($basketId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $result = $queryBuilder->select('*')
            ->from('s_order_basket', 'basket')
            ->join('basket', 's_order_basket_attributes', 'attr', 'basket.id = attr.basketID')
            ->where('basket.id = :id')
            ->setParameter('id', $basketId)
            ->execute()
            ->fetchAll();

        return array_shift($result);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFromBasket($hash)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $ids = $queryBuilder->select('basket.id')->from('s_order_basket', 'basket')
            ->join('basket', 's_order_basket_attributes', 'attr', 'basket.id = attr.basketID')
            ->where('attr.swag_custom_products_configuration_hash LIKE :hash')
            ->setParameter('hash', $hash)
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);

        $queryBuilder->delete('s_order_basket')
            ->where('id IN (:ids)')
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->execute();

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->delete('s_plugin_custom_products_configuration_hash')
            ->where('hash = :hash')
            ->setParameter('hash', $hash)
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function updateBasketTaxes()
    {
        $basketPositions = $this->readBasket();

        $context = $this->contextService->getShopContext();
        if ($context->getCountry() && $context->getCountry()->getId() != $this->session->get('sCountry')) {
            $this->contextService->initializeShopContext();
        }

        foreach ($basketPositions as $position) {
            if ((int) $position['swag_custom_products_mode'] === 1) {
                continue;
            }

            $this->updateTaxAndPrice($position);
        }
    }

    private function updateTaxAndPrice(array $basketPosition)
    {
        $taxId = $this->calculator->getTaxId((int) $basketPosition['swag_custom_products_mode'], $basketPosition['articleID']);
        $context = $this->contextService->getShopContext();

        if (!$taxId) {
            return;
        }

        $tax = $context->getTaxRule($taxId);

        $configuration = json_decode($basketPosition['configuration'], true);
        $ordernumber = $configuration['number'];
        $productPrice = $this->priceGetter->getProductPriceByNumber(
            $ordernumber,
            $basketPosition['quantity']
        );

        $currentCounterpart = $this->getCounterpart(
            $basketPosition['articleID'],
            $basketPosition['swag_custom_products_mode'],
            $productPrice,
            true
        );

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update('s_order_basket')
            ->set('price', ':price')
            ->set('netprice', ':netPrice')
            ->set('tax_rate', ':taxRate')
            ->where('id = :basketId')
            ->setParameters([
                ':price' => $currentCounterpart['surcharge'],
                ':netPrice' => $currentCounterpart['netPrice'],
                ':taxRate' => $tax->getTax(),
                ':basketId' => $basketPosition['basketID'],
            ])
            ->execute();
    }

    /**
     * read the real counterpart by id and mode
     *
     * @param int   $id
     * @param int   $customProductMode
     * @param float $productPrice
     * @param bool  $basketCalculation
     *
     * @return array|null
     */
    private function getCounterpart($id, $customProductMode, $productPrice, $basketCalculation = false)
    {
        if ((int) $customProductMode === BasketManagerInterface::MODE_OPTION) {
            return $this->templateService->getOptionById($id, $productPrice, $basketCalculation);
        }

        if ((int) $customProductMode === BasketManagerInterface::MODE_VALUE) {
            return $this->templateService->getValueById($id, $productPrice, $basketCalculation);
        }

        return null;
    }

    /**
     * @param string $hash
     * @param string $orderNumber
     *
     * @return bool|array
     */
    private function productIsInBasket($hash, $orderNumber)
    {
        $basket = $this->readBasket();

        if (!$basket) {
            return false;
        }

        $config = $this->hashManager->findConfigurationByHash($hash);
        if (!$config) {
            $config = [];
        }

        unset($config['custom_product_created_at']);

        $configHash = $this->hashManager->createHash($config);

        foreach ($basket as $basketItem) {
            $basketConfig = json_decode($basketItem['configuration'], true);
            unset($basketConfig['custom_product_created_at']);
            $basketHash = $this->hashManager->createHash($basketConfig);
            if ($basketHash === $configHash && $basketItem['ordernumber'] === $orderNumber) {
                return [
                    'basketId' => $basketItem['basketID'],
                    'hash' => $basketItem['swag_custom_products_configuration_hash'],
                ];
            }
        }

        return false;
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    private function getBasketIdsByHash($hash)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('basket.id')
            ->from('s_order_basket', 'basket')
            ->innerJoin(
                'basket',
                's_order_basket_attributes',
                'attributes',
                'basket.id = attributes.basketID'
            )
            ->where('basket.sessionID = :session')
            ->andWhere('attributes.swag_custom_products_configuration_hash = :hash')
            ->andWhere('attributes.swag_custom_products_once_price = 0')
            ->setParameter('session', $this->session->get('sessionId'))
            ->setParameter('hash', $hash)
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * updates the quantity in basket
     *
     * @param string $hash
     * @param int    $quantity
     */
    private function updateQuantity($hash, $quantity)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $ids = $this->getBasketIdsByHash($hash);

        $queryBuilder->update('s_order_basket')
            ->set('quantity', 'quantity + :quantity')
            ->where('id IN (:ids)')
            ->setParameter('quantity', $quantity)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    /**
     * $customProductMode.... product = 1, Surcharge option = 2, Surcharge value = 3
     *
     * @param array  $optionData
     * @param string $hash
     * @param bool   $isOnceSurcharge
     * @param int    $customProductMode
     *
     * @return string
     */
    private function insertInToBasket($optionData, $hash, $isOnceSurcharge, $customProductMode = 0)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert('s_order_basket')
            ->values([
                'sessionID' => ':sessionID',
                'userID' => ':userID',
                'articlename' => ':articlename',
                'articleID' => ':articleID',
                'ordernumber' => ':ordernumber',
                'shippingfree' => ':shippingfree',
                'quantity' => ':quantity',
                'price' => ':price',
                'netprice' => ':netprice',
                'tax_rate' => ':tax_rate',
                'datum' => ':datum',
                'modus' => ':modus',
                'esdarticle' => ':esdarticle',
                'partnerID' => ':partnerID',
                'lastviewport' => ':lastviewport',
                'useragent' => ':useragent',
                'config' => ':config',
                'currencyFactor' => ':currencyFactor',
            ]);

        foreach ($optionData as $key => $value) {
            $queryBuilder->setParameter($key, $value);
        }

        $queryBuilder->execute();

        $lastInsertId = $this->connection->lastInsertId();

        $this->updateBasketAttribute($lastInsertId, $hash, $customProductMode, $isOnceSurcharge);

        return $lastInsertId;
    }

    /**
     * @param int    $basketId
     * @param string $hash
     * @param int    $mode
     * @param bool   $isOnceSurcharge
     */
    private function updateBasketAttribute($basketId, $hash, $mode, $isOnceSurcharge)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert('s_order_basket_attributes')
            ->values([
                'basketID' => ':basketId',
                'swag_custom_products_configuration_hash' => ':hash',
                'swag_custom_products_once_price' => ':isOncePrice',
                'swag_custom_products_mode' => ':mode',
            ])
            ->setParameter('basketId', $basketId)
            ->setParameter('hash', $hash)
            ->setParameter('isOncePrice', $isOnceSurcharge)
            ->setParameter('mode', $mode)
            ->execute();
    }

    /**
     * @param int $quantity
     *
     * @return array
     */
    private function getOptionData(OptionStruct $option, $quantity, Product $product)
    {
        return [
            'sessionID' => $this->session->get('sessionId'),
            'userID' => $this->session->get('sUserId') || 0,
            'articlename' => $option->name,
            'articleID' => $option->id,
            'ordernumber' => $option->ordernumber === null ? '' : $option->ordernumber,
            'shippingfree' => $product->isShippingFree(),
            'quantity' => $option->isOnceSurcharge ? 1 : $quantity,
            'price' => $option->surcharge === null ? 0 : $option->surcharge,
            'netprice' => $option->netPrice === null ? 0 : $option->netPrice,
            'tax_rate' => $this->calculator->getTaxRateByTaxId($option->surchargeTaxRate),
            'datum' => $this->dateTimeService->getDateTime()->format(DateTimeServiceInterface::YMD_HIS),
            'modus' => 4,
            'esdarticle' => $product->getEsd() ? 1 : 0,
            'partnerID' => '',
            'lastviewport' => '',
            'useragent' => '',
            'config' => '',
            'currencyFactor' => $this->getCurrencyFactor(),
        ];
    }

    /**
     * @param int $quantity
     *
     * @return array
     */
    private function getValueData(OptionValueStruct $value, $quantity, Product $product)
    {
        return [
            'sessionID' => $this->session->get('sessionId'),
            'userID' => $this->session->get('sUserId') || 0,
            'articlename' => $value->name,
            'articleID' => $value->id,
            'ordernumber' => $value->ordernumber === null ? '' : $value->ordernumber,
            'shippingfree' => $product->isShippingFree(),
            'quantity' => $value->isOnceSurcharge ? 1 : $quantity,
            'price' => $value->surcharge === null ? 0 : $value->surcharge,
            'netprice' => $value->netPrice === null ? 0 : $value->netPrice,
            'tax_rate' => $this->calculator->getTaxRateByTaxId($value->surchargeTaxRate),
            'datum' => $this->dateTimeService->getDateTime()->format(DateTimeServiceInterface::YMD_HIS),
            'modus' => 4,
            'esdarticle' => $product->getEsd() ? 1 : 0,
            'partnerID' => '',
            'lastviewport' => '',
            'useragent' => '',
            'config' => '',
            'currencyFactor' => $this->getCurrencyFactor(),
        ];
    }

    /**
     * @param int $quantity
     *
     * @return array
     */
    private function getArticleData(Product $product, $quantity)
    {
        $prices = $product->getPrices();
        $price = array_shift($prices);

        //LiveShopping integration
        $liveShoppingAttribute = $this->liveShoppingHelper->getLiveShoppingProductAttribute($product);
        if ($liveShoppingAttribute) {
            $price->setCalculatedPrice($liveShoppingAttribute['currentPrice']);
        }

        return [
            'sessionID' => $this->session->get('sessionId'),
            'userID' => $this->session->get('sUserId') || 0,
            'articlename' => $product->getName() . ' ' . $product->getAdditional(),
            'articleID' => $product->getId(),
            'ordernumber' => $product->getNumber() === null ? '' : $product->getNumber(),
            'shippingfree' => $product->isShippingFree(),
            'quantity' => $quantity,
            'price' => $price->getCalculatedPrice(),
            'netprice' => $price->getRule()->getPrice(),
            'tax_rate' => $product->getTax()->getTax(),
            'datum' => $this->dateTimeService->getDateTime()->format(DateTimeServiceInterface::YMD_HIS),
            'modus' => 0,
            'esdarticle' => $product->getEsd() ? 1 : 0,
            'partnerID' => '',
            'lastviewport' => '',
            'useragent' => '',
            'config' => '',
            'currencyFactor' => $this->getCurrencyFactor(),
        ];
    }

    /**
     * @return float
     */
    private function getCurrencyFactor()
    {
        return $this->contextService->getShopContext()->getCurrency()->getFactor();
    }
}
