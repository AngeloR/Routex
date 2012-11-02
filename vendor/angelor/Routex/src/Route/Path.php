<?php 

namespace Routex\Route;

/**
 * The Path is simply an object that contains the regex, parameter names and callback for 
 * every route. 
 */
class Path {

	public $regex;
	public $paramNames;
	public $callback;

	public function __construct() {
		$this->paramNames = array();
	}
}