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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_campaigns_component")
 */
class Component extends ModelEntity
{
    /**
     * INVERSE SIDE
     * Contains all the assigned \SwagNewsletter\Models\Field models.
     * Each component has a field configuration to configure the component data over the
     * backend module. For example: The "Article" component has an "id" field
     * with xtype: 'emotion-article-search' (the shopware article suggest search with a individual configuration for the
     * backend module) to configure which article has to been displayed.
     *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SwagNewsletter\Models\Field", mappedBy="component", orphanRemoval=true, cascade={"persist"})
     */
    protected $fields;

    /**
     * Unique identifier field of the grid model.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Contains the name of the grid which can be configured in the
     * backend emotion module.
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="convert_function", type="string", length=255, nullable=true)
     */
    private $convertFunction;

    /**
     * Contains the component description which displayed in the backend
     * module of
     *
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * Contains the template file which used to display the component data.
     *
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255, nullable=false)
     */
    private $template;

    /**
     * Contains the css class for the component
     *
     * @var string
     *
     * @ORM\Column(name="cls", type="string", length=255, nullable=false)
     */
    private $cls;

    /**
     * The xType for the backend module.
     *
     * @var string
     *
     * @ORM\Column(name="x_type", type="string", length=255, nullable=false)
     */
    private $xType;

    /**
     * Contains the plugin id which added this component to the library
     *
     * @var int
     *
     * @ORM\Column(name="pluginID", type="integer", nullable=true)
     */
    private $pluginId;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * Contains all the assigned \SwagNewsletter\Models\Field models.
     * Each component has a field configuration to configure the component data over the
     * backend module. For example: The "Article" component has an "id" field
     * with xtype: 'emotion-article-search' (the shopware article suggest search with a individual configuration for the
     * backend module) to configure which article has to been displayed.
     *
     * @return ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Contains all the assigned \SwagNewsletter\Models\Field models.
     * Each component has a field configuration to configure the component data over the
     * backend module. For example: The "Article" component has an "id" field
     * with xtype: 'emotion-article-search' (the shopware article suggest search with a individual configuration for the
     * backend module) to configure which product has to been displayed.
     *
     * @param ArrayCollection|array|null $fields
     *
     * @return ModelEntity
     */
    public function setFields($fields)
    {
        return $this->setOneToMany($fields, Field::class, 'fields', 'component');
    }

    /**
     * Unique identifier field of the grid model.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Contains the name of the grid which can be configured in the
     * backend emotion module.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Contains the name of the grid which can be configured in the
     * backend emotion module.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Contains the component description which displayed in the backend
     * module of
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Contains the component description which displayed in the backend
     * module of
     *
     * @param string $description
     *
     * @return Component
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Contains the template file which used to display the component data.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Contains the template file which used to display the component data.
     *
     * @param string $template
     *
     * @return Component
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getCls()
    {
        return $this->cls;
    }

    /**
     * @param string $cls
     *
     * @return Component
     */
    public function setCls($cls)
    {
        $this->cls = $cls;

        return $this;
    }

    /**
     * @return int
     */
    public function getPluginId()
    {
        return $this->pluginId;
    }

    /**
     * @param int $pluginId
     *
     * @return Component
     */
    public function setPluginId($pluginId)
    {
        $this->pluginId = $pluginId;

        return $this;
    }

    /**
     * @return string
     */
    public function getXType()
    {
        return $this->xType;
    }

    /**
     * @param string $xType
     *
     * @return Component
     */
    public function setXType($xType)
    {
        $this->xType = $xType;

        return $this;
    }

    /**
     * @return string
     */
    public function getConvertFunction()
    {
        return $this->convertFunction;
    }

    /**
     * @param string $convertFunction
     */
    public function setConvertFunction($convertFunction)
    {
        $this->convertFunction = $convertFunction;
    }

    /**
     * Generally function to create a new custom newsletter component field.
     *
     * @param array $data
     *
     * @return Field
     */
    public function createField(array $data)
    {
        $data += [
            'fieldLabel' => '',
            'valueType' => '',
            'store' => '',
            'supportText' => '',
            'helpTitle' => '',
            'helpText' => '',
            'defaultValue' => '',
            'displayField' => '',
            'valueField' => '',
            'allowBlank' => false,
        ];

        $field = new Field();
        $field->fromArray($data);

        $field->setComponent($this);
        $this->fields->add($field);

        return $field;
    }

    /**
     * Creates a checkbox field for the passed newsletter component widget.
     *
     * Creates a Ext.form.field.Checkbox element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Checkbox
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createCheckboxField(array $options)
    {
        $options += [
            'xtype' => 'checkboxfield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.ComboBox element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.ComboBox
     *
     * This field type supports the following parameters which can be set
     * as options array value:
     *  - name
     *  - fieldLabel
     *  - allowBlank
     *  - store
     *  - displayField
     *  - valueField
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $store              Required; Store class which used for the combo class
     *     @var string $displayField       Required; Field name of the model which displays as text
     *     @var string $valueField         Required; Identifier field of the combo box
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createComboBoxField(array $options)
    {
        $options += [
            'xtype' => 'combobox',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.Date element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Date
     *
     * This field type supports the following parameters which can be set
     * as options array value:
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createDateField(array $options)
    {
        $options += [
            'xtype' => 'datefield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.Display element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Display
     *
     * This field type supports the following parameters which can be set
     * as options array value:
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createDisplayField(array $options)
    {
        $options += [
            'xtype' => 'displayfield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.Hidden element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Hidden
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createHiddenField(array $options)
    {
        $options += [
            'xtype' => 'hiddenfield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.HtmlEditor element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.HtmlEditor
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createHtmlEditorField(array $options)
    {
        $options += [
            'xtype' => 'htmleditor',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.Number element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Number
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createNumberField(array $options)
    {
        $options += [
            'xtype' => 'numberfield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.Radio element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Radio
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createRadioField(array $options)
    {
        $options += [
            'xtype' => 'radiofield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.Text element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Text
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createTextField(array $options)
    {
        $options += [
            'xtype' => 'textfield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.TextArea element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.TextArea
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createTextAreaField(array $options)
    {
        $options += [
            'xtype' => 'textareafield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a Ext.form.field.Time element.
     * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.field.Time
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createTimeField(array $options)
    {
        $options += [
            'xtype' => 'timefield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a code mirror component field.
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createCodeMirrorField(array $options)
    {
        $options += [
            'xtype' => 'codemirrorfield',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a tiny mce component field.
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createTinyMceField(array $options)
    {
        $options += [
            'xtype' => 'tinymce',
        ];

        return $this->createField($options);
    }

    /**
     * Creates a media selection component field.
     *
     * @param array $options {
     *
     *     @var string $name               Required; Logical name of the component field
     *     @var string $fieldLabel         optional; Ext JS form field label
     *     @var string $allowBlank         Optional; Defines if the value can contains null
     * }
     *
     * @return Field
     */
    public function createMediaSelectionField(array $options)
    {
        $options += [
            'xtype' => 'mediaselectionfield',
        ];

        return $this->createField($options);
    }
}
