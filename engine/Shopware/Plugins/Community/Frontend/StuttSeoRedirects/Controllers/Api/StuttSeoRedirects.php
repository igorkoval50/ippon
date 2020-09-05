<?php
use SwagMigrationConnector\Service\ControllerReturnStruct;

class Shopware_Controllers_Api_StuttSeoRedirects extends Shopware_Controllers_Api_Rest
{
    public function indexAction()
    {
        $offset = (int) $this->Request()->getParam('offset', 0);
        $limit = (int) $this->Request()->getParam('limit', 250);

        $results = Shopware()->Db()->fetchAll('SELECT * FROM s_stutt_redirect WHERE 1=1 LIMIT ' . $offset . ',' . $limit);
        $response = new ControllerReturnStruct($results, empty($results));

        $this->view->assign($response->jsonSerialize());
    }
}