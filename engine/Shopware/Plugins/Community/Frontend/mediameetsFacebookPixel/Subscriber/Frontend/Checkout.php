<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use Shopware\mediameetsFacebookPixel\Components\PluginConfig;
use Shopware\mediameetsFacebookPixel\Components\Template\Data\Basket;
use Shopware\mediameetsFacebookPixel\Components\Template\PluginVar;
use Shopware\mediameetsFacebookPixel\Components\Traits\ConfigHelpers;
use Shopware\mediameetsFacebookPixel\Components\Traits\WithProductValue;

class Checkout implements SubscriberInterface
{
    use WithProductValue, ConfigHelpers;

    /**
     * Returns the subscribed events and their listeners.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatchSecure',
        ];
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchSecure(Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $view = $controller->View();
        $request = $controller->Request();

        if ($request->getActionName() == 'cart') {
            if ($view->getAssign('sArticle') === null) {
                return;
            }

            $this->cartAction($args);
        }

        if (in_array($request->getActionName(), ['shippingPayment', 'confirm', 'finish'])) {
            $this->insertBasketData($args);
        }
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    private function insertBasketData(Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();

        $sBasket = $view->getAssign('sBasket');

        if (! is_array($sBasket)) {
            return;
        }

        $pluginConfig = (new PluginConfig())->get();
        $pluginTemplateVar = new PluginVar($view);

        $data = $pluginTemplateVar->getData();

        $basketData = new Basket($sBasket);

        $data['basket']['contents'] = $basketData->getContents($pluginConfig['productIdentifier']);
        $data['basket']['value'] = $basketData->getValue($pluginConfig['includeShipping'], $pluginConfig['priceMode']);

        $pluginTemplateVar->setData($data);
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    private function cartAction(Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();

        $sArticle = $view->getAssign('sArticle');

        $pluginConfig = (new PluginConfig())->get();
        $pluginTemplateVar = new PluginVar($view);

        $data = $pluginTemplateVar->getData();

        // data.detail.product.value
        $data['checkout']['product']['value'] = $this->getProductValue(
            $sArticle['price_numeric'],
            $sArticle['tax']
        );

        // data.detail.product.identifier
        $identifier = $this->isOrderNumberMode($pluginConfig['productIdentifier'])
            ? $sArticle['ordernumber']
            : $sArticle['articleDetailsID'];

        $data['checkout']['product']['identifier'] = $identifier;

        $pluginTemplateVar->setData($data);
    }
}
