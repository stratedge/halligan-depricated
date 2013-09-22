<?php

namespace Halligan\Tests\Config;

require_once realpath(__DIR__ . "/../config.php");

class GetTest extends HalliganTestCase {


	protected function setUp()
	{
		Config::$configs_loaded = array('Test');

		Config::$config_options = array(
			'test' => array(
				'one' => array(
					'three' => 3
				),
				'two' => 2
			)
		);
	}


	//---------------------------------------------------------------------------------------------
	

	public function testValueReturnedWhenFound()
	{
		$this->assertEquals(Config::get('Test', 'two'), 2);
	}


	//---------------------------------------------------------------------------------------------
	

	public function testDefaultReturnedWhenNotFound()
	{
		$this->assertEquals(Config::get('Test', 'three', 'Default'), 'Default');
	}


	//---------------------------------------------------------------------------------------------
	

	public function testDotNotationReturnsWhenFound()
	{
		$this->assertEquals(Config::get('Test', 'one.three'), 3);
	}


	//---------------------------------------------------------------------------------------------
	

	public function testDotNotationReturnsDefaultWhenNotFound()
	{
		$this->assertEquals(Config::get('Test', 'one.four', 'Default'), 'Default');
	}


	//---------------------------------------------------------------------------------------------
	

	public function testNullReturnedWhenNotFoundNoDefault()
	{
		$this->assertNull(Config::get('Test', 'one.four'));
	}

}

/* End of file GetTest.php */
/* Location: ./Halligan/Tests/Config/GetTest.php */