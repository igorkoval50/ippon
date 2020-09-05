<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile\Attribute;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Data
 *
 * @package NetiFoundation\Struct\PluginConfigFile\Attribute
 */
class Data extends AbstractClass
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @var Translation
     */
    protected $label;

    /**
     * @var Translation
     */
    protected $helpText;

    /**
     * @var Translation
     */
    protected $supportText;

    /**
     * @var bool
     */
    protected $translatable = false;

    /**
     * @var bool
     */
    protected $displayInBackend = false;

    /**
     * @var bool
     */
    protected $custom = false;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var string
     */
    protected $arrayStore = null;

    /**
     * Gets the value of arrayStore from the record
     *
     * @return string
     */
    public function getArrayStore()
    {
        return $this->arrayStore;
    }

    /**
     * Sets the Value to arrayStore in the record
     *
     * @param string $arrayStore
     *
     * @return self
     */
    public function setArrayStore($arrayStore)
    {
        if ($arrayStore) {
            $this->arrayStore = $arrayStore;
        } else {
            $this->arrayStore = null;
        }

        return $this;
    }

    /**
     * Gets the value of position from the record
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the Value to position in the record
     *
     * @param int $position
     *
     * @return self
     */
    public function setPosition($position)
    {
        if ($position) {
            $this->position = $position;
        } else {
            $this->position = null;
        }

        return $this;
    }

    /**
     * Gets the value of custom from the record
     *
     * @return boolean
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * Sets the Value to custom in the record
     *
     * @param boolean $custom
     *
     * @return self
     */
    public function setCustom($custom)
    {
        $this->custom = (boolean) $custom;

        return $this;
    }

    /**
     * Gets the value of displayInBackend from the record
     *
     * @return boolean
     */
    public function getDisplayInBackend()
    {
        return $this->displayInBackend;
    }

    /**
     * Sets the Value to displayInBackend in the record
     *
     * @param boolean $displayInBackend
     *
     * @return self
     */
    public function setDisplayInBackend($displayInBackend)
    {
        $this->displayInBackend = (boolean) $displayInBackend;

        return $this;
    }

    /**
     * Gets the value of translatable from the record
     *
     * @return boolean
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * Sets the Value to translatable in the record
     *
     * @param boolean $translatable
     *
     * @return self
     */
    public function setTranslatable($translatable)
    {
        $this->translatable = (boolean) $translatable;

        return $this;
    }

    /**
     * Gets the value of supportText from the record
     *
     * @return Translation|null
     */
    public function getSupportText()
    {
        return $this->supportText;
    }

    /**
     * Sets the Value to supportText in the record
     *
     * @param string $supportText
     *
     * @return self
     */
    public function setSupportText($supportText)
    {
        if ($supportText) {
            $this->supportText = new Translation($supportText, false);
        } else {
            $this->supportText = null;
        }

        return $this;
    }

    /**
     * Gets the value of helpText from the record
     *
     * @return Translation|null
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    /**
     * Sets the Value to helpText in the record
     *
     * @param string $helpText
     *
     * @return self
     */
    public function setHelpText($helpText)
    {
        if ($helpText) {
            $this->helpText = new Translation($helpText, false);
        } else {
            $this->helpText = null;
        }

        return $this;
    }

    /**
     * Gets the value of label from the record
     *
     * @return Translation|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the Value to label in the record
     *
     * @param string $label
     *
     * @return self
     */
    public function setLabel($label)
    {
        if ($label) {
            $this->label = new Translation($label, false);
        } else {
            $this->label = null;
        }

        return $this;
    }

    /**
     * Gets the value of entity from the record
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sets the Value to entity in the record
     *
     * @param string $entity
     *
     * @return self
     */
    public function setEntity($entity)
    {
        $this->entity = (string) $entity;

        return $this;
    }
}
