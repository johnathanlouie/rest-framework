<?php

namespace Lwd\RestFramework;

use Lwd\RestFramework\HttpExceptions\BaseHttpException;
use Lwd\RestFramework\HttpRequest;
use Exception;
use Error;
use Lwd\RestFramework\Exceptions\RouteNotFound;
use Throwable;

/**
 * Main interface for the framework.
 * Add routes and then execute to run the app.
 */
final class Api
{
    /** @var Route[][] Map of HTTP methods to list of routes. */
    private $routes = [
        HttpMethods::GET => [],
        HttpMethods::HEAD => [],
        HttpMethods::POST => [],
        HttpMethods::PUT => [],
        HttpMethods::DELETE => [],
        HttpMethods::CONNECT => [],
        HttpMethods::OPTIONS => [],
        HttpMethods::TRACE => [],
        HttpMethods::PATCH => [],
    ];

    /** @var string Base path. */
    private $basePath;

    /**
     * Constructs the API.
     * 
     * @param string $basePath Base path.
     */
    public function __construct($basePath = '/')
    {
        $this->basePath = $basePath;
    }

    /**
     * Add route.
     * 
     * @param string $method
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    private function addRoute($method, $path, $controller)
    {
        $this->routes[$method][] = new Route($path, $controller);
    }

    /**
     * Add GET route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function get($path, $controller)
    {
        $this->addRoute(HttpMethods::GET, $path, $controller);
    }

    /**
     * Add HEAD route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function head($path, $controller)
    {
        $this->addRoute(HttpMethods::HEAD, $path, $controller);
    }

    /**
     * Add POST route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function post($path, $controller)
    {
        $this->addRoute(HttpMethods::POST, $path, $controller);
    }

    /**
     * Add PUT route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function put($path, $controller)
    {
        $this->addRoute(HttpMethods::PUT, $path, $controller);
    }

    /**
     * Add DELETE route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function delete($path, $controller)
    {
        $this->addRoute(HttpMethods::DELETE, $path, $controller);
    }

    /**
     * Add CONNECT route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function connect($path, $controller)
    {
        $this->addRoute(HttpMethods::CONNECT, $path, $controller);
    }

    /**
     * Add OPTIONS route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function options($path, $controller)
    {
        $this->addRoute(HttpMethods::OPTIONS, $path, $controller);
    }

    /**
     * Add TRACE route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function trace($path, $controller)
    {
        $this->addRoute(HttpMethods::TRACE, $path, $controller);
    }

    /**
     * Add PATCH route.
     * 
     * @param string $path
     * @param Controller $controller
     * @return void
     */
    public function patch($path, $controller)
    {
        $this->addRoute(HttpMethods::PATCH, $path, $controller);
    }

    /**
     * Find a matching route for the HTTP request path.
     * 
     * @return Route Matching route.
     * @throws RouteNotFound If route not found.
     */
    private function findRoute($httpRequest)
    {
        $requestPath = $httpRequest->getPath()->getPath();
        foreach ($this->routes[$httpRequest->getHttpMethod()] as $route) {
            if ($route->matches($requestPath)) {
                return $route;
            }
        }
        throw new RouteNotFound($requestPath);
    }

    /**
     * Runs the app.
     * Main function.
     * 
     * @return void
     */
    public function execute()
    {
        try {
            try {
                // Put together the request object.
                $request = new HttpRequest($this->basePath);
                $route = $this->findRoute($request);
                $params = $route->params($request->getPath());
                $request->setRouteParams($params);

                $response = new HttpResponse();
                $controller = $route->getController();
                switch ($request->getHttpMethod()) {
                    case HttpMethods::GET:
                        $data = $controller->get($request);
                        break;
                    case HttpMethods::HEAD:
                        $data = $controller->head($request);
                        break;
                    case HttpMethods::POST:
                        $data = $controller->post($request);
                        break;
                    case HttpMethods::PUT:
                        $data = $controller->put($request);
                        break;
                    case HttpMethods::DELETE:
                        $data = $controller->delete($request);
                        break;
                    case HttpMethods::CONNECT:
                        $data = $controller->connect($request);
                        break;
                    case HttpMethods::OPTIONS:
                        $data = $controller->options($request);
                        break;
                    case HttpMethods::TRACE:
                        $data = $controller->trace($request);
                        break;
                    case HttpMethods::PATCH:
                        $data = $controller->patch($request);
                        break;
                    default:
                        throw new Exception('Unknown HTTP request method: ' . $request->getHttpMethod());
                }
                $response->sendJson($data);
            } catch (BaseHttpException $e) {
                $response = new HttpResponse();
                $response->sendJson([
                    'errorCode' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                ]);
            } catch (RouteNotFound $e) {
                $response = new HttpResponse();
                $response->sendJson([
                    'errorCode' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                ]);
            } catch (Exception $e) {
                $response = new HttpResponse();
                $response->sendJson([
                    'errorCode' => 0,
                    'errorMessage' => 'Unknown exception.',
                ]);
            } catch (Error $e) {
                $response = new HttpResponse();
                $response->sendJson([
                    'errorCode' => 0,
                    'errorMessage' => 'Unknown error.',
                ]);
            }
        } catch (Throwable $t) {
        }
    }
}
