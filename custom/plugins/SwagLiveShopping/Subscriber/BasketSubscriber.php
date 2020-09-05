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

namespace SwagLiveShopping\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\Customer\Group;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Detail;
use SwagLiveShopping\Components\LiveShoppingBasketInterface;
use SwagLiveShopping\Components\LiveShoppingInterface;
use SwagLiveShopping\Components\PriceServiceInterface;
use SwagLiveShopping\Models\LiveShopping;

class BasketSubscriber implements SubscriberInterface
{
    /**
     * @var LiveShoppingBasketInterface
     */
    private $liveShoppingBasket;

    /**
     * @var LiveShoppingInterface
     */
    private $liveShopping;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var PriceServiceInterface
     */
    private $priceService;

    /**
     * @var ContextService
     */
    private $contextService;

    public function __construct(
        LiveShoppingBasketInterface $liveShoppingBasket,
        LiveShoppingInterface $liveShopping,
        ModelManager $modelManager,
        PriceServiceInterface $priceService,
        ContextService $contextService
    ) {
        $this->liveShoppingBasket = $liveShoppingBasket;
        $this->liveShopping = $liveShopping;
        $this->modelManager = $modelManager;
        $this->priceService = $priceService;
        $this->contextService = $contextService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'sBasket::sAddArticle::before' => ['onBeforeAddArticle', 999],
            'Shopware_Modules_Basket_UpdateArticle_Start' => 'onUpdateStart',
        ];
    }

    /**
     * Enlight event listener function of the sBasket()->sAddArticle() function.
     * The event is subscribed as replace event.
     * If no case of the bundle module occurred, the default function will be executed.
     */
    public function onBeforeAddArticle(\Enlight_Hook_HookArgs $arguments)
    {
        $orderNumber = $arguments->get('id');
        $quantity = $arguments->get('quantity');

        $variant = $this->getVariant($orderNumber);
        if (!$variant) {
            return;
        }

        $liveShopping = $this->liveShopping->getActiveLiveShoppingForVariant($variant);
        if (!$liveShopping) {
            return;
        }

        if (!$this->liveShopping->isVariantAllowed($liveShopping, $variant)) {
            return;
        }

        $this->liveShoppingBasket->addProduct(
            $orderNumber,
            $quantity,
            ['forceNewPosition' => true, 'liveShopping' => $liveShopping]
        );
    }

    /**
     * @return bool|null
     */
    public function onUpdateStart(\Enlight_Event_EventArgs $args)
    {
        $basketId = $args->get('id');
        $connection = $this->modelManager->getConnection();

        $basket = $connection->createQueryBuilder()
            ->select(['basket.*', 'attributes.swag_live_shopping_id'])
            ->from('s_order_basket', 'basket')
            ->join('basket', 's_order_basket_attributes', 'attributes', 'basket.id = attributes.basketID')
            ->where('basket.id = :basketId')
            ->setParameter('basketId', $basketId)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        if (!$basket['swag_live_shopping_id']) {
            return null;
        }

        $variant = $this->getVariant($basket['ordernumber']);
        if (!$variant) {
            return null;
        }

        $liveShopping = $this->liveShopping->getActiveLiveShoppingForVariant($variant);
        if (!$liveShopping) {
            return null;
        }

        if (!$this->liveShopping->isVariantAllowed($liveShopping, $variant)) {
            return null;
        }

        $price = $this->getPrice($liveShopping, $basket);

        if ($basket['price'] !== $price['price']) {
            $basket['price'] = $price['price'];
            $basket['netPrice'] = $price['netPrice'];
            $basket['taxrate'] = $price['taxrate'];
            $this->liveShoppingBasket->updateItem($basketId, $basket, $variant, $basket['quantity'], []);
        }

        return true;
    }

    /**
     * @param string $orderNumber
     *
     * @return Detail|null
     */
    private function getVariant($orderNumber)
    {
        return $this->modelManager
            ->getRepository(Detail::class)
            ->findOneBy(['number' => $orderNumber]);
    }

    private function getPrice(LiveShopping $liveShopping, array $basket): array
    {
        $buyTime = new \DateTime($basket['datum']);

        $price = $this->priceService->getLiveShoppingPrice(
            $liveShopping->getId(),
            $liveShopping->getType(),
            $buyTime,
            $liveShopping->getValidFrom(),
            $liveShopping->getValidTo()
        );

        $isTaxFreeCountry = false;
        $country = $this->contextService->getShopContext()->getCountry();
        $customerGroup = $this->contextService->getShopContext()->getCurrentCustomerGroup();

        $netPrice = $this->getNetPrice($basket, $customerGroup, $price);

        if ($country) {
            $isTaxFreeCountry = $country->isTaxFree() && ($customerGroup->displayGrossPrices() && $customerGroup->insertedGrossPrices());
        }

        if ($isTaxFreeCountry) {
            $price = $netPrice;
        }

        return [
            'price' => $price,
            'netPrice' => $netPrice,
            'taxrate' => $this->priceService->getTaxRate($liveShopping->getId()),
        ];
    }

    private function getNetPrice(array $basket, Group $customerGroup, float $price): float
    {
        if ($customerGroup->insertedGrossPrices()) {
            if ($customerGroup->displayGrossPrices()) {
                return $this->calculateNetPrice($basket['tax_rate'], $price);
            }

            return $price;
        }

        if ($customerGroup->displayGrossPrices()) {
            return $this->calculateNetPrice($basket['tax_rate'], $price);
        }

        return $price;
    }

    private function calculateNetPrice(float $taxRate, float $price): float
    {
        return (float) abs($price / (100 + $taxRate) * 100);
    }
}
