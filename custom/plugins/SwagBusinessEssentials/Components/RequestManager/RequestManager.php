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

namespace SwagBusinessEssentials\Components\RequestManager;

use Exception;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Customer\Group as CustomerGroup;
use SwagBusinessEssentials\Components\Mail\MailHelperInterface;

class RequestManager implements RequestManagerInterface
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var MailHelperInterface
     */
    private $mailHelper;

    /**
     * RequestManager constructor.
     */
    public function __construct(ModelManager $modelManager, MailHelperInterface $mailHelper)
    {
        $this->mailHelper = $mailHelper;
        $this->modelManager = $modelManager;
    }

    /**
     * Accepts the customer-request and therefore sets the customer-group.
     *
     * @param int $customerId
     *
     * @throws Exception
     */
    public function acceptCustomerRequest($customerId)
    {
        /** @var Customer $customer */
        $customer = $this->modelManager->getRepository(Customer::class)->find($customerId);

        try {
            $this->mailHelper->sendAcceptedMail($customer);
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            $this->updateCustomer($customer);
        }
    }

    /**
     * Declines the customer-request.
     * Only the validation is reset in this case.
     *
     * @param int $customerId
     *
     * @throws Exception
     */
    public function declineCustomerRequest($customerId)
    {
        /** @var Customer $customer */
        $customer = $this->modelManager->getRepository(Customer::class)->find($customerId);

        try {
            $this->mailHelper->sendDeclinedMail($customer);
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            $this->updateCustomer($customer, false);
        }
    }

    /**
     * Helper method to update the customer.
     * It sets the customer-group, if $isAccepted is true, and empties the validation in every case.
     *
     * @param bool $isAccepted
     */
    private function updateCustomer(Customer $customer, $isAccepted = true)
    {
        if ($isAccepted) {
            /** @var CustomerGroup $customerGroup */
            $customerGroup = $this->getTargetCustomerGroup($customer->getValidation());
            $customer->setGroup($customerGroup);
        }

        $customer = $this->removeTargetCustomerGroup($customer);

        $this->modelManager->persist($customer);
        $this->modelManager->flush($customer);
    }

    /**
     * Returns an instance of CustomerGroup by the given key $groupKey - if any is available.
     *
     * @param string $groupKey
     *
     * @return object|null
     */
    private function getTargetCustomerGroup($groupKey)
    {
        return $this->modelManager->getRepository(CustomerGroup::class)->findOneBy([
            'key' => $groupKey,
        ]);
    }

    /**
     * Removes the 'validation' from the customer, so it won't be listed as "to be accepted" anymore.
     *
     * @return Customer
     */
    private function removeTargetCustomerGroup(Customer $customer)
    {
        $customer->setValidation('');

        return $customer;
    }
}
