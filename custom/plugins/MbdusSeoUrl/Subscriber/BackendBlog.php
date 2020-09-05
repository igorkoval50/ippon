<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendBlog implements SubscriberInterface
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
			'Shopware_Controllers_Backend_Blog::getDetailAction::after' => 'afterBlogGetDetail',
			'Enlight_Controller_Action_PostDispatch_Backend_Blog' => 'onPostDispatchBackendBlog'
		);
		
	}
	
	/**
	 * Shopware_Controllers_Backend_Blog_getDetailAction::after: afterBlogGetDetail
	 *
	 * @return array
	 */
	public function afterBlogGetDetail(\Enlight_Event_EventArgs $args) {
		$view = $args->getSubject()->View();
		$data = $view->data;
		$blogID = $data['id'];
		$categoryID = $data['categoryId'];
	
		$sql = "SELECT mbdus_seourl FROM s_blog_attributes WHERE blog_id = ?";
		$path = Shopware()->Db()->fetchOne($sql,array($blogID));
		if(empty($path)){
			$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
			$path = Shopware()->Db()->fetchOne($sql,array('sViewport=blog&sAction=detail&sCategory='.$categoryID.'&blogArticle='.$blogID));
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
	 * override backend blog detail
	 * Enlight_Controller_Action_PostDispatch_Backend_Blog
	 */
	public function onPostDispatchBackendBlog(\Enlight_Event_EventArgs $args) {
		/**
		 *
		 * @var $view Enlight_View_Default
		 */
		$view = $args->getSubject ()->View ();
	
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		// Add template directory
		$view->addTemplateDir ( $this->getPluginPath() . '/Resources/views' );
	
		if ($args->getRequest ()->getActionName () === 'load') {
			$view->extendsTemplate ( 'backend/mbdus_seo_url/blog/controller/blog.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/blog/view/blog/detail/sidebar/seo.js' );
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