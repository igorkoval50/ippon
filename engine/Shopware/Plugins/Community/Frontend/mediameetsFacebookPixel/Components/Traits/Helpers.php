<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Traits;

trait Helpers
{
    /**
     * Parses a textarea value and returns it as a string
     *
     * @param string
     * @return null|array
     */
    private function textareaValueToArray($value)
    {
        if ($value === '' || ! is_string($value)) {
            return null;
        }

        return array_unique(
            array_map(
                'trim',
                explode(
                    '<br>',
                    nl2br($value, false)
                )
            )
        );
    }
}
