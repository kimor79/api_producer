<?php

require_once('./base.php');

class ApiProducerBaseTest extends PHPUnit_Framework_TestCase {

	protected $api;

	protected function setUP() {
		$this->api = new TestApiProducerBase();
	}

	public function testBuildQuery() {
		$this->markTestIncomplete();
	}

	public function testCastInput() {
		$this->markTestIncomplete();
	}

	public function testCastInput_array() {
		$got = $this->api->castInput_array('foobar');

		if(method_exists($this, 'assertInternalType')) {
			$this->assertInternalType('array', $got);
		}

		$this->assertArrayHasKey(0, $got);
		$this->assertArrayNotHasKey(1, $got);
	}

	public function testCastInput_array1() {
		$got = $this->api->castInput_array(32);

		if(method_exists($this, 'assertInternalType')) {
			$this->assertInternalType('array', $got);
		}

		$this->assertArrayHasKey(0, $got);
		$this->assertArrayNotHasKey(1, $got);
	}

	public function testCastInput_array2() {
		$got = $this->api->castInput_array(array('a', 'b'));

		if(method_exists($this, 'assertInternalType')) {
			$this->assertInternalType('array', $got);
		}

		$this->assertArrayHasKey(1, $got);
		$this->assertArrayNotHasKey(2, $got);
	}

	public function testCastInput_bin() {
		$this->markTestIncomplete();
	}

	public function testCastInput_binary() {
		$this->markTestIncomplete();
	}

	public function testCastInput_bool() {
		$got = $this->api->castInput_bool(true);
		$this->assertTrue($got);
	}

	public function testCastInput_bool1() {
		$got = $this->api->castInput_bool(false);
		$this->assertFalse($got);
	}

	public function testCastInput_boolean() {
		$got = $this->api->castInput_boolean(true);
		$this->assertTrue($got);
	}

	public function testCastInput_boolean1() {
		$got = $this->api->castInput_boolean(false);
		$this->assertFalse($got);
	}

	public function testCastInput_double() {
		$this->markTestIncomplete();
	}

	public function testCastInput_float() {
		$this->markTestIncomplete();
	}

	public function testCastInput_int() {
		$got = $this->api->castInput_int(1);
		$this->assertSame(1, $got);
	}

	public function testCastInput_int1() {
		$got = $this->api->castInput_int('1');
		$this->assertSame(1, $got);
	}

	public function testCastInput_int2() {
		$got = $this->api->castInput_int('0');
		$this->assertSame(0, $got);
	}

	public function testCastInput_int3() {
		$got = $this->api->castInput_int(0);
		$this->assertSame(0, $got);
	}

	public function testCastInput_integer() {
		$got = $this->api->castInput_integer(1);
		$this->assertSame(1, $got);
	}

	public function testCastInput_integer1() {
		$got = $this->api->castInput_integer('1');
		$this->assertSame(1, $got);
	}

	public function testCastInput_integer2() {
		$got = $this->api->castInput_integer('0');
		$this->assertSame(0, $got);
	}

	public function testCastInput_integer3() {
		$got = $this->api->castInput_integer(0);
		$this->assertSame(0, $got);
	}

	public function testCastInput_object() {
		$this->markTestIncomplete();
	}

	public function testCastInput_real() {
		$this->markTestIncomplete();
	}

	public function testCastInput_string() {
		$got = $this->api->castInput_string(1);
		$this->assertSame('1', $got);
	}

	public function testCastInput_string1() {
		$got = $this->api->castInput_string('1');
		$this->assertSame('1', $got);
	}

	public function testCastInput_string2() {
		$got = $this->api->castInput_string(0);
		$this->assertSame('0', $got);
	}

	public function testCastInput_string3() {
		$got = $this->api->castInput_string('0');
		$this->assertSame('0', $got);
	}

	public function testCastInput_null() {
		$this->markTestIncomplete();
	}

	public function testContentDisposition() {
		$this->markTestIncomplete();
	}

	public function testContentType() {
		$this->markTestIncomplete();
	}

	public function testFullHandleInput() {
		$input = array(
			'a' => 'a',
			'b' => '1',
			'c' => '1',
			'd' => '1',
		);

		$cast = array(
			'a' => 'string',
			'b' => 'string',
			'c' => 'int',
			'd' => 'bool',
		);

		$sanitize = array(
			'd' => 'bool_true',
		);

		$output = $this->api->sanitizeInput($input, $sanitize);
		$output = $this->api->castInput($output, $cast);

		$this->assertSame('a',$output['a']);
		$this->assertSame('1', $output['b']);
		$this->assertSame(1, $output['c']);
		$this->assertTrue($output['d']);
	}

