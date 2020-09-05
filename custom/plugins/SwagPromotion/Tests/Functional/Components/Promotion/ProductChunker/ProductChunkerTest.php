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

namespace SwagPromotion\Tests\Functional;

use PHPUnit\Framework\TestCase;
use SwagPromotion\Components\Promotion\ProductChunker\CheapestProductChunker;
use SwagPromotion\Components\Promotion\ProductChunker\ChunkerNotFoundException;
use SwagPromotion\Components\Promotion\ProductChunker\ProductChunkerRegistry;

/**
 * @small
 */
class ProductChunkerTest extends TestCase
{
    /**
     * @var array
     */
    protected static $ensureLoadedPlugins = [
        'SwagPromotion' => [],
    ];

    public function testChunkerRegistry()
    {
        $reg = new ProductChunkerRegistry(
            [
                new CheapestProductChunker(),
            ]
        );
        $cheapest = $reg->get('cheapest');

        static::assertInstanceOf(
            CheapestProductChunker::class,
            $cheapest
        );
    }

    public function testAddChunker()
    {
        $reg = new ProductChunkerRegistry(
            [
            ]
        );
        $reg->add(new CheapestProductChunker());
        $cheapest = $reg->get('cheapest');
        static::assertInstanceOf(
            CheapestProductChunker::class,
            $cheapest
        );
    }

    public function testChunkerNotFound()
    {
        $reg = new ProductChunkerRegistry(
            [
            ]
        );

        $this->expectException(ChunkerNotFoundException::class);

        $reg->get('foo');
    }

    public function testCheapestChunker()
    {
        $chunker = new CheapestProductChunker();
        $result = $chunker->chunk(
            [
                ['price' => 1],
                ['price' => 3],
                ['price' => 3],
                ['price' => 5],
                ['price' => 4],
                ['price' => 2],
                ['price' => 3],
            ],
            2
        );
        static::assertEquals(
            [
                [
                    ['price' => 1],
                    ['price' => 5],
                ],
                [
                    ['price' => 2],
                    ['price' => 4],
                ],
                [
                    ['price' => 3],
                    ['price' => 3],
                ],
            ],
            $result
        );
    }
}
