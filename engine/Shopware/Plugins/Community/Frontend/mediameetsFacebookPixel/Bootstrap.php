<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Theme\LessDefinition;
use Shopware\mediameetsFacebookPixel\Components\Installer;
use Shopware\mediameetsFacebookPixel\Components\Updater;
use Shopware\mediameetsFacebookPixel\Subscriber\CookieCollector;
use Shopware\mediameetsFacebookPixel\Subscriber\Frontend;
use Shopware\Models\Shop\Shop;

class Shopware_Plugins_Frontend_mediameetsFacebookPixel_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . '/plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception("Das Plugin hat eine ungültige Version's-Datei.");
        }
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'Facebook&#174; Pixel einbinden';
    }

    /**
     * @inheritdoc
     */
    public function getInfo()
    {
        try {
            $version = $this->getVersion();
        } catch (Exception $e) {
            $version = null;
        }
        return [
            'version' => $version,
            'label' => $this->getLabel(),
            'author' => 'media:meets GmbH',
            'support' => 'Shopware Forum',
            'link' => 'https://www.mediameets.de'
        ];
    }

    /**
     * Is executed after the collection has been added.
     */
    public function afterInit()
    {
        $this->registerMyComponents();
    }

    /**
     * Called when plugin is installed.
     *
     * @throws Exception
     * @return bool
     */
    public function install()
    {
        if (! $this->assertMinimumVersion('5.3.0')) {
            throw new Exception('At least Shopware 5.3.0 is required');
        }

        $this->registerEvents();

        $installer = new Installer($this);
        $installer->run();

        return true;
    }

    /**
     * Called when plugin is enabled.
     *
     * @return array
     */
    public function enable()
    {
        return [
            'success' => true,
            'invalidateCache' => ['template', 'proxy', 'http', 'theme']
        ];
    }

    /**
     * Called when plugin is disabled.
     *
     * @return array
     */
    public function disable()
    {
        return [
            'success' => true,
            'invalidateCache' => ['template', 'proxy', 'http', 'theme']
        ];
    }

    /**
     * Called when plugin is uninstalled.
     *
     * @return array
     */
    public function uninstall()
    {
        return [
            'success' => true,
            'invalidateCache' => ['template', 'proxy', 'http', 'theme']
        ];
    }

    /**
     * Called when plugin is updated.
     *
     * @param $oldVersion
     * @throws Exception
     * @return array
     */
    public function update($oldVersion)
    {
        if (! $this->assertMinimumVersion('5.3.0')) {
            throw new Exception('At least Shopware 5.3.0 is required');
        }

        $this->registerEvents();

        $updater = new Updater($this);
        $success = $updater->run($oldVersion);

        return [
            'success' => $success,
            'invalidateCache' => ['template', 'proxy', 'http', 'theme']
        ];
    }

    /**
     * @deprecated since 1.3.0
     */
    public function onPostDispatchSecure()
    {
        //
    }

    /**
     * @deprecated since 1.3.0
     */
    public function onPostDispatchSecureFrontendNewsletter()
    {
        //
    }

    /**
     * Registers all event listeners.
     */
    private function registerEvents()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            'onStartDispatch'
        );

        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Less',
            'addLessFiles'
        );

        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Javascript',
            'addJavaScriptFiles'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure',
            'onPostDispatchSecure'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Newsletter',
            'onPostDispatchSecureFrontendNewsletter'
        );

        $this->subscribeEvent(
            'Theme_Inheritance_Template_Directories_Collected',
            'onCollectTemplateDirectories'
        );
    }

    /**
     * Register template directories.
     * @param Enlight_Event_EventArgs $args
     */
    public function onCollectTemplateDirectories(Enlight_Event_EventArgs $args)
    {
        /** @var $directories array */
        $directories = $args->getReturn();

        $directories[] = $this->Path() . 'Views/Plugin/';
        $directories[] = $this->Path() . 'Views/';

        $args->setReturn($directories);
    }

    /**
     * Adding less files to less compiler
     *
     * @param Enlight_Event_EventArgs $args
     * @return ArrayCollection
     */
    public function addLessFiles(Enlight_Event_EventArgs $args)
    {
        $config = $this->getConfig($args->get('shop'));

        $collection = new ArrayCollection();

        if (isset($config['status']) && $config['status'] === true) {
            $collection->add(new LessDefinition(
                [],
                [__DIR__ . '/Views/frontend/_public/src/less/all.less']
            ));
        }

        return $collection;
    }

    /**
     * Adding javascript files to javascript compiler
     *
     * @param Enlight_Event_EventArgs $args
     * @return ArrayCollection
     */
    public function addJavaScriptFiles(Enlight_Event_EventArgs $args)
    {
        $config = $this->getConfig($args->get('shop'));

        $collection = new ArrayCollection();

        if (isset($config['status']) && $config['status'] === true) {
            $collection->add(__DIR__ . '/Views/frontend/_public/src/js/jquery.mm-fb-pixel.js');
        }

        return $collection;
    }

    /**
     * @param bool|Shop $shop
     * @return array
     */
    private function getConfig($shop = false)
    {
        $container = $this->Application()->Container();

        if (! $shop && $container->initialized('shop')) {
            /** @var Shop|false $shop */
            $shop = $container->get('shop');
        }

        if (! $shop) {
            $shop = $container->get('models')
                ->getRepository(Shop::class)
                ->getActiveDefault();
        }

        return $container->get('shopware.plugin.cached_config_reader')
            ->getByPluginName($this->getName(), $shop);
    }

    /**
     * Registers the plugin namespace and subscribers
     */
    public function onStartDispatch()
    {
        $subscribers = [
            new Frontend(),
            new CookieCollector(),
            new Frontend\Listing(),
            new Frontend\Detail(),
            new Frontend\Checkout(),
            new Frontend\Controllers(),
            new Frontend\Newsletter(),
        ];

        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
    }

    /**
     * Registers the plugin namespace
     */
    public function registerMyComponents()
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\mediameetsFacebookPixel',
            $this->Path()
        );
    }
}
