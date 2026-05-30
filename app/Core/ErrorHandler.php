<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;
use ErrorException;
use App\Exceptions\BaseException;

class ErrorHandler
{
    /*
    |--------------------------------------------------------------------------
    | REGISTER HANDLERS
    |--------------------------------------------------------------------------
    */
    public static function register(): void
    {
        set_exception_handler(
            [self::class, 'handleException']
        );

        set_error_handler(
            [self::class, 'handleError']
        );

        register_shutdown_function(
            [self::class, 'handleShutdown']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL EXCEPTION HANDLER
    |--------------------------------------------------------------------------
    */
    public static function handleException(
        Throwable $e
    ): void {

        // LOG EVERYTHING
        error_log(
            $e->getMessage() . PHP_EOL .
                $e->getTraceAsString()
        );

        // CUSTOM APP EXCEPTIONS
        if ($e instanceof BaseException) {

            self::renderError(
                $e->getStatusCode(),
                $e->getMessage(),
                $e->getErrors()
            );

            return;
        }

        // UNKNOWN SYSTEM EXCEPTION
        self::renderError(
            500,
            'System Error',
            ['Something went wrong. Please try again later.']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PHP ERROR HANDLER
    |--------------------------------------------------------------------------
    */
    public static function handleError(
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {

        throw new ErrorException(
            $message,
            0,
            $severity,
            $file,
            $line
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FATAL ERROR HANDLER
    |--------------------------------------------------------------------------
    */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        $fatalErrors = [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_PARSE
        ];

        if (!in_array($error['type'], $fatalErrors, true)) {
            return;
        }

        error_log(json_encode($error));

        self::renderError(
            500,
            'Fatal Error',
            ['A critical system error occurred.']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER ERROR PAGE
    |--------------------------------------------------------------------------
    */
    private static function renderError(
        int $statusCode,
        string $title,
        array $errors = []
    ): void {

        http_response_code($statusCode);

        $message = match ($statusCode) {

            404 => 'The requested resource was not found.',

            401 => 'You are not authorized to access this page.',

            422 => 'Please correct the highlighted errors.',

            default => 'Something went wrong. Please try again later.'
        };

        $errorList = $errors;

        require BASE_PATH . '/view/errors/error.php';

        exit;
    }
}
