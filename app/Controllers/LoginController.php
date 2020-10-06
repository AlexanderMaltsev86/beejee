<?php


namespace App\Controllers;


use App\Framework\View;
use App\Models\User;

class LoginController extends BaseController {
    public function indexAction() {
        return new View('login', [], false);
    }

    public function submitAction() {
        if ($this->getRequestParam('login') && $this->getRequestParam('password')) {
            $user = User::findFirst([
                'login' => $this->getRequestParam('login')
            ]);
            if ($user) {
                $passwordHash = hash('sha256', $this->getRequestParam('password'));
                if ($passwordHash === $user->password_hash) { //Successful login
                    session_start();
                    $_SESSION['userId'] = $user->id;
                    $this->redirect('/');
                    return null;
                }
            }
        }
        return new View('login', [
            'authError' => true
        ], false);
    }

    public function logoutAction() {
        session_start();
        unset($_SESSION['userId']);
        $this->redirect('/');
        return null;
    }
}