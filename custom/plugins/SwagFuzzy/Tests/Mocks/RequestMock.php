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

namespace SwagFuzzy\Tests\Mocks;

class RequestMock
{
    /**
     * @var string
     */
    public $actionName;

    /**
     * @var array
     */
    public $params = [];

    /**
     * @param $actionName
     */
    public function __construct($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    public function getParam($string)
    {
        return $this->params[$string];
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function has($string)
    {
        if ($string === '_escaped_fragment_') {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }
}
