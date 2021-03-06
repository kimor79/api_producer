<?php

/*

Copyright (c) 2012-2013, Kimo Rosenbaum and contributors
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
 * APIProducerV2Validators
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2Validators
 */

class APIProducerV2Validators {

	public function __construct() {
	}

	public function __deconstruct() {
	}

	/**
	 * Sanitize to bool (defaulting to false)
	 * @param string $input
	 * @return bool
	 */
	protected function sanitizeInput_bool_false($input) {
		if(is_bool($input)) {
			return $input;
		}

		if($input === 0) {
			return false;
		}

		if($input === 1) {
			return true;
		}

		if(is_string($input)) {
			switch(strtolower($input)) {
				case '1':
				case 'on':
				case 'yes':
				case 'true':
					return true;
			}
		}

		return false;
	}

	/**
	 * Sanitize to bool (defaulting to true)
	 * @param string $input
	 * @return bool
	 */
	protected function sanitizeInput_bool_true($input) {
		if(is_bool($input)) {
			return $input;
		}

		if($input === 0) {
			return false;
		}

		if($input === 1) {
			return true;
		}

		if(is_string($input)) {
			switch(strtolower($input)) {
				case '0':
				case 'false':
				case 'no':
				case 'off':
					return false;
			}
		}

		return true;
	}

	/**
	 * Sanitize fqdn
	 * @param mixed $input
	 * @return string
	 */
	protected function sanitizeInput_fqdn($input) {
		$input = strtolower($input);
		$input = rtrim($input, '.');

		return $input;
	}

	/**
	 * Sanitize to integer
	 * @param mixed $input
	 * @return int
	 */
	protected function sanitizeInput_int($input) {
		return (int) $input;
	}

	/**
	 * Alias of sanitizeInput_regexp
	 */
	protected function sanitizeInput_regex($input) {
		return $this->sanitizeInput_regexp($input);
	}

	/**
	 * Sanitize a regex
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_regexp($input) {
		return $input;
	}

	/**
	 * Sanitize a sha256 as hex
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_sha256_hex($input) {
		return $this->sanitizeInput_tolower($input);
	}

	/**
	 * Sanitize a timestamp (strtotime)
	 * @param string $input
	 * @return int
	 */
	protected function sanitizeInput_timestamp($input) {
		if($this->validateInput_digit($input)) {
			if(date('U', $input)) {
				return $this->sanitizeInput_int($input);
			}

			return 0;
		}

		$date = strtotime($input);

		if($date === -1 || $date === false) {
			return 0;
		}

		return $date;
	}

	/**
	 * Sanitize to lowercase
	 * @param string $input
	 * @param string
	 */
	protected function sanitizeInput_tolower($input) {
		return strtolower($input);
	}

	/**
	 * Validate input is "bool"
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_bool($input) {
		if(is_bool($input)) {
			return true;
		}

		if($input === 0) {
			return true;
		}

		if($input === 1) {
			return true;
		}

		if(is_string($input)) {
			switch(strtolower($input)) {
				case '0':
				case '1':
				case 'false':
				case 'no':
				case 'off':
				case 'on':
				case 'true':
				case 'yes':
					return true;
			}
		}

		return false;
	}

	/**
	 * Validate digit
	 * @param string $value
	 * @return bool
	 */
	protected function validateInput_digit($value) {
		if(is_scalar($value)) {
			if(preg_match('/^\d+$/', $value)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate input is a fqdn
	 * @param string $value
	 * @return bool
	 */
	protected function validateInput_fqdn($value) {
		if(strpos($value, '..') === false) {
			if(preg_match('/^[a-z0-9][a-z0-9.-]+$/i', $value)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Alias of validateInput_regexp
	 */
	protected function validateInput_regex($value) {
		return $this->validateInput_regexp($value);
	}

	/**
	 * Validate input is a valid regex
	 * @param string $value
	 * @return bool
	 */
	protected function validateInput_regexp($value) {
		if(@preg_match($value, 'foo') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Validate input is only one (e.g., not an array)
	 * @param string $value
	 * @return bool
	 */
	protected function validateInput_scalar($value) {
		if(is_scalar($value)) {
			return true;
		}

		return false;
	}

	/**
	 * Validate input looks like a sha256 hex hash
	 * @param string $value
	 * @return bool
	 */
	protected function validateInput_sha256_hex($value) {
		if(length($value) === 64) {
			if(preg_match('/^[A-Fa-f0-9]{64}$/', $value)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate input is a timestamp (anything strtotime can convert)
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_timestamp($input) {
		if($this->validateInput_digit($input)) {
			if(date('U', $input)) {
				return true;
			}
		}

		$date = strtotime($input);

		if($date !== -1 && $date !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Validate input is a url (according to filter_var)
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_url($input) {
		if(filter_var($input, FILTER_VALIDATE_URL) !== false) {
			return true;
		}

		return false;
	}
}

?>
