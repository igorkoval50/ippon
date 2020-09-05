<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendCategory implements SubscriberInterface
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
			'Shopware_Controllers_Backend_Category::getDetailAction::after' => 'afterCategoryGetDetail',
			'Enlight_Controller_Action_PostDispatch_Backend_Category' => 'onPostDispatchBackendCategory'
		);
	}
	
	/**
	 * Shopware_Controllers_Backend_Category_getDetailAction::after: afterCategoryGetDetail
	 *
	 * @return array
	 */
	public function afterCategoryGetDetail(\Enlight_Event_EventArgs $args) {
		$view = $args->getSubject()->View();
		$data = $view->data;
		$categoryID = $data['id'];
	
		$sql = "SELECT mbdus_seourl FROM s_categories_attributes WHERE categoryID = ?";
		$path = Shopware()->Db()->fetchOne($sql,array($categoryID));
		if(empty($path)){
			$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
			$path = Shopware()->Db()->fetchOne($sql,array('sViewport=cat&sCategory='.$categoryID));
			if(empty($path)){
				$path="";
			}
		}
		$attributes = $view->data['attribute'];
		$attributes['mbdusSeoUrl']=$path;
		$data['attribute'] = $attributes;
		$view->assign('data',$data);
	}
	
	/**
	 * override backend category detail
	 * Enlight_Controller_Action_PostDispatch_Backend_Category
	 */
	public function onPostDispatchBackendCategory(\Enlight_Event_EventArgs $args) {
		/**
		 *
		 * @var $view Enlight_View_Default
		 */
		$view = $args->getSubject ()->View ();
	
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		// Add template directory
		$view->addTemplateDir ( $this->getPluginPath() . '/Resources/views' );
	
		if ($args->getRequest ()->getActionName () === 'load') {
			$view->extendsTemplate ( 'backend/mbdus_seo_url/category/controller/main.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/category/controller/settings.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/category/view/tabs/settings.js' );
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