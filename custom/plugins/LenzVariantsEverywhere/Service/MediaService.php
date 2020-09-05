<?php
namespace LenzVariantsEverywhere\Service;

use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\VariantCoverServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class MediaService implements MediaServiceInterface {
    /** @var  \Shopware\Bundle\StoreFrontBundle\Service\Core\MediaService */
    private $coreService;
    /** @var  VariantCoverServiceInterface */
    private $variantCoverService;

    public function __construct($coreService, $variantCoverService)
    {
        $this->coreService = $coreService;
        $this->variantCoverService = $variantCoverService;
    }

    public function get($id, Struct\ShopContextInterface $context)
    {
        return $this->coreService->get($id, $context);
    }

    public function getList($ids, Struct\ShopContextInterface $context)
    {
        return $this->coreService->getList($ids, $context);
    }

    public function getProductsMedia($products, Struct\ShopContextInterface $context)
    {
        return $this->coreService->getProductsMedia($products, $context);
    }

    public function getCover(Struct\BaseProduct $product, Struct\ShopContextInterface $context)
    {
        $covers = $this->getCovers([$product], $context);

        return array_shift($covers);
    }

    public function getCovers($products, Struct\ShopContextInterface $context)
    {
        if (
            Shopware()->Front() !== null
            && Shopware()->Front()->Request() !== null
            && (
                Shopware()->Front()->Request()->getModuleName() == 'frontend'
                || Shopware()->Front()->Request()->getModuleName() == 'widgets'
            )
            && (
                Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'show')
                && Shopware()->Config()->get('forceArticleMainImageInListing')
            )
        ) {

            $mainProducts = [];
            $variantProducts = [];

            foreach ($products as $key => $product) {
                if($product->getAttributes()['core']->get('lenz_variants_everywhere_show') == 1) {
                    $variantProducts[$key] = $product;
                } else {
                    $mainProducts[$key] = $product;
                }
            }

            $mainProducts = $this->coreService->getCovers($products, $context);
            $variantProducts = $this->variantCoverService->getList($products, $context);

            return $variantProducts+$mainProducts;
        }

        return $this->coreService->getCovers($products, $context);
    }

    public function getProductMedia(Struct\BaseProduct $product, Struct\ShopContextInterface $context)
    {
        return $this->coreService->getProductMedia($product, $context);
    }

}
