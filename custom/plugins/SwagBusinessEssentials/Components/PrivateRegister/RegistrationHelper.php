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

namespace SwagBusinessEssentials\Components\PrivateRegister;

use Doctrine\DBAL\Connection as DbalConnection;
use Enlight_Template_Manager as TemplateManager;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Routing\RouterInterface;
use Shopware\Models\Shop\Shop;
use Shopware\Models\Shop\Template;
use SwagBusinessEssentials\Components\ConfigHelperInterface;
use SwagBusinessEssentials\Components\PrivateShopping\RedirectParamHelperInterface;

class RegistrationHelper implements RegistrationHelperInterface
{
    /**
     * @var DbalConnection
     */
    private $dbalConnection;

    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * @var TemplateManager
     */
    private $templateManager;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RedirectParamHelperInterface
     */
    private $redirectParamHelper;

    public function __construct(
        DbalConnection $dbalConnection,
        ConfigHelperInterface $configHelper,
        TemplateManager $templateManager,
        ModelManager $modelManager,
        RouterInterface $router,
        RedirectParamHelperInterface $redirectParamHelper
    ) {
        $this->dbalConnection = $dbalConnection;
        $this->configHelper = $configHelper;
        $this->templateManager = $templateManager;
        $this->modelManager = $modelManager;
        $this->router = $router;
        $this->redirectParamHelper = $redirectParamHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function isRegistrationAllowed($customerGroupKey, $shopId)
    {
        if ($customerGroupKey === 'H') {
            return true;
        }

        if ($this->isDefaultCustomerGroup($customerGroupKey, $shopId)) {
            return true;
        }

        if ($this->isPrivateRegisterConfigured($customerGroupKey)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($customerGroup)
    {
        $template = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'registertemplate',
            $customerGroup
        );

        if (!$template) {
            return false;
        }

        $path = 'frontend/register/' . $template;

        if ($this->templateManager->templateExists($path)) {
            return $path;
        }

        $path .= '.tpl';

        if ($this->templateManager->templateExists($path)) {
            return $path;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetUrl($customerGroup, Shop $shop)
    {
        $defaultAssemble = [
            'controller' => 'register',
            'action' => 'saveRegister',
        ];

        if (!$this->isPrivateShoppingActive($shop->getCustomerGroup()->getKey())) {
            return [];
        }

        if ($this->isConfirmationNeeded($customerGroup, $shop)) {
            $defaultAssemble['sTarget'] = 'PrivateRegister';
            $defaultAssemble['sTargetAction'] = 'registerConfirm';

            return $this->router->assemble($defaultAssemble);
        }

        if ($this->hasRedirectParams($customerGroup)) {
            $defaultAssemble['sTarget'] = 'PrivateRegister';
            $defaultAssemble['sTargetAction'] = 'registerRedirect';

            return $this->router->assemble($defaultAssemble);
        }

        return $this->getConfiguredTargetParams($customerGroup);
    }

    /**
     * {@inheritdoc}
     */
    public function registerTheme($customerGroup, Shop $shop)
    {
        $themeId = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'templateafterlogin',
            $customerGroup
        );

        if (!$themeId) {
            return;
        }

        /** @var \Shopware\Models\Shop\Template $theme */
        $theme = $this->modelManager->getRepository(Template::class)->find($themeId);
        $shop->setTemplate($theme);
    }

    /**
     * {@inheritdoc}
     */
    public function isConfirmationNeeded($validationCustomerGroup, Shop $shop)
    {
        $customerGroup = $shop->getCustomerGroup()->getKey();

        $privateShoppingActive = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'activatelogin',
            $customerGroup
        );

        if (!$privateShoppingActive) {
            return false;
        }

        $activationNecessary = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'requireunlock',
            $validationCustomerGroup
        );

        if (!$activationNecessary) {
            $noEntryFound = $activationNecessary === false;
            if ($validationCustomerGroup !== 'H' || !$noEntryFound) {
                return false;
            }
        }

        if ($this->isTemporaryGroupAllowed($validationCustomerGroup)) {
            return false;
        }

        $earlyShopAccess = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'unlockafterregister',
            $customerGroup
        );

        return !$earlyShopAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationCustomerGroup($customerId)
    {
        $builder = $this->dbalConnection->createQueryBuilder();

        $customerGroup = $builder->select('customer.validation')
            ->from('s_user', 'customer')
            ->where('customer.id = :customerId')
            ->setParameter(':customerId', $customerId)
            ->execute()
            ->fetchColumn();

        if (empty($customerGroup)) {
            return '';
        }

        return $customerGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUrl($customerGroup)
    {
        $redirectConfig = $this->getRedirectRegistrationConfig($customerGroup);

        return $this->redirectParamHelper->buildUrlFromRedirectConfig($redirectConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisterDataFromAssign($customerGroup, $register)
    {
        if (!$register) {
            $register = [];
        }

        $registerGroup = $this->getRegisterGroup($customerGroup);
        $register = array_replace_recursive([
            'personal' => [
                'sValidation' => $registerGroup,
            ],
        ], $register);

        return $register;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisterGroup($customerGroup)
    {
        return $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'registergroup',
            $customerGroup
        );
    }

    /**
     * Checks if the given customer group is the default customer group of the shop related to the given id.
     *
     * @param string $customerGroupKey
     * @param int    $shopId
     *
     * @return bool
     */
    private function isDefaultCustomerGroup($customerGroupKey, $shopId)
    {
        $builder = $this->dbalConnection->createQueryBuilder();

        $defaultCustomerGroupKey = $builder->select('cGroup.groupkey')
            ->from('s_core_shops', 'shop')
            ->innerJoin('shop', 's_core_customergroups', 'cGroup', 'shop.customer_group_id = cGroup.id')
            ->where('shop.id = :shopId')
            ->setParameter(':shopId', $shopId)
            ->execute()
            ->fetchColumn();

        return $defaultCustomerGroupKey === $customerGroupKey;
    }

    /**
     * Checks if the private register feature is activated for the given customer group.
     *
     * @param string $customerGroupKey
     *
     * @return bool
     */
    private function isPrivateRegisterConfigured($customerGroupKey)
    {
        $result = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'allowregister',
            $customerGroupKey
        );

        return (bool) $result;
    }

    /**
     * Returns the proper target params "sTarget" and "sTargetAction" depending on the given configuration.
     *
     * @param string $customerGroup
     *
     * @return string|false
     */
    private function getConfiguredTargetParams($customerGroup)
    {
        $customerGroup = $this->getCustomerGroupAfterRegistration($customerGroup);
        $redirectRegistration = $this->getRedirectRegistrationConfig($customerGroup);

        $target = 'account';
        $targetAction = 'index';
        if ($redirectRegistration) {
            list($target, $targetAction) = explode('/', $redirectRegistration);
        }

        return $this->router->assemble([
            'controller' => 'register',
            'action' => 'saveRegister',
            'sTarget' => $target,
            'sTargetAction' => $targetAction,
        ]);
    }

    /**
     * Returns the proper 'redirectregistration' config by the given customer group.
     *
     * @param string $customerGroup
     *
     * @return bool|string
     */
    private function getRedirectRegistrationConfig($customerGroup)
    {
        $queryBuilder = $this->dbalConnection->createQueryBuilder();

        return $queryBuilder->select('redirectregistration')
            ->from(ConfigHelperInterface::PRIVATE_SHOPPING_TABLE)
            ->where('customergroup = :customerGroup')
            ->setParameter(':customerGroup', $customerGroup)
            ->execute()
            ->fetchColumn();
    }

    /**
     * You can define a temporary group when registering for another customer groups.
     * This leads to another necessary pile of checks, which provide the information if the configured
     * temporary group is allowed to access the shop and therefore no confirmation message will be needed.
     *
     * @param string $customerGroup
     *
     * @return bool
     */
    private function isTemporaryGroupAllowed($customerGroup)
    {
        $temporaryGroup = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'assigngroupbeforeunlock',
            $customerGroup
        );

        if (!$temporaryGroup) {
            return false;
        }

        $activateLogin = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'activatelogin',
            $temporaryGroup
        );

        if (!$activateLogin) {
            return true;
        }

        $earlyShopAccess = (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'unlockafterregister',
            $temporaryGroup
        );

        return $earlyShopAccess;
    }

    /**
     * Checks if the redirect-config has additional parameters.
     *
     * @param string $customerGroup
     *
     * @return bool
     */
    private function hasRedirectParams($customerGroup)
    {
        $customerGroup = $this->getCustomerGroupAfterRegistration($customerGroup);
        $redirectConfig = $this->getRedirectRegistrationConfig($customerGroup);

        return count(explode('/', $redirectConfig)) >= 3;
    }

    /**
     * Returns the customer group the user would own after registering.
     *
     * @param string $validationCustomerGroup
     *
     * @return string
     */
    private function getCustomerGroupAfterRegistration($validationCustomerGroup)
    {
        $requireUnlock = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'requireunlock',
            $validationCustomerGroup
        );

        // Checks if 'requireunlock' is set. In case of customer group = H, we also need to check the type of $requireUnlock
        if (($requireUnlock === '0' && $validationCustomerGroup === 'H') || (!$requireUnlock && $validationCustomerGroup !== 'H')) {
            return $validationCustomerGroup;
        }

        $temporaryGroup = $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_REGISTER_TABLE,
            'assigngroupbeforeunlock',
            $validationCustomerGroup
        );

        if (!$temporaryGroup) {
            $queryBuilder = $this->dbalConnection->createQueryBuilder();

            return $queryBuilder->select('customergroup')
                ->from(ConfigHelperInterface::PRIVATE_SHOPPING_TABLE)
                ->where('registergroup = :customerGroup')
                ->setParameter(':customerGroup', $validationCustomerGroup)
                ->execute()
                ->fetchColumn();
        }

        return $temporaryGroup;
    }

    /**
     * {@inheritdoc}
     */
    private function isPrivateShoppingActive($customerGroup)
    {
        return (bool) $this->configHelper->getConfig(
            ConfigHelperInterface::PRIVATE_SHOPPING_TABLE,
            'activatelogin',
            $customerGroup
        );
    }
}
