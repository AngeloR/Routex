<?php 

namespace Routex;

/**
 * This class defines some basic mime-types that you can utilize in your applications. 
 * 
 * Not every mime type is covered here, just a few standard ones
 */
class MimeType {

	// Plain-text formats
	public static $css = 'text/css';
	public static $html = 'text/html';
	public static $text = 'text/plain';
	public static $xml = 'text/xml';

	// Images
	public static $gif = 'image/gif';
	public static $jpg = 'image/jpg';
	public static $png = 'image/png';
	
	// Video
	public static $flash = 'video/x-flv';
	public static $ogg = 'video/ogg';
	public static $webm = 'video/webm';

	// Application
	public static $application = 'application/octet-stream';
	public static $js = 'application/javascript';
	public static $json = 'application/json';
	public static $pdf = 'application/pdf';
	public static $res = 'application/rss+xml';
	public static $xhtml = 'application/xhtml+xml';
	public static $zip = 'application/zip';
	
}