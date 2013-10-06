<?php

namespace Halligan;

class Input {

	public static function post($key, $trim = TRUE)
	{
		if(isset($_POST[$key]) && (!empty($_POST[$key]) || $_POST[$key] === "0"))
		{
			return $trim ? trim($_POST[$key]) : $_POST[$key];
		}

		return NULL;
	}


	//---------------------------------------------------------------------------------------------


	public static function allPost($trim = TRUE)
	{
		$post = array();

		foreach($_POST as $key => $value)
		{
			$post[$key] = static::post($key, $trim);
		}

		return $post;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function get($key, $trim = TRUE)
	{
		if(isset($_GET[$key]) && (!empty($_GET[$key]) || $_GET[$key] === "0"))
		{
			return $trim ? trim($_GET[$key]) : $_GET[$key];
		}

		return NULL;
	}


	//---------------------------------------------------------------------------------------------


	public static function allGet($trim = TRUE)
	{
		$get = array();

		foreach($_GET as $key => $value)
		{
			$get[$key] = static::get($key, $trim);
		}

		return $get;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function request($key, $trim = TRUE)
	{
		if(isset($_REQUEST[$key]) && !empty($_REQUEST[$key]))
		{
			return $trim ? trim($_REQUEST[$key]) : $_REQUEST[$key];
		}

		return NULL;
	}


	//---------------------------------------------------------------------------------------------


	public static function allRequest($trim = TRUE)
	{
		$request = array();

		foreach($_REQUEST as $key => $value)
		{
			$request[$key] = static::request($key, $trim);
		}

		return $request;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function file($key)
	{
		if(isset($_FILES[$key]) && !empty($_FILES[$key]))
		{
			return $_FILES[$key];
		}

		return NULL;
	}
}

/* End of file Input.php */
/* Location: ./Halligan/Input.php */