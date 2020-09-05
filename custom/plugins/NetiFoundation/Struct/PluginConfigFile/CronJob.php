<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class CronJob
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class CronJob extends AbstractClass
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var int
     */
    protected $interval = 86400;

    /**
     * @var boolean
     */
    protected $active = 1;

    /**
     * @var \DateTime
     */
    protected $nextRun;

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
     * Gets the value of interval from the record
     *
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Sets the Value to interval in the record
     *
     * @param int $interval
     *
     * @return self
     */
    public function setInterval($interval)
    {
        if ($interval) {
            $this->interval = $interval;
        } else {
            $this->interval = 86400;
        }

        return $this;
    }

    /**
     * Gets the value of active from the record
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Sets the Value to active in the record
     *
     * @param boolean $active
     *
     * @return self
     */
    public function setActive($active)
    {
        $this->active = (boolean) $active;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNextRun()
    {
        return $this->nextRun;
    }

    /**
     * @param \DateTime $nextRun
     * @return CronJob
     */
    public function setNextRun($nextRun)
    {
        $this->nextRun = $nextRun;

        return $this;
    }
}
