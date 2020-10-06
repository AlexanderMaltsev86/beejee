<?php


namespace App\Framework;


use ReflectionMethod;

/**
 * MVC Router
 * @package App\Framework
 */
class Router {
    /**
     * @var Route[]
     */
    private $routes;

    /**
     * @var Route[]
     */
    private $errorRoutes;

    const ERROR_404 = '404';
    const ERROR_EXCEPTION = 'exception';

    /**
     * Add route for URI and assign controller, method and middleware
     * @param string $uri
     * @param string $controller
     * @param string $method
     * @param array $middlewares
     */
    public function addRoute(string $uri, string $controller, string $method, $middlewares = []) {
        $baseConfig = DI::getInstance()->get('baseConfig');
        $this->routes[$baseConfig['basePath'] . $uri] = new Route($uri, $controller, $method, $middlewares);
    }

    /**
     * Add handler for an error
     * @param string $error
     * @param string $controller
     * @param string $method
     */
    public function addErrorHandler(string $error, string $controller, string $method) {
        $this->errorRoutes[$error] = new Route(null, $controller, $method, null);
    }

    /**
     * Handle routes for URI
     * @param string $uri
     */
    public function handle(string $uri) {
        $uri = preg_replace('/\?.*/', '', $uri);

        //Exact match. Currently only this match type is supported.
        if (isset($this->routes[$uri])) {
            $this->handleRoute($this->routes[$uri], $uri);
            return;
        }

        //Nothing found -> 404 error
        http_response_code(404);
        if (isset($this->errorRoutes[self::ERROR_404])) {
            $this->handleRoute($this->errorRoutes[self::ERROR_404], $uri, null, self::ERROR_404);
        }
    }

    private function callRouteMethod(Route $route, ?array $params) {
        $controllerClass = $route->getController();
        $controller = new $controllerClass();

        $reflectionMethod = new ReflectionMethod($controllerClass, $route->getMethod());
        if (is_array($params)) {
            $view = $reflectionMethod->invokeArgs($controller, $params);
        } else {
            $view = $reflectionMethod->invoke($controller);
        }

        if ($view instanceof View) {
            $view->render();
        }
    }

    private function callMiddleware(string $middlewareClass, string $uri, callable $next) {
        $middleware = new $middlewareClass();
        $middleware->handle($uri, $next);
    }

    private function handleRoute(Route $route, string $uri, ?array $params=null, ?string $error=null) {
        try {
            if (is_array($route->getMiddlewares()) && count($route->getMiddlewares()) > 0) { //We need to call middlewares before controller
                $ind = 0;
                $next = function () use ($route, $uri, $params, &$ind, &$next) {
                    if (count($route->getMiddlewares()) - 1 <= $ind) {
                        $this->callRouteMethod($route, $params);
                    } else {
                        $this->callMiddleware($route->getMiddlewares()[$ind], $uri, $next);
                        $ind++;
                    }
                };
                $this->callMiddleware($route->getMiddlewares()[0], $uri, $next);
            } else { //Direct call (without middleware)
                $this->callRouteMethod($route, $params);
            }
        } catch (\Throwable $exception) {
            if (!$error) { //To prevent infinite recursion
                http_response_code(500);
                if (isset($this->errorRoutes[self::ERROR_EXCEPTION])) {
                    $this->handleRoute($this->errorRoutes[self::ERROR_EXCEPTION], $uri, [
                        'exception' => $exception
                    ], self::ERROR_EXCEPTION);
                }
            }
        }
    }
}