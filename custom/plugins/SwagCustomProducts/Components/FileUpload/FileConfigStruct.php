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

namespace SwagCustomProducts\Components\FileUpload;

class FileConfigStruct
{
    /**
     * The maximum file size in bytes.
     *
     * @var int
     */
    private $maxSize;

    /**
     * Option type, i.e. imageupload or fileupload
     *
     * @var string
     */
    private $type;

    /**
     * Amount of maximum files which can be uploaded.
     *
     * @var int
     */
    private $maxFiles;

    /**
     * @return int
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param int $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize ?: 1;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getMaxFiles()
    {
        return $this->maxFiles;
    }

    /**
     * @param int $maxFiles
     */
    public function setMaxFiles($maxFiles)
    {
        $this->maxFiles = $maxFiles ?: 1;
    }
}
