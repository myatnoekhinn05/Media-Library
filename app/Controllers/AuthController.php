<?php

namespace App\Controllers;

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
    | SHOW REGISTER FORM
    |--------------------------------------------------------------------------
    */
    public function showRegister(): void
    {
        $pageTitle = 'Register';
        $section = 'register';
        $hideSearch = true;

        require BASE_PATH . '/view/auth/register.php';
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE REGISTER
    |--------------------------------------------------------------------------
    */
    public function register(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$username || !$email || !$password) {
            $_SESSION['error'] = "All fields are required!";
            header("Location: index.php?page=register");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $success = $this->userService->register(
            $username,
            $email,
            $hashedPassword
        );

        if ($success) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: index.php?page=login");
            exit;
        }

        $_SESSION['error'] = "Email already exists!";
        header("Location: index.php?page=register");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW LOGIN FORM
    |--------------------------------------------------------------------------
    */
    public function showLogin(): void
    {
        $pageTitle = 'Login';
        $section = 'login';
        $hideSearch = true;

        require BASE_PATH . '/view/auth/login.php';
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$email || !$password) {
            $_SESSION['error'] = "Email and password are required!";
            header("Location: index.php?page=login");
            exit;
        }

        $user = $this->userService->login($email, $password);

        if ($user) {

            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['username'] = $user->username;

            header("Location: index.php?page=home");
            exit;
        }

        $_SESSION['error'] = "Invalid email or password!";
        header("Location: index.php?page=login");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT USER
    |--------------------------------------------------------------------------
    */
    public function logout(): void
    {
        // Clear session data
        $_SESSION = [];

        // Remove session cookie (safe logout)
        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        // Redirect to login page
        header("Location: index.php?page=login");
        exit;
    }
}
