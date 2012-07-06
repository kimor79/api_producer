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
 * APIProducerV2Output
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2Output
 */

include_once __DIR__ . '/validators.php';

class APIProducerV2Output extends APIProducerV2Validators {

	protected $output_formats = array(
		'json',
		'print_r',
	);

	protected $parameters = array(
		'flatOutput' => false,
		'outputFormat' => 'json',
		'statusHeader' => false,
		'subDetails' => false,
	);

	public function __construct() {
	}

	public function __deconstruct() {
	}

	/**
	 * Flatten array
	 * @param array $input
	 * @param string $prefix
	 * @param string $delim
	 * @return array
	 */
	public function _flattenData($input, $prefix, $delim) {
		$output = array();
		while(list($key, $value) = each($input)) {
			$nkey = sprintf("%s%s%s", $prefix, $delim, $key);

			if(!is_array($value)) {
				$output[$nkey] = $value;
				continue;
			}

			$output = array_merge($output, $this->_flattenData(
				$value, $nkey, $delim));
		}

		return $output;
	}

	/**
	 * Flatten an array
	 * foo => bar => array(a => b)
	 * becomes foo:bar:a => b
	 * @param array $input
	 * @param string $delim default is :
	 * @return mixed
	 */
	public function flattenData($input, $delim = ':') {
		$output = array();

		while(list($key, $value) = each($input)) {
			if(!is_array($value)) {
				$output[$key] = $value;
				continue;
			}

			$output = array_merge($output, $this->_flattenData(
				$value, $key, $delim));
		}

		return $output;
	}

	/**
	 * Format output as JSON
	 * @param array $data
	 * @return string
	 */
	protected function formatData_json($data) {
		return json_encode($data);
	}

	/**
	 * Format output as print_r
	 * @param array $data
	 * @return string
	 */
	protected function formatData_print_r($data) {
		$output = '<pre>';
		$output .= print_r($data, true);
		$output .= '</pre>';

		return $output;
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
	 * Sanitize flatOutput
	 * @param mixed $value
	 * @return bool
	 */
	protected function sanitizeParameter_flatOutput($value) {
		return $this->sanitizeInput_bool_false($value);
	}

	/**
	 * Sanitize outputFormat
	 * @param string $value
	 * @return string
	 */
	protected function sanitizeParameter_outputFormat($value) {
		return $this->sanitizeInput_tolower($value);
	}

	/**
	 * Sanitize statusHeader
	 * @param mixed $value
	 * @return bool
	 */
	protected function sanitizeParameter_statusHeader($value) {
		return $this->sanitizeInput_bool_false($value);
	}

	/**
	 * Sanitize subDetails
	 * @param mixed $value
	 * @return bool
	 */
	protected function sanitizeParameter_subDetails($value) {
		return $this->sanitizeInput_bool_false($value);
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
	 * Validate flatOutput
	 * @param string $value
	 * @return bool
	 protected function validateParameter_flatOutput($value) {
		return $this->validateInput_bool($value);
	}

	/**
	 * Validate outputFormat
	 * @param string $value
	 * @return bool
	 */
	protected function validateParameter_outputFormat($value) {
		if($this->validateInput_scalar($value)) {
			if(in_array(strtolower($value),
					$this->output_formats)) {
				return true;
			}
		}
	}

	/**
	 * Validate statusHeader
	 * @param string $value
	 * @return bool
	 protected function validateParameter_statusHeader($value) {
		return $this->validateInput_bool($value);
	}

	/**
	 * Validate subDetails
	 * @param string $value
	 * @return bool
	 */
	 protected function validateParameter_subDetails($value) {
		return $this->validateInput_bool($value);
	}
}

?>
