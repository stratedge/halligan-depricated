<?php

namespace Halligan;

class Config {


	public static $configs_loaded = array();
	public static $config_options = array();

	
	//---------------------------------------------------------------------------------------------
	

	public static function loadConfig($config)
	{
		if(in_array($config, static::$configs_loaded)) return TRUE;

		static::$config_options[$config] = array();

		foreach(get_all_paths_ordered(TRUE) as $path)
		{
			$path = realpath($path . 'config' . DS . $config . '.php');
			
			if($path !== FALSE)
			{
				include_once $path;

				foreach($$config as $key => $value)
				{
					if(!is_array($value) || !isset(static::$config_options[$config][$key]))
					{
						static::$config_options[$config][$key] = $value;
						continue;
					}

					static::$config_options[$config][$key] = array_unique(array_merge(static::$config_options[$config][$key], $value));
				}
			}
		}

		static::$configs_loaded[] = $config;

		unset($$config, $config);
		
		return TRUE;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function get($class, $property, $default = FALSE)
	{
		if(!in_array($class, static::$configs_loaded)) static::loadConfig($class);
		
		if(!isset(static::$config_options[$class])) return $default;

		return array_get(static::$config_options[$class], $property, $default);
	}
}

/* End of file Config.php */
/* Location: ./Halligan/Config.php */