<?php
namespace App\Middlewares;

use App\Framework\DI;
use App\Framework\Middleware;
use App\Models\User;

class AuthMiddleware extends Middleware {

    public function handle(string $uri, callable $next) {
        session_start();
        if (isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
            $user = User::findFirst([
                'id' => $_SESSION['userId']
            ]);
            if ($user) {
                DI::getInstance()->get('requestContext')->setVariable('user', $user);
                $next();
                return;
            }
        }
        if ($uri === '/') {
            $next();
        } else {
            header("Location: " . DI::getInstance()->get('uriResolver')->getPath('/login'));
        }
    }
}