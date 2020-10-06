<?php


namespace App\Framework;


class Route {
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $method;

    /**
     * @var Middleware[]
     */
    private $middlewares;

    /**
     * Route constructor.
     * @param string $uri
     * @param string $controller
     * @param string|null $method
     * @param string[] $middlewares
     */
    public function __construct(?string $uri, string $controller, ?string $method, ?array $middlewares=null) {
        $this->uri = $uri;
        $this->controller = $controller;
        $this->method = $method;
        $this->middlewares = $middlewares;
    }

    /**
     * @return string
     */
    public function getUri(): ?string {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getController(): string {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getMethod(): ?string {
        return $this->method;
    }

    /**
     * @return string[]
     */
    public function getMiddlewares(): ?array {
        return $this->middlewares;
    }
}