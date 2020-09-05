<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Backports;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractContainerBuilderBackport
{
    /**
     * @var ContainerBuilder
     */
    protected $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }


    protected function camelToSnakeCase($string)
    {
        return strtolower(ltrim(preg_replace('/[A-Z]/', '_$0', $string), '_'));
    }
}