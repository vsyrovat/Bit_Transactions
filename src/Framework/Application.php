<?php

declare(strict_types=1);

namespace Framework;

use Framework\PHP\UploadMaxDetector;
use Pimple\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Application extends Container implements HttpKernelInterface
{
    public function __construct(array $values = [])
    {
        parent::__construct();

        $this['debug'] = false;
        $this['charset'] = 'UTF-8';

        $this['routes'] = function ($app) {
            return new RouteCollection();
        };

        $this['url_generator'] = function ($app) {
            return new UrlGenerator($app['routes'], new RequestContext());
        };

        $this['session'] = function ($app) {
            return new Session();
        };

        $this['upload_max_size'] = min(
            UploadMaxDetector::bytesExtract(ini_get('upload_max_filesize')),
            UploadMaxDetector::bytesExtract(ini_get('post_max_size')),
            UploadMaxDetector::bytesExtract(ini_get('memory_limit'))
        );

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this['routes'], $context);

        $this['url_generator']->setContext($context);

        try {
            $attributes = $matcher->match($request->getPathInfo());
            $controller = $attributes['controller'];
            unset($attributes['controller'], $attributes['_route']);
            $response = $controller($request, $this, $attributes);
            if (!$response instanceof Response) {
                if (is_scalar($response) || is_null($response)) {
                    $response = new Response($response);
                } else {
                    throw new \InvalidArgumentException(
                        'response should be string or instance of Response, '
                        .gettype($response). ' returned in '.$controller
                    );
                }
            }
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not found!', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    public function run(Request $request = null)
    {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        $response = $this->handle($request);
        $response->send();
    }

    public function map($path, $controller, $name)
    {
        $this['routes']->add($name, new Route($path, ['controller' => $controller]));
    }

    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }
}
