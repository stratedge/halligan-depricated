<?php

namespace Halligan;

class Component {

	protected $_global = array();

	public function __construct(Array $global = array())
	{
		$this->_global = $global;
	} 

}

/* End of file Component.php */
/* Location: ./Halligan/Component.php */