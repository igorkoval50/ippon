<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace KibVariantListing\Bundle\AttributeBundle\Repository\Reader;

use Shopware\Bundle\AttributeBundle\Repository\Reader\GenericReader;
use Shopware\Components\Model\ModelManager;

class PropertyOptionReader extends GenericReader
{
    /**
     * @var GenericReader
     */
    private $coreReader;

    public function __construct(
        GenericReader $coreReader,
        $entity,
        ModelManager $entityManager
    )
    {
        $this->coreReader = $coreReader;
        parent::__construct($entity, $entityManager);
    }

    protected function createListQuery()
    {
        $query = $this->coreReader->createListQuery();
        $query->innerJoin('entity.articles', 'articles');

        return $query;
    }
}
