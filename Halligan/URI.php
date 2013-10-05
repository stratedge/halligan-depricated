<?php

namespace Halligan;

class URI {

	public static $segments = array();


	//---------------------------------------------------------------------------------------------
	

	public static function parseSegments()
	{
		//Get the correct URI based on the URL or the CLI appropriately
		if(substr(PHP_SAPI, 0, 3) == 'cli')
		{
			$uri = implode("/", array_slice($_SERVER['argv'], 1));
		}
		else
		{
			$uri = $_SERVER['REQUEST_URI'];
		}	

		//Remove GET parameters
		if(strpos($uri, '?') !== FALSE)	$uri = substr($uri, 0, strpos($uri, '?'));
		
		if(strpos($uri, '/') === 0) $uri = substr($uri, 1);
		static::$segments = explode('/', $uri);
	}


	//---------------------------------------------------------------------------------------------
	

	public static function getController()
	{
		if(isset(static::$segments[0]) && !empty(static::$segments[0]))
		{
			$class = static::$segments[0];

			$pieces = explode("_", $class);

			$pieces = array_map(function($val) { return ucfirst($val); }, $pieces);

			return implode($pieces);
		}

		return ucwords(strtolower(Config::get('Controller', 'default_controller')));
	}


	//---------------------------------------------------------------------------------------------
	

	public static function getMethod()
	{
		//If we have a first URI segment and it's not an integer, use it as our method name
		if(isset(static::$segments[1]) && !empty(static::$segments[1]) && !is_int_val(static::$segments[1]))
		{
			$method = static::$segments[1];

			$pieces = explode("_", $method);

			$pieces = array_map(function($val) { return ucfirst($val); }, $pieces);

			return lcfirst(implode($pieces));
		}

		//Otherwise, return the default method name
		return strtolower(Config::get('Controller', 'default_method'));
	}


	//---------------------------------------------------------------------------------------------
	

	public static function getParams()
	{
		//If our first URI segment exists and is an integer, include it here
		if(isset(static::$segments[1]) && is_int_val(static::$segments[1])) return array_slice(static::$segments, 1);

		//Otherwise exclude the first segment as it was used as the method
		return array_slice(static::$segments, 2);
	}

}

/* End of file URI.php */
/* Location: ./Halligan/URI.php */