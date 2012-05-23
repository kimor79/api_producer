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
 * APIProducerV2Input
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2Input
 */

include_once __DIR__ . '/validators.php';

class APIProducerV2Input extends APIProducerV2Validators {

	public function __construct() {
	}

	public function __deconstruct() {
	}

	/**
	 * Get the parameters/input from the input (GET, POST, JSON, etc)
	 * @param string $where Where to get input from (GPJ)
	 * @return mixed array(input, parameters) or false
	 */
	public function getInput($where = 'GPJ') {
		$input = array();
		$params = array();
		$request = array();

		if(stripos($where, 'G') !== false) {
			$request = array_merge($request, $_GET);
		}

		if(stripos($where, 'P') !== false) {
			$request = array_merge($request, $_POST);
		}

		if(stripos($where, 'J') !== false) {
			$s_json = file_get_contents('php://input');
			$json = json_decode($s_json, true);

			if($json) {
				$request = array_merge($request, $json);
			}
		}

		while(list($key, $value) = each($request)) {
			if(preg_match('/[a-z][A-Z]/', $key)) {
				$params[$key] = $value;
			} else {
				$input[$key] = $value;
			}
		}
		reset($params);

		return array($input, $params);
	}

	/**
	 * Do the get_magic_quotes_gpc/stripslashes dance
	 * @param string $input
	 * @return string
	 */
	public function gpcSlash($input) {
		if(get_magic_quotes_gpc()) {
			if(is_string($input)) {
				return stripslashes($input);
			}
		}

		return $input;
	}

	/**
	 * Recursively do the get_magic_quotes_gpc/stripslashes dance
	 * @param array $input
	 * @return array
	 */
	public function gpcSlashInput($input = array()) {
		$output = array();

		while(list($key, $value) = each($input)) {
			if(is_array($value)) {
				$output[$key] = $this->gpcSlashInput($value);
			} else {
				$output[$key] = $this->gpcSlash($value);
			}
		}
		reset($input);

		return $output;
	}

	/**
	 * Remove (empty) values
	 * @param array $input
	 * @param array $remove optional list of values (default is '')
	 * @return array
	 */
	public function removeValues($input, $remove = array()) {
		$output = array();

		if(empty($remove)) {
			$remove = array('');
		}

		while(list($key, $value) = each($input)) {
			if(!in_array($value, $remove, true)) {
				$output[$key] = $value;
			}
		}
		reset($input);

		return $output;
	}

	/**
	 * Sanitize input
	 * @param array $input
	 * @param array $sanitize Keys and their sanitize functions
	 * @return array
	 */
	public function sanitizeInput($input, $sanitize) {
		$keys = array_intersect_key($sanitize, $input);
		$output = array_diff_key($input, $sanitize);

		while(list($key, $function) = each($keys)) {
			$function = 'sanitizeInput_' . $function;
			if(method_exists($this, $function)) {
				$output[$key] = $this->$function($input[$key]);
			}
		}
		reset($keys);

		return $output;
	}

	/**
	 * Validate input
	 * @param array $input
	 * @param array $required Required keys
	 * @param array $optional Optional keys
	 * @param bool $needed true to check for extraneous keys
 	 * @return array List of errors if any
	 */
	public function validateInput($input, $required, $optional,
			$needed = false) {
		$errors = array();
		$keys = array();

		while(list($key, $function) = each($required)) {
			if(!array_key_exists($key, $input)) {
				$errors[] = 'Missing ' . $key;
				continue;
			}

			if(empty($function)) {
				continue;
			}

			$keys[$key] = $function;
		}
		reset($required);

		while(list($key, $function) = each($optional)) {
			if(array_key_exists($key, $input)) {
				if(!empty($function)) {
					$keys[$key] = $function;
				}
			}
		}
		reset($optional);

		if($needed) {
			$extra = array_diff_key($input, $required, $optional);

			while(list($key, $junk) = each($extra)) {
				$errors[] = 'Extraneous ' . $key;
			}
			reset($extra);
		}

		while(list($key, $func) = each($keys)) {
			$function = 'validateInput_' . $func;
			if(!method_exists($this, $function)) {
				$errors[] = 'Unable to validate ' . $key;
				continue;
			}

			if(!$this->$function($input[$key])) {
				$errors[] = 'Invalid ' . $key;
			}
		}
		reset($keys);

		return $errors;
	}
}

?>
