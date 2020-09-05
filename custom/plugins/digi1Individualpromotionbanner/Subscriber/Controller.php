<?php
    namespace digi1Individualpromotionbanner\Subscriber;

    use Enlight\Event\SubscriberInterface;

    class Controller implements SubscriberInterface {
        public static function getSubscribedEvents(){
            return [
                'Enlight_Controller_Action_PostDispatch_Frontend' => 'postDispatchFrontend'
            ];
        }
        
        public function postDispatchFrontend(\Enlight_Event_EventArgs $args){
            /* get the config parameters */
            $plugin_active = Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'plugin_active');

            if($plugin_active == 1){
                return;
            }
            
            $controller = $args->getSubject();
            
            $request = $controller->Request();

            $response = $controller->Response();
            
            $view = $controller->View();
            
            /* load this code only in frontend */
            if(!$request->isDispatched() || $response->isException() || ($request->getModuleName()!='frontend' && $request->getModuleName()!="widgets")) {
                return;
            }
            
            $base_host_promotionbanner = Shopware()->Config()->Host;
            $controller_name = $request->getControllerName();
            $action_name = $request->getActionName();

            $view->assign('controller_name', $controller_name);
            $view->assign('action_name', $action_name);
            $view->assign('base_host_promotionbanner', $base_host_promotionbanner);
        }
    }