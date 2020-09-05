<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use Doctrine\DBAL\Connection;
use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile\Attribute;
use NetiFoundation\Struct\PluginConfigFile\Attribute\Data;
use NetiFoundation\Struct\PluginConfigFile\Attribute\Translation;
use Shopware\Bundle\AttributeBundle\Service\CrudService as AttributeCrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping as AttributeTypeMappingService;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Snippet\Writer\DatabaseWriter;

/**
 * Class Attributes
 *
 * @package NetiFoundation\Service\PluginManager
 *
 * @TODO    might wanna migrate this to the shopware attribute service
 */
class Attributes implements AttributesInterface
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
     * @var AttributeCrudService
     */
    protected $attributeCrudService;

    /**
     * @var AttributeTypeMappingService
     */
    protected $attributeTypeMappingService;

    /**
     * @var LoggingServiceInterface
     */
    protected $loggingService;

    /**
     * @param ModelManager                $em
     * @param AttributeCrudService        $attributeCrudService
     * @param AttributeTypeMappingService $attributeTypeMappingService
     * @param Connection                  $connection
     * @param LoggingServiceInterface     $loggingService
     */
    public function __construct(
        ModelManager $em,
        AttributeCrudService $attributeCrudService,
        AttributeTypeMappingService $attributeTypeMappingService,
        Connection $connection,
        LoggingServiceInterface $loggingService
    ) {
        $this->em                          = $em;
        $this->attributeCrudService        = $attributeCrudService;
        $this->attributeTypeMappingService = $attributeTypeMappingService;
        $this->connection                  = $connection;
        $this->loggingService              = $loggingService;
    }

    /**
     * @param Plugin      $plugin
     * @param Attribute[] $values
     *
     * @throws \Exception
     */
    public function createAttributes(Plugin $plugin, array $values)
    {
        $this->updateAttributes($plugin, $values);
    }

    /**
     * @param Plugin      $plugin
     * @param Attribute[] $values
     *
     * @throws \Exception
     */
    public function updateAttributes(Plugin $plugin, array $values)
    {
        $logs      = ['success' => true, 'attributes' => []];
        $exception = null;

        try {
            $this->clearCache();

            $types  = $this->attributeTypeMappingService->getTypes();
            $tables = array();
            foreach ($values as $attribute) {
                $table          = $attribute->getTable();
                $suffix         = $attribute->getSuffix();
                $type           = $attribute->getType();
                $prefix         = $attribute->getPrefix();
                $data           = $attribute->getData();
                $column         = $prefix . '_' . $suffix;
                $tables[$table] = true;

                $log = array(
                    'table'   => $table,
                    'column'  => $column,
                    'success' => false
                );

                if (!isset($types[$type])) {
                    $type = $this->convertToUnifiedType($type);
                }

                if ($data instanceof Data) {
                    $label       = $data->getLabel();
                    $helpText    = $data->getHelpText();
                    $supportText = $data->getSupportText();
                    $data        = $data->toArray();
                    if (!empty($label)) {
                        $data['label'] = $this->translate($label, $table, $column, 'backend/attributes/labels');
                    }

                    if (!empty($helpText)) {
                        $data['helpText'] = $this->translate($helpText, $table, $column, 'backend/attributes/help');
                    }

                    if (!empty($supportText)) {
                        $data['supportText'] = $this->translate($supportText, $table, $column, 'backend/attributes/support');
                    }
                }

                $this->attributeCrudService->update(
                    $table,
                    $column,
                    $type,
                    $data
                );

                $log['success'] = true;

                $logs['attributes'][] = $log;
            }

            if (!empty($tables)) {
                $this->clearCache();
                $this->em->generateAttributeModels(array_keys($tables));
            }
        } catch (\Exception $e) {
            $exception       = $e;
            $logs['success'] = false;
            $logs['message'] = 'Error ' . $e->getCode() . ': ' . $e->getMessage();
        }

        $logs['success'] = true;

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['attributes' => $logs]
        );

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * @param Translation $data
     * @param string      $table
     * @param string      $column
     * @param string      $namespace
     *
     * @return string
     * @throws \Exception
     */
    protected function translate(Translation $data, $table, $column, $namespace = 'backend/attributes/labels')
    {
        $name             = $table . '/' . $column;
        $default          = '';
        $localeRepository = $this->em->getRepository('Shopware\Models\Shop\Locale');
        foreach ($data as $locale => $label) {
            /**
             * @var \Shopware\Models\Shop\Locale $localeModel
             */
            $localeModel    = $localeRepository->findOneBy(['locale' => $locale]);
            $databaseWriter = new DatabaseWriter($this->connection);
            $databaseWriter->setForce(false);
            $databaseWriter->setUpdate(true);

            $databaseWriter->write(
                [$name => $label],
                $namespace,
                $localeModel->getId(),
                1
            );

            if ('en_GB' === $locale) {
                $default = $label;
            }
        }

        return '{s namespace="' . $namespace . '" name="' . $name . '"}' . $default . '{/s}';
    }

    /**
     * @param Plugin      $plugin
     * @param Attribute[] $values
     */
    public function removeAttributes(Plugin $plugin, array $values)
    {
        // Todo: Implement logging... therefore the param $plugin is required

        $this->clearCache();

        $tables = array();
        foreach ($values as $attribute) {
            $table          = $attribute->getTable();
            $suffix         = $attribute->getSuffix();
            $prefix         = $attribute->get('prefix', 'neti');
            $column         = $prefix . '_' . $suffix;
            $tables[$table] = true;

            try {
                $this->attributeCrudService->delete(
                    $table,
                    $column
                );
            } catch (\Exception $e) {
            }
        }

        if (! empty($tables)) {
            $this->clearCache();
            $this->em->generateAttributeModels(array_keys($tables));
        }
    }

    /**
     * Converts a mysql data type to a unified sql type
     * as defined in Shopware\Bundle\AttributeBundle\Service\TypeMapping::$types
     *
     * @param string $type
     *
     * @return string
     */
    private function convertToUnifiedType($type)
    {
        $openingBracket = strpos($type, '(');
        if (false !== $openingBracket) {
            $type = substr($type, 0, $openingBracket);
        }
        switch (strtolower($type)) {
            case 'tinyint':
                return 'boolean';
            case 'int':
                return 'integer';
            case 'varchar':
                return 'string';
            case 'text':
                return 'text';
            case 'mediumtext':
                return 'html';
            case 'double':
                return 'float';
        }

        return $type ?: 'string';
    }

    /**
     *
     */
    protected function clearCache()
    {
        $metaDataCache = $this->em->getConfiguration()->getMetadataCacheImpl();

        if (method_exists($metaDataCache, 'deleteAll')) {
            $metaDataCache->deleteAll();
        }
    }
}
