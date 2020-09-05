<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile\Attribute;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Translation
 *
 * @package NetiFoundation\Struct\PluginConfigFile\Attribute
 */
class Translation extends AbstractClass
{
    /**
     * @var string
     */
    protected $de_DE;

    /**
     * @var string
     */
    protected $en_GB;

    /**
     * Gets the value of de_DE from the record
     *
     * @return string
     */
    public function getDe_DE()
    {
        return $this->de_DE;
    }

    /**
     * Sets the Value to de_DE in the record
     *
     * @param string $de_DE
     *
     * @return self
     */
    public function setDe_DE($de_DE)
    {
        $this->de_DE = (string) $de_DE;

        return $this;
    }

    /**
     * Gets the value of en_GB from the record
     *
     * @return string
     */
    public function getEn_GB()
    {
        return $this->en_GB;
    }

    /**
     * Sets the Value to en_GB in the record
     *
     * @param string $en_GB
     *
     * @return self
     */
    public function setEn_GB($en_GB)
    {
        $this->en_GB = (string) $en_GB;

        return $this;
    }
}
