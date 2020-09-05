<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Backports;

use NetiFoundation\Backports\ViewAutoloader\Subscriber\ViewAutoloaderSubscriber;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;

class ViewAutoloaderBackport extends AbstractContainerBuilderBackport
{
    public function backportViewAutoloader(Finder $finder)
    {
        $dirs = [];
        foreach ($finder as $dir) {
            /** @var Finder $viewDirs */
            $viewDirs = (new Finder())->directories()->in($dir->getRealPath())->name('/[Vv]iews$/')
                ->exclude('vendor')->notPath('backend')->ignoreUnreadableDirs();

            $pluginName = $dir->getFilename();
            foreach ($viewDirs as $viewDir) {
                $dirs[$pluginName][] = $viewDir->getRealPath();
            }
        }

        $autoloaderDefinition = new Definition(
            ViewAutoloaderSubscriber::class,
            [
                $dirs,
                new Reference('models'),
                new Reference('events'),
                new Reference('service_container'),
            ]
        );
        $autoloaderDefinition->addTag('shopware.event_subscriber');

        $this->containerBuilder->setDefinition(
            'neti_foundation.backports.view_autoloader_suscriber',
            $autoloaderDefinition
        );
    }
}