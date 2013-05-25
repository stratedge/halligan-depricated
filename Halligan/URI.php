<?php

namespace Halligan;

class URI {

	public static $segments = array();


	//---------------------------------------------------------------------------------------------
	

	public static function parseSegments()
	{
		$uri = $_SERVER['REQUEST_URI'];
		if(strpos($uri, '/') === 0) $uri = substr($uri, 1);
		static::$segments = explode('/', $uri);
	}


	//---------------------------------------------------------------------------------------------
	

	public static function getController()
	{
		if(isset(static::$segments[0]) && !empty(static::$segments[0])) return ucwords(strtolower(static::$segments[0]));

		Config::loadConfig('controller');

		return ucwords(strtolower(Config::get('Controller', 'default_controller')));
	}


	//---------------------------------------------------------------------------------------------
	

	public static function getMethod()
	{
		if(isset(static::$segments[1]) && !empty(static::$segments[1])) return strtolower(static::$segments[1]);

		Config::loadConfig('controller');

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