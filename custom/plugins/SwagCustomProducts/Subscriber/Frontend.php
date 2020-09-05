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

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use SwagCustomProducts\Components\FileUpload\FileSizeFormatterInterface;
use SwagCustomProducts\Components\Services\ProductPriceGetterInterface;
use SwagCustomProducts\Components\Services\TemplateServiceInterface;
use SwagCustomProducts\Components\Services\TranslationServiceInterface;
use SwagCustomProducts\Components\Types\Types\FileUploadType;
use SwagCustomProducts\Components\Types\Types\ImageUploadType;

class Frontend implements SubscriberInterface
{
    /**
     * @var DIContainer
     */
    private $container;

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
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onPostDispatchFrontendDetail',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Compare' => 'onLoadCompare',
        ];
    }

    /**
     * Assigns the custom product template to the product detail page
     */
    public function onPostDispatchFrontendDetail(Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Detail $controller */
        $controller = $args->get('subject');
        $view = $controller->View();
        $assignedProduct = $view->getAssign('sArticle');

        if ($controller->Request()->getParam('isEmotionAdvancedQuickView', false)) {
            $view->assign('customProductsIsEmotionAdvancedQuickView', true);
        }

        /** @var TemplateServiceInterface $templateService */
        $templateService = $this->container->get('custom_products.template_service');

        /** @var ProductPriceGetterInterface $productPriceGetter */
        $productPriceGetter = $this->container->get('custom_products.product_price_getter');
        $price = $productPriceGetter->getProductPriceByNumber($assignedProduct['ordernumber']);

        $customProductTemplate = $templateService->getTemplateByProductId(
            $assignedProduct['articleID'],
            true,
            $price
        );

        if (!$customProductTemplate) {
            return;
        }

        if (!$customProductTemplate['active']) {
            return;
        }

        $customProductTemplate['options'] = $this->formatMaxFileSizes($customProductTemplate['options']);

        /** @var TranslationServiceInterface $translationService */
        $translationService = $this->container->get('custom_products.translation_service');
        $customProductTemplate = $translationService->translateTemplate($customProductTemplate);
        $customProductNeedsConfig = $controller->Request()->getParam('customProductNeedsConfig');

        $view->assign('swagCustomProductsTemplate', $customProductTemplate);
        $view->assign('customProductNeedsConfig', $customProductNeedsConfig);
        $view->assign('variantsOnTop', $customProductTemplate['variants_on_top']);
    }

    /**
     * Checks if the compared products are having a custom products preset
     * assigned and if it's available it will display a new row in compare overlay.
     */
    public function onLoadCompare(Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Compare $controller */
        $controller = $args->get('subject');
        $request = $controller->Request();
        $view = $controller->View();

        if ($request->getActionName() !== 'overlay') {
            return;
        }

        $comparisonsList = $view->getAssign('sComparisonsList');

        /** @var TemplateServiceInterface $templateService */
        $templateService = $this->container->get('custom_products.template_service');

        /** @var ProductPriceGetterInterface $productPriceGetter */
        $productPriceGetter = $this->container->get('custom_products.product_price_getter');

        foreach ($comparisonsList['articles'] as &$comparison) {
            $price = $productPriceGetter->getProductPriceByNumber($comparison['ordernumber']);
            $template = $templateService->getTemplateByProductId(
                $comparison['articleID'],
                true,
                $price
            );

            if ($template !== null && $template['active']) {
                $comparison['swagCustomProducts'] = true;
            }
        }
        unset($comparison);

        $view->assign('sComparisonsList', $comparisonsList);
    }

    /**
     * @return array
     */
    private function formatMaxFileSizes(array $options)
    {
        foreach ($options as &$option) {
            if (!$this->checkUploadOptionTypes($option['type'])) {
                continue;
            }

            $option['max_file_size_formatted'] = $this->formatFileSize($option['max_file_size']);
        }

        return $options;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function checkUploadOptionTypes($type)
    {
        switch ($type) {
            case FileUploadType::TYPE:
            case ImageUploadType::TYPE:
                return true;
            default:
                return false;
        }
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    private function formatFileSize($bytes)
    {
        /** @var FileSizeFormatterInterface $fileSizeFormatter */
        $fileSizeFormatter = $this->container->get('custom_products.file_upload.file_size_formatter');

        return $fileSizeFormatter->formatBytes($bytes);
    }
}
