<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Mappers\UserMapper;
use App\Services\UserService;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER PAGE
    |--------------------------------------------------------------------------
    */
    public function showRegister(): void
    {
        require BASE_PATH . '/view/Auth/register.php';
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */
    public function register(): void
    {
        try {
            $dto = UserMapper::arrayToRegisterDTO($_POST);

            $this->userService->register($dto);

            $_SESSION['success'] = 'Registration successful';
            $this->redirect(BASE_URL . '/Public/index.php?page=login');
        } catch (ValidationException $e) {

            $_SESSION['errors'] = $e->getErrors();
            $_SESSION['old'] = [
                'username' => $_POST['username'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];

            $this->redirect(BASE_URL . '/Public/index.php?page=register');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN PAGE
    |--------------------------------------------------------------------------
    */
    public function showLogin(): void
    {
        require BASE_PATH . '/view/Auth/login.php';
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(): void
    {
        $dto = UserMapper::arrayToLoginDTO($_POST);

        $response = $this->userService->login($dto);

        if (!$response['success']) {

            $_SESSION['errors'] = $response['errors'] ?? [];
            $_SESSION['old'] = ['email' => $_POST['email'] ?? ''];
            $_SESSION['error'] = $response['message'];

            $this->redirect(BASE_URL . '/Public/index.php?page=login');
        }

        $user = $response['data'];

        $this->setAuthSession($user);

        session_regenerate_id(true);

        $_SESSION['success'] = $response['message'];

        $this->redirect(BASE_URL . '/Public/index.php?page=home');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        session_start();

        $_SESSION['success'] = 'Logged out successfully';

        $this->redirect(BASE_URL . '/Public/index.php?page=login');
    }
}
