<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;
use NetiFoundation\Struct\PluginConfigFile\MailTemplate\Locale;

/**
 * Class MailTemplate
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class MailTemplate extends AbstractClass
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var boolean
     */
    protected $html = 0;

    /**
     * @var Locale[]
     */
    protected $locales;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $mailtype = 1;

    /**
     * Gets the value of template from the record
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the Value to template in the record
     *
     * @param string $template
     *
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = (string) $template;

        return $this;
    }

    /**
     * Gets the value of html from the record
     *
     * @return boolean
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Sets the Value to html in the record
     *
     * @param boolean $html
     *
     * @return self
     */
    public function setHtml($html)
    {
        $this->html = (boolean) $html;

        return $this;
    }

    /**
     * Gets the value of locales from the record
     *
     * @return Locale[]
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Sets the Value to locales in the record
     *
     * @param array $locales
     *
     * @return self
     */
    public function setLocales($locales)
    {
        $localesStructs = [];
        if ($this->isAssoc($locales)) {
            foreach ($locales as $locale => $entry) {
                if (! isset($entry['locale'])) {
                    $entry['locale'] = $locale;
                }
                $localesStructs[] = new Locale($entry);
            }
        } else {
            foreach ($locales as $entry) {
                $localesStructs[] = new Locale($entry);
            }
        }

        $this->locales = $localesStructs;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getMailtype()
    {
        return $this->mailtype;
    }

    /**
     * @param int $mailtype
     *
     * @return self
     */
    public function setMailtype($mailtype)
    {
        $this->mailtype = $mailtype;

        return $this;
    }
}
