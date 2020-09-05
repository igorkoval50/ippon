<?php namespace NetzpStaging;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Doctrine\ORM\Tools\SchemaTool;

use NetzpStaging\Component\Helper;
use NetzpStaging\Subscriber\Subscriber;
use NetzpStaging\Models\StagingProfile;
use NetzpStaging\Models\StagingFile;
use NetzpStaging\Models\KeyStore;

class NetzpStaging extends \Shopware\Components\Plugin
{
	public static function getSubscribedEvents() {

        return [
            'Enlight_Bootstrap_InitResource_netzp_staging.helper'
                => 'onInitServiceHelper',
            'Enlight_Controller_Front_StartDispatch'
                => 'onStartDispatch',
        ];
	}

    public function install(InstallContext $context)
    {
    	parent::install($context);

        $this->container->get('template')->addTemplateDir($this->getPath() . '/Resources/views/');
        $this->createSchema();
        $this->createAcl();
    }

    public function uninstall(UninstallContext $context)
    {
        $this->removeAcl();

		parent::uninstall($context);
    }

    public function update(UpdateContext $context)
    {
        $this->createSchema();

        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

	public function activate(ActivateContext $context) 
	{
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
	}

	public function deactivate(DeactivateContext $context) 
	{
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
	}

    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $container->setParameter('netzp_staging.plugin_dir', $this->getPath());
        parent::build($container);
    }

    public function onStartDispatch(\Enlight_Event_EventArgs $args)
    {
        Shopware()->Events()->addSubscriber(new Subscriber());
    }

    public function onInitServiceHelper()
    {
        return new Helper(
        	Shopware()->Container()->get('db'),
			Shopware()->Container()->get('models')
		);
    }

    private function createSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $tool->updateSchema([
            $this->container->get('models')->getClassMetadata(StagingProfile::class),
            $this->container->get('models')->getClassMetadata(StagingFile::class),
            $this->container->get('models')->getClassMetadata(KeyStore::class),
        ], true);
    }

    private function createAcl()
    {
        Shopware()->Acl()->createResource(
            'netzpStaging', 
            array('create'), 
            'Testserver einrichten', 
            $this->container->get('shopware.plugin_manager')->getPluginByName('NetzpStaging')->getId()
        );
    }

    private function removeAcl()
    {
        Shopware()->Acl()->deleteResource('netzpStaging');
    }   
}
