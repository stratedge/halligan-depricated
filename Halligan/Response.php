<?php

namespace Halligan;

class Response {

	protected static $_output;
	protected static $_headers;


	public static function setOutput($output)
	{
		static::$_output = $output;
	}


	public static function route()
	{
		URI::parseSegments();

		$c = URI::getController();
		$c = new $c();

		$m = URI::getMethod();

		if(!method_exists($c, $m) || !is_callable(array($c, $m), TRUE))
		{
			dump('404');
			//Handle 404 error here
		}

		$p = URI::getParams();

		call_user_func(array($c, $m), $p);
	}


	public static function send()
	{
		echo static::$_output;
	}

}

/* End of file Response.php */
/* Location: ./Halligan/Response.php */