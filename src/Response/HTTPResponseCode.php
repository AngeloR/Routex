<?php 

namespace Routex\Response;

/**
 * A listing of common response codes that most ReST interfaces will be utilizing. You are 
 * not limited to these, of course, and are welcome to utilize/extend this as you require.
 */
class HttpResponseCode {
	
	public $OK = 200;
	public $CREATED = 201;
	public $MOVED_PERMANENTLY = 301;
	public $FOUND = 302;
	public $BAD_REQUEST = 400;
	public $UNAUTHORIZED = 401;
	public $FORBIDDEN = 403;
	public $NOT_FOUND = 404;
	public $ENTITY_TOO_LARGE = 413;
	public $NOT_IMPLEMENTED = 501;
}