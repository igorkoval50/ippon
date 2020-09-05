<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use NetiFoundation\Struct\PluginConfigFile;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Kernel;
use Shopware\Models\Plugin\Plugin as PluginModel;

/**
 * Class Base
 *
 * @package NetiFoundation\Service\PluginManager
 */
class Base implements BaseInterface
{
    /**
     * @var PluginModel[]
     */
    public static $pluginModelCache = array();

    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected $db;

    /**
     * @param ModelManager                             $em
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     */
    public function __construct(
        ModelManager $em,
        \Enlight_Components_Db_Adapter_Pdo_Mysql $db
    ) {
        $this->em = $em;
        $this->db = $db;
    }

    /**
     * @param Plugin|object|string $context
     *
     * @return null|Plugin
     */
    public function getPluginModelFromContext($context)
    {
        $plugin = null;
        if (is_string($context)) {
            $plugin = $this->getPluginModelByName(
                $context
            );
        } elseif ($context instanceof Plugin) {
            $plugin = $context;
        } elseif (is_object($context)) {
            $className = get_class($context);
            $plugin    = $this->getPluginModelByName(
                substr($className, 0, strpos($className, '\\'))
            );
        }

        return $plugin;
    }

    /**
     * @param PluginModel|string $plugin
     *
     * @return null|PluginModel
     */
    public function getPluginModelByName($plugin): ?PluginModel
    {
        if (!$plugin instanceof PluginModel) {
            $pluginName = strtolower($plugin);
            if (! isset(self::$pluginModelCache[$pluginName])) {
                $repository = $this->em->getRepository(PluginModel::class);
                /** @noinspection CallableParameterUseCaseInTypeContextInspection */
                $plugin = $repository->findOneBy(['name' => $plugin]);

                if ($plugin instanceof PluginModel) {
                    self::$pluginModelCache[$pluginName] = $plugin;
                }
            }
        } else {
            $pluginName                          = strtolower($plugin->getName());
            self::$pluginModelCache[$pluginName] = $plugin;
        }

        return self::$pluginModelCache[$pluginName];
    }

    /**
     * @param string $pluginName
     *
     * @return Plugin
     */
    public function getPluginByName($pluginName): Plugin
    {
        /** @var Kernel $kernel */
        $kernel  = Shopware()->Container()->get('kernel');
        $plugins = $kernel->getPlugins();

        if (! isset($plugins[$pluginName])) {
            throw new \InvalidArgumentException(sprintf('Plugin by name "%s" not found.', $pluginName));
        }

        return $plugins[$pluginName];
    }

    /**
     * @param Plugin $plugin
     *
     * @return PluginConfigFile
     */
    public function getConfigFile(Plugin $plugin): PluginConfigFile
    {
        $configPath = sprintf('%s/config.php', $plugin->getPath());
        if (is_file($configPath)) {
            $config = require $configPath;
        }

        return new PluginConfigFile($config ?? []);
    }

    public function hasPluginResource(Plugin $plugin, string $resource): bool
    {
        return file_exists($plugin->getPath() . $resource);
    }

    public function getPluginResource(Plugin $plugin, string $resource): string
    {
        return $plugin->getPath() . $resource;
    }
}
