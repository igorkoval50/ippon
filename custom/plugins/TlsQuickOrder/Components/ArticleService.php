<?php
/**
 * Copyright (c) TreoLabs GmbH
 *
 * This Software is the property of TreoLabs GmbH and is protected
 * by copyright law - it is NOT Freeware and can be used only in one project
 * under a proprietary license, which is delivered along with this program.
 * If not, see <https://treolabs.com/eula>.
 *
 * This Software is distributed as is, with LIMITED WARRANTY AND LIABILITY.
 * Any unauthorised use of this Software without a valid license is
 * a violation of the License Agreement.
 *
 * According to the terms of the license you shall not resell, sublicense,
 * rent, lease, distribute or otherwise transfer rights or usage of this
 * Software or its derivatives. You may modify the code of this Software
 * for your own needs, if source code is provided.
 */

namespace TlsQuickOrder\Components;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Shopware\Bundle\StoreFrontBundle\Service\ConfiguratorServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Configurator\Group;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface;
use Shopware\Components\Compatibility\LegacyStructConverter;

class ArticleService
{
    /**
     * @var PluginConfig
     */
    private $config;
    /**
     * @var ListProductServiceInterface
     */
    private $listProductService;
    /**
     * @var ContextServiceInterface
     */
    private $contextService;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var LegacyStructConverter
     */
    private $legacyStructConverter;
    /**
     * @var ConfiguratorServiceInterface
     */
    private $configuratorService;

    /**
     * ArticleService constructor.
     * @param PluginConfig $config
     * @param Connection $connection
     * @param ListProductServiceInterface $listProductService
     * @param ContextServiceInterface $contextService
     * @param LegacyStructConverter $legacyStructConverter
     * @param ConfiguratorServiceInterface $configuratorService
     */
    public function __construct(
        PluginConfig $config,
        Connection $connection,
        ListProductServiceInterface $listProductService,
        ContextServiceInterface $contextService,
        LegacyStructConverter $legacyStructConverter,
        ConfiguratorServiceInterface $configuratorService
    ) {
        $this->config = $config;
        $this->listProductService = $listProductService;
        $this->contextService = $contextService;
        $this->connection = $connection;
        $this->legacyStructConverter = $legacyStructConverter;
        $this->configuratorService = $configuratorService;
    }

    public function getVariants($productId)
    {
        $builder = $this->connection->createQueryBuilder();
        $builder
            ->select('variants.ordernumber')
            ->from('s_articles_details', 'variants')
            ->where('variants.articleID = :productId')
            ->andWhere('variants.active = 1')
            ->setParameter('productId', $productId);
        $numbers = $builder->execute()->fetchAll(PDO::FETCH_COLUMN);

        $productContext = $this->contextService->getShopContext();
        /** @var ProductContextInterface $productContext */
        $productStructs = $this->listProductService->getList($numbers, $productContext);
        $configurations = $this->configuratorService->getProductsConfigurations($productStructs, $productContext);

        $variants = [];
        foreach ($productStructs as $productStruct) {
            $product = $this->legacyStructConverter->convertListProductStruct($productStruct);
            $options = [];
            if (isset($configurations[$productStruct->getNumber()])) {
                /** @var Group $group */
                foreach ($configurations[$productStruct->getNumber()] as $group) {
                    foreach ($group->getOptions() as $option) {
                        $options[] = $option->getId();
                    }
                }
            }

            sort($options);
            $options = implode('_', $options);

            $variants[$options] = $product;
        }

        return $variants;
    }

    /**
     * @param array $product
     * @param array $variants
     * @return array
     */
    public function formatResult($product, $variants)
    {
        $result = [];
        $first = true;
        foreach ($product['sConfigurator'] as $group) {
            foreach ($group['values'] as $option) {
                if ($first) {
                    $result[] = [
                        'name' => $option['optionname'],
                        'groupName' => $group['groupname'],
                        'optionId' => $option['optionID'],
                        'thumbnails' => $option['media']['thumbnails']['0']['source'],
                    ];
                } else {
                    foreach ($result as &$item) {
                        $item['options'][] = [
                            'name' => $option['optionname'],
                            'groupName' => $group['groupname'],
                            'ids' => [$item['optionId'], $option['optionID']],
                        ];
                    }
                }
            }
            $first = false;
        }

        foreach ($result as &$row) {
            if (isset($row['options'])) {
                foreach ($row['options'] as &$option) {
                    sort($option['ids']);
                    $optionIds = implode('_', $option['ids']);
                    if (isset($variants[$optionIds])) {
                        $option['product'] = $variants[$optionIds];
                    }
                }
            } else {
                if (isset($variants[$row['optionId']])) {
                    $row['product'] = $variants[$row['optionId']];
                }
            }
        }
        return $result;
    }
}
