<?php
namespace MbdusSeoUrl\Bootstrap;
use Shopware\Components\Plugin\Context\UninstallContext;

class Uninstaller
{
	/**
	 * @var int
	 */
	private $pluginId;

	/**
	 * @param string $bootstrapPath
	 */
	public function __construct(
		$pluginId
	)
	{
		$this->pluginId = $pluginId;
	}
	
	/**
	 * @return bool
	 *
	 * @throws \Exception
	 */
	public function uninstall(UninstallContext $uninstallContext)
	{
	    if ($uninstallContext->keepUserData()) {
	        return;
	    }
	    
	    $attributeCrudService = Shopware()->Container()->get('shopware_attribute.crud_service');
	    
	    $attributeCrudService->delete('s_articles_attributes', 'mbdus_seourl');
	    $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
	    $metaDataCache->deleteAll();
	    Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
	    
	    $attributeCrudService->delete('s_categories_attributes', 'mbdus_seourl');
	    $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
	    $metaDataCache->deleteAll();
	    Shopware()->Models()->generateAttributeModels(['s_categories_attributes']);
	    
	    $attributeCrudService->delete('s_articles_supplier_attributes', 'mbdus_seourl');
	    $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
	    $metaDataCache->deleteAll();
	    Shopware()->Models()->generateAttributeModels(['s_articles_supplier_attributes']);
	    
	    $attributeCrudService->delete('s_blog_attributes', 'mbdus_seourl');
	    $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
	    $metaDataCache->deleteAll();
	    Shopware()->Models()->generateAttributeModels(['s_blog_attributes']);
	    
	    $attributeCrudService->delete('s_cms_static_attributes', 'mbdus_seourl');
	    $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
	    $metaDataCache->deleteAll();
	    Shopware()->Models()->generateAttributeModels(['s_cms_static_attributes']);
	    
	    $attributeCrudService->delete('s_cms_support_attributes', 'mbdus_seourl');
	    $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
	    $metaDataCache->deleteAll();
	    Shopware()->Models()->generateAttributeModels(['s_cms_support_attributes']);
	    
	    $attributeCrudService->delete('s_emotion_attributes', 'mbdus_seourl');
	    $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
	    $metaDataCache->deleteAll();
	    Shopware()->Models()->generateAttributeModels(['s_emotion_attributes']);
	    
	    // clear cache
	    $uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
	}
}