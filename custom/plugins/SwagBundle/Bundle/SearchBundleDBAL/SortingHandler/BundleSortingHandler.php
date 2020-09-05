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

namespace SwagBundle\Bundle\SearchBundleDBAL\SortingHandler;

use Shopware\Bundle\SearchBundle\SortingInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundleDBAL\SortingHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use SwagBundle\Bundle\SearchBundle\Sorting\BundleSorting;
use SwagBundle\Bundle\SearchBundleDBAL\BundleJoinHelper;

class BundleSortingHandler implements SortingHandlerInterface
{
    /**
     * @var BundleJoinHelper
     */
    private $bundleJoinHelper;

    public function __construct(BundleJoinHelper $bundleJoinHelper)
    {
        $this->bundleJoinHelper = $bundleJoinHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsSorting(SortingInterface $sorting)
    {
        return $sorting instanceof BundleSorting;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSorting(
        SortingInterface $sorting,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
        /* @var BundleSorting $sorting */
        $this->bundleJoinHelper->joinTable($query, $context);

        $query->addOrderBy('swag_bundles.articleID', $sorting->getDirection());
    }
}
