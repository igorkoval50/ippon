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

use SwagCustomProducts\Components\Services\ZipService;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class ZipServiceTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var ZipService
     */
    private $zipService;

    /**
     * @var string
     */
    private $basePath;

    public function test_createZipFile()
    {
        $this->createBasePath();

        $path = __DIR__ . '/_fixtures/XSS.svg';
        $filename = 'testFile';

        $this->zipService->createZipFile($path, $filename);

        $targetFile = $this->basePath . $filename . '.zip';

        static::assertFileExists($targetFile);
        static::assertTrue(is_file($targetFile));
    }

    public function test_deleteZipFile()
    {
        $this->createBasePath();

        $path = __DIR__ . '/_fixtures/XSS.svg';
        $filename = 'testFileTwo';

        $this->zipService->createZipFile($path, $filename);

        $targetFile = $this->basePath . $filename . '.zip';

        $this->zipService->deleteZipFile($targetFile);

        static::assertFileNotExists($targetFile);
        static::assertFalse(is_file($targetFile));
    }

    private function createBasePath()
    {
        $appPaths = Shopware()->Container()->getParameter('shopware.app');
        $this->basePath = $appPaths['downloadsDir'] . 'customProducts-tmp/';

        $this->zipService = new ZipService($appPaths);
    }
}
