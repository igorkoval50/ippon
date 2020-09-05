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

namespace SwagBusinessEssentials\Components\PrivateShopping;

interface RedirectParamHelperInterface
{
    /**
     * Reads the redirect parameters for login / registration and converts them properly.
     *
     * @return array
     */
    public function readRedirectParams(array $data);

    /**
     * Splits the given string $redirectString into an array like this:
     * Example string: 'detail/index/id/1'
     *
     * array(
     *    'detail/index',
     *    array(
     *       'id' => 1
     *    )
     * )
     *
     * @param string $redirectString
     *
     * @return array
     */
    public function convertRedirect($redirectString);

    /**
     * Rebuilds the params-array into a query-string.
     *
     * @param string $controllerAction
     * @param array  $params
     *
     * @return string
     */
    public function buildQueryString($controllerAction, $params);

    /**
     * Builds a new array using the given $paramsArray array.
     * Every 2nd value from the $paramsArray array is the actual value.
     * The previous value will be the key.
     *
     * Example input:
     * [ 'foo', 'bar', 'lorem', 'ipsum' ]
     *
     * Will return:
     * [
     *      [
     *          'key' => 'foo',
     *          'value' => 'bar'
     *      ],
     *      [
     *          'key' => 'lorem',
     *          'value' => 'ipsum'
     *      ]
     * ]
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getParamsFromArray(array $paramsArray);

    /**
     * Builds a redirect-URL due to the given $redirectConfig.
     *
     * @param string $redirectConfig
     *
     * @throws \Exception
     *
     * @return string
     */
    public function buildUrlFromRedirectConfig($redirectConfig);
}
