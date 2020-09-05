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

namespace SwagBundle\Tests;

class ReflectionHelper
{
    public static function getMethod(string $className, string $methodName): \ReflectionMethod
    {
        $method = (new \ReflectionClass($className))->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    public static function getProperty(string $className, string $methodName): \ReflectionProperty
    {
        $property = (new \ReflectionClass($className))->getProperty($methodName);
        $property->setAccessible(true);

        return $property;
    }
}
