<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagFuzzy\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs as EventArgs;
use Enlight_Controller_Front;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\ShopRegistrationService;
use Shopware\Models\Shop\Shop;

/**
 * Class Backend
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Backend implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginPath;

    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var ShopRegistrationService
     */
    private $registrationService;

    /**
     * Constructor of the subscriber. Initialises the path variable
     *
     * @param string $pluginPath
     */
    public function __construct(
        $pluginPath,
        Enlight_Controller_Front $front,
        ModelManager $modelManager,
        ShopRegistrationService $registrationService
    ) {
        $this->pluginPath = $pluginPath;
        $this->front = $front;
        $this->modelManager = $modelManager;
        $this->registrationService = $registrationService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Backend' => 'onPreDispatchBackend',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index' => 'onPostDispatchIndex',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Analytics' => 'onPostDispatchAnalytics',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Config' => 'onPostDispatchConfig',
            'Shopware_Analytics_SearchTerms' => 'onAnalyticsSearchTerms',
            'Enlight_Controller_Action_PreDispatch_Backend_ProductStream' => 'onPreDispatchProductStream',
        ];
    }

    public function onPreDispatchProductStream(EventArgs $args)
    {
        $request = $args->getSubject()->Request();

        if ($request->getActionName() !== 'loadPreview') {
            return;
        }

        $shopId = (int) $request->getParam('shopId');

        $this->registrationService->registerResources(
            $this->modelManager->getRepository(Shop::class)->find($shopId)
        );
    }

    public function onPreDispatchBackend(EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Index $subject */
        $subject = $args->getSubject();
        $view = $subject->View();

        $view->addTemplateDir($this->pluginPath . '/Resources/views/');
    }

    /**
     * provides the SwagFuzzy logo in the backend
     */
    public function onPostDispatchIndex(EventArgs $args)
    {
        $subject = $args->getSubject();
        $view = $subject->View();

        $view->extendsTemplate('backend/swag_fuzzy/menu_entry.tpl');
    }

    /**
     * extends the analytics module with fuzzy statistics
     */
    public function onPostDispatchAnalytics(EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Analytics $subject */
        $subject = $args->getSubject();
        $request = $subject->Request();
        $view = $subject->View();
        if ($request->getActionName() === 'index') {
            $view->extendsTemplate('backend/analytics/fuzzy_analytics.js');
        }

        if ($request->getActionName() === 'load') {
            $view->extendsTemplate('backend/analytics/store/fuzzy/navigation.js');
            $view->extendsTemplate('backend/analytics/view/table/fuzzy/search.js');
            $view->extendsTemplate('backend/analytics/view/main/fuzzy/toolbar.js');
            $view->extendsTemplate('backend/analytics/controller/fuzzy/main.js');
        }
    }

    /**
     * extends the config module and disables double settings in the search config
     */
    public function onPostDispatchConfig(EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Analytics $subject */
        $subject = $args->getSubject();
        $request = $subject->Request();
        $view = $subject->View();

        if ($request->getActionName() === 'load') {
            $view->extendsTemplate('backend/config/view/form/fuzzy/search.js');
        }
    }

    /**
     * extends the search term query with a search
     */
    public function onAnalyticsSearchTerms(\Enlight_Event_EventArgs $args)
    {
        $searchTerm = $this->front->Request()->getParam('searchTerm');
        $builder = $args->getReturn();

        if (!empty($searchTerm)) {
            $builder->andWhere('search.searchTerm LIKE :searchTerm')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    }
}
