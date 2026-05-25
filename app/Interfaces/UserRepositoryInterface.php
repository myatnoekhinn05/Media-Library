<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface extends BaseInterface
{
    public function create(User $user): bool;
    public function findByEmail(string $email): ?User;
}
