<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendSupplier implements SubscriberInterface
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
			'Shopware_Controllers_Backend_Supplier::getSuppliersAction::after' => 'afterSupplierGetSuppliers',
			'Enlight_Controller_Action_PostDispatch_Backend_Supplier' => 'onPostDispatchBackendSupplier'
		);
	}
	
	/**
	 * Shopware_Controllers_Backend_Supplier_getSuppliersAction::after: afterSupplierGetSuppliers
	 *
	 * @return array
	 */
	public function afterSupplierGetSuppliers(\Enlight_Event_EventArgs $args) {
		if ($id = $args->getSubject()->Request()->getParam('id')) {
			$view = $args->getSubject()->View();
			$data = $view->data;
			$supplierID = $data[0]['id'];

			$sql = "SELECT mbdus_seourl FROM s_articles_supplier_attributes WHERE supplierID = ?";
			$path = Shopware()->Db()->fetchOne($sql,array($supplierID));
			if(empty($path)){
				$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
				$path = Shopware()->Db()->fetchOne($sql,array('sViewport=listing&sAction=manufacturer&sSupplier='.$supplierID));
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
	 * override backend supplier detail
	 * Enlight_Controller_Action_PostDispatch_Backend_Supplier
	 */
	public function onPostDispatchBackendSupplier(\Enlight_Event_EventArgs $args) {
		/**
		 *
		 * @var $view Enlight_View_Default
		 */
		$view = $args->getSubject ()->View ();
	
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		// Add template directory
		$view->addTemplateDir ( $this->getPluginPath() . '/Resources/views' );
	
		if ($args->getRequest ()->getActionName () === 'load') {
			$view->extendsTemplate ( 'backend/mbdus_seo_url/supplier/controller/main.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/supplier/view/main/create.js' );
			$view->extendsTemplate ( 'backend/mbdus_seo_url/supplier/view/main/edit.js' );
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