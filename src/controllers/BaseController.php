<?php
namespace Controllers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Config\Config;
use Twig\Environment as TwigEnvironment;

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
     * @var Config
     */
    public $config;

    /**
     * @var TwigEnvironment
     */
    public $twig;

    /**
     * BaseController constructor
     * @param Request $request
     * @param Response $response
     * @param Config $config
     * @param TwigEnvironment $twig
     */
    public function __construct(Request $request, Response $response, Config $config, TwigEnvironment $twig) {
        $this->request = $request;
        $this->response = $response;
        $this->config = $config;
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
