<?php
namespace KssSliderArticlesBuy\Subscriber\Service;

use Shopware\Bundle\StoreFrontBundle\Service\ConfiguratorServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ConfiguratorService;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface;
use Shopware\Components\Compatibility\LegacyStructConverter;

class ListProductService implements ListProductServiceInterface
{

    /**
     * @var ListProductServiceInterface
     */
    private $coreService;

    /**
     * @var ConfiguratorService
     */
    private $configuratorService;
    /**
     * @var LegacyStructConverter
     */
    private $legacyStructConverter;
    /**
     * @var \Enlight_Controller_Front
     */
    private $front;

    /**
     * @param ListProductServiceInterface $coreService
     * @param ConfiguratorServiceInterface $configuratorService
     * @param LegacyStructConverter $legacyStructConverter
     * \Enlight_Controller_Front $front
     */
    public function __construct(
        ListProductServiceInterface $coreService,
        ConfiguratorServiceInterface $configuratorService,
        LegacyStructConverter $legacyStructConverter,
        \Enlight_Controller_Front $front
    )
    {
        $this->coreService = $coreService;
        $this->configuratorService = $configuratorService;
        $this->legacyStructConverter = $legacyStructConverter;
        $this->front = $front;
    }

    public function getList(array $numbers, ProductContextInterface $context)
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
                    $selection = [];
                    $configurator = $this->configuratorService->getProductConfigurator($product, $context, $selection);

                    $products = $this->coreService->get($product->getNumber(), $context);

                    $activOption = $this->getActiveOptionId($products->getVariantId());
                    foreach ($configurator->getGroups() as $group) {
                        foreach ($group->getOptions() as $option) {
                            $option->setSelected(
                                in_array($option->getId(), $activOption)
                            );
                        }
                    }

                    $configurator = $this->legacyStructConverter->convertConfiguratorStruct($product, $configurator);
                    if($product->getStock() == 0 && $product->isCloseouts()){
                        $configurator['disable_buy'] = true;
                    }
                    $product->addAttribute('kss_variant_listing', new Attribute(
                        ['kss_configurator' => $configurator]
                    ));
                }
                return $product;
            },
            $products
        );
    }

    public function get($number, ProductContextInterface $context)
    {
        $products = $this->getList([$number], $context);

        return array_shift($products);
    }

    /**
     * @param int $productId
     * @return array
     */
    private function getActiveOptionId($variantId)
    {
        $builder = Shopware()->Models()->getConnection()->createQueryBuilder();

        $results = $builder->select('option_id')
            ->from('s_article_configurator_option_relations', 'relations')
            ->where('relations.article_id = :articleId')
            ->setParameter('articleId', $variantId)
            ->execute()
            ->fetchAll(\PDO::FETCH_NUM);

        if ($results === false) {
            return false;
        }
        $result = [];
        foreach ($results as $value){
            $result[] = $value[0];
        }

        return $result;
    }
}
