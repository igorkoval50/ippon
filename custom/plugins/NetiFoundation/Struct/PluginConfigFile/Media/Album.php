<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile\Media;

use NetiFoundation\Struct\AbstractClass;
use NetiFoundation\Struct\PluginConfigFile\Media\Album\Settings;

/**
 * Class Album
 *
 * @package NetiFoundation\Struct\PluginConfigFile\Media
 */
class Album extends AbstractClass
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var int
     */
    protected $position;

    /**
     * Gets the value of icon from the record
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Sets the Value to icon in the record
     *
     * @param string $icon
     *
     * @return self
     */
    public function setIcon($icon)
    {
        $this->icon = (string) $icon;

        return $this;
    }

    /**
     * Gets the value of settings from the record
     *
     * @return Settings|null
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets the Value to settings in the record
     *
     * @param array $settings
     *
     * @return self
     */
    public function setSettings($settings)
    {
        if ($settings) {
            $this->settings = new Settings($settings);
        } else {
            $this->settings = null;
        }

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
        $this->position = (int) $position;

        return $this;
    }
}