	public function testFullHandleInput1() {
		$input = array(
			'a' => 1,
			'b' => 1,
			'c' => 1,
			'd' => 1,
		);

		$cast = array(
			'a' => 'string',
			'b' => 'string',
			'c' => 'int',
			'd' => 'bool',
		);

		$sanitize = array(
			'd' => 'bool_true',
		);

		$output = $this->api->sanitizeInput($input, $sanitize);
		$output = $this->api->castInput($output, $cast);

		$this->assertSame('1',$output['a']);
		$this->assertSame('1', $output['b']);
		$this->assertSame(1, $output['c']);
		$this->assertTrue($output['d']);
	}

	public function testFullHandleInput2() {
		$input = array(
			'a' => 0,
			'b' => 0,
			'c' => 0,
			'd' => 0,
		);

		$cast = array(
			'a' => 'string',
			'b' => 'string',
			'c' => 'int',
			'd' => 'bool',
		);

		$sanitize = array(
			'd' => 'bool_true',
		);

		$output = $this->api->sanitizeInput($input, $sanitize);
		$output = $this->api->castInput($output, $cast);

		$this->assertSame('0',$output['a']);
		$this->assertSame('0', $output['b']);
		$this->assertSame(0, $output['c']);
		$this->assertFalse($output['d']);
	}

	public function testGetVariable() {
		$this->markTestIncomplete();
	}

	public function testGetParameter() {
		$this->markTestIncomplete();
	}

	public function testGpcSlash() {
		$this->markTestIncomplete();
	}

	public function testGpcSlashInput() {
		$this->markTestIncomplete();
	}

	public function testRemoveValues() {
		$this->markTestIncomplete();
	}

	public function testSanitizeInput() {
		$this->markTestIncomplete();
	}

	public function testSanitizeInput_bool_false() {
		$got = $this->api->sanitizeInput_bool_false(0);
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_false1() {
		$got = $this->api->sanitizeInput_bool_false('0');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_false2() {
		$got = $this->api->sanitizeInput_bool_false('');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_false3() {
		$got = $this->api->sanitizeInput_bool_false(1);
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_false4() {
		$got = $this->api->sanitizeInput_bool_false('1');
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_false5() {
		$got = $this->api->sanitizeInput_bool_false('foobar');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_true() {
		$got = $this->api->sanitizeInput_bool_true(0);
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_true1() {
		$got = $this->api->sanitizeInput_bool_true('0');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_true2() {
		$got = $this->api->sanitizeInput_bool_true('');
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_true3() {
		$got = $this->api->sanitizeInput_bool_true(1);
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_true4() {
		$got = $this->api->sanitizeInput_bool_true('1');
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_true5() {
		$got = $this->api->sanitizeInput_bool_true('foobar');
		$this->assertTrue($got);
	}

	public function testSanitizeInput_date() {
		$this->markTestIncomplete();
	}

	public function testSanitizeInput_fqdn() {
		$this->markTestIncomplete();
	}

	public function testSanitizeInput_gpcSlash() {
		$this->markTestIncomplete();
	}

	public function testSanitizeInput_mac_address() {
		$this->markTestIncomplete();
	}

	public function testSanitizeInput_tolower() {
		$this->markTestIncomplete();
	}

	public function testSanitizeInput_toupper() {
		$this->markTestIncomplete();
	}

	public function testSanitizeParameter_contentType() {
		$this->markTestIncomplete();
	}

	public function testSanitizeParameter_flatOutput() {
		$this->markTestIncomplete();
	}

	public function testSanitizeParameter_outputFormat() {
		$this->markTestIncomplete();
	}

	public function testSanitizeParameter_subDetails() {
		$this->markTestIncomplete();
	}

	public function testSendHeaders() {
		$this->markTestIncomplete();
	}

	public function testSetVariable() {
		$this->markTestIncomplete();
	}

	public function testSetInput() {
		$this->markTestIncomplete();
	}

	public function testSetParameters() {
		$this->markTestIncomplete();
	}

	public function testShowOutput_json() {
		$this->markTestIncomplete();
	}

	public function testShowOutput_print_r() {
		$this->markTestIncomplete();
	}

	public function testTrueFalse() {
		$this->markTestIncomplete();
	}

	public function testValidateInput() {
		$this->markTestIncomplete();
	}

	public function testValidateInput_bool() {
		$this->markTestIncomplete();
	}

	public function testValidateInput_date() {
		$this->markTestIncomplete();
	}

	public function testValidateInput_digit() {
		$this->markTestIncomplete();
	}

	public function testValidateInput_fqdn() {
		$this->markTestIncomplete();
	}

	public function testValidateInput_mac_address() {
		$this->markTestIncomplete();
	}
}

?>
