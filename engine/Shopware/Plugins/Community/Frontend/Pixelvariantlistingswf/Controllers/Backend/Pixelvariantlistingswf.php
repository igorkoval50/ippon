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
class  Shopware_Controllers_Backend_Pixelvariantlistingswf extends Shopware_Controllers_Backend_ExtJs implements CSRFWhitelistAware


{

    public function getWhitelistedCSRFActions()
    {
        return [

            'index',
            'saveFormOptionsConfig',
            'FormOptionsConfig',
            'getStatuses',
            'getTrueFalse'


        ];
    }


    /**
     * Generate Form for Article Configuration
     * @return json
     */
    public function saveFormOptionsConfigAction()
    {


        $params = $this->Request()->getParams();
        $request = $this->request();


        $locale = 1; // Id der Sprache aus s_core_locales

        $articleId = intval($this->Request()->getParam('articleId'));


        $params = $this->Request()->getParams();
        $request = $this->request();
        $articleId = intval($this->Request()->getParam('articleId'));

        try {


            $sql = "SELECT pix_am_status FROM pix_variantlistingswf_config WHERE  id = ?";

            $_MoOptions = Shopware()->Db()->fetchAll($sql, array(intval($this->Request()->getParam('articleId'))));

            if ($_MoOptions) {

                $parameter = array(
                    (int)$this->Request()->getParam('pix_am_status'),
                    intval($this->Request()->getParam('articleId'))

                );


                Shopware()->Db()->query(
                    "UPDATE pix_variantlistingswf_config set pix_am_status = ? where id = ?",
                    $parameter
                );

            } else {

                $parameter = array(
                    intval($this->Request()->getParam('articleId')),
                    (int)$this->Request()->getParam('pix_am_status')

                );

                Shopware()->Db()->query(
                    "INSERT INTO `pix_variantlistingswf_config` (`id`, `pix_am_status` ) VALUES(?,? );",
                    $parameter
                );


            }

            $this->View()->assign(array("success" => true));

        } catch (Exception $e) {

            $this->View()->assign(array("success" => false));

        }


    }


    /**
     * get all data from article from Database
     * @return json
     */
    public function FormOptionsConfigAction()
    {

        $params = $this->Request()->getParams();
        $request = $this->request();
        $articleId = intval($this->Request()->getParam('articleId'));


        $sql = "SELECT pix_am_status   FROM pix_variantlistingswf_config WHERE  id = ?";

        $_MoOptions = Shopware()->Db()->fetchAll($sql, array($articleId));

        if (!$_MoOptions) {

            $parameter = array(
                $articleId,
                '0'
            );

            Shopware()->Db()->query(
                "INSERT INTO `pix_variantlistingswf_config` (`id`, `pix_am_status` ) VALUES(?,?  );", $parameter
            );


        }

        $sql = "select * from pix_variantlistingswf_config where id = ?";

        $result = Shopware()->Db()->fetchRow($sql, array($articleId));


        $this->View()->assign(array("success" => true, 'data' => $result));


    }


    /**
     * Get Statuses active / deactive for Dropdown Functionality in Forms
     *
     * @public
     * @return json
     */
    public function getStatusesAction()
    {


        $request = $this->request();

        $limit = intval($request->limit);
        $start = intval($request->start);


        $articles = array();

        $articles[] = array('id' => '0', 'text' => 'Inaktiv');
        $articles[] = array('id' => '1', 'text' => 'Aktiv');


        $this->View()->assign(array("success" => true, "items" => $articles, "total" => 2));

    }

    /**
     * Get Statuses true/false for Dropdown Functionality in Forms
     *
     * @public
     * @return json
     */
    public function getTrueFalseAction()
    {

        $request = $this->request();

        $limit = intval($request->limit);
        $start = intval($request->start);


        $articles = array();

        $articles[] = array('id' => '0', 'text' => 'Nein');
        $articles[] = array('id' => '1', 'text' => 'Ja');


        $this->View()->assign(array("success" => true, "items" => $articles, "total" => 2));


    }


}