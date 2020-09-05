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

// @codingStandardsIgnoreLine
class Shopware_Controllers_Frontend_TlsQuickOrder extends Enlight_Controller_Action
{
    public function addToCartAction()
    {
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
        $this->Response()->setHeader('content-type', 'application/json', true);

        $orderNumbers = $this->request->getParam('orderNumber');
        $quantities = $this->request->getParam('quantity');

        if (!array($orderNumbers) || !array($quantities)) {
            $this->Response()->setBody(json_encode([
                'success' => false,
            ]));
            return;
        }

        $sBasket = $this->get('modules')->Basket();
        $basket = $sBasket->sGetBasketData();

        foreach ($basket["content"] as $position) {
            if (($index = array_search($position["ordernumber"], $orderNumbers)) !== false ) {
                if ($quantities[$index] === '0') {
                    $success = $sBasket->sDeleteArticle($position['id']);
                } else {
                    $success = $sBasket->sUpdateArticle($position['id'], $quantities[$index]);
                }

                if ($success === false) {
                    $this->Response()->setBody(json_encode([
                        'success' => false,
                        'orderNumber' => $position["ordernumber"],
                    ]));
                    return;
                }

                unset($quantities[$index]);
            }
        }

        foreach ($quantities as $index => $quantity) {
            if ($quantity) {
                if (($id = $sBasket->sAddArticle($orderNumbers[$index], $quantity)) === false) {
                    $this->Response()->setBody(json_encode([
                        'success' => false,
                        'orderNumber' => $orderNumbers[$index],
                    ]));
                    return;
                }
            }
        }

        $this->Response()->setBody(json_encode([
            'success' => true,
        ]));
    }
}
