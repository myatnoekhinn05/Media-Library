<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use App\DTOs\UserDTO;
use App\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Mappers\UserMapper;
use App\Validation\Validator;

class UserService extends BaseService
{
    private UserRepositoryInterface $userRepo;
    private Validator $validator;

    public function __construct(
        UserRepositoryInterface $userRepo,
        Validator $validator
    ) {
        $this->userRepo = $userRepo;
        $this->validator = $validator;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */
    public function register(RegisterUserDTO $dto): void
    {
        $data = [
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => $dto->password,
            'confirm_password' => $dto->confirmPassword
        ];

        if (!$this->validator->validate($data, RegisterRequest::rules())) {
            throw new ValidationException(
                $this->validator->errors()
            );
        }

        if ($this->userRepo->findByEmail($dto->email)) {
            throw new ValidationException([
                'email' => 'Email already exists'
            ]);
        }

        if ($dto->password !== $dto->confirmPassword) {
            throw new ValidationException([
                'confirm_password' => 'Passwords do not match'
            ]);
        }

        $created = $this->userRepo->create([
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => password_hash(
                $dto->password,
                PASSWORD_BCRYPT
            )
        ]);

        if (!$created) {
            throw new DatabaseException(
                'Registration failed'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(LoginUserDTO $dto): array
    {
        $data = [
            'email' => $dto->email,
            'password' => $dto->password
        ];

        if (
            !$this->validator->validate(
                $data,
                LoginRequest::rules()
            )
        ) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $this->validator->errors()
            ];
        }

        $user = $this->userRepo->findByEmail(
            $dto->email
        );

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found',
                'data' => null,
                'errors' => [
                    'email' => 'Email not found'
                ]
            ];
        }

        if (
            !password_verify(
                $dto->password,
                $user['password']
            )
        ) {
            return [
                'success' => false,
                'message' => 'Incorrect password',
                'data' => null,
                'errors' => [
                    'password' => 'Incorrect password'
                ]
            ];
        }

        return [
            'success' => true,
            'message' => 'Login successful',
            'data' => $user,
            'errors' => []
        ];
    }
}
