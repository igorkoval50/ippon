<?php
namespace KssSliderArticlesBuy\Subscriber;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Enlight_Template_Manager;

class RegistrTemplateDirectory implements SubscriberInterface
{

    /**
     * @var string
     */
    private $pluginBasePath;

    /**
     * @param string $pluginBasePath
     */
    public function __construct($pluginBasePath)
    {
        $this->pluginBasePath = $pluginBasePath;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecureFrontend',
        ];
    }


    public function onPostDispatchSecureFrontend(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        $view->addTemplateDir(
            $this->pluginBasePath . '/Resources/views'
        );
    }
}