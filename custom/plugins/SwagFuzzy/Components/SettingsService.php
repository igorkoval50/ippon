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

namespace SwagFuzzy\Components;

use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use Shopware\Components\Model\ModelManager;

/**
 * Class SettingsService
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class SettingsService
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var array
     */
    private $cacheSettings = [];

    /**
     * @var Shop
     */
    private $shop;

    public function __construct(ModelManager $modelManager, ContextServiceInterface $contextService)
    {
        $this->modelManager = $modelManager;
        $this->contextService = $contextService;
    }

    /**
     * Return cached plugin settings for current shop
     *
     * @return array
     */
    public function getSettings()
    {
        if (empty($this->cacheSettings)) {
            $this->shop = $this->contextService->getShopContext()->getShop();
            $this->cacheSettings = $this->loadSettings();
        }

        return $this->cacheSettings;
    }

    /**
     * returns the fuzzy settings depending on the current shop context
     * if no settings found for this shop, use settings from parent shop
     * if still no settings found, use settings for active default shop
     *
     * @return array
     */
    private function loadSettings()
    {
        $builder = $this->modelManager->getDBALQueryBuilder();
        $builder->select('settings.*')
            ->from('s_plugin_swag_fuzzy_settings', 'settings')
            ->where('settings.shopId = :shopId')
            ->setParameter('shopId', $this->shop->getId());

        $settings = $builder->execute()->fetch();

        if (empty($settings)) {
            $builder->setParameter('shopId', $this->shop->getParentId());
            $settings = $builder->execute()->fetch();
        }

        if (empty($settings)) {
            $shopBuilder = $this->modelManager->getDBALQueryBuilder();
            $shopBuilder->select('shop.id')
                ->from('s_core_shops', 'shop')
                ->where('shop.default = 1')
                ->andWhere('shop.active = 1');

            $activeDefaultShopId = $shopBuilder->execute()->fetchColumn();

            $builder->setParameter('shopId', $activeDefaultShopId);
            $settings = $builder->execute()->fetch();
        }

        return $settings;
    }
}
