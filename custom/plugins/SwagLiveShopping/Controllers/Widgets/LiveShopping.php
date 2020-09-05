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

use SwagLiveShopping\Components\LiveShoppingInterface;
use SwagLiveShopping\Components\LiveShoppingServiceInterface;

class Shopware_Controllers_Widgets_LiveShopping extends Enlight_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->Front()->Plugins()->Json()->setRenderer();
    }

    /**
     * Enlight controller action function.
     *
     * This function is used to refresh the live shopping data on the product detail page.
     * The function expects the live shopping id of the displayed live shopping product.
     */
    public function getLiveShoppingDataAction()
    {
        $liveShoppingId = (int) $this->Request()->getParam('liveShoppingId');
        if ($liveShoppingId === null || $liveShoppingId === 0) {
            $this->View()->assign('success', false);

            return;
        }

        /** @var LiveShoppingInterface $component */
        $component = $this->get('swag_liveshopping.live_shopping');
        $liveShopping = $component->getActiveLiveShoppingById($liveShoppingId);

        $data = $component->getLiveShoppingArrayData($liveShopping);

        $this->View()->assign(['success' => !empty($data), 'data' => $data]);
    }

    public function getLiveShoppingListingDataAction()
    {
        $ids = $this->request->get('liveShoppingIds');
        $context = $this->container->get('shopware_storefront.context_service')->getContext();
        $liveShoppingService = $this->container->get(LiveShoppingServiceInterface::class);

        $this->View()->assign([
            'success' => true,
            'data' => $liveShoppingService->getLiveShoppingList($ids, $context->getCurrentCustomerGroup()),
        ]);
    }
}
