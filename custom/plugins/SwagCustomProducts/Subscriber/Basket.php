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

namespace SwagCustomProducts\Subscriber;

use Doctrine\DBAL\Query\QueryBuilder;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs as EventArgs;
use Enlight_Hook_HookArgs as HookArgs;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use SwagCustomProducts\Components\DataConverter\ConverterInterface;
use SwagCustomProducts\Components\DataConverter\RegistryInterface;
use SwagCustomProducts\Components\Services\BasketManagerInterface;
use SwagCustomProducts\Components\Services\CustomProductsServiceInterface;
use SwagCustomProducts\Components\Services\HashManagerInterface;
use SwagCustomProducts\Components\Services\TranslationServiceInterface;

class Basket implements SubscriberInterface
{
    /**
     * @var DIContainer
     */
    private $container;

    /**
     * @var array
     */
    private $optionWhiteList = ['date', 'numberfield', 'textarea', 'textfield', 'time', 'wysiwyg'];

    public function __construct(DIContainer $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Basket_AddArticle_Start' => 'addArticle',
            'sBasket::sDeleteArticle::before' => 'afterDeleteArticle',
            'sBasket::sGetAmountArticles::after' => 'getVoucherAmount',
            'Shopware_Modules_Basket_GetBasket_FilterSQL' => 'getBasketSqlQuery',
            'sBasket::sGetBasket::after' => 'getBasket',
            'sBasket::updateCartItems::after' => 'afterUpdateCartItems',
            'Shopware_Modules_Basket_UpdateArticle_Start' => 'onUpdateArticle',
            'Shopware_Modules_Basket_GetBasket_FilterItemStart' => 'onFilterItemStart',
            'Shopware_Modules_Basket_GetBasket_FilterItemEnd' => 'onFilterItemEnd',
            'sAdmin::sGetDispatchBasket::after' => 'onBeforeGetShippingCosts',
            'Shopware_Modules_Basket_AddArticle_CheckBasketForArticle' => 'onCheckBasketForArticle',
        ];
    }

    /**
     * Extends the amount which is used for percentage voucher calculation
     */
    public function getVoucherAmount(HookArgs $args)
    {
        $return = $args->getReturn();

        if (!$return['totalAmount']) {
            return;
        }

        $amount = $this->getCustomProductSurchargesAmount();

        if (!$amount) {
            return;
        }

        $return['totalAmount'] += $amount;

        $args->setReturn($return);
    }

    /**
     * @return array
     */
    public function onFilterItemStart(EventArgs $args)
    {
        $article = $args->getReturn();

        if ((int) $article['modus'] === 4 && !empty($article['customProductHash'])) {
            $article['swag_custom_product_original_mode'] = 4;
            $article['modus'] = 0;
        }

        return $article;
    }

    /**
     * @return array
     */
    public function onFilterItemEnd(EventArgs $args)
    {
        $article = $args->getReturn();

        if (isset($article['swag_custom_product_original_mode'])
            && (int) $article['swag_custom_product_original_mode'] === 4
        ) {
            $article['modus'] = 4;
        }

        return $article;
    }

    /**
     * Updates taxes of custom products options and values.
     */
    public function onUpdateArticle()
    {
        $session = $this->container->get('session');

        if ($session->offsetExists('swag_custom_products_cart_update')) {
            return;
        }

        /** @var BasketManagerInterface $basketManager */
        $basketManager = $this->container->get('custom_products.basket_manager');
        $basketManager->updateBasketTaxes();
        $session->offsetSet('swag_custom_products_cart_update', true);
    }

    /**
     * @return bool|null
     */
    public function addArticle(EventArgs $args)
    {
        $orderNumber = $args->get('id');
        $quantity = $args->get('quantity');
        $hash = $this->container->get('front')->Request()->get('customProductsHash');

        /** @var CustomProductsServiceInterface $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');

        if (!$hash || !$customProductsService->isCustomProduct($orderNumber)) {
            return null;
        }

        $contextService = $this->container->get('shopware_storefront.context_service');
        $context = $contextService->getShopContext();
        $storeFrontService = $this->container->get('shopware_storefront.product_service');
        $product = $storeFrontService->get($orderNumber, $context);
        $additionalTextService = $this->container->get('shopware_storefront.additional_text_service');
        $product = $additionalTextService->buildAdditionalText($product, $context);

        /** @var BasketManagerInterface $basketManager */
        $basketManager = $this->container->get('custom_products.basket_manager');
        $basketId = $basketManager->addToBasket($product, $hash, $quantity);

        $this->container->get('template')->assign('lastInsertedCustomProductBasketId', $basketId);

        // we return true because we need to stop the sAddArticle action.
        return true;
    }

    public function afterDeleteArticle(HookArgs $args)
    {
        $basketId = $args->get('id');

        /** @var BasketManagerInterface $basketManager */
        $basketManager = $this->container->get('custom_products.basket_manager');
        $basket = $basketManager->readBasketPosition($basketId);
        $hash = $basket['swag_custom_products_configuration_hash'];
        $basketManager->deleteFromBasket($hash);
    }

    /**
     * @return string
     */
    public function getBasketSqlQuery(EventArgs $args)
    {
        $sql = $args->getReturn();
        $search = 's_order_basket_attributes.attribute6 as ob_attr6';
        $replace = 's_order_basket_attributes.attribute6 as ob_attr6,
                    s_order_basket_attributes.swag_custom_products_configuration_hash as customProductHash,
                    s_order_basket_attributes.swag_custom_products_once_price as customProductIsOncePrice,
                    s_order_basket_attributes.swag_custom_products_mode as customProductMode';

        $sql = str_replace($search, $replace, $sql);

        return $sql;
    }

    public function getBasket(HookArgs $args)
    {
        /** @var RegistryInterface $converterRegistry */
        $converterRegistry = $this->container->get('custom_products.data_converter.registry');

        /** @var HashManagerInterface $hashManager */
        $hashManager = $this->container->get('custom_products.hash_manager');

        /** @var CustomProductsServiceInterface $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');

        /** @var TranslationServiceInterface $translationService */
        $translationService = $this->container->get('custom_products.translation_service');

        $blackList = [
            'number',
            'custom_product_created_at',
        ];

        $basket = $args->getReturn();
        $content = $basket['content'];

        $options = null;
        foreach ($content as &$basketPosition) {
            if ((int) $basketPosition['customProductMode'] === BasketManagerInterface::MODE_PRODUCT) {
                $configuration = $hashManager->findConfigurationByHash($basketPosition['customProductHash']);
                if (!$configuration) {
                    $configuration = [];
                }

                $options = [];
                foreach ($configuration as $key => $optionConfig) {
                    if (in_array($key, $blackList, true)) {
                        continue;
                    }

                    $option = $customProductsService->getOptionById(
                        $key,
                        $configuration,
                        true,
                        $basketPosition
                    );

                    if (!$option) {
                        $option = [];
                    }

                    $option = $translationService->getTranslatedOption($option);

                    if (!isset($option['type'])) {
                        continue;
                    }

                    /** @var ConverterInterface $converter */
                    $converter = $converterRegistry->get($option['type']);
                    $options[$option['id']] = $converter->convertBasketData($option, $optionConfig);
                }

                $basketPosition['custom_product_adds'] = $options;

                $basketPosition['custom_product_prices'] = $this->getPrice(
                    $options,
                    $configuration,
                    $basketPosition['ordernumber'],
                    $basketPosition['quantity']
                );
            } elseif ((int) $basketPosition['customProductMode'] === BasketManagerInterface::MODE_OPTION) {
                $optionId = $basketPosition['articleID'];

                if (!isset($options[$optionId])) {
                    continue;
                }

                $option = $options[$optionId];

                $basketPosition['articlename'] = $option['name'];

                if (!in_array($option['type'], $this->optionWhiteList, true)) {
                    continue;
                }

                $basketPosition['customProductOption'] = $option;
            } elseif ((int) $basketPosition['customProductMode'] === BasketManagerInterface::MODE_VALUE) {
                $basketPosition = $this->handleValue($basketPosition, $options);
            }
        }
        unset($basketPosition);

        $basket['content'] = $content;

        return $basket;
    }

    public function afterUpdateCartItems()
    {
        $session = $this->container->get('session');

        if ($session->offsetExists('swag_custom_products_cart_update')) {
            $session->offsetUnset('swag_custom_products_cart_update');
        }
    }

    /**
     * For a correct shippingCosts calculation, add customProductSurcharges to the amount.
     */
    public function onBeforeGetShippingCosts(\Enlight_Hook_HookArgs $args)
    {
        $return = $args->getReturn();

        if (empty($return)) {
            return;
        }

        $customProductsAmount = $return['amount'] + $this->getCustomProductSurchargesAmount();
        $return['amount'] = $customProductsAmount;

        $args->setReturn($return);
    }

    /**
     * Adds a check for the custom products hash.
     */
    public function onCheckBasketForArticle(\Enlight_Event_EventArgs $args)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $args->get('queryBuilder');
        $selects = $queryBuilder->getQueryPart('select');

        $mapping = [
            'id' => 'basket.id',
            'quantity' => 'basket.quantity',
        ];

        foreach ($selects as &$singleSelect) {
            if (!array_key_exists($singleSelect, $mapping)) {
                continue;
            }

            $singleSelect = $mapping[$singleSelect];
        }
        unset($singleSelect);

        $queryBuilder->resetQueryPart('select');
        $queryBuilder->select($selects)
            ->innerJoin('basket', 's_order_basket_attributes', 'basketAttr', 'basketAttr.basketID = basket.id')
            ->andWhere('basketAttr.swag_custom_products_configuration_hash IS NULL');
    }

    /**
     * @param string $number
     *
     * @return array
     */
    private function getPrice($options, $configuration, $number, $quantity)
    {
        $calculator = $this->container->get('custom_products.dependency_provider')->getCalculator();
        $result = $calculator->calculate($options, $configuration, $number, $quantity, true);

        return [
            'quantity' => $quantity,
            // price of the baseProduct
            'basePrice' => $result['basePrice'],
            // price of the surcharges
            'onlySurcharges' => $result['totalPriceSurcharges'] + $result['totalPriceOnce'],
            // price of one Custom Product with surcharges
            'customProduct' => $result['totalPriceSurcharges'] + $result['totalPriceOnce'] + $result['basePrice'],
            // price of (1 x (QUANTITY)) CustomProduct's
            'total' => (($result['totalPriceSurcharges'] * $quantity) + $result['totalPriceOnce']) + ($result['basePrice'] * $quantity),
            // price of the Surcharge x (QUANTITY)
            'surchargesTotal' => ($result['totalPriceSurcharges'] * $quantity) + $result['totalPriceOnce'],
        ];
    }

    /**
     * @return float|false
     */
    private function getCustomProductSurchargesAmount()
    {
        /** @var QueryBuilder $query */
        $query = $this->container->get('dbal_connection')->createQueryBuilder();
        $query->select(['SUM(quantity*(floor(basket.price * 100 + .55)/100))']);
        $query->from('s_order_basket', 'basket');
        $query->innerJoin('basket', 's_order_basket_attributes', 'attribute', 'attribute.basketID = basket.id');
        $query->andWhere('basket.modus = 4');
        $query->andWhere('attribute.swag_custom_products_configuration_hash IS NOT NULL');
        $query->andWhere('basket.sessionID = :sessionId');
        $query->setParameter('sessionId', $this->container->get('session')->get('sessionId'));
        $amount = $query->execute()->fetch(\PDO::FETCH_COLUMN);

        return (float) $amount;
    }

    /**
     * Handles the translation of the value.
     *
     * @return array
     */
    private function handleValue(array $orderPosition, array $options)
    {
        $valueId = $orderPosition['articleID'];
        $currentValue = $this->findCurrentValueInOptions((int) $valueId, $options);
        if (!$currentValue) {
            return $orderPosition;
        }

        $orderPosition['articlename'] = $currentValue['name'];

        return $orderPosition;
    }

    /**
     * Finds the value in the options-array by the id of the value.
     *
     * @param int $valueId
     *
     * @return array
     */
    private function findCurrentValueInOptions($valueId, array $options)
    {
        foreach ($options as $option) {
            foreach ($option['values'] as $value) {
                if ((int) $value['id'] === $valueId) {
                    return $value;
                }
            }
        }

        return [];
    }
}
