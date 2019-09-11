<?php
namespace PMAV\Controllers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController {
    // render hello world page
    public function hello(Request $request): Response {
        // get route params from Request object
        $name = $request->attributes->get('name', 'world');
        // simplistic PHP template handling -- don't actually do this
        ob_start();
        require ROOT_DIR.'/templates/home.php';
        $html = ob_get_contents();
        ob_end_clean();
        $response = new Response($html);
        return $response;
    }
}
