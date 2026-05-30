<?php

declare(strict_types=1);

namespace App\Controllers;

class BaseController
{
    /*
    |--------------------------------------------------------------------------
    | REDIRECT HELPER
    |--------------------------------------------------------------------------
    */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | FLASH ERROR + OLD INPUT
    |--------------------------------------------------------------------------
    */
    protected function withErrors(array $errors, array $old, string $url): void
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;

        $this->redirect($url);
    }

    /*
    |--------------------------------------------------------------------------
    | FLASH SUCCESS MESSAGE
    |--------------------------------------------------------------------------
    */
    protected function withSuccess(string $message, string $url): void
    {
        $_SESSION['success'] = $message;

        $this->redirect($url);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH SESSION SET
    |--------------------------------------------------------------------------
    */
    protected function setAuthSession(array $user): void
    {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
    }

    /*
    |--------------------------------------------------------------------------
    | REQUIRE LOGIN (FIX FOR YOUR ERROR)
    |--------------------------------------------------------------------------
    */
    protected function requireLogin(): void
    {
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {

            $_SESSION['auth_error'] = 'Please login first';
            $this->redirect(BASE_URL . '/Public/index.php?page=login');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT SESSION
    |--------------------------------------------------------------------------
    */
    protected function logoutSession(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
