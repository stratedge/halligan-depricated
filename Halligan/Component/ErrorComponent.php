<?php

namespace Halligan\Component;

class ErrorComponent extends Component {

	public function __construct()
	{
		parent::__construct();
	}


	//---------------------------------------------------------------------------------------------


	public function index()
	{
		$tpl = new Template(Config::get('Response', 'error_template', '404'));
		return $tpl->build();
	}

}

/* End of file ErrorComponent.php */
/* Location: ./Halligan/Component/ErrorComponent.php */