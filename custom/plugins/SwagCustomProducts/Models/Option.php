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
 * @ORM\Table(name="s_plugin_custom_products_option")
 * @ORM\Entity()
 */
class Option extends ModelEntity
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="ordernumber", type="string", nullable=true)
     */
    protected $ordernumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="required", type="boolean", nullable=true)
     */
    protected $required;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    protected $type;

    /**
     * @var int
     *
     * @ORM\Column(name="`position`", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var string
     *
     * @ORM\Column(name="default_value", type="string", nullable=true)
     */
    protected $defaultValue;

    /**
     * @var string
     *
     * @ORM\Column(name="placeholder", type="string", nullable=true)
     */
    protected $placeholder;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_once_surcharge", type="boolean", nullable=true)
     */
    protected $isOnceSurcharge;

    /**
     * @var int
     *
     * @ORM\Column(name="max_text_length", type="integer", nullable=true)
     */
    protected $maxTextLength;

    /**
     * @var int
     *
     * @ORM\Column(name="min_value", type="integer", nullable=true)
     */
    protected $minValue;

    /**
     * @var int
     *
     * @ORM\Column(name="max_value", type="integer", nullable=true)
     */
    protected $maxValue;

    /**
     * @var int
     *
     * @ORM\Column(name="max_file_size", type="integer", nullable=true)
     */
    protected $maxFileSize;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="min_date", type="datetime", nullable=true)
     */
    protected $minDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="max_date", type="datetime", nullable=true)
     */
    protected $maxDate;

    /**
     * @var int
     *
     * @ORM\Column(name="max_files", type="integer", nullable=true)
     */
    protected $maxFiles;

    /**
     * @var float
     *
     * @ORM\Column(name="`interval`", type="float", nullable=true)
     */
    protected $interval;

    /**
     * @var bool
     *
     * @ORM\Column(name="could_contain_values", type="boolean", nullable=false)
     */
    protected $couldContainValues;

    /**
     * @var int
     *
     * @ORM\Column(name="template_id", type="integer", nullable=false)
     */
    protected $templateId;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="SwagCustomProducts\Models\Template", inversedBy="options")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $template;

    /**
     * @var Collection<Value>
     *
     * @ORM\OneToMany(
     *     targetEntity="SwagCustomProducts\Models\Value",
     *     mappedBy="option",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $values;

    /**
     * @var bool
     *
     * @ORM\Column(name="allows_multiple_selection", type="boolean", nullable=true)
     */
    protected $allowsMultipleSelection;

    /**
     * @var Collection<Price>
     *
     * @ORM\OneToMany(
     *     targetEntity="SwagCustomProducts\Models\Price",
     *     mappedBy="option",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    protected $prices;

    public function __construct()
    {
        $this->values = new ArrayCollection();
        $this->prices = new ArrayCollection();
    }

    /**
     * Option Clone
     */
    public function __clone()
    {
        $this->id = null;
        $this->ordernumber = '';

        $values = [];
        foreach ($this->values as $value) {
            /** @var Value $newValue */
            $newValue = clone $value;
            $newValue->setOption($this);
            $values[] = $newValue;
        }

        $prices = [];
        foreach ($this->prices as $price) {
            $newPrice = clone $price;
            $newPrice->setOption($this);
            $prices[] = $newPrice;
        }

        $this->values = new ArrayCollection($values);
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(?string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(?string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): void
    {
        $this->setManyToOne(
            $template,
            Template::class,
            'template'
        );
    }

    public function getTemplateId(): ?int
    {
        return $this->templateId;
    }

    public function setTemplateId(?int $templateId): void
    {
        $this->templateId = $templateId;
    }

    /**
     * @return Collection<Value>
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    /**
     * @param Collection<Value> $values
     */
    public function setValues(Collection $values): void
    {
        $this->setOneToMany(
            $values,
            Value::class,
            'values',
            'option'
        );
    }

    /**
     * @return Collection<Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * @param Collection<Price> $prices
     */
    public function setPrices(Collection $prices): void
    {
        $this->setOneToMany(
            $prices,
            Price::class,
            'prices',
            'option'
        );
    }

    public function getIsOnceSurcharge(): ?bool
    {
        return $this->isOnceSurcharge;
    }

    public function setIsOnceSurcharge(?bool $isOnceSurcharge): void
    {
        $this->isOnceSurcharge = $isOnceSurcharge;
    }

    public function getMaxTextLength(): ?int
    {
        return $this->maxTextLength;
    }

    public function setMaxTextLength(?int $maxTextLength): void
    {
        $this->maxTextLength = $maxTextLength;
    }

    public function getMinValue(): ?int
    {
        return $this->minValue;
    }

    public function setMinValue(?int $minValue): void
    {
        $this->minValue = $minValue;
    }

    public function getMaxValue(): ?int
    {
        return $this->maxValue;
    }

    public function setMaxValue(?int $maxValue): void
    {
        $this->maxValue = $maxValue;
    }

    public function getMinDate(): ?string
    {
        return $this->minDate;
    }

    public function setMinDate(?string $minDate): void
    {
        $this->minDate = $minDate;
    }

    public function getMaxDate(): ?string
    {
        return $this->maxDate;
    }

    public function setMaxDate(?string $maxDate): void
    {
        $this->maxDate = $maxDate;
    }

    public function getInterval(): ?float
    {
        return $this->interval;
    }

    public function setInterval(?float $interval): void
    {
        $this->interval = $interval;
    }

    public function getOrdernumber(): ?string
    {
        return $this->ordernumber;
    }

    public function setOrdernumber(?string $ordernumber): void
    {
        $this->ordernumber = $ordernumber;
    }

    public function getCouldContainValues(): bool
    {
        return $this->couldContainValues;
    }

    public function setCouldContainValues(bool $couldContainValues): void
    {
        $this->couldContainValues = $couldContainValues;
    }

    public function getMaxFiles(): ?int
    {
        return $this->maxFiles;
    }

    public function setMaxFiles(?int $maxFiles): void
    {
        $this->maxFiles = $maxFiles;
    }

    public function getAllowsMultipleSelection(): ?bool
    {
        return $this->allowsMultipleSelection;
    }

    public function setAllowsMultipleSelection(?bool $allowsMultipleSelection): void
    {
        $this->allowsMultipleSelection = $allowsMultipleSelection;
    }

    public function getMaxFileSize(): ?int
    {
        return $this->maxFileSize;
    }

    public function setMaxFileSize(?int $maxFileSize): void
    {
        $this->maxFileSize = $maxFileSize;
    }
}
