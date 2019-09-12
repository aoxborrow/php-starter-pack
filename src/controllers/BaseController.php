<?php
namespace Controllers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class BaseController {

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var Environment
     */
    public $twig;

    /**
     * BaseController constructor
     * @param Request $request
     * @param Response $response
     * @param Environment $twig
     */
    public function __construct(Request $request, Response $response, Environment $twig) {
        $this->request = $request;
        $this->response = $response;
        $this->twig = $twig;
    }

    /**
     * Shortcut to render a Twig template and return Response
     * @param string $template
     * @param array $context
     * @return Response
     */
    protected function render(string $template, array $context = array()) {
        try {
            // render Twig template with optional context
            $this->response->setContent($this->twig->render($template, $context));
        } catch (\Exception $e) {
            // log Twig exceptions and show generic server error
            error_log("Caught $e");
            $this->response->setContent('500 - Internal Server Error');
            $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->response;
    }
}
