<?php 

namespace Routex;

use \Routex\Route;
use \Routex\Request\HttpRequest;
use \Routex\Response\HttpResponse;


class Routex {

	public $version = '0.2.0';
	private $config;

	function __construct() {
		$this->loadConfig('config.php');
	}

	public function loadConfig($file) {
		if(empty($this->config)) {
			include($file);
			$this->config = $config;
		}
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
		$res = new HttpResponse();
		
		$callback = Route::find($httpVerb, $uri, $req);

		if(is_callable($callback)) {
			$res->statusCode = $res->statusCodes->OK;
			call_user_func($callback, $req, $res, $this);
		}
		else {
			$res->statusCode = $res->statusCodes->NOT_FOUND;
			$res->text('');
		}
	}
}
