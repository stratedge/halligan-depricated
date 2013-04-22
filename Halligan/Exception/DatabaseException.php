<?php

namespace Halligan\Exception;

use Exception;

class DatabaseException extends Exception {

	function __construct(Array $error)
	{
		return parent::__construct($error[2], $error[1]);
	}
}

/* End of file DatabaseException.php */
/* Location: ./Halligan/Exception/DatabaseException.php */