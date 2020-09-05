<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation;

use NetiFoundation\Backports\BackportHelper;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class NetiFoundation
 *
 * @package NetiFoundation
 */
class NetiFoundation extends Plugin
{
    const LOGGER_FILE     = 'file';
    const LOGGER_OPTIONAL = 'optional';

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->setParameter('neti_foundation.plugin_dir', $this->getPath());

        // figure out environment for file logging or reporting
        $env = getenv('NETI_SW_ENV');
        switch ($env) {
            case 'dev':
            case 'staging':
                $logger = self::LOGGER_FILE;
                break;
            case 'live':
            default:
                $logger = self::LOGGER_OPTIONAL;
        }

        $container->setParameter('neti_foundation.logger', $logger);
        $container->setParameter('neti_foundation.environment', $env);

        $helper = new BackportHelper($container);
        $helper->processBackports($container->getParameter('shopware.release.version'));
    }
}
