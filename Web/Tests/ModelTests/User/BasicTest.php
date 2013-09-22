<?php


require_once realpath(__DIR__ . "/../../config.php");


class BasicTest extends HalliganTestCase {

	public function testIsModel()
	{
		$u = new User();

		$this->assertTrue(is_a($u, 'Model'));
	}

}

/* End of file GetUsersByNameTest.php */
/* Location: ./Web/Tests/ModelTests/User/GetUsersByNameTest.php */