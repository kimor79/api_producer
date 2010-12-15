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
		'numResults' => 0,
		'outputFields' => array(),
		'outputFormat' => 'json',
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
	);

	protected $output_formats = array(
		'csv',
		'json',
		'list',
		'print_r',
	);

	protected $parameters = array();

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
	 * Sanitize csvHeader value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_csvHeader($value) {
		return $this->trueFalse($value, $this->api_parameters['csvHeader']);
	}

	/**
	 * Sanitize flatOutput value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_flatOutput($value) {
		return $this->trueFalse($value, $this->api_parameters['flatOutput']);
	}

	/**
	 * Sanitize numResults value
	 * @param string $value
	 * @return int
	 */
	protected function sanitizeParameter_numResults($value) {
		if(ctype_digit((string) $value)) {
			return (int) $value;
		}

		return $this->api_parameters['numResults'];
	}

	/**
	 * Sanitize outputFields value
	 * @param mixed $value
	 * @return array
	 */
	protected function sanitizeParameter_outputFields($value) {
		$fields = array();
		$keys = array();

		if(is_array($value)) {
			$keys = $value;
		} else {
			$keys = explode(',', $value);
		}

		foreach($keys as $key) {
			if($key == '') {
				continue;
			}

			if(substr($key, 0, 1) == '!') {
				$fields[$key] = false;
			} else {
				$fields[$key] = true;
			}
		}

		return $fields;
	}

	/**
	 * Sanitize outputFormat value
	 * @param string $value
	 * @return string
	 */
	protected function sanitizeParameter_outputFormat($value) {
		$t_value = strtolower((string) $value);
		if(in_array($t_value, $this->output_formats)) {
			return $t_value;
		}

		return $this->api_parameters['outputFormat'];
	}

	/**
	 * Sanitize sortDir value
	 * @param string $value
	 * @return string
	 */
	protected function sanitizeParameter_sortDir($value) {
		switch(strtolower((string) $value)) {
			case 'asc':
			case 'ascending':
				return 'asc';
				break;
			case 'desc':
			case 'descending':
				return 'desc';
				break;
		}

		return $this->api_parameters['sortDir'];
	}

	/**
	 * Sanitize subDetails value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_subDetails($value) {
		return $this->trueFalse($value, $this->api_parameters['subDetails']);
	}

	/**
	 * Sanitize startIndex value
	 * @param string $value
	 * @return int
	 */
	protected function sanitizeParameter_startIndex($value) {
		if(ctype_digit((string) $value)) {
			return (int) $value;
		}

		return $this->api_parameters['startIndex'];
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
	 * Checks whether input is "true" (yes, 1, on) or "false" (no, 0, off)
	 * @param string $input
	 * @param bool $default What to return if input is neither true/false
	 * @return bool
	 */
	public function trueFalse($input, $default) {
		switch(strtolower((string) $input)) {
			case '1':
			case 'on':
			case 'true':
			case 'yes':
				return true;
			case '0':
			case 'false':
			case 'no':
			case 'off':
				return false;
		}

		if(is_bool($default)) {
			return $default;
		}

		return false;
	}

	/**
	 * Validate input
	 * @param array $input
	 * @param array $required Required keys
	 * @param array $optional Optional keys
	 * @param bool $needed false to skip check for extraneous keys
	 * @return array list of errors or an empty array
	 */
	public function validateInput($input = array(), $required = array(), $optional = array(), $needed = false) {
		$errors = array();
		$keys = array();

		foreach($required as $key => $func) {
			if(!array_key_exists($key, $input)) {
				$errors[] = 'Missing ' . $key;
				continue;
			}

			$keys[$key] = $func;
		}

		foreach($optional as $key => $func) {
			if(array_key_exists($key, $input)) {
				$keys[$key] = $func;
			}
		}

		if($needed) {
			$extra = array_diff_key($input, $required, $optional);

			foreach($extra as $key) {
				$errors[] = 'Extraneous ' . $key;
			}
		}

		foreach($keys as $key => $func) {
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

		return $errors;
	}
}

?>
