<?php
namespace App;

use App\Controllers\ErrorController;
use App\Controllers\LoginController;
use App\Controllers\TasksController;
use App\Framework\DI;
use App\Framework\RequestContext;
use App\Framework\Router;
use App\Framework\UriResolver;
use App\Middlewares\AuthMiddleware;

/**
 * Application entry point. Should be called in index.php file.
 * @package App
 */
class Application {
    public function handle() {
        $di = DI::getInstance();
        $di->set('baseConfig', function () {
            return require __DIR__ . '/../config/base.php';
        });
        $di->set('dbConfig', function () {
            return require __DIR__ . '/../config/database.php';
        });
        $di->set('uriResolver', function () use ($di) {
            return new UriResolver($di->get('baseConfig')['basePath']);
        });
        $di->set('requestContext', function () {
            return new RequestContext();
        });

        $router = new Router();

        $router->addErrorHandler(Router::ERROR_404, ErrorController::class, 'notFoundAction');
        $router->addErrorHandler(Router::ERROR_EXCEPTION, ErrorController::class, 'exceptionAction');

        $router->addRoute('/', TasksController::class, 'indexAction', [
            AuthMiddleware::class
        ]);
        $router->addRoute('/tasks/form', TasksController::class, 'formAction', [
            AuthMiddleware::class
        ]);
        $router->addRoute('/tasks/submit', TasksController::class, 'submitAction', [
            AuthMiddleware::class
        ]);
        $router->addRoute('/tasks/delete', TasksController::class, 'deleteAction', [
            AuthMiddleware::class
        ]);
        $router->addRoute('/login', LoginController::class, 'indexAction');
        $router->addRoute('/login/submit', LoginController::class, 'submitAction');
        $router->addRoute('/logout', LoginController::class, 'logoutAction');

        $router->handle($_SERVER['REQUEST_URI']);
    }
}