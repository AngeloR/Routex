<?php 

namespace Routex;

use \Routex\Route;
use \Routex\Request\HttpRequest;
use \Routex\Response\HttpResponse;
use \Routex\Response\HttpResponseCode;

class Routex {

	public $version = '0.3.0';
	private $config;

	function __construct() {
		$this->loadConfig('config.php');
	}

	/**
	 * Load the configuration file defined
	 * 
	 * @param string $file the path and filename of the config file to load
	 */
	public function loadConfig($file) {
		if(empty($this->config)) {
            if(file_exists($file)) {
                include($file);
    		    $this->config = $config;   
            }
            else {
                throw new Exception('Configuration file not set');
            }
		}
	}

	/**
	 * Pass in a .delimitered path and it returns the result. Want to know the active db? 
	 * Config('db.active'). Pretty nifty right? It tries to go as far through your path 
	 * as it can before it fails and returns whatever it can find. 
	 * 
	 * @param string $key_path (optional) the . delimitered path to a config var
	 * @param string $root (optional) used internally for recursion
	 * 
	 * @return mixed the value from the path to the config var
	 */
	public function config($key_path = '', $root = null) {
		if(empty($root)) {
			$root = $this->config;
		}

		$pieces = explode('.', $key_path);
		$node = array_shift($pieces);

		if(is_array($root) && array_key_exists($node, $root)) {
			$root = $this->config(join('.', $pieces), $root[$node]);
		}
		
		return $root;
	}

	/**
	 * A nessessary end-point. Although it shows up at the end of index.php really what it does 
	 * is parse the current route and call the appropriate callback
	 * 
	 * @param \Routex\Route $route the \Routex\Route object containing all the endpoints defined
	 */
	public function run(\Routex\Route $route) {
		$httpVerb = $_SERVER['REQUEST_METHOD'];
		$route_path = $this->config('route.path');
		
		if(!array_key_exists($route_path, $_GET) || empty($_GET[$route_path])) {
			$path = '/';
		}
		else {
			$path = $_GET[$this->config('route.path')];
		}

		$req = new HttpRequest($httpVerb, $path);
		$res = new HttpResponse(new HttpResponseCode);
		
		$callback = $route->find($httpVerb, $path, $req);

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
