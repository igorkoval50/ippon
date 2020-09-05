<?php

namespace Shopware\CustomModels\Stutt;

use Shopware\Components\Model\ModelEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Custom model for redirects
 *
 * @ORM\Entity
 * @ORM\Table(name="s_stutt_redirect")
 */
class Redirect extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $oldUrl
     *
     * @ORM\Column()
     */
    private $oldUrl;

    /**
     * @var string $newUrl
     *
     * @ORM\Column()
     */
    private $newUrl;

    /**
     * @var integer $active
     *
     * @ORM\Column(type="boolean")
     */
    private $active = TRUE;

    /**
     * @var integer $overrideShopUrl
     *
     * @ORM\Column(type="boolean")
     */
    private $overrideShopUrl = TRUE;

    /**
     * @var integer $temporaryRedirect
     *
     * @ORM\Column(type="boolean")
     */
    private $temporaryRedirect = TRUE;

    /**
     * @var integer $externalRedirect
     *
     * @ORM\Column(type="boolean")
     */
    private $externalRedirect = FALSE;

    /**
     * @var integer $gone
     *
     * @ORM\Column(type="boolean")
     */
    private $gone = FALSE;


    /**
     * @var
     * @ORM\Column(name="shop_id", type="integer", nullable=true)
     */
    protected $shop_id;

    /**
     * @var \Shopware\Models\Shop\Shop $shop
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id", nullable=true)
     */
    protected $shop;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $oldUrl
     */
    public function setOldUrl($oldUrl)
    {
        $this->oldUrl = $oldUrl;
    }

    /**
     * @return string
     */
    public function getOldUrl()
    {
        return $this->oldUrl;
    }

    /**
     * @param string $newUrl
     */
    public function setNewUrl($newUrl)
    {
        $this->newUrl = $newUrl;
    }

    /**
     * @return string
     */
    public function getNewUrl()
    {
        return $this->newUrl;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param int $overrideShopUrl
     */
    public function setOverrideShopUrl($overrideShopUrl)
    {
        $this->overrideShopUrl = $overrideShopUrl;
    }

    /**
     * @return int
     */
    public function getOverrideShopUrl()
    {
        return $this->overrideShopUrl;
    }

    /**
     * @param int $temporaryRedirect
     */
    public function setTemporaryRedirect($temporaryRedirect)
    {
        $this->temporaryRedirect = $temporaryRedirect;
    }

    /**
     * @return int
     */
    public function getTemporaryRedirect()
    {
        return $this->temporaryRedirect;
    }


    /**
     * @param int $externalRedirect
     */
    public function setExternalRedirect($externalRedirect)
    {
        $this->externalRedirect = $externalRedirect;
    }

    /**
     * @return int
     */
    public function getExternalRedirect()
    {
        return $this->externalRedirect;
    }

    /**
     * @return mixed
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param mixed $shop
     */
    public function setShop($shop)
    {
        $this->shop = $shop;
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shop_id;
    }

    /**
     * @param mixed $shop_id
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    /**
     * @return int
     */
    public function getGone()
    {
        return $this->gone;
    }

    /**
     * @param int $gone
     */
    public function setGone($gone)
    {
        $this->gone = $gone;
    }



}
