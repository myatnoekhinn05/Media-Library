<?php

declare(strict_types=1);

namespace App\DTOs;

final class UserDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly string $username,
        public readonly string $email
    ) {}
}
