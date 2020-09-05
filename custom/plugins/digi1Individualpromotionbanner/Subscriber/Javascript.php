<?php
    namespace digi1Individualpromotionbanner\Subscriber;

    use Enlight\Event\SubscriberInterface;
    use Doctrine\Common\Collections\ArrayCollection;

    class Javascript implements SubscriberInterface {
        public static function getSubscribedEvents(){
            return [
               'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles'
            ];
        }

        /**
         * Provide the needed javascript files
         *
         * @param \Enlight_Event_EventArgs $args
         * @return ArrayCollection
         */
        public function addJsFiles(\Enlight_Event_EventArgs $args){
            $jsPath = array(
                dirname(__DIR__) . '/Resources/views/responsive/frontend/_public/src/js/jquery.promotionbanner.js'
            );
		
            return new ArrayCollection($jsPath);
        }
    }