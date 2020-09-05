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

namespace SwagCustomProducts\Components\Services;

use ZipArchive;

class ZipService implements ZipServiceInterface
{
    const CUSTOM_PRODUCTS_ZIP_TEMP_FOLDER = 'customProducts-tmp/';

    /**
     * @var string
     */
    private $basePath;

    public function __construct(array $appPaths)
    {
        $this->basePath = $appPaths['downloadsDir'] . self::CUSTOM_PRODUCTS_ZIP_TEMP_FOLDER;
    }

    /**
     * {@inheritdoc}
     */
    public function createZipFile($path, $zipFile)
    {
        $this->createTempFolder();

        $zipFile = $this->basePath . $zipFile . '.zip';
        $fileName = basename($path);

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE);
        $zip->addFile($path, $fileName);
        $zip->close();

        return $zipFile;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteZipFile($path)
    {
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function createTempFolder()
    {
        if (is_dir($this->basePath)) {
            return;
        }

        mkdir($this->basePath);
    }
}
