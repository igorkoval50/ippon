<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Service;

use Shopware\Components\Theme\Inheritance;
use Shopware\Models\Shop\Shop as ShopModel;

class Theme implements ThemeInterface
{
    /**
     * @var ShopInterface
     */
    private $shopService;

    /**
     * @var \Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * @var Inheritance
     */
    private $themeInheritance;

    /**
     * Theme constructor.
     *
     * @param Shop                      $shopService
     * @param \Enlight_Template_Manager $templateManager
     * @param Inheritance               $themeInheritance
     */
    public function __construct(
        Shop $shopService,
        \Enlight_Template_Manager $templateManager,
        Inheritance $themeInheritance
    ) {
        $this->shopService      = $shopService;
        $this->templateManager  = $templateManager;
        $this->themeInheritance = $themeInheritance;
    }

    /**
     * taken from \Shopware\Components\Theme\EventListener\ConfigLoader::onDispatch(), which can't be called directly
     *
     * @see \Shopware\Components\Theme\EventListener\ConfigLoader::onDispatch()
     *
     * @param int|null $shopId
     *
     * @return array
     * @throws \Exception
     */
    public function getThemeConfiguration($shopId = null)
    {
        if (is_int($shopId) && 0 < $shopId) {
            $shop = $this->shopService->getShop($shopId);
        } else {
            $shop = $this->shopService->getActiveShop();
        }

        if (!$shop instanceof ShopModel) {
            throw new \RuntimeException('Theme configuration cannot be read due to missing shop.');
        }

        /** @var array $config */
        $config = $this->templateManager->getTemplateVars('theme');
        if (!empty($config)) {
            return $config;
        }

        $config = $this->themeInheritance->buildConfig(
            $shop->getTemplate(),
            $shop,
            false
        );

        $this->templateManager->addPluginsDir(
            $this->themeInheritance->getSmartyDirectories($shop->getTemplate())
        );

        $this->templateManager->assign('theme', $config);

        return $config;
    }
}