<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     rubyc
 */

namespace NetiLanguageDetector\Service;
use NetiFoundation\Service\PluginManager\Config;
use NetiLanguageDetector\NetiLanguageDetector;
use NetiLanguageDetector\Struct\PluginConfig;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Redirect
 *
 * @package NetiLanguageDetector\Service
 */
class Redirect implements RedirectInterface
{
    /**
     * @var Config
     */
    private $configService;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $connection;

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @var \Shopware
     */
    private $application;

    /**
     * @var \Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var Debug
     */
    private $debug;

    /**
     * @var Debug
     * Redirect constructor.
     * @param Config $configService
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $connection
     * @param ModelManager                             $em
     * @param \Enlight_Components_Session_Namespace    $session
     * @param \Shopware $application
     * @param \Shopware_Components_Snippet_Manager     $snippetManager
     * @param \NetiLanguageDetector\Service\UrlHelper  $urlHelper
     */
    public function __construct(
        Config $configService,
        \Enlight_Components_Db_Adapter_Pdo_Mysql $connection,
        ModelManager $em,
        \Enlight_Components_Session_Namespace $session,
        \Shopware $application,
        \Shopware_Components_Snippet_Manager $snippetManager,
        UrlHelper $urlHelper,
        Debug $debug
    ) {
        $this->configService  = $configService;
        $this->connection     = $connection;
        $this->em             = $em;
        $this->session        = $session;
        $this->application      = $application;
        $this->snippetManager = $snippetManager;
        $this->urlHelper      = $urlHelper;
        $this->debug          = $debug;
    }

    /**
     *
     * @return array
     */
    public function getSubshopCountries()
    {
        $configCountries = $this->getConfig()->getCountrycode();
        $shopCountries = array();
        foreach ($configCountries as $value) {
            $shopCountries[] = $value;
        }

        return $shopCountries;
    }

    /**
     * @param string $struserccode
     * @param int    $currentShopId
     *
     * @return bool|Shop
     */
    public function getFittingSubshop($struserccode, $currentShopId)
    {
        $shopRepository = $this->em->getRepository(Shop::class);
        
        // Get all configured shops and languages
        $sql       = 'SELECT `id` FROM `s_core_config_elements` WHERE `name` LIKE \'countrycode\'';
        $elementId = $this->connection->fetchOne($sql);
        $sql       = 'SELECT v.shop_id, v.value, s.main_id
                        FROM s_core_config_values AS v
                        INNER JOIN s_core_shops AS s
                          ON v.shop_id = s.id
                          AND s.active = 1
                        WHERE v.element_id = ?';
        $values    = $this->connection->fetchAll($sql, [$elementId]);

        // Get the current shop model
        $currentShop = $shopRepository->find($currentShopId);

        // Get the current main shop
        $mainShop    = $currentShop->getMain() ?: $currentShop;

        $this->debug->log(\sprintf('Current (main) shop: %d', $currentShop->getId()), ['shop' => $currentShop]);

        // Get all language shops of the current main shop
        $languageShops = $shopRepository->findBy(['main' => $mainShop]);
        $shopIDs       = [$mainShop->getId()];
        foreach ($languageShops as $languageShop) {
            $shopIDs[] = $languageShop->getId();
        }

        $defaultShopId        = null;
        $defaultOverallShopId = null;

        // Search in all language shops for the proper language or find a default
        foreach ($values as $value) {
            $value['shop_id'] = (int) $value['shop_id'];
            if (in_array($value['shop_id'], $shopIDs)) {
                $countries = unserialize($value['value']);
                if (in_array($struserccode, $countries)) {
                    $this->debug->log(\sprintf('Detected shop: %d', $value['shop_id']));

                    return $shopRepository->find($value['shop_id']);
                }
                if (in_array('0', $countries)) {
                    $defaultShopId = $value['shop_id'];
                }
            }
        }

        if (
            $defaultShopId
            && (
                !$this->getConfig()->isSubShopRedirect()
                || !$this->getConfig()->isSearchSubshopsBeforeDefault()
            )
        ) {
            $this->debug->log(\sprintf('Falling back to default shop %d', $defaultShopId));

            return $shopRepository->find($defaultShopId);
        }

        // If activated in the plugin settings, search in all existing shops for the proper language or find a default
        if ($this->getConfig()->isSubShopRedirect()) {
            foreach ($values as $value) {
                $countries = unserialize($value['value']);
                if (in_array($struserccode, $countries)) {
                    $this->debug->log(\sprintf('Detected shop: %d', $value['shop_id']));

                    return $shopRepository->find($value['shop_id']);
                }
                if (in_array('0', $countries)) {
                    $defaultOverallShopId = $value['shop_id'];
                }
            }

            if ($defaultShopId) {
                $this->debug->log(\sprintf('Falling back to default shop: %d', $defaultShopId));

                return $shopRepository->find($defaultShopId);
            }

            if ($defaultOverallShopId) {
                $this->debug->log(\sprintf('Falling back to default shop: %d', $defaultOverallShopId));

                return $shopRepository->find($defaultOverallShopId);
            }
        }

        $this->debug->log('No language shop found.');

        return false;
    }

