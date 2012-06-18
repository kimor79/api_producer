<?php

require_once __DIR__ . '/driver.php';

class APIProducerV2DriverTest extends PHPUnit_Framework_TestCase {

	protected $api;

	protected function setUP() {
		$this->api = new TestAPIProducerV2Driver();
	}

	public function testBuildQuery() {
		$data = array(
			'a' => '1',
			'b' => '3',
			'b_re' => 'foobar',
			'c_le' => 5,
		);
		$expected = array(
			'a' => array(
				'eq' => array('1'),
			),
			'b' => array(
				'eq' => array('3'),
				're' => array('foobar'),
			),
			'c' => array(
				'le' => 5,
			),
		);
		$fields = array('a', 'b', 'c', 'd');


		$got = $this->api->buildQuery($data, $fields);
		$this->assertEquals($expected, $got);
	}

	public function testValidateParameter_sortField() {
		$got = $this->api->validateParameter_sortField('foobar');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField1() {
		$got = $this->api->validateParameter_sortField('foobar1');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField2() {
		$got = $this->api->validateParameter_sortField('foo-bar');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField3() {
		$got = $this->api->validateParameter_sortField('foo_bar');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField4() {
		$got = $this->api->validateParameter_sortField('foo-bar');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField5() {
		$got = $this->api->validateParameter_sortField('foo.bar');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField6() {
		$got = $this->api->validateParameter_sortField('foo:bar');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField7() {
		$got = $this->api->validateParameter_sortField('foo:bar:blaz');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField8() {
		$got = $this->api->validateParameter_sortField('foo bar');
		$this->assertFalse($got);
	}

	public function testValidateParameter_sortField9() {
		$got = $this->api->validateParameter_sortField('');
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField10() {
		$got = $this->api->validateParameter_sortField(NULL);
		$this->assertTrue($got);
	}

	public function testValidateParameter_sortField11() {
		$got = $this->api->validateParameter_sortField(false);
		$this->assertFalse($got);
	}

	public function testValidateParameter_sortField12() {
		$got = $this->api->validateParameter_sortField(true);
		$this->assertFalse($got);
	}
}

?>
