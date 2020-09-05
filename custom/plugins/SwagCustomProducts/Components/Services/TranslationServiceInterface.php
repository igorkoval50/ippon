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

namespace SwagCustomProducts\Components\Services;

use SwagCustomProducts\Models\Template;

/**
 * This interface is for easy extending or overwriting the TranslationService
 */
interface TranslationServiceInterface
{
    /**
     * Translates the given CustomProduct template with all options and values
     *
     * @return array
     */
    public function translateTemplate(array $template);

    /**
     * Clones the translations from $oldTemplate and saves them in $newTemplate
     *
     * @param Template $oldTemplate
     * @param Template $newTemplate
     */
    public function cloneTranslations($oldTemplate, $newTemplate);

    /**
     * Translates and returns the given options.
     *
     * @return array
     */
    public function getTranslatedOptions(array $options);

    /**
     * Translates a single option.
     *
     * @return array
     */
    public function getTranslatedOption(array $option);
}
