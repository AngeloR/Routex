<?php 

namespace Routex;

/**
 * The Route class provides some static methods to allow us to map endpoints to callbacks. 
 */
class Route {

	public static $REQUEST_METHODS = array('GET','PUT','POST','DELETE');

	public static $VERB_GET = array();
	public static $VERB_POST = array();
	public static $VERB_PUT = array();
	public static $VERB_DELETE = array();

	/**
	 * To keep things generic, this method ends up being called by  get/post/put/delete 
	 * to map routes. It maps the regex generated by \Routex\Route::routeBuild to the 
	 * callback for the specific verb.
	 */
	public static function add($httpVerb, $route, callable $callback) {
		if(in_array($httpVerb, self::$REQUEST_METHODS)) {
			$verb = 'VERB_' . $httpVerb;
			$route = self::routeBuild($route);
			self::${$verb}[$route] = $callback;
		}
		else {
			throw new RouteException($httpVerb.' is not a valid HTTP Verb.');
		}
	}

	/**
	 * Looks through our code to find a path that is a match for route that is being requested. 
	 * It then grabs the user-defined args from the list and returns them.
	 */
	public static function find($httpVerb, $path) {
		$verb = 'VERB_' . $httpVerb;

		$res = array('callback' => null, 'args' => array());

		foreach(self::${$verb} as $route => $callback) {
			if($route == $path) {
				$res['callback'] = $callback;
				return $res;
			}
			else if(preg_match($route, $path)){
				$res['callback'] = $callback;
				$res['args'] = self::findArgs($route, $path);
				return $res;
			}
		}

		// Perhaps we should define a default route? It will be triggered at this location
	}

	/**
	 * Pretty straight forward, but I've encapsulated it in a separate function for ease of 
	 * debugging. Basically it grabs all the matched regex vars and returns them.
	 */
	public static function findArgs($regex, $path) {
		preg_match($regex, $path, $matches);
		array_shift($matches);
		var_dump($matches);
		return $matches;
	}

	/**
	 * By far the most complicated function in the entire library. Basically it grabs your 
	 * user-defined route and parses it based on the regex strings. It then returns the final 
	 * regex value that will match it. This section will need to be documented further 
	 */
	private static function routeBuild($path) {
		$single_asterisk_subpattern   = "(?:/([^\/]*))?";
	  	$double_asterisk_subpattern   = "(?:/(.*))?";
	  	$optionnal_slash_subpattern   = "(?:/*?)";
	  	$no_slash_asterisk_subpattern = "(?:([^\/]*))?";

	  	if($path[0] == "^") {
	    	if($path{strlen($path) - 1} != "$") $path .= "$";
	    	$pattern = "#".$path."#i";
	  	}
	  	else if(empty($path) || $path == "/") {
	    	$pattern = "#^".$optionnal_slash_subpattern."$#";
	  	}
	  	else {
	    	$parsed = array();
	    	$pieces = explode('/', $path);


	    	foreach($pieces as $piece) {
	      		if(empty($piece)) continue;

	      		// extracting double asterisk **
	      		if($piece == "**"):
	        		$parsed[] = $double_asterisk_subpattern;

	     		// extracting single asterisk *
	      		elseif($piece == "*"):
	        		$parsed[] = $single_asterisk_subpattern;

	      		// extracting named parameters :my_param 
	      		elseif($piece[0] == ":"):
	        		if(preg_match('/^:([^\:]+)$/', $piece, $matches)) {
	          			$parsed[] = $single_asterisk_subpattern;
	        		};

	     		elseif(strpos($piece, '*') !== false):
	        		$sub_pieces = explode('*', $piece);
	        		$parsed_sub = array();
	        		foreach($sub_pieces as $sub_elt) {
	          			$parsed_sub[] = preg_quote($sub_elt, "#");
	        		}
	        		
	        		$parsed[] = "/".implode($no_slash_asterisk_subpattern, $parsed_sub);

	      		else:
	        		$parsed[] = "/".preg_quote($piece, "#");

	      		endif;
	    	}

	    	$pattern = "#^".implode('', $parsed).$optionnal_slash_subpattern."?$#i";
		}

		return $pattern;
	}

	/**
	 * Shortcut for \Routex\Route::add('GET', $route, $callback);
	 */
	public static function get($route, callable $callback) {
		self::add('GET', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('POST', $route, $callback);
	 */
	public static function post($route, callable $callback) {
		self::add('POST', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('PUT', $route, $callback);
	 */
	public static function put($route, callable $callback) {
		self::add('PUT', $route, $callback);
	}

	/**
	 * Shortcut for \Routex\Route::add('DELETE', $route, $callback);
	 */
	public static function delete($route, callable $callback) {
		self::add('DELETE', $route, $callback);
	}
}

class RouteException extends \Exception {}