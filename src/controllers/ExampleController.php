<?php
namespace Controllers;

class ExampleController extends BaseController {

    // render hello world homepage
    // http://php-starter-pack.local/
    public function index() {
        return $this->hello();
    }

    // example of explicitly routed controller method
    // http://php-starter-pack.local/hello/chrysanthemum
    public function hello() {
        $context = [
            // get named param from Request object
            'name' => $this->request->attributes->get('name', 'world'),
        ];
        return $this->render('hello.html.twig', $context);
    }

    // example of automatically routed public method, and querying the database
    // http://php-starter-pack.local/example/results
    public function results() {
        // you can use "queryAll" shortcut for SELECT statements
        $results = $this->queryAll("SELECT * FROM example_data ORDER BY created DESC");
        return $this->render('results.html.twig', ['results' => $results]);
    }

    // the controller router will not match protected or static methods
    // http://php-starter-pack.local/example/protected_example
    protected function protected_example() {
        $this->response->setContent("Can't find this protected method, sorry.");
        return $this->response;
    }
}
