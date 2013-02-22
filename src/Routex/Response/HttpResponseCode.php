<?php 

namespace Routex\Response;

/**
 * A listing of common response codes that most ReST interfaces will be utilizing. You are 
 * not limited to these, of course, and are welcome to utilize/extend this as you require.
 */
class HttpResponseCode {
	
	const $OK = 200;
	const $CREATED = 201;
	const $MOVED_PERMANENTLY = 301;
	const $FOUND = 302;
	const $BAD_REQUEST = 400;
	const $UNAUTHORIZED = 401;
	const $FORBIDDEN = 403;
	const $NOT_FOUND = 404;
	const $ENTITY_TOO_LARGE = 413;
	const $NOT_IMPLEMENTED = 501;
}
