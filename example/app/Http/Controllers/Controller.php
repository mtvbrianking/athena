<?php

namespace App\Http\Controllers;

use Pimple\Container;
// use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\HtmlResponse;

class Controller
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        // throw new \Exception("Testing whoops error handler", 500, null);

        $name = $request->getQueryParams()['name'] ?? 'mysterious';

        return new HtmlResponse('Hello, ' . $name . ' from controller!', 200);
    }

    public function index()
    {
        // $this->container['logger']->debug('test', ['key' => 'value']);

        $html = $this->container['twig']->render('index.twig', [
            'name' => 'Athena',
        ]);

        // Render plain php template
        // $html = $this->render(__DIR__.'/../../../views/index.php', [
        //     'name' => 'Athena',
        // ]);

        return new HtmlResponse($html, 200);
    }

    /**
     * Render template.
     * @param  string $template
     * @param  array  $params
     * @return string html
     */
    private function render(string $template, array $params = []) : string
    {
        ob_start();
        // echo '<html><h1>You\'re at Controller@index!</h1></html>';
        extract($params, EXTR_OVERWRITE);
        require $template;
        return ob_get_clean();
    }

    // curl -X POST \
    // 'http://localhost:8000/edit/159753?qp=qp1' \
    // -H 'content-type: multipart/form-data;' \
    // -F 'bp=bp1'
    public function edit(ServerRequestInterface $request, $id)
    {
        return new JsonResponse([
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'headers' => $request->getHeaders(),
            'attributes' => $request->getAttributes(),
            'query_params' => $request->getQueryParams(),
            'parsed_body' => $request->getParsedBody(),
            'cookie_params' => $request->getCookieParams(),
            'server_params' => $request->getServerParams(),
        ]);
        return new HtmlResponse('Controller@edit,id', 200);
    }

}
