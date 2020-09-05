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

namespace TlsVariantExtends\Components;

use Doctrine\DBAL\Connection;
use Enlight_Controller_Front;
use Shopware\Bundle\StoreFrontBundle\Service\ConfiguratorServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;

class ConfiguratorService implements ConfiguratorServiceInterface
{
    /**
     * @var ConfiguratorServiceInterface
     */
    private $configuratorService;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var PluginConfig
     */
    private $pluginConfig;
    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * ConfiguratorService constructor.
     * @param ConfiguratorServiceInterface $configuratorService
     * @param Connection $connection
     * @param PluginConfig $pluginConfig
     * @param Enlight_Controller_Front $front
     */
    public function __construct(
        ConfiguratorServiceInterface $configuratorService,
        Connection $connection,
        PluginConfig $pluginConfig,
        Enlight_Controller_Front $front //TODO
    ) {
        $this->configuratorService = $configuratorService;
        $this->connection = $connection;
        $this->pluginConfig = $pluginConfig;
        $this->front = $front;
    }

    /**
     * @inheritDoc
     */
    public function getProductConfiguration(Struct\BaseProduct $product, Struct\ShopContextInterface $context)
    {
        return $this->configuratorService->getProductConfiguration($product, $context);
    }

    /**
     * @inheritDoc
     */
    public function getProductsConfigurations($products, Struct\ShopContextInterface $context)
    {
        return $this->configuratorService->getProductsConfigurations($products, $context);
    }

    /**
     * @inheritDoc
     */
    public function getProductConfigurator(
        Struct\BaseProduct $product,
        Struct\ShopContextInterface $context,
        array $selection
    ) {
        $selection = $this->defaultSelection($product, $selection);

        $configurator = $this->configuratorService->getProductConfigurator($product, $context, $selection);

        $onlyOneGroup = count($configurator->getGroups()) === 1;
        if ($selection || $onlyOneGroup) {
            $activateProductCombination = $this->getActiveProductCombinations($product);

            foreach ($configurator->getGroups() as $group) {
                foreach ($group->getOptions() as $option) {
                    if (!$this->isCombinationValid($group, $activateProductCombination[$option->getId()], $selection)) {
                        $option->addAttribute('tlsVariantExtends', new Attribute(
                            [
                                'invalidCombination' => true,
                            ]
                        ));
                    }
                }
            }
        }

        return $configurator;
    }

    /**
     * @param Struct\BaseProduct $product
     * @return array
     */
    private function getActiveProductCombinations(Struct\BaseProduct $product)
    {
        $query = $this->connection->createQueryBuilder();

        $query->select([
            'relations.option_id',
            "GROUP_CONCAT(DISTINCT assignedRelations.option_id, '' SEPARATOR '|') as combinations",
        ]);

        $query->from('s_article_configurator_option_relations', 'relations')
            ->innerJoin('relations', 's_articles_details', 'variant',
                'variant.id = relations.article_id AND variant.articleID = :articleId AND variant.active = 1')
            ->innerJoin(
                'variant',
                's_articles',
                'product',
                'product.id = variant.articleID'
            )
            ->leftJoin('relations', 's_article_configurator_option_relations', 'assignedRelations',
                'assignedRelations.article_id = relations.article_id AND assignedRelations.option_id != relations.option_id')
            ->groupBy('relations.option_id')
            ->setParameter(':articleId', $product->getId());

        /** @var \Doctrine\DBAL\Driver\ResultStatement $statement */
        $statement = $query->execute();

        $data = $statement->fetchAll(\PDO::FETCH_KEY_PAIR);

        foreach ($data as &$row) {
            $row = explode('|', $row);
        }

        return $data;
    }

    /**
     * @param Struct\Configurator\Group $group
     * @param array $combinations
     * @param array $selection
     * @return bool
     */
    private function isCombinationValid(Struct\Configurator\Group $group, $combinations, $selection)
    {
        if (empty($combinations)) {
            return false;
        }

        foreach ($selection as $selectedGroup => $selectedOption) {
            if (!in_array($selectedOption, $combinations) && $selectedGroup !== $group->getId()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $productId
     * @return bool|int
     */
    private function getConfiguratorType($productId)
    {
        $type = $this->connection->fetchColumn(
            'SELECT type
             FROM s_article_configurator_sets configuratorSet
              INNER JOIN s_articles product
                ON product.configurator_set_id = configuratorSet.id
             WHERE product.id = ?',
            [$productId]
        );

        if ($type === false) {
            return false;
        }

        return (int) $type;
    }

    /**
     * @param Struct\BaseProduct $product
     * @param array $selection
     * @return array
     */
    private function defaultSelection(Struct\BaseProduct $product, array $selection)
    {
        if (empty($selection) &&
            $this->pluginConfig->get('defaultSelection') &&
            !$this->front->Request()->isXmlHttpRequest() &&
            $product instanceof Struct\Product &&
            $this->getConfiguratorType($product->getId()) ===
            \Shopware\Bundle\StoreFrontBundle\Service\Core\ConfiguratorService::CONFIGURATOR_TYPE_PICTURE
        ) {
            $selection = $product->getSelectedOptions();
        }
        return $selection;
    }
}
