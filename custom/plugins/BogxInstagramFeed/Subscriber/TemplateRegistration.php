<?php

namespace BogxInstagramFeed\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;
use Enlight_Controller_ActionEventArgs;

class TemplateRegistration implements SubscriberInterface
{

    /**
     * @var string
     */
    private $pluginDir;

    /**
     * @var Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * @param Enlight_Template_Manager $templateManager
     * @param string $pluginDir
     */
    public function __construct($pluginDir, Enlight_Template_Manager $templateManager)
    {
        $this->pluginDir = $pluginDir;
        $this->templateManager = $templateManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch' => 'onActionPreDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Emotion' => 'onPostDispatchBackendEmotion'
        ];
    }

    public function onActionPreDispatch()
    {

        $this->templateManager->addTemplateDir($this->pluginDir . '/Resources/views');

    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchBackendEmotion(Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->pluginDir . '/Resources/views');
        $view->extendsTemplate('backend/emotion/bogx_instagram_feed/view/detail/elements/bogx_feed.js');
        $view->extendsTemplate('backend/emotion/bogx_instagram_feed/store/bogx_feed_store.js');
    }


}