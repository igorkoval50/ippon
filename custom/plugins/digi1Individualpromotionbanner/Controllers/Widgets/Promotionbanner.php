<?php
    class Shopware_Controllers_Widgets_Promotionbanner extends Enlight_Controller_Action {
        public function indexAction(){
            $active = 1;
            $shop_id = Shopware()->Shop()->getId();
            $showinallshops = 1;
            $position = (int) $this->Request()->getParam('position');
            $promotionbanner_id = (int) $this->Request()->getParam('promotionbanner_id');
            $controllerName = $this->Request()->getParam('controllerName');
            $actionName = $this->Request()->getParam('actionName');
            $targetName = $this->Request()->getParam('targetName');

            if($promotionbanner_id == 0){
                $sql_promotionbanners = '
                SELECT *
                FROM `s_plugin_digi1_individualpromotionbanner`
                WHERE `active` = ? AND (`shop_id` = ? OR `showinallshops` = ?) AND `position` = ? AND (((NOW() > `displaydatefrom`) AND (NOW() < `displaydateto`)) OR ((`displaydatefrom` IS NULL) AND (NOW() < `displaydateto`)) OR ((`displaydateto` IS NULL) AND (NOW() > `displaydatefrom`)) OR (`displaydatefrom` IS NULL AND `displaydateto` IS NULL)) ORDER BY `positionnumber` ASC, `id` ASC';

                $promotionbanners = Shopware()->Db()->fetchAll($sql_promotionbanners, array($active, $shop_id, $showinallshops, $position));
            }else{
                if($position != 0) {
                    $sql_promotionbanners = '
                    SELECT *
                    FROM `s_plugin_digi1_individualpromotionbanner`
                    WHERE `id` = ? AND `active` = ? AND (`shop_id` = ? OR `showinallshops` = ?) AND `position` = ? AND (((NOW() > `displaydatefrom`) AND (NOW() < `displaydateto`)) OR ((`displaydatefrom` IS NULL) AND (NOW() < `displaydateto`)) OR ((`displaydateto` IS NULL) AND (NOW() > `displaydatefrom`)) OR (`displaydatefrom` IS NULL AND `displaydateto` IS NULL)) ORDER BY `positionnumber` ASC, `id` ASC';

                    $promotionbanners = Shopware()->Db()->fetchAll($sql_promotionbanners, array($promotionbanner_id, $active, $shop_id, $showinallshops, $position));
                }else{
                    $sql_promotionbanners = '
                    SELECT *
                    FROM `s_plugin_digi1_individualpromotionbanner`
                    WHERE `id` = ? AND `active` = ? AND (`shop_id` = ? OR `showinallshops` = ?) AND (((NOW() > `displaydatefrom`) AND (NOW() < `displaydateto`)) OR ((`displaydatefrom` IS NULL) AND (NOW() < `displaydateto`)) OR ((`displaydateto` IS NULL) AND (NOW() > `displaydatefrom`)) OR (`displaydatefrom` IS NULL AND `displaydateto` IS NULL)) ORDER BY `positionnumber` ASC, `id` ASC';

                    $promotionbanners = Shopware()->Db()->fetchAll($sql_promotionbanners, array($promotionbanner_id, $active, $shop_id, $showinallshops));
                }
            }

            $promotionbanner_count = count($promotionbanners);

            $this->View()->promotionbanner_count = $promotionbanner_count;
            $this->View()->promotionbanner = $promotionbanners;
            $this->View()->position = $position;
            $this->View()->controllerName = $controllerName;
            $this->View()->actionName = $actionName;
            $this->View()->targetName = $targetName;
        }
    }