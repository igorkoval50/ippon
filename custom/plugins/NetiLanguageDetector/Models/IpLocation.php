<?php
namespace NetiLanguageDetector\Models;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Model Class for table s_neti_ip2location
 * @subpackage de.netinventors.LanguageDetector
 * @copyright  Copyright (c) 2013, Net Inventors - Agentur für digitale Medien GmbH
 * @author     Net Inventors GmbH
 *
 * @ORM\Entity
 * @ORM\Table(name="s_neti_ip2location")
 * @ORM\HasLifecycleCallbacks
*/
class IpLocation extends ModelEntity {
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
     * @ORM\Column(name="ip_from", type="decimal", nullable=true)
     *
     */
    private $ip_from;

    /**
     * @ORM\Column(name="ip_to", type="decimal", nullable=true)
     */
    private $ip_to;

    /**
     * @ORM\Column(name="country_code", type="string", length=2, nullable=true)
     */
    private $country_code;

    /**
     * @ORM\Column(name="country_name", type="string", length=64, nullable=true)
     */
    private $country_name;

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
     * Gets the value of ip_from.
     *
     * @return mixed
     */
    public function getIp_from()
    {
        return $this->ip_from;
    }

    /**
     * Sets the value of ip_from.
     *
     * @param mixed $ip_from the ip_from
     *
     * @return self
     */
    public function setIp_from($ip_from)
    {
        $this->ip_from = $ip_from;

        return $this;
    }

    /**
     * Gets the value of ip_to.
     *
     * @return mixed
     */
    public function getIp_to()
    {
        return $this->ip_to;
    }

    /**
     * Sets the value of ip_to.
     *
     * @param mixed $ip_to the ip_to
     *
     * @return self
     */
    public function setIp_to($ip_to)
    {
        $this->ip_to = $ip_to;

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

}
