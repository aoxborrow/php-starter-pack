<?php
namespace PMAV;

class HomeController
{
    // render hello world page
    public function home(array $request = array()): string {
        // simplistic PHP template handling -- don't actually do this
        ob_start();
        require dirname(__DIR__).'/templates/home.php';
        $response = ob_get_contents();
        ob_end_clean();
        return $response;
    }
}
