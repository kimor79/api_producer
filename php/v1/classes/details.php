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
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package ApiProducerDetails
 */

include('base.php');

class ApiProducerDetails extends ApiProducerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Show output
	 * @param int $status
	 * @param string $message
	 * @param mixed $details
	 */
	public function showOutput($status = 200, $message = '',
			$details = array()) {
		$output = array(
			'details' => $details,
			'message' => $message,
			'status' => $status,
		);

		$function = 'showOutput_' . $this->getParameter('outputFormat');
		if(method_exists($this, $function)) {
			$this->$function($output);
			return;
		}

		// Fallback
		print_r($output);
	}
}

?>
