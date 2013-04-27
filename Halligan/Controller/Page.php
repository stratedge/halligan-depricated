<?php

namespace Halligan\Controller;

use \Response;
use \Layout;
use \Config;

class Page extends \Halligan\Controller {

	protected $_components = array();
	protected $_global = array();
	protected $_layout;


	//---------------------------------------------------------------------------------------------
	

	public function __construct()
	{
		parent::__construct();
	}


	//---------------------------------------------------------------------------------------------
	

	public function addComponent($class, $section, Array $options = array())
	{
		$data = array_merge($options, array('class' => $class, 'section' => $section));
		return ($this->_components[] = (object) $data);
	}


	//---------------------------------------------------------------------------------------------


	public function build()
	{
		//Instantiate our layout file
		$layout = new Layout($this->_layout);

		//Add global data to the layout
		$layout->addGlobal($this->_global);

		Config::loadConfig('component');

		foreach($this->_components as $component)
		{
			if(!isset($component->class) || empty($component->class)) continue;
			if(!isset($component->section) || empty($component->section)) continue;

			$method = isset($component->options['method']) && !empty($component->options['method']) ? $component->options['method'] : Config::get('component', 'default_method');

			$params = (!isset($component->options['params']) || empty($component->options['params']) || !is_array($component->options['params'])) ? array() : $component->options['params'];

			$class = new $component->class();

			if(!method_exists($class, $method)) continue;

			$layout->addContentToSection(call_user_func(array($class, $method), $params), $component->section);
		}

		Response::setOutput($layout->build());
	}
}

/* End of file Page.php */
/* Location: ./Halligan/Controller/Page.php */