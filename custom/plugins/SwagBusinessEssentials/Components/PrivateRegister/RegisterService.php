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

namespace SwagBusinessEssentials\Components\PrivateRegister;

use Shopware\Bundle\AccountBundle\Service\RegisterServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Customer\Group;
use SwagBusinessEssentials\Components\ConfigHelperInterface;

/**
 * Decorates \Shopware\Bundle\AccountBundle\Service\RegisterService
 */
class RegisterService implements RegisterServiceInterface
{
    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * @var RegisterServiceInterface
     */
    private $decoratedRegisterService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    public function __construct(
        RegisterServiceInterface $decoratedRegisterService,
        ConfigHelperInterface $configHelper,
        ModelManager $modelManager
    ) {
        $this->decoratedRegisterService = $decoratedRegisterService;
        $this->configHelper = $configHelper;
        $this->modelManager = $modelManager;
    }

    /**
     * Adds an additional check for the validation if it is necessary.
     * If not, the customer group is automatically assigned.
     *
     * {@inheritdoc}
     */
    public function register(
        Shop $shop,
        Customer $customer,
        Address $billing,
        Address $shipping = null
    ) {
        $validation = $customer->getValidation();
        if (!$validation) {
            $this->decoratedRegisterService->register($shop, $customer, $billing, $shipping);

            return;
        }

        if ($this->isActivationRequired($validation)) {
            $tempCustomerGroup = $this->getTemporaryCustomerGroup($validation);
            if ($tempCustomerGroup) {
                $customer->setGroup($tempCustomerGroup);
            }

            $this->decoratedRegisterService->register($shop, $customer, $billing, $shipping);

            return;
        }

        // No activation was required, so we clear the validation and directly assign the group
        $customer->setValidation('');
        $customer->setGroup($this->getCustomerGroup($validation));

        $this->decoratedRegisterService->register($shop, $customer, $billing, $shipping);
    }

    /**
     * Checks if the activation for the given customer group is active.
     *
     * @param string $validation
     *
     * @return bool|null
     */
    private function isActivationRequired($validation)
    {
        $requireUnlock = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'requireunlock',
            $validation
        );

        if ($validation === 'H' && $requireUnlock === false) {
            return true;
        }

        return (bool) $requireUnlock;
    }

    /**
     * Returns the customer group model instance related to the given customer group key.
     *
     * @param string $customerGroupKey
     *
     * @return Group|null
     */
    private function getCustomerGroup($customerGroupKey)
    {
        return $this->modelManager->getRepository(Group::class)->findOneBy([
            'key' => $customerGroupKey,
        ]);
    }

    /**
     * Returns the customer group from the "assignGroupBeforeUnlock" configuration.
     *
     * @param string $customerGroup
     *
     * @return Group|null
     */
    private function getTemporaryCustomerGroup($customerGroup)
    {
        $customerGroupKey = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'assigngroupbeforeunlock',
            $customerGroup
        );

        return $this->getCustomerGroup($customerGroupKey);
    }
}
