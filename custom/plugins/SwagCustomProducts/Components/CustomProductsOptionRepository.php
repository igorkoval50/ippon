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

namespace SwagCustomProducts\Components;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use SwagCustomProducts\Components\Types\Types;

class CustomProductsOptionRepository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns the selected / filled options due to the given hash.
     *
     * @param string $hash
     *
     * @return array
     */
    public function getOptionsFromHash($hash)
    {
        $data = $this->getCustomProduct($hash);
        $config = json_decode($data['configuration'], true);
        $options = json_decode($data['template'], true);

        $result = [];
        foreach ($config as $optionId => $optionValue) {
            $option = $this->getElementById($optionId, $options);
            if ($option === null) {
                continue;
            }

            if ($option['could_contain_values']) {
                $result[] = [
                    'label' => $option['name'],
                    'type' => $option['type'],
                    'multi' => true,
                    'value' => $this->iterateValues($optionValue, $option),
                ];
                continue;
            }

            $result[] = [
                'label' => $option['name'],
                'type' => $option['type'],
                'multi' => false,
                'value' => $this->getOptionValue($option, $optionValue),
            ];
        }

        return $result;
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    private function getCustomProduct($hash)
    {
        /** @var QueryBuilder $query */
        $query = $this->connection->createQueryBuilder();

        $data = $query->select(['configuration', 'template'])
            ->from('s_plugin_custom_products_configuration_hash', 'hash_table')
            ->where('hash_table.hash = :hash')
            ->setParameter(':hash', $hash)
            ->execute()->fetch(\PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * @param int $id
     *
     * @return array|null
     */
    private function getElementById($id, array $elements)
    {
        foreach ($elements as $element) {
            if ($element['id'] == $id) {
                return $element;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    private function iterateValues(array $values, array $option)
    {
        $result = [];
        foreach ($values as $data) {
            $valueData = $this->getElementById($data, $option['values']);

            if (!$valueData) {
                $valueData = [];
            }

            $label = $this->getLabelOfValue($option['type'], $option['name'], $valueData['name']);
            $value = $this->getDataOfValue($option['type'], $valueData['value'], $data, $valueData);

            $result[] = [
                'label' => $label,
                'type' => $option['type'],
                'value' => $value,
            ];
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getOptionValue(array $option, array $optionValue)
    {
        switch ($option['type']) {
            case Types\DateType::TYPE:
                return implode(' ', $optionValue);
            case Types\TextAreaType::TYPE:
            case Types\WysiwygType::TYPE:
            case Types\TextFieldType::TYPE:
                return implode(',', $optionValue);
            default:
                return array_shift($optionValue);
        }
    }

    /**
     * @param string $type
     * @param string $optionName
     * @param string $valueName
     *
     * @return string
     */
    private function getLabelOfValue($type, $optionName, $valueName)
    {
        switch ($type) {
            case Types\FileUploadType::TYPE:
            case Types\ImageUploadType::TYPE:
                return $optionName;

            default:
                return $valueName;
        }
    }

    /**
     * @param string       $type
     * @param string       $value
     * @param string|array $data
     *
     * @return array|string
     */
    private function getDataOfValue($type, $value, $data, array $valueData)
    {
        switch ($type) {
            case Types\ImageSelectType::TYPE:
                return $valueData['image']['file'];
            case Types\FileUploadType::TYPE:
            case Types\ImageUploadType::TYPE:
                return json_decode($data, true);
            case Types\ColorSelectType::TYPE:
                return $value;
            default:
                return $data;
        }
    }
}
