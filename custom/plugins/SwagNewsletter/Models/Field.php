<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagNewsletter\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_campaigns_component_field")
 */
class Field extends ModelEntity
{
    /**
     * Unique identifier field of the element model.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Id of the associated \SwagNewsletter\Models\Component
     * which will be displayed in the shopware backend component library.
     *
     * @var int
     *
     * @ORM\Column(name="componentID", type="integer", nullable=false)
     */
    private $componentId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="field_label", type="string", length=255, nullable=false)
     */
    private $fieldLabel;

    /**
     * The xType for the backend module.
     *
     * @var string
     *
     * @ORM\Column(name="x_type", type="string", length=255, nullable=false)
     */
    private $xType;

    /**
     * The valueType for the database
     *
     * @var string
     *
     * @ORM\Column(name="value_type", type="string", length=255, nullable=false)
     */
    private $valueType = '';

    /**
     * Contains the support text for the extJs field.
     *
     * @var string
     *
     * @ORM\Column(name="support_text", type="string", length=255, nullable=false)
     */
    private $supportText;

    /**
     * Contains the store name for a component field.
     *
     * @var string
     *
     * @ORM\Column(name="store", type="string", length=255, nullable=false)
     */
    private $store = '';

    /**
     * Contains the field name which used as display for a combo box field
     *
     * @var string
     *
     * @ORM\Column(name="display_field", type="string", length=255, nullable=false)
     */
    private $displayField = '';

    /**
     * Contains the field name which used as value for a combo box field
     *
     * @var string
     *
     * @ORM\Column(name="value_field", type="string", length=255, nullable=false)
     */
    private $valueField = '';

    /**
     * Contains the default-value for the field
     *
     * @var string
     *
     * @ORM\Column(name="default_value", type="string", length=255, nullable=false)
     */
    private $defaultValue = '';

    /**
     * Could this field be let unfilled
     *
     * @var int
     *
     * @ORM\Column(name="allow_blank", type="integer", length=1, nullable=false)
     */
    private $allowBlank;

    /**
     * Contains the help title for the extJs field.
     *
     * @var string
     *
     * @ORM\Column(name="help_title", type="string", length=255, nullable=false)
     */
    private $helpTitle = '';

    /**
     * Contains the help title for the extJs field.
     *
     * @var string
     *
     * @ORM\Column(name="help_text", type="text",  nullable=false)
     */
    private $helpText = '';

    /**
     * Contains the assigned \SwagNewsletter\Models\Component
     * which can be configured in the backend emotion module.
     * The assigned library component contains the data definition for the grid element.
     *
     * @var Component
     *
     * @ORM\ManyToOne(targetEntity="\SwagNewsletter\Models\Component", inversedBy="fields")
     * @ORM\JoinColumn(name="componentID", referencedColumnName="id")
     */
    private $component;

    /**
     * Unique identifier field of the element model.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Id of the associated \SwagNewsletter\Models\Component
     * which will be displayed in the shopware backend component library.
     *
     * @return int
     */
    public function getComponentId()
    {
        return $this->componentId;
    }

    /**
     * Id of the associated \SwagNewsletter\Models\Component
     * which will be displayed in the shopware backend component library.
     *
     * @param int $componentId
     *
     * @return Field
     */
    public function setComponentId($componentId)
    {
        $this->componentId = $componentId;

        return $this;
    }

    /**
     * The xType for the backend module.
     *
     * @return string
     */
    public function getXType()
    {
        return $this->xType;
    }

    /**
     * The xType for the backend module.
     *
     * @param string $xType
     *
     * @return Field
     */
    public function setXType($xType)
    {
        $this->xType = $xType;

        return $this;
    }

    /**
     * Contains the support text for the extJs field.
     *
     * @return string
     */
    public function getSupportText()
    {
        return $this->supportText;
    }

    /**
     * Contains the support text for the extJs field.
     *
     * @param string $supportText
     *
     * @return Field
     */
    public function setSupportText($supportText)
    {
        $this->supportText = $supportText;

        return $this;
    }

    /**
     * Contains the help title for the extJs field.
     *
     * @return string
     */
    public function getHelpTitle()
    {
        return $this->helpTitle;
    }

    /**
     * Contains the help title for the extJs field.
     *
     * @param string $helpTitle
     *
     * @return Field
     */
    public function setHelpTitle($helpTitle)
    {
        $this->helpTitle = $helpTitle;

        return $this;
    }

    /**
     * Contains the help title for the extJs field.
     *
     * @return string
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    /**
     * Contains the help title for the extJs field.
     *
     * @param string $helpText
     *
     * @return Field
     */
    public function setHelpText($helpText)
    {
        $this->helpText = $helpText;

        return $this;
    }

    /**
     * Contains the assigned \SwagNewsletter\Models\Component
     * which can be configured in the backend emotion module.
     * The assigned library component contains the data definition for the grid element.
     *
     * @return Component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * Contains the assigned \SwagNewsletter\Models\Component
     * which can be configured in the backend emotion module.
     * The assigned library component contains the data definition for the grid element.
     *
     * @param Component $component
     *
     * @return Field
     */
    public function setComponent($component)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Field
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldLabel()
    {
        return $this->fieldLabel;
    }

    /**
     * @param string $fieldLabel
     *
     * @return Field
     */
    public function setFieldLabel($fieldLabel)
    {
        $this->fieldLabel = $fieldLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * @param string $valueType
     *
     * @return Field
     */
    public function setValueType($valueType)
    {
        $this->valueType = $valueType;

        return $this;
    }

    /**
     * @return string
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param string $store
     *
     * @return Field
     */
    public function setStore($store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayField()
    {
        return $this->displayField;
    }

    /**
     * @param string $displayField
     *
     * @return Field
     */
    public function setDisplayField($displayField)
    {
        $this->displayField = $displayField;

        return $this;
    }

    /**
     * @return string
     */
    public function getValueField()
    {
        return $this->valueField;
    }

    /**
     * @param string $valueField
     *
     * @return Field
     */
    public function setValueField($valueField)
    {
        $this->valueField = $valueField;

        return $this;
    }

    /**
     * @param int $allowBlank
     *
     * @return Field
     */
    public function setAllowBlank($allowBlank)
    {
        $this->allowBlank = $allowBlank;

        return $this;
    }

    /**
     * @return int
     */
    public function getAllowBlank()
    {
        return $this->allowBlank;
    }

    /**
     * @param string $defaultValue
     *
     * @return Field
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
