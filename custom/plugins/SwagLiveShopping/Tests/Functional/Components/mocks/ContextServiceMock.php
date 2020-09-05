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

namespace SwagLiveShopping\Tests\Functional\Components\mocks;

use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\Currency;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;

class ContextServiceMock extends ContextService
{
    public $testCurrencyFactor = 1;

    public function getShopContext()
    {
        $context = parent::getShopContext();

        return new ShopContext(
            $context->getBaseUrl(),
            $context->getShop(),
            $this->getCurrency(),
            $context->getCurrentCustomerGroup(),
            $context->getFallbackCustomerGroup(),
            $context->getTaxRules(),
            $context->getPriceGroups(),
            $context->getArea(),
            $context->getCountry(),
            $context->getState(),
            []
        );
    }

    public function getCurrency(): Currency
    {
        $currency = new Currency();

        $currency->setFactor($this->testCurrencyFactor);
        $currency->setName('unittest');
        $currency->setSymbol('FOO');

        return $currency;
    }

    public function setTestCurrencyFactor($factor): void
    {
        $this->testCurrencyFactor = $factor;
    }
}
