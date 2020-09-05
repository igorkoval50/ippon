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

namespace SwagCustomProducts\Structs;

use Shopware\Models\Article\Article;
use Shopware\Models\Media\Media;

class TemplateStruct
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $internalName;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $mediaId;

    /**
     * @var bool
     */
    public $stepByStepConfigurator = false;

    /**
     * @var bool
     */
    public $active = false;

    /**
     * @var bool
     */
    public $confirmInput = false;

    /**
     * @var OptionStruct[]
     */
    public $options;

    /**
     * @var Article[]
     */
    public $articles;

    /**
     * @var Media
     */
    public $media;

    /**
     * @param int            $id
     * @param string         $internalName
     * @param string         $description
     * @param int            $mediaId
     * @param bool           $stepByStepConfigurator
     * @param bool           $confirmInput
     * @param OptionStruct[] $options
     * @param Article[]      $articles
     * @param Media          $media
     */
    public function __construct(
        $id,
        $internalName,
        $description,
        $mediaId,
        $stepByStepConfigurator,
        $active,
        $confirmInput,
        $options,
        $articles,
        $media
    ) {
        $this->id = $id;
        $this->internalName = $internalName;
        $this->description = $description;
        $this->mediaId = $mediaId;
        $this->stepByStepConfigurator = $stepByStepConfigurator;
        $this->active = $active;
        $this->confirmInput = $confirmInput;
        $this->options = $options;
        $this->articles = $articles;
        $this->media = $media;
    }
}
