<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagFuzzy\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Shop\Shop;

/**
 * Shopware SwagFuzzy Plugin - SynonymGroups Model
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_swag_fuzzy_synonym_groups")
 */
class SynonymGroups extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer");
     */
    private $shopId;

    /**
     * @var Shop
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;

    /**
     * @var string ;
     *
     * @ORM\Column()
     */
    private $groupName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="SwagFuzzy\Models\Synonyms",
     *      mappedBy="synonymGroup",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     */
    private $synonyms;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true);
     */
    private $normalSearchEmotionId;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Emotion\Emotion")
     * @ORM\JoinColumn(name="normalSearchEmotionId",referencedColumnName="id")
     */
    private $normalSearchEmotion;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $normalSearchBanner = '';

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $normalSearchLink = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $normalSearchHeader = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $normalSearchDescription = '';

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $ajaxSearchBanner = '';

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $ajaxSearchLink = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $ajaxSearchHeader = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $ajaxSearchDescription = '';

    /**
     * initialises the synonyms as ArrayCollection.
     */
    public function __construct()
    {
        $this->synonyms = new ArrayCollection();
    }

    /**
     * defines how to clone the synonyms.
     */
    public function __clone()
    {
        $this->id = null;
        $synonyms = [];

        foreach ($this->synonyms as $synonym) {
            /** @var Synonyms $new */
            $new = clone $synonym;
            $new->setSynonymGroup($this);
            $synonyms[] = $new;
        }
        $this->synonyms = $synonyms;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param Shop $shop
     *
     * @return $this
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     *
     * @return $this
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * @return Synonyms[]
     */
    public function getSynonyms()
    {
        return $this->synonyms;
    }

    /**
     * @param Synonyms[] $synonyms
     *
     * @return ModelEntity
     */
    public function setSynonyms($synonyms)
    {
        return $this->setOneToMany($synonyms, Synonyms::class, 'synonyms', 'synonymGroup');
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNormalSearchEmotion()
    {
        return $this->normalSearchEmotion;
    }

    /**
     * @param mixed $normalSearchEmotion
     *
     * @return $this
     */
    public function setNormalSearchEmotion($normalSearchEmotion)
    {
        $this->normalSearchEmotion = $normalSearchEmotion;

        return $this;
    }

    /**
     * @return string
     */
    public function getNormalSearchBanner()
    {
        return $this->normalSearchBanner;
    }

    /**
     * @param string $normalSearchBanner
     *
     * @return $this
     */
    public function setNormalSearchBanner($normalSearchBanner)
    {
        $this->normalSearchBanner = $normalSearchBanner;

        return $this;
    }

    /**
     * @return string
     */
    public function getNormalSearchLink()
    {
        return $this->normalSearchLink;
    }

    /**
     * @param string $normalSearchLink
     *
     * @return $this
     */
    public function setNormalSearchLink($normalSearchLink)
    {
        $this->normalSearchLink = $normalSearchLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getNormalSearchHeader()
    {
        return $this->normalSearchHeader;
    }

    /**
     * @param string $normalSearchHeader
     *
     * @return $this
     */
    public function setNormalSearchHeader($normalSearchHeader)
    {
        $this->normalSearchHeader = $normalSearchHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function getNormalSearchDescription()
    {
        return $this->normalSearchDescription;
    }

    /**
     * @param string $normalSearchDescription
     *
     * @return $this
     */
    public function setNormalSearchDescription($normalSearchDescription)
    {
        $this->normalSearchDescription = $normalSearchDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getAjaxSearchBanner()
    {
        return $this->ajaxSearchBanner;
    }

    /**
     * @param string $ajaxSearchBanner
     *
     * @return $this
     */
    public function setAjaxSearchBanner($ajaxSearchBanner)
    {
        $this->ajaxSearchBanner = $ajaxSearchBanner;

        return $this;
    }

    /**
     * @return string
     */
    public function getAjaxSearchLink()
    {
        return $this->ajaxSearchLink;
    }

    /**
     * @param string $ajaxSearchLink
     *
     * @return $this
     */
    public function setAjaxSearchLink($ajaxSearchLink)
    {
        $this->ajaxSearchLink = $ajaxSearchLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getAjaxSearchHeader()
    {
        return $this->ajaxSearchHeader;
    }

    /**
     * @param string $ajaxSearchHeader
     *
     * @return $this
     */
    public function setAjaxSearchHeader($ajaxSearchHeader)
    {
        $this->ajaxSearchHeader = $ajaxSearchHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function getAjaxSearchDescription()
    {
        return $this->ajaxSearchDescription;
    }

    /**
     * @param string $ajaxSearchDescription
     *
     * @return $this
     */
    public function setAjaxSearchDescription($ajaxSearchDescription)
    {
        $this->ajaxSearchDescription = $ajaxSearchDescription;

        return $this;
    }
}
