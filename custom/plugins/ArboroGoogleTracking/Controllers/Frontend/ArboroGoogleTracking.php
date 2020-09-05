<?php

class Shopware_Controllers_Frontend_ArboroGoogleTracking extends Enlight_Controller_Action
{
    public function indexAction()
    {
        $articleData = [];
        if($this->Request()
            ->isXmlHttpRequest()
        ) {
            $orderNumber = trim(
                $this->Request()
                    ->get('sAdd')
            );
            $quantity = $this->Request()
                ->get('sQuantity');

            if(!$quantity) {
                $quantity = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('ArboroGoogleTracking', Shopware()->Shop())['fallbackQuantity'];
            }

            if($orderNumber && $quantity) {
                $sql = 'SELECT d.articleID, d.ordernumber, a.supplierID, a.name AS articleName, p.price, s.name AS supplierName FROM s_articles_details AS d LEFT JOIN s_articles AS a ON a.id = d.articleID LEFT JOIN s_articles_prices AS p ON p.articleID = d.articleID LEFT JOIN s_articles_supplier AS s ON s.id = a.supplierID WHERE d.ordernumber = :order';
                $result = Shopware()
                    ->Db()
                    ->query($sql, [':order' => $orderNumber])
                    ->fetchAll();
                $result = $result[0];

                $articleData = [
                    'currencyCode'  => Shopware()
                        ->Shop()
                        ->getCurrency()
                        ->getCurrency(),
                    'ordernumber'   => $orderNumber,
                    'quantity'      => (int) $quantity,
                    'articleName'   => $result['articleName'],
                    'price_numeric' => round($result['price'], 2),
                    'supplierName'  => $result['supplierName'],
                ];
            }
        }

        Shopware()
            ->Plugins()
            ->Controller()
            ->ViewRenderer()
            ->setNoRender();
        echo json_encode($articleData);
    }

    public function sAddAction()
    {
        $articleData = [];
        if($this->Request()
            ->isXmlHttpRequest()
        ) {
            $requestUri = $this->Request()
                ->getRequestUri();
            $orderNumber = str_replace('/ArboroGoogleTracking/sAdd/', '', $requestUri);
            $orderNumber = substr($orderNumber, 0, strpos($orderNumber, '?'));
            $quantity = 1;
            if($orderNumber !== null && $orderNumber !== '') {
                $sql = 'SELECT d.articleID, d.ordernumber, a.supplierID, a.name AS articleName, p.price, s.name AS supplierName FROM s_articles_details AS d LEFT JOIN s_articles AS a ON a.id = d.articleID LEFT JOIN s_articles_prices AS p ON p.articleID = d.articleID LEFT JOIN s_articles_supplier AS s ON s.id = a.supplierID WHERE d.ordernumber = :order';
                $result = Shopware()
                    ->Db()
                    ->query($sql, [':order' => $orderNumber])
                    ->fetchAll();
                $result = $result[0];

                $articleData = [
                    'currencyCode'  => Shopware()
                        ->Shop()
                        ->getCurrency()
                        ->getCurrency(),
                    'ordernumber'   => $orderNumber,
                    'quantity'      => $quantity,
                    'articleName'   => $result['articleName'],
                    'price_numeric' => round($result['price'], 2),
                    'supplierName'  => $result['supplierName'],
                ];
            }
        }

        Shopware()
            ->Plugins()
            ->Controller()
            ->ViewRenderer()
            ->setNoRender();
        echo json_encode($articleData);
    }

    public function removeAction()
    {
        $articleData = [];
        if($this->Request()
            ->isXmlHttpRequest()
        ) {
            $id = $this->Request()
                ->get('id');
            $sql = 'SELECT articlename, articleID, ordernumber, quantity, netprice FROM s_order_basket WHERE id = :id';
            $result = Shopware()
                ->Db()
                ->query($sql, [':id' => $id])
                ->fetchAll();

            if(null !== $result && $result && count($result)) {
                $result = $result[0];

                $supplierSql = 'SELECT s.name AS supplierName FROM s_articles AS a LEFT JOIN s_articles_supplier AS s ON s.id = a.supplierID WHERE a.id = :id';
                $supplier = Shopware()
                    ->Db()
                    ->query($supplierSql, [':id' => $result['articleID']])
                    ->fetchAll();
                $supplier = $supplier[0];

                $articleData = [
                    'currencyCode'  => Shopware()
                        ->Shop()
                        ->getCurrency()
                        ->getCurrency(),
                    'ordernumber'   => $result['ordernumber'],
                    'quantity'      => (int) $result['quantity'],
                    'articleName'   => $result['articlename'],
                    'price_numeric' => round($result['netprice'], 2),
                    'supplierName'  => $supplier['supplierName'],
                ];
            }
        }

        Shopware()
            ->Plugins()
            ->Controller()
            ->ViewRenderer()
            ->setNoRender();
        echo json_encode($articleData);
    }

    public function clickAction()
    {
        $articleData = [];
        if($this->Request()->isXmlHttpRequest())
        {
            $orderNumber = trim($this->Request()->get('ordernumber'));
            $categoryID = trim($this->Request()->get('category'));

            if($orderNumber)
            {
                $sql = 'SELECT d.articleID, d.ordernumber, a.supplierID, a.name AS articleName, p.price, s.name AS supplierName FROM s_articles_details AS d LEFT JOIN s_articles AS a ON a.id = d.articleID LEFT JOIN s_articles_prices AS p ON p.articleID = d.articleID LEFT JOIN s_articles_supplier AS s ON s.id = a.supplierID WHERE d.ordernumber = :order';
                $result = Shopware()
                    ->Db()
                    ->query($sql, [':order' => $orderNumber])
                    ->fetchAll();
                $result = $result[0];

                $articleData = [
                    'currencyCode'  => Shopware()
                        ->Shop()
                        ->getCurrency()
                        ->getCurrency(),
                    'ordernumber'   => $orderNumber,
                    'articleName'   => $result['articleName'],
                    'price_numeric' => round($result['price'], 2),
                    'supplierName'  => $result['supplierName'],
                ];

                if($categoryID) {
                    $categorySql = 'SELECT description FROM s_categories WHERE id = :id';
                    $category = Shopware()
                        ->Db()
                        ->query($categorySql, [':id' => $categoryID])
                        ->fetchAll();
                    $category = $category[0];

                    $articleData['category'] = $category['description'];
                }else {
                    $articleData['category'] = '';
                }
            }
        }

        Shopware()
            ->Plugins()
            ->Controller()
            ->ViewRenderer()
            ->setNoRender();
        echo json_encode($articleData);
    }

    public function cookieSettingsMenuAction() {
        $view = $this->View();
        //$config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('ArboroGoogleTracking', Shopware()->Container()->get('shop'));
    }
}