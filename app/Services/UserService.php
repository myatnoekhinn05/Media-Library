<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Validation\Validator;
use App\Interfaces\UserRepositoryInterface;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class UserService
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

    public function register(array $data): array
    {
        if (!$this->validator->validate($data, RegisterRequest::rules())) {
            return [
                'success' => false,
                'errors' => $this->validator->errors()
            ];
        }

        $existingUser = $this->userRepo->findByEmail($data['email']);

        if (!empty($existingUser)) {
            return [
                'success' => false,
                'errors' => ['email' => 'Email already exists']
            ];
        }

        $user = new User(
            $data['username'],
            $data['email']
        );

        $user->changePassword($data['password']);

        $created = $this->userRepo->create($user);

        return [
            'success' => $created
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(array $data): array
    {
        if (!$this->validator->validate($data, LoginRequest::rules())) {
            return [
                'success' => false,
                'errors' => $this->validator->errors()
            ];
        }

        $user = $this->userRepo->findByEmail($data['email']);

        if (empty($user)) {
            return [
                'success' => false,
                'errors' => ['email' => 'Email not found']
            ];
        }

        // IMPORTANT: array-based password check
        if (!password_verify($data['password'], $user['password'] ?? '')) {
            return [
                'success' => false,
                'errors' => ['password' => 'Incorrect password']
            ];
        }

        return [
            'success' => true,
            'user' => [
                'user_id' => $user['user_id'] ?? null,
                'username' => $user['username'] ?? null,
                'email' => $user['email'] ?? null
            ]
        ];
    }
}
