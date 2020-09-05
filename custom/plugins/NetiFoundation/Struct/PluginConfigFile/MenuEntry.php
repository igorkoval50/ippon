<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class MenuEntry
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class MenuEntry extends AbstractClass
{
    /**
     * @var Translation
     */
    protected $label;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $onclick;

    /**
     * @var int
     */
    protected $active;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var array
     */
    protected $parent;

    /**
     * @var MenuEntry[]
     */
    protected $children;

    /**
     * @param array $data
     * @param bool  $camelize
     */
    public function __construct(array $data, $camelize = true)
    {
        if (isset($data['label'])) {
            $labelConfig = $data['label'];
            $label       = [];
            if (! empty($labelConfig)) {
                if (isset($labelConfig['de_DE'])) {
                    $label['de'] = $labelConfig['de_DE'];
                }

                if (isset($labelConfig['en_GB'])) {
                    $label['en'] = $labelConfig['en_GB'];
                }
            }

            $data['label'] = $label;
        }

        if (isset($data['parent']) && ! is_array($data['parent'])) {
            $data['parent'] = [
                'label' => $data['parent']
            ];
        }

        parent::__construct($data, $camelize);
    }

    /**
     * Gets the value of parent from the record
     *
     * @return array
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the Value to parent in the record
     *
     * @param array $parent
     *
     * @return self
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Gets the value of active from the record
     *
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Sets the Value to active in the record
     *
     * @param int $active
     *
     * @return self
     */
    public function setActive($active)
    {
        $this->active = (int) $active;

        return $this;
    }

    /**
     * Gets the value of action from the record
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets the Value to action in the record
     *
     * @param string $action
     *
     * @return self
     */
    public function setAction($action)
    {
        $this->action = (string) $action;

        return $this;
    }

    /**
     * Gets the value of class from the record
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets the Value to class in the record
     *
     * @param string $class
     *
     * @return self
     */
    public function setClass($class)
    {
        $this->class = (string) $class;

        return $this;
    }

    /**
     * Gets the value of controller from the record
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets the Value to controller in the record
     *
     * @param string $controller
     *
     * @return self
     */
    public function setController($controller)
    {
        $this->controller = (string) $controller;

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
     * Gets the value of children from the record
     *
     * @return MenuEntry[]
     */
    public function getChildren()
    {
        $children = array();
        if (is_array($this->children)) {
            foreach ($this->children as $child) {
                $entry = $child->toArray();
                $label = $child->getLabel();
                $name  = $label->getDe();

                $entry['name']  = $name;
                $entry['label'] = $label->toArray();

                $children[] = $entry;
            }
        }

        return $children;
    }

    /**
     * Sets the Value to children in the record
     *
     * @param array $children
     *
     * @return self
     */
    public function setChildren($children)
    {
        $structs = array();
        foreach ($children as $child) {
            $structs[] = new MenuEntry($child);
        }
        $this->children = $structs;

        return $this;
    }

    /**
     * Gets the value of onclick from the record
     *
     * @return string
     */
    public function getOnclick()
    {
        return $this->onclick;
    }

    /**
     * Sets the Value to onclick in the record
     *
     * @param string $onclick
     *
     * @return self
     */
    public function setOnclick($onclick)
    {
        $this->onclick = $onclick;

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
        $this->position = $position;

        return $this;
    }
}
