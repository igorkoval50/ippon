<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Service;

class StringOperation implements StringOperationInterface
{
    /**
     * @param string $input
     * @param string $char
     *
     * @return string
     */
    public function decamelize($input, $char = '_')
    {
        if (preg_match('/[A-Z]/', $input) === 0) {
            return $input;
        }
        $pattern = '/([a-z])([A-Z])/';
        $r       = strtolower(preg_replace_callback($pattern, function ($a) use ($char) {
            return $a[1] . $char . strtolower($a[2]);
        }, $input));

        return $r;
    }
}