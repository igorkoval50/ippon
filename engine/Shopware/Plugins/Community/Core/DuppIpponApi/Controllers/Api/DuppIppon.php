<?php

/**
 * Dupp Ippon REST API Extensions
 * @copyright Copyright (c) 2018, Dupp GmbH (http://www.dupp.de)
 */
use Shopware\Components\Api\Exception as ApiException;

/**
 * Dupp Ippon API Controller
 */
class Shopware_Controllers_Api_DuppIppon extends Shopware_Controllers_Api_Rest
{
	/**
	 * @var Shopware\Components\Api\Resource\DuppIppon
	 */
	protected $resource = null;

	/**
	 * @var array of (function => HTTP method)
	 */
	protected $allowedActions = array();

	/**
	 * Initializes the Dupp Ippon API
	 */
	public function init()
	{
		$this->resource = \Shopware\Components\Api\Manager::getResource('DuppIppon');

		//add allowed actions. NOTE: PUT and DELETE don't work, will throw 405 error (batch operations)

		$this->addAllowedAction('createVariantListing', 'POST');
		$this->addAllowedAction('getStatus', 'GET');
	}

	/**
	 * Add a allowed Action to the Controller.
	 * @param type $name
	 * @param type $method
	 * @return \Shopware_Controllers_Api_DuppIppon
	 */
	protected function addAllowedAction($name, $method)
	{
		if ( ! isset( $this->allowedActions[$name] ) )
		{
			$this->allowedActions[$name] = $method;
		}
		return $this;
	}

	/**
	 * GET /api/DuppIppon/?action={action}
	 */
	public function indexAction()
	{
		$this->checkAndInvokeAction();
	}

	/**
	 * POST /api/DuppIppon/?action={action}
	 */
	public function postAction()
	{
		$this->checkAndInvokeAction();
	}

	/**
	 * DELETE /api/DuppIppon/?action={action}
	 */
	public function deleteAction()
	{
		$this->checkAndInvokeAction();
	}

	/**
	 * PUT /api/DuppIppon/?action={action}
	 */
	public function putAction()
	{
		$this->checkAndInvokeAction();
	}

	/**
	 * Check and invoke a action.
	 * @throws ApiException\NotFoundException
	 */
	protected function checkAndInvokeAction()
	{
		$action = $this->Request()->getParam('action', 'getStatus');
		$method = $this->Request()->getMethod();

		// check if action is allowed
		if (!array_key_exists($action, $this->allowedActions))
		{
			throw new ApiException\NotFoundException("Action $action not found");
		}

		// check if method is valid
		if ($method != $this->allowedActions[$action])
		{
			throw new ApiException\NotFoundException("Action $action Method $method not allowed");
		}

		// check if resource has an associated method
		if (!method_exists($this->resource, $action))
		{
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
