<?php 

namespace Routex\Response;

/**
 * A listing of common response codes that most ReST interfaces will be utilizing. You are 
 * not limited to these, of course, and are welcome to utilize/extend this as you require.
 */
class HttpResponseCode {
	
	constant $OK = 200;
	constant $CREATED = 201;
	constant $MOVED_PERMANENTLY = 301;
	constant $FOUND = 302;
	constant $BAD_REQUEST = 400;
	constant $UNAUTHORIZED = 401;
	constant $FORBIDDEN = 403;
	constant $NOT_FOUND = 404;
	constant $ENTITY_TOO_LARGE = 413;
	constant $NOT_IMPLEMENTED = 501;
}
