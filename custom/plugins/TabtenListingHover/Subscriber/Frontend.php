<?php

namespace TabtenListingHover\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Frontend implements SubscriberInterface
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PreDispatch_Frontend_Listing' => 'onListing',
            'Enlight_Controller_Action_PreDispatch_Widgets_Listing'  => 'onListing',
        );
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     *
     * @throws \Exception
     */
    public function onListing(\Enlight_Controller_ActionEventArgs $args)
    {
        $shop = false;

        if ($this->container->has('shop')) {
            $shop = $this->container->get('shop');
        }

        if (!$shop) {
            $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->getActiveDefault();
        }

        $controller = $args->getSubject();
        $view       = $controller->View();

        // Plugin configs
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.config_reader')->getByPluginName('TabtenListingHover', $shop);

        // Register template
        $view->addTemplateDir(__DIR__.'/../Resources/views/');

        $view->assign('lhIsActive', $pluginConfig['lhIsActive']);
        $view->assign('lhFadeImage', $pluginConfig['lhFadeImage']);
        $view->assign('lhNoLoadImage', $pluginConfig['lhNoLoadImage']);
        $view->assign('lhProductBoxImgSource', $pluginConfig['lhProductBoxImgSource']);
        $view->assign('lhProductBoxBigImgSource', $pluginConfig['lhProductBoxBigImgSource']);
    }
}