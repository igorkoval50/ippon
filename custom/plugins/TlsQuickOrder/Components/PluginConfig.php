<?php
/**
 * Copyright (c) TreoLabs GmbH
 *
 * This Software is the property of TreoLabs GmbH and is protected
 * by copyright law - it is NOT Freeware and can be used only in one project
 * under a proprietary license, which is delivered along with this program.
 * If not, see <https://treolabs.com/eula>.
 *
 * This Software is distributed as is, with LIMITED WARRANTY AND LIABILITY.
 * Any unauthorised use of this Software without a valid license is
 * a violation of the License Agreement.
 *
 * According to the terms of the license you shall not resell, sublicense,
 * rent, lease, distribute or otherwise transfer rights or usage of this
 * Software or its derivatives. You may modify the code of this Software
 * for your own needs, if source code is provided.
 */

namespace TlsQuickOrder\Components;

use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\ConfigReader;
use Shopware\Models\Shop\Repository;
use Shopware\Models\Shop\Shop;

class PluginConfig
{
    /**
     * @var string
     */
    private $pluginName;
    /**
     * @var ConfigReader
     */
    private $configReader;
    /**
     * @var array
     */
    private $config;
    /**tls_quick_order_active
     * @var Container
     */
    private $container;
    /**
     * @var ModelManager
     */
    private $em;

    /**
     * CleanupService constructor.
     * @param string $pluginName
     * @param ConfigReader $configReader
     * @param ModelManager $em
     * @param Container $container
     */
    public function __construct($pluginName, ConfigReader $configReader, ModelManager $em, Container $container)
    {
        $this->pluginName = $pluginName;
        $this->configReader = $configReader;
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param string|null $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name = null, $default = null)
    {
        if (!$this->config) {
            $this->config = $this->configReader->getByPluginName($this->pluginName, $this->getShop());
        }

        if (is_null($name)) {
            return $this->config;
        }

        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }

    /**
     * @return Shop
     */
    private function getShop()
    {
        if ($this->container->initialized('shop')) {
            return $this->container->get('shop');
        }

        /** @var Repository $repository */
        $repository = $this->em->getRepository(Shop::class);
        return $repository->getActiveDefault();
    }
}
