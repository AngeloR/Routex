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

	/**
	 * @var string $verb the http verb that the request is generated with
	 * @var string $uri the endpoint that is being accessed
	 */
	public function __construct($verb, $uri) {
		$this->httpVerb = $verb;
		$this->uri = $uri;

		$this->params = array();
		$this->setHeaders();
	}

	/**
	 * Set all the arguments pertaining to this request
	 * 
	 * @var array $args a list of the arguments in this endpoint
	 */
	public function setParams(array $args) {
		$this->params = array_merge($this->params, $args);
	}
	
	/**
	 * Return the value of a particular argument
	 * 
	 * @var string $name the key of the argument that you are trying to access
	 * 
	 * @return mixed the value of the argument being accessed
	 */
	public function param($name) {
		if(is_array($this->params) && array_key_exists($name, $this->params)) {
			return $this->params[$name];
		}
	}

	/**
	 * Return all arguments that are matched in this request
	 * 
	 * @return array a list of all uri params
	 */
	public function params() {
		return $this->params;
	}

	/**
	 * Get all request headers and set them so that they are accessible within this object
	 */
	public function setHeaders() {
		$this->headers = getallheaders();
	}

	/**
	 * Get the value of a particular header
	 * 
	 * @var string $name the header you are trying to retrieve the value for
	 * 
	 * @return string the value of the header
	 */
	public function header($name) {
		if(is_array($this->headers)) {
			return $this->headers[$name];
		}
		return null;
	}

	/**
	 * Get all the headers
	 * 
	 * @return array all the headers pertaining to this request
	 */
	public function headers() {
		return $this->headers;
	}
}