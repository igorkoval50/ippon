<?php

declare(strict_types=1);

/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\{Model\ModelManager, Plugin};

/**
 * Class Schema
 *
 * @package NetiFoundation\Service\PluginManager
 */
class Schema implements SchemaInterface
{
    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param ModelManager $em
     * @param Connection   $connection
     */
    public function __construct(ModelManager $em, Connection $connection)
    {
        $this->em         = $em;
        $this->connection = $connection;
    }

    /**
     * @param Plugin $plugin
     * @param array  $models
     */
    public function createSchema(Plugin $plugin, array $models): void
    {
        $this->updateSchema($plugin, $models);
    }

    /**
     * @param Plugin $plugin
     * @param array  $models
     */
    public function updateSchema(Plugin $plugin, array $models): void
    {
        $schemaTool = new SchemaTool($this->em);

        $metaData = \array_map(function ($model) {
            return $this->em->getClassMetadata($model);
        }, $models);

        $schemaTool->updateSchema($metaData, true);
    }

    /**
     * @param Plugin $plugin
     * @param array  $models
     */
    public function removeSchema(Plugin $plugin, array $models): void
    {
        // Todo: Implement logging... therefore the param $plugin is required

        $tool    = new SchemaTool($this->em);
        $classes = [];
        foreach ($models as $className) {
            $classes[] = $this->em->getClassMetadata($className);
        }
        $tool->dropSchema($classes);
    }
}
