<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace KibVariantListing\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Backend implements SubscriberInterface
{
    private $pluginPath;

    /**
     * Backend constructor.
     * @param $pluginPath
     */
    public function __construct(
        $pluginPath
    )
    {
        $this->pluginPath = $pluginPath;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Base' => 'onPostDispatchSecureBackendBase',
        );
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     * @return bool
     * @throws \Exception
     */
    public function onPostDispatchSecureBackendBase(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_View_Default $view */
        $view = $args->getSubject()->View();

        $view->addTemplateDir(
            $this->pluginPath . '/Resources/views/kib'
        );

        $view->extendsTemplate('backend/kib_variant_listing/base/attribute/Shopware.attribute.Form-KibVariantListingImgMapping.js');
    }
}
