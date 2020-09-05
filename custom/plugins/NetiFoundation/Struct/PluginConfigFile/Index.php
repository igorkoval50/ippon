<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Index
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class Index extends AbstractClass
{
    /**
     * @var string
     */
    protected $type = 'index';

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $columns;

    /**
     * Gets the value of type from the record
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the Value to type in the record
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        if ($type) {
            $this->type = strtolower($type);
        } else {
            $this->type = 'index';
        }

        return $this;
    }

    /**
     * Gets the value of table from the record
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Sets the Value to table in the record
     *
     * @param string $table
     *
     * @return self
     */
    public function setTable($table)
    {
        $this->table = (string) $table;

        return $this;
    }

    /**
     * Gets the value of columns from the record
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the Value to columns in the record
     *
     * @param array $columns
     *
     * @return self
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }
}
