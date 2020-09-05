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

namespace SwagNewsletter\Models;

use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Newsletter\Newsletter as CoreNewsletter;

/**
 * Repository for the \SwagNewsletter\Models\Newsletter model.
 * The repository is responsible for all CRUD function around component library.
 * The component library is used for shopware backend modules like the emotion
 * module.
 */
class Repository extends ModelRepository
{
    /**
     * Returns an instance of the \Doctrine\ORM\Query object which selects Elements by a given newsletterId
     *
     * @param $newsletterId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getElementsByNewsletterIdQuery($newsletterId)
    {
        $builder = $this->getElementsByNewsletterIdQueryBuilder($newsletterId);

        return $builder->getQuery();
    }

    /**
     * Helper function to create the query builder for the "getElementsByNewsletterIdQuery" function.
     * This function can be hooked to modify the query builder of the query object.
     *
     * @param int $newsletterId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getElementsByNewsletterIdQueryBuilder($newsletterId)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->select(
            [
                'elements',
                'component',
                'field',
                'fields',
                'elementData',
            ]
        )
        ->from(Element::class, 'elements')
        ->leftJoin('elements.component', 'component')
        ->leftJoin('component.fields', 'fields')
        ->leftJoin('elements.data', 'elementData')
        ->innerJoin('elementData.field', 'field')
        ->where('elements.newsletterId = :newsletterId')
        ->setParameter('newsletterId', $newsletterId);

        return $builder;
    }

    /**
     * Get all newsletters with status -1
     */
    public function getPreviewNewslettersQuery()
    {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $builder->select(
            [
                'mailing',
                'container',
                'text',
            ]
        )
        ->from(CoreNewsletter::class, 'mailing')
        ->leftJoin('mailing.containers', 'container')
        ->leftJoin('container.text', 'text')
        ->where('mailing.status = -1');

        return $builder->getQuery();
    }

    /**
     * Little helper function to get a component by its cls.
     *
     * @param $cls
     *
     * @return \Doctrine\ORM\Query
     */
    public function getComponentsByClassQuery($cls)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->select(['components', 'fields'])
                ->from(Component::class, 'components')
                ->leftJoin('components.fields', 'fields')
                ->where('components.cls = ?1')
                ->setParameter(1, $cls);

        return $builder->getQuery();
    }

    /**
     * Helper function to get a component list
     *
     * @return \Doctrine\ORM\Query
     */
    public function getComponentsQuery()
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->select(['components', 'fields'])
                ->from(Component::class, 'components')
                ->leftJoin('components.fields', 'fields');

        return $builder->getQuery();
    }

    /**
     * Returns the query for fetching the detail data for a newsletter.
     *
     * @param string $newsletterId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getNewsletterDetailQuery($newsletterId)
    {
        $builder = $this->getNewsletterDetailQueryBuilder($newsletterId);

        return $builder->getQuery();
    }

    /**
     * Returns the builder for fetching the detail data for a newsletter.
     *
     * @param string $newsletterId
     *
     * @return \Doctrine\ORM\QueryBuilder|\Shopware\Components\Model\QueryBuilder
     */
    public function getNewsletterDetailQueryBuilder($newsletterId)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select(
            [
            'mailing',
            'container',
            'text',
            'articles',
            'links',
            'banner',
            ]
        )
        ->from(CoreNewsletter::class, 'mailing')
        ->leftJoin('mailing.containers', 'container')
        ->leftJoin('container.text', 'text')
        ->leftJoin('container.articles', 'articles')
        ->leftJoin('container.links', 'links')
        ->leftJoin('container.banner', 'banner')
        ->where('mailing.status > -1')
        ->andWhere('mailing.id = :mailingId')
        ->setParameter(':mailingId', $newsletterId);

        return $queryBuilder;
    }
}
