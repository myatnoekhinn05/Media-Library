<?php

declare(strict_types=1);

namespace App\Exceptions;

class ValidationException extends BaseException
{
    protected int $statusCode = 422;

    public function __construct(array $errors)
    {
        parent::__construct("Validation failed", $errors);
    }
}
