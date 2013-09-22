<?php

namespace Web\Model;

class User extends Model {

	function __construct()
	{
		parent::__construct();
	}

	function getUsersByName($name)
	{
		$where = array(
			'first_name' => $name,
			'active' => 1
		);

		$result = $this->query()->select('user_id')->where($where)->get('user');

		return $result->getAllColumn();
	}
}