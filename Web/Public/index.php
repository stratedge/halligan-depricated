<?php

/**
 * Begin benchmark timer
 */
define('HALLIGAN_START', microtime(TRUE));


/**
 * Include the paths configuration before we change working directories
 */
require realpath(__DIR__ . '/../Config/Paths.php');


/**
 * Change the current working directory
 */
chdir(realpath(__DIR__ . '/../..'));


/**
 * Load the Halligan bootstrap
 */
require 'Halligan/Halligan.php';


/* End of file index.php */
/* Location: ./web/public/index.php */