<?php

namespace Halligan;

use ReflectionMethod;

class Response {

	protected static $_output;
	protected static $_headers = array();


	public static function setOutput($output)
	{
		static::$_output = $output;
	}


	//---------------------------------------------------------------------------------------------


	public static function route()
	{
		URI::parseSegments();
		
		//Get the requested controller
		$class = URI::getController();

		//Make sure that the requested controller isn't off-limits
		if(in_array($class, Config::get('Controller', 'private_controllers', array())))
		{
			return static::show404();
		}

		/**
		 * Grab the controller class
		 * Circumvent regular autoloading so we can deal with the class not existing
		 */
		$c = Autoloader::loadController($class);

		//Make sure we found a controller
		if($c == FALSE)
		{
			return static::show404();
		}

		$m = URI::getMethod();

		$p = URI::getParams();
		
		//Gets filled in if we're using a map method
		$map = Config::get('Controller', 'map_method', 'map');
		
		if(method_exists($c, $map))
		{
			$r = new ReflectionMethod($c, $map);
			if($r->isPublic())
			{
				$p = array($m, $p);
				$m = $map;
			}
		}
		else
		{
			if(method_exists($c, $m))
			{
				$r = new ReflectionMethod($c, $m);
				if(!$r->isPublic())
				{
					return static::show404();
				}
			}
			else
			{
				return static::show404();
			}
		}

		call_user_func_array(array($c, $m), $p);
	}


	//---------------------------------------------------------------------------------------------


	public static function send($exit = FALSE)
	{
		//Send headers
		self::sendHeaders();

		if($exit) exit(static::$_output);
		
		echo static::$_output;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function sendHeaders()
	{
		foreach(static::$_headers as $header)
		{
			header($header[0], $header[1]);
		}
	}


	//---------------------------------------------------------------------------------------------


	public static function show404()
	{
		$class = Config::get('Response', 'error_controller', 'ErrorController');

		$c = Autoloader::loadController($class);
		$m = 'index';

		static::setStatusHeader(404);
		$c->$m();
	}


	//---------------------------------------------------------------------------------------------


	public static function addHeader($header, $overwrite = TRUE)
	{
		static::$_headers[] = array($header, $overwrite);
	}


	//---------------------------------------------------------------------------------------------


	public static function setStatusHeader($code = 200, $text = NULL)
	{
		$codes = array(
			200	=> 'OK',
			201	=> 'Created',
			202	=> 'Accepted',
			203	=> 'Non-Authoritative Information',
			204	=> 'No Content',
			205	=> 'Reset Content',
			206	=> 'Partial Content',

			300	=> 'Multiple Choices',
			301	=> 'Moved Permanently',
			302	=> 'Found',
			303	=> 'See Other',
			304	=> 'Not Modified',
			305	=> 'Use Proxy',
			307	=> 'Temporary Redirect',

			400	=> 'Bad Request',
			401	=> 'Unauthorized',
			403	=> 'Forbidden',
			404	=> 'Not Found',
			405	=> 'Method Not Allowed',
			406	=> 'Not Acceptable',
			407	=> 'Proxy Authentication Required',
			408	=> 'Request Timeout',
			409	=> 'Conflict',
			410	=> 'Gone',
			411	=> 'Length Required',
			412	=> 'Precondition Failed',
			413	=> 'Request Entity Too Large',
			414	=> 'Request-URI Too Long',
			415	=> 'Unsupported Media Type',
			416	=> 'Requested Range Not Satisfiable',
			417	=> 'Expectation Failed',
			422	=> 'Unprocessable Entity',

			500	=> 'Internal Server Error',
			501	=> 'Not Implemented',
			502	=> 'Bad Gateway',
			503	=> 'Service Unavailable',
			504	=> 'Gateway Timeout',
			505	=> 'HTTP Version Not Supported'
		);

		if(!$text) $text = $codes[$code];

		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP:/1.1';

		$header = sprintf("%s %d %s", $protocol, $code, $text);

		static::addHeader($header, TRUE, $code);
	}


	//---------------------------------------------------------------------------------------------
	

	public static function setContentType($type)
	{
		static::addHeader("Content-type: " . $type);
	}

}

/* End of file Response.php */
/* Location: ./Halligan/Response.php */