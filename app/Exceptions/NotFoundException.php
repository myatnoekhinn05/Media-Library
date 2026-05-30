<?php

declare(strict_types=1);

namespace App\Exceptions;

class NotFoundException extends BaseException
{
    protected int $statusCode = 404;

    public function __construct(string $message = "Resource not found")
    {
        parent::__construct($message);
    }
}
