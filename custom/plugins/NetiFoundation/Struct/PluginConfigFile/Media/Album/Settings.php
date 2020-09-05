<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile\Media\Album;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Settings
 *
 * @package NetiFoundation\Struct\PluginConfigFile\Media\Album
 */
class Settings extends AbstractClass
{
    /**
     * @var integer
     */
    protected $createThumbnails;

    /**
     * @var string
     */
    protected $thumbnailSize;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var bool
     */
    protected $thumbnailHighDpi;

    /**
     * @var int
     */
    protected $thumbnailQuality;

    /**
     * @var int
     */
    protected $thumbnailHighDpiQuality;

    /**
     * Gets the value of createThumbnails from the record
     *
     * @return int
     */
    public function getCreateThumbnails()
    {
        return $this->createThumbnails;
    }

    /**
     * Sets the Value to createThumbnails in the record
     *
     * @param int $createThumbnails
     *
     * @return self
     */
    public function setCreateThumbnails($createThumbnails)
    {
        if ($createThumbnails) {
            $this->createThumbnails = $createThumbnails;
        } else {
            $this->createThumbnails = null;
        }

        return $this;
    }

    /**
     * Gets the value of thumbnailSize from the record
     *
     * @return string
     */
    public function getThumbnailSize()
    {
        return $this->thumbnailSize;
    }

    /**
     * Sets the Value to thumbnailSize in the record
     *
     * @param string $thumbnailSize
     *
     * @return self
     */
    public function setThumbnailSize($thumbnailSize)
    {
        if ($thumbnailSize) {
            $this->thumbnailSize = $thumbnailSize;
        } else {
            $this->thumbnailSize = null;
        }

        return $this;
    }

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
     * Gets the value of thumbnailHighDpi from the record
     *
     * @return boolean
     */
    public function getThumbnailHighDpi()
    {
        return $this->thumbnailHighDpi;
    }

    /**
     * Sets the Value to thumbnailHighDpi in the record
     *
     * @param boolean $thumbnailHighDpi
     *
     * @return self
     */
    public function setThumbnailHighDpi($thumbnailHighDpi)
    {
        $this->thumbnailHighDpi = (boolean) $thumbnailHighDpi;

        return $this;
    }

    /**
     * Gets the value of thumbnailQuality from the record
     *
     * @return int
     */
    public function getThumbnailQuality()
    {
        return $this->thumbnailQuality;
    }

    /**
     * Sets the Value to thumbnailQuality in the record
     *
     * @param int $thumbnailQuality
     *
     * @return self
     */
    public function setThumbnailQuality($thumbnailQuality)
    {
        $this->thumbnailQuality = (int) $thumbnailQuality;

        return $this;
    }

    /**
     * Gets the value of thumbnailHighDpiQuality from the record
     *
     * @return int
     */
    public function getThumbnailHighDpiQuality()
    {
        return $this->thumbnailHighDpiQuality;
    }

    /**
     * Sets the Value to thumbnailHighDpiQuality in the record
     *
     * @param int $thumbnailHighDpiQuality
     *
     * @return self
     */
    public function setThumbnailHighDpiQuality($thumbnailHighDpiQuality)
    {
        $this->thumbnailHighDpiQuality = (int) $thumbnailHighDpiQuality;

        return $this;
    }
}
