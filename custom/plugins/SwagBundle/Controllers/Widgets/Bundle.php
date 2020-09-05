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

use SwagBundle\Components\BundleComponentInterface;
use SwagBundle\Models\Article as BundleProduct;
use SwagBundle\Models\Bundle;
use SwagBundle\Services\BundleAvailableServiceInterface;

/**
 * Shopware Widget Controller
 *
 * @category  Shopware
 *
 * @copyright Copyright (c), shopware AG (http://en.shopware.com)
 */
class Shopware_Controllers_Widgets_Bundle extends Enlight_Controller_Action
{
    public function updateBundlePriceAction()
    {
        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $this->get('front')->Plugins()->Json()->setRenderer();

        $bundleId = (int) $this->Request()->getParam('bundleId', 0);

        if ($bundleId === 0) {
            return;
        }

        /** @var Bundle $bundle */
        $bundle = $this->get('models')->find(Bundle::class, $bundleId);

        $selection = $this->getSelection($bundle);

        $bundle->setArticles($selection);

        $configuration = $this->getBundleConfigurationFromRequest($bundle->getType(), $selection);

        $bundle = $this->get('swag_bundle.full_bundle_service')->getCalculatedBundle($bundle, '', false, null, $configuration, $selection);

        if (is_array($bundle)) {
            $this->view->assign($bundle);

            return;
        }

        $this->view->assign(
            'prices',
            $this->getPrices($bundle)
        );
    }

    /**
     * Checks if the current product is available for the bundle
     */
    public function isBundleAvailableAction()
    {
        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $this->get('front')->Plugins()->Json()->setRenderer();

        $orderNumber = $this->Request()->get('number');
        $bundleId = (int) $this->Request()->get('bundleId');
        $mainProductId = (int) $this->Request()->get('mainProductId');

        /** @var BundleAvailableServiceInterface $bundleAvailableService */
        $bundleAvailableService = $this->get('swag_bundle.available_service');
        $this->View()->assign('data', [
            'isAvailable' => $bundleAvailableService->isBundleAvailable($mainProductId, $bundleId, $orderNumber),
            'isVariantProduct' => $this->get('swag_bundle.products.repository')->isNumberFromVariantProduct($orderNumber),
        ]);
    }

    /**
     * Global interface to add a single bundle to the basket.
     */
    public function addBundleToBasketAction()
    {
        $bundleId = (int) $this->Request()->getParam('bundleId');

        if ($bundleId <= 0) {
            return;
        }

        /** @var Bundle $bundle */
        $bundle = $this->get('models')->find(Bundle::class, $bundleId);

        //If the bundle doesn't exist or isn't active,
        //we can redirect to the same page again to display an error message for the customer.
        if ($bundle === null || !$bundle->getActive()) {
            $productId = (int) $this->Request()->getParam('productId');
            $this->redirect(['controller' => 'detail', 'sArticle' => $productId, 'bundleMessage' => 'notAvailable']);

            return;
        }

        $selection = $this->getSelection($bundle);

        if ($bundle->getType() === BundleComponentInterface::SELECTABLE_BUNDLE && count($selection) === 0) {
            $this->redirect(['controller' => 'detail', 'sArticle' => $bundle->getArticle()->getId()]);

            return;
        }

        $configuration = $this->getBundleConfigurationFromRequest($bundle->getType(), $selection);

        $result = $this->get('swag_bundle.bundle_component')->addBundleToBasket(
            $bundleId,
            $selection,
            $configuration
        );

        $this->get('events')->notify('bundleAddToBasket', ['articleId' => $bundle->getArticleId()]);

        if ($result['success'] === false) {
            $this->redirect(['controller' => 'detail', 'sArticle' => $bundle->getArticle()->getId()]);

            return;
        }

        $this->redirect(['controller' => 'checkout', 'action' => 'cart']);
    }

    /**
     * @param int $bundleType
     *
     * @return array
     */
    private function getBundleConfigurationFromRequest($bundleType, array $selection)
    {
        $helperService = $this->get('swag_bundle.helper_service');
        $bundleId = $this->request->getParam('bundleId');

        $result = [$bundleId => []];
        foreach ($this->request->getParams() as $key => $value) {
            if (!$helperService->isConfigParameter($key)) {
                continue;
            }

            if (!$helperService->canConfigBeAdded($bundleType, $selection, $key)) {
                continue;
            }

            $result[$bundleId][$helperService->getProductId($key)][$helperService->prepareArrayKey($key)] = $value;
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getSelection(Bundle $bundle)
    {
        $selection = [];
        if ($bundle->getType() === BundleComponentInterface::SELECTABLE_BUNDLE) {
            /** @var BundleProduct $bundleProduct */
            foreach ($bundle->getArticles() as $bundleProduct) {
                if ($this->request->has('bundle-product-' . $bundleProduct->getId())) {
                    $selection[] = $bundleProduct;
                }
            }

            return $selection;
        }

        return array_merge($selection, $bundle->getArticles()->toArray());
    }

    private function getPrices(Bundle $bundle): array
    {
        $customerGroup = $this->get('shopware_storefront.context_service')->getShopContext()->getCurrentCustomerGroup();

        $currentPrice = null;
        foreach ($bundle->getUpdatedPrices() as $price) {
            if ($price->getCustomerGroup()->getId() === $customerGroup->getId()) {
                $currentPrice = $price;
                break;
            }
        }

        $isTaxFree = $this->get('session')->get('taxFree') || !$customerGroup->displayGrossPrices();
        $discount = $bundle->getDiscount();

        $currency = $this->container->get('currency');

        $productPrices = [];
        foreach ($bundle->getProductData() as $product) {
            $product['basePrice']['referencePrice']['display'] = $currency->toCurrency($product['basePrice']['referencePrice']['numeric']);
            $productPrices[sprintf("'%s'", $product['bundleArticleId'])] = [
                'price' => $currency->toCurrency($product['price']['numeric']),
                'referencePrice' => $product['basePrice'],
            ];
        }

        $prices = $this->get('swag_bundle.helper_service')->createPrice($isTaxFree, $currentPrice, $discount);
        $prices['productPrices'] = $productPrices;

        return $prices;
    }
}
