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

namespace SwagCustomProducts\tests\Unit\Models;

use SwagCustomProducts\Models\ConfigurationHash;

class ConfigurationHashTest extends \PHPUnit\Framework\TestCase
{
    public function test_getAndSet()
    {
        $model = new ConfigurationHash();
        $model->setConfiguration('TEST_CONFIG');
        $model->setCreated(new \DateTime('01-01-2018'));
        $model->setHash('TEST_HASH');
        $model->setPermanent(true);
        $model->setTemplate('TEST_TEMPLATE');

        static::assertEquals('TEST_CONFIG', $model->getConfiguration());
        static::assertEquals('01-01-2018', $model->getCreated()->format('m-d-Y'));
        static::assertEquals('TEST_HASH', $model->getHash());
        static::assertEquals('TEST_TEMPLATE', $model->getTemplate());
        static::assertTrue($model->getPermanent());
    }
}
