<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     sbrueggenolte
 */

namespace NetiFoundation\Controllers\Backend;

use Enlight_Controller_ActionEventArgs;
use Enlight_Controller_Plugins_Json_Bootstrap;
use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use ReflectionClass;
use Shopware\Components\ContainerAwareEventManager;
use Shopware_Components_Convert_Csv;

/**
 * Class AbstractBackendApplicationController
 *
 * @package NetiFoundation\Controllers\Backend
 */
abstract class AbstractBackendApplicationController extends \Shopware_Controllers_Backend_Application
{
    /**
     * @var string
     */
    protected $shortClassName;

    /**
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (in_array($this->Request()->getActionName(), array('export'))) {
            $plugins = $this->Front()->Plugins();
            /**
             * @var Enlight_Controller_Plugins_ViewRenderer_Bootstrap $viewRenderer
             * @var Enlight_Controller_Plugins_Json_Bootstrap         $json
             */
            $viewRenderer = $plugins->get('ViewRenderer');
            $json         = $plugins->get('Json');
            if ($viewRenderer) {
                $viewRenderer->setNoRender(true);
            }

            if ($json) {
                $json->setRenderer(false);
            }
        }
    }

    /**
     *
     */
    public function exportAction()
    {
        $selection = $this->Request()->getParam('selection', array());
        $exportAs  = $this->Request()->getParam('exportAs', 'xls');
        if (is_string($selection)) {
            $selection = json_decode($selection, true);
        }

        $data = $this->getExportList(
            $this->Request()->getParam('start', 0),
            $this->Request()->getParam('limit', 20),
            $this->Request()->getParam('sort', array()),
            $this->Request()->getParam('filter', array()),
            $this->Request()->getParam('toExport', 'currentPage'),
            $selection,
            $this->Request()->getParams()
        );

        $metaData = $this->getManager()->getClassMetadata($this->model);
        array_walk($data, function (&$item) use ($metaData) {
            return array_walk($item, function (&$value, $key) use ($metaData) {
                $value = $this->exportDataFilter($value, $key, $metaData);
            });
        });
        reset($data);

        if (isset($data[0])) {
            $data[0] = array_combine(
                $this->getExportHeaderColumns($data),
                $data[0]
            );
        }

        switch ($exportAs) {
            case 'xls':
                $this->exportAsXls($data);
                break;

            case 'csv':
                $this->exportAsCsv($data);
                break;
        }
    }

    /**
     * @param mixed                               $value
     * @param string                              $columnName
     * @param \Doctrine\ORM\Mapping\ClassMetadata $metaData
     *
     * @return mixed
     */
    protected function exportDataFilter($value, $columnName, \Doctrine\ORM\Mapping\ClassMetadata $metaData)
    {

        if ($value instanceof \DateTime) {
            $mapping = $metaData->getFieldMapping($columnName);
            switch ($mapping['type']) {
                case 'datetime':
                    $format = 'd.m.Y H:i:s';
                    break;

                default:
                    $format = 'd.m.Y';
                    break;
            }

            $value = $value->format($format);
        }

        return $value;
    }

    /**
     * @return string|void
     */
    protected function getShortClassName()
    {
        if (! $this->shortClassName) {
            $reflection           = new ReflectionClass($this);
            $this->shortClassName = $reflection->getShortName();
            $isProxy              = $reflection->implementsInterface('Enlight_Hook_Proxy');

            if ($isProxy) {
                $this->shortClassName = $reflection->getParentClass()->getShortName();
            }
        }

        return $this->shortClassName;
    }

    /**
     * @param string|null $shortName
     *
     * @return string
     */
    protected function getExportFileName($shortName = null)
    {
        if (! $shortName) {
            $shortName = $this->container->get('neti_foundation.service.helper')->decamelize(
                str_replace('Shopware_Controllers_Backend_', '', $this->getShortClassName())
            );
        }

        return sprintf(
            '%s_%s',
            date('Y-m-d'),
            $shortName
        );
    }

    /**
     * @param array $data
     * @param array $headerColumns
     */
    protected function exportAsXls(array $data)
    {
        $outputFileName = $this->getExportFileName();

        $this->Response()->setHeader(
            'Content-Disposition',
            sprintf('attachment; filename="%s"', $outputFileName)
        );
        $this->Response()->setHeader('Content-Type', 'application/vnd.ms-excel;charset=UTF-8');
        $this->Response()->setHeader('Content-Transfer-Encoding', 'binary');

        /** @var \NetiPhpExcel\Service\PhpExcel $phpExcel */
        $phpExcel = $this->container->get('neti_php_excel.php_excel');
        $phpExcel->exportFunction(
            $data,
            $outputFileName,
            $phpExcel::FORMAT_EXCEL
        );
    }

    /**
     * @param array $data
     * @param array $headerColumns
     */
    protected function exportAsCsv(array $data)
    {
        $outputFileName = $this->getExportFileName();

        $this->Response()->setHeader(
            'Content-Disposition',
            sprintf('attachment; filename="%s"', $outputFileName)
        );
        $this->Response()->setHeader('Content-Type', 'text/x-comma-separated-values;charset=utf-8');
        $this->Response()->setHeader('Content-Transfer-Encoding', 'binary');

        /** @var \NetiPhpExcel\Service\PhpExcel $phpExcel */
        $phpExcel = $this->container->get('neti_php_excel.php_excel');
        $phpExcel->exportFunction(
            $data,
            $outputFileName,
            $phpExcel::FORMAT_CSV
        );
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getExportHeaderColumns(array $data)
    {
        return array_keys(reset($data));
    }

    /**
     * @param int    $offset
     * @param int    $limit
     * @param array  $sort
     * @param array  $filter
     * @param string $toExport
     * @param array  $selection
     * @param array  $wholeParams
     *
     * @return array
     */
    protected function getExportList($offset,
                                     $limit,
                                     $sort = array(),
                                     $filter = array(),
                                     $toExport = 'currentPage',
                                     array $selection = array(),
                                     array $wholeParams = array()
    ) {
        $builder = $this->getListQuery();

        if ('currentPage' === $toExport) {
            $builder->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        $filter = $this->getFilterConditions(
            $filter,
            $this->model,
            $this->alias,
            $this->filterFields
        );

        $sort = $this->getSortConditions(
            $sort,
            $this->model,
            $this->alias,
            $this->sortFields
        );

        if (! empty($sort)) {
            $builder->addOrderBy($sort);
        }

        if (! empty($filter)) {
            $builder->addFilter($filter);
        }

        if (! empty($selection)) {
            $metaData            = $this->getManager()->getClassMetadata($this->model);
            $identifierFieldName = reset($metaData->getIdentifierFieldNames());
            $builder->andWhere(
                $builder->expr()->in(
                    $this->alias . '.' . $identifierFieldName,
                    array_column($selection, $identifierFieldName)
                )
            );
        }

        $this->injectEvent('GetExportListAfterAddFilter', new Enlight_Controller_ActionEventArgs(array(
            'subject'     => $this,
            'request'     => $this->Request(),
            'response'    => $this->Response(),
            'builder'     => $builder,
            'offset'      => $offset,
            'limit'       => $limit,
            'sort'        => $sort,
            'filter'      => $filter,
            'wholeParams' => $wholeParams,
            'alias'       => $this->alias
        )));

        $paginator = $this->getQueryPaginator($builder);

        return $paginator->getIterator()->getArrayCopy();
    }

    /**
     * @param int   $offset
     * @param int   $limit
     * @param array $sort
     * @param array $filter
     * @param array $wholeParams
     *
     * @return array
     */
    protected function getList($offset, $limit, $sort = array(), $filter = array(), array $wholeParams = array())
    {
        $builder = $this->getListQuery();
        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $filter = $this->getFilterConditions(
            $filter,
            $this->model,
            $this->alias,
            $this->filterFields
        );

        $sort = $this->getSortConditions(
            $sort,
            $this->model,
            $this->alias,
            $this->sortFields
        );

        if (! empty($sort)) {
            $builder->addOrderBy($sort);
        }

        if (! empty($filter)) {
            $builder->addFilter($filter);
        }

        $this->injectEvent('GetListAfterAddFilter', new Enlight_Controller_ActionEventArgs(array(
            'subject'     => $this,
            'request'     => $this->Request(),
            'response'    => $this->Response(),
            'builder'     => $builder,
            'offset'      => $offset,
            'limit'       => $limit,
            'sort'        => $sort,
            'filter'      => $filter,
            'wholeParams' => $wholeParams,
            'alias'       => $this->alias
        )));

        $paginator = $this->getQueryPaginator($builder);
        $data      = $paginator->getIterator()->getArrayCopy();
        $count     = $paginator->count();

        return array('success' => true, 'data' => $data, 'total' => $count);
    }

    /**
     * @param string                             $eventName
     * @param Enlight_Controller_ActionEventArgs $args
     */
    protected function injectEvent($eventName, Enlight_Controller_ActionEventArgs $args)
    {
        /**
         * @var ContainerAwareEventManager $eventManager
         */
        $eventManager = $this->container->get('events');
        $reflection   = new ReflectionClass($this);
        $className    = $reflection->getShortName();
        $isProxy      = $reflection->implementsInterface('Enlight_Hook_Proxy');

        if ($isProxy) {
            $className = $reflection->getParentClass()->getShortName();
        }

        $eventManager->notify(
            sprintf('%s_%s', $className, $eventName),
            $args
        );
    }
}
