<?php
/*
Dieser Quellcode, zugehörige Dokumentation und alle damit verbundenen Komponenten unterliegen dem deutschen Urheberrecht und Leistungsschutzrecht.

Der Lizenzgeber und seine Zulieferer sind Inhaber der vollständigen Rechte am Quellcode-Produkt, inklusive aller 
diesbezüglichen Urheberrechte, Patente, Geschäftsgeheimnisse, Marken und anderer Rechte zum Schutze geistigen Eigentums. 
Ihnen ist bekannt, dass der Besitz, die Installation oder die Benutzung der Software (des Quellcodes) keinerlei Ansprüche
auf das geistige Eigentum an der Software begründet, und dass Sie keinerlei Ansprüche an der Software ausser den in der Lizenzvereinbarung 
explizit eingeräumten erwerben. Sie stellt sicher, dass alle eventuell angefertigten Kopien der Software und der zugehörigen 
Dokumentation die entsprechenden Hinweise wie im Originalprodukt enthalten. 

Jede Art der Vervielfältigung, Bearbeitung, Verbreitung, Einspeicherung und jede
Art der Verwertung außerhalb der Grenzen des Urheberrechts bedarf der vorherigen
schriftlichen Zustimmung des Rechteinhabers.

Das geistige Eigentum, die Urheberrechte an dieser Software liegen bei dem Lizenzgeber:
Borucinski Grafix, Inhaber Konrad Borucinski, info@bogx.de, http://bogx.de

Das unerlaubte Kopieren/Speichern, Weitergeben oder Verkaufen dieser Software (dieses Quellcodes) und aller damit verbundenen Komponenten
(auch auszugsweise) ist nicht gestattet und strafbar. Ausgenommen davon sind alle Standard-Komponenten der Software-Umgebung, deren Urherberrechte
bei der Firma Shopware AG oder anderen Software-Herstellern liegen.

Der Lizenznehmer darf den Quellcode ausschliesslich für die eigenen Projektzwecke und nur innerhalb 
seines Unternehmens uneingeschränkt jedoch nicht exklusiv nutzen.

Die Gewährleistung ist in unseren AGB unter http://bogx.de/home/agb/ spezifiziert.
Wir weisen ausdrücklich darauf hin, dass die Gewährleistung mit sofortiger Wirkung entfällt, sobald der Lizenznehmer Änderungen oder
Erweiterungen an dem Quellcode selbst oder durch Dritte vornimmt.

Autor: Konrad Borucinski

Class BogxSubscriber

 */

namespace BogxInstagramFeed\Subscriber;

use Enlight\Event\SubscriberInterface;
//use BogxInstagramFeed\Services\InstagramFeedService;

class BogxSubscriber implements SubscriberInterface
{

    /**
     * @var string
     */
    private $pluginDir;

    //private $instagram;

    /**
     * BogxSubscriber constructor.
     * @param $name
     * @param null $info
     * @throws \Enlight_Exception
     */


    //public function __construct($pluginDir, InstagramFeedService $instagram)
    public function __construct($pluginDir)
    {
        $this->pluginDir = $pluginDir;
        //$this->instagram = $instagram;
    }

    /*
    public function __construct($name, $info = null)
    {
        //$cache           = Shopware()->Cache();
        //$this->instagram = new InstagramFeedService($cache);

        return parent::__construct($name, $info);
    }
    */

    static public function getSubscribedEvents()
    {
        $events = array();

        // Subscribe the needed event for js merge and compression
        //$events['Theme_Compiler_Collect_Plugin_Javascript'] = 'addJsFiles';

        // Subscribe the needed event for less merge and compression
        //$events['Theme_Compiler_Collect_Plugin_Less'] = 'addLessFiles';

        // Subscribe the needed event for all sites & widgets
        //$events['Enlight_Controller_Action_PostDispatchSecure_Frontend'] = 'onFrontendPostDispatch';


        // Subscribe the needed event for load emotion widget
        $events['Shopware_Controllers_Widgets_Emotion_AddElement'] = 'onLoadEmotionWidget';

        return $events;
    }


