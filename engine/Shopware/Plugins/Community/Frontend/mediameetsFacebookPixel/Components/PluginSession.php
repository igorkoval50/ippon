<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components;

use Enlight_Components_Session_Namespace;

class PluginSession
{
    /**
     * @var string
     */
    private $varKey = 'mediameetsFacebookPixel';

    /**
     * @var Enlight_Components_Session_Namespace|null
     */
    private $session;

    public function __construct()
    {
        $this->session = Shopware()->Session();
    }

    /**
     * @param null|string $key
     * @return array|string|null
     */
    public function get($key = null)
    {
        $sessionData = $this->session->get($this->varKey);

        if ($key === null || ! is_string($key)) {
            return $sessionData;
        }

        return isset($sessionData[$key])
            ? $sessionData[$key] : null;
    }

    /**
     * @param mixed $data
     * @param null|string $key
     */
    public function set($data = null, $key = null)
    {
        if (is_string($key)) {
            $storedData = $this->get();
            $storedData[$key] = $data;
            $data = $storedData;
        }

        $this->session->offsetSet($this->varKey, $data);
    }
}
