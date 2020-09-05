<?php
    class Shopware_Controllers_Backend_Promotionbanner extends Shopware_Controllers_Backend_Application {
        protected $model = 'digi1Individualpromotionbanner\Models\Promotionbanner';
        protected $alias = 'promotionbanner';

	    protected function getListQuery(){
            $builder = parent::getListQuery();
	
            $builder->leftJoin('promotionbanner.shop', 'shop');
            $builder->addSelect(array('shop'));
		
            return $builder;
	    }
		
	    protected function getDetailQuery($id){
            $builder = parent::getDetailQuery($id);
	
            $builder->leftJoin('promotionbanner.shop', 'shop');
            $builder->addSelect(array('shop'));

            return $builder;
	    }
        
        public function copyPromotionbannerAction(){
            try {
                $promotionbannerId = $this->Request()->getParam('promotionbannerId');

                $select = " 
                INSERT INTO `s_plugin_digi1_individualpromotionbanner` (`active`, `positionnumber`, `label`, `collapsible`, `collapsiblecookielifetime`, `hidecollapseicon`, `collapseiconbackgroundcolor`, `collapseiconfontcolor`, `position`, `shop_id`, `showinallshops`, `showoncontroller`, `cssclass`, `modalboxtimedelay`, `backgroundimage`, `backgroundposition`, `backgroundsize`, `backgroundcolor`, `backgroundopacity`, `percentagebackgroundcolor`, `percentagefontcolor`, `percentagecssclass`, `percentagealignment`, `percentagewidth`, `percentagepadding`, `percentage`, `contentbackgroundcolor`, `contentpadding`, `contentcssclass`, `headlinefontcolor`, `headlinealignment`, `headlinewidth`, `headline`, `txtfontcolor`, `txtalignment`, `txtwidth`, `txt`, `completelinking`, `linkbelowcontent`, `linkbackgroundcolor`, `linkpadding`, `target`, `link`, `linkcssclass`, `linktransparent`, `linkbgcolor`, `linkfontcolor`, `linkbordercolor`, `linktext`, `linkalignment`, `linkwidth`, `displaydatefrom`, `displaydateto`, `hideinsmartphoneportrait`, `hideinsmartphonelandscape`, `hideintabletportrait`, `hideintabletlandscape`, `hideindesktop`) SELECT `active`, `positionnumber`, `label`, `collapsible`, `collapsiblecookielifetime`, `hidecollapseicon`, `collapseiconbackgroundcolor`, `collapseiconfontcolor`, `position`, `shop_id`, `showinallshops`, `showoncontroller`, `cssclass`, `modalboxtimedelay`, `backgroundimage`, `backgroundposition`, `backgroundsize`, `backgroundcolor`, `backgroundopacity`, `percentagebackgroundcolor`, `percentagefontcolor`, `percentagecssclass`, `percentagealignment`, `percentagewidth`, `percentagepadding`, `percentage`, `contentbackgroundcolor`, `contentpadding`, `contentcssclass`, `headlinefontcolor`, `headlinealignment`, `headlinewidth`, `headline`, `txtfontcolor`, `txtalignment`, `txtwidth`, `txt`, `completelinking`, `linkbelowcontent`, `linkbackgroundcolor`, `linkpadding`, `target`, `link`, `linkcssclass`, `linktransparent`, `linkbgcolor`, `linkfontcolor`, `linkbordercolor`, `linktext`, `linkalignment`, `linkwidth`, `displaydatefrom`, `displaydateto`, `hideinsmartphoneportrait`, `hideinsmartphonelandscape`, `hideintabletportrait`, `hideintabletlandscape`, `hideindesktop` FROM `s_plugin_digi1_individualpromotionbanner` WHERE `id` = ?;";
                
                Shopware()->Db()->query($select, array($promotionbannerId));

                $this->View()->assign(
                    array(
                        'success' => true
                    )
                );
            } catch (Exception $e) {
                $this->View()->assign(
                    array(
                        'success' => false,
                        'error' => $e->getMessage()
                    )
                );
            }
        }
    }