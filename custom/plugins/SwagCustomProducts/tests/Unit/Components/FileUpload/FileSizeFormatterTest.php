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

namespace SwagCustomProducts\Tests\Unit\Components\FileUpload;

use SwagCustomProducts\Components\FileUpload\FileSizeFormatter;

class FileSizeFormatterTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_can_be_created()
    {
        $service = new FileSizeFormatter();
        static::assertInstanceOf(FileSizeFormatter::class, $service);
    }

    public function test_it_should_format_bytes_to_mb()
    {
        $service = new FileSizeFormatter();
        $result = $service->formatBytes(1000000);
        static::assertEquals('0.95 MB', $result);
    }
}
