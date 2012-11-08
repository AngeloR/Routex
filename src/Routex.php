<?php 

namespace Routex;

use \Routex\Route;
use \Routex\Request\HttpRequest;


/**
 * Our main class. I've tried my best to ensure that this doesn't end up a god class and god 
 * dammit I'll make whatever changes I need to so that that is ensured. While I enjoy how 
 * CI handles everything, I dislike that everything is accessible from everything else so easily. 
 * That means something is tying everything together, which isn't reusable. That's coupling. 
 * But this document isn't the place to discuss my feelings on the matter.
 */
class Routex {
	public $version = '0.1.0';
	private $config;

	// instance
	private static $instance; 

	/**
	 * Instances? But they're ugly yes? yes. But it's the best way to ensure that we are only 
	 * leaving a single access point for what essentially is the registry (config.php);
	 */
	private function __construct() {
		require_once('config.php');
		$this->config = $config;
	}

	/**
	 * Returns the instance of Routex
	 */
	public static function getInstance() {
		if(!isset(self::$instance)) {
			self::$instance = new Routex(); 
		}

		return self::$instance;
	}

	/**
	 * Pass in a .delimitered path and it returns the result. Want to know the active db? 
	 * Config('db.active'). Pretty nifty right? It tries to go as far through your path 
	 * as it can before it fails and returns whatever it can find. 
	 */
	public function Config($key_path = '', $root = null) {
		if(empty($root)) {
			$root = $this->config;
		}

		$pieces = explode('.', $key_path);
		$node = array_shift($pieces);

		if(is_array($root) && array_key_exists($node, $root)) {
			$root = $this->Config(join('.', $pieces), $root[$node]);
		}
		
		return $root;
	}


	/**
	 * A nessessary end-point. Although it shows up at the end of index.php really what it does 
	 * is parse the current route and hand it off the "exec" which deals with getting the 
	 * callback and actually calling it. 
	 */
	public function Run() {
		$httpVerb = $_SERVER['REQUEST_METHOD'];
		$route_path = $this->Config('route.path');
		
		if(!array_key_exists($route_path, $_GET) || empty($_GET[$route_path])) {
			$path = '/';
		}
		else {
			$path = $_GET[$this->Config('route.path')];
		}
		
		$this->Exec($httpVerb, $path);
	}

	/**
	 * This takes our verb and final path and figures out the callback. Note that even though it 
	 * is an integral part of Run it is separate. This means that you can perform "lazyLoading" 
	 * to add new routes. Instead of adding 1000+ routes all at once you can add them as certain 
	 * parameters are matched.
	 */
	public function Exec($httpVerb, $uri) {
		$req = new HttpRequest();
		// set headers!

		$callback = Route::find($httpVerb, $uri, $req);

		$res = new HttpResponse();

		if(is_callable($callback)) {
			$res->statusCode = $res->STATUS['OK'];
			call_user_func($callback, $req, $res);
		}
		else {
			$res->statusCode = $res->statusCodes->NOT_FOUND;
			$res->text('');
		}
	}
}