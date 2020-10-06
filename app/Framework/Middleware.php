<?php


namespace App\Framework;


/**
 * Base class for all middlewares
 * @package App\Framework
 */
abstract class Middleware {
    /**
     * The method is called for each middleware. Call $next() method to go to a next middleware or controller
     * @param string $uri
     * @param callable $next
     */
    public abstract function handle(string $uri, callable $next);
}