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

use Enlight_Hook_HookArgs;

/**
 * This interface is for easy extending or overwriting DocumentValueExtender
 */
interface DocumentValueExtenderInterface
{
    /**
     * This method extends the document view with necessary values of the CustomProduct.
     */
    public function extendWithValues(Enlight_Hook_HookArgs $args);

    /**
     * Removes unnecessary option positions for the document generation.
     * An option position is unnecessary when it has no price and no own custom product value - therefore
     * there's no need for a very own position.
     * In that case, the option name gets prefixed to the options value.
     */
    public function groupOptionsForDocument(Enlight_Hook_HookArgs $args);

    /**
     * Removes unnecessary option positions for mails.
     *
     * @return array
     */
    public function groupOptionsForMail(array $positions);
}
