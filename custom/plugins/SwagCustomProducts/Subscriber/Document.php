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

namespace SwagCustomProducts\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Hook_HookArgs;
use SwagCustomProducts\Components\Services\DocumentValueExtenderInterface;

class Document implements SubscriberInterface
{
    /**
     * @var DocumentValueExtenderInterface
     */
    private $documentValueExtender;

    public function __construct(DocumentValueExtenderInterface $documentValueExtender)
    {
        $this->documentValueExtender = $documentValueExtender;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Components_Document::assignValues::after' => 'onBeforeRenderDocument',
        ];
    }

    /**
     * Extend the Document with optionValues
     */
    public function onBeforeRenderDocument(Enlight_Hook_HookArgs $args)
    {
        $this->documentValueExtender->extendWithValues($args);

        $documentType = (int) $args->getSubject()->_typID;

        if ($documentType === 2) {
            return;
        }

        $this->documentValueExtender->groupOptionsForDocument($args);
    }
}
