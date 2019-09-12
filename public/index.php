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

// define routes and controller endpoints
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '/', ['Controllers\HomeController', 'hello']);
    $r->addRoute(['GET', 'POST'], '/hello/{name}', ['Controllers\HomeController', 'hello']);
    // automatic controller routing -- remove these routes to use explicit routing only
    $r->addRoute(['GET', 'POST'], '/{controller}/', 'automatic_controller_routing');
    $r->addRoute(['GET', 'POST'], '/{controller}[/{method}]', 'automatic_controller_routing');
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
        // route handler callable and named params
        $routeHandler = $route[1];
        $routeParams = $route[2];

        // add named params as custom attributes of Request object
        // usage: $request->attributes->get('some_param');
        $request->attributes->add($routeParams);

        // build dependencies we'll pass to each handler
        $dependencies = [$request, new Response, $twig];

        // class-based route handler (e.g. explicitly routed controller)
        if (is_array($routeHandler)) {
            $controllerClass = $routeHandler[0];
            $controllerMethod = $routeHandler[1];

            // instantiate controller and execute method endpoint
            // using manual dependency injection for now
            $controller = new $controllerClass(...$dependencies);
            $response = $controller->$controllerMethod();

        } else {

            // call function-based route handler and pass dependencies
            $response = call_user_func_array($routeHandler, $dependencies);
        }

        // we didn't get a Response object back, throw 500 error -- we want to enforce convention
        if (!$response instanceof Response) {
            Response::create('500 - Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR)
                ->prepare($request)
                ->send();
            break;
        }

        // we got a Response object back, send it to browser
        $response->prepare($request)->send();
        break;

    // error matching route, e.g METHOD_NOT_ALLOWED, render 400 invalid request
    default:
        Response::create('400 – Invalid Request', Response::HTTP_BAD_REQUEST)
            ->prepare($request)
            ->send();
        return;
}

/**
 * Match URIs to controllers by naming convention
 * @param mixed ...$dependencies
 * @return Response
 */
function automatic_controller_routing(...$dependencies) {
    $request = $dependencies[0];

    // custom attributes are added to Request for named params
    $controller = $request->attributes->get('controller');
    $controllerClass = 'Controllers\\'.ucfirst($controller).'Controller';

    // default to index() method if not supplied
    $method = $request->attributes->get('method');
    $controllerMethod = empty($method) ? 'index' : $method;

    // if controller class doesn't exist, just render a basic 404 not found
    if (!class_exists($controllerClass)) {
        return Response::create('404 – Page Not Found', Response::HTTP_NOT_FOUND);
    }

    try {
        // if the class exists then let's validate access to the requested method
        $reflectClass = new ReflectionClass($controllerClass);
        $reflectMethod = $reflectClass->getMethod($controllerMethod);
        // don't allow access to private, abstract, or static methods
        if ($reflectClass->isAbstract() || !$reflectMethod->isPublic() || $reflectMethod->isAbstract() || $reflectMethod->isStatic()) {
            throw new \Exception('Attempt to access private, abstract, or static controller method');
        }
    } catch (\Exception $e) {
        // log exception and render 400 invalid request if attempting to access private, abstract, static method
        error_log("Caught $e");
        return Response::create('400 – Invalid Request', Response::HTTP_BAD_REQUEST);
    }

    // controller class exists and method is accessible!
    // instantiate controller and execute method endpoint
    $controller = new $controllerClass(...$dependencies);
    $response = $controller->$controllerMethod();
    return $response;
}
