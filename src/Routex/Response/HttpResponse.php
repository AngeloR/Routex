<?php 

namespace Routex\Response;

use \Routex\Common\ResponseHeaderException; 
use \Routex\Response\HttpResponseCode;
use \Routex\Common\MimeType;

/**
 * The Response class is used to return your result back to the user. It applies the 
 * proper mime-types for various types of code, providing an easy to use "shortcut" 
 * in case you don't want to manually set headers.
 * 
 * Currently the response class doesn't do too much, but I wanted to separate it out 
 * so that incase future functionality is necessary I can add it in.
 * 
 * By default the response will be 200 OK, however this can be changed at the 
 * discretion of the user. If an endpoint is hit but does not exist it will 
 * automatically return a 404.
 */
class HttpResponse {

	/**
	 * An array of all custom headers that is being sent to the client.
	 */
	public $headers = array();

	/**
	 * The default status code, this is set to 200 normally
	 */
	public $statusCode;

	/**
	 * @var \Routex\ResponseCode
	 */
	public $statusCodes;

	public function __construct() {
		$this->statusCodes = new HttpResponseCode;
	}

	/**
	 * Write a header. 
	 * 
	 * @var $name string The name of the header
	 * @var $text string The content of the header
	 * @var $overwrite bool Should Routex overwrite an existing header
	 */
	public function writeHeader($name, $content, $overwrite = false) {
		header($name . ': ' . $content, $overwrite);
	}

	/**
	 * This should be used when you do not want to return anything 
	 */
	public function end($mime = null) {
		// set the status codes for phpfpm
		if(empty($this->statusCode)) {
			$this->statusCode = $this->statusCodes->OK;
		}
		$this->writeHeader('Status', $this->statusCode);
		// this header is special due to its format! 
		// set the status codes for mod_php
		header('HTTP/1.1 ' . $this->statusCode);

		
		if(!empty($mime)) {
			$this->writeHeader('Content-type', $mime);
		}
	}

	/**
	 * Return a text type doc
	 */
	public function text($thing) {
		$this->create(MimeType::$text, $thing);
	}

	/**
	 * Return a css type doc
	 */
	public function css($thing) {
		$this->create(MimeType::$css, $thing);
	}

	/**
	 * Return a js type doc
	 */
	public function js($thing) {
		$this->create(MimeType::$js, $thing);
	}

	/**
	 * Return an html type doc
	 */
	public function html($thing) {
		$this->create(MimeType::$html, $thing);
	}

	/**
	 * Return a json type doc
	 */
	public function json($thing) {
		$thing = json_encode($thing);
		// json requests will most likely change, so this is set by default
		$this->writeHeader('Cache-Control', 'no-cache, must-revalidate');
		$this->create(MimeType::$json, $thing);
	}

	/**
	 * Create the headers and output the content.
	 * 
	 * I would advise against using this unless one of the other methods really don't 
	 * meet your needs. If you are trying to add custom headers on top of the other 
	 * methods, use writeHeader. 
	 */
	public function create($mime, $thing) {
		$this->end($mime);

		echo $thing;
	}
}
