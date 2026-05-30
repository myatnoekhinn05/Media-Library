<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\UserDTO;
use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;

final class UserMapper
{
    public static function arrayToRegisterDTO(array $data): RegisterUserDTO
    {
        return new RegisterUserDTO(
            username: trim((string)($data['username'] ?? '')),
            email: trim((string)($data['email'] ?? '')),
            password: (string)($data['password'] ?? ''),
            confirmPassword: (string)($data['confirm_password'] ?? '')
        );
    }

    public static function arrayToLoginDTO(array $data): LoginUserDTO
    {
        return new LoginUserDTO(
            email: trim((string)($data['email'] ?? '')),
            password: (string)($data['password'] ?? '')
        );
    }

    public static function databaseToDTO(array $row): UserDTO
    {
        return new UserDTO(
            user_id: isset($row['user_id'])
                ? (int)$row['user_id']
                : null,

            username: (string)($row['username'] ?? ''),

            email: (string)($row['email'] ?? ''),

            password: (string)($row['password'] ?? '')
        );
    }

    public static function dtoToArray(UserDTO $dto): array
    {
        return [
            'user_id' => $dto->user_id,
            'username' => $dto->username,
            'email' => $dto->email
        ];
    }
}