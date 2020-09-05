<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Updates;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\Plugin;
use Shopware\Components\Plugin as PluginBootstrap;

/**
 * Class AbstractUpdate
 *
 * @package NetiFoundation\Updates
 */
abstract class AbstractUpdate
{
    const WARNING                   = 'warning';
    const ERROR                     = 'error';
    const SUCCESS                   = 'success';

    const RUN_BEFORE_GENERAL_UPDATE = 0;
    const RUN_AFTER_GENERAL_UPDATE  = 1;

    /**
     * @var array
     */
    protected $messages = array(
        self::WARNING => array(),
        self::ERROR   => array(),
        self::SUCCESS => array(),
    );

    /**
     * @var PluginBootstrap
     */
    protected $bootstrap;

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var ModelManager
     */
    protected $modelManager;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected $db;

    /**
     * AbstractUpdate constructor.
     *
     * @param PluginBootstrap $bootstrap
     * @param Plugin $plugin
     * @param ModelManager $modelManager
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     * @param string $version
     * @param int $runningOrder (see RUN_-constants)
     * @throws \Exception
     */
    public function __construct(
        PluginBootstrap $bootstrap,
        Plugin $plugin,
        ModelManager $modelManager,
        \Enlight_Components_Db_Adapter_Pdo_Mysql $db,
        $version,
        $runningOrder
    ) {
        $this->setBootstrap($bootstrap);
        $this->setPlugin($plugin);
        $this->setVersion($version);
        $this->modelManager = $modelManager;
        $this->db           = $db;

        switch ($runningOrder){
            case self::RUN_BEFORE_GENERAL_UPDATE:
                $success = ((bool) $this->before());
                break;
            case self::RUN_AFTER_GENERAL_UPDATE:
                $success = ((bool) $this->after());
                break;
            default:
                throw new \InvalidArgumentException('This running order is not implemented yet: '. $runningOrder);
                break;
        }

        $loggingService = Shopware()->Container()->get('neti_foundation.logging_service');

        if ($success) {
            $loggingService->write(
                $plugin->getName(),
                __FUNCTION__,
                sprintf('Update from %s successful', $this->getVersion()),
                $this->getMessages()
            );
        } else {
            $loggingService->write(
                $plugin->getName(),
                __FUNCTION__,
                sprintf('Update from Version %s failed!', $this->getVersion()),
                $this->getMessages()
            );

            throw new \Exception(sprintf('Update from Version %s failed!', $this->getVersion()));
        }
    }

    /**
     * @return bool
     */
    protected function run()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function before()
    {
        return $this->run();
    }

    /**
     * @return bool
     */
    protected function after()
    {
        return true;
    }

    /**
     * Gets the value of version from the record
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the Value to version in the record
     *
     * @param string $version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = (string) $version;

        return $this;
    }

    /**
     * Gets the value of plugin from the record
     *
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Sets the Value to plugin in the record
     *
     * @param Plugin $plugin
     *
     * @return self
     */
    public function setPlugin($plugin)
    {
        if ($plugin instanceof Plugin) {
            $this->plugin = $plugin;
        } else {
            $this->plugin = null;
        }

        return $this;
    }

    /**
     * Sets the Value to bootstrap in the record
     *
     * @param PluginBootstrap $bootstrap
     *
     * @return self
     */
    public function setBootstrap($bootstrap)
    {
        if ($bootstrap instanceof PluginBootstrap) {
            $this->bootstrap = $bootstrap;
        } else {
            $this->bootstrap = null;
        }

        return $this;
    }

    /**
     * Gets the value of bootstrap from the record
     *
     * @return PluginBootstrap
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * @param string|array $msg
     * @param string       $type
     *
     * @throws \Exception
     */
    protected function addMessage($msg, $type = self::ERROR)
    {
        if (! isset($this->messages[$type])) {
            throw new \Exception('Type ' . $type . ' does not exist.');
        }

        $this->messages[$type][] = $msg;
    }

    /**
     * @param null|string $type
     *
     * @return array
     * @throws \Exception
     */
    protected function getMessages($type = null)
    {
        if (null === $type) {
            return $this->messages;
        }

        if (! isset($this->messages[$type])) {
            throw new \Exception('Type ' . $type . ' does not exist.');
        }

        return $this->messages;
    }

    /**
     * Internal helper function to check if a database table column exist.
     *
     * @param $tableName
     * @param $columnName
     *
     * @return bool
     */
    protected function columnExist($tableName, $columnName)
    {
        $sql    = "SHOW COLUMNS FROM " . $tableName . " LIKE '" . $columnName . "'";
        $result = $this->db->fetchRow($sql);

        return ! empty($result);
    }
}
