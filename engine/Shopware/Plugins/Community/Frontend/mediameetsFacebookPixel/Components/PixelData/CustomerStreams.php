<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\PixelData;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomerStreams
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        $this->container = Shopware()->Container();
    }

    /**
     * Returns array of data related to CustomerStreams of a customer
     *
     * @param $customerId int
     * @return array
     */
    public function getData($customerId)
    {
        $data = [];

        if (($customerId = intval($customerId)) != 0) {
            $customerStreams = $this->findByCustomerId($customerId);
            if (! empty($customerStreams)) {
                $data = $this->parseCustomerStreams($customerStreams, $customerId);
            }
        }

        return $data;
    }

    /**
     * Parses all CustomerStreams from a customer.
     *
     * @param $customerStreams array
     * @param $customerId int
     * @return array
     */
    private function parseCustomerStreams($customerStreams, $customerId)
    {
        $data = [];

        foreach ($customerStreams[$customerId] as $stream) {
            $data['names'][] = $stream['name'];
            $data['ids'][] = $stream['id'];
        }

        return $data;
    }

    /**
     * Returns array of CustomerStreams by given customer id.
     *
     * @param int $customerId
     * @return array
     */
    private function findByCustomerId($customerId)
    {
        return $this->container
            ->get('shopware.customer_stream.repository')
            ->fetchStreamsForCustomers([$customerId]);
    }
}
