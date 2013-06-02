<?php

namespace Halligan;

class URI {

	public static $segments = array();


	//---------------------------------------------------------------------------------------------
	

	public static function parseSegments()
	{
		$uri = $_SERVER['REQUEST_URI'];

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
		if(isset(static::$segments[1]) && !empty(static::$segments[1]))
		{
			$method = static::$segments[1];

			$pieces = explode("_", $method);

			$pieces = array_map(function($val) { return ucfirst($val); }, $pieces);

			return lcfirst(implode($pieces));
		}

		return strtolower(Config::get('Controller', 'default_method'));
	}


	//---------------------------------------------------------------------------------------------
	

	public static function getParams()
	{
		return array_slice(static::$segments, 2);
	}

}

/* End of file URI.php */
/* Location: ./Halligan/URI.php */