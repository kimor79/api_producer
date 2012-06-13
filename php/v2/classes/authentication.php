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
 * APIProducerV2Authentication
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2Authentication
 */

class APIProducerV2Authentication {

	protected $config = array(
		'webserver_auth' => false,
	);

	public function __construct($config = array()) {
		$this->config = array_merge($this->config, $config);
	}

	public function __deconstruct() {
	}

	/**
	 * Get the authenticated user's username
	 * @return string
	 */
	public function getUser() {
		if(array_key_exists('REMOTE_USER', $_SERVER)) {
			return $_SERVER['REMOTE_USER'];
		}

		return '';
	}

	/**
	 * Check if user is authenticated
	 * @return bool
	 */
	public function isAuthenticated() {
		if($this->config['webserver_auth']) {
			if(array_key_exists('REMOTE_USER', $_SERVER)) {
				return true;
			} else {
				return false;
			}
		}

		return true;
	}
}

?>
