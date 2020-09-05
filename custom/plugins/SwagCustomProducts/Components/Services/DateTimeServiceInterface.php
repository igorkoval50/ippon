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

use DateTime;

/**
 * This interface is for easy extending or overwriting the dateTimeService
 */
interface DateTimeServiceInterface
{
    /** DateTime format */
    const YMD_HIS = 'Y-m-d H:i:s';
    const GERMAN = 'd.m.Y H:i:s';
    const UNIX_TIMESTAMP = 'U';

    /** NOW string */
    const NOW = 'NOW';

    /**
     * Returns the formatted DateTime string by the given format
     *
     * @param string $format
     *
     * @return string
     */
    public function getNowString($format = 'U');

    /**
     * Change a formatted dateTime string to another
     *
     * @return string
     */
    public function changeFormatString($dateTimeString, $newFormat);

    /**
     * Returns a new DateTime object with the given format.
     *
     * @param string $dateTimeString
     *
     * @return DateTime
     */
    public function getDateTime($dateTimeString = 'NOW');
}
