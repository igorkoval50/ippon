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

namespace SwagNewsletter\Components\LiveShopping;

use RuntimeException;
use Shopware\Models\Plugin\Plugin;
use SwagNewsletter\Components\NewsletterComponentHelperInterface;

class LiveShoppingWidgetCreator implements LiveShoppingWidgetCreatorInterface
{
    /**
     * @var NewsletterComponentHelperInterface
     */
    private $newsletterComponentHelper;

    /**
     * @param NewsletterComponentHelperInterface $newsletterComponentHelper
     */
    public function __construct(NewsletterComponentHelperInterface $newsletterComponentHelper)
    {
        $this->newsletterComponentHelper = $newsletterComponentHelper;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function create(Plugin $plugin)
    {
        if ($this->newsletterComponentHelper->findByPluginId($plugin->getId())) {
            throw new RuntimeException('Widget was already created.');
        }

        $options = [
            'name' => 'Live-Shopping',
            'xtype' => 'newsletter-components-live-shopping',
            'template' => 'liveshopping',
            'description' => '',
            'cls' => 'newsletter-liveshopping-element',
        ];

        $component = $this->newsletterComponentHelper->createNewsletterComponent($options, $plugin->getId());

        $component->createTextField(
            [
                'name' => 'headline',
                'fieldLabel' => 'Überschrift',
                'allowBlank' => false,
            ]
        );

        $component->createNumberField(
            [
                'name' => 'number',
                'fieldLabel' => 'Anzahl der Liveshopping Produkte',
                'allowBlank' => false,
            ]
        );

        $component->createHiddenField(
            [
                'name' => 'article_data',
                'valueType' => 'json',
                'defaultValue' => '2',
                'allowBlank' => false,
            ]
        );

        $this->newsletterComponentHelper->save();
    }
}
