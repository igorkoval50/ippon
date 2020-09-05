<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace KibVariantListing\Bundle\AttributeBundle\Repository\Searcher;

use Shopware\Bundle\AttributeBundle\Repository\SearchCriteria;
use Shopware\Bundle\AttributeBundle\Repository\Searcher\GenericSearcher;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Model\SearchBuilder;

class PropertyOptionSearcher extends GenericSearcher
{
    /**
     * @var GenericSearcher
     */
    private $coreSearcher;

    public function __construct(
        GenericSearcher $coreSearcher,
        $entity,
        ModelManager $entityManager,
        SearchBuilder $searchBuilder = null
    )
    {
        $this->coreSearcher = $coreSearcher;
        parent::__construct($entity, $entityManager, $searchBuilder);
    }

    protected function createQuery(SearchCriteria $criteria)
    {
        $query = $this->coreSearcher->createQuery($criteria);
        $query->innerJoin('entity.articles', 'articles');

        return $query;
    }
}
