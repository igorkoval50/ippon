<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace KibVariantListing\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Bundle\StoreFrontBundle\Service\CategoryServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Components\Plugin\ConfigReader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Frontend implements SubscriberInterface
{
    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * @var ConfigReader
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    private $pluginPath;
    private $pluginName;

    /**
     * Frontend constructor.
     * @param ContextServiceInterface $contextService
     * @param CategoryServiceInterface $categoryService
     * @param ConfigReader $config
     * @param ContainerInterface $container
     * @param $pluginPath
     * @param $pluginName
     */
    public function __construct(
        ContextServiceInterface $contextService,
        CategoryServiceInterface $categoryService,
        ConfigReader $config,
        ContainerInterface $container,
        $pluginPath,
        $pluginName
    )
    {
        $this->contextService = $contextService;
        $this->categoryService = $categoryService;
        $this->config = $config;
        $this->container = $container;
        $this->pluginPath = $pluginPath;
        $this->pluginName = $pluginName;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecureFrontend',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPostDispatchSecureFrontend',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Listing' => 'onEvent',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Search' => 'onEvent',
            'Enlight_Controller_Action_PreDispatch_Widgets_Listing' => 'onEvent',
            'Enlight_Controller_Action_PostDispatchSecure_Widgets_Listing' => 'onEvent',
            'Enlight_Controller_Action_PostDispatchSecure_Widgets_Emotion' => 'onEvent',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onEvent',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Blog' => 'onEvent',
        );
    }

    public function onPostDispatchSecureFrontend(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_View_Default $view */
        $view = $args->getSubject()->View();

        $view->addTemplateDir(
            $this->pluginPath . '/Resources/views/kib'
        );
    }

    public function onEvent(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        /** @var \Enlight_Controller_Request_Request $request */
        $request = $args->getRequest();

        $config = $this->config->getByPluginName($this->pluginName, $this->container->get('Shop'));

        $showVariants = $config['enableVariantsListing'];
        $textVariants = $config['textVariants'];
        $slideOut = $config['variantSlideout'];
        $zoomCover = $config['zoomCover'];
        $dropDown = $config['viewDropdown'];

        if ($showVariants) {
            if ($request->getActionName() === 'top_seller') {
                $showVariants = $config['enableVariantsTopseller'];
            } else if ($request->getModuleName() === 'widgets' && $request->getControllerName() === 'emotion') {
                $showVariants = $config['enableVariantsEmotion'];
            } else if ($request->getControllerName() === 'search' && $request->getActionName() === 'defaultSearch') {
                $showVariants = $config['enableVariantsSearch'];
            } else if ($request->getControllerName() === 'listing' && $request->getActionName() === 'manufacturer') {
                $showVariants = $config['enableVariantsManufacturer'];
            } else if ($request->getControllerName() === 'detail' && $request->getActionName() === 'index') {
                $showVariants = $config['enableVariantsDetail'];
            }
        }

        if (strcasecmp($request->getControllerName(), 'listing') === 0 &&
            ($request->getActionName() === 'index' ||
                ($request->getActionName() === 'ajaxListing' && $args->getName() === 'Enlight_Controller_Action_PostDispatchSecure_Widgets_Listing') ||
                ($request->getActionName() === 'listingCount' && $args->getName() === 'Enlight_Controller_Action_PreDispatch_Widgets_Listing')
            )
        ) {
            $shopContext = $this->contextService->getShopContext();
            $categoryId = $args->getRequest()->getParam('sCategory');

            /** @var Struct\Category $category */
            $category = $this->categoryService->get($categoryId, $shopContext);

            if ($category != null) {
                $coreAttribute = $category->getAttribute('core');

                if ($coreAttribute != null) {
                    $showVariants = $coreAttribute->get('kib_variantlisting') === '2' || $coreAttribute->get('kib_variantlisting') === null ? $showVariants : boolval($coreAttribute->get('kib_variantlisting'));
                    $slideOut = $coreAttribute->get('kib_variantlisting_slideout') === '2' || $coreAttribute->get('kib_variantlisting_slideout') === null ? $slideOut : boolval($coreAttribute->get('kib_variantlisting_slideout'));
                    $textVariants = $coreAttribute->get('kib_variantlisting_textvariants') === '2' || $coreAttribute->get('kib_variantlisting_textvariants') === null ? $textVariants : boolval($coreAttribute->get('kib_variantlisting_textvariants'));
                    $zoomCover = $coreAttribute->get('kib_variantlisting_zoomcover') === '2' || $coreAttribute->get('kib_variantlisting_zoomcover') === null ? $zoomCover : boolval($coreAttribute->get('kib_variantlisting_zoomcover'));
                    $dropDown = $coreAttribute->get('kib_variantlisting_viewdropdown') === '2' || $coreAttribute->get('kib_variantlisting_viewdropdown') === null ? $dropDown : boolval($coreAttribute->get('kib_variantlisting_viewdropdown'));
                }
            }
        }

        $pluginConfig = array(
            'enableVariantsInListing' => $showVariants,
            'slideOut' => $slideOut,
            'textVariants' => $textVariants,
            'zoomCover' => $zoomCover,
            'viewDropdown' => $dropDown,
            'numberOfVariants' => $config['numberOfVariants'],
            'slideVariants' => $config['slideVariants'],
            'thumbnailRef' => $config['thumbnailRef'],
            'showInactive' => $config['showInactive'],
            'titleFallback' => $config['titleFallback'],
            'maxConfiguratorLevel' => $config['maxConfiguratorLevel'],
            'minVariants' => $config['minVariants'],
            'variantCoverDelay' => $config['variantCoverDelay'],
        );

        $view->assign('KibVariantListing', $pluginConfig);

        if ($request->getActionName() !== 'ajaxListing' && $request->getActionName() !== 'listingCount') {
            $view->extendsTemplate('frontend/plugins/kib_variant_listing/listing/box_article.tpl');
        }
    }
}
