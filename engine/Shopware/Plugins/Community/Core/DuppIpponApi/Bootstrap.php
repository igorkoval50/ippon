<?php
/**
 * Dupp Ippon Api
 * @copyright Copyright (c) 2018, Dupp GmbH (http://www.dupp.de)
 */

class Shopware_Plugins_Core_DuppIpponApi_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{	
	/**
	 * Return Capabilities for the Plugin
	 */
	public function getCapabilities()
	{
		return array
		(
			'install' => true,
			'update' => true,
			'enable' => true
		);
	}

	/**
	 * Return plugin label.
	 */
	public function getLabel()
	{
		return 'Dupp Ippon Api';
	}


	/**
	 * Return plugin version number
	 */
	public function getVersion()
	{
		return '1.0.0.0';
	}

	/**
	 * Return Information about the plugin
	 */
	public function getInfo()
	{
		return array(
			'version' => $this->getVersion(),
			'label' => $this->getLabel(),
			'author' => 'Dupp GmbH',
			'copyright' => 'Dupp GmbH',
			'description' => 'Dupp Ippon API Extensions',
			'support' => 'http://support.dupp.de/',
			'link' => 'http://www.dupp.de'
		);
	}

	/**
	 * Install the Dupp Ippon Api plugin
	 */
	public function install()
	{
		$this->subscribeEvents();
		
		// $this->updateSchema();
		
		return true;
	}

	public function update($oldVersion)
	{
		return true;
	}

	/**
	 * Creates the database scheme from an existing doctrine model.
	 *
	 * Will remove the table first, so handle with care.
	 */
	protected function updateSchema()
	{
	
	}

	/**
	 * subscribe Events for Dupp Ippon Api plugin
	 */
	private function subscribeEvents()
	{
		$this->subscribeEvent
		(
				'Enlight_Controller_Front_StartDispatch', 'onEnlightControllerFrontStartDispatch'
		);

		$this->subscribeEvent
		(
				'Enlight_Controller_Dispatcher_ControllerPath_Api_DuppIppon', 'onGetDuppIpponApiController'
		);  		
	}

	/**
	 * Register plugin namespace in autoloader
	 */
	public function afterInit()
	{
		/** @var Enlight_Loader $loader */
		$loader = $this->get('loader');
		$loader->registerNamespace
		(
			'ShopwarePlugins\\'.$this->getLabel(),
			__DIR__ . '/'
		);
	}

	/**
	 * Register namespace for the Dupp Ippon Api plugin     
	 */
	public function onEnlightControllerFrontStartDispatch(Enlight_Event_EventArgs $args)
	{
		$this->Application()->Loader()->registerNamespace
		(
				'Shopware\Components', $this->Path() . 'Components/'
		);
		
		$this->registerCustomModels();
	}

	/**
	 * Return Dupp Ippon Api Controller path.
	 */
	public function onGetDuppIpponApiController()
	{
		return $this->Path() . 'Controllers/Api/DuppIppon.php';
	}
}
