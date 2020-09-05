<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendForm implements SubscriberInterface
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
			'Shopware_Controllers_Backend_Form::getFormsAction::after' => 'afterFormGetForms',
			'Enlight_Controller_Action_PostDispatch_Backend_Form' => 'onPostDispatchBackendForm'
		);
	}
	
	/**
	 * Shopware_Controllers_Backend_Form_getFormsAction::after: afterFormGetForms
	 *
	 * @return array
	 */
	public function afterFormGetForms(\Enlight_Event_EventArgs $args) {
		if ($id = $args->getSubject()->Request()->getParam('id')) {
			$view = $args->getSubject()->View();
			$data = $view->data;
			$formID = $data[0]['id'];
	
			$sql = "SELECT mbdus_seourl FROM s_cms_support_attributes WHERE cmsSupportID = ?";
			$path = Shopware()->Db()->fetchOne($sql,array($formID));
			$version = Shopware()->Container()->get('config')->get('version');
			if(empty($path)){
				$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
				if (version_compare($version, '5.5', '<')) {
					$path = Shopware()->Db()->fetchOne($sql,array('sViewport=ticket&sFid='.$formID));
				}
				else{
					$path = Shopware()->Db()->fetchOne($sql,array('sViewport=forms&sFid='.$formID));
				}
				if(empty($path)){
					$path="";
				}
			}
			$attributes = $view->data[0]['attribute'];
			$attributes['mbdusSeoUrl']=$path;
			$data[0]['attribute'] = $attributes;
			$view->assign('data',$data);
		}
	}
	
	/**
	 * override backend form detail
	 * Enlight_Controller_Action_PostDispatch_Backend_Form
	 */
	public function onPostDispatchBackendForm(\Enlight_Event_EventArgs $args) {
		/**
		 *
		 * @var $view Enlight_View_Default
		 */
		$view = $args->getSubject ()->View ();
	
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		// Add template directory
		$view->addTemplateDir ( $this->getPluginPath() . '/Resources/views' );
	
		if ($args->getRequest ()->getActionName () === 'load') {	
			$view->extendsTemplate ( 'backend/mbdus_seo_url/form/controller/main.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/form/view/main/formpanel.js' );
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