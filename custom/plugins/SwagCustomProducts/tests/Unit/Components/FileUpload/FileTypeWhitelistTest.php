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

use SwagCustomProducts\Components\FileUpload\FileTypeWhitelist;

class FileTypeWhitelistTest extends \PHPUnit\Framework\TestCase
{
    const IMAGE_UPLOAD_TYPE = 'imageupload';
    const FILE_UPLOAD_TYPE = 'fileupload';
    const NOT_EXISTING_UPLOAD_TYPE = 'not existing type';

    /**
     * @var FileTypeWhitelist
     */
    private $service;

    /**
     * @before
     */
    public function createServiceBefore()
    {
        $this->service = new FileTypeWhitelist();
    }

    public function test_it_can_be_created()
    {
        $fileTypeWhitelist = new FileTypeWhitelist();

        static::assertInstanceOf(FileTypeWhitelist::class, $fileTypeWhitelist);
    }

    public function test_getExtensionWhitelist_should_return_file_whitelist()
    {
        $whitelist = $this->service->getExtensionWhitelist(self::FILE_UPLOAD_TYPE);

        static::assertArrayNotHasKey('image', $whitelist);
        static::assertArraySubset(['png', 'jpg', 'jpeg', 'tiff', 'svg', 'gif'], $whitelist);
    }

    public function test_getExtensionWhitelist_should_return_image_whitelist()
    {
        $whitelist = $this->service->getExtensionWhitelist(self::IMAGE_UPLOAD_TYPE);

        static::assertEquals(['png', 'jpg', 'jpeg', 'tiff', 'gif'], $whitelist);
    }

    public function test_getExtensionWhitelist_should_throw_exception_if_type_is_not_valid()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(self::NOT_EXISTING_UPLOAD_TYPE);

        $this->service->getExtensionWhitelist(self::NOT_EXISTING_UPLOAD_TYPE);
    }

    public function test_getMimeTypeWhitelist_should_return_file_whitelist()
    {
        $result = $this->service->getMimeTypeWhitelist(self::FILE_UPLOAD_TYPE);

        static::assertArraySubset([
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/tiff',
            'image/svg+xml',
            'image/gif',
            'application/illustrator',
            'application/postscript',
            'application/pdf',
        ], $result);
    }

    public function test_getMimeTypeWhitelist_should_return_image_whitelist()
    {
        $result = $this->service->getMimeTypeWhitelist(self::IMAGE_UPLOAD_TYPE);

        static::assertArraySubset([
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/tiff',
            'image/gif',
        ], $result);
    }

    public function test_getMimeTypeWhitelist_should_throw_exception_if_type_is_not_valid()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(self::NOT_EXISTING_UPLOAD_TYPE);

        $this->service->getMimeTypeWhitelist(self::NOT_EXISTING_UPLOAD_TYPE);
    }

    public function test_getMediaOverrideType_by_extension()
    {
        $result = $this->service->getMediaOverrideType('eps');
        static::assertEquals('UNKNOWN', $result);

        $result = $this->service->getMediaOverrideType('ai');
        static::assertEquals('UNKNOWN', $result);
    }
}
