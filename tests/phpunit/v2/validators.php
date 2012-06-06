<?php

require_once __DIR__ . '/../../../php/v2/classes/validators.php' ;

class TestAPIProducerV2Validators extends APIProducerV2Validators {

	public function __construct() {
		parent::__construct();
	}

	public function __deconstruct() {
		parent::__deconstruct();
	}

	public function sanitizeInput_bool_false() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function sanitizeInput_bool_true() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function sanitizeInput_fqdn() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function sanitizeInput_int() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function sanitizeInput_timestamp() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function sanitizeInput_tolower() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function validateInput_bool() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function validateInput_digit() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function validateInput_fqdn() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function validateInput_scalar() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}

	public function validateInput_timestamp() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}
}

?>
