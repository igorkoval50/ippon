<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RewriteTable implements SubscriberInterface
{
	/**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'sRewriteTable::sCreateRewriteTableArticles::after' => 'afterCreateSeoArticle',
			'Shopware_Controllers_Backend_Seo::seoArticleAction::after' => 'afterCreateSeoArticle',
			'sRewriteTable::sCreateRewriteTableCategories::after' => 'afterCreateSeoCategory',
			'sRewriteTable::sCreateRewriteTableSuppliers::after' => 'afterCreateSeoSupplier',
			'sRewriteTable::createManufacturerUrls::after' => 'afterCreateSeoSupplier',
			'sRewriteTable::sCreateRewriteTableBlog::after' => 'afterCreateSeoBlog',
			'sRewriteTable::sCreateRewriteTableContent::after' => 'afterCreateSeoContent',
			'sRewriteTable::sCreateRewriteTableStatic::after' => 'afterCreateSeoStatic',
			'sRewriteTable::sCreateRewriteTableCampaigns::after' => 'afterCreateSeoEmotion',
			'Shopware_Controllers_Backend_Emotion::saveAction::after' => 'afterCreateSeoEmotion'
		);
	}

	/**
	 * Shopware_Controllers_Backend_Seo_seoArticleAction::after: afterCreateSeoArticle
	 *
	 * @return array
	 */
	public function afterCreateSeoArticle(\Enlight_Hook_HookArgs $args) {
		$shopIds = array();
		if (method_exists($args->getSubject(), 'Request')){
			$tmpShopId = (int) $args->getSubject()->Request()->getParam('shopId', 1);
			array_push($shopIds, ['id'=>$tmpShopId]);
		}
		if(empty($shopIds)){
			$sql = "SELECT id FROM s_core_shops";
			$shopIds = Shopware()->Db()->fetchAll($sql);
		}
		try {
			$sql = "SELECT ad.articleID, aa.mbdus_seourl FROM s_articles_attributes aa, s_articles_details ad WHERE aa.articledetailsID = ad.id AND ad.kind=1 AND aa.mbdus_seourl IS NOT NULL AND aa.mbdus_seourl != ''";
			$articles = Shopware()->Db()->fetchAll($sql);

			foreach($articles as $article){
				foreach($shopIds as $shopId){
					$shopId = $shopId['id'];
					$seoUrl = "";
					$name = "";
					$articleID = $article['articleID'];

					if($shopId!=1){
						$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='article' AND objectkey=? AND objectlanguage=?";
						$data = Shopware()->Db()->fetchOne($sql,array($articleID,$shopId));
						$data = unserialize($data, ['allowed_classes' => false]);
						$seoUrl = $data['mbdusSeoUrl'];
						$name = $data['txtArtikel'];
					}

					if(empty($seoUrl) && empty ($name)){
						$seoUrl = $article['mbdus_seourl'];
					}

					if (! empty ( $seoUrl )) {
					    $seoUrl = $this->substituteSpecialChars($seoUrl);
						$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
						$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
								$seoUrl,
								'%sArticle='.$articleID,
								$shopId
						) );
						if (! empty ( $url2 )) {
							$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id']
							) );
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id'],
									$url2 ['org_path'],
									$shopId
							) );
						} else {
							$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
							$pathExists = Shopware()->Db()->fetchOne($sql, array(
									$seoUrl,
									$shopId
							));

							if(empty($pathExists)){
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
								Shopware ()->Db ()->query ( $sql, array (
										'%sArticle='.$articleID,
										$shopId
								) );

								$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
								Shopware ()->Db ()->query ( $sql, array (
										$seoUrl,
										'sViewport=detail&sArticle='.$articleID,
										1,
										$shopId
								) );
							}
						}
					}
				}
			}
		} catch ( \Exception $e ) {
			Shopware()->PluginLogger()->error("MbdusSeoUrl:".$e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString());
		}
	}

	/**
	 * Shopware_Controllers_Backend_Seo_seoCategoryAction::after: afterCreateSeoCategory
	 *
	 * @return array
	 */
	public function afterCreateSeoCategory(\Enlight_Hook_HookArgs $args) {
		$shopIds = array();
		$configReader = $this->container->get('shopware.plugin.config_reader');
		$config = $configReader->getByPluginName('MbdusSeoUrl');

		if (method_exists($args->getSubject(), 'Request')){
			$tmpShopId = (int) $args->getSubject()->Request()->getParam('shopId', 1);
			array_push($shopIds, ['id'=>$tmpShopId]);
		}
		if(empty($shopIds)){
			$sql = "SELECT id FROM s_core_shops";
			$shopIds = Shopware()->Db()->fetchAll($sql);
		}
		try {
			foreach($shopIds as $shopId){
				$shopId = $shopId['id'];
				$sql = "SELECT category_id FROM s_core_shops WHERE id = ?";
				$shopParentCategory = Shopware()->Db()->fetchOne($sql, array($shopId));
				$sql = "SELECT ca.categoryID, ca.mbdus_seourl, blog FROM s_categories_attributes ca, s_categories c WHERE c.id = ca.categoryID AND ca.mbdus_seourl IS NOT NULL AND ca.mbdus_seourl != '' AND c.path LIKE ?";
				$categories = Shopware()->Db()->fetchAll($sql,array('%|'.$shopParentCategory.'|%'));

				foreach($categories as $category){
					foreach($shopIds as $shopId2){
						$shopIdInner = $shopId2['id'];
						$seoUrl = "";
						$name = "";
						$categoryID = $category['categoryID'];
						$isBlog = $category['blog'];
						$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='category' AND objectkey=? AND objectlanguage=?";
						$data = Shopware()->Db()->fetchOne($sql,array($categoryID,$shopIdInner));
						$data = unserialize($data, ['allowed_classes' => false]);
						$seoUrl = $data['mbdusSeoUrl'];
						$name = $data['description'];

						if($shopId==$shopIdInner && empty($seoUrl) && empty ($name)){
							$seoUrl = $category['mbdus_seourl'];
						}

						if(empty($seoUrl) && empty ($name) && $config['categoryinherit'] == 1){
							$seoUrl = $category['mbdus_seourl'];
						}

						if($shopId!=$shopIdInner && $config['categoryinherit'] == 0){
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE org_path LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									'%sCategory='.$categoryID,
									$shopIdInner
							) );
						}

						if (! empty ( $seoUrl )) {
						    $seoUrl = $this->substituteSpecialChars($seoUrl);
							$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
							$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
									$seoUrl,
									'%sCategory='.$categoryID,
									$shopIdInner
							) );

							if (! empty ( $url2 )) {
								$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
								Shopware ()->Db ()->query ( $sql, array (
										$url2 ['id']
								) );
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
								Shopware ()->Db ()->query ( $sql, array (
										$url2 ['id'],
										$url2 ['org_path'],
										$shopIdInner
								) );
							} else {
								$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
								$pathExists = Shopware()->Db()->fetchOne($sql, array(
										$seoUrl,
										$shopIdInner
								));

								if(empty($pathExists)){
									$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
									Shopware ()->Db ()->query ( $sql, array (
											'%sCategory='.$categoryID,
											$shopIdInner
									) );

									if($isBlog){
										$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
										Shopware ()->Db ()->query ( $sql, array (
												$seoUrl,
												'sViewport=blog&sCategory='.$categoryID,
												1,
												$shopIdInner
										) );
									}
									else{
										$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
										Shopware ()->Db ()->query ( $sql, array (
												$seoUrl,
												'sViewport=cat&sCategory='.$categoryID,
												1,
												$shopIdInner
										) );
									}
								}
							}
						}
					}
				}
			}
		} catch ( \Exception $e ) {
			Shopware()->PluginLogger()->error("MbdusSeoUrl:".$e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString());
		}
	}

	/**
	 * Shopware_Controllers_Backend_Seo_seoSupplierAction::after: afterCreateSeoSupplier
	 *
	 * @return array
	 */
	public function afterCreateSeoSupplier(\Enlight_Hook_HookArgs $args) {
		$shopIds = array();
		if (method_exists($args->getSubject(), 'Request')){
			$tmpShopId = (int) $args->getSubject()->Request()->getParam('shopId', 1);
			array_push($shopIds, ['id'=>$tmpShopId]);
		}
		if(empty($shopIds)){
			$sql = "SELECT id FROM s_core_shops";
			$shopIds = Shopware()->Db()->fetchAll($sql);
		}
		try {
			$sql = "SELECT supplierID, mbdus_seourl FROM s_articles_supplier_attributes WHERE mbdus_seourl IS NOT NULL AND mbdus_seourl != ''";
			$suppliers = Shopware()->Db()->fetchAll($sql);
			foreach($suppliers as $supplier){
				foreach($shopIds as $shopId){
					$shopId = $shopId['id'];
					$seoUrl = "";
					$metaTitle = "";
					$supplierID = $supplier['supplierID'];

					if($shopId!=1){
						$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='supplier' AND objectkey=? AND objectlanguage=?";
						$data = Shopware()->Db()->fetchOne($sql,array($supplierID,$shopId));
						$data = unserialize($data, ['allowed_classes' => false]);
						$seoUrl = $data['mbdusSeoUrl'];
						$metaTitle = $data['metaTitle'];
					}

					if(empty($seoUrl) && empty($metaTitle)){
						$seoUrl = $supplier['mbdus_seourl'];
					}

					if (! empty ( $seoUrl )) {
					    $seoUrl = $this->substituteSpecialChars($seoUrl);
						$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
						$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
								$seoUrl,
								'%sSupplier='.$supplierID,
								$shopId
						) );
						if (! empty ( $url2 )) {
							$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id']
							) );
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id'],
									$url2 ['org_path'],
									$shopId
							) );
						} else {
							$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
							$pathExists = Shopware()->Db()->fetchOne($sql, array(
									$seoUrl,
									$shopId
							));

							if(empty($pathExists)){
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
								Shopware ()->Db ()->query ( $sql, array (
										'%sSupplier='.$supplierID,
										$shopId
								) );

								$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
								Shopware ()->Db ()->query ( $sql, array (
										$seoUrl,
										'sViewport=listing&sAction=manufacturer&sSupplier='.$supplierID,
										1,
										$shopId
								) );
							}
						}
					}
				}
			}
		} catch ( \Exception $e ) {
			echo "Exception:";
			print_r ( $e );
			exit ();
		}
	}

	/**
	 * Shopware_Controllers_Backend_Seo_seoSupplierAction::after: afterCreateSeoSupplier
	 *
	 * @return array
	 */
	public function afterCreateSeoBlog(\Enlight_Hook_HookArgs $args) {
		if (method_exists($args->getSubject(), 'Request')){
			$shopId = (int) $args->getSubject()->Request()->getParam('shopId', 1);
		}
		else{
			$shopId = Shopware()->Shop()->getId();
		}
		try {
			$sql = "SELECT blog_id, mbdus_seourl FROM s_blog_attributes WHERE mbdus_seourl IS NOT NULL AND mbdus_seourl != ''";
			$blogArticles = Shopware()->Db()->fetchAll($sql);
			foreach($blogArticles as $blogArticle){
				$seoUrl = $blogArticle['mbdus_seourl'];
				$blogID = $blogArticle['blog_id'];

				$sql="SELECT category_id FROM s_blog WHERE id = ?";
				$categoryID = Shopware()->Db()->fetchOne($sql,array($blogID));

				$org_path="sViewport=blog&sAction=detail&sCategory=".$categoryID."&blogArticle=".$blogID;

				if (! empty ( $seoUrl )) {
				    $seoUrl = $this->substituteSpecialChars($seoUrl);
					$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path = ? AND subshopID = ?";
					$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
							$seoUrl,
							$org_path,
							$shopId
					) );
					if (! empty ( $url2 )) {
						$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
						Shopware ()->Db ()->query ( $sql, array (
								$url2 ['id']
						) );
						$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
						Shopware ()->Db ()->query ( $sql, array (
								$url2 ['id'],
								$url2 ['org_path'],
								$shopId
						) );
					} else {
						$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
						$pathExists = Shopware()->Db()->fetchOne($sql, array(
								$seoUrl,
								$shopId
						));

						if(empty($pathExists)){
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$org_path,
									$shopId
							) );

							$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
							Shopware ()->Db ()->query ( $sql, array (
									$seoUrl,
									$org_path,
									1,
									$shopId
							) );
						}
					}
				}
			}
		} catch ( \Exception $e ) {
			echo "Exception:";
			print_r ( $e );
			exit ();
		}
	}

	/**
	 * Shopware_Controllers_Backend_Seo_seoContentAction::after: afterCreateSeoContent
	 *
	 * @return array
	 */
	public function afterCreateSeoContent(\Enlight_Hook_HookArgs $args) {
		$configReader = $this->container->get('shopware.plugin.config_reader');
		$config = $configReader->getByPluginName('MbdusSeoUrl');
		$version = Shopware()->Container()->get('config')->get('version');
		$shopIds = array();
		if (method_exists($args->getSubject(), 'Request')){
			$tmpShopId = (int) $args->getSubject()->Request()->getParam('shopId', 1);
			array_push($shopIds, ['id'=>$tmpShopId]);
		}
		if(empty($shopIds)){
			$sql = "SELECT id FROM s_core_shops";
			$shopIds = Shopware()->Db()->fetchAll($sql);
		}
		try {
			$sql = "SELECT cmsStaticID, mbdus_seourl FROM s_cms_static_attributes csa, s_cms_static cs WHERE cs.id = csa.cmsStaticID AND mbdus_seourl IS NOT NULL AND mbdus_seourl != '' AND (link = '' OR link IS NULL)";
			$staticSites = Shopware()->Db()->fetchAll($sql);
			foreach($staticSites as $staticSite){
				foreach($shopIds as $shopId){
					$shopId = $shopId['id'];
					$seoUrl = "";
					$metaTitle = "";
					$cmsStaticID = $staticSite['cmsStaticID'];

					$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='page' AND objectkey=? AND objectlanguage=?";
					$data = Shopware()->Db()->fetchOne($sql,array($cmsStaticID,$shopId));
					$data = unserialize($data, ['allowed_classes' => false]);
					$seoUrl = $data['mbdusSeoUrl'];

					if(empty($seoUrl) && $config['siteinherit'] == 1){
						$seoUrl = $staticSite['mbdus_seourl'];
					}

					if(empty($seoUrl) && $config['siteinherit'] == 0){
						$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE org_path LIKE ? AND subshopID = ?";
						Shopware ()->Db ()->query ( $sql, array (
								'%sCustom='.$cmsStaticID,
								$shopId
						) );
					}

					if (! empty ( $seoUrl )) {
					    $seoUrl = $this->substituteSpecialChars($seoUrl);
						$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
						$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
								$seoUrl,
								'%sCustom='.$cmsStaticID,
								$shopId
						) );
						if (! empty ( $url2 )) {
							$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id']
							) );
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id'],
									$url2 ['org_path'],
									$shopId
							) );
						} else {
							$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
							$pathExists = Shopware()->Db()->fetchOne($sql, array(
									$seoUrl,
									$shopId
							));

							if(empty($pathExists)){
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
								Shopware ()->Db ()->query ( $sql, array (
										'%sCustom='.$cmsStaticID,
										$shopId
								) );

								$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
								Shopware ()->Db ()->query ( $sql, array (
										$seoUrl,
										'sViewport=custom&sCustom='.$cmsStaticID,
										1,
										$shopId
								) );
							}
						}
					}
				}
			}

			$sql = "SELECT cmsStaticID, mbdus_seourl, link FROM s_cms_static_attributes csa, s_cms_static cs WHERE cs.id = csa.cmsStaticID AND mbdus_seourl IS NOT NULL AND mbdus_seourl != '' AND link LIKE '%sViewport=ticket&sFid=%'";
			$formulars = Shopware()->Db()->fetchAll($sql);
			foreach($formulars as $formular){
				foreach($shopIds as $shopId){
					$shopId = $shopId['id'];
					$array = explode ( 'sFid=', $formular['link'] );
					$sFid = $array[1];
					$seoUrl = "";
					$cmsStaticID = $formular['cmsStaticID'];

					$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='forms' AND objectkey=? AND objectlanguage=?";
					$data = Shopware()->Db()->fetchOne($sql,array($cmsStaticID,$shopId));
					$data = unserialize($data, ['allowed_classes' => false]);
					$seoUrl = $data['mbdusSeoUrl'];

					if(empty($seoUrl) && $config['formularinherit'] == 1){
						$seoUrl = $formular['mbdus_seourl'];
					}

					if(empty($seoUrl) && $config['formularinherit'] == 0){
						$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE org_path LIKE ? AND subshopID = ?";
						if (version_compare($version, '5.5', '<')) {
							Shopware ()->Db ()->query ( $sql, array (
									'sViewport=ticket&sFid='.$sFid,
									$shopId
							) );
						}
						else{
							Shopware ()->Db ()->query ( $sql, array (
									'sViewport=forms&sFid='.$sFid,
									$shopId
							) );
						}
					}

					if (! empty ( $seoUrl )) {
					    $seoUrl = $this->substituteSpecialChars($seoUrl);
						$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
						if (version_compare($version, '5.5', '<')) {
							$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
									$seoUrl,
									'sViewport=ticket&sFid='.$sFid,
									$shopId
							) );
						}
						else{
							$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
									$seoUrl,
									'sViewport=forms&sFid='.$sFid,
									$shopId
							) );
						}
						if (! empty ( $url2 )) {
							$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id']
							) );
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id'],
									$url2 ['org_path'],
									$shopId
							) );
						} else {
							$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
							$pathExists = Shopware()->Db()->fetchOne($sql, array(
									$seoUrl,
									$shopId
							));

							if(empty($pathExists)){
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
								if (version_compare($version, '5.5', '<')) {
									Shopware ()->Db ()->query ( $sql, array (
											'sViewport=ticket&sFid='.$sFid,
											$shopId
									) );
								}
								else{
									Shopware ()->Db ()->query ( $sql, array (
											'sViewport=forms&sFid='.$sFid,
											$shopId
									) );
								}

								$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
								if (version_compare($version, '5.5', '<')) {
									Shopware ()->Db ()->query ( $sql, array (
											$seoUrl,
											'sViewport=ticket&sFid='.$sFid,
											1,
											$shopId
									) );
								}
								else{
									Shopware ()->Db ()->query ( $sql, array (
											$seoUrl,
											'sViewport=forms&sFid='.$sFid,
											1,
											$shopId
									) );
								}
							}
						}
					}
				}
			}

			$sql = "SELECT cmsSupportID, mbdus_seourl FROM s_cms_support_attributes WHERE mbdus_seourl IS NOT NULL AND mbdus_seourl != ''";
			$formulars = Shopware()->Db()->fetchAll($sql);
				foreach($formulars as $formular){
					foreach($shopIds as $shopId){
						$shopId = $shopId['id'];
						$sFid = $formular['cmsSupportID'];
						$seoUrl = "";

						$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='forms' AND objectkey=? AND objectlanguage=?";
						$data = Shopware()->Db()->fetchOne($sql,array($sFid,$shopId));
						$data = unserialize($data, ['allowed_classes' => false]);
						$seoUrl = $data['mbdusSeoUrl'];

						if(empty($seoUrl) && $config['formularinherit'] == 1){
							$seoUrl = $formular['mbdus_seourl'];
						}

						if(empty($seoUrl) && $config['formularinherit'] == 0){
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE org_path LIKE ? AND subshopID = ?";
							if (version_compare($version, '5.5', '<')) {
								Shopware ()->Db ()->query ( $sql, array (
										'sViewport=ticket&sFid='.$sFid,
										$shopId
								) );
							}
							else{
								Shopware ()->Db ()->query ( $sql, array (
										'sViewport=forms&sFid='.$sFid,
										$shopId
								) );
							}
						}

						if (! empty ( $seoUrl )) {
						    $seoUrl = $this->substituteSpecialChars($seoUrl);
							$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
							if (version_compare($version, '5.5', '<')) {
								$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
										$seoUrl,
										'sViewport=ticket&sFid='.$sFid,
										$shopId
								) );
							}
							else{
								$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
										$seoUrl,
										'sViewport=forms&sFid='.$sFid,
										$shopId
								) );
							}
							if (! empty ( $url2 )) {
								$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
								Shopware ()->Db ()->query ( $sql, array (
										$url2 ['id']
								) );
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
								Shopware ()->Db ()->query ( $sql, array (
										$url2 ['id'],
										$url2 ['org_path'],
										$shopId
								) );
							} else {
								$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
								$pathExists = Shopware()->Db()->fetchOne($sql, array(
										$seoUrl,
										$shopId
								));

								if(empty($pathExists)){
									$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
									if (version_compare($version, '5.5', '<')) {
										Shopware ()->Db ()->query ( $sql, array (
												'sViewport=ticket&sFid='.$sFid,
												$shopId
										) );
									}
									else{
										Shopware ()->Db ()->query ( $sql, array (
												'sViewport=forms&sFid='.$sFid,
												$shopId
										) );
									}

									$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
									if (version_compare($version, '5.5', '<')) {
										Shopware ()->Db ()->query ( $sql, array (
												$seoUrl,
												'sViewport=ticket&sFid='.$sFid,
												1,
												$shopId
										) );
									}
									else{
										Shopware ()->Db ()->query ( $sql, array (
												$seoUrl,
												'sViewport=forms&sFid='.$sFid,
												1,
												$shopId
										) );
									}
								}
							}
						}
				}
			}
		} catch ( \Exception $e ) {
			echo "Exception:";
			print_r ( $e );
			exit ();
		}
	}

	/**
	 * Shopware_Controllers_Backend_Seo_seoStaticAction::after: afterCreateSeoStatic
	 *
	 * @return array
	 */
	public function afterCreateSeoStatic(\Enlight_Hook_HookArgs $args) {
		$configReader = $this->container->get('shopware.plugin.config_reader');
		$config = $configReader->getByPluginName('MbdusSeoUrl');
		$version = Shopware()->Container()->get('config')->get('version');
		$shopIds = array();
		if (method_exists($args->getSubject(), 'Request')){
			$tmpShopId = (int) $args->getSubject()->Request()->getParam('shopId', 1);
			array_push($shopIds, ['id'=>$tmpShopId]);
		}
		if(empty($shopIds)){
			$sql = "SELECT id FROM s_core_shops";
			$shopIds = Shopware()->Db()->fetchAll($sql);
		}
		try {
			$sql = "SELECT cmsSupportID, mbdus_seourl FROM s_cms_support_attributes WHERE mbdus_seourl IS NOT NULL AND mbdus_seourl != ''";
			$formulars = Shopware()->Db()->fetchAll($sql);
			foreach($formulars as $formular){
				foreach($shopIds as $shopId){
					$shopId = $shopId['id'];
					$sFid = $formular['cmsSupportID'];
					$seoUrl = "";

					$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='forms' AND objectkey=? AND objectlanguage=?";
					$data = Shopware()->Db()->fetchOne($sql,array($sFid,$shopId));
					$data = unserialize($data, ['allowed_classes' => false]);
					$seoUrl = $data['mbdusSeoUrl'];

					if(empty($seoUrl) && $config['formularinherit'] == 1){
						$seoUrl = $formular['mbdus_seourl'];
					}

					if(empty($seoUrl) && $config['formularinherit'] == 0){
						$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE org_path LIKE ? AND subshopID = ?";
						if (version_compare($version, '5.5', '<')) {
							Shopware ()->Db ()->query ( $sql, array (
									'sViewport=ticket&sFid='.$sFid,
									$shopId
							) );
						}
						else{
							Shopware ()->Db ()->query ( $sql, array (
									'sViewport=forms&sFid='.$sFid,
									$shopId
							) );
						}
					}
					$sFid = $formular['cmsSupportID'];

					if (! empty ( $seoUrl )) {
					    $seoUrl = $this->substituteSpecialChars($seoUrl);
						$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
						if (version_compare($version, '5.5', '<')) {
							$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
									$seoUrl,
									'sViewport=ticket&sFid='.$sFid,
									$shopId
							) );
						}
						else{
							$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
									$seoUrl,
									'sViewport=forms&sFid='.$sFid,
									$shopId
							) );
						}
						if (! empty ( $url2 )) {
							$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id']
							) );
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id'],
									$url2 ['org_path'],
									$shopId
							) );
						} else {
							$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
							$pathExists = Shopware()->Db()->fetchOne($sql, array(
									$seoUrl,
									$shopId
							));

							if(empty($pathExists)){
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
								if (version_compare($version, '5.5', '<')) {
									Shopware ()->Db ()->query ( $sql, array (
											'sViewport=ticket&sFid='.$sFid,
											$shopId
									) );
								}
								else{
									Shopware ()->Db ()->query ( $sql, array (
											'sViewport=forms&sFid='.$sFid,
											$shopId
									) );
								}
								$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
								if (version_compare($version, '5.5', '<')) {
									Shopware ()->Db ()->query ( $sql, array (
											$seoUrl,
											'sViewport=ticket&sFid='.$sFid,
											1,
											$shopId
									) );
								}
								else{
									Shopware ()->Db ()->query ( $sql, array (
											$seoUrl,
											'sViewport=forms&sFid='.$sFid,
											1,
											$shopId
									) );
								}
							}
						}
					}
				}
			}
		} catch ( \Exception $e ) {
			echo "Exception:";
			print_r ( $e );
			exit ();
		}
	}

	/**
	 * Shopware_Controllers_Backend_Seo_seoEmotionAction::after: afterCreateSeoEmotion
	 *
	 * @return array
	 */
	public function afterCreateSeoEmotion(\Enlight_Hook_HookArgs $args) {
		$shopIds = array();
		if (method_exists($args->getSubject(), 'Request')){
			$tmpShopId = (int) $args->getSubject()->Request()->getParam('shopId', 1);
			array_push($shopIds, ['id'=>$tmpShopId]);
		}
		if(empty($shopIds)){
			$sql = "SELECT id FROM s_core_shops";
			$shopIds = Shopware()->Db()->fetchAll($sql);
		}
		try {
			$sql = "SELECT emotionID, mbdus_seourl FROM s_emotion_attributes WHERE mbdus_seourl IS NOT NULL AND mbdus_seourl != ''";
			$emotions = Shopware()->Db()->fetchAll($sql);
			foreach($emotions as $emotion){
				foreach($shopIds as $shopId){
					$shopId = $shopId['id'];
					$seoUrl = "";
					$name = "";
					$emotionID = $emotion['emotionID'];

					if($shopId!=1){
						$sql = "SELECT objectdata FROM s_core_translations WHERE objecttype='emotion' AND objectkey=? AND objectlanguage=?";
						$data = Shopware()->Db()->fetchOne($sql,array($emotionID,$shopId));
						$data = unserialize($data, ['allowed_classes' => false]);
						$seoUrl = $data['mbdusSeoUrl'];
						$name = $data['name'];
					}

					if(empty($seoUrl) && empty($name)){
						$seoUrl = $emotion['mbdus_seourl'];
					}

					if (! empty ( $seoUrl )) {
					    $seoUrl = $this->substituteSpecialChars($seoUrl);
						$sql = "SELECT * FROM s_core_rewrite_urls WHERE path=? AND org_path LIKE ? AND subshopID = ?";
						$url2 = Shopware ()->Db ()->fetchRow ( $sql, array (
								$seoUrl,
								'sViewport=campaign&emotionId='.$emotionID,
								$shopId
						) );
						if (! empty ( $url2 )) {
							$sql = "UPDATE s_core_rewrite_urls SET main=1 WHERE id = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id']
							) );
							$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE id != ? AND org_path LIKE ? AND subshopID = ?";
							Shopware ()->Db ()->query ( $sql, array (
									$url2 ['id'],
									$url2 ['org_path'],
									$shopId
							) );
						} else {
							$sql="SELECT path FROM s_core_rewrite_urls WHERE path LIKE ? AND subshopID = ?";
							$pathExists = Shopware()->Db()->fetchOne($sql, array(
									$seoUrl,
									$shopId
							));

							if(empty($pathExists)){
								$sql = "UPDATE s_core_rewrite_urls SET main=0 WHERE `org_path` LIKE ? AND subshopID = ?";
								Shopware ()->Db ()->query ( $sql, array (
										'sViewport=campaign&emotionId='.$emotionID,
										$shopId
								) );

								$sql = "INSERT INTO s_core_rewrite_urls (path,org_path,main,subshopID) VALUES (?,?,?,?)";
								Shopware ()->Db ()->query ( $sql, array (
										$seoUrl,
										'sViewport=campaign&emotionId='.$emotionID,
										1,
										$shopId
								) );
							}
						}
					}
				}
			}
		} catch ( \Exception $e ) {
			echo "Exception:";
			print_r ( $e );
			exit ();
		}
	}

	/**
	 * substitute special chars for zend cache
	 *
	 * @param string $string
	 * @return array
	 */
	public function substituteSpecialChars($string) {
	    $configReader = $this->container->get('shopware.plugin.config_reader');
	    $config = $configReader->getByPluginName('MbdusSeoUrl');
	    if($config['rewritevowels']){
    	    $substitution = array (
    	        "Ä" => "ae",
    	        "ä" => "ae",
    	        "Ö" => "oe",
    	        "ö" => "oe",
    	        "Ü" => "ue",
    	        "ü" => "ue",
    	        "ß" => "ss"
    	    );
    	    $string = str_replace ( array_keys ( $substitution ), array_values ( $substitution ), $string );
	    }
	    return $string;
	}
}