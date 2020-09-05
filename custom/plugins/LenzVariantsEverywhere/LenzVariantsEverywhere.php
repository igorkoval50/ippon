<?php

namespace LenzVariantsEverywhere;

use Doctrine\Common\Cache\Cache;
use Enlight_Controller_ActionEventArgs;
use Exception;
use LenzVariantsEverywhere\SearchBundleDBAL\Condition\ShowVariantsConditionHandler;
use LenzVariantsEverywhere\SearchBundleDBAL\CriteriaRequestHandler;
use LenzVariantsEverywhere\Service\MediaService;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\StoreFrontBundle\Service\AdditionalTextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware_Components_Config;

class LenzVariantsEverywhere extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_SearchBundle_Collect_Criteria_Request_Handlers' => 'registerRequestHandler',
            'Shopware_SearchBundleDBAL_Collect_Condition_Handlers' => 'registerConditionHandler',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecureFrontend',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPostDispatchSecureFrontend',
            'Legacy_Struct_Converter_Convert_List_Product' => 'onLegacyStructConverterConvertListProduct',
            'Enlight_Bootstrap_AfterInitResource_shopware_storefront.media_service' => 'decorateShopwareStorefrontMediaService',
        ];
    }

    public function install(InstallContext $context) {
        $this->createAttributes();

        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);

        return true;
    }

    public function update(UpdateContext $context) {
        $this->createAttributes();
        return true;
    }

    public function uninstall(UninstallContext $context) {
        if ( ! $context->keepUserData() ) {
            $this->deleteAttributes();
        } else {

        }
        return true;
    }

    public function activate(ActivateContext $context) {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
        return true;
    }

    public function deactivate(DeactivateContext $context) {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
        return true;
    }

    public function decorateShopwareStorefrontMediaService()
    {
        $coreService  = $this->container->get('shopware_storefront.media_service');
        $variantCoverService  = $this->container->get('shopware_storefront.variant_cover_service');
        $myMediaService = new MediaService($coreService, $variantCoverService);
        Shopware()->Container()->set('shopware_storefront.media_service', $myMediaService);
    }

    public function registerRequestHandler()
    {
        return new CriteriaRequestHandler();
    }

    public function registerConditionHandler()
    {
        /** @var Shopware_Components_Config $config */
        $config = $this->container->get('config');

        return new ShowVariantsConditionHandler(
            $config
        );
    }

    public function createAttributes() {
        $service = Shopware()->Container()->get('shopware_attribute.crud_service');
        $service->update('s_articles_attributes', 'lenz_variants_everywhere_show', 'boolean', [
            'label' => 'Variante in Listing zeigen [LVE]:',
            'displayInBackend' => true,
            'position' => 0,
            'custom' => false,
            'translatable' => true,
        ]);

        $service->update('s_articles_attributes', 'lenz_variants_everywhere_variantname', 'string', [
            'label' => 'Variantenname im Listing [LVE]:',
            'displayInBackend' => true,
            'position' => 0,
            'custom' => false,
            'translatable' => true,
        ]);

        $this->clearAttributeCache();
    }

    private function deleteAttributes() {
        /** @var CrudService $service */
        $service = Shopware()->Container()->get( 'shopware_attribute.crud_service' );

        try {
            $service->delete( 's_articles_attributes', 'lenz_variants_everywhere_show' );
        } catch (Exception $e) {

        }

        try {
            $service->delete( 's_articles_attributes', 'lenz_variants_everywhere_variantname' );
        } catch (Exception $e) {

        }

        $this->clearAttributeCache();
    }

    private function clearAttributeCache() {
        /** @var ModelManager $models */
        $models = $this->container->get("models");

        $metaDataCache = $models->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels( [ 's_articles_attributes' ] );
    }

    public function onPostDispatchSecureFrontend( Enlight_Controller_ActionEventArgs $args ) {
        if (!Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'show')) {
            // abort if shop is not activated
            return;
        }

        $controller = $args->getSubject();
        $view = $controller->View();

        $view->addTemplateDir(__DIR__ . '/Resources/Views/');
    }

    public function onLegacyStructConverterConvertListProduct(\Enlight_Event_EventArgs $args) {
        if (!Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'show')) {
            // abort if shop is not activated
            return;
        }

        $promotion = $args->getReturn();
        /** @var ListProduct $product */
        $product = $args->get('product');

        $cheapestPrice = null;
        $differentPricesCount = 0;
        foreach($product->getPrices() as $price) {
            if($cheapestPrice === null || $cheapestPrice->getCalculatedPrice() > $price->getCalculatedPrice()) {
                $cheapestPrice = $price;
                $differentPricesCount++;
            }
        }

        $promotion['lenzVariantsEverywhereVariantPrice'] = $cheapestPrice->getCalculatedPrice();
        $promotion['lenzVariantsEverywhereVariantPseudoPrice'] = $cheapestPrice->getCalculatedPseudoPrice();
        $promotion['lenzVariantsEverywhereVariantReferencePrice'] = $cheapestPrice->getCalculatedReferencePrice();
        $promotion['lenzVariantsEverywhereVariantPurchaseUnit'] = $cheapestPrice->getUnit()->getPurchaseUnit();
        $promotion['lenzVariantsEverywhereVariantReferenceUnit'] = $cheapestPrice->getUnit()->getReferenceUnit();
        $promotion['lenzVariantsEverywhereVariantUnit'] = $cheapestPrice->getUnit()->getName();
        //$promotion['lenzVariantsEverywhereVariantPriceFrom'] = $product->hasDifferentPrices();
        $promotion['lenzVariantsEverywhereVariantPriceFrom'] = ($differentPricesCount <= 1 ? false : true);

        if (class_exists('Shopware_Plugins_Frontend_PremsDiscountCategory_Bootstrap')) {
            $sql = 'SELECT 1 FROM s_core_plugins WHERE name = ? AND active = 1';
            $test = Shopware()->Db()->fetchOne($sql, array('PremsDiscountCategory'));

            if (!empty($test)) {
                $discountCategory = new \Shopware_Plugins_Frontend_PremsDiscountCategory_Bootstrap('discount');
                $discountValue = $discountCategory->ifArticleIsInDiscountCategoryReturnDiscount($product->getId(), false, $promotion['lenzVariantsEverywhereVariantPrice'], $promotion['lenzVariantsEverywhereVariantPseudoPrice']);

                if($discountValue !== null) {
                    $promotion['lenzVariantsEverywhereVariantPrice'] = $promotion['lenzVariantsEverywhereVariantPrice'] * $discountValue;
                    $promotion['lenzVariantsEverywhereVariantPseudoPrice'] = $product->getVariantPrice()->getCalculatedPrice();
                    $promotion['lenzVariantsEverywhereVariantReferencePrice'] = $promotion['lenzVariantsEverywhereVariantReferencePrice'] * $discountValue;
                }
            }
        }

        // todo: do this in ListProductService for better performance.
        if(empty($promotion['additionaltext'])) {
            /** @var AdditionalTextServiceInterface $additionalTextService */
            $additionalTextService = Shopware()->Container()->get('shopware_storefront.additional_text_service');
            /** @var ContextServiceInterface $contextService */
            $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
            $listProduct = $additionalTextService->buildAdditionalText($product, $contextService->getShopContext());
            $promotion['additionaltext'] = $listProduct->getAdditional();
        }

        // allow variant buying in listing
        if(
            $promotion['lenz_variants_everywhere_show'] == 1
           || Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'showOnlySpecifiedVariants') == false
        ) {
            $promotion['allowBuyInListing'] = true;
        }

        // add direct link to number only if it is a selected variant or if all variants are shown.
        if(
            $product->hasAvailableVariant()
            && (
                $promotion['lenz_variants_everywhere_show'] == 1
                || Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'showOnlySpecifiedVariants') == false
            )
        ) {
            $promotion['linkDetails'] .= '&number=' . $product->getNumber();
        }

        return $promotion;
    }
}
