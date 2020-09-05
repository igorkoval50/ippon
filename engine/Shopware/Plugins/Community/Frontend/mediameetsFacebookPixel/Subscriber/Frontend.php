<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use Shopware\mediameetsFacebookPixel\Components\PixelData\AdvancedMatching;
use Shopware\mediameetsFacebookPixel\Components\PixelData\CustomerStreams;
use Shopware\mediameetsFacebookPixel\Components\PixelData\Orders;
use Shopware\mediameetsFacebookPixel\Components\PluginConfig;
use Shopware\mediameetsFacebookPixel\Components\PluginSession;
use Shopware\mediameetsFacebookPixel\Components\Template\PluginVar;

class Frontend implements SubscriberInterface
{
    /**
     * Returns the subscribed events and their listeners.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatch',
        ];
    }

    /**
     * Injecting various customer data into the view.
     *
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatch(Enlight_Controller_ActionEventArgs $args)
    {
        $pluginConfig = new PluginConfig();
        $configData = $pluginConfig->get();

        if (
            ! is_array($configData) ||
            is_null($configData['facebookPixelID']) ||
            $configData['facebookPixelID'] == ''
        ) {
            return;
        }

        $session = Shopware()->Session();

        $view = $args->getSubject()->View();

        $pluginTemplateVar = new PluginVar($view);

        // Assign config to plugin view var
        $viewData = $pluginTemplateVar->get();
        $viewData['config'] = $configData;
        $viewData['jsConfig'] = $pluginConfig->prepreJsConfig($configData);
        $pluginTemplateVar->set($viewData);

        // Assign data to plugin view var
        $data = $pluginTemplateVar->getData();

        // Get customer id and prepare customer specific data
        $customerId = $session->get('sUserId');

        if (intval($customerId) > 0) {
            if ($configData['customerStreams'] === true) {
                $data['customerStreams'] = $this->collectCustomerData('customerStreams', $customerId);
            }
            $data['orders'] = $this->collectCustomerData('orders', $customerId);
            $data['advancedMatchingData'] = $this->collectCustomerData('advancedMatchingData', $customerId);
        }

        $data['ratedProduct'] = (new PluginSession())->get('ratedProduct');
        $data['customerHasAccount'] = ! is_null($session->get('sUserId')) && $session->get('sOneTimeAccount') !== true;

        $pluginTemplateVar->setData($data);
    }

    /**
     * Collects data by type and customer id and caches them in session.
     *
     * @param string $type
     * @param int $customerId
     * @return bool|array
     */
    private function collectCustomerData($type, $customerId)
    {
        $supportedTypes = ['advancedMatchingData', 'orders', 'customerStreams'];

        if (! in_array($type, $supportedTypes) || ($customerId = intval($customerId)) == 0) {
            return false;
        }

        $pluginSession = new PluginSession();
        $sessionData = $pluginSession->get();

        if (isset($sessionData[$type])) {
            return $sessionData[$type];
        }

        $data = false;

        switch ($type) {
            case 'advancedMatchingData':
                $advancedMatching = new AdvancedMatching();
                $data = $advancedMatching->getData($customerId);
                break;
            case 'orders':
                $orders = new Orders();
                $data = $orders->getData($customerId);
                break;
            case 'customerStreams':
                $customerStreams = new CustomerStreams();
                $data = $customerStreams->getData($customerId);
                break;
        }

        $pluginSession->set($data, $type);

        return $data;
    }
}
