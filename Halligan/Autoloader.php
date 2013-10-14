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
		
		$path = realpath(path('base') . implode(DS, $parts) . EXT);

		if($path !== FALSE)
		{
			require_once $path;
			return TRUE;
		}

		//See if the class is namespaced in the Vendor folder
		foreach(get_all_paths_ordered() as $path)
		{
			$path = realpath($path . path('vendor') . DS . strtolower(implode(DS, $parts)) . EXT);
			if($path !== FALSE)
			{
				require_once $path;
				return TRUE;
			}
		}

		$ns = implode("\\", array_slice($parts, 0, count($parts) - 1));
		
		end($parts);
		
		return static::_loadWithoutNamespace(current($parts), $ns);

	}


	//---------------------------------------------------------------------------------------------


	protected static function _loadWithoutNamespace($class, $ns = FALSE)
	{
		//If we've already loaded this class here, we've forsaken namespacing, so if we find the
		//class has been loaded, just alias it to the requesting namespace
		if(class_exists($class)) return class_alias($class, $ns ? $ns . "\\" . $class : $class);

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
			$alias = $ns ? $ns . "\\" . $class : $class;
			if(!class_exists($alias)) class_alias($namespace, $alias);
			return TRUE;
		}

		//Didn't find it yet, now we need to go through all the manually set paths
		Config::loadConfig('Autoloader');

		foreach(Config::get('Autoloader', 'paths') as $config_path)
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


	//---------------------------------------------------------------------------------------------
	

	public static function loadController($class)
	{
		//Go backwards through our available paths to find the controller
		foreach(get_all_paths_ordered() as $path)
		{
			//Go through each path where a controller can be stored
			foreach(Config::get('Paths', 'controllers') as $c_path)
			{
				$loc = realpath($path . $c_path . DS . $class . EXT);
				if($loc !== FALSE)
				{
					require_once $loc;
					$class = resolve_namespace_class($loc, $class);
					return new $class();
				}
			}
		}

		return FALSE;
	}

}

/* End of file Autoloader.php */
/* Location: ./Halligan/Autoloader.php */