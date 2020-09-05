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

namespace SwagCustomProducts\tests\Functional\Components\Services;

use SwagCustomProducts\Components\Services\DateTimeService;
use SwagCustomProducts\Components\Services\HashManager;
use SwagCustomProducts\Components\Types\Types\CheckBoxType;
use SwagCustomProducts\Components\Types\Types\ColorSelectType;
use SwagCustomProducts\Components\Types\Types\DateType;
use SwagCustomProducts\Components\Types\Types\FileUploadType;
use SwagCustomProducts\Components\Types\Types\ImageSelectType;
use SwagCustomProducts\Components\Types\Types\ImageUploadType;
use SwagCustomProducts\Components\Types\Types\MultiSelectType;
use SwagCustomProducts\Components\Types\Types\NumberFieldType;
use SwagCustomProducts\Components\Types\Types\RadioType;
use SwagCustomProducts\Components\Types\Types\SelectType;
use SwagCustomProducts\Components\Types\Types\TextAreaType;
use SwagCustomProducts\Components\Types\Types\TextFieldType;
use SwagCustomProducts\Components\Types\Types\TimeType;
use SwagCustomProducts\Components\Types\Types\WysiwygType;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class HashManagerTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_manageHashByConfiguration()
    {
        $hash = $this->createService()->manageHashByConfiguration([]);

        static::assertEquals('0afbbc803fa9775dd5e2009308fb758a', $hash);
    }

    public function test_createHash()
    {
        $hash = $this->createService()->createHash(['some' => 'config']);

        static::assertEquals('5cc285cbf916cb78b44a4ccf85e7bab7', $hash);
    }

    public function test_getMediaOptionsFromTemplate_ensureMediaTypeMatch(): void
    {
        $hashManager = $this->createService();

        $reflectionMethod = (new \ReflectionClass(HashManager::class))->getMethod('getMediaOptionsFromTemplate');
        $reflectionMethod->setAccessible(true);

        $data = [
            ['type' => FileUploadType::TYPE, 'match' => true],
            ['type' => ImageUploadType::TYPE, 'match' => true],
            ['type' => CheckBoxType::TYPE, 'match' => false],
            ['type' => ColorSelectType::TYPE, 'match' => false],
            ['type' => DateType::TYPE, 'match' => false],
            ['type' => ImageSelectType::TYPE, 'match' => false],
            ['type' => MultiSelectType::TYPE, 'match' => false],
            ['type' => NumberFieldType::TYPE, 'match' => false],
            ['type' => RadioType::TYPE, 'match' => false],
            ['type' => SelectType::TYPE, 'match' => false],
            ['type' => TextAreaType::TYPE, 'match' => false],
            ['type' => TextFieldType::TYPE, 'match' => false],
            ['type' => TimeType::TYPE, 'match' => false],
            ['type' => WysiwygType::TYPE, 'match' => false],
        ];

        $result = $reflectionMethod->invoke($hashManager, $data);

        foreach ($result as $item) {
            static::assertTrue($item['match'], 'Item result "match" is not true');
        }
        static::assertCount(2, $result);
    }

    private function createService()
    {
        return new HashManager(
            Shopware()->Container()->get('dbal_connection'),
            new DateTimeService(),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );
    }
}
