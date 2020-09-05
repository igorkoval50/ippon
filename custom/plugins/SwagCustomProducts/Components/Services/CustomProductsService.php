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

namespace SwagCustomProducts\Components\Services;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Components\DependencyInjection\Container;
use SwagCustomProducts\Components\CustomProductsOptionRepository;
use SwagCustomProducts\Structs\OptionStruct;

class CustomProductsService implements CustomProductsServiceInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var TemplateServiceInterface
     */
    private $templateService;

    /**
     * @var CustomProductsOptionRepository
     */
    private $customProductsOptionRepository;

    /**
     * @var ProductPriceGetterInterface
     */
    private $priceGetter;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    public function __construct(
        Container $container,
        TemplateServiceInterface $templateService,
        Connection $connection,
        CustomProductsOptionRepository $customProductsOptionRepository,
        ProductPriceGetterInterface $priceGetter,
        ContextServiceInterface $contextService
    ) {
        $this->container = $container;
        $this->templateService = $templateService;
        $this->connection = $connection;
        $this->customProductsOptionRepository = $customProductsOptionRepository;
        $this->priceGetter = $priceGetter;
        $this->contextService = $contextService;
    }

    /**
     * {@inheritdoc}
     */
    public function isCustomProduct($identifier, $isBasketId = false)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        if ($isBasketId) {
            $result = $queryBuilder->select('swag_custom_products_configuration_hash')
                ->from('s_order_basket_attributes', 'attr')
                ->where('attr.basketID = :basketId')
                ->setParameter('basketId', $identifier)
                ->execute()
                ->fetchColumn();

            return !empty($result);
        }

        /** @var \Shopware_Components_Modules $modules */
        $modules = $this->container->get('modules');
        $identifier = $modules->Articles()->sGetArticleIdByOrderNumber($identifier);

        $result = $queryBuilder->select('article_id')
            ->from('s_plugin_custom_products_template_product_relation')
            ->where('article_id = :id')
            ->setParameter('id', $identifier)
            ->execute()
            ->fetchColumn();

        return !empty($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionById($optionId, array $configuration, $basketCalculation = false, array $basketPosition = [])
    {
        $list = $this->getOptionList([$optionId], $configuration, $basketCalculation);
        $option = array_shift($list);

        if (is_array($option) && $this->isValidBasketPosition($basketPosition)) {
            $option = $this->recalculatePercentageOptionPriceForBlockPrices($option, $basketPosition);
        }

        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsByConfiguration(array $configuration)
    {
        $optionIds = array_keys($configuration);
        $optionList = $this->getOptionList($optionIds, $configuration);

        return $this->createOptionStructs($optionList);
    }

    /**
     * {@inheritdoc}
     */
    public function checkForRequiredOptions($productId)
    {
        $customProductTemplate = $this->templateService->getTemplateByProductId($productId);

        if (!$customProductTemplate) {
            return false;
        }

        foreach ($customProductTemplate['options'] as $option) {
            if ($option['required']) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsFromHash($hash)
    {
        return $this->customProductsOptionRepository->getOptionsFromHash($hash);
    }

    /**
     * @param bool $basketCalculation
     *
     * @return array
     */
    private function getOptionList(array $ids, array $configuration, $basketCalculation = false)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $price = $this->priceGetter->getProductPriceByNumber($configuration['number']);

        $optionList = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option', 'opts')
            ->where('id IN (:ids)')
            ->orderBy('opts.position')
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($optionList as &$option) {
            $option['prices'] = $this->templateService->getPrices($option['id']);
            $option = $this->templateService->enrich($option, $price, $basketCalculation);

            if (!$option['could_contain_values']) {
                $option['values'] = [];
                continue;
            }

            $queryBuilder = $this->connection->createQueryBuilder();
            $valueIds = array_values($configuration[$option['id']]);

            $option['values'] = $queryBuilder->select('*')
                ->from('s_plugin_custom_products_value')
                ->where('id IN (:ids)')
                ->andWhere('option_id = :optionId')
                ->orderBy('position')
                ->setParameter('ids', $valueIds, Connection::PARAM_INT_ARRAY)
                ->setParameter('optionId', $option['id'])
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($option['values'] as &$value) {
                $value['prices'] = $this->templateService->getPrices(null, $value['id']);
                $value = $this->templateService->enrich($value, $price, $basketCalculation);
            }
        }

        return $optionList;
    }

    /**
     * @return array
     */
    private function recalculatePercentageOptionPriceForBlockPrices(array $option, array $basketPosition)
    {
        $quantity = (int) $basketPosition['quantity'];

        if ($quantity <= 1) {
            return $option;
        }

        if (count($option['prices']) <= 0) {
            return $option;
        }

        $customerGroupId = (int) $this->contextService->getShopContext()->getCurrentCustomerGroup()->getId();
        $option = $this->recalculate($option, $basketPosition, $customerGroupId);

        if ($option['could_contain_values']) {
            $option = $this->recalculatePercentageOptionValuePricesForBlockPrices($option, $basketPosition, $customerGroupId);
        }

        return $option;
    }

    /**
     * @param int $customerGroupId
     *
     * @return array
     */
    private function recalculatePercentageOptionValuePricesForBlockPrices(array $option, array $basketPosition, $customerGroupId)
    {
        foreach ($option['values'] as $index => $value) {
            if (count($option['prices']) <= 0) {
                continue;
            }

            $option['values'][$index] = $this->recalculate($value, $basketPosition, $customerGroupId);
        }

        return $option;
    }

    /**
     * @param int $customerGroupId
     *
     * @return array
     */
    private function recalculate(array $item, array $basketPosition, $customerGroupId)
    {
        foreach ($item['prices'] as $price) {
            $percentage = (float) $price['percentage'];
            if ($percentage <= 0.0) {
                continue;
            }

            if ($customerGroupId === (int) $price['customer_group_id']) {
                $price = (float) str_replace(',', '.', $basketPosition['price']);
                $netPrice = (float) str_replace(',', '.', $basketPosition['netprice']);
                $item['surcharge'] = $price / 100 * $percentage;
                $item['netPrice'] = $netPrice / 100 * $percentage;

                break;
            }
        }

        return $item;
    }

    /**
     * @return bool
     */
    private function isValidBasketPosition(array $basketPosition)
    {
        return isset($basketPosition['quantity'], $basketPosition['price'], $basketPosition['netprice']);
    }

    /**
     * @return OptionStruct[]
     */
    private function createOptionStructs(array $options)
    {
        $optionList = [];
        foreach ($options as $option) {
            $optionList[] = (new OptionStruct())->fromArray($option);
        }

        return $optionList;
    }
}
