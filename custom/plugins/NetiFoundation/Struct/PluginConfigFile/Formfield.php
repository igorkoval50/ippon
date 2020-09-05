<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;
use Shopware\Models\Config\Element;

/**
 * Class Formfield
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class Formfield extends AbstractClass
{
    /**
     * @var int
     */
    protected $scope = Element::SCOPE_SHOP;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var boolean
     */
    protected $isRequired = false;

    /**
     * @var array
     */
    protected $store;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var Translation
     */
    protected $label;

    /**
     * @var Translation
     */
    protected $description;

    /**
     * @param array $data
     * @param bool  $camelize
     */
    public function __construct(array $data, $camelize = true)
    {
        if (! $this->isAssoc($data)) {
            $options     = [];
            $type        = $data[0];
            $name        = $data[1];
            $label       = $data[2];
            $description = $data[3];
            $value       = $data[4];
            $scope       = isset($data[5]) ? $data[5] : Element::SCOPE_SHOP;
            $required    = isset($data[6]) ? $data[6] : false;
            $store       = isset($data[7]) ? $data[7] : null;
            $multiSelect = isset($data[8]) ? $data[8] : null;
            $refresh     = isset($data[9]) ? $data[9] : null;

            if ($multiSelect) {
                $options['multiSelect'] = $multiSelect;
            }

            if ($refresh) {
                $options['refresh'] = $refresh;
            }

            $data = [
                'scope'       => $scope,
                'name'        => $name,
                'value'       => $value,
                'isRequired'  => $required,
                'store'       => $store,
                'type'        => $type,
                'options'     => $options,
                'label'       => $label,
                'description' => $description,
            ];
        }

        parent::__construct($data, $camelize);
    }

    /**
     * Gets the value of scope from the record
     *
     * @return int
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Sets the Value to scope in the record
     *
     * @param int $scope
     *
     * @return self
     */
    public function setScope($scope)
    {
        $this->scope = (int) $scope;

        return $this;
    }

    /**
     * Gets the value of name from the record
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the Value to name in the record
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Gets the value of value from the record
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the Value to value in the record
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets the value of isRequired from the record
     *
     * @return boolean
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Sets the Value to isRequired in the record
     *
     * @param boolean $isRequired
     *
     * @return self
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = (boolean) $isRequired;

        return $this;
    }

    /**
     * Gets the value of store from the record
     *
     * @return array
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Sets the Value to store in the record
     *
     * @param array $store
     *
     * @return self
     */
    public function setStore($store)
    {
        $this->store = $store;

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
     * Gets the value of options from the record
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the Value to options in the record
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options ?: [];

        return $this;
    }

    /**
     * Gets the value of label from the record
     *
     * @return Translation
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the Value to label in the record
     *
     * @param array $label
     *
     * @return self
     */
    public function setLabel($label)
    {
        if ($label) {
            $this->label = new Translation($label);
        } else {
            $this->label = null;
        }

        return $this;
    }

    /**
     * Gets the value of description from the record
     *
     * @return Translation
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the Value to description in the record
     *
     * @param array $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        if ($description) {
            $this->description = new Translation($description);
        } else {
            $this->description = null;
        }

        return $this;
    }
}
