<?php
namespace NetiLanguageDetector\Models;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Model Class for table s_neti_currencylocation
 * @subpackage de.netinventors.LanguageDetector
 * @copyright  Copyright (c) 2013, Net Inventors - Agentur für digitale Medien GmbH
 * @author     Net Inventors GmbH
 *
 * @ORM\Entity
 * @ORM\Table(name="s_neti_currencylocation")
 * @ORM\HasLifecycleCallbacks
*/
class CurrencyLocation extends ModelEntity {
    /**
     * Primary Key - autoincrement value
     *
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(name="country_code", type="string", length=2, nullable=true)
     */
    private $country_code;

    /**
     * @ORM\Column(name="country_name", type="string", length=64, nullable=true)
     */
    private $country_name;

    /**
     * @ORM\Column(name="currency_code", type="string", length=5, nullable=true)
     */
    private $currency_code;

    /**
     * @ORM\Column(name="currency_name", type="string", length=64, nullable=true)
     */
    private $currency_name;

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of country_code.
     *
     * @return mixed
     */
    public function getCountry_code()
    {
        return $this->country_code;
    }

    /**
     * Sets the value of country_code.
     *
     * @param mixed $country_code the country_code
     *
     * @return self
     */
    public function setCountry_code($country_code)
    {
        $this->country_code = $country_code;

        return $this;
    }

    /**
     * Gets the value of country_name.
     *
     * @return mixed
     */
    public function getCountry_name()
    {
        return $this->country_name;
    }

    /**
     * Sets the value of country_name.
     *
     * @param mixed $country_name the country_name
     *
     * @return self
     */
    public function setCountry_name($country_name)
    {
        $this->country_name = $country_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param mixed $currency_code
     *
     * @return self
     */
    public function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrencyName()
    {
        return $this->currency_name;
    }

    /**
     * @param mixed $currency_name
     *
     * @return self
     */
    public function setCurrencyName($currency_name)
    {
        $this->currency_name = $currency_name;

        return $this;
    }
}
