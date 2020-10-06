<?php


namespace App\Controllers;


use App\Framework\DI;

class BaseController {
    protected function getRequestParam(string $param) {
        if (!isset($_REQUEST[$param])) {
            return null;
        }
        return $_REQUEST[$param];
    }

    protected function redirect(string $uri) {
        header("Location: " . DI::getInstance()->get('uriResolver')->getPath($uri));
    }
}