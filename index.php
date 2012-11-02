<?php

include('vendor/angelor/Routex/common/Exceptions.php');
include('vendor/angelor/Routex/src/Response/HTTPResponseCode.php');
include('vendor/angelor/Routex/src/Response/HTTPResponse.php');
include('vendor/angelor/Routex/src/Request/HTTPRequest.php');
include('vendor/angelor/Routex/src/Route/Path.php');
include('vendor/angelor/Routex/src/Routex.php');
include('vendor/angelor/Routex/src/Route.php');


$app = \Routex\Routex::getInstance();

\Routex\Route::get('/', 'index');
\Routex\Route::get('/test', 'yay');
\Routex\Route::get('/test/headers', 'test_headers');
\Routex\Route::get('/test/**','what');


function test_headers($req, $res) {
	var_dump($req->headers());
}

function what($req, $res) {
	\Routex\Route::get('/test/add/:this', 'another');
	$app = \Routex\Routex::getInstance();


	$app->Run();
}

function another($req, $res) {
	var_dump($req);
	echo '<br><br>';
	var_dump($res);
}

function args($req, $res) {
	var_dump($req->param('dothis'). ' - '. $req->param('again'));
}

function yay($req, $res) {
	$res->html('yay');
}

function index($req, $res) {
	$res->statusCode = $res->statusCodes->OK;

	$res->json(array('my' => 'object'));
}

$app->Run();