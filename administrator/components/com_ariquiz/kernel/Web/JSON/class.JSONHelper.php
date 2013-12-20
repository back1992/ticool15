<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('Web.JSON.JSON');

class AriJSONHelper
{
	function encode($data)
	{
		$jsonHandler =& AriJSONHelper::_getJSONHandler();

		return $jsonHandler->encode($data);
	}

	function decode($str)
	{
		if (empty($str)) return null;

		$jsonHandler =& AriJSONHelper::_getJSONHandler();

		return $jsonHandler->decode($str);
	}

	function &_getJSONHandler()
	{
		static $jsonHandler = null;

		if (is_null($jsonHandler))
		{
			$jsonHandler = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		}

		return $jsonHandler;
	}
}
?>