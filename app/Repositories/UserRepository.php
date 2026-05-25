<?php

namespace App\Repositories;

use PDO;
use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }

    /**
     * Create new user
     */
    public function create(User $user): bool
    {
        $sql = "
            INSERT INTO users (
                username,
                email,
                password
            )
            VALUES (
                :username,
                :email,
                :password
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':username' => $user->username,
            ':email'    => $user->email,
            ':password' => $user->password
        ]);
    }

    /**
     * Find user by email
     */
    public function findByEmail(
        string $email
    ): ?User {

        $sql = "
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new User();

        $user->user_id  = $data['id'];
        $user->username = $data['username'];
        $user->email    = $data['email'];
        $user->password = $data['password'];

        return $user;
    }

    /**
     * Required because BaseRepository has abstract method
     */
    public function getById(
        int $id
    ): ?array {

        $sql = "
            SELECT *
            FROM users
            WHERE user_id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
