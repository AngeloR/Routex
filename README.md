# Routex

Routex is a very simple ReSTful library that was created to solve a simple problem: The complexity barrier for creating truly ReSTful interfaces is too high. 


## Installation

There are two ways to install: You can entire utilize Composer or download the GitHub repo. 

#### GitHub
- [Download the repo](https://github.com/AngeloR/Routex/downloads)
- Unzip it wherever you require
- Set up your psr-0 standard autoloader OR Include the following files: 
	<pre><code>include('vendor/angelor/Routex/src/Common/MimeType.php');
	include('vendor/angelor/Routex/src/Common/Exceptions.php');
	include('vendor/angelor/Routex/src/Response/HttpResponseCode.php');
	include('vendor/angelor/Routex/src/Response/HttpResponse.php');
	include('vendor/angelor/Routex/src/Request/HttpRequest.php');
	include('vendor/angelor/Routex/src/Route/Path.php');
	include('vendor/angelor/Routex/src/Routex.php');
	include('vendor/angelor/Routex/src/Route.php');</code></pre>

#### Composer
- `require: "angelor/routex": "dev-master"`


## Getting Started
<pre><code>use \Routex\Route;

$app = \Routex\Routex::getInstance();

Route::get('/', function($request, $response){
	$response->text('Hello, World.');
});

$app->Run();
</code></pre>

The line `use \Routex\Route;` is purely for convenience to ensure that we don't always need to write `\Routex\Route::get` for our method definition. 

The next line simply gets an instance of our application. The core application simply handles the configuration options and executing the router. 

The next section defines a route that will respond to a `GET` request on the root of the document. If that occurs, it will call the callback (the anonymous function passed as the second argument). The callback is always passed `\Routex\Response\HttpResponse` and `\Routex\Request\HttpRequest` objects. 

Within the callback we set the response type to text and set the body content of the response to "Hello, World." 

Finally we call `$app->Run()` which handles the route matching.


## Routing
Routing is essentially defining an HTTPVerb (GET, POST, PUT, DELETE etc.), a URI endpoint (/, /users, /users/23) and a callback that will be executed when a URI is matched. 

#### Direct Match
Direct matches look for an exact match of the URI to a route. In this case, the callback will only be executed when the URI is /users 
<pre><code>Route::get('/users', function($req, $res){
	
});
</code></pre>

#### Variable Match
Variable matches allow us to set named parameters in our URI. In this case, the callback will only be executed when the URI is /users/anything. In the `HttpRequest` object that is passed to the callback, you can access the value of the variable `$req->param('id')`. 
<pre><code>Route::get('/users/:id', function($req, $res){
	
});
</code></pre>

#### Wildcard Match 
Wildcard paramters are the same as variable matches, except that the values provided MAY contain a `/`. In this case, the callback will only be excuted wehn the URI is `/users/what/are/you/doing`. `$req->param(0)` will contain the value `what/are/you/doing`. 

<pre><code>Route::get('/users/*', function($req, $res){

});
</code></pre>

What is important to note is that routes are NOT matched in the order they are declarted. Instead they are matched in order of increasing complexity. 

#### Regex Match
Regex matches allow us to fine tune our URI matches. In this case, the calback will only be excuted when the URI is /users/12 (where 12 is any number). However, it will not match /users/angelor.

<pre><code>Route::get('/users/(\d+)', function($req, $res){

});
</code></pre>


## Callbacks
Callbacks can be anything that is of the [callable type](http://ca3.php.net/manual/en/language.types.callable.php). 


## Where's the views and models?! 
Routex doesn't ship with those. Calm down though, I have a really good reason. See this is a ReSTful framework and 9/10 times you're using a ReSTful interface because you are trying to build some kind of API and in general you don't need "views". Models are also not included because Routex is meant to be a framework which handles routing and requests. Chances are you already know how you want to build your application and Routex is meant to work WITH your application rather than telling you how it needs to be built. Project organization is entirely up to you. 

What Routex DOES come with, however, is easy repsonse headers. What's that? Simple. Through the `HttpResponse` object in Routex you can easily return html, css, text, js, json or just about anything else you can think of. While the first 5 are provided as part of Routex, if oyu know the mime type for anything you can easily set it through the `HttpResponse->writeHeader` method. 

This means that you can utilize smarty or twig or whatever else you want, and just push the output through `HttpResponse->html()` which will ensure that your HTML is delivered back to the browser with all the appropriate headers in place.


## Features
- Complex routing (with variables and regex) - Define /api/:version/action/:format as a valid URL
- Route injection - The ability to load MORE routes and have them parsed against the request on the fly. 
- Compliance with [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) and [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)


## Todo
- Unit Tests
- Proper getting-started articles