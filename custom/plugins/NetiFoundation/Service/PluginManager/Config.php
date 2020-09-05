<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use NetiFoundation\Service\Shop as ShopService;
use NetiFoundation\Service\ShopInterface;
use NetiFoundation\Struct\PluginConfig;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\CachedConfigReader;
use Shopware\Models\Plugin\Plugin;
use Shopware\Models\Shop\Shop;

/**
 * Class View
 *
 * @package NetiFoundation\Service\PluginManager
 */
class Config implements ConfigInterface
{
    /**
     * @var BaseInterface
     */
    protected $pluginManagerBase;

    /**
     * @var CachedConfigReader
     */
    protected $configReader;

    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var ShopInterface
     */
    protected $shop;

    /**
     * @param ModelManager       $em
     * @param Base               $pluginManagerBase
     * @param CachedConfigReader $configReader
     * @param ShopService        $shop
     */
    public function __construct(
        ModelManager $em,
        Base $pluginManagerBase,
        CachedConfigReader $configReader,
        ShopService $shop
    ) {
        $this->em                = $em;
        $this->pluginManagerBase = $pluginManagerBase;
        $this->configReader      = $configReader;
        $this->shop              = $shop;
    }

    /**
     * @param Plugin|object|string $plugin
     * @param Shop                 $shop
     * @param string|null          $className
     *
     * @return PluginConfig|null|object
     * @throws \Exception
     */
    public function getPluginConfig($plugin, Shop $shop = null, $className = null)
    {
        $plugin = $this->pluginManagerBase->getPluginModelFromContext($plugin);
        if ($plugin instanceof Plugin && $plugin->getActive()) {
            if (! $shop) {
                $shop = $this->getActiveShop();
            }

            $config = $this->configReader->getByPluginName($plugin->getName(), $shop);
            if (! $className) {
                $pluginModel = $this->pluginManagerBase->getPluginModelByName($plugin);
                $className   = sprintf('%s\\Struct\\PluginConfig', $pluginModel->getName());
            }

            if (class_exists($className)) {
                $config = new $className($config);
            } else {
                throw new \Exception(sprintf('Required class %s does not exist.', $className));
            }

            return $config;
        }

        return null;
    }

    /**
     * @return Shop
     */
    protected function getActiveShop()
    {
        return $this->shop->getActiveShop();
    }
}
