<?php

declare(strict_types=1);

namespace NetiLanguageDetector\Service;

use NetiFoundation\Service\PluginManager\Config;
use NetiLanguageDetector\Struct\PluginConfig;
use Psr\Log\LoggerInterface;

class Debug
{
    /**
     * @var LoggerInterface
     */
    private $fileLogger;

    /**
     * @var LoggerInterface
     */
    private $firePhpLogger;

    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    public function __construct(LoggerInterface $fileLogger, LoggerInterface $firePhpLogger, Config $configService)
    {
        $this->fileLogger    = $fileLogger;
        $this->firePhpLogger = $firePhpLogger;
        $this->pluginConfig  = $configService->getPluginConfig($this);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($message, array $context = [])
    {
        if (!$this->pluginConfig->isDebugMode()) {
            return;
        }

        if (!isset($context['method'])) {
            $context['method'] = $this->detectCallingMethod();
        }

        $this->fileLogger->debug($message, $context);
        $this->firePhpLogger->debug($message, $context);
    }

    private function detectCallingMethod(): string
    {
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        // remove this method
        \array_shift($trace);
        // remove log method
        \array_shift($trace);

        foreach ($trace as $frame) {
            if (false !== \strpos($frame['function'], 'log')) {
                continue;
            }

            return \sprintf('%s::%s', $frame['class'], $frame['function']);
        }

        return '';
    }
}