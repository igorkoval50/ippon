<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\PixelData;

use Shopware;
use Shopware\Models\Order\Order;

class Orders
{
    /**
     * @var Shopware
     */
    protected $app;

    /**
     * Orders constructor.
     */
    public function __construct()
    {
        $this->app = Shopware();
    }

    /**
     * Returns array of data related to Orders of a customer
     *
     * @param $customerId
     * @return array
     */
    public function getData($customerId)
    {
        $data = [];

        if (($customerId = intval($customerId)) != 0) {
            $orders = $this->findByCustomerId($customerId);
            if (! empty($orders)) {
                $data = $this->parseOrders($orders);
            }
        }

        return $data;
    }

    /**
     * Parses all Orders from a customer.
     *
     * @param array|Order[] $orders
     * @return array
     */
    private function parseOrders($orders)
    {
        $data = [];
        $totalOrders = count($orders);

        $data['totalOrders'] = $totalOrders;
        $data['averageOrder'] = 0;

        if ($totalOrders > 0) {
            $totalInvoice = 0;

            /* @var Order $item */
            foreach ($orders as $item) {
                $totalInvoice += $item->getInvoiceAmount();
            }

            $averageOrderAmount = round($totalInvoice / $totalOrders, 2);
            $data['averageOrder'] = $averageOrderAmount;
        }

        return $data;
    }

    /**
     * Returns array of completed Orders by given customer id.
     *
     * @param int $customerId
     * @return array|Order[]
     */
    private function findByCustomerId($customerId)
    {
        return $this->app
            ->Models()
            ->getRepository('Shopware\Models\Order\Order')
            ->findBy([
                'customer' => $customerId,
                'status' => 2
            ]);
    }
}
