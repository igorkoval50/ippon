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

/**
 * This interface is for easy extending or overwriting the white listed fileTypes
 */
interface FileTypeWhitelistInterface
{
    /**
     * Returns an array of strings with the MIME types which are whitelisted
     *
     * @param string $type
     *
     * @return string[]|void
     */
    public function getMimeTypeWhitelist($type);

    /**
     * Returns an array of strings with the file extensions which are whitelisted
     *
     * @param string $type
     *
     * @return string[]|void
     */
    public function getExtensionWhitelist($type);

    /**
     * Returns a string which is being used to overwrite the default
     * shopware media types. For example an .tiff image can be of (internal) type unknown if
     * so defined in this function, even though shopware would otherwise completely
     * handle it as image (including generating thumbnails).
     *
     * @param string $extension
     *
     * @return string
     */
    public function getMediaOverrideType($extension);
}
