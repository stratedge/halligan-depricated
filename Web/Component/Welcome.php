<?php

namespace Web\Component;

use \Template;

class Welcome extends \Halligan\Component {

	public function __construct()
	{
		parent::__construct();
	}


	//---------------------------------------------------------------------------------------------
	

	public function index()
	{
		$tpl = new Template('welcome/index');
		return $tpl->build();
	}

}

/* End of file Welcome.php */
/* Location: ./Web/Component/Welcome.php */