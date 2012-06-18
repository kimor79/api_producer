<?php

require_once __DIR__ . '/../../../php/v2/classes/driver.php' ;

class TestAPIProducerV2Driver extends APIProducerV2Driver {

	public function __construct() {
		parent::__construct();
	}

	public function __deconstruct() {
		parent::__deconstruct();
	}

	public function validateParameter_sortField() {
		return call_user_func_array(array('parent', __FUNCTION__),
			func_get_args());
	}
}

?>
