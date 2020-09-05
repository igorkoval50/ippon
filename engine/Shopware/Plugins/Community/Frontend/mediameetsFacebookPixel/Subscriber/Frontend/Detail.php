<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use Enlight_View_Default;
use Shopware\mediameetsFacebookPixel\Components\PluginConfig;
use Shopware\mediameetsFacebookPixel\Components\PluginSession;
use Shopware\mediameetsFacebookPixel\Components\Template\PluginVar;
use Shopware\mediameetsFacebookPixel\Components\Traits\ConfigHelpers;
use Shopware\mediameetsFacebookPixel\Components\Traits\WithProductValue;

class Detail implements SubscriberInterface
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
            'Enlight_Controller_Action_PreDispatch_Frontend_Detail' => 'onPreDispatchDetail',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onPostDispatchSecure',
        ];
    }

    /**
     * Adds rating data to session if a product is successfully rated. We need to do this, because the
     * Shopware_Controllers_Frontend_Detail::ratingAction is forwarding to the
     * Shopware_Controllers_Frontend_Detail::indexAction.
     *
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPreDispatchDetail(Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();

        if ($request->getActionName() == 'rating' && $request->isPost()) {
            $postData = $request->getPost();

            $pluginSessionVar = new PluginSession();

            $sessionData = $pluginSessionVar->get();
            $sessionData['ratedProduct']['stars'] = $postData['sVoteStars'];
            $pluginSessionVar->set($sessionData);
        }
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchSecure(Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();

        if ($request->getActionName() == 'index') {
            $this->indexAction($controller->View());
        }
    }

    /**
     * @param Enlight_View_Default $view
     */
    private function indexAction(Enlight_View_Default $view)
    {
        $pluginConfig = (new PluginConfig())->get();

        $pluginTemplateVar = new PluginVar($view);

        $data = $pluginTemplateVar->getData();

        $sArticle = $view->getAssign('sArticle');

        // data.detail.product.value
        $data['detail']['product']['value'] = $this->getProductValue(
            $sArticle['price_numeric'],
            $sArticle['tax']
        );

        // data.detail.product.identifier
        $identifier = $this->isOrderNumberMode($pluginConfig['productIdentifier'])
            ? $sArticle['ordernumber']
            : $sArticle['articleDetailsID'];

        $data['detail']['product']['identifier'] = $identifier;

        $pluginTemplateVar->setData($data);
    }
}
