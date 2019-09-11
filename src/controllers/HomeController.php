<?php
namespace PMAV\Controllers;

class HomeController extends BaseController {

    // render hello world homepage
    public function hello() {
        // build template context
        $context = array(
            // get route param from Request object
            'name' => $this->request->attributes->get('name', 'world'),
        );
        return $this->render('home.html.twig', $context);
    }
}
