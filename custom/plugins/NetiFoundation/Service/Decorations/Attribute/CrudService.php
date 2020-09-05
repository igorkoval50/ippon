<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\Decorations\Attribute;

use Shopware\Bundle\AttributeBundle\Service\ConfigurationStruct;
use Shopware\Bundle\AttributeBundle\Service\CrudService as SwCrudService;
use Shopware\Bundle\AttributeBundle\Service\SchemaOperator;
use Shopware\Bundle\AttributeBundle\Service\TableMapping;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Model\ModelManager;

/**
 * class CrudService
 * @package Shopware\Bundle\AttributeBundle\Service
 */
class CrudService extends SwCrudService
{
    /**
     * @var ModelManager
     */
    private $entityManager;

    /**
     * @var SchemaOperator
     */
    private $schemaOperator;

    /**
     * @var TableMapping
     */
    private $tableMapping;

    /**
     * @var TypeMapping
     */
    private $typeMapping;

    /**
     * @var SwCrudService
     */
    private $coreService;

    /**
     * @var \Enlight_Template_Manager
     */
    private $templateManager;

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * CrudService constructor.
     *
     * @param ModelManager              $entityManager
     * @param SchemaOperator            $schemaOperator
     * @param TableMapping              $tableMapping
     * @param TypeMapping               $typeMapping
     * @param SwCrudService             $coreService
     * @param \Enlight_Template_Manager $templateManager
     */
    public function __construct(
        ModelManager $entityManager,
        SchemaOperator $schemaOperator,
        TableMapping $tableMapping,
        TypeMapping $typeMapping,
        SwCrudService $coreService,
        \Enlight_Template_Manager $templateManager
    ) {
        $this->entityManager = $entityManager;
        $this->schemaOperator = $schemaOperator;
        $this->tableMapping = $tableMapping;
        $this->typeMapping = $typeMapping;
        $this->coreService = $coreService;
        $this->templateManager = $templateManager;
    }

    /**
     * @param string $table
     * @param string $column
     * @param bool   $updateDependingTables
     *
     * @throws \Exception
     */
    public function delete($table, $column, $updateDependingTables = false)
    {
        $this->coreService->delete($table, $column, $updateDependingTables);
    }

    /**
     * @param string                $table
     * @param string                $columnName
     * @param string                $unifiedType
     * @param array                 $data
     * @param null                  $newColumnName
     * @param bool                  $updateDependingTables
     * @param null|string|int|float $defaultValue
     */
    public function update(
        $table,
        $columnName,
        $unifiedType,
        array $data = [],
        $newColumnName = null,
        $updateDependingTables = false,
        $defaultValue = null
    )
    {
        $this->coreService->update(
            $table,
            $columnName,
            $unifiedType,
            $data,
            $newColumnName,
            $updateDependingTables,
            $defaultValue
        );
    }

    /**
     * @param string $table
     * @param string $columnName
     *
     * @return ConfigurationStruct|null
     */
    public function get($table, $columnName)
    {
        return $this->coreService->get($table, $columnName);
    }

    /**
     * @param string $table
     *
     * @return ConfigurationStruct[]
     */
    public function getList($table)
    {
        $list = $this->coreService->getList($table);

        foreach ($list as $config) {
            if (false !== strpos($config->getLabel(), '{s namespace=')) {
                $config->setLabel($this->templateManager->fetch('snippet:string:' . $config->getLabel()));
            }

            if (false !== strpos($config->getHelpText(), '{s namespace=')) {
                $config->setHelpText($this->templateManager->fetch('snippet:string:' . $config->getHelpText()));
            }

            if (false !== strpos($config->getSupportText(), '{s namespace=')) {
                $config->setSupportText($this->templateManager->fetch('snippet:string:' . $config->getSupportText()));
            }
        }

        return $list;
    }
}
