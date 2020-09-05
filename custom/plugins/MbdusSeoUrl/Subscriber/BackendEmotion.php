<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendEmotion implements SubscriberInterface
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
			'Shopware_Controllers_Backend_Emotion::detailAction::after' => 'afterEmotionDetail',
			'Enlight_Controller_Action_PostDispatch_Backend_Emotion' => 'onPostDispatchBackendEmotion'
		);
	}
	
	/**
	 * Shopware_Controllers_Backend_Emotion_detailAction::after: afterEmotionDetail
	 *
	 * @return array
	 */
	public function afterEmotionDetail(\Enlight_Event_EventArgs $args) {
		$view = $args->getSubject()->View();
		$data = $view->data;
		$emotionID = $data['id'];
	
		$sql = "SELECT mbdus_seourl FROM s_emotion_attributes WHERE emotionID = ?";
		$path = Shopware()->Db()->fetchOne($sql,array($emotionID));
		if(empty($path)){
			$sql = "SELECT path FROM `s_core_rewrite_urls` WHERE `org_path` LIKE ? AND `main` = 1 AND subshopID = 1";
			$path = Shopware()->Db()->fetchOne($sql,array("sViewport=campaign&emotionId=".$emotionID));
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
	 * override backend emotion detail
	 * Enlight_Controller_Action_PostDispatch_Backend_Emotion
	 */
	public function onPostDispatchBackendEmotion(\Enlight_Event_EventArgs $args) {
		/**
		 *
		 * @var $view Enlight_View_Default
		 */
		$view = $args->getSubject ()->View ();
	
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		// Add template directory
		$view->addTemplateDir ( $this->getPluginPath() . '/Resources/views' );
	
		if ($args->getRequest ()->getActionName () === 'load') {
			$view->extendsTemplate ( 'backend/mbdus_seo_url/emotion/controller/detail.js');
			$view->extendsTemplate ( 'backend/mbdus_seo_url/emotion/view/detail/settings.js');
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