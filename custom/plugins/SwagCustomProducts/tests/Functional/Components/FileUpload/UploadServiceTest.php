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

namespace SwagCustomProducts\tests\Functional\Components\FileUpload;

use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\Models\Media\Media;
use SwagCustomProducts\Components\FileUpload\Uploader;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadServiceTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    /** The fake size of the file that is being used. */
    const UPLOAD_FILE_SIZE = 50000;

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * @var Uploader
     */
    private $uploaderService;

    /**
     * @var string
     */
    private $tempCopyPath;

    /**
     * @var string
     */
    private $mediaPath;

    public function test_upload_of_an_image_success()
    {
        $this->beforeTest();

        $this->tempCopyPath = $this->createFileCopy(__DIR__ . '/_fixtures/test_image.jpg');

        $uploadedFile = $this->getUploadedFile('test_image.jpg', 'image/jpeg', $this->tempCopyPath);
        $createdMedia = $this->uploaderService->upload($uploadedFile);

        $this->mediaPath = $createdMedia->getPath();

        static::assertInstanceOf(Media::class, $createdMedia);
        static::assertEquals('IMAGE', $createdMedia->getType());
        static::assertEquals('jpg', $createdMedia->getExtension());

        static::assertTrue($this->mediaService->has($this->mediaPath));
        static::assertGreaterThanOrEqual(1, count($createdMedia->getThumbnails()));

        $this->afterTest();
    }

    public function test_upload_of_an_image_with_no_generatable_thumbnails_ai()
    {
        $this->beforeTest();

        $this->tempCopyPath = $this->createFileCopy(__DIR__ . '/_fixtures/test_invalid.ai');

        $uploadedFile = $this->getUploadedFile('test_invalid.ai', 'application/illustrator', $this->tempCopyPath);
        $createdMedia = $this->uploaderService->upload($uploadedFile);

        $this->mediaPath = $createdMedia->getPath();

        $this->afterTest();

        static::assertInstanceOf(Media::class, $createdMedia);
        static::assertEquals('UNKNOWN', $createdMedia->getType());
        static::assertEquals('ai', $createdMedia->getExtension());
    }

    public function test_upload_of_an_image_with_no_generatable_thumbnails_tiff()
    {
        $this->beforeTest();

        $this->tempCopyPath = $this->createFileCopy(__DIR__ . '/_fixtures/test_invalid.tiff');

        $uploadedFile = $this->getUploadedFile('test_invalid.tiff', 'image/tiff', $this->tempCopyPath);
        $createdMedia = $this->uploaderService->upload($uploadedFile);

        $this->mediaPath = $createdMedia->getPath();

        $this->afterTest();

        static::assertInstanceOf(Media::class, $createdMedia);
        static::assertEquals('UNKNOWN', $createdMedia->getType());
        static::assertEquals('tiff', $createdMedia->getExtension());
    }

    public function test_upload_of_an_image_with_no_generatable_thumbnails_svg()
    {
        $this->beforeTest();

        $this->tempCopyPath = $this->createFileCopy(__DIR__ . '/_fixtures/test_invalid.svg');

        $uploadedFile = $this->getUploadedFile('test_invalid.svg', 'image/svg-xml', $this->tempCopyPath);
        $createdMedia = $this->uploaderService->upload($uploadedFile);

        $this->mediaPath = $createdMedia->getPath();

        $this->afterTest();

        static::assertInstanceOf(Media::class, $createdMedia);
        static::assertEquals('UNKNOWN', $createdMedia->getType());
        static::assertEquals('svg', $createdMedia->getExtension());
    }

    public function test_upload_of_an_image_with_no_generatable_thumbnails_eps()
    {
        $this->beforeTest();

        $this->tempCopyPath = $this->createFileCopy(__DIR__ . '/_fixtures/test_invalid.eps');

        $uploadedFile = $this->getUploadedFile('test_invalid.eps', 'application/postscript', $this->tempCopyPath);
        $createdMedia = $this->uploaderService->upload($uploadedFile);

        $this->mediaPath = $createdMedia->getPath();

        $this->afterTest();

        static::assertInstanceOf(Media::class, $createdMedia);
        static::assertEquals('UNKNOWN', $createdMedia->getType());
        static::assertEquals('eps', $createdMedia->getExtension());
    }

    private function beforeTest()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();

        $this->mediaService = Shopware()->Container()->get('shopware_media.media_service');
        $this->uploaderService = Shopware()->Container()->get('custom_products.file_upload.uploader');
    }

    private function afterTest()
    {
        $this->deleteMedia($this->mediaPath);
        $this->deleteFileCopy($this->tempCopyPath);
    }

    /**
     * @param string $name
     * @param string $mimeType
     * @param string $path
     *
     * @return UploadedFile
     */
    private function getUploadedFile($name = '', $mimeType = '', $path = '')
    {
        return new UploadedFile($path, $name, $mimeType, self::UPLOAD_FILE_SIZE);
    }

    /**
     * The Uploader moves the source file to the media directory,
     * therefore, we have to create a copy of the source file first.
     *
     * @param string $path
     *
     * @return string
     */
    private function createFileCopy($path)
    {
        copy($path, $path . '.tmp');

        return $path . '.tmp';
    }

    /**
     * The copy of the source file can be delete after the test has run.
     *
     * @param string $path
     */
    private function deleteFileCopy($path)
    {
        unlink($path);
    }

    /**
     * The media object can be removed from shopware after a test has run.
     */
    private function deleteMedia($path)
    {
        $this->mediaService->delete($path);
    }
}
