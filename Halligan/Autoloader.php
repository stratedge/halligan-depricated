<?php

namespace Halligan;

class Autoloader {

	public static $paths = array();


	//---------------------------------------------------------------------------------------------
	

	public static function load($class)
	{
		if(strpos($class, "\\") === FALSE)
		{
			return static::_loadWithoutNamespace($class);
		}

		return static::_loadWithNamespace($class);
	}


	//---------------------------------------------------------------------------------------------


	protected static function _loadWithNamespace($class)
	{
		$parts = explode("\\", $class);
		
		$path = realpath(path('base') . strtolower(implode(DS, $parts)) . EXT);

		if($path !== FALSE)
		{
			require_once $path;
			return TRUE;
		}

		end($parts);

		return static::_loadWithoutNamespace(current($parts));

	}


	//---------------------------------------------------------------------------------------------


	protected static function _loadWithoutNamespace($class)
	{		
		//Go through all the system paths first
		foreach(get_all_paths_ordered() as $path)
		{
			$path = realpath($path . $class . ".php");
			if($path !== FALSE) break;
		}
		
		//If we found a matching file, load it, and alias the class
		if($path !== FALSE)
		{
			require_once $path;
			$namespace = resolve_namespace_class($path, $class);
			class_alias($namespace, $class);
			return TRUE;
		}

		//Didn't find it yet, now we need to go through all the manually set paths
		Config::loadConfig('autoloader');

		foreach(Config::get('autoloader', 'paths') as $config_path)
		{
			foreach(get_all_paths_ordered() as $path)
			{
				$path = realpath($path . $config_path . DS . $class. ".php");
				if($path !== FALSE) break;
			}

			if($path !== FALSE)
			{
				require_once $path;
				$namespace = resolve_namespace_class($path, $class);
				class_alias($namespace, $class);
				return TRUE;
			}
		}
	}


	//---------------------------------------------------------------------------------------------

	
	public static function registerPath($path)
	{
		if(!in_array($path, static::$paths)) static::$paths[] = $path;
	}


	//---------------------------------------------------------------------------------------------


	public static function registerPaths($paths)
	{
		foreach($paths as $path)
		{
			static::registerPath($path);
		}
	}

}

/* End of file Autoloader.php */
/* Location: ./Halligan/Autoloader.php */