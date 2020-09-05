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

namespace SwagNewsletter\Bootstrap;

use Shopware\Bundle\MediaBundle\MediaServiceInterface;
use Shopware\Components\Logger;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\Plugin;
use SwagNewsletter\Components\LiveShopping\LiveShoppingCompatibilityException;
use SwagNewsletter\Components\LiveShopping\LiveShoppingWidgetCreator;
use SwagNewsletter\Components\NewsletterComponentHelper;
use SwagNewsletter\Components\NewsletterHelper;

class LiveShoppingIntegrationInstaller
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var MediaServiceInterface
     */
    private $mediaService;

    /**
     * @var Logger
     */
    private $pluginLogger;

    /**
     * @param ModelManager          $modelManager
     * @param MediaServiceInterface $mediaService
     * @param Logger                $pluginLogger
     */
    public function __construct(
        ModelManager $modelManager,
        MediaServiceInterface $mediaService,
        Logger $pluginLogger
    ) {
        $this->modelManager = $modelManager;
        $this->mediaService = $mediaService;
        $this->pluginLogger = $pluginLogger;
    }

    /**
     * Create live shopping widget component if live shopping plugin is active
     */
    public function createLiveShoppingWidget()
    {
        try {
            $plugin = $this->getLiveShoppingPlugin();
        } catch (LiveShoppingCompatibilityException $exception) {
            return;
        }

        try {
            $liveShoppingWidgetCreator = new LiveShoppingWidgetCreator(
                new NewsletterComponentHelper(
                    $this->modelManager,
                    new NewsletterHelper(
                        $this->modelManager,
                        $this->mediaService
                    ),
                    $this->modelManager->getConnection()
                )
            );

            $liveShoppingWidgetCreator->create($plugin);
        } catch (\RuntimeException $exception) {
            $this->pluginLogger->addNotice('LiveShopping element can\'t added more than one time. Exception message: ' . $exception->getMessage());
        }
    }

    /**
     * Checks if the LiveShopping plugin is available and returns the plugin model
     *
     * @throws LiveShoppingCompatibilityException
     *
     * @return Plugin
     */
    private function getLiveShoppingPlugin()
    {
        /** @var Plugin $plugin */
        $plugin = $this->modelManager->getRepository(Plugin::class)->findOneBy([
            'label' => 'LiveShopping',
        ]);

        if (!$plugin) {
            throw new LiveShoppingCompatibilityException('LiveShopping is not installed.');
        }

        return $plugin;
    }
}
