<?php
namespace Controllers;

class ExampleController extends BaseController {

    // render hello world homepage
    // http://pmav.local/
    public function index() {
        return $this->hello();
    }

    // example of explicitly routed controller method
    // http://pmav.local/hello/dave
    public function hello() {
        $context = [
            // get named param from Request object
            'name' => $this->request->attributes->get('name', 'world'),
        ];
        return $this->render('hello.html.twig', $context);
    }

    // example of automatically routed method
    // you can find this dog() because it's public
    // http://pmav.local/example/dog
    public function dog() {
        return $this->render('hello.html.twig', ['name' => 'dog']);
    }

    // example of querying database
    // http://pmav.local/example/results
    public function results() {
        // you can use "queryAll" shortcut for SELECT statements
        $results = $this->queryAll("SELECT * FROM example_data ORDER BY created DESC");
        return $this->render('results.html.twig', ['results' => $results]);
    }

    // you can't find this cat() because it's protected
    // http://pmav.local/example/cat
    protected function cat() {
        echo "Can't find this cat, sorry.";

    }

    // you can't find this cow() because it's static
    // http://pmav.local/example/cow
    public static function cow() {
        echo "Can't find this cow, sorry.";
    }
}
