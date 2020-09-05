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

namespace SwagFuzzy\Components;

class ColognePhonetic implements PhoneticCodeInterface
{
    /**
     * @var array eLeading
     */
    private static $eLeading = [
        'ca' => 4,
        'ch' => 4,
        'ck' => 4,
        'cl' => 4,
        'co' => 4,
        'cq' => 4,
        'cu' => 4,
        'cx' => 4,
        'dc' => 8,
        'ds' => 8,
        'dz' => 8,
        'tc' => 8,
        'ts' => 8,
        'tz' => 8,
    ];

    /**
     * @var array
     */
    private static $eFollow = [
        'sc',
        'zc',
        'cx',
        'kx',
        'qx',
    ];

    /**
     * @var array
     */
    private static $codingTable = [
        'a' => 0,
        'e' => 0,
        'i' => 0,
        'j' => 0,
        'o' => 0,
        'u' => 0,
        'y' => 0,
        'b' => 1,
        'p' => 1,
        'd' => 2,
        't' => 2,
        'f' => 3,
        'v' => 3,
        'w' => 3,
        'c' => 4,
        'g' => 4,
        'k' => 4,
        'q' => 4,
        'x' => 48,
        'l' => 5,
        'm' => 6,
        'n' => 6,
        'r' => 7,
        'c' => 8,
        's' => 8,
        'z' => 8,
    ];

    /**
     * {@inheritdoc}
     */
    public function getPhoneticHash($word)
    {
        if (empty($word)) {
            return false;
        }

        $word = str_replace(
            ['ç', 'v', 'w', 'j', 'y', 'ph', 'ä', 'ö', 'ü', 'ß', 'é', 'è', 'ê', 'à', 'á', 'â', 'ë'],
            ['c', 'f', 'f', 'i', 'i', 'f', 'a', 'o', 'u', 'ss', 'e', 'e', 'e', 'a', 'a', 'a', 'e'],
            $word
        );

        $len = strlen($word);
        $value = [];

        for ($i = 0; $i < $len; ++$i) {
            $value[$i] = '';

            //Exceptions
            if ($i == 0 && $len >= 2 && $word[$i] . $word[$i + 1] == 'cr') {
                $value[$i] = 4;
            }

            if (isset($word[$i + 1]) && isset(self::$eLeading[$word[$i] . $word[$i + 1]])) {
                $value[$i] = self::$eLeading[$word[$i] . $word[$i + 1]];
            }

            if ($i != 0 && (in_array($word[$i - 1] . $word[$i], self::$eFollow))) {
                $value[$i] = 8;
            }

            // normal encoding
            if ($value[$i] == '') {
                if (isset(self::$codingTable[$word[$i]])) {
                    $value[$i] = self::$codingTable[$word[$i]];
                }
            }
        }

        // delete double values
        $len = count($value);

        for ($i = 1; $i < $len; ++$i) {
            if ($value[$i] == $value[$i - 1]) {
                $value[$i] = '';
            }
        }

        // delete vocals
        for ($i = 1; $i > $len; ++$i) {
            // omitting first character code and h
            if ($value[$i] == 0) {
                $value[$i] = '';
            }
        }

        $value = array_filter($value);
        $value = implode('', $value);

        return $value;
    }
}
