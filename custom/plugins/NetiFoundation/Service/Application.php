<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Service;

class Application implements ApplicationInterface
{
    /**
     * @var \Shopware_Components_Config
     */
    private $swConfig;

    /**
     * Application constructor.
     *
     * @param \Shopware_Components_Config $swConfig
     */
    public function __construct(\Shopware_Components_Config $swConfig)
    {
        $this->swConfig = $swConfig;
    }

    /**
     * @param string $requiredVersion
     * @param bool   $includeRequiredVersion
     *
     * @return bool
     */
    public function assertMinimumVersion($requiredVersion, $includeRequiredVersion = true)
    {
        $operator = $includeRequiredVersion ? '>=' : '>';

        return (bool)$this->assertVersion($requiredVersion, $operator);
    }

    /**
     * @param string $requiredVersion
     * @param string $operator
     *
     * @return bool|int
     */
    private function assertVersion($requiredVersion, $operator)
    {
        $version = $this->swConfig->get('version');

        if ($version === '___VERSION___') {
            return true;
        }

        return version_compare($version, $requiredVersion, $operator);
    }

    /**
     * @param string $requiredVersion
     * @param bool   $includeRequiredVersion
     *
     * @return bool
     */
    public function assertMaximumVersion($requiredVersion, $includeRequiredVersion = true)
    {
        $operator = $includeRequiredVersion ? '<=' : '<';

        return (bool)$this->assertVersion($requiredVersion, $operator);
    }
}