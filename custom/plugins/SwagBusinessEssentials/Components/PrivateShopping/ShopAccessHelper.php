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

namespace SwagBusinessEssentials\Components\PrivateShopping;

use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use SwagBusinessEssentials\Components\ConfigHelperInterface;
use SwagBusinessEssentials\Components\DependencyProvider;

class ShopAccessHelper implements ShopAccessHelperInterface
{
    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * @var \sAdmin
     */
    private $adminModule;

    public function __construct(
        ConfigHelperInterface $configHelper,
        DependencyProvider $modules
    ) {
        $this->configHelper = $configHelper;
        $this->adminModule = $modules->getModule('admin');
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessAllowed(ShopContextInterface $shopContext)
    {
        $customerGroup = $shopContext->getCurrentCustomerGroup()->getKey();

        if (!$this->isPrivateShoppingActive($customerGroup)) {
            return true;
        }

        if (!$this->shouldDenyCustomerGroup($customerGroup)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrivateShoppingActive($customerGroup)
    {
        $isActive = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'activatelogin',
            $customerGroup
        );

        return $isActive;
    }

    /**
     * Checks if the customer group access should be denied.
     * Depends on the "unlockafterregister" setting. In this case the customer can access the shop, if he is logged in.
     *
     * @param string $customerGroup
     *
     * @return bool
     */
    private function shouldDenyCustomerGroup($customerGroup)
    {
        $unlockAfterRegistration = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'unlockafterregister',
            $customerGroup
        );

        if ($unlockAfterRegistration && $this->adminModule->sCheckUser()) {
            return false;
        }

        return true;
    }
}
