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

/**
 * Shopware SwagFuzzy Plugin - SwagFuzzyEmotions Backend Controller
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzyEmotions extends Shopware_Controllers_Backend_Application
{
    /**
     * @var string
     */
    protected $model = Shopware\Models\Emotion\Emotion::class;

    /**
     * @var string
     */
    protected $alias = 'emotions';

    /**
     * overrides parent method, so it is no possible to create emotions via this controller
     */
    public function createAction()
    {
    }

    /**
     * overrides parent methods, so it is no possible to update emotions via this controller
     */
    public function updateAction()
    {
    }

    /**
     * overrides parent methods, so it is no possible to delete emotions via this controller
     */
    public function deleteAction()
    {
    }
}