    /**
     * Provide the file collection for js files
     *
     * @param \Enlight_Event_EventArgs $args
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Exception
     */
    /*
    public function addJsFiles(\Enlight_Event_EventArgs $args)
    {

        //$pluginPath = Shopware()->Container()->get('kernel')->getPlugins()['BogxInstagramFeed']->getPath();
        $jsFiles = array(
            __DIR__ . '/../Resources/views/frontend/_public/src/js/jquery.tosrus.all.min.js',
            //__DIR__ . '/../Resources/views/frontend/_public/src/js/jquery.tosrus.js',
            __DIR__ . '/../Resources/views/frontend/_public/src/js/jquery.bogx-custom.js',
        );
        return new \Doctrine\Common\Collections\ArrayCollection($jsFiles);

    }
    */
    /**
     * Provide the file collection for less
     *
     * @param \Enlight_Event_EventArgs $args
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Exception
     */
    /*
    public function addLessFiles(\Enlight_Event_EventArgs $args)
    {
        //config Variablen
        // Plugin Config über Cached Service (performanter)
        //$config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('BogxInstagramFeed');

        $less = new \Shopware\Components\Theme\LessDefinition(
        //LESS Variablen mit config Values verknüpfen
            array(
                //'bogx-thumb-color' => $config['bogxThumbColor'],
                //'bogx-thumb-size' => $config['bogxThumbSize'] . "px"
            ),
            //less files to compile
            array(
                $this->pluginDir . '/Resources/views/frontend/_public/src/less/all.less'
            ),
            //import directory
            __DIR__);
        return new \Doctrine\Common\Collections\ArrayCollection(array($less));
    }
    */

    /**
     * Post dispatch event of the frontend controller
     *
     * @param \Enlight_Event_EventArgs $arguments
     * @throws \Exception
     */
    public function onFrontendPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        $request = $args->getRequest();
        $controller = $args->getSubject();
        $view = $controller->View();

        //Plugin Templates hinzufügen (und verfügbar machen)
        $view->addTemplateDir($this->pluginDir . '/Resources/views');
        //if ($request->getActionName() === 'index') {
        //    $view->extendsTemplate('frontend/index/header.tpl');
        //}

    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @return mixed
     * @throws \InstagramScraper\Exception\InstagramException
     * @throws \InstagramScraper\Exception\InstagramNotFoundException
     */
    public function onLoadEmotionWidget(\Enlight_Event_EventArgs $args)
    {
        //$controller = $args->getSubject();
        //$view = $controller->View();

        $bogx_instagram_data = $args->getReturn();
        $element = $args->get('element');

        if ('bogx_instagram_feed' !== $element['component']['template']) {
            return $bogx_instagram_data;
        }

        /**
         * Cache Feed Handling
         */
        $cacheTime = $bogx_instagram_data['cache_time'] * 60; //Minuten aus dem Config in Sekunden umrechnen
        //$cacheTime = $bogx_instagram_data['cache_time']; //Minuten aus dem Config in Sekunden umrechnen

        /*
        if (!empty($bogx_instagram_data['cache_suffix'])) {
            $suffix = $bogx_instagram_data['cache_suffix']; //Namens-Suffix
            //Suffix validieren
            $cacheSuffix = $this->makeSuffixValid($suffix);
        } else {
            $cacheSuffix = '';
        }
        $cacheDir = './var/cache/';
        $cacheFileName = "bogx_insta_feed_" . sha1($bogx_instagram_data['username']) . $cacheSuffix . ".json"; //als SHA1-Hash mit .json Erweiterung
        $cacheFilePath = $cacheDir . $cacheFileName;
        */

        /**
         * Wenn Cache-Feed-File bereits existiert und ist nicht älter als die Cache-Zeit im Config,
         * wird das json-Feed aus dem Cache verwendet
         * ansonsten Feed dynamisch aus dem Request/Response holen (scrappen)
         * und Cache-Feed-File neu erstellen/überschreiben mit file_put_contents
         */
        /*
        if (file_exists($cacheFilePath) && filemtime($cacheFilePath) > time() - $cacheTime) {

            $jsonFeed = json_decode(file_get_contents($cacheFilePath), true);

        } else {


            $jsonFeed = $this->instagram->getInstagramData(
                $bogx_instagram_data['username'],
                $bogx_instagram_data['limit'],
                $bogx_instagram_data['hashtags'],
                $bogx_instagram_data['blacklist1'],
                $bogx_instagram_data['blacklist2'],
                $bogx_instagram_data['proxy_ip'],
                $bogx_instagram_data['proxy_port']
            );


            file_put_contents($cacheFilePath, json_encode($jsonFeed));
        }

        $bogx_instagram_data['feed'] = $jsonFeed;
        */

        //$view->assign('bogx_instagram_data', $bogx_instagram_data);
        return $bogx_instagram_data;

    }

    /*
    //Helper Methode
    function makeSuffixValid($suffix) {

        //Kleinschreibung
        $suffix = strtolower($suffix);

        //Sonderzeichen
        $replace = array(
            '/ä/' => 'ae',
            '/ü/' => 'ue',
            '/ö/' => 'oe',
            '/ß/' => 'ss',
            '/\040/' => '_',
            '/[^a-z0-9_\.\-]/' => ''
        );

        //Sonderzeichen entfernen
        $suffix = preg_replace(array_keys($replace), array_values($replace), $suffix);

        return $suffix;
    }
*/
}