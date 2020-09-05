<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Acl
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class Acl extends AbstractClass
{
    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var array
     */
    protected $privileges;

    /**
     * Gets the value of resourceName from the record
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Sets the Value to resourceName in the record
     *
     * @param string $resourceName
     *
     * @return self
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = (string) $resourceName;

        return $this;
    }

    /**
     * Gets the value of privileges from the record
     *
     * @return array
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * Sets the Value to privileges in the record
     *
     * @param array $privileges
     *
     * @return self
     */
    public function setPrivileges($privileges)
    {
        $this->privileges = $privileges;

        return $this;
    }
}
