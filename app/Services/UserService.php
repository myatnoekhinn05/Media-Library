<?php

declare(strict_types=1);

namespace App\Services;

use App\Validation\Validator;
use App\Interfaces\UserRepositoryInterface;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

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

    public function register(array $data): array
    {
        if (!$this->validator->validate($data, RegisterRequest::rules())) {
            return [
                'success' => false,
                'errors' => $this->validator->errors()
            ];
        }

        $existing = $this->userRepo->findByEmail($data['email']);

        if (!empty($existing)) {
            return [
                'success' => false,
                'errors' => ['email' => 'Email already exists']
            ];
        }

        $created = $this->userRepo->create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);

        return [
            'success' => $created
        ];
    }

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

        if (!password_verify($data['password'], $user['password'])) {
            return [
                'success' => false,
                'errors' => ['password' => 'Incorrect password']
            ];
        }

        return [
            'success' => true,
            'user' => [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ];
    }
}
