<?php 

namespace Routex\Common;

/**
 * This class defines some basic mime-types that you can utilize in your applications. 
 * 
 * Not every mime type is covered here, just a few standard ones
 */
class MimeType {

	// Plain-text formats
	constant $CSS = 'text/css';
	constant $HTML = 'text/html';
	constant $TEXT = 'text/plain';
	constant $XML = 'text/xml';

	// Images
	constant $GIF = 'image/gif';
	constant $JPG = 'image/jpg';
	constant $PNG = 'image/png';

	// Video
	constant $FLASH = 'video/x-flv';
	constant $OGG = 'video/ogg';
	constant $WEBM = 'video/webm';

	// Application
	constant $APPLICATION = 'application/octet-stream';
	constant $JS = 'application/javascript';
	constant $JSON = 'application/json';
	constant $PDF = 'application/pdf';
	constant $RES = 'application/rss+xml';
	constant $XHTML = 'application/xhtml+xml';
	constant $ZIP = 'application/zip';
	
}
