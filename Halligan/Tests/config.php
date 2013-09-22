<?php

require realpath(__DIR__ . '/../Config/Paths.php');

chdir(__DIR__ . "/../..");


//error_reporting(E_ERROR | E_WARNING | E_PARSE);


/**
 * Define useful constants
 */
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('NL')) define('NL', "\n");
if(!defined('TAB')) define('TAB', "\t");
if(!defined('CRLF')) define('CRLF', "\r\n");
if(!defined('EXT')) define('EXT', ".php");


/**
 * Go through each path in the paths configuration file and add it to the global paths array
 */
foreach($paths['paths'] as $key => $path)
{
	if(!isset($GLOBALS['halligan_paths'][$key])) $GLOBALS['halligan_paths'][$key] = realpath($path) . DS;
}


/**
 * Add the base path to the global paths array
 */
$GLOBALS['halligan_paths']['base'] = getcwd() . DS;


/**
 * Instantiate the global runcard array
 */
$GLOBALS['halligan_paths']['runcard'] = array();


/**
 * Add all the runcard paths to the global runcard array
 */
foreach($paths['runcard'] as $path)
{
	$GLOBALS['halligan_paths']['runcard'][] = realpath($path) . DS;
}


/**
 * Add the vendor folder to the global paths array
 */
$GLOBALS['halligan_paths']['vendor'] = $paths['vendor'];


unset($paths);


/**
 * Create a function to get the requested path
 * 
 * @param	string	$key	The key of the $paths array corresponding to the value being requested
 * @return	string			The full path to the directory
 */
function path($path)
{
	return $GLOBALS['halligan_paths'][$path];
}


/**
 * Create a function that sets the path to the specified key in the global paths array
 * 
 * @param	string	$key	The key to save the path to in the global paths array
 * @param	string	$path	The path to save to the specified key in the global paths array
 * @return	boolean			Whether or not the path was set succesfully to the global paths array
 */
function set_path($key, $path)
{
	return ($GLOBALS['halligan_paths'][$key] = $path);
}


/**
 * Create a function to return the saved paths in order, optionally backwards
 * 
 * @param	boolean	$backwards	Whether or not to move through the paths backwards
 * @return	array				An array of paths, sorted ascending or descending as specified
 */
function get_all_paths_ordered($backwards = FALSE)
{
	$paths = array_merge((array) path('app'), path('runcard'), (array) path('sys'));
	return $backwards ? array_reverse($paths) : $paths;
}


/**
 * Goes through each registered path (app, runcard, and system) and loads the requested file if
 * found in any and all those locations. Optionally can progress forwards or backwards through the
 * list of registered paths.
 * 
 * @param	string	$file		The folder(s) and filename of the file to be loaded
 * @param	boolean	$backwards	FALSE (default) to begin with the app path, TRUE to begin with the system path
 * @return	mixed				FALSE if no file is ever loaded, otherwise the path to the last loaded file
 */
function load_file_from_all_paths($file, $backwards = FALSE)
{
	$last_path = FALSE;

	foreach(get_all_paths_ordered($backwards) as $path)
	{
		$path = realpath($path . $file);
		if($path !== FALSE)
		{
			require_once $path;
			$last_path = $path;
		}
	}

	return $last_path;
}


/**
 * Load the utility file from all registered paths where one is found
 */
load_file_from_all_paths('Utilities.php');


/**
 * Load all the config class(es)
 * We load them all backwards incase any one extends another
 */
$last_path = load_file_from_all_paths('Config.php', TRUE);


/**
 * Use the latest loaded config as the default config
 */
$config = resolve_namespace_class($last_path, 'Config');
class_alias($config, 'Config');

unset($last_path, $config);


/**
 * Load all the autoloader class(es)
 * We load them all backwards incase any one extends another
 */
$last_path = load_file_from_all_paths('Autoloader.php', TRUE);


/**
 * Use the latest loaded autoloader as the default autoloader
 */
$autoloader = resolve_namespace_class($last_path, 'Autoloader');
spl_autoload_register(array($autoloader, 'load'));

unset($last_path, $autoloader);

// require __DIR__ . "/PDO.php";
// require __DIR__ . "/PDOStatement.php";
// require __DIR__ . "/Database.php";
require __DIR__ . "/HalliganTestCase.php";