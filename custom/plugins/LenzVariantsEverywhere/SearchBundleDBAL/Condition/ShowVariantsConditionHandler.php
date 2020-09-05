<?php

namespace LenzVariantsEverywhere\SearchBundleDBAL\Condition;

use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class ShowVariantsConditionHandler implements ConditionHandlerInterface
{
    const STATE_INCLUDED = 'lenz_variants_everywhere_include';

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        return ($condition instanceof ShowVariantsCondition);
    }

    /**
     * @inheritdoc
     */
    public function generateCondition(
        ConditionInterface $condition,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
        if (!$query->hasState(self::STATE_INCLUDED)) {

	        $this->rewriteOriginalJoin($query);

            $sGroupByGroup = trim($this->config->getByNamespace('LenzVariantsEverywhere', 'groupByGroup', ''));

	        if($this->config->getByNamespace('LenzVariantsEverywhere', 'showOnlySpecifiedVariants') == true) {
	            // Use shop owner custom selection.
                $query->andWhere("variant.id = product.main_detail_id OR productAttribute.lenz_variants_everywhere_show = 1");
                // Group by variant to prevent from grouping after product only.
                $query->addGroupBy('variant.id');
            } elseif(!empty($sGroupByGroup)) {
	            // Use shop owner grouping.
                $query->leftJoin('variant', 's_article_configurator_option_relations', 'lenzVariantsEverywhereSacor', 'lenzVariantsEverywhereSacor.article_id = variant.id');
                $query->leftJoin('lenzVariantsEverywhereSacor', 's_article_configurator_options', 'lenzVariantsEverywhereSaco', 'lenzVariantsEverywhereSaco.id = lenzVariantsEverywhereSacor.option_id');
                $query->leftJoin('lenzVariantsEverywhereSaco', 's_article_configurator_groups', 'lenzVariantsEverywhereSacg', 'lenzVariantsEverywhereSacg.id = lenzVariantsEverywhereSaco.group_id');

                $aGroupByGroup = explode(',', $sGroupByGroup);

                foreach ($aGroupByGroup as $group) {
                    $query->addGroupBy('IF(lenzVariantsEverywhereSacg.name = "' . trim($group) . '", CONCAT(lenzVariantsEverywhereSacg.name, "_", lenzVariantsEverywhereSaco.name), "")');
                }
            } else {
                // Group by variant to prevent from grouping after product only.
                $query->addGroupBy('variant.id');
            }

            if($this->config->getByNamespace('LenzVariantsEverywhere', 'hideOutOfStockVariants') == true) {
                $query->andWhere("variant.instock > 0");
            }

            $query->andWhere('variant.active = 1');

            $query->addState(self::STATE_INCLUDED);

        }
    }

    private function rewriteOriginalJoin(&$query) {
        $joinQueryPart = $query->getQueryPart("join");
        $query->resetQueryPart("join");

        foreach ($joinQueryPart as $joinAlias => $joins) {
            foreach ($joins as $join) {
                if($join["joinType"] == "inner") {

                    if($joinAlias == "product" && strpos($join["joinCondition"], "variant.id = product.main_detail_id") !== false) {
                        $join["joinCondition"] = str_replace(
                            "variant.id = product.main_detail_id",
                            "variant.articleID = product.id",
                            $join["joinCondition"]
                        );
                    }

                    $query->innerJoin(
                        $joinAlias,
                        $join["joinTable"],
                        $join["joinAlias"],
                        $join["joinCondition"]
                    );
                } elseif($join["joinType"] == "left") {
                    $query->leftJoin(
                        $joinAlias,
                        $join["joinTable"],
                        $join["joinAlias"],
                        $join["joinCondition"]
                    );
                }
            }
        }
    }
}
