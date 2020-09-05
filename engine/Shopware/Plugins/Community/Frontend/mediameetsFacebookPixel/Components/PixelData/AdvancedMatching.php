<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\PixelData;

use Shopware;
use Shopware\Models\Customer\Customer;

class AdvancedMatching
{
    /**
     * @var Shopware
     */
    protected $app;

    /**
     * AdvancedMatching constructor.
     */
    public function __construct()
    {
        $this->app = Shopware();
    }

    /**
     * Returns array of Advanced Matching data,
     * keyed and formatted according to Facebook
     *
     * @param $customerId
     * @return array
     */
    public function getData($customerId)
    {
        $data = [];

        if (($customerId = intval($customerId)) != 0) {
            $customer = $this->findCustomerById($customerId);
            if (! is_null($customer)) {
                $customerData = $this->parseCustomer($customer);
                $data = $this->formatData($customerData);
            }
        }

        return $data;
    }

    /**
     * Formats given value how Facebook wants it.
     *
     * @param string|array $value
     * @return string|array
     */
    private function formatData($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->formatData($val);
            }
        } else {
            $value = str_replace(' ', '', strtolower($value));
        }
        return $value;
    }

    /**
     * Parses Customer model and returns array keyed according Facebook.
     *
     * @param Customer $customer
     * @return array
     */
    private function parseCustomer(Customer $customer)
    {
        $data = [];

        $genderMapping = ['ms' => 'f', 'mr' => 'm'];

        $data['em'] = $customer->getEmail();
        $data['fn'] = $customer->getFirstname();
        $data['ln'] = $customer->getLastname();

        $defaultBillingAddress = $customer->getDefaultBillingAddress();
        if ($defaultBillingAddress instanceof Shopware\Models\Customer\Address) {
            $data['ct'] = $defaultBillingAddress->getCity();
            $data['zp'] = $defaultBillingAddress->getZipcode();
        }

        if (isset($genderMapping[$customer->getSalutation()])) {
            $data['ge'] = $genderMapping[$customer->getSalutation()];
        }
        if (! is_null($customer->getBirthday())) {
            $data['db'] = $customer->getBirthday()->format('Ymd');
        }

        return $data;
    }

    /**
     * Returns Customer model by given id.
     *
     * @param int $id
     * @return null|Customer
     */
    private function findCustomerById($id)
    {
        return $this->app
            ->Models()
            ->getRepository('Shopware\Models\Customer\Customer')
            ->find(intval($id));
    }
}
