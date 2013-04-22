<?php

namespace Web\Controller;

class Home extends \Halligan\Controller\Page {

	public function __construct()
	{
		parent::__construct();
	}


	public function index()
	{
		$this->addComponent('Welcome', 'main');
		$this->build();
	}

}

/* End of file Home.php */
/* Location: ./Web/Controller/Home.php */