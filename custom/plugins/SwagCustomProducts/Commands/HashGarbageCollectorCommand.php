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

namespace SwagCustomProducts\Commands;

use Shopware\Commands\ShopwareCommand;
use SwagCustomProducts\Components\GarbageCollection\GarbageCollectorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HashGarbageCollectorCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('swag:customproducts:configurations:cleanup')
            ->setDescription('Deletes expired Custom Products configurations and files.')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command deletes Custom Products configurations, which are not permanent and older than the time interval configured by the shop owner.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var GarbageCollectorInterface $garbageCollectionService */
        $garbageCollectionService = $this->container->get('custom_products.garbage_collection.garbage_collector_service');
        $garbageCollectionService->cleanUp();

        $output->writeln('Successfully cleaned up all hashes.');
    }
}
