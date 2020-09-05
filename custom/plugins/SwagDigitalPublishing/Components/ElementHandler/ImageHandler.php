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

namespace SwagDigitalPublishing\Components\ElementHandler;

use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\Components\Compatibility\LegacyStructConverter;

class ImageHandler implements PopulateElementHandlerInterface
{
    /**
     * @var MediaServiceInterface
     */
    private $mediaService;

    /**
     * @var LegacyStructConverter
     */
    private $legacyStructConverter;

    public function __construct(MediaServiceInterface $mediaService, LegacyStructConverter $legacyStructConverter)
    {
        $this->mediaService = $mediaService;
        $this->legacyStructConverter = $legacyStructConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(array $element)
    {
        return $element['name'] === 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $element, ShopContextInterface $context)
    {
        if (array_key_exists('mediaId', $element) && !empty($element['mediaId'])) {
            $media = $this->mediaService->getList([$element['mediaId']], $context);
            $media = array_shift($media);

            if ($media) {
                $element['media'] = $this->legacyStructConverter->convertMediaStruct($media);
            }
        }

        return $element;
    }
}
