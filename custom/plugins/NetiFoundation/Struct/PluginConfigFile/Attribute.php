<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Attribute
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class Attribute extends AbstractClass
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var string
     */
    protected $prefix = 'neti';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Attribute\Data
     */
    protected $data;

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
     * Gets the value of suffix from the record
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Sets the Value to suffix in the record
     *
     * @param string $suffix
     *
     * @return self
     */
    public function setSuffix($suffix)
    {
        $this->suffix = (string) $suffix;

        return $this;
    }

    /**
     * Gets the value of prefix from the record
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the Value to prefix in the record
     *
     * @param string $prefix
     *
     * @return self
     */
    public function setPrefix($prefix)
    {
        $this->prefix = (string) $prefix;

        return $this;
    }

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
        $this->type = (string) $type;

        return $this;
    }

    /**
     * Gets the value of data from the record
     *
     * @return Attribute\Data|array
     */
    public function getData()
    {
        return $this->data ?: [];
    }

    /**
     * Sets the Value to data in the record
     *
     * @param array $data
     *
     * @return self
     */
    public function setData($data)
    {
        if ($data) {
            $this->data = new Attribute\Data($data);
        } else {
            $this->data = null;
        }

        return $this;
    }
}
