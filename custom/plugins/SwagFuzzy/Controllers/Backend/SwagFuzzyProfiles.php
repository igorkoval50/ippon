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
 * Shopware SwagFuzzy Plugin - SwagFuzzyProfiles Backend Controller
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzyProfiles extends Shopware_Controllers_Backend_Application
{
    /**
     * @var string
     */
    protected $model = SwagFuzzy\Models\Profiles::class;

    /**
     * @var string
     */
    protected $alias = 'synonymGroups';

    /**
     * overrides the parent method, to check if the user wants to delete a standard profile, which is not possible
     *
     * {@inheritdoc}
     */
    public function delete($id)
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'The id parameter contains no value.'];
        }

        /** @var SwagFuzzy\Models\Profiles $model */
        $model = $this->getManager()->find($this->model, $id);

        if (!($model instanceof $this->model)) {
            return ['success' => false, 'error' => 'The passed id parameter exists no more.'];
        }

        if ($model->getStandard()) {
            return ['success' => false, 'error' => 'Standard profiles can not be deleted.'];
        }

        $this->getManager()->remove($model);
        $this->getManager()->flush();

        return ['success' => true];
    }
}
