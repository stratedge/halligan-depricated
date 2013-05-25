<?php

/**
 * Begin benchmark timer
 */
define('HALLIGAN_START', microtime(TRUE));


/**
 * Include the paths configuration before we change working directories
 */
require '../Config/Paths.php';


/**
 * Change the current working directory
 */
chdir('../..');


/**
 * Load the Halligan bootstrap
 */
require 'Halligan/Halligan.php';


/* End of file index.php */
/* Location: ./web/public/index.php */