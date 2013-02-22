<?php 

namespace Routex\Common;

/**
 * This class defines some basic mime-types that you can utilize in your applications. 
 * 
 * Not every mime type is covered here, just a few standard ones
 */
class MimeType {

	// Plain-text formats
	const CSS = 'text/css';
	const HTML = 'text/html';
	const TEXT = 'text/plain';
	const XML = 'text/xml';

	// Images
	const GIF = 'image/gif';
	const JPG = 'image/jpg';
	const PNG = 'image/png';

	// Video
	const FLASH = 'video/x-flv';
	const OGG = 'video/ogg';
	const WEBM = 'video/webm';

	// Application
	const APPLICATION = 'application/octet-stream';
	const JS = 'application/javascript';
	const JSON = 'application/json';
	const PDF = 'application/pdf';
	const RES = 'application/rss+xml';
	const XHTML = 'application/xhtml+xml';
	const ZIP = 'application/zip';
	
}
