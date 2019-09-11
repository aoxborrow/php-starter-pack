<?php
use FastRoute\Dispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// setup ROOT_DIR constant
define('ROOT_DIR', dirname(__DIR__));

// init composer autoloader
require_once ROOT_DIR.'/vendor/autoload.php';

// setup Twig templating
$loader = new FilesystemLoader(ROOT_DIR.'/templates');
$twig = new Environment($loader, [
    // disable cache for local development
    // 'cache' => ROOT_DIR.'/tmp',
]);

// TODO: automatically route to Controllers and public methods?
// define routes and controller endpoints
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '/', ['PMAV\Controllers\HomeController', 'hello']);
    $r->addRoute(['GET', 'POST'], '/hello/{name}', ['PMAV\Controllers\HomeController', 'hello']);
});

// create Request object and match against routes
$request = Request::createFromGlobals();
$route = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());
switch ($route[0]) {
    // could not match route, render 404 page
    case Dispatcher::NOT_FOUND:
        Response::create('404 – Page Not Found', Response::HTTP_NOT_FOUND)
            ->prepare($request)
            ->send();
        break;

    // successfully matched to route
    case Dispatcher::FOUND:
        // get controller details from route
        $controllerClass = $route[1][0];
        $controllerMethod = $route[1][1];
        $routeParams = $route[2];

        // add route params as custom attributes of Request object
        // usage: $request->attributues->get('some_param');
        $request->attributes->add($routeParams);

        // instantiate controller and execute method endpoint
        // using manual dependency injection for now
        $controller = new $controllerClass($request, new Response, $twig);
        $response = $controller->$controllerMethod();

        // if we got a Response object back, render it
        if ($response instanceof Response) {
            $response->prepare($request)->send();
        // otherwise just try to output the response
        } else {
            echo $response;
        }

        break;

    // error matching route, e.g METHOD_NOT_ALLOWED, render 400 invalid request
    default:
        Response::create('400 – Invalid Request', Response::HTTP_BAD_REQUEST)
            ->prepare($request)
            ->send();
        return;
}
