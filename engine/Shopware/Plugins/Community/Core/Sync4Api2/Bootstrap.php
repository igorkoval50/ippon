<?php
/**
 * sync4 REST API Extensions
 * @copyright Copyright (c) 2014, Dupp GmbH (http://www.dupp.de)
 * @author Jan Eichmann
 * @author Mario Schwarz
 */

class Shopware_Plugins_Core_Sync4Api2_Bootstrap extends Shopware_Components_Plugin_Bootstrap {
	
	/**
     * Return Capabilities for the Plugin
     */
    public function getCapabilities() {
        return array(
            'install' => true,
            'update' => true,
            'enable' => true
        );
    }

	/**
	 * Return plugin label.
     */
    public function getLabel() {
        return 'sync4 REST API Extensions';
    }


	/**
     * Return plugin version number
     */
    public function getVersion() {
        return '9.0.4.1';
    }

	/**
     * Return Information about the plugin
     */
    public function getInfo() {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'author' => 'Dupp GmbH',
            'copyright' => 'Dupp GmbH',
            'description' => 'sync4 REST API Extensions extends the default REST API, e.g. delete all products, get countries and areas, CRUD manufacturers, base64 pictures, etc.',
            'support' => 'http://support.sync4.de/',
            'link' => 'http://www.sync4.de'
        );
    }

	/**
     * Install the sync4api plugin
     */
    public function install() {
	    $service = Shopware()->Container()->get('shopware_attribute.crud_service');
        $service->update('s_articles_attributes', 'sync4VariantGuid', 'string', [
            'label' => 'Sync4 Guid',
            'supportText' => 'Sync4 Guid',
            'helpText' => 'Do not change',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => false,

            //in case of multi_selection or single_selection type, article entities can be selected,
            'entity' => 'Shopware\Models\Article\Article',

            //numeric position for the backend view, sorted ascending
            'position' => 0,

            //user can modify the attribute in the free text field module
            'custom' => false
        ]);
		Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
        $this->subscribeEvents();
        return true;
    }

	public function uninstall()
    {
        $service = Shopware()->Container()->get('shopware_attribute.crud_service');
        $service->delete('s_articles_attributes', 'sync4VariantGuid');
		Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
		return true;
    }

	/**
     * subscribe Events for sync4 plugin
     */
    private function subscribeEvents() {
        $this->subscribeEvent(
                'Enlight_Controller_Front_StartDispatch', 'onEnlightControllerFrontStartDispatch'
        );

        $this->subscribeEvent(
                'Enlight_Controller_Dispatcher_ControllerPath_Api_Sync4', 'onGetSync4ApiController'
        );  
    }
	
	/**
     * Register plugin namespace in autoloader
     */
    public function afterInit()
    {
        /** @var Enlight_Loader $loader */
        $loader = $this->get('loader');
        $loader->registerNamespace(
            'ShopwarePlugins\\'.$this->getLabel(),
            __DIR__ . '/'
        );
    }

	/**
     * Register namespace for the sync4api plugin     
     */
    public function onEnlightControllerFrontStartDispatch(Enlight_Event_EventArgs $args) {
        $this->Application()->Loader()->registerNamespace(
                'Shopware\Components', $this->Path() . 'Components/'
        );
    }

	/**
     * Return sync4api Controller path.
     */
    public function onGetSync4ApiController() {
        return $this->Path() . 'Controllers/Api/Sync4.php';
    }
   
}
