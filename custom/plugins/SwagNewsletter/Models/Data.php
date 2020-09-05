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
use Shopware\Models\Newsletter\Newsletter as NewsletterModel;
use SwagNewsletter\Models\Element as ElementModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_plugin_swag_newsletter_element_value")
 */
class Data extends ModelEntity
{
    /**
     * OWNING SIDE
     * Contains the assigned \Models\Newsletter\Newsletter
     * which can be configured in the backend newsletter module.
     * The assigned newsletter contains the definition of the newsletter elements.
     * The element model is the owning side (primary key in this table) of the association between
     * newsletter and grid elements.
     *
     * @var NewsletterModel
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Newsletter\Newsletter", inversedBy="elements", cascade={"persist"})
     * @ORM\JoinColumn(name="newsletterID", referencedColumnName="id")
     */
    protected $newsletter;

    /**
     * @var ElementModel
     *
     * @ORM\ManyToOne(targetEntity="\SwagNewsletter\Models\Element", inversedBy="data")
     * @ORM\JoinColumn(name="elementID", referencedColumnName="id")
     */
    protected $element;

    /**
     * @var Component
     *
     * @ORM\OneToOne(targetEntity="\SwagNewsletter\Models\Component")
     * @ORM\JoinColumn(name="componentID", referencedColumnName="id")
     */
    protected $component;

    /**
     * @var Field
     * @ORM\OneToOne(targetEntity="\SwagNewsletter\Models\Field")
     * @ORM\JoinColumn(name="fieldID", referencedColumnName="id")
     */
    protected $field;

    /**
     * Unique identifier field for the shopware emotion.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Contains the id of the newsletter
     *
     * @var int
     *
     * @ORM\Column(name="newsletterID", type="integer", nullable=false)
     */
    private $newsletterId;

    /**
     * Contains the name of the newsletter.
     *
     * @var int
     *
     * @ORM\Column(name="elementID", type="integer", nullable=false)
     */
    private $elementId;

    /**
     * Contains the id of the assigned element component
     *
     * @var int
     *
     * @ORM\Column(name="componentID", type="integer", nullable=false)
     */
    private $componentId;

    /**
     * @var int
     *
     * @ORM\Column(name="fieldID", type="integer", nullable=false)
     */
    private $fieldId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @param int $componentId
     */
    public function setComponentId($componentId)
    {
        $this->componentId = $componentId;
    }

    /**
     * @return int
     */
    public function getComponentId()
    {
        return $this->componentId;
    }

    /**
     * @param string $elementId
     */
    public function setElementId($elementId)
    {
        $this->elementId = $elementId;
    }

    /**
     * @return string
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * @param int $fieldId
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;
    }

    /**
     * @return int
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return Component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * @param Component $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }

    /**
     * @return ElementModel
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param ElementModel $element
     */
    public function setElement($element)
    {
        $this->element = $element;
    }

    /**
     * @return string
     */
    public function getNewsletterId()
    {
        return $this->newsletterId;
    }

    /**
     * @param string $newsletterId
     */
    public function setNewsletterId($newsletterId)
    {
        $this->newsletterId = $newsletterId;
    }
}
