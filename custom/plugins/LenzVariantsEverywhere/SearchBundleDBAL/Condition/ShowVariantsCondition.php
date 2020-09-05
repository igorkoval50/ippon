<?php

namespace LenzVariantsEverywhere\SearchBundleDBAL\Condition;

use Shopware\Bundle\SearchBundle\ConditionInterface;

class ShowVariantsCondition implements ConditionInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'lenz_search_bundle_show_variants';
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
