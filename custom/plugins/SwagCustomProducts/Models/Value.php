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

namespace SwagCustomProducts\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Table(name="s_plugin_custom_products_value")
 * @ORM\Entity()
 */
class Value extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ordernumber", type="string", nullable=true)
     */
    protected $ordernumber;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    protected $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_default_value", type="boolean", nullable=true)
     */
    protected $isDefaultValue;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_once_surcharge", type="boolean", nullable=true)
     */
    protected $isOnceSurcharge;

    /**
     * @var int
     *
     * @ORM\Column(name="option_id", type="integer")
     */
    protected $optionId;

    /**
     * @var Option
     *
     * @ORM\ManyToOne(targetEntity="SwagCustomProducts\Models\Option")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id")
     */
    protected $option;

    /**
     * @var int
     *
     * @ORM\Column(name="media_id", type="integer", nullable=true)
     */
    protected $mediaId;

    /**
     * @var string
     *
     * @ORM\Column(name="seo_title", type="string", nullable=true)
     */
    protected $seoTitle;

    /**
     * @var Collection<Price>
     *
     * @ORM\OneToMany(
     *     targetEntity="SwagCustomProducts\Models\Price",
     *     mappedBy="value",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    protected $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    /**
     * Value Clone
     */
    public function __clone()
    {
        $this->id = null;
        $this->ordernumber = '';

        $prices = [];
        foreach ($this->prices as $price) {
            $new = clone $price;
            $new->setValue($this);
            $prices[] = $new;
        }
        $this->prices = new ArrayCollection($prices);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOrdernumber(): ?string
    {
        return $this->ordernumber;
    }

    public function setOrdernumber(?string $orderNumber): void
    {
        $this->ordernumber = $orderNumber;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getIsDefaultValue(): ?bool
    {
        return $this->isDefaultValue;
    }

    public function setIsDefaultValue(?bool $isDefaultValue): void
    {
        $this->isDefaultValue = $isDefaultValue;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getIsOnceSurcharge(): ?bool
    {
        return $this->isOnceSurcharge;
    }

    public function setIsOnceSurcharge(?bool $isOnceSurcharge): void
    {
        $this->isOnceSurcharge = $isOnceSurcharge;
    }

    public function getOptionId(): ?int
    {
        return $this->optionId;
    }

    public function setOptionId(?int $optionId)
    {
        $this->optionId = $optionId;
    }

    public function getOption(): ?option
    {
        return $this->option;
    }

    public function setOption(?Option $option): void
    {
        $this->setManyToOne($option, Option::class, 'option');
    }

    public function getMediaId(): ?int
    {
        return $this->mediaId;
    }

    public function setMediaId(?int $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): void
    {
        $this->seoTitle = $seoTitle;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function setPrices(Collection $prices): void
    {
        $this->setOneToMany(
            $prices,
            Price::class,
            'prices',
            'value'
        );
    }
}
