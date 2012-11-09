<?php 

namespace Routex\Request;

/**
 * The standard HttpRequest object that is created regardless of whether a route is matched 
 * or not. 
 * 
 * The HttpRequest object is passed as the first argument to any action endpoint. It 
 * contains information about variables within the url, as well as information related 
 * to the client-side request such as headers.
 */
class HttpRequest {

	public $httpVerb; 
	public $uri;

	private $params;
	private $headers;

	public function __construct($verb, $uri) {
		$this->httpVerb = $verb;
		$this->uri = $uri;

		$this->params = array();
		$this->setHeaders();
	}

	public function setParams($args) {
		$this->params = array_merge($this->params, $args);
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
		if(is_array($this->headers)) {
			return $this->headers[$name];
		}
		return null;
	}

	public function headers() {
		return $this->headers;
	}
}