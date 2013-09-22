<?php

namespace Halligan;

class Component {

	protected $_global = array();

	public function __construct(Array $global = array())
	{
		$this->_global = $global;
	}

	public function getGlobal($key = NULL)
	{
		//No key or invalid key? 
		if(empty($key) || is_object($key) || is_assoc($key) || is_bool($key)) return $this->_global;

		if(is_array($key))
		{
			$tmp = array();
			
			foreach($key as $value)
			{
				$tmp[$value] = $this->getGlobal($value);
			}

			return $tmp; 
		}

		if(isset($this->_global[$key])) return $this->_global[$key];

		return NULL;
	}

}

/* End of file Component.php */
/* Location: ./Halligan/Component.php */