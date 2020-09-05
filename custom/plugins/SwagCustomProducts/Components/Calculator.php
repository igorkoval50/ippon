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

namespace SwagCustomProducts\Components;

use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Tax;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\Customer;
use SwagCustomProducts\Components\Services\LiveShoppingHelperInterface;
use SwagCustomProducts\Components\Services\TemplateServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Calculator
{
    // CustomProduct modes as constants
    const MODE_OPTION = 2;
    const MODE_VALUE = 3;

    // constants for calculation
    const DEFAULT_SURCHARGE = 0.00;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var float
     */
    private $basePrice;

    /**
     * @var float
     */
    private $surchargesArray;

    /**
     * @var array
     */
    private $onceSurchargesArray;

    /**
     * @var float
     */
    private $totalOnceSurcharges;

    /**
     * @var float
     */
    private $totalSurcharges;

    /**
     * @var ContextService
     */
    private $contextService;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->totalSurcharges = self::DEFAULT_SURCHARGE;
        $this->totalOnceSurcharges = self::DEFAULT_SURCHARGE;
        $this->surchargesArray = [];
        $this->onceSurchargesArray = [];
        $this->basePrice = self::DEFAULT_SURCHARGE;
        $this->contextService = $container->get('shopware_storefront.context_service');
    }

    /**
     * @param string $productNumber
     * @param int    $quantity
     * @param bool   $basketCalculation
     *
     * @return array
     */
    public function calculate(array $options, array $configuration, $productNumber, $quantity = 1, $basketCalculation = false)
    {
        $this->basePrice = $this->getProductPrice($productNumber, $quantity, $basketCalculation);

        $this->iterate($options, $configuration);

        $unitPrice = $this->totalSurcharges + $this->basePrice;
        $total = $unitPrice * $quantity + $this->totalOnceSurcharges;

        $result = [
            'totalPriceSurcharges' => $this->totalSurcharges,
            'totalPriceOnce' => $this->totalOnceSurcharges,
            'surcharges' => $this->surchargesArray,
            'onceprices' => $this->onceSurchargesArray,
            'basePrice' => $this->basePrice,
            'totalUnitPrice' => $unitPrice,
            'total' => $total,
            'hasOnceSurcharges' => true,
        ];

        if ((int) $this->totalOnceSurcharges === 0) {
            $result['hasOnceSurcharges'] = false;
        }

        return $result;
    }

    /**
     * calculate the price
     *
     * @param array $surcharge
     * @param float $productPrice
     * @param bool  $basketCalculation
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function getPrice($surcharge, ShopContextInterface $context, $productPrice, $basketCalculation = false)
    {
        // if there no TaxId throw a Exception.
        if (!isset($surcharge['tax_id']) || empty($surcharge['tax_id'])) {
            throw new \RuntimeException('Cannot proceed without a tax ID');
        }

        $taxId = $surcharge['tax_id'];

        $isTaxFreeDelivery = $this->isTaxFreeDelivery();

        $price = $surcharge['surcharge'];

        if ($surcharge['is_percentage_surcharge'] === '1') {
            $price = ($productPrice / 100) * $surcharge['percentage'];
        }

        if (empty($price)) {
            return [
                'tax_id' => $taxId,
                'netPrice' => self::DEFAULT_SURCHARGE,
                'surcharge' => self::DEFAULT_SURCHARGE,
                'tax' => 0,
                'isTaxFreeDelivery' => $isTaxFreeDelivery,
            ];
        }

        $customerGroup = $context->getCurrentCustomerGroup();

        if ($customerGroup->useDiscount() && $customerGroup->getPercentageDiscount()) {
            $price = $price - ($price / 100 * $customerGroup->getPercentageDiscount());
        }

        $price = $price * $context->getCurrency()->getFactor();
        $gross = $price * (100 + $this->getTaxRateByTaxId($taxId)) / 100;

        $result = [
            'netPrice' => $price,
            'surcharge' => $price,
            'tax_id' => $taxId,
            'tax' => round($gross - $price, 2),
            'isTaxFreeDelivery' => $isTaxFreeDelivery,
        ];

        if ($basketCalculation === false) {
            $isTaxFreeDelivery = false;
        }

        if ($isTaxFreeDelivery || !$customerGroup->displayGrossPrices()) {
            return $result;
        }

        $result['surcharge'] = $gross;

        return $result;
    }

    /**
     * get the TaxId by customProductMode and (Option/Value - ID)
     *
     * @param int $customProductMode
     * @param int $id
     *
     * @return int|string
     */
    public function getTaxId($customProductMode, $id)
    {
        /** @var TemplateServiceInterface $templateService */
        $templateService = $this->container->get('custom_products.template_service');

        if ($customProductMode === self::MODE_OPTION) {
            $option = $templateService->getOptionById($id);

            return $option['tax_id'];
        }

        if ($customProductMode === self::MODE_VALUE) {
            $value = $templateService->getValueById($id);

            return $value['tax_id'];
        }
    }

    /**
     * @param int $id
     *
     * @return float
     */
    public function getTaxRateByTaxId($id)
    {
        if (!$id) {
            return 0;
        }

        /** @var Tax $tax */
        $tax = $this->contextService->getProductContext()->getTaxRule($id);

        return $tax->getTax();
    }

    /**
     * This method add the calculated prices to the properties and amount
     * them for displaying it in the Price Overview and the basket
     *
     * @param bool   $isOncePrice
     * @param string $name
     * @param float  $surcharge
     * @param bool   $isParent
     * @param bool   $hasParent
     */
    private function add(
        $isOncePrice,
        $name,
        $surcharge,
        $netPrice,
        $isParent = false,
        $hasParent = false
    ) {
        if (!$isParent && empty($surcharge)) {
            return;
        }

        $option = [
            'name' => $name,
            'price' => $surcharge,
            'netPrice' => $netPrice,
            'tax' => round($surcharge - $netPrice, 2),
            'isParent' => $isParent,
            'hasParent' => $hasParent,
            'hasSurcharge' => !empty($surcharge),
        ];

        if ($isOncePrice) {
            $this->onceSurchargesArray[] = $option;
            $this->totalOnceSurcharges += round($surcharge, 2);

            return;
        }

        $this->surchargesArray[] = $option;
        $this->totalSurcharges += round($surcharge, 2);
    }

    /**
     * Iterate over all options and values to add them to the calculation.
     */
    private function iterate(array $options, array $configuration)
    {
        foreach ($configuration as $optionId => $config) {
            $option = $this->find($optionId, $options);

            if (!$option) {
                continue;
            }

            if (!$option['could_contain_values']) {
                $this->add($option['is_once_surcharge'], $option['name'], $option['surcharge'], $option['netPrice']);
                continue;
            }

            // helper vars
            $optionIsAddedToOncePrice = false;
            $optionIsAddedToPrice = false;

            foreach ($config as $val) {
                $value = $this->find($val, $option['values']);

                if (!$value) {
                    continue;
                }

                list($optionIsAddedToOncePrice, $optionIsAddedToPrice) = $this->handleValues(
                    $value,
                    $optionIsAddedToOncePrice,
                    $option,
                    $optionIsAddedToPrice
                );
            }

            if ($option['is_once_surcharge'] && !$optionIsAddedToOncePrice && !empty($option['surcharge'])) {
                $this->add($option['is_once_surcharge'], $option['name'], $option['surcharge'], $option['netPrice']);
            }

            if (!$option['is_once_surcharge'] && !$optionIsAddedToPrice && !empty($option['surcharge'])) {
                $this->add($option['is_once_surcharge'], $option['name'], $option['surcharge'], $option['netPrice']);
            }
        }
    }

    /**
     * @param int $id
     */
    private function find($id, array $items)
    {
        foreach ($items as $item) {
            if ($id == $item['id']) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param bool $optionIsAddedToOncePrice
     * @param bool $optionIsAddedToPrice
     *
     * @return array
     */
    private function handleValues(array $value, $optionIsAddedToOncePrice, array $option, $optionIsAddedToPrice)
    {
        if (empty($value['surcharge'])) {
            return [
                $optionIsAddedToOncePrice,
                $optionIsAddedToPrice,
            ];
        }

        if ($value['is_once_surcharge']) {
            if (!$optionIsAddedToOncePrice) {
                if ($option['is_once_surcharge']) {
                    $this->add(true, $option['name'], $option['surcharge'], $option['netPrice'], true);
                } else {
                    $this->add(true, $option['name'], self::DEFAULT_SURCHARGE, self::DEFAULT_SURCHARGE, true);
                }

                $optionIsAddedToOncePrice = true;
            }

            $this->add($value['is_once_surcharge'], $value['name'], $value['surcharge'], $value['netPrice']);

            return [
                $optionIsAddedToOncePrice,
                $optionIsAddedToPrice,
            ];
        }

        if (!$optionIsAddedToPrice) {
            if ($option['is_once_surcharge']) {
                $this->add(false, $option['name'], self::DEFAULT_SURCHARGE, self::DEFAULT_SURCHARGE, true);
            } else {
                $this->add(false, $option['name'], $option['surcharge'], $option['netPrice'], true);
            }

            $optionIsAddedToPrice = true;
        }

        $this->add($value['is_once_surcharge'], $value['name'], $value['surcharge'], $value['netPrice'], true);

        return [
            $optionIsAddedToOncePrice,
            $optionIsAddedToPrice,
        ];
    }

    /**
     * @param string $number
     * @param int    $quantity
     * @param bool   $basketCalculation
     *
     * @return float
     */
    private function getProductPrice($number, $quantity, $basketCalculation = false)
    {
        $liveShoppingPrice = $this->checkForLiveShoppingPrice($number);
        if ($liveShoppingPrice !== false) {
            return $liveShoppingPrice;
        }

        $context = $this->container->get('shopware_storefront.context_service')->getShopContext();

        /** @var ListProduct $product */
        $product = $this->container->get('shopware_storefront.list_product_service')->get($number, $context);

        $price = $product->getVariantPrice();

        foreach ($product->getPrices() as $graduation) {
            if ($graduation->getFrom() <= $quantity && ($graduation->getTo() >= $quantity || $graduation->getTo() === null)) {
                $price = $graduation;
                break;
            }
        }

        $taxFreeDelivery = $this->isTaxFreeDelivery() && $basketCalculation;

        if ($taxFreeDelivery || !$context->getCurrentCustomerGroup()->displayGrossPrices()) {
            return $this->calculateProductNetPrice($price->getRule()->getPrice(), $context);
        }

        return round($price->getCalculatedPrice(), 2);
    }

    /**
     * @param float $price
     *
     * @return float
     */
    private function calculateProductNetPrice($price, ShopContextInterface $context)
    {
        $customerGroup = $context->getCurrentCustomerGroup();
        if ($customerGroup->useDiscount() && $customerGroup->getPercentageDiscount()) {
            $price = $price - ($price / 100 * $customerGroup->getPercentageDiscount());
        }
        $price = $price * $context->getCurrency()->getFactor();

        return round($price, 2);
    }

    /**
     * @return bool
     */
    private function isTaxFreeDelivery()
    {
        $deliveryAddress = $this->getDeliveryAddress();

        if (!$deliveryAddress) {
            return false;
        }

        $deliveryCountry = $deliveryAddress->getCountry();

        if ($deliveryCountry->getTaxFree()) {
            return true;
        }

        return $deliveryCountry->getTaxFreeUstId() && !empty($deliveryAddress->getVatId());
    }

    /**
     * @return Address|null
     */
    private function getDeliveryAddress()
    {
        $session = $this->container->get('session');

        //customer switched checkout billing address
        if ($id = $session->offsetGet('checkoutShippingAddressId')) {
            return $this->container->get('models')->find(Address::class, $id);
        }

        //customer isn't logged in
        if (!($id = $session->offsetGet('sUserId'))) {
            return null;
        }

        $customer = $this->container->get('models')->find(Customer::class, $id);

        return $customer->getDefaultShippingAddress();
    }

    /**
     * @param string $number
     *
     * @return float|bool
     */
    private function checkForLiveShoppingPrice($number)
    {
        if ($this->container->has('swag_liveshopping.live_shopping')) {
            /** @var LiveShoppingHelperInterface $liveShoppingHelper */
            $liveShoppingHelper = $this->container->get('custom_products.live_shopping_helper');

            return $liveShoppingHelper->checkForLiveShoppingPrice($number, $this->container->get('swag_liveshopping.live_shopping'));
        }

        return false;
    }
}
