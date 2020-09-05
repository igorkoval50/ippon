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
use SwagNewsletter\Components\LiveShopping\LiveShoppingRepository;
use SwagNewsletter\Tests\LiveShoppingCompatiblityTestCaseTrait;

class LiveShoppingServiceTest extends TestCase
{
    use LiveShoppingCompatiblityTestCaseTrait;

    public function test_getLiveShoppingArticles_should_return_all_configured_live_shopping_articles()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/live_shopping.sql'));

        $articles = $this->getLiveShoppingService()->getProducts([]);

        $this->assertContains([
            'articleId' => 272,
            'name' => 'Spachtelmasse',
        ], $articles);
    }

    public function test_getLiveShoppingArticles_should_filter_articles_by_name()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/live_shopping.sql'));

        $articles = $this->getLiveShoppingService()->getProducts([
            ['property' => 'product.name', 'value' => '%spachtel%', 'operator' => 'LIKE'],
        ]);

        $this->assertEquals([
            [
                'articleId' => 272,
                'name' => 'Spachtelmasse',
            ],
        ], $articles);
    }

    public function test_getLiveShoppingArticles_should_ignore_LiveShopping_articles_which_are_expired()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/live_shopping.sql'));

        $articles = $this->getLiveShoppingService()->getProducts([
            ['property' => 'product.name', 'value' => '%latte macchiato%', 'operator' => 'LIKE'],
        ]);

        $this->assertEmpty($articles);
    }

    public function test_getLiveShoppingArticles_should_ignore_future_LiveShopping_articles()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/live_shopping.sql'));

        $articles = $this->getLiveShoppingService()->getProducts([
            ['property' => 'product.name', 'value' => '%Emmelkamp Holunder%', 'operator' => 'LIKE'],
        ]);

        $this->assertEmpty($articles);
    }

    /**
     * @return LiveShoppingRepository
     */
    private function getLiveShoppingService()
    {
        return  $liveShoppingRepository = new LiveShoppingRepository(
            self::getContainer()->get('models'),
            self::getContainer()->get('swag_newsletter.dependendency_provider'),
            self::getContainer()->get('swag_liveshopping.live_shopping')
        );
    }
}
