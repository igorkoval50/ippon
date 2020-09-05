<?php

namespace KssPropertiesInListing\Service;

use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\PropertyServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface;


class ListProductService implements ListProductServiceInterface
{
    /**
     * @var ListProductServiceInterface
     */
    private $service;
    /**
     * @var PropertyServiceInterface
     */
    private $propertyService;

    /**
     * ListProductService constructor.
     * @param ListProductServiceInterface $service
     * @param PropertyServiceInterface $propertyService
     */
    public function __construct(
        ListProductServiceInterface $service,
        PropertyServiceInterface $propertyService
    )
    {
        $this->service = $service;
        $this->propertyService = $propertyService;
    }

    /**
     * @param array $numbers
     * @param ProductContextInterface $context
     * @return Struct\ListProduct[]
     */
    public function getList(array $numbers, Struct\ProductContextInterface $context)
    {
        $products = $this->service->getList($numbers, $context);
        $properties = $this->propertyService->getList($products, $context);
        foreach ($products as $product) {
            $attribute = new Attribute();
            $product->addAttribute('properties_attribute', $attribute);

            if (isset($properties[$product->getNumber()])) {
                $attribute->set('productProperties', $properties[$product->getNumber()]);
            }
        }


        return $products;
    }

    /**
     * @param string $number
     * @param ProductContextInterface $context
     * @return Struct\ListProduct|null
     */
    public function get($number, ProductContextInterface $context)
    {
        $products = $this->getList([$number], $context);
        return array_shift($products);
    }
}
