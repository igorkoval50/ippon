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

use SwagBusinessEssentials\Components\PrivateShopping\RedirectParamHelperInterface;
use SwagBusinessEssentials\Components\PrivateShopping\WhiteListHelperInterface;
use SwagBusinessEssentials\Models\PrivateShopping\PrivateShopping;

class Shopware_Controllers_Backend_SwagBEPrivateShopping extends Shopware_Controllers_Backend_Application
{
    protected $model = PrivateShopping::class;
    protected $alias = 'privateShopping';

    /**
     * Returns all available sensible frontend controllers.
     */
    public function getControllersAction()
    {
        /** @var WhiteListHelperInterface $whiteListHelper */
        $whiteListHelper = $this->get('swag_business_essentials.whitelist_helper');

        $this->View()->assign([
            'data' => $whiteListHelper->getControllers(),
        ]);
    }

    /**
     * Overwrites the default "save"-method to convert the whitelist-array to a string.
     *
     * @param array $data
     *
     * @return array
     */
    public function save($data)
    {
        /** @var WhiteListHelperInterface $whiteListHelper */
        $whiteListHelper = $this->get('swag_business_essentials.whitelist_helper');
        $data['whiteListedControllers'] = $whiteListHelper->convertToString($data['whiteListedControllers']);

        /** @var RedirectParamHelperInterface $paramHelper */
        $paramHelper = $this->get('swag_business_essentials.redirect_param_helper');

        $data['redirectLogin'] = $paramHelper->buildQueryString($data['loginControllerAction'], $data['loginParams']);
        $data['redirectRegistration'] = $paramHelper->buildQueryString($data['registerControllerAction'], $data['registerParams']);

        return parent::save($data);
    }

    /**
     * Overwrites the default "getDetailQuery"-method to only show the settings for a given customer-group.
     *
     * @param int $id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getDetailQuery($id)
    {
        /** @var \Doctrine\ORM\QueryBuilder $builder */
        $builder = $this->get('models')->createQueryBuilder();
        $builder->select($this->alias)
            ->from($this->model, $this->alias)
            ->where($this->alias . '.customerGroup = :customerGroup')
            ->setParameter(':customerGroup', $this->Request()->getParam('customerGroup'));

        return $builder;
    }

    /**
     * Overwrite to remove the type-hint.
     *
     * @param array|null $data
     *
     * @return array|null
     */
    protected function getAdditionalDetailData(array $data)
    {
        /** @var WhiteListHelperInterface $whiteListHelper */
        $whiteListHelper = $this->get('swag_business_essentials.whitelist_helper');

        if ($data) {
            $data['whiteListedControllers'] = $whiteListHelper->prepareWhiteList($data['whiteListedControllers']);

            /** @var RedirectParamHelperInterface $redirectParamHelper */
            $redirectParamHelper = $this->get('swag_business_essentials.redirect_param_helper');
            $data = $redirectParamHelper->readRedirectParams($data);
        }

        return $data;
    }
}
