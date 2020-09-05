<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct;

/**
 * Class PluginConfig
 *
 * @package NetiFoundation\Struct
 */
class PluginConfig extends AbstractClass
{
    /**
     * @var bool
     */
    protected $showDebug;

    /**
     * @var bool
     */
    protected $fileLogging;

    /**
     * @return bool
     */
    public function isFileLogging()
    {
        return $this->fileLogging;
    }

    /**
     * @return bool
     */
    public function isShowDebug()
    {
        return $this->showDebug;
    }
}
