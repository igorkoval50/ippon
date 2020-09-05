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

namespace SwagNewsletter\Tests\Integration\Components\LiveShopping;

use PHPUnit\Framework\TestCase;
use SwagLiveShopping\Models\Repository;
use SwagNewsletter\Components\LiveShopping\LiveShoppingCompatibilityException;
use SwagNewsletter\Tests\KernelTestCaseTrait;

class LiveShoppingRepositoryNegativeTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_getArticles_should_throw_exception_if_Repository_is_not_available()
    {
        if (class_exists(Repository::class)) {
            $this->markTestSkipped('Test cannot be executed while LiveShopping is installed and active.');
        }

        $liveShoppingRepository = self::getContainer()->get('swag_newsletter.components.live_shopping_repository');

        $this->expectException(LiveShoppingCompatibilityException::class);

        $liveShoppingRepository->getProducts([]);
    }
}
