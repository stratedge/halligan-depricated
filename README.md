# Halligan


## Description

Halligan is a free-to-use platform that seeks to create a componentized library system and versioned-extendability.


## To-Do List


### Classes


#### Autoloader Class

*	Vendor (3rd party) support


#### CLI Class

*	Allow calls to be made to the application through the command line interface (CLI)


#### Config Class

*	See if using output buffering when including config files and then cleaning it helps reduce memory usage


#### Email Class

*	Simple class to handle sending of emails
  *	Setting headers
  *	Setting recipients
  *	Setting sender
  *	Setting the subject
  *	Setting the body
  *	Sending the email


#### Input Class

*	Retrieve data from $_GET, $_POST
*	Retrieve uploaded file information


#### Query Class

*	Make the Database/Query classes usable beyond just MySQL
*	Add joins to the query class
*	Add where-grouping capability to the query class
*	Add limiting of query results
*	~~Add insert functionality to the query class~~


#### Session Class

*	Add some simple session handling


#### Template Class

*	~~Ensure that all objects passed as data to a template are converted to arrays to allow for dot notation for objects, too (requires recursively going through all array keys and object properties, yeouch)~~
*	~~Allow variables that are keys in arrays to be referencable using dot notation (var.key = $var['key'])~~


#### TBD

*	Maybe add a memcache class?


### Miscellaneous

*	Add support for environements, including custom config items for different environments
*	~~Adjust the capitalization on files and folders - all lowercase really won't do for multi-word file names~~