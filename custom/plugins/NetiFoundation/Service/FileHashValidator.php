<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     jmatthiesen
 */

namespace NetiFoundation\Service;

use NetiFoundation\Service\PluginManager\Base;
use Shopware\Models\Plugin\Plugin;

final class FileHashValidator
{
    private static $cache = [];

    private        $pluginManagerBase;

    /**
     * @param Base $pluginManagerBase
     */
    public function __construct(Base $pluginManagerBase)
    {
        $this->pluginManagerBase = $pluginManagerBase;
    }

    /**
     * @param Plugin $plugin
     *
     * @return array
     */
    private function initMd5ChecksumFromPlugin(Plugin $plugin)
    {
        $pluginName = $plugin->getName();

        if (isset(static::$cache[$pluginName])) {
            return static::$cache[$pluginName];
        }

        $pathToPlugin      = $this->pluginManagerBase->getPluginPath($plugin);
        $pathToMd5Checksum = $pathToPlugin . DIRECTORY_SEPARATOR . 'md5checksum.php';

        // Exit if no md5checksum file exists or is not readable
        if (! is_file($pathToMd5Checksum) || ! is_readable($pathToMd5Checksum)) {
            return static::$cache[$pluginName] = [];
        }

        return static::$cache[$pluginName] = include $pathToMd5Checksum;
    }

    /**
     * @param Plugin $plugin
     * @param bool   $key
     *
     * @return array|null|string
     */
    public function getMd5ChecksumFromPlugin(Plugin $plugin, $key = false)
    {
        $pluginMd5Checksum = $this->initMd5ChecksumFromPlugin($plugin);

        if (empty($key)) {
            unset($pluginMd5Checksum['__SECRET__']);

            return $pluginMd5Checksum;
        }

        if (! isset($pluginMd5Checksum[$key])) {
            return null;
        }

        return $pluginMd5Checksum[$key];
    }

    /**
     * @param Plugin $plugin
     *
     * @return array
     */
    public function getMd5ChecksumListFromPlugin(Plugin $plugin)
    {
        return $this->getMd5ChecksumFromPlugin($plugin);
    }

    /**
     * @param Plugin $plugin
     * @param        $key
     *
     * @return array|null|string
     */
    public function getMd5ChecksumEntryFromPlugin(Plugin $plugin, $key)
    {
        return $this->getMd5ChecksumFromPlugin($plugin, $key);
    }
}
