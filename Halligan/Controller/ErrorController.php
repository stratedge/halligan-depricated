<?php

namespace Halligan\Controller;

class ErrorController extends Page {

	public function __construct()
	{
		parent::__construct();
	}


	//---------------------------------------------------------------------------------------------


	public function index()
	{
		$this->addComponent('ErrorComponent', 'main');
		$this->build();
	}
}

/* End of file ErrorController.php */
/* Location: ./Halligan/Controller/ErrorController.php */