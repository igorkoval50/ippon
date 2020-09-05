<?php
namespace MbdusSeoUrl\Bootstrap;

class Updater
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
	public function update()
	{
		$this->createAttributes();
		$this->createAclResource ();
	}
	
	/**
	 * Creates an attribute in s_articles_attributes
	 *
	 * @return void
	 */
	public function createAttributes() {
	
		Shopware()->Container()->get ( 'shopware_attribute.crud_service' )->update ( 's_articles_attributes', 'mbdus_seourl', 'text', array (
				'label' => 'Artikel Seo-Url',
				'displayInBackend' => false
		) );
	
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
	
		Shopware()->Container()->get ( 'shopware_attribute.crud_service' )->update ( 's_categories_attributes', 'mbdus_seourl', 'text', array (
				'label' => 'Kategorie Seo-Url',
				'displayInBackend' => false
		) );
	
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_categories_attributes']);
	
		Shopware()->Container()->get ( 'shopware_attribute.crud_service' )->update ( 's_articles_supplier_attributes', 'mbdus_seourl', 'text', array (
				'label' => 'Hersteller Seo-Url',
				'displayInBackend' => false
		) );
	
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_articles_supplier_attributes']);
	
		Shopware()->Container()->get ( 'shopware_attribute.crud_service' )->update ( 's_blog_attributes', 'mbdus_seourl', 'text', array (
				'label' => 'Blogartikel Seo-Url',
				'displayInBackend' => false
		) );
	
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_blog_attributes']);
	
		Shopware()->Container()->get ( 'shopware_attribute.crud_service' )->update ( 's_cms_static_attributes', 'mbdus_seourl', 'text', array (
				'label' => 'Shopseiten Seo-Url',
				'displayInBackend' => false
		) );
	
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_cms_static_attributes']);
	
		Shopware()->Container()->get ( 'shopware_attribute.crud_service' )->update ( 's_cms_support_attributes', 'mbdus_seourl', 'text', array (
				'label' => 'Formular Seo-Url',
				'displayInBackend' => false
		) );
	
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_cms_support_attributes']);
	
		Shopware()->Container()->get ( 'shopware_attribute.crud_service' )->update ( 's_emotion_attributes', 'mbdus_seourl', 'text', array (
				'label' => 'Einkaufswelten Seo-Url',
				'displayInBackend' => false
		) );
	
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_emotion_attributes']);
	}
	
	/**
	 * create acl rights for import export
	 */
	public function createAclResource()
	{
		// If exists: find existing MbdusSeoUrl resource
		$pluginId = Shopware()->Db()->fetchRow(
				'SELECT pluginID FROM s_core_acl_resources WHERE name = ? ',
				array("mbdusseourlimportexport")
		);
		$pluginId = isset($pluginId['pluginID']) ? $pluginId['pluginID'] : null;

		if ($pluginId) {
			// prevent creation of new acl resource
			return;
		}

		$resource = new \Shopware\Models\User\Resource();
		$resource->setName('mbdusseourlimportexport');
		$resource->setPluginId($this->pluginId);

		foreach (array('read', 'export', 'import') as $action) {
			$privilege = new \Shopware\Models\User\Privilege();
			$privilege->setResource($resource);
			$privilege->setName($action);

			Shopware()->Models()->persist($privilege);
		}

		Shopware()->Models()->persist($resource);

		Shopware()->Models()->flush();
	}
}