<?php
namespace MbdusSeoUrl;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use MbdusSeoUrl\Bootstrap\Installer;
use MbdusSeoUrl\Bootstrap\Updater;
use MbdusSeoUrl\Bootstrap\Uninstaller;

class MbdusSeoUrl extends Plugin
{
    /**
     * add compiler pass
     *
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
    	$container->setParameter('mbdus_seo_url.plugin_dir', $this->getPath());
        parent::build($container);
    }

    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {   
        $installer = new Installer(
        		$this->container->get('shopware.plugin_manager')->getPluginByName('MbdusSeoUrl')->getId()
        );
        $installer->install();
    }

    /**
     * {@inheritdoc}
     */
    public function update(UpdateContext $context)
    {	
        $updater = new Updater(
        		$this->container->get('shopware.plugin_manager')->getPluginByName('MbdusSeoUrl')->getId()
        );
        $updater->update();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
        $uninstaller = new Uninstaller(
            $this->container->get('shopware.plugin_manager')->getPluginByName('MbdusSeoUrl')->getId()
            );
        $uninstaller->uninstall($context);
    }

    /**
     * {@inheritdoc}
     */
    public function activate(ActivateContext $context)
    {
        $this->requestClearCache($context);
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->requestClearCache($context);
    }

    /**
     * @param InstallContext | UpdateContext | UninstallContext | ActivateContext | DeactivateContext $context
     */
    private function requestClearCache($context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }
}
