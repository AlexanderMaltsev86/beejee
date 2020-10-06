<?php


namespace App\Framework;

/**
 * Dependency injection implementation. Works as singleton.
 * @package App\Framework
 */
class DI {
    /**
     * @var DI
     */
    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DI();
        }
        return self::$instance;
    }

    /**
     * @var array
     */
    private $container;
    private function __construct() {}

    public function get(string $service) {
        if (!isset($this->container[$service])) {
            return null;
        }
        return $this->container[$service];
    }

    public function set(string $service, callable $callback) {
        $value = $callback();
        $this->container[$service] = $value;
    }
}