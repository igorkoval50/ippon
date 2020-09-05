<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace KibVariantListing\Bundle\StoreFrontBundle\Service;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\ConfiguratorServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ConfiguratorService;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ProductNumberServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;

class ListProductService implements ListProductServiceInterface
{
    /**
     * @var ProductNumberServiceInterface
     */
    private $productNumberService;

    /**
     * @var ListProductServiceInterface
     */
    private $coreService;

    /**
     * @var ConfiguratorService
     */
    private $configuratorService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \Enlight_Controller_Front
     */
    private $front;
    /**
     * @var MediaServiceInterface
     */
    private $mediaService;

    /**
     * @param ProductNumberServiceInterface $productNumberService
     * @param ListProductServiceInterface $coreService
     * @param ConfiguratorServiceInterface $configuratorService
     * @param MediaServiceInterface $mediaService
     * @param Connection $connection
     * @param \Enlight_Controller_Front $front
     */
    public function __construct(
        ProductNumberServiceInterface $productNumberService,
        ListProductServiceInterface $coreService,
        ConfiguratorServiceInterface $configuratorService,
        MediaServiceInterface $mediaService,
        Connection $connection,
        \Enlight_Controller_Front $front
    )
    {
        $this->productNumberService = $productNumberService;
        $this->coreService = $coreService;
        $this->configuratorService = $configuratorService;
        $this->connection = $connection;
        $this->front = $front;
        $this->mediaService = $mediaService;
    }

    public function getList(array $numbers, Struct\ProductContextInterface $context)
    {
        $products = $this->coreService->getList($numbers, $context);

        return array_map(
        /**
         * @param $product ListProduct
         * @return ListProduct
         */
            function ($product) use ($context) {
                /** @var ListProduct $product */
                if ($product->hasConfigurator()) {
                    $configurator = $this->configuratorService->getProductConfigurator($product, $context, array());

                    /** @var Struct\Configurator\Group $group */
                    foreach ($configurator->getGroups() as $group) {
                        /** @var Struct\Configurator\Option $option */
                        foreach ($group->getOptions() as $option) {
                            if ($option->getActive()) {
                                $selection = array();
                                $mappingRules = array();

                                if ($configurator->getType() == 2 && $option->getMedia() != null && $this->front->Request() != null) {
                                    $mappingRules = $this->getImageMappingRules($product->getId(), $option->getMedia()->getId());
                                    $sFilterProperties = $this->front->Request()->getParam('sFilterProperties', null);

                                    //only if filter is active
                                    if ($sFilterProperties !== null) {
                                        $articleImgAttribute = $this->getArticleImageAttributes($product->getId(), $option->getMedia()->getId());

                                        if (!empty($articleImgAttribute[0])) {
                                            $filters = explode('|', $sFilterProperties);

                                            foreach ($filters as $filter) {
                                                if (strpos($articleImgAttribute[0], '|' . $filter . '|') !== false) {
                                                    $product->setCover($option->getMedia());
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }

                                if (count($mappingRules) > 1 &&
                                    $group->getId() == $mappingRules[0]['group_id'] &&
                                    $option->getId() == $mappingRules[0]['option_id']
                                ) {
                                    foreach ($mappingRules as $rule) {
                                        if ($mappingRules[0]['id'] == $rule['id']) {
                                            $selection[$rule['group_id']] = $rule['option_id'];
                                        } else {
                                            break;
                                        }
                                    }
                                } else {
                                    $selection = [$group->getId() => $option->getId()];
                                }

                                $optionOrderNumber = $this->productNumberService->getAvailableNumber(
                                    $product->getNumber(),
                                    $context,
                                    $selection
                                );

                                $detailAttributes = $this->getArticleDetailAttributesByOrdernumber($optionOrderNumber);

                                if (!empty($detailAttributes[0])) {
                                    $media = $this->mediaService->get($detailAttributes[0], $context);

                                    $option->setMedia($media);
                                }

                                $option->addAttribute('kib_configurator_ordernumbers', new Attribute(
                                    [
                                        'option_ordernumber' => $optionOrderNumber
                                    ]
                                ));
                            }
                        }
                    }

                    $product->addAttribute('kib_variant_listing', new Attribute(
                        ['kib_configurator' => $configurator]
                    ));
                }

                return $product;
            },
            $products
        );
    }

    public function get($number, Struct\ProductContextInterface $context)
    {
        $products = $this->getList([$number], $context);
        return array_shift($products);
    }

    /**
     * @param $articleId
     * @param $mediaId
     * @return array
     */
    private function getImageMappingRules($articleId, $mediaId)
    {
        $builder = $this->connection->createQueryBuilder();
        $builder->select(['mappings.id', 'rules.option_id', 'options.group_id'])
            ->from('s_article_img_mappings', 'mappings')
            ->innerJoin('mappings', 's_articles_img', 'image', 'image.id = mappings.image_id')
            ->innerJoin('mappings', 's_article_img_mapping_rules', 'rules', 'rules.mapping_id = mappings.id')
            ->innerJoin('rules', 's_article_configurator_options', 'options', 'rules.option_id = options.id')
            ->where('image.articleId = :articleId')
            ->andWhere('image.media_id = :mediaId')
            ->setParameters(['articleId' => $articleId, 'mediaId' => $mediaId]);


        /** @var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $builder->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @deprecated Workaround for missing media attributes.
     * $media->getAttribute('kib_variantlisting_prop_img_mapping') should be used in future, if provided by Shopware-Core
     * @param $mediaId
     * @param $articleId
     * @return array
     */
    private function getArticleImageAttributes($articleId, $mediaId)
    {
        $articleImgAttributeQuery = $this->connection->createQueryBuilder();
        $articleImgAttributeQuery->select('imageAttributes.kib_variantlisting_prop_img_mapping')
            ->from('s_articles_img_attributes', 'imageAttributes')
            ->innerJoin('imageAttributes', 's_articles_img', 'image', 'imageAttributes.imageID = image.id')
            ->where('image.media_id = :mediaID')
            ->andWhere('image.articleID = :articleId')
            ->setParameters(['mediaID' => $mediaId, 'articleId' => $articleId])
            ->setMaxResults(1);

        /** @var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $articleImgAttributeQuery->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param $ordernumber
     * @return array
     */
    private function getArticleDetailAttributesByOrdernumber($ordernumber)
    {
        $articleImgAttributeQuery = $this->connection->createQueryBuilder();
        $articleImgAttributeQuery->select('detailAttributes.kib_variantlisting_img')
            ->from('s_articles_details', 'details')
            ->innerJoin('details', 's_articles_attributes', 'detailAttributes', 'detailAttributes.articledetailsID = details.id')
            ->where('details.ordernumber = :number')
            ->setParameter('number', $ordernumber)
            ->setMaxResults(1);

        /** @var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $articleImgAttributeQuery->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }
}
