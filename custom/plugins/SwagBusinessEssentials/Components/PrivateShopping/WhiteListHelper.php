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

namespace SwagBusinessEssentials\Components\PrivateShopping;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight_Event_EventManager as EventManager;
use Shopware_Components_Snippet_Manager as SnippetManager;
use SwagBusinessEssentials\Components\ConfigHelperInterface;

class WhiteListHelper implements WhiteListHelperInterface
{
    /**
     * @var SnippetManager
     */
    private $snippetManager;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    public function __construct(
        SnippetManager $snippetManager,
        EventManager $eventManager,
        ConfigHelperInterface $configHelper
    ) {
        $this->snippetManager = $snippetManager;
        $this->eventManager = $eventManager;
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function isControllerWhiteListed($customerGroup, $controllerName, $actionName)
    {
        if (in_array($controllerName, ['PrivateLogin', 'PrivateRegister', 'error', 'csrftoken'])) {
            return true;
        }

        // Account must be allowed for e.g. password-forgotten or password-reset functions
        if ($controllerName === 'account' && $this->isAllowedAccountAction($actionName)) {
            return true;
        }

        // Register must be allowed when the register-link is provided
        if ($controllerName === 'register' && $this->isAllowedRegisterRequest($actionName)) {
            return true;
        }

        $controllerString = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'whitelistedcontrollers',
            $customerGroup
        );

        if (!$controllerString) {
            return false;
        }

        $controllerArray = explode(',', $controllerString);

        return in_array($controllerName, $controllerArray, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getControllers()
    {
        $controllers = $this->getFrontendControllerList();

        $resultArray = [];
        foreach ($controllers as $key => $controller) {
            $resultArray[] = [
                'key' => $key,
                'name' => $controller,
            ];
        }

        return $this->sort($resultArray);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareWhiteList($controllerString)
    {
        if (!$controllerString) {
            return [];
        }

        $controllerList = [];
        $controllerArray = explode(',', $controllerString);
        $frontendControllers = $this->getFrontendControllerList();

        foreach ($controllerArray as $controllerKey) {
            $controllerName = $frontendControllers[trim($controllerKey)];
            $controllerList[] = [
                'key' => $controllerKey,
                'name' => $controllerName,
            ];
        }

        return $controllerList;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToString($controllers)
    {
        if (!$controllers) {
            return '';
        }

        return implode(',', array_column($controllers, 'key'));
    }

    /**
     * Returns array of all available frontend controllers in the array-schema 'Technical name' => 'Simple controller name'
     *
     * @return array
     */
    private function getFrontendControllerList()
    {
        $namespace = $this->snippetManager->getNamespace('backend/swag_business_essentials/view/main');

        $collection = new ArrayCollection([
            'account' => $namespace->get('ControllerNameAccount'),
            'address' => $namespace->get('ControllerNameAddress'),
            'ajax_search' => $namespace->get('ControllerNameAjaxSearch'),
            'blog' => $namespace->get('ControllerNameBlog'),
            'campaign' => $namespace->get('ControllerNameCampaign'),
            'checkout' => $namespace->get('ControllerNameCheckout'),
            'compare' => $namespace->get('ControllerNameCompare'),
            'custom' => $namespace->get('ControllerNameCustom'),
            'detail' => $namespace->get('ControllerNameDetail'),
            'error' => $namespace->get('ControllerNameError'),
            'forms' => $namespace->get('ControllerNameForms'),
            'index' => $namespace->get('ControllerNameIndex'),
            'listing' => $namespace->get('ControllerNameListing'),
            'newsletter' => $namespace->get('ControllerNameNewsletter'),
            'note' => $namespace->get('ControllerNameNote'),
            'register' => $namespace->get('ControllerNameRegister'),
            'search' => $namespace->get('ControllerNameSearch'),
            'sitemap' => $namespace->get('ControllerNameSiteMap'),
            'sitemapmobilexml' => $namespace->get('ControllerNameSiteMapMobileXml'),
            'sitemapxml' => $namespace->get('ControllerNameSiteMapXml'),
            'tellafriend' => $namespace->get('ControllerNameTellAFriend'),
            'ticket' => $namespace->get('ControllerNameTicket'),
        ]);

        $this->eventManager->collect('SwagBusinessEssentials_Collect_Controllers', $collection);

        return $collection->toArray();
    }

    /**
     * Checks if the action is a valid account-action and must be accessible.
     *
     * @param string $actionName
     *
     * @return bool
     */
    private function isAllowedAccountAction($actionName)
    {
        $actionArray = [
            'login',
            'logout',
            'password',
            'resetPassword',
        ];

        if (!in_array($actionName, $actionArray, true)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the given register request is valid due to the action name or the given params.
     *
     * @param string $actionName
     *
     * @return bool
     */
    private function isAllowedRegisterRequest($actionName)
    {
        if (in_array($actionName, ['saveRegister', 'ajax_validate_email', 'ajax_validate_password'])) {
            return true;
        }

        return false;
    }

    /**
     * Sorts the controllers-array depending on its translated names.
     *
     * @return array
     */
    private function sort(array $controllers)
    {
        $names = [];
        foreach ($controllers as $key => $controller) {
            $names[$key] = $controller['name'];
        }

        array_multisort($names, SORT_ASC, $controllers);

        return $controllers;
    }
}
