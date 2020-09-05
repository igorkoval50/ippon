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

namespace SwagNewsletter\Components;

use SwagNewsletter\Models\Component;

interface NewsletterComponentHelperInterface
{
    /**
     * Creates a new component which can be used in the backend newsletter
     * module.
     *
     * @param array $options {
     *
     * @var string $name Required; Logical name of the component
     * @var string $template Required; Template class name which will be loaded in the frontend
     * @var string $xType Required; Ext JS xtype for the backend module component
     * @var string $cls Optional; $cls Css class which used in the frontend emotion
     * @var string $convertFunction Optional; Data convert function which allows to convert the saved backend data
     * @var string $description Optional; Description field for the component, which displayed in the backend module.
     *             }
     *
     * @return Component
     */
    public function createNewsletterComponent(array $options, $pluginId);

    /**
     * Save all components created with the createNewsletterComponent method
     */
    public function save();

    /**
     * @param int $pluginId
     *
     * @return Component[]
     */
    public function findByPluginId($pluginId);
}
