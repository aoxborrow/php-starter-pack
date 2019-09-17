<?php
use FastRoute\Dispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;
use Zend\Config\Factory as ConfigFactory;

// setup ROOT_DIR constant
define('ROOT_DIR', realpath(dirname(__DIR__)));

// init composer autoloader
require_once ROOT_DIR.'/vendor/autoload.php';

// register custom exception handler that shows appropriate error pages
set_exception_handler('exception_handler');

// setup Config object (zend-config)
$config = ConfigFactory::fromFile(ROOT_DIR.'/config.php', true);

// setup Twig templating
$loader = new FilesystemLoader(ROOT_DIR.'/templates');
$twig = new TwigEnvironment($loader, [
    // disable cache for local development
    // 'cache' => ROOT_DIR.'/tmp',
]);

// define routes and controller endpoints
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // examples of explicit controller routing
    $r->addRoute(['GET'], '/', ['Controllers\ExampleController']);
    $r->addRoute(['GET'], '/hello[/{name}]', ['Controllers\ExampleController', 'hello']);

    // automatic controller routing, e.g. /example/dog
    $r->addRoute(['GET', 'POST'], '/{controller}/', 'automatic_controller_routing');
    $r->addRoute(['GET', 'POST'], '/{controller}[/{method}]', 'automatic_controller_routing');
});

// create Request object and match against routes
$request = Request::createFromGlobals();
$route = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());
switch ($route[0]) {
    // could not match route, render 404 page
    case Dispatcher::NOT_FOUND:
        throw new NotFoundException;

    // successfully matched to route
    case Dispatcher::FOUND:
        // route handler callable and named params
        $routeHandler = $route[1];
        $routeParams = $route[2];

        // add named params as custom attributes of Request object
        // usage: $request->attributes->get('some_param');
        $request->attributes->add($routeParams);

        // build dependencies we'll pass to each handler
        $dependencies = [$request, new Response, $config, $twig];

        // class-based route handler (e.g. explicitly routed controller)
        if (is_array($routeHandler)) {
            $controllerClass = $routeHandler[0];
            $controllerMethod = empty($routeHandler[1]) ? 'index' : $routeHandler[1];

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
            throw new \Exception('Route handler did not return a valid Response object');
        }

        // we got a Response object back, send it to browser
        $response->prepare($request)->send();
        break;

    // error matching route, e.g METHOD_NOT_ALLOWED, render 400 invalid request
    default:
        throw new InvalidRequestException;
}

/**
 * Match URIs to controllers by naming convention
 * @param mixed ...$dependencies
 * @return Response
 * @throws NotFoundException
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
        throw new NotFoundException;
    }

    try {
        // if the class exists then let's validate access to the requested method
        $reflectClass = new ReflectionClass($controllerClass);
        $reflectMethod = $reflectClass->getMethod($controllerMethod);
        // don't allow access to private, abstract, or static methods
        if ($reflectClass->isAbstract() || !$reflectMethod->isPublic() || $reflectMethod->isAbstract() || $reflectMethod->isStatic()) {
            throw new InvalidRequestException('Attempt to access private, abstract, or static controller method');
        }
    } catch (Exception $e) {
        // log Reflection exception but render 404 not found
        error_log("Caught $e");
        throw new NotFoundException;
    }

    // controller class exists and method is accessible!
    // instantiate controller and execute method endpoint
    $controller = new $controllerClass(...$dependencies);
    $response = $controller->$controllerMethod();
    return $response;
}

// custom exceptions to display appropriate error pages
class InvalidRequestException extends \Exception {}
class UnauthorizedException extends \Exception {}
class AccessDeniedException extends \Exception {}
class NotFoundException extends \Exception {}

// custom exception handler to display appropriate error pages
function exception_handler(Throwable $e) {

    // don't log our custom exceptions, just show error page
    if ($e instanceof InvalidRequestException) {

        // render 400 Invalid Request error page
        render_error_page(400, 'Invalid Request');

    } elseif ($e instanceof UnauthorizedException) {

        // render 401 Unauthorized error page
        render_error_page(401, 'Unauthorized');

    } elseif ($e instanceof AccessDeniedException) {

        // render 403 Access Denied error page
        render_error_page(403, 'Access Denied');

    } elseif ($e instanceof NotFoundException) {

        // render 404 Not Found error page
        render_error_page(404, 'Not Found');

    } else {

        // for all other exceptions, show 500 error page
        render_error_page(500, 'Internal Server Error');

        // log full exception w/stack trace
        error_log("Caught $e");
    }

    // kill further execution
    die;
}

// render a basic error page, can extend this to use HTML template
function render_error_page($status_code, $msg) {
    http_response_code($status_code);
    echo "$status_code - $msg";
}
