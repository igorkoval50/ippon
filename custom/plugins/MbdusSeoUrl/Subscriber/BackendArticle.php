<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendArticle implements SubscriberInterface
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
			'Shopware_Controllers_Backend_Article::loadStoresAction::after' => 'afterArticleLoadStores',
			'Shopware_Controllers_Backend_Article::getArticleAction::after' => 'afterGetArticle',
			'Enlight_Controller_Action_PostDispatch_Backend_Article' => 'onPostDispatchBackendArticle'
		);
	}
	
	/**
	 * Shopware_Controllers_Backend_Article_loadStoresAction::after: afterArticleLoadStores
	 *
	 * @return array
	 */
	public function afterArticleLoadStores(\Enlight_Event_EventArgs $args) {
		$view = $args->getSubject()->View();
		$articleID = $view->data['article'][0]['id'];
		$articledetailsID = $view->data['article'][0]['mainDetail']['id'];
		$sql = "SELECT mbdus_seourl FROM s_articles_attributes WHERE articledetailsID = ?";
		$path = Shopware()->Db()->fetchOne($sql,array($articledetailsID));
		if(empty($path)){
			$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
			$path = Shopware()->Db()->fetchOne($sql,array('sViewport=detail&sArticle='.$articleID));
			if(empty($path)){
				$path="";
			}
		}
		$data = $view->data;
		$article = $view->data['article'];
		$attributes = $view->data['article'][0]['attribute'];
		$attributes['mbdusSeoUrl']=$path;
		$article[0]['attribute']=$attributes;
		$attributes = $view->data['article'][0]['mainDetail']['attribute'];
		$attributes['mbdusSeoUrl']=$path;
		$article[0]['mainDetail']['attribute']=$attributes;
		$data['article'] = $article;
		$view->assign('data',$data);
	}
	
	/**
	 * Shopware_Controllers_Backend_Article_getArticleAction::after: afterGetArticle
	 *
	 * @return array
	 */
	public function afterGetArticle(\Enlight_Event_EventArgs $args) {
		$view = $args->getSubject()->View();
		$articleID = $view->data[0]['id'];
		$articledetailsID = $view->data[0]['mainDetail']['id'];
		$sql = "SELECT mbdus_seourl FROM s_articles_attributes WHERE articledetailsID = ?";
		$path = Shopware()->Db()->fetchOne($sql,array($articledetailsID));
		if(empty($path)){
			$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
			$path = Shopware()->Db()->fetchOne($sql,array('sViewport=detail&sArticle='.$articleID));
			if(empty($path)){
				$path="";
			}
		}
		$data = $view->data;
		$attributes = $view->data[0]['mainDetail']['attribute'];
		$attributes['mbdusSeourl']=$path;
		$view->data[0]['mainDetail']['attribute'] = $attributes;
		$data[0]['mainDetail']['attribute'] = $attributes;
		$view->assign('data',$data);
	}
	
	/**
	 * override backend article detail
	 * Enlight_Controller_Action_PostDispatch_Backend_Article
	 */
	public function onPostDispatchBackendArticle(\Enlight_Event_EventArgs $args) {
		/**
		 *
		 * @var $view Enlight_View_Default
		 */
		$view = $args->getSubject ()->View ();
	
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		// Add template directory
		$view->addTemplateDir ( $this->getPluginPath() . '/Resources/views' );
		if ($args->getRequest ()->getActionName () === 'load') {
			$view->extendsTemplate ( 'backend/mbdus_seo_url/article/controller/detail.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/article/controller/main.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/article/view/detail/base.js' );
		
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