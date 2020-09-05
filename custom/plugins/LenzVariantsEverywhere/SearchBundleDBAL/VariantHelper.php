<?php
namespace LenzVariantsEverywhere\SearchBundleDBAL;

use GuzzleHttp\Query;
use Shopware\Bundle\SearchBundle\Condition\HasPseudoPriceCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class VariantHelper {

    const LISTING_PRICE_JOINED = 'lenz_variants_everywhere_listing_price_joined';
    const ATTRIBUTES_JOINED = 'lenz_variants_everywhere_attributes_joined';

    public function joinPrice(QueryBuilder $query)
    {
        if ($query->hasState(self::LISTING_PRICE_JOINED)) {
            return;
        }

        $query->addState(self::LISTING_PRICE_JOINED);

        $this->joinPrices($query);
    }

    private function joinPrices(QueryBuilder $query) {
        $query->innerJoin('variant', 's_articles_prices', 'lenzVariantsEveryWhereVariantPrices', 'lenzVariantsEveryWhereVariantPrices.articledetailsID = variant.id');
    }

    public function joinAttributes(QueryBuilder $query) {
        if($query->hasState(self::ATTRIBUTES_JOINED)) {
            return;
        }

        $query->addState(self::ATTRIBUTES_JOINED);
        $query->innerJoin('variant', 's_articles_attributes', 'lenzVariantsEverywhereAttributes', 'lenzVariantsEverywhereAttributes.articledetailsID = variant.id');
    }

    public function supportHasPseudopriceCondition(QueryBuilder $query) {
        // See if one price of article has pseudoprice.
        $query->andHaving('MAX(lenzVariantsEveryWhereVariantPrices.pseudoprice > 0)');
    }
}