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
			$path = realpath($path . 'Config' . DS . $config . '.php');
			
			if($path !== FALSE)
			{
				include $path;
				
				$config_lower = strtolower($config);

				if(isset($$config_lower))
				{
					foreach($$config_lower as $key => $value)
					{
						if(!is_array($value) || !isset(static::$config_options[$config_lower][$key]))
						{
							static::$config_options[$config_lower][$key] = $value;
							continue;
						}

						static::$config_options[$config_lower][$key] = array_unique(array_merge(static::$config_options[$config_lower][$key], $value));
					}
				}
			}
		}
		
		static::$configs_loaded[] = $config;

		return TRUE;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function get($class, $property, $default = NULL)
	{
		if(!in_array($class, static::$configs_loaded)) static::loadConfig($class);
		
		if(!isset(static::$config_options[strtolower($class)])) return $default;

		return array_get(static::$config_options[strtolower($class)], $property, $default);
	}
}

/* End of file Config.php */
/* Location: ./Halligan/Config.php */