<?php

class Shopware_Plugins_Frontend_StuttSeoRedirects_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    protected $_urlCache = array();

    /**
     * Returns the current version of the plugin.
     * @return string
     */
    public function getVersion()
    {
        return '1.11.1';
    }

    /**
     * Get (nice) name for plugin manager list
     * @return string
     */
    public function getLabel()
    {
        return 'SEO 301 / 302 Redirects';
    }

    /**
     * Get info for plugin manager list
     * @return array
     */
    public function getInfo()
    {
        return array(
            'label' => 'SEO 301 / 302 Redirects',
            'author' => 'STUTTGART MEDIA GmbH',
            'support' => 'shopware@stuttgartmedia.de',
            'link'    => 'https://stuttgartmedia.de/',
            'version' => $this->getVersion(),
        );
    }

    /**
     * Installs the plugin
     *
     * @return array
     * @throws Exception
     */
    public function install()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Router_Route',
            'onEnlightControllerRouteRoute'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Front_PreDispatch',
            'onEnlightControllerFrontPreDispatch'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Front_StartDispatch',
            'onEnlightControllerFrontStartDispatch'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_StuttSeoRedirects',
            'getBackendController'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Api_StuttSeoRedirects',
            'getApiController'
        );

        $this->createMenuItem(array(
            'label' => 'SEO 301/302 Redirects',
            'controller' => 'StuttSeoRedirects',
            'class' => 'sprite-arrow-switch',
            'action' => 'Index',
            'active' => 1,
            'parent' => $this->Menu()->findOneBy(['label' => 'Einstellungen'])
        ));

        $this->createSchema();

        $this->createConfiguration();

        if (!Shopware()->Acl()->hasResourceInDatabase('stuttseoredirects')) {
            Shopware()->Acl()->createResource('stuttseoredirects', array('read', 'create', 'update', 'delete', 'import'), 'SEO-Weiterleitungen', $this->getId());
        }

        return array(
            'success' => TRUE
        );
    }

    /**
     * @return array
     */
    public function enable()
    {
        parent::enable();

        return array(
            'success' => TRUE,
            'invalidateCache' => array('config', 'backend', 'proxy', 'template', 'theme')
        );
    }

    public function uninstall()
    {
        $sql = "SELECT id FROM s_core_acl_resources WHERE name = ?";
        $resourceID = Shopware()->Db()->fetchOne($sql, array('stuttseoredirects'));

        $delete = 'DELETE FROM s_core_acl_resources WHERE id = ?';
        Shopware()->Db()->query($delete, array($resourceID));

        $delete = 'DELETE FROM s_core_acl_privileges WHERE resourceID = ?';
        Shopware()->Db()->query($delete, array($resourceID));

        $delete = 'DELETE FROM s_core_acl_roles WHERE resourceID = ?';
        Shopware()->Db()->query($delete, array($resourceID));

        $this->Plugin()->getMenuItems()->remove(array('label' => 'SEO-Weiterleitungen'));

        return parent::uninstall();
    }

    public function update($oldVersion)
    {
        switch ($oldVersion) {
            case '1.0.0':
                Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN overrideShopUrl tinyint(1) NOT NULL DEFAULT '0';");

                $this->subscribeEvent(
                    'Enlight_Controller_Front_PreDispatch',
                    'onEnlightControllerFrontPreDispatch'
                );

                // fallthrough is intended

            case '1.0.1':
            case '1.0.2':
                Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN temporaryRedirect tinyint(1) NOT NULL DEFAULT '0';");

            case '1.0.3':
            case '1.0.4':
            case '1.1.0':
            case '1.2.0':
            case '1.3.0':
            case '1.3.1':
                $this->Form()->setElement(
                    'boolean', 'caseSensitive',
                    array(
                        'label' => 'Groß-/Kleinschreibung bei URLs beachten',
                        'value' => FALSE
                    )
                );

            case '1.3.2':
                $this->Form()->setElement(
                    'boolean', 'allowWildcards',
                    array(
                        'label' => 'Wildcards / Platzhalter (*) in alten URLs nutzen',
                        'value' => FALSE
                    )
                );

            case '1.4.0':

                if (!Shopware()->Acl()->hasResourceInDatabase('stuttseoredirects')) {
                    Shopware()->Acl()->createResource('stuttseoredirects', array('read', 'create', 'update', 'delete', 'import'), 'SEO-Weiterleitungen', $this->getId());
                }

            case '1.4.1':
            case '1.4.2':
            case '1.5.0':
            case '1.6.0':
            case '1.6.1':
                try {
                    Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN externalRedirect tinyint(1) NOT NULL DEFAULT '0';");
                    Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN shop_id int(11) NULL DEFAULT NULL;");
                } catch (Exception $e) {
                    // ignore, if columns already exist
                }

            case '1.7.0':
                try {
                    Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN gone tinyint(1) NOT NULL DEFAULT '0';");
                } catch (Exception $e) {
                    // ignore, if columns already exist
                }

            case '1.8.0':
            case '1.8.1':
                $this->Form()->setElement(
                    'boolean', 'urlDecode',
                    array(
                        'label' => 'Decode request URLs for use of umlauts etc.',
                        'value' => FALSE
                    )
                );

            case '1.8.2':
            case '1.8.3':
                $this->subscribeEvent(
                    'Enlight_Controller_Dispatcher_ControllerPath_Api_StuttSeoRedirects',
                    'getApiController'
                );

            // fallthrough is intended

            default:
                // let's spread some happiness
                return array(
                    'success' => true,
                    'invalidateCache' => array('config', 'backend', 'proxy', 'template', 'theme')
                );
        }
    }


    /**
     * Creates modul configuration text editor and remove footer logo dropdown
     */
    public function createConfiguration() {
        $form = $this->Form();
        $form->setElement(
            'boolean', 'caseSensitive',
            array(
                'label' => 'Groß-/Kleinschreibung bei URLs beachten',
                'value' => FALSE
            )
        );
        $form->setElement(
            'boolean', 'allowWildcards',
            array(
                'label' => 'Allow wildcards (*) in old URLs',
                'value' => FALSE
            )
        );
        $form->setElement(
            'boolean', 'urlDecode',
            array(
                'label' => 'Decode request URLs for use of umlauts etc.',
                'value' => FALSE
            )
        );

        $translations = array(
            'nl_NL' => array(
                'caseSensitive' => 'Hoofdletters en kleine letters aanteken',
                'allowWildcards' => 'Wildcards (*) in oude URLs gebruiken',
                'urlDecode' => 'Decodeer aanvraag-URL\'s voor gebruik van umlauts etc.',
            ),
            'de_DE' => array(
                'caseSensitive' => 'Groß-/Kleinschreibung bei URLs beachten',
                'allowWildcards' => 'Wildcards / Platzhalter (*) in alten URLs nutzen',
                'urlDecode' => 'Dekodiere Anforderungs-URLs für die Verwendung von Umlauten usw.',
            ),
            'en_GB' => array(
                'caseSensitive' => 'Case-sensitive URLs',
                'allowWildcards' => 'Allow wildcards (*) in old URLs',
                'urlDecode' => 'Decode request URLs for use of umlauts etc.',
            ),
        );
        $translations['nl_BE'] = $translations['nl_NL'];
        $translations['de_AT'] = $translations['de_DE'];
        $translations['de_CH'] = $translations['de_DE'];
        $translations['en_US'] = $translations['en_GB'];

        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');
        foreach ($translations as $locale => $snippets) {
            $localeModel = $shopRepository->findOneBy(array('locale' => $locale));
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
     * Creates the database schema
     */
    protected function createSchema()
    {
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('\Shopware\CustomModels\Stutt\Redirect'),
        );

        try {
            $tool->createSchema($classes);
        } catch (Exception $e) {
            // ignore
        }
    }

    /**
     * Updates the database schema
     */
    protected function updateSchema()
    {
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('\Shopware\CustomModels\Stutt\Redirect'),
        );

        try {
            $tool->updateSchema($classes, TRUE);
        } catch (Exception $e) {
            // ignore
        }
    }

    /**
     *
     * Routes to frontend controller if redirect is hit
     *
     * @param \Enlight_Event_EventArgs $arguments
     *
     * @return array
     */
    public function onEnlightControllerFrontStartDispatch(\Enlight_Event_EventArgs $arguments)
    {
        $this->registerCustomModels();
        /** @var \Enlight_Controller_Front $front */
        $front = $arguments->getSubject();
        $currentUrlFull = $front->Request()->getRequestUri();
        $currentUrlWithoutGetParams = $front->Request()->getPathInfo();

        $redirect = $this->getRedirect($currentUrlFull, $currentUrlWithoutGetParams, Shopware()->Front()->Router()->getContext()->getShopId());

        if (is_object($redirect) && $redirect->getOverrideShopUrl()) {

            if ($redirect->getGone()) {
                header('X-Stutt-Seo-Redirects-StartDispatch: HTTP 410', TRUE);
                $front->Response()->setHttpResponseCode(410);
            }
            else {
                header('X-StuttSeoRedirects-StartDispatch: ' . urlencode($redirect->getNewUrl()), TRUE);
                $front->Response()->setRedirect($redirect->getNewUrl(), $redirect->getTemporaryRedirect() ? 302 : 301);
            }
            $front->Response()->sendResponse();
            exit;
        }
    }

    /**
     *
     * Routes to frontend controller if redirect is hit
     *
     * @param \Enlight_Event_EventArgs $arguments
     *
     * @return array
     */
    public function onEnlightControllerFrontPreDispatch(\Enlight_Event_EventArgs $arguments)
    {
        $this->registerCustomModels();
        /** @var \Enlight_Controller_Front $front */
        $front = $arguments->getSubject();
        $currentUrlFull = $front->Request()->getRequestUri();
        $currentUrlWithoutGetParams = $front->Request()->getPathInfo();

        $redirect = $this->getRedirect($currentUrlFull, $currentUrlWithoutGetParams, Shopware()->Front()->Router()->getContext()->getShopId());

        if (is_object($redirect) && $redirect->getOverrideShopUrl()) {
            if ($redirect->getGone()) {
                header('X-StuttSeoRedirects-PreDispatch: HTTP 410', TRUE);
                $front->Response()->setHttpResponseCode(410);
            }
            else {
                header('X-StuttSeoRedirects-PreDispatch: ' . urlencode($redirect->getNewUrl()), TRUE);
                $front->Response()->setRedirect($redirect->getNewUrl(), $redirect->getTemporaryRedirect() ? 302 : 301);
            }
            $front->Response()->sendResponse();
            exit;
        }
        else {
            header('X-StuttSeoRedirects-PreDispatch: FALSE', TRUE);
        }
    }

    /**
     *
     * Routes to frontend controller if redirect is hit
     *
     * @param \Enlight_Event_EventArgs $arguments
     *
     * @return array
     */
    public function onEnlightControllerRouteRoute(\Enlight_Event_EventArgs $arguments)
    {
        $this->registerCustomModels();

        /** @var $enlightController \Enlight_Controller_Router */
        $enlightController = $arguments->getSubject();

        /** @var $request \Enlight_Controller_Request_RequestHttp */
        $request = $arguments->getRequest();

        $currentUrlFull = $request->getRequestUri();
        $currentUrlWithoutGetParams = $request->getPathInfo();

        $redirect = $this->getRedirect($currentUrlFull, $currentUrlWithoutGetParams, $arguments->getContext()->getShopId());

        if (is_object($redirect) ) {
            $response = new \Enlight_Controller_Response_ResponseHttp();
            if ($redirect->getGone()) {
                header('X-Stutt-Seo-Redirects-Route: HTTP 410', TRUE);
                $response->setHttpResponseCode(410);
            }
            else {
                header('X-Stutt-Seo-Redirects-Route: ' . urlencode($redirect->getNewUrl()), TRUE);
                $response->setRedirect($redirect->getNewUrl(), $redirect->getTemporaryRedirect() ? 302 : 301);
            }
            $response->sendResponse();
            exit;
        }
        else {
            header('X-Stutt-Seo-Redirects-Route: FALSE', TRUE);
        }
    }

    /**
     * Retrieve redirect from database
     *
     * @param $currentUrl
     *
     * @return bool|\Shopware\CustomModels\Stutt\Redirect
     */
    protected function getRedirect($currentUrl, $currentUrlAlternative = '', $shopScope) {

        if (Shopware()->Plugins()->Frontend()->StuttSeoRedirects()->Config()->urlDecode) {
            $currentUrl = urldecode($currentUrl);
            $currentUrlAlternative = urldecode($currentUrlAlternative);
        }

        if (array_key_exists($currentUrl.$currentUrlAlternative, $this->_urlCache)) {
            /** @var \Shopware\CustomModels\Stutt\Redirect $redirect */
            $redirect = $this->_urlCache[$currentUrl.$currentUrlAlternative];
        } else {
            $redirectRepository = Shopware()->Models()->getRepository('Shopware\CustomModels\Stutt\Redirect');
            /** @var \Shopware\CustomModels\Stutt\Redirect $redirect */
            $redirects = array_merge(
                $redirectRepository->findBy(array('active' => 1,  'oldUrl' => $currentUrl)),
                $redirectRepository->findBy(array('active' => 1,  'oldUrl' => $currentUrlAlternative))
            );
            $redirect = FALSE;
            foreach ($redirects as $singleRedirect) {
                if (!$singleRedirect->getShopId() || $singleRedirect->getShopId() == $shopScope) {
                    $redirect = $singleRedirect;
                    break;
                }
            }
        }

        if (
            Shopware()->Plugins()->Frontend()->StuttSeoRedirects()->Config()->allowWildcards
            && !is_object($redirect)
        ) {
            return $this->getWildcard($currentUrl, $currentUrlAlternative, $shopScope);
        }

        if (is_object($redirect) && strlen(trim($redirect->getOldUrl())) > 1) {

            if (
                Shopware()->Plugins()->Frontend()->StuttSeoRedirects()->Config()->caseSensitive
                && $redirect->getOldUrl() != $currentUrl && $redirect->getOldUrl() != $currentUrlAlternative
            ) {
                return FALSE;
            }
            else {
                if ($redirect->getNewUrl() == $currentUrl || $redirect->getNewUrl() == $currentUrlAlternative) {
                    return FALSE;
                }
                else {
                    return $redirect;
                }
            }

        }
        else {
            return FALSE;
        }
    }

    protected function getWildcard($currentUrl, $currentUrlAlternative = '', $shopScope) {
        $redirectRepository = Shopware()->Models()->getRepository('Shopware\CustomModels\Stutt\Redirect');
        $shopScope = (int) $shopScope;
        foreach (Shopware()->Db()->fetchAssoc("SELECT id FROM s_stutt_redirect WHERE active AND (shop_id IS NULL OR shop_id=0 OR shop_id=$shopScope) AND oldUrl LIKE '%*%'") as $redirectRow) {
            /** @var \Shopware\CustomModels\Stutt\Redirect $redirect */
            $redirect = $redirectRepository->findOneById($redirectRow['id']);
            $pattern = '@^' .  str_replace(array('(*)', '*'), '(.*)', $redirect->getOldUrl()) . '$@';
            if (!Shopware()->Plugins()->Frontend()->StuttSeoRedirects()->Config()->caseSensitive) {
                $pattern .= 'i';
            }
            if (preg_match($pattern, $currentUrl) || preg_match($pattern, $currentUrlAlternative)) {
                return $redirect;
            }
        }
        return FALSE;
    }

    /**
     * Propagates the backend controller file
     *
     * @param \Enlight_Event_EventArgs $args
     *
     * @return string
     */
    public function getBackendController(\Enlight_Event_EventArgs $args)
    {
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        $this->Application()->Snippets()->addConfigDir(__DIR__ . '/Snippets/');

        $this->registerCustomModels();

        return $this->Path() . '/Controllers/Backend/StuttSeoRedirects.php';
    }

    /**
     * Propagates the API controller file
     *
     * @param \Enlight_Event_EventArgs $args
     *
     * @return string
     */
    public function getApiController(\Enlight_Event_EventArgs $args)
    {

        if (Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwagMigrationConnector')) {
            return $this->Path() . '/Controllers/Api/StuttSeoRedirects.php';
        }
        else {
            die('Please install and activate the Shopware Migration Connector plugin first.');
        }
    }

    /**
     * Yet unused function to add demo data
     */
    protected function addDemoData()
    {
        $sql = array();
        $csv = @file(__DIR__ . '/demodata.csv');
        foreach ($csv as $csvLine) {
            list($oldUrl, $newUrl) = explode(';', $csvLine);
            if (strlen(trim($oldUrl)) > 0 && strlen(trim($newUrl)) > 0) {
                $sql[] = "('', 1, '" . trim($oldUrl) . "', '" . trim($newUrl) . "')";
            }
        }
        $sql = 'INSERT INTO s_stutt_redirect (id, active, oldUrl, newUrl) VALUES ' . implode(', ', $sql) . ';';

        Shopware()->Db()->query($sql);
    }

    protected function registerCustomModels()
    {
        $externalRedirect = FALSE;
        $shop_id = FALSE;
        $gone = FALSE;

        try {
            $res = Shopware()->Db()->fetchAll('SHOW COLUMNS FROM s_stutt_redirect;');

            foreach ($res as $result) {
                if ($result['Field'] == 'externalRedirect') $externalRedirect = TRUE;
                if ($result['Field'] == 'shop_id') $shop_id = TRUE;
                if ($result['Field'] == 'gone') $gone = TRUE;
            }
        } catch (Exception $e) {
            // ignore
        }

        if (!$externalRedirect) {
            try {
                Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN externalRedirect int(11) NULL DEFAULT NULL;");
            } catch (Exception $e) {
                // ignore, if columns already exist
            }
        }

        if (!$shop_id) {
            try {
                Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN shop_id int(11) NULL DEFAULT NULL;");
            } catch (Exception $e) {
                // ignore, if columns already exist
            }
        }

        if (!$gone) {
            try {
                Shopware()->Db()->exec("ALTER TABLE s_stutt_redirect ADD COLUMN gone tinyint(1) NOT NULL DEFAULT '0';");
            } catch (Exception $e) {
                // ignore, if columns already exist
            }
        }

        parent::registerCustomModels();
    }

}
