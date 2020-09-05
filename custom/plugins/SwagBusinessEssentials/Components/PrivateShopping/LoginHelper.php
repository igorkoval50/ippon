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

use Doctrine\DBAL\Connection as DbalConnection;
use Enlight_Template_Manager as TemplateManager;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Components\Routing\RouterInterface;
use SwagBusinessEssentials\Components\ConfigHelperInterface;

class LoginHelper implements LoginHelperInterface
{
    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TemplateManager
     */
    private $templateManager;

    /**
     * @var DbalConnection
     */
    private $dbalConnection;

    /**
     * @var RedirectParamHelperInterface
     */
    private $redirectParamHelper;

    public function __construct(
        ConfigHelperInterface $configHelper,
        ContextServiceInterface $contextService,
        RouterInterface $router,
        TemplateManager $templateManager,
        DbalConnection $dbalConnection,
        RedirectParamHelperInterface $redirectParamHelper
    ) {
        $this->configHelper = $configHelper;
        $this->contextService = $contextService;
        $this->router = $router;
        $this->templateManager = $templateManager;
        $this->dbalConnection = $dbalConnection;
        $this->redirectParamHelper = $redirectParamHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewVariables()
    {
        $customerGroup = $this->contextService->getShopContext()->getCurrentCustomerGroup()->getKey();

        $viewConfig = [];

        if ($this->isPrivateShoppingActive($customerGroup)) {
            $viewConfig['loginUrl'] = $this->getLoginUrl();
        }

        $viewConfig['showRegister'] = $this->getShowRegister();

        return $viewConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoginTpl()
    {
        $templateLogin = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'templatelogin',
            $this->getCurrentCustomerGroup()
        );

        $path = 'frontend/register';
        $file = 'pslogin.tpl';
        if ($templateLogin) {
            $file = $templateLogin;
        }

        $fullPath = $path . '/' . $file;

        if ($this->templateManager->templateExists($fullPath)) {
            return $fullPath;
        }

        $compatibilityFilePath = 'frontend/b2bessentials/' . $file;

        if ($this->templateManager->templateExists($compatibilityFilePath)) {
            return $compatibilityFilePath;
        }

        return $fullPath;
    }

    /**
     * {@inheritdoc}
     */
    public function isLoginAllowed($currentCustomerGroup)
    {
        $privateShoppingActive = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'activatelogin',
            $currentCustomerGroup
        );

        if (!$privateShoppingActive) {
            return true;
        }

        return (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'unlockafterregister',
            $currentCustomerGroup
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLoginUrl()
    {
        return $this->router->assemble([
            'controller' => 'PrivateLogin',
            'action' => 'login',
            'sTarget' => 'PrivateLogin',
            'sTargetAction' => 'redirectLogin',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUrl()
    {
        $redirectConfig = $this->getRedirectLoginConfig($this->getCurrentCustomerGroup());

        return $this->redirectParamHelper->buildUrlFromRedirectConfig($redirectConfig);
    }

    /**
     * Returns the current customer group.
     *
     * @return string
     */
    private function getCurrentCustomerGroup()
    {
        return $this->contextService->getShopContext()->getCurrentCustomerGroup()->getKey();
    }

    /**
     * Returns the redirect login configuration.
     *
     * @param string $customerGroup
     *
     * @return string
     */
    private function getRedirectLoginConfig($customerGroup)
    {
        $queryBuilder = $this->dbalConnection->createQueryBuilder();

        return $queryBuilder->select('redirectlogin')
            ->from(ConfigHelperInterface::PRIVATE_SHOPPING_TABLE)
            ->where('registergroup = :customerGroup')
            ->setParameter(':customerGroup', $customerGroup)
            ->execute()
            ->fetchColumn();
    }

    /**
     * {@inheritdoc}
     */
    private function isPrivateShoppingActive($customerGroup)
    {
        $isActive = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'activatelogin',
            $customerGroup
        );

        return $isActive;
    }

    /**
     * @return bool
     */
    private function getShowRegister()
    {
        $isActive = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'registerlink',
            $this->getCurrentCustomerGroup()
        );

        return $isActive;
    }
}
