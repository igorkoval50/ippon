<?php

/**
 * Dupp Ippon REST API Extensions
 * @copyright Copyright (c) 2018, Dupp GmbH (http://www.dupp.de)
 */

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;

/**
 * Dupp Ippon API Resource
 */
class DuppIppon extends Resource
{
	private function Write($zeile)
	{
		/*
		$datei = fopen ("/var/www/vhosts/wwwshopwaretorsten/test/logs.txt", "a");
		fwrite ($datei, $zeile);
		fclose ($datei);
		*/
	}

	private function WriteLine($zeile)
	{
		/*
		$datei = fopen ("/var/www/vhosts/wwwshopwaretorsten/test/logs.txt", "a");
		fwrite ($datei, $zeile."\n");
		fclose ($datei);
		*/
	}
	
	/**
	 * @var array Parameters
	 */
	protected $_params;

	/**
	 * @var array POST data
	 */
	protected $_data;

	/**
	 * Set an array of parameters
	 * @param array $value
	 * @return \Shopware\Components\Api\Resource\DuppIppon
	 */
	public function setParams($value)
	{
		$this->_params = $value;
		
		return $this;
	}

	/**
	 * Set the post data
	 * @param type $value
	 * @return \Shopware\Components\Api\Resource\DuppIppon
	 */
	public function setData($value)
	{
		$this->_data = $value;
		
		return $this;
	}

	/**
	 * Get the post data
	 * @param type $value
	 * @return \Shopware\Components\Api\Resource\DuppIppon
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * Retrieve a parameter
	 * @param mixed $key
	 * @param mixed $default Default value to use if key not found
	 * @return mixed
	 */
	protected function getParam($key, $default = null)
	{
		$key = (string) $key;
		
		if ( isset( $this->_params[$key] ) )
		{
			return $this->_params[$key];
		}

		return $default;
	}

	/**
	 * Retrieve an array of status information.
	 * @return array
	 */
	public function getStatus()
	{
		return array('success' => true, 'message' => 'Dupp Ippon REST API Extension is running');
	}
	
	public function createVariantListing()
	{
		$this->checkPrivilege('write');

		$data = $this->getData();
        if (empty($data)) {
			throw new ApiException\ParameterMissingException();
        }	

		$Id = $data['Id'];
		try
		{
			$sql = "DELETE FROM pix_variantlistingswf_config WHERE `id` = '". $data['Id']."'";
			$result = Shopware()->Db()->query($sql);
			
			$sql = "INSERT INTO pix_variantlistingswf_config (id,pix_am_status) VALUES ('" .  $data['Id'] . "','" . $data["status"] . "')";
            $erg = Shopware()->DB()->query($sql);

			return array('success' => true, 'data' => []);
		}
		catch (Exception $e)
		{
			$this->WriteLine("EXCEPTION");
			return array('success' => false, 'data' => null);
		}
	}
}

?>
