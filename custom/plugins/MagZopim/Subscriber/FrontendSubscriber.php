<?php

namespace MagZopim\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use Shopware\Components\Plugin\ConfigReader;

class FrontendSubscriber implements SubscriberInterface
{

    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @var string
     */
    private $pluginConfig;

    /**
     * @var string
     */
    private $configReader;

    /**
     * @param $pluginDirectory
	 * @param $pluginName
	 * @param $configReader
     */
    public function __construct($pluginDirectory, $pluginName, ConfigReader $configReader)
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->pluginConfig = $configReader->getByPluginName($pluginName);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
			'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onFrontendPostDispatch',
        ];
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onFrontendPostDispatch(\Enlight_Controller_ActionEventArgs $args)
   {
		$shop = false;

		if (Shopware()->Container()->initialized('shop')) {
			$shop = Shopware()->Container()->get('shop');
		}

		if (!$shop) {
			$shop = Shopware()->Container()->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
		}

		$config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('MagZopim', $shop);

		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->get('subject');
		$view = $controller->View();
		$request = $controller->Request();

		if($config['plugin_state'] && $config['zopimsource']) {

			if (!$config['pagehome'] && $request->getControllerName() == 'index') {
				$view->assign('active', '');
			} else {
				$view->assign('active', 'true');
			}			

			if ($config['setuserdata']) {
				$userData = Shopware()->Modules()->Admin()->sGetUserData();				

				if ($userData) {
					$userName = $userData['billingaddress']['firstname'].' '.$userData['billingaddress']['lastname'];
					$userEmail = $userData['additional']['user']['email'];

					$view->assign('username', $userName);
					$view->assign('useremail', $userEmail);
				}
			}	

			$view->assign('zopimsource', $config['zopimsource']);
			$view->assign('agenttitle', $config['agenttitle']);
			$view->assign('agenttext', $config['agenttext']);
			$view->assign('agentimage', $config['agentimage']);
			$view->assign('title', $config['title']);
			$view->assign('color', $config['color']);
			$view->assign('position', ($config['position']==1?'bl':'br'));
			$view->assign('positionmobile', ($config['positionmobile']==1?'bl':'br'));
			
			$view->addTemplateDir($this->pluginDirectory . '/Resources');

		}
    }
}