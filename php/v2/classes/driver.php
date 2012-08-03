<?php

/*

Copyright (c) 2012, Kimo Rosenbaum and contributors
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the owner nor the names of its contributors
      may be used to endorse or promote products derived from this
      software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

/**
 * APIProducerV2Driver
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2Driver
 */

include_once __DIR__ . '/validators.php';

class APIProducerV2Driver extends APIProducerV2Validators {

	protected $config = array();
	protected $count = 0;
	protected $error = '';
	protected $parameters = array(
		'numResults' => 0,
		'sortDir' => 'asc',
		'sortField' => NULL,
		'startIndex' => 0,
	);
	protected $slave_okay = false;

	public function __construct($slave_okay = false, $config = array()) {
		$this->config = $config;
		$this->slave_okay = $slave_okay;
	}

	public function __deconstruct() {
	}

	/**
	 * Build a query (array(key => array(eq => array() ...
	 * @param array $input
	 * @param array $fields
	 * @return array
	 */
	public function buildQuery($input, $fields) {
		$types = array('ge', 'gt', 'le', 'lt');
		$output = array();

		while(list($junk, $field) = each($fields)) {
			$output[$field] = array();

			if(array_key_exists($field, $input)) {
				if(is_array($input[$field])) {
					$output[$field]['eq'] = $input[$field];
				} else {
					$output[$field]['eq'] = array(
						$input[$field],
					);
				}
			}

			if(array_key_exists($field . '_re', $input)) {
				$output[$field]['re'] =
					(array) $input[$field . '_re'];
			}

			foreach($types as $type) {
				$key = $field . '_' . $type;

				if(array_key_exists($key, $input)) {
					if(is_array($input[$key])) {
						// Having multiple "less than"s
						// doesn't make sense.
						$output[$field][$type] =
							$input[$key][0];
					} else {
						$output[$field][$type] =
							$input[$key];
					}
				}
			}

			if(empty($output[$field])) {
				unset($output[$field]);
			}
		}

		return $output;
	}

	/**
	 * Get a config value
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	protected function getConfig($key = '', $default = '') {
		if(array_key_exists($key, $this->config)) {
			return $this->config[$key];
		}

		return $default;
	}

	/**
	 * Return the total number of records from a query
	 * @return int
	 */
	public function getCount() {
		return (int) $this->count;
	}

	/**
	 * Return error (if any) from most recent query
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Get a parameter
	 * @param string $parameter
	 * @return mixed the value of the parameter or NULL
	 */
	public function getParameter($parameter) {
		if(array_key_exists($parameter, $this->parameters)) {
			return $this->parameters[$parameter];
		}

		return NULL;
	}

	/**
	 * Get all parameters
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * Sanitize numResults
	 * @param mixed $value
	 * @return int
	 */
	protected function sanitizeParameter_numResults($value) {
		return $this->sanitizeInput_int($value);
	}

	/**
	 * Sanitize sortDir
	 * @param string $value
	 * @return string
	 */
	protected function sanitizeParameter_sortDir($value) {
		$substr = strtolower(substr($value, 0, 3));

		if($substr === 'des') {
			return 'desc';
		}

		return 'asc';
	}

	/**
	 * Sanitize startIndex
	 * @param mixed $value
	 * @return int
	 */
	protected function sanitizeParameter_startIndex($value) {
		return $this->sanitizeInput_int($value);
	}

	/**
	 * Set the parameters
	 * @param array $parameters
	 * @return array list of errors if any
	 */
	public function setParameters($parameters = array()) {
		$errors = array();

		$new = array_intersect_key($parameters, $this->parameters);
		$merged = array_merge($this->parameters, $new);

		while(list($key, $value) = each($merged)) {
			$function = 'validateParameter_' . $key;
			if(method_exists($this, $function)) {
				if($this->$function($value) !== true) {
					$errors[] = 'Invalid ' . $key;
					continue;
				}
			}

			$function = 'sanitizeParameter_' . $key;
			if(method_exists($this, $function)) {
				$value = $this->$function($value);
			}

			$this->parameters[$key] = $value;
		}
		reset($merged);

		return $errors;
	}

	/**
	 * Validate numResults
	 * @param string $value
	 * @return bool
	 */
	protected function validateParameter_numResults($value) {
		return $this->validateInput_digit($value);
	}

	/**
	 * Validate sortDir
	 * @param string $value
	 * @return bool
	 */
	protected function validateParameter_sortDir($value) {
		if($this->validateInput_scalar($value)) {
			$substr = strtolower(substr($value, 0, 3));

			switch($substr) {
				case 'asc';
				case 'des':
					return true;
			}
		}

		return false;
	}

	/**
	 * Validate sortField
	 * @param string $value
	 * @return bool
	 */
	protected function validateParameter_sortField($value) {
		if(is_null($value)) {
			return true;
		}

		if($value === '') {
			return true;
		}

		if($this->validateInput_scalar($value)) {
			if(is_string($value)) {
				if(preg_match('/^[A-Za-z0-9:._-]+$/', $value)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Validate startIndex
	 * @param mixed $value
	 * @return bool
	 */
	protected function validateParameter_startIndex($value) {
		return $this->validateInput_digit($value);
	}
}

?>
