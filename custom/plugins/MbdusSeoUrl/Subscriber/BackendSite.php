<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendSite implements SubscriberInterface
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
			'Shopware_Controllers_Backend_Site::getNodesAction::after' => 'afterSiteGetNodes',
			'Enlight_Controller_Action_PostDispatch_Backend_Site' => 'onPostDispatchBackendSite'
		);
	}
	
	/**
	 * Shopware_Controllers_Backend_Site_getNodesAction::after: afterSiteGetNodes
	 *
	 * @return array
	 */
	public function afterSiteGetNodes(\Enlight_Event_EventArgs $args) {
		$view = $args->getSubject()->View();
		$node = $args->getSubject()->Request()->getParam('node');
	
		$nodes = $view->nodes;
		if(isset($nodes[0]['text'])){
			foreach($nodes as $key=>&$node){
				$cmsStaticID = $node['helperId'];
	
				$sql = "SELECT mbdus_seourl FROM s_cms_static_attributes WHERE cmsStaticID = ?";
				$path = Shopware()->Db()->fetchOne($sql,array($cmsStaticID));
				if(empty($path)){
					$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
					$path = Shopware()->Db()->fetchOne($sql,array('sViewport=custom&sCustom='.$cmsStaticID));
					if(empty($path)){
						$path="";
					}
				}
				$attributes = $node['attribute'];
				$attributes['mbdusSeoUrl']=$path;
				$nodes[$key]['attribute'] = $attributes;
				if($node['nodes']){
					$this->recursiveSubNodes($node);
				}
			}
			$view->assign('nodes',$nodes);
		}
	}
	
	/**
	 * set seo attribute for node
	 */
	public function recursiveSubNodes(&$node){
		if($node['nodes']){
			if(isset($node['nodes'][0]['text'])){
				foreach($node['nodes'] as $key=>&$node){
					$cmsStaticID = $node['helperId'];
	
					$sql = "SELECT mbdus_seourl FROM s_cms_static_attributes WHERE cmsStaticID = ?";
					$path = Shopware()->Db()->fetchOne($sql,array($cmsStaticID));
					if(empty($path)){
						$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
						$path = Shopware()->Db()->fetchOne($sql,array('sViewport=custom&sCustom='.$cmsStaticID));
						if(empty($path)){
							$path="";
						}
					}
					$attributes = $node['attribute'];
					$attributes['mbdusSeoUrl']=$path;
					$node['attribute'] = $attributes;
					if($node['nodes']){
						$this->recursiveSubNodes($node);
					}
				}
			}
		}
	}
	
	/**
	 * override backend site detail
	 * Enlight_Controller_Action_PostDispatch_Backend_Site
	 */
	public function onPostDispatchBackendSite(\Enlight_Event_EventArgs $args) {
		/**
		 *
		 * @var $view Enlight_View_Default
		 */
		$view = $args->getSubject ()->View ();
	
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		// Add template directory
		$view->addTemplateDir ( $this->getPluginPath() . '/Resources/views' );
	    $version = Shopware()->Container()->get('config')->get('version');
		if ($args->getRequest ()->getActionName () === 'load') {
			if (version_compare($version, '5.5', '<')) {
				$view->extendsTemplate ( 'backend/mbdus_seo_url_until_5_5/site/controller/tree.js' );
			}
			else{
				$view->extendsTemplate ( 'backend/mbdus_seo_url/site/controller/tree.js' );
			}
			$view->extendsTemplate ( 'backend/mbdus_seo_url/site/controller/form.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/site/view/site/form.js' );
		}
	}
	
	/**
	 * @return string
	 */
	public function getPluginPath()
	{
		return $this->container->getParameter('mbdus_seo_url.plugin_dir');
	}
}