<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class Frontend
 * @package ArboroGoogleTracking\Subscriber
 */
class Frontend extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Frontend';

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

        if($this->isTrackingConfigured()) {
            $actionName = $request->getActionName();
            $controllerName = $request->getControllerName();

            $this->assign('useCollectedJS', true);

            switch($this->getConfigElement('trackingPosition')) {
                case 'head-top':
                    $this->addTemplateToView('index/index_header_top.tpl');
                    break;

                case 'head-bottom':
                    $this->addTemplateToView('index/index_header_bottom.tpl');
                    break;

                case 'body-top':
                    $this->addTemplateToView('index/index_body_top.tpl');
                    break;

                case 'body-bottom':
                    $this->addTemplateToView('index/index_body_bottom.tpl');
                    break;
            }

            /** Add basic tracking information */
            $this->assign('trackingType', $this->getTrackingType());
            $this->assign(['trackingID', 'enhancedEcommerce']);

            //Google Optimize information
            $this->assign('optimizeCID', $this->getConfigElement('optimizeCID'));
            $this->assign('optimizeDataLayerName', $this->getConfigElement('optimizeDataLayerName'));
            $this->assign('optimizeAntiFlickerSnippet', $this->getConfigElement('optimizeAntiFlickerSnippet'));
            $this->assign('optimizeTimeout', $this->getConfigElement('optimizeTimeout'));
            $this->assign('optimizeDisplayConfig', $this->getConfigElement('optimizeDisplayConfig'));
            if($this->getTrackingType() === 'GTM') {
                $this->assign('optimizeUAID', $this->getConfigElement('optimizeUAID'));
                $this->assign('optimizeAnonymizeIp', $this->getConfigElement('optimizeAnonymizeIp'));
                $this->assign('optimizeDisplayFeatures', $this->getConfigElement('optimizeDisplayfeatures'));
                $this->assign('optimizeEnhancedEcommerce', $this->getConfigElement('optimizeEnhancedEcommerce'));
            }

            /** Shopware Cookie Consent Tool */
            $this->assign(['enableCookieConsent']);

            /** If cookie banner is enabled */
            if($this->getConfigElement('enableCookieConsent') === 'agt') {
                $this->addTemplateToView('index/cookie_banner.tpl');
                $this->assign(['cookieBgColor', 'cookieBannerPosition', 'cookieBtnColor', 'cookieBannerMore', 'cookieBannerLink', 'cookieBannerLinkText', 'cookieMenu', 'cookieMenuId', 'cookieBtnInText']);
                $this->assign('cookieAcceptAll', Shopware()->Snippets()->getNamespace('frontend/ArboroGoogleTracking')->get('cookieDismissAll'));
                $this->assign('cookieAcceptTechnical', Shopware()->Snippets()->getNamespace('frontend/ArboroGoogleTracking')->get('cookieDeny'));
            }

            /** If sw cookie banner is enabled */
            if($this->getConfigElement('enableCookieConsent') === 'swcct') {
                //$this->view->extendsTemplate('frontend/_includes/cookie_permission_note.tpl');
                $this->addTemplateToView('_includes/cookie_permission_note_extended.tpl');
                $this->assign(['cookieSwOptimizeView']);
                $this->assign('swCookieDeny', Shopware()->Snippets()->getNamespace('frontend/cookiepermission/index')->get('cookiePermission/declineText'));
            }

            /** If user is logged in, add userId */
            if(null !== $userId = Shopware()->Session()->sUserId) {
                $this->assign('userId', $userId)
                    ->assign('userIdName');
            }

            /** Whether or not load tracking asynchronously */
            $this->assign('loadAsync');

            /** If a google webmaster tools site verification code is configured */
            if($this->getConfigElement('siteVerification') !== '') {
                $this->addTemplateToView('index/site_verification.tpl');
                $this->assign('siteVerification');
            }

            /** If remarketing is enabled */
            if($actionName === 'index' && $controllerName === 'index' && $this->getConfigElement('conversionID')) {
                $this->assign(['enableRemarketing', 'conversionID']);
                $this->assign('ecomm_pagetype', 'home');
                $this->addTemplateToView('home/index.tpl');
            }

            $this->assign(['brandTracking', 'adWordsTracking', 'adWordsDimension']);

            if($this->isAnalytics()) {
                /** If tracking code is for google analytics / universal analytics assign tracking specific variables */
                $this->assign(
                    [
                        'trackBounce',
                        'bounceTime',
                        'forceSSL',
                        'anonymizeIp',
                        'cleanURL',
                        'outboundForm',
                        'outboundLink',
                        'pageVisibility',
                        'socialWidget',
                        'urlChange',
                    ]
                );
                //TODO: refactor this!
                $this->assign('displayFeatures', $this->getConfigElement('displayfeatures'));
            } else {
                if($this->isTagManager()) {
                    /** If tracking code is for google tag manager assign tracking specific variables */
                    $this->assign('dataLayerName');
                }
            }
        }
    }
}
