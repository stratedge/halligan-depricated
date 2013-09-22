<?php


require_once realpath(__DIR__ . "/../../config.php");


class GetUsersByNameTest extends HalliganTestCase {

	public function testGetUsersByNameReturnsList()
	{
		$result = array("test1", "test2");

		PDOStatement_Stub::addResult($result);

		$u = new User();
		$r = $u->getUsersByName("test");

		$this->assertInternalType('array', $result);
		$this->assertEquals($r, array('test1', 'test2'));
	}


	//---------------------------------------------------------------------------------------------


	public function testGetUsersByNameReturnsEmptyArray()
	{
		$result = array();

		PDOStatement_Stub::addResult($result);

		$u = new User();
		$r = $u->getUsersByName("test");

		$this->assertInternalType('array', $result);
		$this->assertEmpty($r);
	}


	//---------------------------------------------------------------------------------------------


	public function testGetUsersByNameChecksActiveFlag()
	{
		$u = new User();
		$r = $u->getUsersByName("test");

		$this->assertContains("AND `active` = '1'", PDOStatement_Stub::getLastSQL());
	}

}

/* End of file GetUsersByNameTest.php */
/* Location: ./Web/Tests/ModelTests/User/GetUsersByNameTest.php */