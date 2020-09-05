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
use Shopware\mediameetsFacebookPixel\Components\Template\PluginVar;
use Shopware\mediameetsFacebookPixel\Components\Traits\ConfigHelpers;

class Listing implements SubscriberInterface
{
    use ConfigHelpers;

    /**
     * Returns the subscribed events and their listeners.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Listing' => 'onPostDispatchSecure',
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

        if ($request->getActionName() == 'index') {
            if ($view->getAssign('manufacturer') !== null) {
                return;
            }

            $this->indexAction($args);
        }

        if ($request->getActionName() == 'manufacturer') {
            $this->manufacturerAction($args);
        }
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    private function manufacturerAction(Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();

        $pluginTemplateVar = new PluginVar($view);

        $data = $pluginTemplateVar->getData();
        $data['manufacturer']['contents'] = $this->collectContents($view->getAssign('sArticles'));
        $pluginTemplateVar->setData($data);
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    private function indexAction(Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();

        $pluginTemplateVar = new PluginVar($view);
        $data = $pluginTemplateVar->getData();

        $breadcrumb = [];
        foreach ($view->getAssign('sBreadcrumb') as $breadcrumbItem) {
            $breadcrumb[] = $breadcrumbItem['name'];
        }

        $data['listing']['categoryPath'] = implode(' > ', $breadcrumb);

        $data['listing']['contents'] = $this->collectContents($view->getAssign('sArticles'));

        $pluginTemplateVar->setData($data);
    }

    /**
     * @param array $sArticles
     * @return array
     */
    private function collectContents($sArticles)
    {
        $pluginConfig = (new PluginConfig())->get();

        $identifier = $this->isOrderNumberMode($pluginConfig['productIdentifier'])
            ? 'ordernumber'
            : 'articleDetailsID';

        $contents = [];
        foreach ($sArticles as $article) {
            $contents[] = ['id' => $article[$identifier], 'quantity' => 1];
        }
        return $contents;
    }
}
