<?php
namespace LenzVariantsEverywhere\SearchBundleDBAL\SortingHandler;

use LenzVariantsEverywhere\SearchBundleDBAL\VariantHelper;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\Sorting\PriceSorting;
use Shopware\Bundle\SearchBundle\SortingInterface;
use Shopware\Bundle\SearchBundleDBAL\CriteriaAwareInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundleDBAL\SortingHandler\PriceSortingHandler as PriceSortingHandlerParent;
use Shopware\Bundle\SearchBundleDBAL\SortingHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class PriceSortingHandler implements SortingHandlerInterface, CriteriaAwareInterface {

    private $parentHandler;
    private $variantHelper;
    private $criteria;

    public function __construct(PriceSortingHandlerParent $parentHandler, VariantHelper $variantHelper)
    {
        $this->parentHandler = $parentHandler;
        $this->variantHelper = $variantHelper;
    }

    /**
     * @inheritdoc
     */
    public function supportsSorting(SortingInterface $sorting)
    {
        return $this->parentHandler->supportsSorting($sorting);
    }

    /**
     * @inheritdoc
     */
    public function generateSorting(
        SortingInterface $sorting,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
        $this->variantHelper->joinPrice($query);
        /* @var PriceSorting $sorting */
        $query->addOrderBy('lenzVariantsEveryWhereVariantPrices.price', $sorting->getDirection());
        $this->parentHandler->generateSorting($sorting, $query, $context);
    }

//    public function generateSorting(
//        SortingInterface $sorting,
//        QueryBuilder $query,
//        ShopContextInterface $context
//    ) {
//        $this->variantHelper->joinPrice($query);
//        $this->variantHelper->joinAttributes($query);
//        /* @var PriceSorting $sorting */
//        $query->addOrderBy('lenzVariantsEveryWhereVariantPrices.price', $sorting->getDirection());
//
//        $query->addOrderBy('product.id', 'ASC');
//        $query->addOrderBy('lenzVariantsEverywhereAttributes.bogx_sort', 'ASC');
//
//        $this->parentHandler->generateSorting($sorting, $query, $context);
//    }

    public function setCriteria(Criteria $criteria)
    {
        $this->criteria = $criteria;
        $this->parentHandler->setCriteria($criteria);
    }
}