<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Translation
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class Translation extends AbstractClass
{
    /**
     * @var string
     */
    protected $de;

    /**
     * @var string
     */
    protected $en;

    /**
     * @param array $data
     * @param bool  $camelize
     */
    public function __construct(array $data, $camelize = true)
    {
        if (isset($data['de_DE'])) {
            $data['de'] = $data['de_DE'];
            unset($data['de_DE']);
        }

        if (isset($data['en_GB'])) {
            $data['en'] = $data['en_GB'];
            unset($data['en_GB']);
        }

        parent::__construct($data, $camelize);
    }

    /**
     * Gets the value of de from the record
     *
     * @return string
     */
    public function getDe()
    {
        return $this->de;
    }

    /**
     * Sets the Value to de in the record
     *
     * @param string $de
     *
     * @return self
     */
    public function setDe($de)
    {
        $this->de = (string) $de;

        return $this;
    }

    /**
     * Gets the value of en from the record
     *
     * @return string
     */
    public function getEn()
    {
        return $this->en;
    }

    /**
     * Sets the Value to en in the record
     *
     * @param string $en
     *
     * @return self
     */
    public function setEn($en)
    {
        $this->en = (string) $en;

        return $this;
    }
}
