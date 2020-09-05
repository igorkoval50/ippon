<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Backports;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

class BackportHelper
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * BackportHelper constructor.
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @param $swVersion
     */
    public function processBackports($swVersion)
    {
        $finder = Finder::create()->directories()->in(__DIR__ . '/../../')->depth(0)->name('Neti*');

        (new ViewAutoloaderBackport($this->containerBuilder))->backportViewAutoloader($finder);
    }
}