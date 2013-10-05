# Halligan


[![Build Status](https://travis-ci.org/xjstratedgebx/halligan.png?branch=master)](https://travis-ci.org/xjstratedgebx/halligan)


## Description

Halligan is a free-to-use platform that seeks to create a componentized library system and versioned-extendability.


## To-Do List


### Classes


#### Autoloader Class

*	~~Vendor (3rd party) support~~


#### CLI Class

*	~~Allow calls to be made to the application through the command line interface (CLI)~~


#### Config Class

*	~~See if using output buffering when including config files and then cleaning it helps reduce memory usage~~


#### Email Class

*	Simple class to handle sending of emails
  *	~~Setting headers~~
  *	~~Setting one or more recipients~~
    *	~~To~~
    *	~~CC~~
    *	~~BCC~~
  *	~~Setting sender~~
  *	~~Setting the subject~~
  *	~~Setting the body~~
  *	~~Sending the email~~
  * ~~Multi-part emails (html section and plain text section)~~
  * ~~Optionally use the Template class to build message text~~
  * ~~Change the character set~~


#### Input Class

*	~~Retrieve single item from $_GET~~
*	~~Retrieve all items from $_GET~~
*	~~Retrive single item from $_POST~~
*	~~Retrieve all items from $_POST~~
*	Retrieve uploaded file information


#### Query Class

*	Make the Database/Query classes usable beyond just MySQL
*	Add joins to the query class
*	Add where-grouping capability to the query class
*	~~Add limiting of query results~~
*	~~Add insert functionality to the query class~~


#### Response Class

*	~~Deal with a url that requests a controller that does not actually exist~~
*	~~Add the ability to declare a particular controller off-limits from direct access~~


#### Session Class

*	Add some simple session handling


#### Template Class

*	~~Ensure that all objects passed as data to a template are converted to arrays to allow for dot notation for objects, too (requires recursively going through all array keys and object properties, yeouch)~~
*	~~Allow variables that are keys in arrays to be referencable using dot notation (var.key = $var['key'])~~
*	~~Fix a bug in the addData method where non-arrays are being passed to a foreach as part of the recursive de-objectifying script~~
*	~~Fix a bug in the if template tag parsing where $this is being used out of context~~
*	~~Add a new template tag to accept a condition and a value to print if true (and maybe a value to print if false)~~
*	~~Create a template tag that is "echo" and behaves the same as "var"~~


#### TBD

*	Maybe add a memcache class?


### Miscellaneous

*	Add support for environements, including custom config items for different environments
*	~~Adjust the capitalization on files and folders - all lowercase really won't do for multi-word file names~~