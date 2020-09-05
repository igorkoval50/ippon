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

use Shopware\Models\Shop\Shop;
use SwagBusinessEssentials\Models\CgSettings\CgSettings;

class Shopware_Controllers_Backend_SwagBEPrivateRegister extends Shopware_Controllers_Backend_Application
{
    protected $model = CgSettings::class;
    protected $alias = 'cgSettings';

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
        $builder = $this->getManager()->createQueryBuilder();
        $builder->select($this->alias)
            ->from($this->model, $this->alias)
            ->where($this->alias . '.customerGroup = :customerGroup')
            ->setParameter(':customerGroup', $this->Request()->getParam('customerGroup'));

        return $builder;
    }

    /**
     * Overwrite to remove the type-hint.
     * Additionally adds the link to the register-page.
     *
     * @param array|null $data
     *
     * @return array|null
     */
    protected function getAdditionalDetailData(array $data)
    {
        if ($data) {
            $data['displayLink'] = 'register/index/sValidation/' . $data['customerGroup'];
            $data['link'] = $this->generateLink($data['customerGroup']);
        }

        return $data;
    }

    /**
     * Simulates a shop-instance and generates a link to register for the given customer-group.
     *
     * @param string $customerGroup
     *
     * @return string
     */
    private function generateLink($customerGroup)
    {
        /** @var Shopware\Models\Shop\Repository $shopRepository */
        $shopRepository = $this->getManager()->getRepository(Shop::class);
        $this->get('shopware.components.shop_registration_service')->registerResources(
            $shopRepository->getActiveDefault()
        );

        return $this->get('front')->Router()->assemble([
            'controller' => 'register',
            'sValidation' => $customerGroup,
            'module' => 'frontend',
        ]);
    }
}
