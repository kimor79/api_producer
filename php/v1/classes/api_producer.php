<?php

/**

Copyright (c) 2010, Kimo Rosenbaum and contributors
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

**/

/**
 * ApiProducer
 * @author Kimo Rosenbaum <kimo@bitgravity.com>
 * @version $Id$
 * @package ApiProducer
 */

class ApiProducer {

	protected $allow_input_arrays = false;

	protected $api_parameters = array(
		'contentType' => true,
		'csvHeader' => true,
		'flatOutput' => false,
		'outputFields' => array(),
		'outputFormat' => 'json',
		'numResults' => 0,
		'sortDir' => 'desc',
		'sortField' => NULL,
		'subDetails' => false,
		'startIndex' => 0,
	);

	// Add content-disposition header
	// Value is file extension
	// Set to NULL to skip header
	protected $content_disposition = array(
		'csv' => 'csv',
	);

	protected $content_types = array(
		'csv' => 'text/csv',
		'json' => 'application/json',
		'list' => 'text/plain',
		'print_r' => NULL,
	);

	protected $output_formats = array(
		'csv',
		'json',
		'list',
		'print_r',
	);

	protected $requires_flat_output = array(
		'csv',
	);

	/**
	 * Add/modify content-disposition header for given output format
	 * @param string output format
	 * @param string file extension header (or NULL)
	 */
	public function contentDisposition($format, $ext) {
		if(in_array($format, $this->output_formats)) {
			if(is_null($ext) || is_scalar($ext)) {
				$this->content_disposition[$format] = $ext;
			}
		}
	}

	/**
	 * Add/modify content-type header for given output format
	 * @param string output format
	 * @param string content-type header (or NULL)
	 */
	public function contentTypes($format, $header) {
		if(in_array($format, $this->output_formats)) {
			if(is_null($header) || is_scalar($header)) {
				$this->content_types[$format] = $header;
			}
		}
	}

	/**
	 * Get the value for given parameter
	 * @param string $parameter
	 * @return mixed
	 */
	public function getParameter($parameter) {
		if(array_key_exists($parameter, $this->parameters)) {
			return $this->parameters[$parameter];
		}

		return NULL;
	}

	/**
	 * Remove API parameters from input
	 * @param array $input
	 * @return array
	 */
	public function setInput($input = array()) {
		$output = array();

		foreach($input as $key => $value) {
			if(!array_key_exists($key, $this->parameters)) {
				$output[$key] = $value;
			}
		}

		return $output;
	}

	/**
	 * Set the API parameters from request (eg $_GET, $_POST)
	 * @param array $input array containing input to look for parameters [default = $_GET]
	 * @param array $defaults Override default values
	 */
	public function setParameters($input = false, $defaults) {
		$array = $_GET;

		if(is_array($input)) {
			$array = $input;
		}

		foreach($this->api_parameters as $param => $d_value) {
			$value = $d_value;
			if(array_key_exists($param, $array)) {
				$value = $array[$param];
			} else {
				if(array_key_exists($param, $defaults)) {
					$value = $defaults[$param];
				}
			}

			$function = 'sanitizeParameter_' . $param;
			if(method_exists($this, $function)) {
				$this->parameters[$param] = $this->$function($value);
			} else {
				$this->parameters[$param] = $value;
			}
		}

		if(in_array($this->parameters['outputFormat'], $this->requires_flat_output)) {
			if(!is_null($this->requires_flat_output[$this->parameters['outputFormat']])) {
				$this->parameters['flatOutput'] = true;
			}
		}
	}

	/**
	 * Validate input
	 * @param array $input
	 * @param array $required Required keys
	 * @param array $optional Optional keys
	 * @param bool $needed false to skip check for extraneous keys
	 * @return array list of errors or an empty array
	public function validateInput($input = array(), $required = array(), $optional = array(), $needed = false) {
		$errors = array();
		$tests = array();

		foreach($required as $key => $func) {
			if(!array_key_exists($key, $input)) {
				$errors[] = 'Missing ' . $key;
				continue;
			}

			$tests[$key] = $func;
		}

			$tests = array();

			if(is_array($input[$key])) {
				if(!$this->allow_input_arrays) {
					$errors[] = 'Multiple ' . $key . ' not allowed';
					continue;
				}

				$tests = $input[$key];
			} else {
				$tests[] = $input[$key];
			}

			$function = 'validateInput_' . $func;
			if(!method_exists($this, $function)) {
				$errors[] = 'Unable to validate ' . $key;
				continue;
			}

			foreach($tests as $test) {
				if($this->$function($input[$key]) !== true) {
					$errors[] = 'Invalid ' . $key;
					continue;
				}
			}
		}
	}
}

?>
