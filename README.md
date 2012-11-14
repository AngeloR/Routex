# Routex

Routex is a very simple ReSTful library that was created to solve a simple problem: The complexity barrier for creating truly ReSTful interfaces is too high. 


## Installation

There are two ways to install: You can entire utilize Composer or download the GitHub repo. 

#### GitHub
- [Download the repo](https://github.com/AngeloR/Routex/downloads)
- Unzip it wherever you require
- Set up your psr-0 standard autoloader OR Include the following files: 
	<pre><code>include('vendor/angelor/Routex/src/Common/MimeType.php');
	include('vendor/angelor/Routex/src/Routex/Response/ResponseHeaderException.php');
	include('vendor/angelor/Routex/src/Routex/Response/HttpResponseCode.php');
	include('vendor/angelor/Routex/src/Routex/Response/HttpResponse.php');
	include('vendor/angelor/Routex/src/Routex/Request/HttpRequest.php');
	include('vendor/angelor/Routex/src/Routex/Route/RouteException.php');
	include('vendor/angelor/Routex/src/Routex/Route/Path.php');
	include('vendor/angelor/Routex/src/Routex/Routex.php');
	include('vendor/angelor/Routex/src/Routex/Route.php');</code></pre>

#### Composer
- `require: "angelor/routex": "dev-master"`

## Getting Started
The first thing to do is `cp config.sample.php` from `/vendor/angelor/routex/` to the root of your application. 

<pre><code>use \Routex\Route;

$app = new \Routex\Routex();
$route = new Route($app->config('http.verbs'));

$route->get('/', function($request, $response){
	$response->text('Hello, World.');
});

$app->run($route);
</code></pre>

The line `use \Routex\Route;` is purely for convenience.

The next line simply gets an instance of our application. The core application simply handles the configuration options and executing the router. 

We then create an instance of the \Routex\Route object and pass it a list of HTTP Verbs that we support (as an array). In this case, I'm using the default http verbs as defined in our config.php file.

The next section defines a route that will respond to a `GET` request on the root of the document. If that occurs, it will call the callback (the anonymous function passed as the second argument). The callback is always passed `\Routex\Response\HttpResponse` and `\Routex\Request\HttpRequest` objects. 

Within the callback we set the response type to text and set the body content of the response to "Hello, World." 

Finally we call `$app->run($route)` which handles the route matching.


## Routing
Routing is essentially defining an HTTPVerb (GET, POST, PUT, DELETE etc.), a URI endpoint (/, /users, /users/23) and a callback that will be executed when a URI is matched. 

#### Direct Match
Direct matches look for an exact match of the URI to a route. In this case, the callback will only be executed when the URI is /users 
<pre><code>$route->get('/users', function($req, $res){
	
});
</code></pre>

#### Variable Match
Variable matches allow us to set named parameters in our URI. In this case, the callback will only be executed when the URI is /users/anything. In the `HttpRequest` object that is passed to the callback, you can access the value of the variable `$req->param('id')`. 
<pre><code>$route->get('/users/:id', function($req, $res){
	
});
</code></pre>

#### Wildcard Match 
Wildcard paramters are the same as variable matches, except that the values provided MAY contain a `/`. In this case, the callback will only be excuted wehn the URI is `/users/what/are/you/doing`. `$req->param(0)` will contain the value `what/are/you/doing`. 

<pre><code>$route->get('/users/*', function($req, $res){

});
</code></pre>

What is important to note is that routes are NOT matched in the order they are declarted. Instead they are matched in order of decreasing complexity. This means, that if you have a route that defines `/path/to/my/route`, `/path/:to/my` and `/path/*` the system will first try and match `/path/to/my/route` before trying to match `/path/:to/my` and finally `/path/*`.

#### Regex Match
Regex matches allow us to fine tune our URI matches. In this case, the calback will only be excuted when the URI is /users/12 (where 12 is any number). However, it will not match /users/angelor.

<pre><code>$route->get('/users/(\d+)', function($req, $res){

});
</code></pre>


## Callbacks
Callbacks can be anything that is of the [callable type](http://ca3.php.net/manual/en/language.types.callable.php). A callback will always receive **THREE** parameters, but most times only the first two will be required. In order passed, they are: 

#### HttpRequest
This holds information about the actual request. Headers, URI and Verb (GET/POST/PUT etc) are all part of this object. Depending on needs, additional information might be available to users about the request.

#### HttpResponse
This holds information about YOUR response back to the request. It sets up some defaults (status code and headers) but is completely customizable and allows you to add/modify headers as well as configure the response type through some pre-created methods.

#### Routex
As well as the Request/Response you also receive a copy of the application. This is mainly used for adding new routes on the fly or access configuration vars from within your callback.


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

## Special Thanks
- [Limonade-php](https://github.com/sofadesign/limonade)
- [Composer](http://getcomposer.org/)
- [Packagist](https://packagist.org)
- GitHub