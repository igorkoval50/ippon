<?php
/**
 * TabtenListingHoverImage SearchResultHoverImageDecorator.php.
 *
 * @author Fabian Golle <fabian@golle-it.de>
 */

namespace TabtenListingHover\Decorator;


use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class SearchResultHoverImageDecorator implements ListProductServiceInterface
{
    private $service;
    private $mediaService;

    public function __construct(ListProductServiceInterface $service, MediaServiceInterface $mediaService)
    {
        $this->service      = $service;
        $this->mediaService = $mediaService;
    }

    public function getList(array $numbers, Struct\ProductContextInterface $context)
    {
        $products = $this->service->getList($numbers, $context);
        $media    = $this->mediaService->getProductsMedia($products, $context);

        foreach ($numbers as $number) {
            if (array_key_exists($number, $products)) {
                $product   = $products[$number];
                $attribute = new Struct\Attribute();

                if (isset($media[$number])) {
                    $articleMedia = array_map(array($this, 'convertMedia'), $media[$number]);
                    $attribute    = new Struct\Attribute($articleMedia);
                }

                $product->addAttribute('hover_images', $attribute);

                $products[$number] = $product;
            }

        }

        return $products;
    }

    private function convertMedia(Struct\Media $media)
    {
        $data = array(
            'src'        => $media->getFile(),
            'thumbnails' => array(),
        );

        foreach ($media->getThumbnails() as $thumbnail) {
            $data['thumbnails'][] = array(
                'src'       => $thumbnail->getSource(),
                'srcRetina' => $thumbnail->getRetinaSource(),
            );
        }

        return $data;
    }

    public function get($number, Struct\ProductContextInterface $context)
    {
        $products = $this->getList([$number], $context);

        return array_shift($products);
    }
}