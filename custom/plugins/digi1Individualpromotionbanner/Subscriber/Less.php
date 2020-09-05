<?php
    namespace digi1Individualpromotionbanner\Subscriber;

    use Enlight\Event\SubscriberInterface;
    use Doctrine\Common\Collections\ArrayCollection;
    use Shopware\Components\Theme\LessDefinition;

    class Less implements SubscriberInterface {
        public static function getSubscribedEvents(){
            return [
               'Theme_Compiler_Collect_Plugin_Less' => 'addLessFiles'
            ];
        }

        /**
         * Provide the needed less files
         *
         * @param \Enlight_Event_EventArgs $args
         * @return LessDefinition[]|ArrayCollection
         */
        public function addLessFiles(\Enlight_Event_EventArgs $args){
            $less = new LessDefinition(
		        //configuration
		        [
                    'promotionbanner_backgroundcolor_collapseicon' => Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'promotionbanner_backgroundcolor_collapseicon'),
                    'promotionbanner_fontcolor_collapseicon' => Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'promotionbanner_fontcolor_collapseicon'),
                    'promotionbanner_modalbox_width' => Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'promotionbanner_modalbox_width'),
                    'promotionbanner_modalbox_closeicon_backgroundcolor' => Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'promotionbanner_modalbox_closeicon_backgroundcolor'),
                    'promotionbanner_modalbox_closeicon_fontcolor' => Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'promotionbanner_modalbox_closeicon_fontcolor')
                ],
		        //less files to compile
		        array(
                    dirname(__DIR__) . '/Resources/views/responsive/frontend/_public/src/less/all.less'
		        ),
		        //import directory
		        dirname(__DIR__)
            );
            
            return new ArrayCollection(array($less));
        }
    }