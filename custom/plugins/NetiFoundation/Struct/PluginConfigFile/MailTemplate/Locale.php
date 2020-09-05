<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile\MailTemplate;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class Locale
 *
 * @package NetiFoundation\Struct\PluginConfigFile\MailTemplate
 */
class Locale extends AbstractClass
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $subject;

    /**
     * Gets the value of locale from the record
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the Value to locale in the record
     *
     * @param string $locale
     *
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;

        return $this;
    }

    /**
     * Gets the value of subject from the record
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the Value to subject in the record
     *
     * @param string $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = (string) $subject;

        return $this;
    }
}
