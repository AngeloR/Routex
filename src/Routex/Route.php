<?php 

namespace Routex;

use \Routex\Route\RouteException; 
use \Routex\Request\HttpRequest;
use \Routex\Route\Path;
/**
 * The Route class provides some static methods to allow us to map endpoints to callbacks. 
 */
class Route {

	public $VERBS = array(); 
	public $REQUEST_METHODS;

	/**
	 * Create an instance of our Route object
	 * 
	 * @param array $supportedVerbs a list of HTTP Verbs that we are supporting within the application
	 */
	function __construct(array $supportedVerbs) {
		$this->REQUEST_METHODS = $supportedVerbs;

		foreach($this->REQUEST_METHODS as $verb) {
			$this->VERBS[$verb] = array();
		}
	}

	/**
	 * To keep things generic, this method ends up being called by  get/post/put/delete 
	 * to map routes. It maps the regex generated by \Routex\Route::routeBuild to the 
	 * callback for the specific verb.
	 * 
	 * @param string $httpVerb the HTTP Verb (get/post/put etc.) that this route pertains to
	 * @param string $route the route that the user set
	 * @param callable $callback the callback that is executed when the route is matched
	 */
	public function add($httpVerb, $route, $callback) {
		$httpVerb = strtoupper($httpVerb);
		if(in_array($httpVerb, $this->REQUEST_METHODS)) {
			$route = $this->routeBuild($route);
			$route->callback = $callback;
			$this->VERBS[$httpVerb][$route->regex] = $route;

			krsort($this->VERBS[$httpVerb]);
		}
		else {
			throw new RouteException($httpVerb.' is not a valid HTTP Verb.');
		}
	}

	/**
	 * Looks through our code to find a path that is a match for route that is being requested. 
	 * It then grabs the user-defined args from the list and returns them.
	 * 
	 * @param string $httpVerb the HTTP Verb (get/post/put etc.) that we are accessing with
	 * @param string $uri the endpoint that we are trying to access
	 * @param HTTPRequest &$req a reference to our HttpRequest object
	 * 
	 * @return callable the matched callback for the verb
	 */
	public function find($httpVerb, $uri, HttpRequest &$req) {

		foreach($this->VERBS[$httpVerb] as $route => $path) {
			if($route == $uri) {
				// direct match, no need for slower regex
				$callback = $path->callback;
			}
			else if(preg_match($route, $uri)){
				$callback = $path->callback;
				$req->setParams($this->findArgs($uri, $path), $path);

				// if a post var, then grab the post vars :D 
				if($httpVerb == 'POST') {
					$req->setParams($_POST);
				}
			}

			if(isset($callback)) {
				return $callback;
			}
		}
	}

	/**
	 * Pretty straight forward, but I've encapsulated it in a separate function for ease of 
	 * debugging. uses preg_match to figure out the arguments in the endpoint. Hands that 
	 * off to mapArgs to map the arguments by name to value
	 * 
	 * @param string $uri the endpoint that we are trying to access
	 * @param \Routex\Route\Path $path the path object that matches the endpoint
	 * 
	 * @return see mapArgs
	 */
	public function findArgs($uri, \Routex\Route\Path $path) {
		preg_match($path->regex, $uri, $matches);
		array_shift($matches);

		return $this->mapArgs($path, $matches);
	}

	/**
	 * Maps the arguments in a particular path to the names that they were defined with. 
	 * IE: /user/:id 
	 * 		This will ensure that HttpRequest->param('id') is set to the value.
	 *
	 * @param \Routex\Route\Path $path the path object that matches the endpoint
	 * @param $values a list of the values of all variable elements within the route
	 *
	 * @return array an associative array of argument names and values
	 */
	public function mapArgs(\Routex\Route\Path $path, array $values) {
		$args = array();
		$length = count($values);

		for($i = 0; $i < $length; $i++) {
			$name = $path->paramNames[$i];
			if(empty($name)) {
				$args[] = $values[$i];
			}
			else {
				$args[$path->paramNames[$i]] = $values[$i];
			}
		}

		return $args;
	}

	/**
	 * By far the most complicated function in the entire library. Basically it grabs your 
	 * user-defined route and parses it based on the regex strings. It then returns the final 
	 * regex value that will match it. This section will need to be documented further 
	 */
	private function routeBuild($path) {
		$routePath = new Path();

	  	$single_asterisk_subpattern   = "(?:/(.*))?";
	  	$optionnal_slash_subpattern   = "(?:/*?)";
	  	$no_slash_asterisk_subpattern = "(?:([^\/]*))?";

	  	

	  	if($path[0] == "^") {
	    	if($path{strlen($path) - 1} != "$") {
	    		$path .= "$";
	    	}
	    	$pattern = "#".$path."#i";
	  	}
	  	else if(empty($path) || $path == "/") {
	    	$pattern = "#^".$optionnal_slash_subpattern."$#";
	  	}
	  	else {
	    	$parsed = array();
	    	$pieces = explode('/', $path);
	    	$paramCount = 0;

	    	foreach($pieces as $piece) {
	      		if(empty($piece)) continue;

	      		// extracting single asterisk *
	      		if($piece == "*") {
	        		$parsed[] = $single_asterisk_subpattern;
	        		$name = $paramCount;
	        	}
	      		// extracting named parameters :my_param 
	      		elseif($piece[0] == ":") {
	        		if(preg_match('/^:([^\:]+)$/', $piece, $matches)) {
	          			$parsed[] = $single_asterisk_subpattern;
	          			$name = $matches[1];
	        		};
	        	}
	     		elseif(strpos($piece, '*') !== false) {
	        		$sub_pieces = explode('*', $piece);
	        		$parsed_sub = array();
	        		foreach($sub_pieces as $sub_elt) {
	          			$parsed_sub[] = preg_quote($sub_elt, "#");
	        		}
	        		
	        		$parsed[] = "/".implode($no_slash_asterisk_subpattern, $parsed_sub);
	        	}
	      		else {
	        		$parsed[] = "/".preg_quote($piece, "#");
	        		$name = $paramCount;
	        	}

	        	if(!empty($name)) {
	        		$routePath->paramNames[] = $name;
	        		$paramCount++;
	        	}
	    	}

	    	//$routePath->paramNames[] = $matches;
	    	$pattern = "#^".implode('', $parsed).$optionnal_slash_subpattern."?$#i";
		}

		$routePath->regex = $pattern;

		return $routePath;
	}

	/**
	 * Shortcut for \Routex\Route::add('GET', $route, $callback);
	 * 
	 * @param string $route the endpoint that you want to handle
	 * @param callable $callback the callback that you want to execute
	 */
	public function get($route, $callback) {
		$this->add('GET', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('POST', $route, $callback);
	 * @param string $route the endpoint that you want to handle
	 * @param callable $callback the callback that you want to execute
	 */
	public function post($route, $callback) {
		$this->add('POST', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('PUT', $route, $callback);
	 * @param string $route the endpoint that you want to handle
	 * @param callable $callback the callback that you want to execute
	 */
	public function put($route, $callback) {
		$this->add('PUT', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('DELETE', $route, $callback);
	 * @param string $route the endpoint that you want to handle
	 * @param callable $callback the callback that you want to execute
	 */
	public function delete($route, $callback) {
		$this->add('DELETE', $route, $callback);
	}
}

