<?php

/**
 * @copyright Copyright (c) 2013, Pixeleyes Gmbh
 * @author $Author$
 * @package Shopware
 * @subpackage Components_Plugin
 * @creation_date 01.02.2013 15:30
 * @version $Id$
 */
class Shopware_Plugins_Frontend_Pixelvariantlistingswf_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    static protected $userLoggedIn;
    /**
     * The query alias list.
     *
     * @var array
     */
    protected $_sQueryAliasList;


    // getCapabilities
    private $plugin_capabilities = array(
        'install' => true,
        'update' => true,
        'enable' => true,
        'secureUninstall' => true
    );


    /**
     * Repository for the article model.
     * @var \Shopware\Models\Article\Repository
     */
    protected $articlerepository = null;
    /**
     * Internal helper function to get access to the article repository.
     *
     * @return Shopware\Models\Article\Repository
     */
    protected function getArticleRepository()
    {
        if ($this->articlerepository === null) {
            $this->articlerepository = Shopware()->Models()->getRepository('Shopware\Models\Article\Article');
        }

        return $this->articlerepository;
    }


    /**
     * Installs the plugin
     * delete previus entries, create Events, Menues, form, translation
     *
     * @public
     * @return bool
     */
    public function install()
    {

        if ($this->assertMinimumVersion('5')) {


            $this->createEvents();
            $this->createMyForm();
            $this->createMyTranslations();
            $this->sqlInstall();


            // fertig
            return array(
                'success' => true,
                'invalidateCache' => array("frontend", "backend", "config")
            );

        } else {


            return array(
                'success' => false,
                'message' => array("Minimum Shopware Version 5")
            );


        }

    }




    /**
     * Creates and stores the config form.
     * absolute Url
     * Domain
     *
     * @public
     * @return void
     */
    protected function createMyForm()
    {


        $form = $this->Form();



        $form->setElement('select', 'BATCHLISTING',  		array(	'label' => 'Varianten einzeln in den Warenkorb',
            'value'=> '1',
            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
            'description' => 'Wählen Sie aus ob Varianten einzeln in den Warenkorb gelegt werden sollen',
            'required' => true,
            'store' => array(
                array('0', 'Ja'),
                array('1', 'Nein')
            )
        ));

        $form->setElement('select', 'BATCHLISTINGDETAIL',  		array(	'label' => 'Variantenliste detailliert',
            'value'=> '1',
            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
            'description' => 'Wählen Sie aus ob die Variantenliste detailliert oder als Liste angezeigt werden soll',
            'required' => true,
            'store' => array(
                array('0', 'Ja'),
                array('1', 'Nein')
            )
        ));


        $form->setElement('select', 'VARIANTFORM',  		array(	'label' => 'Variantendropdowns anzeigen',
            'value'=> '1',
            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
            'description' => 'Wählen Sie aus ob die Variantenliste detailliert oder als Liste angezeigt werden soll',
            'required' => true,
            'store' => array(
                array('0', 'Ja'),
                array('1', 'Nein')
            )
        ));


        $form->setElement('select', 'LISTINGSORT',  		array(	'label' => 'Sortierung aktivieren (BETA)',
            'value'=> '0',
            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
            'description' => 'Hiermit können Sie die Sortierung beinflussen',
            'required' => true,
            'store' => array(
                array('1', 'Ja'),
                array('0', 'Nein')
            )
        ));




    }

    /**
     * Creates and stores the Translations from Plugin
     *
     * @public
     * @return void
     */
    public function createMyTranslations()
    {
        $form = $this->Form();
        $translations = array(
            'en_GB' => array(

                'BATCHLISTING' => 'Generate batch listing',
                'BATCHLISTINGDETAIL' => 'View as detaillist or list',
                'VARIANTFORM' => 'Variants dropdown deactivate',
            )
        );
        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');
        foreach ($translations as $locale => $snippets) {
            $localeModel = $shopRepository->findOneBy(
                array(
                    'locale' => $locale
                )
            );
            foreach ($snippets as $element => $snippet) {
                if ($localeModel === null) {
                    continue;
                }
                $elementModel = $form->getElement($element);
                if ($elementModel === null) {
                    continue;
                }
                $translationModel = new \Shopware\Models\Config\ElementTranslation();
                $translationModel->setLabel($snippet);
                $translationModel->setLocale($localeModel);
                $elementModel->addTranslation($translationModel);
            }
        }
    }


    /**
     * Create Events from Plugin
     * Subscribes the  Backend and Frontend Functionality
     * @protected
     *
     * @return void
     */
    protected function createEvents()
    {


        /**
         *no description
         */
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_Pixelvariantlistingswf',
            'onGetControllerPathBackend'
        );

        /**
         *no description
         */
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Pixelvariantlistingswf',
            'onGetControllerPathFrontend'
        );


        /**
         *no description
         */
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend_Detail',
            'onPostDispatchDetail'
        );

        /**
         *no description
         */
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Article',
            'postDispatchBackendArticle'
        );


    }

    /**
     * Create Menu Items from Plugin
     *
     * @public
     * @return void
     */
    protected function createMenu()
    {




        $this->createMenuItem(
            array(
                'label' => 'Pixelvariantlisting',
                'controller' => 'Pixelvariantlisting',
                'action' => 'Index',
                'class' => 'sprite-gear',
                'active' => 1,
                'position' => 15,
                'parent' => $this->Menu()->findOneBy(array('label' => 'Artikel'))
            )
        );



    }

    /**
     * Create Find Article Menu to position menuitems from Plugin
     *
     * @public
     * @return void
     */
    protected function findMenuEntry()
    {
        $parent = $this->Menu()->findOneBy('label', 'Inhalte');

        return $parent;
    }



    /**
     * Install the Database Tables
     *
     * @static
     * @param Enlight_Event_EventArgs $args
     * @return bool
     */
    public function sqlInstall()
    {


        try {


            Shopware()->Db()->query("DROP TABLE  IF EXISTS  pix_variantlistingswf_config ;");
            Shopware()->Db()->query(
                "CREATE TABLE IF NOT EXISTS `pix_variantlistingswf_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pix_am_status` int(1) DEFAULT '0', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;"
            );


        } catch (Exception $e) {

        }

        return true;
    }

    /**
     * Uninstall Plugin , Config and Tables
     *
     * @public
     * @return bool
     */
    public function uninstall()
    {



        $this->sqlUninstall();


        return true;
    }

    /**
     * Denstall the Database Tables
     *
     * @static
     * @param Enlight_Event_EventArgs $args
     * @return bool
     */
    public function sqlUninstall()
    {





        try {

            Shopware()->Db()->query("DROP TABLE pix_variantlistingswf_config;");



        } catch (Exception $e) {

        }


        return true;
    }

    /**
     * Get Info version description from Plugin
     *
     * @public
     * @return array
     */
    public function getInfo()
    {


        return array(
            'version' => $this->getVersion(),
            'autor' => 'Pixeleyes GmbH',
            'copyright' => 'Copyright @ 2011, Pixeleyes GmbH',
            'label' => $this->getLabel(),
            'source' => '',
            'description' => file_get_contents($this->Path() . 'info.txt'),
            'licence' => 'commercial',
            'support' => 'http://www.pixeleyes.de',
            'link' => 'http://www.pixeleyes.de',
            'changes' => '',
            'revision' => '1234',
        );


    }

    /**
     * get Version Number from Plugin
     *
     * @public
     * @return string
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);
        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    /**
     *  Get Label from Plugin
     *
     * @return array
     */
    public function getLabel()
    {
        return 'Varianten Listing Shopware 5';
    }

    /**
     * onGetControllerPixelmultioptionenPathFrontend  Eventlistener for Frontend
     * @public
     * @param Enlight_Event_EventArgs $args
     * @return void
     */
    public function onGetControllerDownloadPathFrontend(Enlight_Event_EventArgs $args)
    {




            $this->Application()->Template()->addTemplateDir(
                $this->Path() . '/Views/'
            );



        return dirname(__FILE__) . '/Controllers/Frontend/Pixelvariantlistingswf.php';
    }

    /**
     * onGetControllerMooptionsPathBackend  Eventlistener for Backend Options
     * @public
     * @param Enlight_Event_EventArgs $args
     * @return void
     */
    public function postDispatchBackendIndex(Enlight_Event_EventArgs $args)
    {

        $args->getSubject()->View()->addTemplateDir(
            $this->Path() . 'Views/'
        );



        $args->getSubject()->View()->extendsTemplate(
            'backend/index/pixelvariantlistingswf.tpl'
        );



    }

    /**
     * onGetControllerMooptionsPathBackend  Eventlistener for Backend Options
     * @public
     * @param Enlight_Event_EventArgs $args
     * @return void
     */
    public function onGetControllerPathBackend(Enlight_Event_EventArgs $args)
    {

          $this->registerMyTemplateDir();
        return dirname(__FILE__) . '/Controllers/Backend/Pixelvariantlistingswf.php';
    }

    /**
     * onGetControllerPixelbasketoptionsPathFrontend  Eventlistener for Frontend
     * @public
     * @param Enlight_Event_EventArgs $args
     * @return void
     */
    public function onGetControllerPathFrontend(Enlight_Event_EventArgs $args)
    {


            $this->Application()->Template()->addTemplateDir(
                $this->Path() . '/Views/'
            );


        return dirname(__FILE__) . '/Controllers/Frontend/Pixelvariantlistingswf.php';
    }

    /**
     * register the Plugin Template Directory
     *
     * @protected
     * @return void
     */
    protected function registerMyTemplateDir()
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/',
            'pixelvariantlistingswf'
        );
    }






    /**
     * called after of the sSaveOrder function
     * @static
     * @param Enlight_Hook_HookArgs $arguments
     * @internal param \Enlight_Hook_HookArgs $args
     * @return void
     */
    public function afterGetArticleById(Enlight_Hook_HookArgs $arguments)
    {
        $articleId = $arguments->getId();

        $categoryId = $arguments->get('sCategoryID');

        $sArticle = $arguments->getSubject();

        $articleData = $arguments->getReturn();


        $arguments->setReturn($articleData);
    }


    /**
     * Price Formater for Article, Basket, Order View
     * @return string
     * @preis float
     */
    public function oubersetzer($id,   $art)
    {


        /**
         * Get translation for groups and options
         */
        if (!Shopware()->Shop()->getDefault()) {

            $sql = 'SELECT objectdata FROM s_core_translations WHERE objecttype=? AND objectkey=? AND objectlanguage=?';
            $objectData = Shopware()->Db()->fetchOne($sql, array($art, intval($id), Shopware()->Shop()->getId()));
            if (!empty($objectData)) {
                $objectData = unserialize($objectData);


                $oname = $objectData;
            }


        }

        return $oname;
    }

    /**
     * is called after load ArticleDetail on Frontend
     *
     * @static
     * @param Enlight_Event_EventArgs $args
     * @return void
     */
    public function onPostDispatchDetail(Enlight_Event_EventArgs $args)
    {


        /** @var $action Enlight_Controller_Action */
        $action = $args->getSubject();
        $request = $action->Request();
        $response = $action->Response();
        $view = $action->View();
        $config = $this->Config();


        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend') {
            return;
        }


        if (!empty($view->sArticle['articleID'])) {


            $this->Application()->Template()->addTemplateDir(
                $this->Path() . '/Views/'
            );



            $config = Shopware()->Plugins()->Frontend()->Pixelvariantlistingswf()->Config()->toArray();



            $BATCHLISTING =  $config['BATCHLISTING'];
            $BATCHLISTINGDETAIL =  $config['BATCHLISTINGDETAIL'];


            $pixelvariantlistingparams = $this->EnabledOnArticle($view->sArticle['articleID']);




            if ((int)$pixelvariantlistingparams['pix_am_status'] == 1) {

                $this->registerNamespace();




                $action = $args->getSubject();
                $request = $action->Request();
                $response = $action->Response();
                $view = $action->View();


                $articleId = $view->sArticle['articleID'];




                $product_data = Shopware()->Modules()->sArticles()->sGetArticleById($view->sArticle['articleID']);

                $articleResource = \Shopware\Components\Api\Manager::getResource('article');

                $article = $articleResource->getOne($view->sArticle['articleID'], array( 'considerTaxInput' => true));


                $variants = $this->getVariantsdata($article);




                if(is_array($variants)){


                    if((int)$config['LISTINGSORT'] == 1) {
                        /*
                         * DANK AN Ippon Shop OHG für den Codeschnipsel
                        * AENDERN DER VARIANTEN REIHENFOLGE IM ARRAY START
                        */
                        try{

                            if (array_key_exists("variants", $variants) && is_array($variants["variants"]) &&
                                sizeof($variants["variants"]) > 0 && is_array($variants["variants"][0]) &&
                                array_key_exists("configuratorOptions", $variants["variants"][0]) &&
                                is_array($variants["variants"][0]["configuratorOptions"]) &&
                                sizeof($variants["variants"][0]["configuratorOptions"]) > 0 &&
                                sizeof($variants["variants"][0]["configuratorOptions"]) <= 5) {
                                $tempVariantsDataArray = array();

                                foreach ($variants['variants'] as $tempItem) {
                                    $tempArray = array();

                                    $tempSizeOfItemConfiguratorOptions = sizeof($tempItem["configuratorOptions"]);

                                    for ($tempIndex = 0; $tempIndex < 5; $tempIndex++) {
                                        if ($tempSizeOfItemConfiguratorOptions > $tempIndex) {
                                            $tempArray["sort".$tempIndex] = $tempItem["configuratorOptions"][$tempIndex]["name"];
                                        } else {
                                            $tempArray["sort".$tempIndex] = "";
                                        }
                                    }

                                    $tempArray["number"] = $tempItem["number"];

                                    array_push($tempVariantsDataArray, $tempArray);
                                }

                                foreach ($tempVariantsDataArray as $tempNr => $tempItem) {
                                    $tempSort0[$tempNr] = $tempItem['sort0'];
                                    $tempSort1[$tempNr] = $tempItem['sort1'];
                                    $tempSort2[$tempNr] = $tempItem['sort2'];
                                    $tempSort3[$tempNr] = $tempItem['sort3'];
                                    $tempSort4[$tempNr] = $tempItem['sort4'];
                                }

                                array_multisort($tempSort0, SORT_ASC, $tempSort1, SORT_ASC, $tempSort2, SORT_ASC,
                                    $tempSort3, SORT_ASC, $tempSort4, $tempVariantsDataArray);

                                $tempVariantsIndexArray = array();

                                foreach ($tempVariantsDataArray as $tempNr => $tempItem) {
                                    $tempVariantsIndexArray["".$tempItem["number"]] = $tempNr;
                                }

                                $tempNewVariantsArray = array(sizeof($variants['variants']));

                                foreach ($variants['variants'] as $tempItem) {
                                    $tempNewVariantsArray[$tempVariantsIndexArray["".$tempItem["number"]]] = $tempItem;
                                }

                                $tempNewVariantsPushArray = array();

                                for ($tempIndex = 0; $tempIndex < sizeof($tempNewVariantsArray); $tempIndex++) {
                                    array_push($tempNewVariantsPushArray, $tempNewVariantsArray[$tempIndex]);
                                }

                                $variants['variants'] = $tempNewVariantsPushArray;
                            }

                            //
                            // AENDERN DER VARIANTEN REIHENFOLGE IM ARRAY ENDE
                            //

                        } catch (Exception $e) {

                            $message = sprintf(
                                'Variants-Notify: Exception %s',
                                $e->getMessage()
                            );
                            $context = array('exception' => $e);
                            Shopware()->PluginLogger()->error($message, $context);

                        }
                    }

                $view->assign('pixelvariants',  $variants['variants']);
                $article = $variants['article'];

                }




                $view->Mcurrency = Shopware()->Modules()->Admin()->sSYSTEM->sCurrency;

                $view->mOHash = time();
                $view->larticle = $product_data;
                $view->mArticle = $article;
                $view->pixelvariantlisting = $pixelvariantlistingparams;
                $view->pixelvariantconfig = $config;
                if($BATCHLISTING == '0'){
                    $view->extendsTemplate('frontend/plugins/pixelvariantlistingswf/detail/pixelvariantlisting.tpl');
                }else{

                    if($BATCHLISTINGDETAIL == '0'){
                        $view->extendsTemplate('frontend/plugins/pixelvariantlistingswf/detail/pixelvariantlisting_batchdetail.tpl');
                    }else{

                        $view->extendsTemplate('frontend/plugins/pixelvariantlistingswf/detail/pixelvariantlisting_batch.tpl');
                    }
                }


            }
        }

    }


    private function getVariantsdata($article){


        $variants = array();
        $vcount = 0;



        $variants[$vcount] = $article['mainDetail'];
        $variants[$vcount]["instock"] =  $variants[$vcount]["inStock"] ;
        $variants[$vcount]["maxpurchase"] =  $variants[$vcount]["maxPurchase"] ;
        $variants[$vcount]["minpurchase"] =  $variants[$vcount]["minPurchase"] ;
        $variants[$vcount]["stockmin"] =  $variants[$vcount]["stockMin"] ;
        $variants[$vcount]["shippingfree"] =  $variants[$vcount]["shippingFree"] ;
        $variants[$vcount]["shippingtime"] =  $variants[$vcount]["shippingTime"] ;
        $variants[$vcount]["sReleaseDate"] =  $variants[$vcount]["releaseDate"] ;


        $variants[$vcount]["image"] = Shopware()->Modules()->sArticles()->sGetArticlePictures($variants[$vcount]["articleId"], true, 4, $variants[$vcount]['number']);
        $variants[$vcount]["images"] = Shopware()->Modules()->sArticles()->sGetArticlePictures($variants[$vcount]["articleId"], false, 0, $variants[$vcount]['number']);

        $variants[$vcount]["linkDetails"] = Shopware()->Modules()->Admin(
            )->sSYSTEM->sCONFIG['sBASEFILE'] . "?sViewport=detail&sArticle=" . $variants[$vcount]["articleId"];

        $variants[$vcount]["linkDetailsRewrited"] = Shopware()->Modules()->Admin(
        )->sSYSTEM->sMODULES['sCore']->sRewriteLink($variants[$vcount]["linkDetails"], $variants[$vcount]["name"]);

        foreach($variants[$vcount]['configuratorOptions'] as $gkey => $gval){


            $objectDatas = $this->oubersetzer(
                $variants[$vcount]['configuratorOptions'][$gkey]['id'],
                'configuratoroption'
            );

            if (is_array($objectDatas)) {

                $variants[$vcount]['configuratorOptions'][$gkey]["name"] =  $objectDatas['name'];
            }


        }

        foreach($variants[$vcount]['prices'] as $pkeys => $pval){

            if($variants[$vcount]['prices'][$pkeys]['customerGroupKey'] == Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUP){


                $variants[$vcount]['price'] = $variants[$vcount]['prices'][$pkeys]['price'];
                $variants[$vcount]['net'] = $variants[$vcount]['prices'][$pkeys]['net'];




                if ((Shopware()->Modules()->Admin()->sSYSTEM->sCONFIG['sARTICLESOUTPUTNETTO'] && !Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUPDATA["tax"]) || (!Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUPDATA["tax"] && Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUPDATA["id"])
                ) {
                      $variants[$vcount]['price'] = Shopware()->Modules()->sArticles()->sCalculatingPriceNum(
                        $variants[$vcount]['net'],
                        $article["tax"]["tax"],
                        false,
                        true,
                        $article["taxId"],
                        false
                    );

                } else {


                    $variants[$vcount]['price'] = Shopware()->Modules()->sArticles()->sCalculatingPriceNum(
                        $variants[$vcount]['net'],
                        $article["tax"]["tax"],
                        false,
                        false,
                        $article["taxId"],
                        false
                    );


                }

                break;
            }

        }

        $skey = 0;

        foreach($article['details'] as $key => $val){


            $article['details'][$key] = $article['details'][$key];
            $article['details'][$key]["instock"] =  $article['details'][$key]["inStock"] ;
            $article['details'][$key]["sReleaseDate"] =  $article['details'][$key]["releaseDate"] ;
            $article['details'][$key]["maxpurchase"] =  $article['details'][$key]["maxPurchase"] ;
            $article['details'][$key]["minpurchase"] =  $article['details'][$key]["minPurchase"] ;
            $article['details'][$key]["stockmin"] =  $article['details'][$key]["stockMin"] ;
            $article['details'][$key]["shippingfree"] =  $article['details'][$key]["shippingFree"] ;
            $article['details'][$key]["shippingtime"] =  $article['details'][$key]["shippingTime"] ;
            $article['details'][$key]["sReleaseDate"] =  $article['details'][$key]["releaseDate"] ;



            if($article['details'][$key]["additionalText"]){
                $objectDatas = $this->oubersetzer(
                    $article['details'][$key]['id'],
                    'variant'
                );

                if (is_array($objectDatas)) {


                   // $article['details'][$key]["txtlangbeschreibung"] = $article['details'][$key]["txtlangbeschreibung"];
                   // $article['details'][$key]["txtlangbeschreibung"] = $article['details'][$key]["txtlangbeschreibung"];
                    $article['details'][$key]["additionalText"] =  $objectDatas['txtzusatztxt'];
                }
            }

            $article['details'][$key]["image"] = Shopware()->Modules()->sArticles()->sGetArticlePictures($article['details'][$key]["articleId"], true, 4, $article['details'][$key]['number']);
            $article['details'][$key]["images"] = Shopware()->Modules()->sArticles()->sGetArticlePictures($article['details'][$key]["articleId"], false, 0, $article['details'][$key]['number']);

            $article['details'][$key]["linkDetails"] = Shopware()->Modules()->Admin(
                )->sSYSTEM->sCONFIG['sBASEFILE'] . "?sViewport=detail&sArticle=" . $article['details'][$key]["articleId"];

            $article['details'][$key]["linkDetailsRewrited"] = Shopware()->Modules()->Admin(
            )->sSYSTEM->sMODULES['sCore']->sRewriteLink($article['details'][$key]["linkDetails"], $article['details'][$key]["name"]);


            foreach($article['details'][$key]['configuratorOptions'] as $gkey => $gval){


                    $objectDatas = $this->oubersetzer(
                        $article['details'][$key]['configuratorOptions'][$gkey]['id'],
                        'configuratoroption'
                    );

                    if (is_array($objectDatas)) {

                        $article['details'][$key]['configuratorOptions'][$gkey]["name"] =  $objectDatas['name'];
                    }


            }


            foreach($article['details'][$key]['prices'] as $pkey => $pval){




                if($article['details'][$key]['prices'][$pkey]['customerGroupKey'] == Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUP){


                    $article['details'][$key]['price'] = $article['details'][$key]['prices'][$pkey]['price'];
                    $article['details'][$key]['net'] = $article['details'][$key]['prices'][$pkey]['net'];


                    if ((Shopware()->Modules()->Admin()->sSYSTEM->sCONFIG['sARTICLESOUTPUTNETTO'] && !Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUPDATA["tax"]) || (!Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUPDATA["tax"] && Shopware()->Modules()->Admin()->sSYSTEM->sUSERGROUPDATA["id"])
                    ) {
                        // Brutto is equal to net - price

                        // Consider global discount for net price
                        $article['details'][$key]['price'] = Shopware()->Modules()->sArticles()->sCalculatingPriceNum(
                            $article['details'][$key]['net'],
                            $article["tax"]["tax"],
                            false,
                            true,
                            $article["taxId"],
                            false
                        );

                    } else {


                        $article['details'][$key]['price'] = Shopware()->Modules()->sArticles()->sCalculatingPriceNum(
                            $article['details'][$key]['net'],
                            $article["tax"]["tax"],
                            false,
                            false,
                            $article["taxId"],
                            false
                        );


                    }
                    break;
                }

            }



            $variants[] = $article['details'][$key];

        }


        return array('variants' => $variants, 'article' => $article);



    }




    /**
     * check  if Article has Options and enable them
     *
     * @static
     * @param $id
     * @internal param \Enlight_Event_EventArgs $args
     * @return void
     */
    public function EnabledOnArticle($id)
    {


        $sql = "SELECT *  FROM pix_variantlistingswf_config WHERE  id = ?";

        $getValues = Shopware()->Db()->fetchAll($sql, array($id));

        return $getValues[0];

    }

    /**
     * check  if Article has Options and enable them
     *
     * @static
     * @param $id
     * @internal param \Enlight_Event_EventArgs $args
     * @return void
     */
    public function CheckOnArticle($id)
    {


        $sql = "SELECT id  FROM pix_variantlistingswf_config WHERE  id = ? AND pix_am_status = '1'";

        $getValues = Shopware()->Db()->fetchAll($sql, array($id));


        if (is_array($getValues) and count($getValues) > 0) {


            return true;
        } else {

            return false;

        }


    }



    /**
     * function for register namespace
     *
     * @static
     */
    static public function registerNamespace()
    {
        static $done = false;
        if (!$done) {
            $done = true;

            Shopware()->Loader()->registerNamespace('mOComponents', dirname(__FILE__) . '/Components/');
        }
    }



    /**
     * postDispatchBackendArticle  Eventlistener for Backend
     * extends Article view
     * @public
     * @param Enlight_Event_EventArgs $args
     * @return void
     */
    public function postDispatchBackendArticle(Enlight_Event_EventArgs $args)
    {




        $args->getSubject()->View()->addTemplateDir(
            $this->Path() . 'Views/'
        );

        //if the controller action name equals "load" we have to load all application components.
        if ($args->getRequest()->getActionName() === 'load') {

            $args->getSubject()->View()->extendsTemplate(
                'backend/pixelvariantlistingswf/article/app.js'
            );


            $args->getSubject()->View()->extendsTemplate(
                'backend/pixelvariantlistingswf/article/view/detail/window.js'
            );

        }




        if ($args->getRequest()->getActionName() === 'duplicateArticle') {

            if ($args->getRequest()->has('articleId')) {

                $sqla = "SELECT * from pix_variantlistingswf_config WHERE id = '" . intval(
                        $args->getRequest()->getParam('articleId')
                    ) . "'";
                $egetValues = Shopware()->Db()->fetchAll($sqla);

                if (is_array($egetValues) and count($egetValues) > 0) {

                    $msresult = Shopware()->Db()->fetchAll("SHOW TABLE STATUS LIKE 's_articles'");

                    $insertedId = (int)$msresult[0]['Auto_increment'] - 1;

                    $params = array(
                        intval($insertedId),
                        (int)$egetValues[0]['pix_am_status']
                    );


                    Shopware()->Db()->query(
                        "INSERT INTO `pix_variantlistingswf_config` (`id`, `pix_am_status`) VALUES (?,?);",
                        $params
                    );



                }
            }

        }




    }



    /**
     * UPDATE the Database Tables
     *
     * @static
     * @param string $oldVersion
     * @internal param \Enlight_Event_EventArgs $args
     * @return bool
     */
    public function update($oldVersion)
    {

        $form = $this->Form();
        //Der Parameter $oldVersion gibt die Plugin-Version an, die vor dem Update
        //auf dem System installiert ist. Somit kann unterschieden werden, welche
        //Aktionen noch ausgefuehrt werden muessen.
        // Die neue Version des Plugins kann wie gewohnt ueber $this->getVersion() abgefragt werden.

        //Als Kontrollstruktur bietet sich hier eine Switch an.
        //Durch das Weglassen von breaks der switch k&ouml;nnen Sie
        //den Einstiegspunkt optimal definieren. Fangen Sie hierbei
        //mit der kleinsten Version an, in unserem Beispiel die Version 1.0.0
        switch ($oldVersion) {
            case "1.0.6":
            case "1.0.7":
            case "1.0.8":
            case "1.0.9":
            case "1.1.0":
            case "1.1.1":
            case "1.1.2":


            $form->setElement('select', 'LISTINGSORT',  		array(	'label' => 'Sortierung aktivieren (BETA)',
                'value'=> '0',
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
                'description' => 'Hiermit können Sie die Sortierung beinflussen',
                'required' => true,
                'store' => array(
                    array('1', 'Ja'),
                    array('0', 'Nein')
                )
            ));

            case "1.1.3":

                return true;

                break;




            default:
                //Die installierte Version entspricht weder 1.0.0 noch 1.0.1
                //Aus diesem Grund wird dem Plugin-Manaager mitgeteilt,
                //dass das Update fehlgeschlagen ist.
                return false;

        }

    }


}
