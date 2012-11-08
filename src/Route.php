<?php 

namespace Routex;

use \Common\RouteException; 
use \Routex\Request\HTTPRequest;
use \Routex\Route\Path;
/**
 * The Route class provides some static methods to allow us to map endpoints to callbacks. 
 */
class Route {

	public static $REQUEST_METHODS = array('GET','PUT','POST','DELETE', 'HEAD');

	public static $VERBS = array(); 

	/**
	 * To keep things generic, this method ends up being called by  get/post/put/delete 
	 * to map routes. It maps the regex generated by \Routex\Route::routeBuild to the 
	 * callback for the specific verb.
	 */
	public static function add($httpVerb, $route, $callback) {
		$httpVerb = strtoupper($httpVerb);
		if(in_array($httpVerb, self::$REQUEST_METHODS)) {
			$route = self::routeBuild($route);
			$route->callback = $callback;
			self::$VERBS[$httpVerb][$route->regex] = $route;

			krsort(self::$VERBS[$httpVerb]);
		}
		else {
			throw new RouteException($httpVerb.' is not a valid HTTP Verb.');
		}
	}

	/**
	 * Looks through our code to find a path that is a match for route that is being requested. 
	 * It then grabs the user-defined args from the list and returns them.
	 */
	public static function find($httpVerb, $uri, &$req) {

		foreach(self::$VERBS[$httpVerb] as $route => $path) {
			if($route == $uri) {
				// direct match, no need for slower regex
				$callback = $path->callback;
			}
			else if(preg_match($route, $uri)){
				$callback = $path->callback;
				$req->setParams(self::findArgs($uri, $path), $path);

				// if a post var, then grab the post vars :D 
				if($httpVerb == 'POST') {
					$req->setParams($_POST);
				}
			}

			if(isset($callback)) {
				return $callback;
			}
		}

		// Perhaps we should define a default route? It will be triggered at this location
	}

	/**
	 * Pretty straight forward, but I've encapsulated it in a separate function for ease of 
	 * debugging. Basically it grabs all the matched regex vars and returns them.
	 */
	public static function findArgs($uri, \Routex\Route\Path $path) {
		preg_match($path->regex, $uri, $matches);
		array_shift($matches);

		return self::mapArgs($path, $matches);
	}

	public static function mapArgs(\Routex\Route\Path $path, $values) {
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
	private static function routeBuild($path) {
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
	        	}

	        	$routePath->paramNames[] = $name;
	        	$paramCount++;
	    	}

	    	//$routePath->paramNames[] = $matches;
	    	$pattern = "#^".implode('', $parsed).$optionnal_slash_subpattern."?$#i";
		}

		$routePath->regex = $pattern;

		return $routePath;
	}

	/**
	 * Shortcut for \Routex\Route::add('GET', $route, $callback);
	 */
	public static function get($route, $callback) {
		self::add('GET', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('POST', $route, $callback);
	 */
	public static function post($route, $callback) {
		self::add('POST', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('PUT', $route, $callback);
	 */
	public static function put($route, $callback) {
		self::add('PUT', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('DELETE', $route, $callback);
	 */
	public static function delete($route, $callback) {
		self::add('DELETE', $route, $callback);
	}
}

