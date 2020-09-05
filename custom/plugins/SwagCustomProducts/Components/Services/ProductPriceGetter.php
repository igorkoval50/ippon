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

use Doctrine\ORM\EntityManagerInterface;
use Enlight_Components_Session_Namespace;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\Customer;

class ProductPriceGetter implements ProductPriceGetterInterface
{
    /**
     * @var ListProductServiceInterface
     */
    private $listProductService;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @var Container
     */
    private $container;

    public function __construct(
        ListProductServiceInterface $listProductService,
        ContextServiceInterface $contextService,
        EntityManagerInterface $entityManager,
        Container $container
    ) {
        $this->listProductService = $listProductService;
        $this->contextService = $contextService;
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductPriceByNumber($number, $quantity = 1, $basketCalculation = false)
    {
        $context = $this->contextService->getShopContext();

        /** @var ListProduct $product */
        $product = $this->listProductService->get($number, $context);

        if (!$product instanceof ListProduct) {
            return 0.0;
        }

        $quantity = max($quantity, $product->getUnit()->getMinPurchase());

        $price = $this->checkForLiveShoppingPrice($number);

        //If it's no live shopping product, we can use the regular price calculation
        if ($price === false) {
            foreach ($product->getPrices() as $graduation) {
                if ($graduation->getFrom() <= $quantity && ($graduation->getTo() >= $quantity || $graduation->getTo() === null)) {
                    $price = $graduation;
                    break;
                }
            }

            $price = $price->getRule()->getPrice();
        }

        $taxFreeDelivery = $this->isTaxFreeDelivery() && $basketCalculation;

        if ($taxFreeDelivery || !$context->getCurrentCustomerGroup()->displayGrossPrices()) {
            $customerGroup = $context->getCurrentCustomerGroup();

            if ($customerGroup->useDiscount() && $customerGroup->getPercentageDiscount()) {
                $price = $price - ($price / 100 * $customerGroup->getPercentageDiscount());
            }

            $price = $price * $context->getCurrency()->getFactor();
        }

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
        /* Could not inject the session in the constructor because the backend has dependencies to this service */
        if (!$this->container->has('session')) {
            return null;
        }

        //customer switched checkout billing address
        if ($id = $this->getSession()->offsetGet('checkoutShippingAddressId')) {
            return $this->entityManager->find(Address::class, $id);
        }

        //customer isn't logged in
        if (!($id = $this->getSession()->offsetGet('sUserId'))) {
            return null;
        }

        $customer = $this->entityManager->find(Customer::class, $id);

        return $customer->getDefaultShippingAddress();
    }

    /**
     * @return Enlight_Components_Session_Namespace
     */
    private function getSession()
    {
        if (!$this->session) {
            $this->session = $this->container->get('session');
        }

        return $this->session;
    }

    /**
     * This function checks if the product is of type LiveShopping.
     * If so, the LiveShopping price will be obtained.
     *
     * @param string $number
     *
     * @return float|bool
     */
    private function checkForLiveShoppingPrice($number)
    {
        if ($this->container->has('swag_liveshopping.live_shopping')) {
            /** @var LiveShoppingHelperInterface $liveShoppingHelper */
            $liveShoppingHelper = $this->container->get('custom_products.live_shopping_helper');

            return $liveShoppingHelper->checkForLiveShoppingPrice($number, $this->container->get('swag_liveshopping.live_shopping'), false);
        }

        return false;
    }
}
