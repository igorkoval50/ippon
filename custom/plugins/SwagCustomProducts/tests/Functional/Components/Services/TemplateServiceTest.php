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

use PHPUnit\Framework\TestCase;
use SwagCustomProducts\tests\Functional\Components\Services\_fixtures\EnrichValuesTestData;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class TemplateServiceTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_enrichValues_preventContinue2()
    {
        $service = self::getContainer()->get('custom_products.template_service');
        $basePath = self::getContainer()->get('config')->get('base_path');

        $data = new EnrichValuesTestData();

        $result = $service->enrichValues(
            $data->getValues($basePath),
            $data->getValuesPrices(),
            $data->getProductPrice(),
            1,
            1,
            $data->getMedias(
                self::getContainer()->get('shopware_storefront.media_service'),
                self::getContainer()->get('shopware_storefront.context_service')
            )
        );

        static::assertEquals(
            $data->getResult($basePath),
            $this->removeImageWithAndHeight($result)
        );
    }

    private function removeImageWithAndHeight(array $result): array
    {
        foreach ($result as $optionIndex => &$options) {
            foreach ($options as &$option) {
                if (!empty($option['image'])) {
                    unset($option['image']['width']);
                    unset($option['image']['height']);
                }
            }
        }

        return $result;
    }
}
