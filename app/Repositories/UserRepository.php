<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | GET ALL USERS
    |--------------------------------------------------------------------------
    */

    public function getAll(
        ?int $limit = 10,
        int $offset = 0
    ): array {

        $stmt = $this->db->prepare(
            "CALL sp_get_all_users(:limit, :offset)"
        );

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->mapUser($row);
        }

        $stmt->closeCursor();

        return $users;
    }

    /*
    |--------------------------------------------------------------------------
    | GET USER BY ID
    |--------------------------------------------------------------------------
    */

    public function getById(int $id): array
    {
        $stmt = $this->db->prepare(
            "CALL sp_get_user_by_id(:id)"
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        if (!$data) {
            return [];
        }

        return $this->mapUser($data);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE USER----------------------------------
    */


    public function create(User $user): bool
    {
        $stmt = $this->db->prepare(
            "CALL sp_create_user(:username, :email, :password)"
        );

        $result = $stmt->execute([
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword()
        ]);

        $stmt->closeCursor();

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    public function update(int $id, User $user): bool
    {
        $stmt = $this->db->prepare(
            "CALL sp_update_user(:id, :username, :email)"
        );

        $result = $stmt->execute([
            ':id' => $id,
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail()
        ]);

        $stmt->closeCursor();

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "CALL sp_delete_user(:id)"
        );

        $result = $stmt->execute([
            ':id' => $id
        ]);

        $stmt->closeCursor();

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | FIND USER BY EMAIL
    |--------------------------------------------------------------------------
    */

    public function findByEmail(string $email): array
    {
        $stmt = $this->db->prepare(
            "CALL sp_find_user_by_email(:email)"
        );

        $stmt->execute([
            ':email' => $email
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        if (!$data) {
            return [];
        }

        return $this->mapUser($data);
    }

    /*
    |--------------------------------------------------------------------------
    | MAP DATABASE ROW → USER ENTITY
    |--------------------------------------------------------------------------
    */

    private function mapUser(array $data): array
    {
        return [
            'user_id'  => $data['user_id'] ?? null,
            'username' => $data['username'] ?? null,
            'email'    => $data['email'] ?? null,
            'password' => $data['password'] ?? null, // REQUIRED FOR LOGIN
        ];
    }
}
