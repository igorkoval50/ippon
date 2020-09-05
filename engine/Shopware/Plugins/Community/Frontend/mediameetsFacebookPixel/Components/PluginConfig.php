<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components;

use Shopware\Components\DependencyInjection\Container;
use Shopware\mediameetsFacebookPixel\Components\Traits\Helpers;
use Shopware\mediameetsFacebookPixel\Components\Traits\WithShop;

class PluginConfig
{
    use Helpers, WithShop;

    /**
     * @var string
     */
    private $swagCCMPluginName = 'SwagCookieConsentManager';

    /**
     * @var string
     */
    private $pluginName = 'mediameetsFacebookPixel';

    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        $this->container = Shopware()->Container();
    }

    /**
     * @param array $pluginConfig
     * @return array
     */
    public function prepreJsConfig(array $pluginConfig)
    {
        unset(
            $pluginConfig['advancedMatching'],
            $pluginConfig['customerStreams'],
            $pluginConfig['includeShipping'],
            $pluginConfig['priceMode'],
            $pluginConfig['productIdentifier'],
            $pluginConfig['status'],
            $pluginConfig['linkAction']
        );
        return $pluginConfig;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->enrich($this->loadConfig());
    }

    /**
     * @param array $config
     * @return array
     */
    private function enrich(array $config)
    {
        $priceModes = ['gross', 'net'];
        $productIdentifiers = ['ordernumber', 'id'];

        $config['priceMode'] = in_array($config['priceMode'], $priceModes)
            ? $config['priceMode'] : 'gross';

        $config['productIdentifier'] = in_array($config['productIdentifier'], $productIdentifiers)
            ? $config['productIdentifier'] : 'ordernumber';

        $config['includeShipping'] = isset($config['includeShipping'])
            ? boolval($config['includeShipping']) : true;

        $config['advancedMatching'] = isset($config['advancedMatching'])
            ? boolval($config['advancedMatching']) : false;

        $config['autoConfig'] = isset($config['autoConfig'])
            ? boolval($config['autoConfig']) : true;

        $config['customerStreams'] = isset($config['customerStreams'])
            ? boolval($config['customerStreams']) : false;

        $config['status'] = isset($config['status'])
            ? boolval($config['status']) : false;

        $config['linkAction'] = $this->getLinkAction($config);

        $config['shopId'] = $this->getShop()->getId();

        $config['additionalFacebookPixelIDs'] =
            $this->textareaValueToArray($config['additionalFacebookPixelIDs']);

        list($swCookieMode, $swCookieDisplay) = $this->getSWCookieConfig();
        $config['swCookieMode'] = $swCookieMode;
        $config['swCookieDisplay'] = $swCookieDisplay;

        return $config;
    }

    /**
     * @return array
     */
    private function getSWCookieConfig()
    {
        $globalConfig = $this->container->get('config');
        $noteMode = 0;

        /**
         * cookie_note_mode exists since Shopware 5.4.6, so we need to check
         * if config value exists for Shopware >= 5.3.0 and <= 5.4.6
         */
        if ($globalConfig->offsetExists('cookie_note_mode')) {
            $noteMode = $globalConfig->get('cookie_note_mode');
        }

        /**
         * if SwagCookieConsentManager plugin is installed, we get the value
         * from their plugin config
         */
        $swagCCMMode = $this->getModeFromSwagCCMIfInstalled();
        if ($swagCCMMode !== null) {
            $noteMode = $swagCCMMode;
        }

        $showNote = $globalConfig->get('show_cookie_note', 0);

        return [$noteMode, $showNote];
    }

    /**
     * @return int|null
     */
    private function getModeFromSwagCCMIfInstalled()
    {
        $activePlugins = $this->container->getParameter('active_plugins');

        if (! isset($activePlugins[$this->swagCCMPluginName])) {
            return null;
        }

        $swagCCMConfig = $this->loadConfigForPlugin($this->swagCCMPluginName);

        $configKey = 'swag_cookie.cookie_note_mode';
        if (! isset($swagCCMConfig[$configKey])) {
            return null;
        }

        return intval($swagCCMConfig[$configKey]);
    }

    /**
     * Returns link action of current privacy mode.
     *
     * @param array $config
     * @return string
     */
    private function getLinkAction($config)
    {
        $action = '';
        if ($config['privacyMode'] !== 'active') {
            $action = ($config['privacyMode'] == 'optin') ? 'activate' : 'deactivate';
        }

        return $action;
    }

    /**
     * @param string $pluginName
     * @return array
     */
    private function loadConfigForPlugin($pluginName)
    {
        return $this->container
            ->get('shopware.plugin.cached_config_reader')
            ->getByPluginName($pluginName, $this->getShop());
    }

    /**
     * @return array
     */
    private function loadConfig()
    {
        return $this->loadConfigForPlugin($this->pluginName);
    }
}
