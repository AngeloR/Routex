<?php 

namespace Routex\Request;

/**
 * The standard HTTPRequest object that is created regardless of whether a route is matched 
 * or not. 
 * 
 * The HTTPRequest object is passed as the first argument to any action endpoint. It 
 * contains information about variables within the url, as well as information related 
 * to the client-side request such as headers.
 */
class HTTPRequest {

	private $params;
	private $headers;

	public function __construct($args = array()) {
		if(is_array($args)) {
			$this->setParams($args);
		}

		$this->setHeaders();
	}

	public function setParams($args) {
		$this->params = $args;
	}
	
	public function param($name) {
		if(is_array($this->params) && array_key_exists($name, $this->params)) {
			return $this->params[$name];
		}
	}

	public function params() {
		return $this->params;
	}

	public function setHeaders() {
		$this->headers = getallheaders();
	}

	public function header($name) {

	}

	public function headers() {
		return $this->headers;
	}
}