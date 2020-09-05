<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Subscriber;

use Enlight\Event\SubscriberInterface;

/**
 * Class Backend
 *
 * @package NetiFoundation\Subscriber
 */
class Backend implements SubscriberInterface
{
    /**
     * @var \Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * Backend constructor.
     *
     * @param \Enlight_Template_Manager $templateManager
     */
    public function __construct(
        \Enlight_Template_Manager $templateManager
    ) {
        $this->templateManager = $templateManager;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_Foundation' => 'onGetControllerPathBackend',
            'Enlight_Controller_Action_PostDispatchSecure_Backend'            => 'onPostDispatchSecureBackend',
        );
    }

    public function onPostDispatchSecureBackend(\Enlight_Controller_ActionEventArgs $args)
    {
        $args->getSubject()->View()->extendsTemplate('backend/foundation_menu_item.tpl');
    }

    /**
     * @return string
     */
    public function onGetControllerPathBackend()
    {
        $this->templateManager->addTemplateDir(
            __DIR__ . '/../Views/',
            'NetiFoundation_BackendController'
        );

        return __DIR__ . '/../Controllers/Backend/Foundation.php';
    }
}
