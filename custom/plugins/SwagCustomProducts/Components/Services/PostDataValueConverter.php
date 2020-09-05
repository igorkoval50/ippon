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

use SwagCustomProducts\Components\DataConverter\ConverterInterface;
use SwagCustomProducts\Components\DataConverter\RegistryInterface;

class PostDataValueConverter implements PostDataValueConverterInterface
{
    /**
     * @var RegistryInterface
     */
    private $converterRegistry;

    public function __construct(RegistryInterface $converterRegistry)
    {
        $this->converterRegistry = $converterRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function convertPostData(array $postData, array $options)
    {
        $returnData = [];
        foreach ($postData as $key => $value) {
            if (strpos($key, PostDataValueConverterInterface::REPLACE_STRING) !== false) {
                $id = $this->getIdFromKey($key);
                $option = $this->getOptionById($options, $id);

                /** @var ConverterInterface $converter */
                $converter = $this->converterRegistry->get($option['type']);
                $returnData[$id] = $converter->convertRequestData($value);
            }
        }
        $returnData = $this->sortReturnData($returnData, $options);

        /* add ordernumber to the postData */
        $returnData['number'] = $postData['number'];

        return $returnData;
    }

    /**
     * {@inheritdoc}
     */
    public function convertPostDataBackward(array $data)
    {
        $responseData = [];

        foreach ($data as $key => $value) {
            if (is_array($value) && !isset($value[0]['id'])) {
                //Implode non media files
                $value = implode(',', $value);
            }

            $responseData[PostDataValueConverterInterface::REPLACE_STRING . $key] = $value;
        }

        return $responseData;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFromKey($key)
    {
        return str_replace(PostDataValueConverterInterface::REPLACE_STRING, '', $key);
    }

    /**
     * @param array $options
     * @param int   $optionId
     *
     * @return bool|array
     */
    private function getOptionById($options, $optionId)
    {
        foreach ($options as $option) {
            if ($option['id'] == $optionId) {
                return $option;
            }
        }

        return false;
    }

    /**
     *  The options are selected by "ODER BY position". So we can sort the order by the option id
     *
     * @return array
     */
    private function sortReturnData(array $returnData, array $options)
    {
        $newReturnData = [];
        foreach ($options as $option) {
            $id = $option['id'];
            if (!empty($returnData[$id])) {
                $newReturnData[$id] = $returnData[$id];
            }
        }

        return $newReturnData;
    }
}
