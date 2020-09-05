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
use Shopware\Models\Shop\Shop;
use SwagFuzzy\Components\SynonymService;

/**
 * Class Frontend
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Frontend implements SubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Search' => 'onFrontendSearch',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_AjaxSearch' => 'onFrontendSearch',
        ];
    }

    /**
     * Extends frontend search template
     * adds blocks to show similar results and synonym groups options
     */
    public function onFrontendSearch(\Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $view = $controller->View();
        /** @var SynonymService $synonymService */
        $synonymService = $controller->get('swag_fuzzy.synonym_service');
        /** @var Shop $shop */
        $shop = $controller->get('shop');

        $term = $args->getRequest()->getParam('sSearch');

        $synonymGroups = $synonymService->getSynonymGroups($term, $shop->getId());

        $hasEmotion = false;

        foreach ($synonymGroups as $synonymGroup) {
            if ($synonymGroup['normalSearchEmotionId'] != 0) {
                $hasEmotion = true;
                break;
            }
        }

        $view->assign('swagFuzzySynonymGroups', $synonymGroups);
        $view->assign('hasEmotion', $hasEmotion);
    }
}
