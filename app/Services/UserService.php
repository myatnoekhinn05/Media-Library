<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserService
{
    private UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /** Register new user */
    public function register(string $username, string $email, string $hashedPassword): bool
    {
        // Check if email already exists
        if ($this->userRepo->findByEmail($email)) {
            return false;
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = $hashedPassword;

        return $this->userRepo->create($user);
    }

    /** Login user */
    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepo->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }
}
