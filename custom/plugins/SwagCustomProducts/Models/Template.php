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
use Shopware\Models\Article\Article;
use Shopware\Models\Media\Media;

/**
 * @ORM\Table(name="s_plugin_custom_products_template")
 * @ORM\Entity()
 */
class Template extends ModelEntity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="internal_name", type="string", nullable=false, length=255, unique=true)
     */
    protected $internalName;

    /**
     * @var string
     * @ORM\Column(name="display_name", type="string", nullable=true)
     */
    protected $displayName;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var int
     * @ORM\Column(name="media_id", type="integer", nullable=true)
     */
    protected $mediaId;

    /**
     * @var bool
     * @ORM\Column(name="step_by_step_configurator", type="boolean")
     */
    protected $stepByStepConfigurator = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = false;

    /**
     * @var bool
     * @ORM\Column(name="confirm_input", type="boolean")
     */
    protected $confirmInput = false;

    /**
     * @var bool
     * @ORM\Column(name="variants_on_top", type="boolean")
     */
    protected $variantsOnTop = false;

    /**
     * @var Collection<Option>
     *
     * @ORM\OneToMany(
     *     targetEntity="SwagCustomProducts\Models\Option",
     *     orphanRemoval=true,
     *     mappedBy="template",
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $options;

    /**
     * @var Collection<Article>
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Article\Article")
     * @ORM\JoinTable(name="s_plugin_custom_products_template_product_relation",
     *     joinColumns={
     *         @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="article_id", referencedColumnName="id", unique=true)
     *     }
     * )
     */
    protected $articles;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Media\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true)
     */
    protected $media;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    /**
     * Template Clone
     */
    public function __clone()
    {
        $this->id = null;

        $options = [];
        foreach ($this->options as $option) {
            /** @var Option $option */
            $newOption = clone $option;
            $newOption->setTemplate($this);

            $options[] = $newOption;
        }

        $this->options = new ArrayCollection($options);
        $this->setArticles(new ArrayCollection());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function setInternalName($internalName): void
    {
        $this->internalName = $internalName;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): void
    {
        $this->media = $media;
    }

    public function getMediaId(): int
    {
        return $this->mediaId;
    }

    public function setMediaId(int $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getStepByStepConfigurator(): bool
    {
        return $this->stepByStepConfigurator;
    }

    public function setStepByStepConfigurator(bool $stepByStepConfigurator): void
    {
        $this->stepByStepConfigurator = $stepByStepConfigurator;
    }

    public function getConfirmInput(): bool
    {
        return $this->confirmInput;
    }

    public function setConfirmInput(bool $confirmInput): void
    {
        $this->confirmInput = $confirmInput;
    }

    /**
     * @return Collection<Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Collection<Article> $articles
     */
    public function setArticles(Collection $articles): void
    {
        $this->articles = $articles;
    }

    public function addArticle(Article $article): void
    {
        $this->articles->add($article);
    }

    /**
     * @return Collection<Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    /**
     * @param Collection<Option> $options
     */
    public function setOptions(Collection $options): void
    {
        $this->setOneToMany(
            $options,
            Option::class,
            'options',
            'template'
        );
    }

    public function addOption(Option $option): void
    {
        $this->options->add($option);
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getVariantsOnTop(): bool
    {
        return $this->variantsOnTop;
    }

    public function setVariantsOnTop(bool $variantsOnTop): void
    {
        $this->variantsOnTop = $variantsOnTop;
    }
}