    /**
     * @param \Enlight_Controller_Response_ResponseHttp $response
     * @param \Enlight_Controller_Request_RequestHttp   $request
     * @param string                                    $struserccode
     * @param string                                    $currentCurrency
     *
     * @return bool
     */
    public function upgradeCurrency($response, $request, $struserccode, $currentCurrency)
    {
        if (true === $this->session->netiLanguageDetector['suppressCurrencyChange']) {
            return false;
        }

        $strusercurcode = $this->getUsersCurrency($struserccode);

        if (! $strusercurcode || $strusercurcode === $currentCurrency){
            $this->session->netiLanguageDetector['suppressCurrencyChange'] = true;

            return false;
        }

        $sql = "SELECT id FROM s_core_currencies WHERE currency = ?";
        $currencyid = (int)$this->connection->fetchOne($sql, array($strusercurcode));

        if (0 === $currencyid) {
            $this->debug->log(\sprintf('Currency "%s" not found in any shops, will not be changed', $strusercurcode));

            return false;
        }

        // Please do not try to inject the "shop" service directly, since this will break the backend!
        $shop                    = $this->application->Shop();
        $availableCurrencies     = $shop->getCurrencies();
        $currencyAvailableInShop = false;

        foreach ($availableCurrencies as $availableCurrency) {
            if ($availableCurrency->getId() === $currencyid) {
                $currencyAvailableInShop = true;
                break;
            }
        }

        if (! $currencyAvailableInShop){
            $this->session->netiLanguageDetector['suppressCurrencyChange'] = true;

            $this->debug->log(\sprintf('Currency "%s" (id: %d) not available in shop', $strusercurcode, $currencyid));

            return false;
        }

        $path = rtrim($shop->getBasePath(), '/') . '/';
        $response->setCookie('currency', (string)$currencyid, 0, $path);

        $this->session->netiLanguageDetector['suppressRedirect']       = null;
        $this->session->netiLanguageDetector['suppressCurrencyChange'] = true;
        session_write_close();

        return $request->has('referrer') ? $request->get('referrer') : false;
    }

    /**
     * get translations for countries
     * @param Shop $shop
     * @return array
     */
    private function getCountryTranslations(Shop $shop)
    {
        // get country translations
        $sql = 'SELECT objectdata
            FROM s_core_translations
            WHERE objecttype = "config_countries"
            AND objectlanguage = ?';
        $object = $this->connection->fetchOne($sql, array($shop->getId()));
        $translations = $object ? unserialize($object) : array();

        return $translations;
    }

    /**
     * Gets all countries from DB, that are assigned to stores
     * @param string $struserccode
     * @param Shop $shop
     * @return array|false countries
     */
    private function getCountryTranslation($struserccode, Shop $shop)
    {
        // get the country
        $sql = "
            SELECT DISTINCT c.`id`, c.`countryname`
            FROM `s_core_countries` AS c
            WHERE c.`countryiso` = ?
        ";
        $countryDE = $this->connection->fetchRow(
            $sql,
            array(
                $struserccode
            )
        );

        // get country translations
        $translations = $this->getCountryTranslations($shop);

        // if translation exists, replace German value
        if (! empty($translations)) {
            $idx = (int) $countryDE['id'];
            if (isset($translations[$idx])) {
                return $translations[$idx]['countryname'];
            }
        }

        return ('de_DE' === $shop->getLocale()->getLocale()) ? $countryDE['countryname'] : false;
    }

    /**
     * Gets currency code from DB, that belongs to users ip
     * @param string $struserccode
     *
     * @return string
     */
    private function getUsersCurrency($struserccode)
    {
        /** getting Country code of the user from the table using the user ipaddress */
        $sql = "select currency_code from s_neti_currencylocation where country_code = ?";
        $strusercur = $this->connection->fetchOne($sql, array($struserccode));

        return $strusercur;
    }

    /**
     * @param NetiLanguageDetector                    $bootstrap
     * @param \Enlight_View_Default                   $view
     * @param \Enlight_Controller_Request_RequestHttp $request
     * @param Shop                                    $shop
     *
     * @return array
     */
    public function prepareModal($bootstrap, $view, $request, $shop)
    {
        // Template extensions
        $view->addTemplateDir(__DIR__ . '/../Views/');
        $view->loadTemplate('frontend/neti_language_detector/user_redirect/modal.tpl');

        $snippets = $this->snippetManager->setShop($shop)
            ->getNamespace('plugins/NetiLanguageDetector/modal');

        // Template contents
        $modaltext  = $snippets->get('modaltext');
        $modaltitle = $snippets->get('modaltitle');
        $buttonyes  = $snippets->get('button_yes');
        $buttonno   = $snippets->get('button_no');

        $newRequestUri = $this->urlHelper->modifyRequestUri(
            $request->getParam('pathname'),
            $request->getParam('search'),
            ['__shop', '__redirect']
        );

        $countryTranslation = $this->getCountryTranslation($bootstrap->struserccode, $shop);

        $cloneView = clone $view;
        $cloneView->assign('countryname', $countryTranslation ? $countryTranslation : $bootstrap->strusercname);
        $modaltext = str_replace('"', '\"', $cloneView->fetch('string:' . $modaltext));

        $netiLanguageDetector = [
            'content'    => $modaltext,
            'title'      => $modaltitle,
            'shopId'     => $shop->getId(),
            'requestUri' => $newRequestUri,
            'follow'     => $buttonyes,
            'stay'       => $buttonno,
        ];

        $view->assign('netiLanguageDetector', $netiLanguageDetector);

        return array(
            'title'   => $modaltitle,
            'content' => $view->render(),
        );
    }

    /**
     * @return bool
     */
    protected function intlAvailable()
    {
        return extension_loaded('intl');
    }

    /**
     * @return PluginConfig
     */
    public function getConfig()
    {
        /**
         * @var PluginConfig $config
         */
        $config = $this->configService->getPluginConfig($this);

        return $config;
    }
}
