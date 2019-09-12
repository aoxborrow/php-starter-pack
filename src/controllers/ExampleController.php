<?php
namespace Controllers;
use Symfony\Component\HttpFoundation\Response;

class ExampleController extends BaseController {

    // example of automatic controller routing
    // http://pmav.local/example
    public function index() {
        return Response::create("This ExampleController was routed automatically. Conventions are neat!");
    }

    // you can find this dog because it's public
    // http://pmav.local/example/dog
    public function dog() {
        return Response::create("You found the dog!");
    }

    // you can't find this cat because it's protected
    // http://pmav.local/example/cat
    protected function cat() {
        return Response::create("Can't find this cat, sorry.");
    }

    // you can't find this cow because it's static
    // http://pmav.local/example/cow
    public static function cow() {
        return Response::create("Can't find this cow, sorry.");
    }
}
