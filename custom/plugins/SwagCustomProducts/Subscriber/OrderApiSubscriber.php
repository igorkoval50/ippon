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

namespace SwagCustomProducts\Subscriber;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_View_Default;
use SwagCustomProducts\Components\Services\BasketManagerInterface;
use SwagCustomProducts\Components\Services\HashManager;

class OrderApiSubscriber implements SubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Api_Orders' => 'get',
        ];
    }

    /**
     * Extend the API call data with values if there is CustomProduct order position
     */
    public function get(\Enlight_Event_EventArgs $arguments)
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $arguments->get('subject');

        /** @var Enlight_View_Default $view */
        $view = $controller->View();

        if ($controller->Request()->getActionName() !== 'get') {
            return;
        }

        $order = $view->getAssign('data');
        $hashes = $this->getHashes($order);

        if (empty($hashes)) {
            return;
        }

        $configurations = $this->getConfigurations($hashes);

        foreach ($order['details'] as &$detail) {
            $mode = $detail['attribute']['swagCustomProductsMode'];

            if (empty($mode)) {
                continue;
            }

            $hash = $detail['attribute']['swagCustomProductsConfigurationHash'];
            $detail['attribute']['swagCustomProductsValue'] = $this->getValue(
                $detail['articleId'],
                $mode,
                $configurations[$hash]
            );
        }
        unset($detail);

        $view->assign('data', $order);
        $controller->postDispatch();
    }

    /**
     * gets the value by id from the configuration json string
     *
     * @param int|string $id
     * @param int|string $mode
     * @param string     $configuration
     */
    private function getValue($id, $mode, $configuration)
    {
        $configuration = json_decode($configuration, true);

        if ((int) $mode === BasketManagerInterface::MODE_PRODUCT) {
            return $configuration;
        }

        return $configuration[$id];
    }

    /**
     * Reads all configurations by a hashArray from the database
     *
     * @return array
     */
    private function getConfigurations(array $hashes)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select(['hash', 'configuration'])
            ->from(HashManager::CONFIG_HASH_TABLE)
            ->where('hash IN (:hashes)')
            ->setParameter(':hashes', $hashes, Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * Reads and return all CustomProductHashes from the order
     *
     * @return string[]
     */
    private function getHashes(array $order)
    {
        $attributes = array_column($order['details'], 'attribute');
        $hashes = array_column($attributes, 'swagCustomProductsConfigurationHash');

        return array_filter(array_unique($hashes));
    }
}
