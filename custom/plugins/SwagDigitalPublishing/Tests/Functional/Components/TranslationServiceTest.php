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

namespace SwagDigitalPublishing\Tests\Functional\Components;

use PHPUnit\Framework\TestCase;
use Shopware\Components\DependencyInjection\Container;
use SwagDigitalPublishing\Services\TranslationService;
use SwagDigitalPublishing\Tests\KernelTestCaseTrait;

class TranslationServiceTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var TranslationService
     */
    private $translationService;

    public function test_translateElements_there_should_be_a_translated_text_element()
    {
        $this->beforeTest();
        $this->createBanners();

        $layer = [
            'id' => '5168212',
            'label' => 'translationLayerLink',
            'link' => 'http://www.deutscheSeite.de',
            'elements' => [
                [
                    'id' => '7168212',
                    'name' => 'text',
                    'text' => 'Das ist ein Deutscher Text',
                ],
            ],
        ];

        $expectedSubset = [
            'id' => '7168212',
            'name' => 'text',
            'text' => 'This is a english text',
        ];

        $result = $this->translationService->translateElements($layer, 2, 1);

        static::assertSame($expectedSubset['id'], $result['elements'][0]['id']);
        static::assertSame($expectedSubset['name'], $result['elements'][0]['name']);
        static::assertSame($expectedSubset['text'], $result['elements'][0]['text']);
    }

    public function test_translateElements_there_should_be_a_translated_button_element()
    {
        $this->beforeTest();
        $this->createBanners();

        $layer = [
            'id' => '5168212',
            'label' => 'translationLayerLink',
            'link' => 'http://www.deutscheSeite.de',
            'elements' => [
                [
                    'id' => '8168212',
                    'name' => 'button',
                    'text' => 'Knopf Text',
                ],
            ],
        ];

        $expectedSubset = [
            'id' => '8168212',
            'name' => 'button',
            'text' => 'Button text',
        ];

        $result = $this->translationService->translateElements($layer, 2, 1);

        static::assertSame($expectedSubset['id'], $result['elements'][0]['id']);
        static::assertSame($expectedSubset['name'], $result['elements'][0]['name']);
        static::assertSame($expectedSubset['text'], $result['elements'][0]['text']);
    }

    public function test_translateElements_there_should_be_a_translated_image_element()
    {
        $this->beforeTest();
        $this->createBanners();

        $layer = [
            'id' => '5168212',
            'label' => 'translationLayerLink',
            'link' => 'http://www.deutscheSeite.de',
            'elements' => [
                [
                    'id' => '8188212',
                    'name' => 'image',
                    'alt' => 'Knopf Text',
                ],
            ],
        ];

        $expectedSubset = [
            'id' => '8188212',
            'name' => 'image',
            'alt' => 'Alternative english text',
        ];

        $result = $this->translationService->translateElements($layer, 2, 1);

        static::assertSame($expectedSubset['id'], $result['elements'][0]['id']);
        static::assertSame($expectedSubset['name'], $result['elements'][0]['name']);
        static::assertSame($expectedSubset['alt'], $result['elements'][0]['alt']);
    }

    public function test_translateLink_there_should_be_a_translated_layer_link()
    {
        $this->beforeTest();
        $this->createBanners();

        $layer = [
            'id' => '5168212',
            'label' => 'translationLayerLink',
            'link' => 'http://www.deutscheSeite.de',
        ];

        $expectedSubset = [
            'id' => '5168212',
            'label' => 'translationLayerLink',
            'link' => 'http://www.englishPage.com',
        ];

        $translatedLayer = $this->translationService->translateLink($layer, 2);

        static::assertSame($expectedSubset['id'], $translatedLayer['id']);
        static::assertSame($expectedSubset['label'], $translatedLayer['label']);
        static::assertSame($expectedSubset['link'], $translatedLayer['link']);
    }

    public function test_translateElements_replaceNewLinesWithHtmlBreaks(): void
    {
        $this->beforeTest();
        $this->createBanners();

        $layer = [
            'elements' => [
                [
                    'id' => '8188212',
                    'name' => 'text',
                    'text' => sprintf(
                        ' %s %s %sZombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro. %sDe carne lumbering animata corpora quaeritis.%sSummus brains sit​​, morbo vel maleficia?',
                        PHP_EOL,
                        PHP_EOL,
                        PHP_EOL,
                        PHP_EOL,
                        PHP_EOL
                    ),
                ],
            ],
        ];

        $expectedText = '<br><br><br>Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro.<br>De carne lumbering animata corpora quaeritis.<br>Summus brains sit​​, morbo vel maleficia?';
        $translatedLayer = $this->translationService->translateElements($layer, 1, 1);

        static::assertSame($expectedText, $translatedLayer['elements'][0]['text']);
    }

    private function beforeTest()
    {
        $this->container = Shopware()->Container();

        $this->translationService = new TranslationService(
            Shopware()->Container()->get('translation')
        );
    }

    private function createBanners()
    {
        $sql = file_get_contents(__DIR__ . '/Fixtures/translateTestBanners.sql');
        $this->container->get('dbal_connection')->exec($sql);
    }
}
