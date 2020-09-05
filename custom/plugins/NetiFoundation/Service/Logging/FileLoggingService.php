<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\Logging;

use NetiFoundation\NetiFoundation;
use NetiFoundation\Service\PluginManager\Config;

/**
 * Class FileLoggingService
 *
 * @package NetiFoundation\Service\Logging
 *
 * @deprecated 4.0.0 - use shopware pluginlogger instead.
 */
class FileLoggingService implements LoggingServiceInterface
{
    /**
     * @var  string $logDir
     */
    private $logDir;

    /**
     * @var \NetiFoundation\Struct\PluginConfig
     */
    private $pluginConfig;

    /**
     * @var string
     */
    private $loggerMode;

    /**
     * FileLoggingService constructor.
     *
     * @param Config $config
     * @param string $logDir
     * @param string $loggerMode
     *
     * @throws \Exception
     */
    public function __construct(Config $config, $logDir, $loggerMode)
    {
        $this->logDir = $logDir . DIRECTORY_SEPARATOR
                        . 'NetInventors' . DIRECTORY_SEPARATOR
                        . date('Y') . DIRECTORY_SEPARATOR
                        . date('m') . DIRECTORY_SEPARATOR;

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777 - umask(), true);
        }

        $this->pluginConfig = $config->getPluginConfig($this);
        $this->loggerMode   = $loggerMode;
    }

    /**
     * @param string $pluginName
     * @param string $action
     * @param string $comment
     * @param array  $params
     *
     * @return boolean
     */
    public function write($pluginName, $action, $comment = '', $params = [])
    {
        if (
            'cli' === PHP_SAPI ||
            !is_dir($this->logDir) ||
            !is_writable($this->logDir) ||
            (NetiFoundation::LOGGER_OPTIONAL === $this->loggerMode && !$this->pluginConfig->isFileLogging())
        ) {
            return false;
        }

        $logFile = $this->logDir . $pluginName . '_' . date('Y-m-d') . '.log';

        $data = sprintf(
            '%s %s: %s',
            date('H:i:s'),
            $action,
            $comment
        );
        $data .= !empty($params) ? ' - Parameter: ' . json_encode($params) : '';

        return (bool)file_put_contents($logFile, $data . PHP_EOL, FILE_APPEND);
    }
}
