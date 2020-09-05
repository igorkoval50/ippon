<?php
namespace LenzVariantsEverywhere\SearchBundleDBAL\ConditionHandler;

use LenzVariantsEverywhere\SearchBundleDBAL\VariantHelper;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandler\HasPseudoPriceConditionHandler as HasPseudoPriceConditionHandlerParent;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\CriteriaAwareInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class HasPseudoPriceConditionHandler implements ConditionHandlerInterface, CriteriaAwareInterface {

    private $parentHandler;
    private $variantHelper;

    public function __construct(HasPseudoPriceConditionHandlerParent $parentHandler, VariantHelper $variantHelper)
    {
        $this->parentHandler = $parentHandler;
        $this->variantHelper = $variantHelper;
    }

    /**
     * @inheritdoc
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        return $this->parentHandler->supportsCondition($condition);
    }

    /**
     * @inheritdoc
     */
    public function generateCondition(
        ConditionInterface $condition,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
        $this->parentHandler->generateCondition($condition, $query, $context);

        if (!Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'show')) {
            // abort if shop is not activated
            return;
        }

        $this->variantHelper->joinAttributes($query);
        $this->variantHelper->joinPrice($query);
        $this->variantHelper->supportHasPseudopriceCondition($query);
    }

    /**
     * @inheritdoc
     */
    public function setCriteria(Criteria $criteria)
    {
        $this->parentHandler->setCriteria($criteria);
    }
}