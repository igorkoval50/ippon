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
use Enlight_Hook_HookArgs;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Shopware_Components_Document;
use Smarty_Data;

class DocumentValueExtender implements DocumentValueExtenderInterface
{
    const OPTION = 2;
    const VALUE = 3;

    /**
     * @var array
     */
    private $whiteList = ['date', 'numberfield', 'textarea', 'textfield', 'time', 'wysiwyg'];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var HashManagerInterface
     */
    private $hashManager;

    public function __construct(Connection $connection, HashManagerInterface $hashManager)
    {
        $this->connection = $connection;
        $this->hashManager = $hashManager;
    }

    /**
     * {@inheritdoc}
     */
    public function extendWithValues(Enlight_Hook_HookArgs $args)
    {
        /* @var Shopware_Components_Document $document */
        $document = $args->getSubject();

        /* @var Smarty_Data $view */
        $view = $document->_view;

        $orderData = $view->getTemplateVars('Order');
        $positions = $orderData['_positions'];

        $values = [];

        foreach ($positions as &$position) {
            if (!isset($position['attributes']['swag_custom_products_mode'])) {
                continue;
            }

            if ((int) $position['attributes']['swag_custom_products_mode'] !== BasketManagerInterface::MODE_OPTION) {
                continue;
            }

            $optionId = $position['articleID'];
            $optionType = $this->getOptionTypeById($optionId);

            if (!in_array($optionType, $this->whiteList, true)) {
                continue;
            }

            $hash = $position['attributes']['swag_custom_products_configuration_hash'];
            $config = $this->hashManager->findConfigurationByHash($hash);
            if (!$config) {
                $config = [];
            }

            $values[$position['id']][$optionId] = $config[$optionId][0];
        }
        unset($position);

        $view->assign('customProductOptionValues', $values);
    }

    /**
     * {@inheritdoc}
     */
    public function groupOptionsForDocument(Enlight_Hook_HookArgs $args)
    {
        /* @var Shopware_Components_Document $document */
        $document = $args->getSubject();

        /* @var Smarty_Data $view */
        $view = $document->_view;

        $pages = $view->getTemplateVars('Pages');

        $customProductOptionValues = $view->getTemplateVars('customProductOptionValues');

        $optionValues = $this->getOptionValues($customProductOptionValues);

        $positions = $this->unchunkPositions($pages);

        $positions = $this->mergeOptionValues($positions, $optionValues);

        $pages = $this->chunkPages(
            $positions,
            $document->_document['pagebreak']
        );

        $view->assign('Pages', $pages);
    }

    /**
     * {@inheritdoc}
     */
    public function groupOptionsForMail(array $positions)
    {
        $optionValues = [];

        foreach ($positions[0]['custom_product_adds'] as $customProductAdd) {
            if (!isset($customProductAdd['selectedValue'])) {
                continue;
            }

            if (!in_array($customProductAdd['type'], $this->whiteList, true)) {
                continue;
            }

            $optionValues[$customProductAdd['id']] = $customProductAdd['selectedValue'][0];
        }

        $positions = $this->mergeOptionValues($positions, $optionValues);

        return $positions;
    }

    /**
     * Try to find the type of the option by the option id.
     *
     * @return bool|string
     */
    private function getOptionTypeById($id)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('type')
            ->from('s_plugin_custom_products_option')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchColumn();
    }

    /**
     * Chunks the order positions into the configured amount of pages.
     *
     * @param array  $positions
     * @param string $positionsPerPage
     *
     * @return array
     */
    private function chunkPages($positions, $positionsPerPage)
    {
        $positions = array_chunk($positions, $positionsPerPage, true);

        return $positions;
    }

    /**
     * The order positions are chunked into pages, depending on the configuration.
     * This needs to be merged into one array first before we can work with the positions themselves.
     *
     * @param array $pages
     *
     * @return array
     */
    private function unchunkPositions($pages)
    {
        $result = [];
        $pageCount = count($pages);

        for ($i = 0; $i < $pageCount; ++$i) {
            $result += $pages[$i];
            $pages[$i] = null;
            unset($pages[$i]);
        }

        return $result;
    }

    /**
     * Merges the options and the values in case the option doesn't an own position on the document.
     * That's the case when the option has no price and no own option value, e.g. for textfields.
     *
     * @return array
     */
    private function mergeOptionValues(array $positions, array $optionValues)
    {
        $optionName = [];

        foreach ($positions as $key => $position) {
            if (!isset($position['attributes'])) {
                continue;
            }

            $positionAttribute = $position['attributes'];
            $mode = (int) $positionAttribute['swag_custom_products_mode'];

            if ($mode === self::VALUE && count($optionName) > 0) {
                if (array_key_exists('name', $optionName)) {
                    $position['name'] = $optionName['name'] . ': ' . $position['name'];
                } else {
                    $position['articlename'] = $optionName['articlename'] . ': ' . $position['articlename'];
                }

                $position['hasNoParent'] = true;
                $positions[$key] = $position;
                continue;
            }

            if ($mode !== self::OPTION) {
                continue;
            }

            $optionName = [];

            if (!$this->isPositionNeeded($position, $optionValues)) {
                if (array_key_exists('name', $position)) {
                    $optionName['name'] = $position['name'];
                } else {
                    $optionName['articlename'] = $position['articlename'];
                }
                unset($positions[$key]);
            }
        }

        return array_values($positions);
    }

    /**
     * Creates an array like [ optionKey => value ] from the given array, which contains another array-depth with the articleID's as key.
     *
     * @return array
     */
    private function getOptionValues(array $customProductOptionValues)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($customProductOptionValues));

        return iterator_to_array($iterator);
    }

    /**
     * Checks if the current item needs an own position.
     *
     * @return bool
     */
    private function isPositionNeeded(array $item, array $optionValues)
    {
        return $item['price'] || array_key_exists($item['articleID'], $optionValues);
    }
}
