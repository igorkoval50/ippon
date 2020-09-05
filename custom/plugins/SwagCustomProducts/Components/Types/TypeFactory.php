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

namespace SwagCustomProducts\Components\Types;

use Doctrine\Common\Collections\ArrayCollection;
use SwagCustomProducts\Components\Types\Types\CheckBoxType;
use SwagCustomProducts\Components\Types\Types\ColorSelectType;
use SwagCustomProducts\Components\Types\Types\DateType;
use SwagCustomProducts\Components\Types\Types\FileUploadType;
use SwagCustomProducts\Components\Types\Types\ImageSelectType;
use SwagCustomProducts\Components\Types\Types\ImageUploadType;
use SwagCustomProducts\Components\Types\Types\MultiSelectType;
use SwagCustomProducts\Components\Types\Types\NumberFieldType;
use SwagCustomProducts\Components\Types\Types\RadioType;
use SwagCustomProducts\Components\Types\Types\SelectType;
use SwagCustomProducts\Components\Types\Types\TextAreaType;
use SwagCustomProducts\Components\Types\Types\TextFieldType;
use SwagCustomProducts\Components\Types\Types\TimeType;
use SwagCustomProducts\Components\Types\Types\WysiwygType;

class TypeFactory implements TypeFactoryInterface
{
    /**
     * @var \Enlight_Event_EventManager
     */
    private $eventManager;

    public function __construct(\Enlight_Event_EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function factory()
    {
        $types = [
            TextFieldType::TYPE => new TextFieldType(),
            TextAreaType::TYPE => new TextAreaType(),
            NumberFieldType::TYPE => new NumberFieldType(),
            SelectType::TYPE => new SelectType(),
            MultiSelectType::TYPE => new MultiSelectType(),
            CheckBoxType::TYPE => new CheckBoxType(),
            RadioType::TYPE => new RadioType(),
            ColorSelectType::TYPE => new ColorSelectType(),
            ImageSelectType::TYPE => new ImageSelectType(),
            FileUploadType::TYPE => new FileUploadType(),
            ImageUploadType::TYPE => new ImageUploadType(),
            DateType::TYPE => new DateType(),
            TimeType::TYPE => new TimeType(),
            WysiwygType::TYPE => new WysiwygType(),
        ];

        $collection = new ArrayCollection([]);
        $this->eventManager->collect('SwagCustomProduct_Collect_Types', $collection);

        $typesFromEvent = [];

        foreach ($collection->toArray() as $type) {
            $typesFromEvent[$type::TYPE] = $type;
        }

        $types = array_merge($types, $typesFromEvent);

        return $types;
    }
}
