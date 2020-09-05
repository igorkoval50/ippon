<?php
namespace KssPropertiesInListing\Subscriber;


use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;

class Frontend implements SubscriberInterface
{
    private $pluginPath;

    /**
     * Frontend constructor.
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
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecureFrontend',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPostDispatchSecureFrontend',
        );
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchSecureFrontend(Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        $view->addTemplateDir(
            $this->pluginPath . '/Resources/views'
        );
    }
}
