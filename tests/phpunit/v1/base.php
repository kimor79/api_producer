<?php

require_once('api_producer/v1/classes/base.php');

class TestApiProducerBase extends ApiProducerBase {

	public function __construct() {
		parent::__construct();
	}

	public function __deconstruct() {
		parent::__deconstruct();
	}

	public function buildQuery() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'buildQuery'),
			$args);
	}

	public function castInput() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput'),
			$args);
	}

	public function castInput_array() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_array'),
			$args);
	}

	public function castInput_bin() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_bin'),
			$args);
	}

	public function castInput_binary() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_binary'),
			$args);
	}

	public function castInput_bool() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_bool'),
			$args);
	}

	public function castInput_boolean() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'castInput_boolean'), $args);
	}

	public function castInput_double() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'castInput_double'), $args);
	}

	public function castInput_float() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_float'),
			$args);
	}

	public function castInput_int() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_int'),
			$args);
	}

	public function castInput_integer() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'castInput_integer'), $args);
	}

	public function castInput_object() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'castInput_object'), $args);
	}

	public function castInput_real() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_real'),
			$args);
	}

	public function castInput_string() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_string'),
			$args);
	}

	public function castInput_null() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'castInput_null'),
			$args);
	}

	public function contentDisposition() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'contentDisposition'), $args);
	}

	public function contentType() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'contentType'), $args);
	}

	public function diffArray() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'diffArray'), $args);
	}

	public function getVariable() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'getVariable'),
			$args);
	}

	public function getParameter() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'getParameter'),
			$args);
	}

	public function gpcSlash() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'gpcSlash'), $args);
	}

	public function gpcSlashInput() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'gpcSlashInput'),
			$args);
	}

	public function removeValues() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'removeValues'),
			$args);
	}

	public function sanitizeInput() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'sanitizeInput'),
			$args);
	}

	public function sanitizeInput_bool_false() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_bool_false'), $args);
	}

	public function sanitizeInput_bool_true() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_bool_true'), $args);
	}

	public function sanitizeInput_date() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_date'), $args);
	}

	public function sanitizeInput_fqdn() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_fqdn'), $args);
	}

	public function sanitizeInput_gpcSlash() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_gpcSlash'), $args);
	}

	public function sanitizeInput_mac_address() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_mac_address'), $args);
	}

	public function sanitizeInput_tolower() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_tolower'), $args);
	}

	public function sanitizeInput_toupper() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeInput_toupper'), $args);
	}

	public function sanitizeParameter_contentType() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeParameter_contentType'), $args);
	}

	public function sanitizeParameter_flatOutput() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeParameter_flatOutput'), $args);
	}

	public function sanitizeParameter_outputFormat() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeParameter_outputFormat'), $args);
	}

	public function sanitizeParameter_subDetails() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'sanitizeParameter_subDetails'), $args);
	}

	public function sendHeaders() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'sendHeaders'),
			$args);
	}

	public function setVariable() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'setVariable'),
			$args);
	}

	public function setInput() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'setInput'), $args);
	}

	public function setParameters() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'setParameters'),
			$args);
	}

	public function showOutput_json() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'showOutput_json'), $args);
	}

	public function showOutput_print_r() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'showOutput_print_r'), $args);
	}

	public function trueFalse() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'trueFalse'),
			$args);
	}

	public function validateInput() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'validateInput'), $args);
	}

	public function validateInput_bool() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'validateInput_bool'), $args);
	}

	public function validateInput_date() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'validateInput_date'), $args);
	}

	public function validateInput_digit() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'validateInput_digit'), $args);
	}

	public function validateInput_fqdn() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'validateInput_fqdn'), $args);
	}

	public function validateInput_mac_address() {
		$args = func_get_args();
		return call_user_func_array(array('parent',
			'validateInput_mac_address'), $args);
	}
}

?>
