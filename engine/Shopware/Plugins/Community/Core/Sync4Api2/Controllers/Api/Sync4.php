<?php

/**
 * sync4 REST API Extensions
 * @copyright Copyright (c) 2014, Dupp GmbH (http://www.dupp.de)
 * @author Jan Eichmann
 * @author Mario Schwarz
 */
use Shopware\Components\Api\Exception as ApiException;

/**
 * Sync4 API Controller
 */
class Shopware_Controllers_Api_Sync4 extends Shopware_Controllers_Api_Rest {

    /**
     * @var Shopware\Components\Api\Resource\Sync4
     */
    protected $resource = null;

    /**
     * @var array of (function => HTTP method)
     */
    protected $allowedActions = array();

    /**
     * Initializes the sync4 API
     */
    public function init() {
        $this->resource = \Shopware\Components\Api\Manager::getResource('sync4');

        //add allowed actions. NOTE: PUT and DELETE don't work, will throw 405 error (batch operations)

        //status
        $this->addAllowedAction('getStatus', 'GET');

        //shops (languages)
        $this->addAllowedAction('getShops', 'GET');

        //countries
        $this->addAllowedAction('getCountries', 'GET');
        $this->addAllowedAction('getAreas', 'GET');

        //order with variants details
        $this->addAllowedAction('getOrder', 'GET');

        //payments
        $this->addAllowedAction('getPayments', 'GET');
        $this->addAllowedAction('getShipments', 'GET');
        $this->addAllowedAction('getShipmentStatus', 'GET');
        $this->addAllowedAction('getPaymentStatus', 'GET');

        //delete
        $this->addAllowedAction('deleteAllProducts', 'POST');
		$this->addAllowedAction('deleteProductDetail', 'POST');
        $this->addAllowedAction('deleteAllCategories', 'POST');
        $this->addAllowedAction('deleteEmptyCategories', 'POST');
        $this->addAllowedAction('deleteCategoryAssignments', 'POST');
		$this->addAllowedAction('deleteProductsXSell', 'POST');
        $this->addAllowedAction('deleteUnassignedFilters', 'POST');

        //manufacturers
        $this->addAllowedAction('getManufacturers', 'GET');
        $this->addAllowedAction('updateManufacturer', 'POST');
        $this->addAllowedAction('createManufacturer', 'POST');
        $this->addAllowedAction('deleteManufacturer', 'POST');

        //translations
        $this->addAllowedAction('updateVariantTranslation', 'POST');

        //pictures
        $this->addAllowedAction('uploadFile', 'POST');
        $this->addAllowedAction('deleteAllFilesInAlbum', 'POST');
        $this->addAllowedAction('setCategorieMedia', 'POST');

		// Attribut Products
        $this->addAllowedAction('getAttributProducts','GET');
        $this->addAllowedAction('getTaxRates','GET');
        $this->addAllowedAction('getArticleDetail','GET');
        $this->addAllowedAction('createCustomerPrice','POST');
        $this->addAllowedAction('deleteAllCustomerPriceLists','POST');
        $this->addAllowedAction('deleteFilteredCustomerPriceLists','POST');

        $this->addAllowedAction('addESD','POST');
		$this->addAllowedAction('deleteESD','POST');

        // Cache
		$this->addAllowedAction('warmUpCache', 'POST');

        // Seo
		$this->addAllowedAction('recreateSEOUrls', 'POST');
       
       //Filter     
        $this->addAllowedAction('addFilterValueImage', 'POST');    
        $this->addAllowedAction('removeTemplate','POST');
        $this->addAllowedAction('getTemplates','GET');
        $this->addAllowedAction('createTemplate','POST');
        $this->addAllowedAction('addProductToTemplate','POST');
        $this->addAllowedAction('getOptions','GET');
        $this->addAllowedAction('addOption','POST');
        $this->addAllowedAction('getValues','GET');
        $this->addAllowedAction('addValue','POST');
        $this->addAllowedAction('addOptionsAndValues','POST');
		$this->addAllowedAction('updateLockedOptions','POST');
				$this->addAllowedAction('updateLockedOptions','POST');
				
		// Live Shopping Functions
		$this->addAllowedAction('getLiveShoppingLiveShoppings', 'GET');
		$this->addAllowedAction('getLiveShoppingLiveShoppingJustPrices', 'GET');
		$this->addAllowedAction('createLiveShoppingLiveShopping', 'POST');
		$this->addAllowedAction('updateLiveShoppingLiveShopping', 'POST');
		$this->addAllowedAction('deleteLiveShoppingLiveShopping', 'POST');
		$this->addAllowedAction('getLiveShoppingPrices', 'GET');
		$this->addAllowedAction('createLiveShoppingPrice', 'POST');
		$this->addAllowedAction('updateLiveShoppingPrice', 'POST');
		$this->addAllowedAction('deleteLiveShoppingPrice', 'POST');
    }

    /**
     * Add a allowed Action to the Controller.
     * @param type $name
     * @param type $method
     * @return \Shopware_Controllers_Api_Sync4
     */
    protected function addAllowedAction($name, $method) {
        if (!isset($this->allowedActions[$name])) {
            $this->allowedActions[$name] = $method;
        }
        return $this;
    }

    /**
     * GET /api/sync4/?action={action}
     */
    public function indexAction() {
        $this->checkAndInvokeAction();
    }

    /**
     * POST /api/sync4/?action={action}
     */
    public function postAction() {
        $this->checkAndInvokeAction();
    }

    /**
     * DELETE /api/sync4/?action={action}
     */
    public function deleteAction() {
        $this->checkAndInvokeAction();
    }

    /**
     * PUT /api/sync4/?action={action}
     */
    public function putAction() {
        $this->checkAndInvokeAction();
    }

    /**
     * Check and invoke a action.
     * @throws ApiException\NotFoundException
     */
    protected function checkAndInvokeAction() {
        $action = $this->Request()->getParam('action', 'getStatus');
        $method = $this->Request()->getMethod();

        // check if action is allowed
        if (!array_key_exists($action, $this->allowedActions)) {
            throw new ApiException\NotFoundException("Action $action not found");
        }

        // check if method is valid
        if ($method != $this->allowedActions[$action]) {
            throw new ApiException\NotFoundException("Action $action Method $method not allowed");
        }

        // check if resource has an associated method
        if (!method_exists($this->resource, $action)) {
            throw new ApiException\NotFoundException("Action $action doesn't exist");
        }

        $this->resource->setParams($this->Request()->getParams());
        $this->resource->setData($this->Request()->getPost());

        //invoke action
        $result = $this->resource->{$action}();
        $this->View()->assign($result);
    }

}

?>