<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class FrontendCheckout
 * @package ArboroGoogleTracking\Subscriber
 */
class FrontendCheckout extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Frontend_Checkout';

    /**
     * @param \Enlight_Event_EventArgs $args
     *
     * @throws \InvalidArgumentException
     */
    public function onDispatch($args)
    {
        if(!$this->isLicensed()) {
            return;
        }
        /** @var $action \Enlight_Controller_Action */
        $action = $args->getSubject();

        /** @var \Enlight_Controller_Request_Request $request */
        $request = $action->Request();

        /** @var \Enlight_View_Default view */
        $this->view = $action->View();

        if($this->shouldTrack('enhancedEcommerce')) {
            $actionName = $request->getActionName();
            $this->assign('trackingType', $this->getTrackingType());

            if($this->isTagManager()) {
                $this->assign('dataLayerName');
            }

            $this->assign('checkoutStep', 1);

            if($actionName === 'ajax_add_article') {
                $this->addTemplateToView('checkout/ajax_add_article_emotion.tpl');
            }

            if($actionName === 'shippingPayment') {
                $this->assign('checkoutStep', 3);
                $this->addTemplateToView('checkout/confirm_extended.tpl');
            }

            if($actionName === 'confirm') {
                $this->assign('checkoutStep', 4);
                $this->addTemplateToView('checkout/confirm_extended.tpl');
            }

            if($actionName === 'finish') {
                $this->addTemplateToView('checkout/finish_extended.tpl');

                $this->assign('checkoutStep', 5);
                if($this->getConfigElement('conversionID')) {

                    $this->assign(['enableRemarketing']);

                    $label = $this->getConfigElement('conversionLabel');
                    $label = trim($label) !== '' ? trim($label) : 'purchase';

                    $color = $this->getConfigElement('conversionColor');
                    $color = trim($color) !== '' ? str_replace('#', '', strtolower(trim($color))) : 'ffffff';

                    if($sAmountWithTax = $this->view->sAmountWithTax) {
                        $conversionAmount = $sAmountWithTax;
                    } else {
                        $conversionAmount = $this->view->sAmount;
                    }
                    $conversionAmount = str_replace(',', '.', $conversionAmount);

                    $this->assign(['conversionID', 'conversionFormat']);
                    $this->assign('conversionLanguage', Shopware()->Shop()->getLocale()->getLanguage());
                    $this->assign('conversionLabel', $label);
                    $this->assign('conversionColor', $color);
                    $this->assign('conversionAmount', $conversionAmount);
                    $this->assign('conversionCurrency', Shopware()->Shop()->getCurrency()->getCurrency());

                    $ecomm_prodid = '[';
                    foreach($this->view->getAssign('sBasket')['content'] as $detail) {
                        $ecomm_prodid .= '"'.$detail['ordernumber'].'",';
                    }
                    $ecomm_prodid .= ']';
                    $this->assign('ecomm_prodid', $ecomm_prodid);
                    $this->assign('ecomm_pagetype', 'cart');
                    $this->assign('ecomm_totalvalue', str_replace(',','.', $this->view->getAssign('sBasket')['Amount']));
                }
            }

            if($actionName === 'ajaxCart') {
                $this->addTemplateToView('checkout/ajax_cart_extended.tpl');
            }

            if($actionName === 'cart' && $this->getConfigElement('conversionID')) {
                $this->assign(['enableRemarketing', 'conversionID']);

                $ecomm_prodid = '[';
                foreach($this->view->getAssign('sBasket')['content'] as $detail) {
                    $ecomm_prodid .= '"'.$detail['ordernumber'].'",';
                }
                $ecomm_prodid .= ']';
                $this->assign('ecomm_prodid', $ecomm_prodid);
                $this->assign('ecomm_pagetype', 'cart');
                $this->assign('ecomm_totalvalue', str_replace(',','.', $this->view->getAssign('sBasket')['Amount']));

                $this->addTemplateToView('checkout/cart.tpl');
            }
        }
    }
}
