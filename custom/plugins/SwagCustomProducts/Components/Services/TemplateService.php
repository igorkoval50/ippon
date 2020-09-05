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
use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;
use SwagCustomProducts\Components\Calculator;
use SwagCustomProducts\Components\FileUpload\FileTypeWhitelist;

class TemplateService implements TemplateServiceInterface
{
    /**
     * @var MediaServiceInterface
     */
    private $mediaService;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Calculator
     */
    private $calculator;

    public function __construct(
        MediaServiceInterface $mediaService,
        ContextServiceInterface $contextService,
        Connection $connection,
        DependencyProviderInterface $dependencyProvider
    ) {
        $this->mediaService = $mediaService;
        $this->contextService = $contextService;
        $this->connection = $connection;
        $this->calculator = $dependencyProvider->getCalculator();
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateByProductId($productId, $enrichTemplate = true, $productPrice = 0.00)
    {
        $templateId = $this->getTemplateId($productId);
        if (!$templateId) {
            return null;
        }

        $template = $this->getTemplate($templateId);
        if (!$template) {
            return null;
        }

        if (!$enrichTemplate) {
            return $template;
        }

        return $this->enrichTemplate($template, $templateId, $productPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function enrichValues(
        array $values,
        array $valuePrices,
        $productPrice = 0.0,
        $customerGroupId,
        $fallbackId,
        array $medias
    ) {
        foreach ($values as &$row) {
            foreach ($row as &$value) {
                $value['prices'] = $valuePrices[$value['id']];
                if (!empty($value['prices'])) {
                    $value = array_merge($value, $this->calculator->getPrice(
                        $this->getCurrentPrice($value['prices'], $customerGroupId, $fallbackId),
                        $this->contextService->getShopContext(),
                        $productPrice
                    ));
                }

                if (empty($medias) || !isset($value['media_id'])) {
                    $value['image'] = json_decode(json_encode([]), true);
                    continue;
                }

                $value['image'] = json_decode(json_encode($medias[$value['media_id']]), true);
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function enrichOptions(
        array $options,
        array $values,
        array $optionPrices,
        $productPrice = 0.0,
        $customerGroupId,
        $fallbackId
    ) {
        foreach ($options as &$option) {
            $option = $this->applyMimeTypes($option);
            $option['prices'] = $optionPrices[$option['id']];
            if (!empty($option['prices'])) {
                $option = array_merge($option, $this->calculator->getPrice(
                    $this->getCurrentPrice($option['prices'], $customerGroupId, $fallbackId),
                    $this->contextService->getShopContext(),
                    $productPrice
                ));
            }

            if (!$option['could_contain_values']) {
                continue;
            }

            $option['values'] = array_key_exists($option['id'], $values) ? $values[$option['id']] : [];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function isInternalNameAssigned($internalName)
    {
        $query = $this->connection->createQueryBuilder();

        $result = $query->select('id')
            ->from('s_plugin_custom_products_template', 'template')
            ->where('internal_name = :internalName')
            ->setParameter(':internalName', $internalName)
            ->execute()
            ->fetchColumn();

        if ($result) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsByTemplateId($templateId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $options = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option')
            ->where('template_id = :templateId')
            ->setParameter('templateId', $templateId)
            ->orderBy('position')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($options as &$option) {
            $option['prices'] = $this->getPrices($option['id']);
            $option = $this->enrich($option);

            if (!$option['could_contain_values']) {
                continue;
            }

            $option['values'] = $this->getValuesByOptionId($option['id'], $option['type']);
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function getPrices($optionId = null, $valueId = null)
    {
        if (($optionId && $valueId) || (!$optionId && !$valueId)) {
            throw new \RuntimeException('optionId OR valueId is needed!');
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('*')->from('s_plugin_custom_products_price');

        if ($optionId) {
            $queryBuilder->where('option_id = :optionId')
                ->setParameter('optionId', $optionId);
        }

        if ($valueId) {
            $queryBuilder->where('value_id = :valueId')
                ->setParameter('valueId', $valueId);
        }

        return $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionById($id, $productPrice = 0.00, $basketCalculation = false)
    {
        if (!$id) {
            return null;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $option = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $option['prices'] = $this->getPrices($id);

        return $this->enrich($option, $productPrice, $basketCalculation);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueById($id, $productPrice = 0.00, $basketCalculation = false)
    {
        if (!$id) {
            return null;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $values = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_value')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $values['prices'] = $this->getPrices(null, $id);

        return $this->enrich($values, $productPrice, $basketCalculation);
    }

    /**
     * {@inheritdoc}
     */
    public function enrich(array $data, $productPrice = 0.00, $basketCalculation = false)
    {
        if (empty($data['prices'])) {
            return $data;
        }

        $customerGroup = $this->contextService->getShopContext()->getCurrentCustomerGroup();

        $price = $this->getRightPrice($data['prices'], $customerGroup->getId());

        if (empty($price)) {
            return $data;
        }

        $price = $this->calculator->getPrice(
            $price,
            $this->contextService->getShopContext(),
            $productPrice,
            $basketCalculation
        );

        $data = array_merge($data, $price);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function enrichTemplate(array $template, $templateId, $productPrice = 0.0)
    {
        $shopContext = $this->contextService->getShopContext();
        $customerGroupId = $shopContext->getCurrentCustomerGroup()->getId();
        $customerGroupFallbackId = $shopContext->getFallbackCustomerGroup()->getId();
        $mediaIds = [];

        if ($template['media_id']) {
            $mediaIds[] = $template['media_id'];
        }

        $options = $this->getOptions($templateId);
        $optionIds = array_column($options, 'id');

        $valueIds = [];
        $values = $this->getValues($optionIds);

        foreach ($values as $value) {
            $valueIds = array_merge(array_column($value, 'id', 'id'), $valueIds);
            $mediaId = array_column($value, 'media_id');
            if (!$mediaId) {
                continue;
            }

            $mediaIds = array_merge($mediaIds, $mediaId);
        }

        $medias = $this->mediaService->getList($mediaIds, $shopContext);
        $optionPrices = $this->getOptionPrices($optionIds);
        $valuePrices = $this->getValuePrices($valueIds);

        $values = $this->enrichValues(
            $values,
            $valuePrices,
            $productPrice,
            $customerGroupId,
            $customerGroupFallbackId,
            $medias
        );

        $template['options'] = $this->enrichOptions(
            $options,
            $values,
            $optionPrices,
            $productPrice,
            $customerGroupId,
            $customerGroupFallbackId
        );

        if ($template['media_id']) {
            $template['media'] = json_decode(json_encode($medias[$template['media_id']]), true);
        }

        return $template;
    }

    /**
     * @param int|string $productId
     *
     * @return array|bool
     */
    private function getTemplateId($productId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('DISTINCT template_id')
            ->from('s_plugin_custom_products_template_product_relation')
            ->where('article_id = :id')
            ->setParameter('id', $productId)
            ->execute()
            ->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * @param int|string $templateId
     *
     * @return array|bool
     */
    private function getTemplate($templateId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from('s_plugin_custom_products_template')
            ->where('id = :id')
            ->setParameter(':id', $templateId)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param int $templateId
     *
     * @return array
     */
    private function getOptions($templateId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option')
            ->where('template_id = :id')
            ->setParameter('id', $templateId)
            ->orderBy('position', 'ASC')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    private function getValues(array $optionIds)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('option_id', 'value.*')
            ->from('s_plugin_custom_products_value', 'value')
            ->where('option_id IN (:ids)')
            ->setParameter(':ids', $optionIds, Connection::PARAM_INT_ARRAY)
            ->orderBy('position', 'ASC')
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);
    }

    /**
     * @return array
     */
    private function getOptionPrices(array $optionIds)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('option_id', 'price.*')
            ->from('s_plugin_custom_products_price', 'price')
            ->where('option_id IN (:ids)')
            ->setParameter(':ids', $optionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);
    }

    /**
     * @return array
     */
    private function getValuePrices(array $valueIds)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('value_id', 'price.*')
            ->from('s_plugin_custom_products_price', 'price')
            ->where('value_id IN (:ids)')
            ->setParameter(':ids', $valueIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);
    }

    /**
     * @param array      $prices
     * @param int|string $customerGroupId
     * @param int|string $customerGroupFallbackId
     *
     * @return array
     */
    private function getCurrentPrice($prices, $customerGroupId, $customerGroupFallbackId)
    {
        if (!$prices) {
            return [];
        }

        $defaultPrice = null;

        foreach ($prices as $price) {
            if ($price['customer_group_id'] == $customerGroupId) {
                return $price;
            }

            if ($price['customer_group_id'] == $customerGroupFallbackId) {
                $defaultPrice = $price;
            }
        }

        return $defaultPrice;
    }

    /**
     * @return array
     */
    private function applyMimeTypes(array $option)
    {
        if (!in_array($option['type'], ['fileupload', 'imageupload'], true)) {
            return $option;
        }

        if ($option['type'] === 'fileupload') {
            $option['allowed_mime_types'] = json_encode(FileTypeWhitelist::$mimeTypeWhitelist['image']);

            return $option;
        }

        $option['allowed_mime_types'] = json_encode(FileTypeWhitelist::$mimeTypeWhitelist['file']);

        return $option;
    }

    /**
     * @param int    $optionId
     * @param string $type
     *
     * @return array
     */
    private function getValuesByOptionId($optionId, $type)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $values = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_value')
            ->where('option_id = :optionId')
            ->setParameter('optionId', $optionId)
            ->orderBy('position')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($values as &$value) {
            $value['prices'] = $this->getPrices(null, $value['id']);
            $value = $this->enrich($value);

            if ($type === 'imageselect' && $value['media_id']) {
                $value['image'] = $this->getMediaById($value['media_id']);
            }
        }

        return $values;
    }

    /**
     * @param int $mediaId
     *
     * @return array
     */
    private function getMediaById($mediaId)
    {
        $context = $this->contextService->getShopContext();

        return json_decode(json_encode($this->mediaService->get($mediaId, $context)), true);
    }

    /**
     * @param int $customerGroupId
     *
     * @return array|null
     */
    private function getRightPrice(array $prices, $customerGroupId)
    {
        $fallbackId = $this->contextService->getShopContext()->getFallbackCustomerGroup()->getId();

        $defaultPrice = null;

        foreach ($prices as $price) {
            if ($price['customer_group_id'] == $customerGroupId) {
                return $price;
            }

            if ($price['customer_group_id'] == $fallbackId) {
                $defaultPrice = $price;
            }
        }

        return $defaultPrice;
    }
}
