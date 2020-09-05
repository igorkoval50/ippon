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

namespace SwagCustomProducts\Subscriber;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs as ActionEventArgs;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use SwagCustomProducts\Components\Services\CustomProductsServiceInterface;

class Backend implements SubscriberInterface
{
    /**
     * @var DIContainer
     */
    private $container;

    /**
     * @var
     */
    private $pluginRoot;

    /**
     * @param string $pluginRoot
     */
    public function __construct(DIContainer $container, $pluginRoot)
    {
        $this->container = $container;
        $this->pluginRoot = $pluginRoot;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index' => 'extendMenu',
            'Enlight_Controller_Action_PostDispatch_Backend_Article' => 'extendArticleModule',
            'Enlight_Controller_Action_PostDispatch_Backend_Order' => ['extendOrderModule', 100],
            'Enlight_Controller_Action_PostDispatch_Backend_Config' => 'onBackendConfig',
        ];
    }

    public function onBackendConfig(\Enlight_Controller_ActionEventArgs $args)
    {
        $this->container->get('snippets')->addConfigDir($this->pluginRoot . '/Resources/snippets/');
        $this->registerTemplateDir();

        $args->getSubject()->View()->extendsTemplate(
            'backend/config/custom_products_extension.js'
        );
    }

    public function extendOrderModule(ActionEventArgs $args)
    {
        $this->registerTemplateDir();

        /** @var \Enlight_View_Default $view */
        $view = $args->getSubject()->View();

        if ($args->getRequest()->getActionName() === 'getList') {
            $data = $this->assignAttributes($view->getAssign('data'));
            $view->assign('data', $data);
        }

        if ($args->getRequest()->getActionName() === 'index') {
            $view->extendsTemplate('backend/order/swag_custom_products/app.js');
        }

        if ($args->getRequest()->getActionName() === 'load') {
            $view->extendsTemplate('backend/order/swag_custom_products_position.js');
        }
    }

    /**
     * Event handler which provides the necessary snippet and views directories for the plugin.
     */
    public function extendArticleModule(ActionEventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $actionName = $args->getRequest()->getActionName();
        $view = $controller->View();

        // Add template directory
        $this->registerTemplateDir();

        if ($actionName === 'index') {
            $view->extendsTemplate('backend/article/swag_custom_products/app.js');
        }

        if ($actionName === 'load') {
            $view->extendsTemplate('backend/article/swag_custom_products/views/window.js');
        }
    }

    /**
     * Loads the menu icon
     */
    public function extendMenu(ActionEventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Index $subject */
        $view = $args->getSubject()->View();

        $this->registerTemplateDir();

        $view->extendsTemplate('backend/swag_custom_products/menu_item.tpl');
    }

    /**
     * registers the Views/ directory as template directory
     */
    private function registerTemplateDir()
    {
        /** @var \Enlight_Template_Manager $template */
        $template = $this->container->get('template');

        $template->addTemplateDir($this->pluginRoot . '/Resources/views/');
    }

    /**
     * @param array[] $data
     *
     * @return array[]
     */
    private function assignAttributes($data)
    {
        $ids = [];
        foreach ($data as $orderArray) {
            $ids = array_merge(array_column($orderArray['details'], 'id'), $ids);
        }

        $attributes = $this->loadAttributes($ids);

        foreach ($data as &$order) {
            foreach ($order['details'] as &$detail) {
                $id = $detail['id'];
                // $attributes[$index$] can be a empty array and then isset() returns true.
                // Thats the reason why we check for empty() to.
                if (!isset($attributes[$id]) || empty($attributes[$id])) {
                    continue;
                }
                $attribute = array_shift($attributes[$id]);

                // Set the "hide"-element if the "detail"-icon should not be displayed
                if (!$this->shouldDisplayCustomOptionDetails($attribute)) {
                    $attribute['swag_custom_products_hide_details'] = true;
                }

                $detail = array_merge($detail, $attribute);
            }
        }

        return $data;
    }

    /**
     * @param int[] $ids
     *
     * @return array[]
     */
    private function loadAttributes($ids)
    {
        /** @var QueryBuilder $query */
        $query = $this->container->get('dbal_connection')->createQueryBuilder();
        $query->select([
            'detailID',
            'swag_custom_products_configuration_hash',
            'swag_custom_products_mode',
        ]);
        $query->from('s_order_details_attributes', 'attributes');
        $query->where('detailID IN (:ids)');
        $query->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);

        return $query->execute()->fetchAll(\PDO::FETCH_GROUP);
    }

    /**
     * Checks if the custom options entered for this order should be displayed in the backend.
     * Depends on the mode and the actually values of the options.
     * If the entered options are completely empty, we also need to hide the details.
     *
     * @return bool
     */
    private function shouldDisplayCustomOptionDetails(array $attributes)
    {
        $hash = $attributes['swag_custom_products_configuration_hash'];
        $mode = (int) $attributes['swag_custom_products_mode'];

        if (!$hash || !$mode || $mode !== 1) {
            return false;
        }

        /** @var CustomProductsServiceInterface $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');

        // If no options were set, we need to return false to hide the "details" icon
        if (!$customProductsService->getOptionsFromHash($hash)) {
            return false;
        }

        return true;
    }
}
