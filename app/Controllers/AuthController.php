<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;

class AuthController
{
    private UserService $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW REGISTER PAGE
    |--------------------------------------------------------------------------
    */

    public function showRegister(): void
    {
        require BASE_PATH . '/view/auth/register.php';
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER SUBMIT
    |--------------------------------------------------------------------------
    */

    public function register(): void
    {
        $data = [

            'username' => trim(
                $_POST['username'] ?? ''
            ),

            'email' => trim(
                $_POST['email'] ?? ''
            ),

            'password' => trim(
                $_POST['password'] ?? ''
            )
        ];

        $result = $this->userService
            ->register($data);

        /*
        |--------------------------------------------------------------------------
        | VALIDATION FAILED
        |--------------------------------------------------------------------------
        */

        if (!$result['success']) {

            $_SESSION['errors']
                = $result['errors'];

            $_SESSION['old']
                = $data;

            header(
                'Location: '
                    . BASE_URL
                    . '/Public/index.php?page=register'
            );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $_SESSION['success']
            = 'Registration successful';

        header(
            'Location: '
                . BASE_URL
                . '/Public/index.php?page=login'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW LOGIN PAGE
    |--------------------------------------------------------------------------
    */

    public function showLogin(): void
    {
        require BASE_PATH . '/view/auth/login.php';
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN SUBMIT
    |--------------------------------------------------------------------------
    */

    public function login(): void
    {
        $data = [

            'email' => trim(
                $_POST['email'] ?? ''
            ),

            'password' => trim(
                $_POST['password'] ?? ''
            )
        ];

        $result = $this->userService
            ->login($data);

        /*
        |--------------------------------------------------------------------------
        | LOGIN FAILED
        |--------------------------------------------------------------------------
        */

        if (!$result['success']) {

            $_SESSION['errors']
                = $result['errors'];

            $_SESSION['old']
                = $data;

            header(
                'Location: '
                    . BASE_URL
                    . '/Public/index.php?page=login'
            );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN SUCCESS
        |--------------------------------------------------------------------------
        */

        $user = $result['user'];

        /*
        |--------------------------------------------------------------------------
        | STORE SESSION
        |--------------------------------------------------------------------------
        */

        $_SESSION['user_id']
            = $user['user_id'];

        $_SESSION['username']
            = $user['username'];

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL:
        | LOGIN FLAG
        |--------------------------------------------------------------------------
        */

        $_SESSION['logged_in']
            = true;

        header(
            'Location: '
                . BASE_URL
                . '/Public/index.php?page=home'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CLEAR SESSION
        |--------------------------------------------------------------------------
        */

        $_SESSION = [];

        /*
        |--------------------------------------------------------------------------
        | DESTROY SESSION
        |--------------------------------------------------------------------------
        */

        session_destroy();

        header(
            'Location: '
                . BASE_URL
                . '/Public/index.php?page=login'
        );

        exit;
    }
}
