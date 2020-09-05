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

use Shopware\Models\Newsletter\Newsletter;

interface NewsletterHelperInterface
{
    /**
     * Helper function which will convert a given list of campaign-containers to emotion-style-elements
     *
     * @param array $newsletter
     *
     * @return array
     */
    public function getNewsletterElements(array $newsletter);

    /**
     * Internal helper function which will save the emotion style elements.
     *
     * @param Newsletter $newsletter
     * @param array      $elements
     */
    public function saveThirdPartyElements(Newsletter $newsletter, array $elements);

    /**
     * Helper function to convert the emotion-style element/table structure to the old newsletter structure
     *
     * @param Newsletter $model
     * @param array      $elements
     */
    public function saveNewsletterElements(Newsletter $model, array $elements);
}
