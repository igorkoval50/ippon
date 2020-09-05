<?php

/**
 * This is the Backend Class of the Plugin.
 * It have all Functions for Backend functionality
 *
 *
 * @copyright Copyright (c) 2013, Pixeleyes GmbH
 * @author $Author$
 * @package Shopware
 * @subpackage Components_Plugin
 * @creation_date 01.02.2013 15:30
 * @version $Id$
 */

use Shopware\Components\CSRFWhitelistAware;
class  Shopware_Controllers_Frontend_Pixelvariantlistingswf extends Enlight_Controller_Action implements CSRFWhitelistAware


{


    public $_template;

    /**
     * Shopware View Object (Smarty)
     *
     * @var object
     */
    public $_view;

    /**
     * preDispatch and set View to no render
     * @return void
     */

    /**
     * Reference to sAdmin object (core/class/sAdmin.php)
     *
     * @var sAdmin
     */
    protected $admin;

    /**
     * Reference to sBasket object (core/class/sBasket.php)
     *
     * @var sBasket
     */
    protected $basket;

    /**
     * Reference to Shopware session object (Shopware()->Session)
     *
     * @var Zend_Session_Namespace
     */
    protected $session;

    /**
     * Init method that get called automatically
     *
     * Set class properties
     */
    public function init()
    {
        $this->admin = Shopware()->Modules()->Admin();
        $this->basket = Shopware()->Modules()->Basket();
        $this->session = Shopware()->Session();
    }

    public function getWhitelistedCSRFActions()
    {
        return [

            'index',
            'sendForm',


        ];
    }

    /**
     * Pre dispatch method
     */

    public function preDispatch()
    {

        $this->View()->setScope(Enlight_Template_Manager::SCOPE_PARENT);

        $this->View()->sUserLoggedIn = $this->admin->sCheckUser();
        $this->View()->sUserData = $this->getUserData();

        if (in_array($this->Request()->getActionName(), array('index'))) {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
        }
    }

    /**
     * index action is called if no other action is triggered
     * @return void
     */
    public function indexAction()
    {


    }


    /**
     * is triggered from Articledetailsite
     * calculate all forms and return json
     */
    public function sendFormAction()
    {
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();

        $params = $this->Request()->getParams();

        $data = utf8_decode(urldecode($this->Request()->getParam('data')));

        parse_str($data, $output);


        $pixi = 0;

        foreach ($output['sAdd'] as $key => $value) {


            $ordernumber = $output['sAdd'][$key];
            $quantity = $output['sQuantity'][$key];

            if ($quantity > 0) {
                $articleID = Shopware()->Modules()->Articles()->sGetArticleIdByOrderNumber($ordernumber);

                $InstockInfo[] = array(
                    'sAdd' => $ordernumber,
                    'stockinfo' => $this->getInstockInfo($ordernumber, $quantity)
                );


                if (!empty($articleID)) {
                    $insertID = $this->basket->sAddArticle($ordernumber, $quantity);

                }
            }


        }


        echo utf8_encode(
            json_encode(


                array(
                    'formdata' => $output,
                    'success' => true,
                    'stock' => $InstockInfo 
                )
            )
        );


    }

    /**
     * Get complete user-data as an array to use in view
     *
     * @return array
     */
    public function getUserData()
    {
        $system = Shopware()->System();
        $userData = $this->admin->sGetUserData();


        return $userData;
    }

    /**
     * Used in ajax add cart action
     * Check availability of product and return info / error - messages
     *
     * @param unknown_type $ordernumber article order number
     * @param unknown_type $quantity quantity
     * @return unknown
     */
    public function getInstockInfo($ordernumber, $quantity)
    {
        if (empty($ordernumber)) {
            return Shopware()->Snippets()->getNamespace("frontend")->get(
                'CheckoutSelectVariant',
                'Please select an option to place the required product in the cart',
                true
            );
        }

        $quantity = max(1, (int)$quantity);
        $instock = $this->getAvailableStock($ordernumber);
        $instock['quantity'] += $quantity;

        if (empty($instock['articleID'])) {
            return Shopware()->Snippets()->getNamespace("frontend")->get(
                'CheckoutArticleNotFound',
                'Product could not be found.',
                true
            );
        }
        if (!empty($instock['laststock']) || !empty(Shopware()->Config()->InstockInfo)) {
            if ($instock['instock'] <= 0 && !empty($instock['laststock'])) {
                return Shopware()->Snippets()->getNamespace("frontend")->get(
                    'CheckoutArticleNoStock',
                    'Unfortunately we can not deliver the desired product in sufficient quantity',
                    true
                );
            } elseif ($instock['instock'] < $instock['quantity']) {
                $result = 'Unfortunately we can not deliver the desired product in sufficient quantity. (#0 von #1 in stock).';
                $result = Shopware()->Snippets()->getNamespace("frontend")->get(
                    'CheckoutArticleLessStock',
                    $result,
                    true
                );
                return str_replace(array('#0', '#1'), array($instock['instock'], $instock['quantity']), $result);
            }
        }
        return null;
    }

    /**
     * Get current stock from a certain product defined by $ordernumber
     * Support for multidimensional variants
     *
     * @param unknown_type $ordernumber
     * @return array with article id / current basket quantity / instock / laststock
     */
    public function getAvailableStock($ordernumber)
    {
        $sql = '
            SELECT
                a.id as articleID,
                ob.quantity,
                IF(ad.instock < 0, 0, ad.instock) as instock,
                a.laststock,
                ad.ordernumber as ordernumber
            FROM s_articles a
            LEFT JOIN s_articles_details ad
            ON ad.ordernumber=?
            LEFT JOIN s_order_basket ob
            ON ob.sessionID=?
            AND ob.ordernumber=ad.ordernumber
            AND ob.modus=0
            WHERE a.id=ad.articleID
        ';
        $row = Shopware()->Db()->fetchRow(
            $sql,
            array(
                $ordernumber,
                Shopware()->SessionID(),
            )
        );
        return $row;
    }


}