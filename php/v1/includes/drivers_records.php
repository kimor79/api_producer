<?php

/**

Copyright (c) 2011, Kimo Rosenbaum and contributors
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

/*
 * This bit of code loops through each needed driver and
 * attempts to instantiate it.
 *
 * In the calling file, before including this script, the following
 * will load the foo driver as read-only and the bar driver as read-write:
 *  $drivers_needed = array(
 *    'foo' => 'ro',
 *    'bar' => 'rw',
 *  );
 */

$drivers = array();

if(!is_array($drivers_needed)) {
	$drivers_needed = array();
}

while(list($key, $type) = each($drivers_needed)) {
	$driver = 'driver-' . $key;

	if(!array_key_exists($driver, $config)) {
		$api->setParameters();
		$api->sendHeaders();
		$api->showOutput(array(), 0, 500, 'No such driver');
		exit(0);
	}

	if(!array_key_exists('file', $config[$driver])) {
		$api->setParameters();
		$api->sendHeaders();
		$api->showOutput(array(), 0, 500, 'No driver file configured');
		exit(0);
	}

	if(!array_key_exists('class', $config[$driver])) {
		$api->setParameters();
		$api->sendHeaders();
		$api->showOutput(array(), 0, 500, 'No driver class configured');
		exit(0);
	}

	require_once($config[$driver]['file']);

	try {
		$slave_okay = false;
		switch($type) {
			case 'ro':
				$slave_okay = true;
				break;
		}

		$driver_class = $config[$driver]['class'];

		$drivers[$key] = new $driver_class($slave_okay,
			$config[$driver]);
	} catch (Exception $e) {
		$api->setParameters();
		$api->sendHeaders();
		$api->showOutput(array(), 0, 500, $e->getMessage());
		exit(0);
	}
}

?>
