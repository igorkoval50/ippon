<?php
/**
 * Copyright (c) TreoLabs GmbH
 *
 * This Software is the property of TreoLabs GmbH and is protected
 * by copyright law - it is NOT Freeware and can be used only in one project
 * under a proprietary license, which is delivered along with this program.
 * If not, see <https://treolabs.com/eula>.
 *
 * This Software is distributed as is, with LIMITED WARRANTY AND LIABILITY.
 * Any unauthorised use of this Software without a valid license is
 * a violation of the License Agreement.
 *
 * According to the terms of the license you shall not resell, sublicense,
 * rent, lease, distribute or otherwise transfer rights or usage of this
 * Software or its derivatives. You may modify the code of this Software
 * for your own needs, if source code is provided.
 */

namespace TlsQuickOrder;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;

class TlsQuickOrder extends Plugin
{
    public function install(InstallContext $context)
    {
        $this->addAttribute();
    }

    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function uninstall(UninstallContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);

        if (!$context->keepUserData()) {
            $this->removeAttribute();
        }
    }

    private function addAttribute()
    {
        $crud = $this->container->get('shopware_attribute.crud_service');
        $em = $this->container->get('models');

        $crud->update('s_articles_attributes', 'tls_quick_order_disable', TypeMapping::TYPE_BOOLEAN, [
            'displayInBackend' => true,
            'position' => 150,
        ]);

        $metaDataCache = $em->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        $em->generateAttributeModels(['s_articles_attributes']);
    }

    private function removeAttribute()
    {
        $crud = $this->container->get('shopware_attribute.crud_service');
        $em = $this->container->get('models');

        if ($crud->get('s_articles_attributes', 'tls_quick_order_disable')) {
            $crud->delete('s_articles_attributes', 'tls_quick_order_disable');
        }

        $em->generateAttributeModels(['s_articles_attributes']);
    }
}
