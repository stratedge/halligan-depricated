<?php


require_once realpath(__DIR__ . "/../config.php");


class GetGlobalTest extends HalliganTestCase {

	public function __construct()
	{
		$this->data = array('hello' => 1, 'world' => 2);
		$this->c = new Component($this->data);
	}


	//---------------------------------------------------------------------------------------------
	

	public function testNoKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal());
	}


	//---------------------------------------------------------------------------------------------
	

	public function testEmptyKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal(''));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testNullKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal(NULL));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testFalseKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal(FALSE));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testTrueKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal(TRUE));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testEmptyArrayKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal(array()));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testObjectKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal(new \stdClass()));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testAssocArrayKeyReturnsAllGlobals()
	{
		$this->assertEquals($this->data, $this->c->getGlobal(array('test' => 'ing')));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testUnknownKeyReturnsNull()
	{
		$this->assertNull($this->c->getGlobal('test'));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testKnownKeyReturnsValue()
	{
		$this->assertEquals(2, $this->c->getGlobal('world'));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testKnownKeysReturnValues()
	{
		$this->assertEquals(array('world' => 2, 'hello' => 1), $this->c->getGlobal(array('world', 'hello')));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testMixedKnownUnknownKeysReturnsKnownValues()
	{
		$this->assertEquals(array('test' => NULL, 'world' => 2), $this->c->getGlobal(array('test', 'world')));
	}

}

/* End of file GetGlobalTest.php */
/* Location: ./Halligan/Tests/Component/GetGlobalTest.php */