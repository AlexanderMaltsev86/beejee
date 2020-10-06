<?php


namespace App\Controllers;


use App\Framework\View;
use Throwable;

class ErrorController extends BaseController {
    public function notFoundAction() {
        return new View('errors/404');
    }

    public function exceptionAction(Throwable $exception) {
        return new View('errors/exception', [
            'exceptionClass' => get_class($exception),
            'message' => $exception->getMessage(),
            'stackTrace' => $exception->getTraceAsString(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}