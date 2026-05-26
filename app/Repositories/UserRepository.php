<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository
extends BaseRepository
implements UserRepositoryInterface
{
    public function getAll(
        ?int $limit = 10,
        int $offset = 0
    ): array {

        $stmt = $this->db->prepare(
            "CALL sp_get_all_users(:limit, :offset)"
        );

        $stmt->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(
        int $id
    ): array {

        $result = $this->db->prepare(
            "CALL sp_get_item_full_detail(?)"
        );

        $result->bindParam(
            1,
            $id,
            PDO::PARAM_INT
        );

        $result->execute();

        $item = $result->fetch(PDO::FETCH_ASSOC);

        if ($item === false) {
            $result->closeCursor();
            return [];
        }

        $result->nextRowset();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $role = strtolower($row['role']);

            $item[$role][] = $row['fullname'];
        }

        $result->closeCursor();

        return $item;
    }
    public function create(
        User $user
    ): bool {

        $stmt = $this->db->prepare(
            "CALL sp_create_user(
                :username,
                :email,
                :password
            )"
        );

        $result = $stmt->execute([
            ':username' => $user->username,
            ':email' => $user->email,
            ':password' => $user->password
        ]);

        $stmt->closeCursor();

        return $result;
    }

    public function update(
        int $id,
        User $user
    ): bool {

        $stmt = $this->db->prepare(
            "CALL sp_update_user(
                :id,
                :username,
                :email
            )"
        );

        $result = $stmt->execute([
            ':id' => $id,
            ':username' => $user->username,
            ':email' => $user->email
        ]);

        $stmt->closeCursor();

        return $result;
    }

    public function delete(
        int $id
    ): bool {

        $stmt = $this->db->prepare(
            "CALL sp_delete_user(:id)"
        );

        $result = $stmt->execute([
            ':id' => $id
        ]);

        $stmt->closeCursor();

        return $result;
    }

    public function findByEmail(
        string $email
    ): ?User {

        $stmt = $this->db->prepare(
            "CALL sp_find_user_by_email(:email)"
        );

        $stmt->execute([
            ':email' => $email
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        if (!$data) {
            return null;
        }

        return $this->mapUser($data);
    }

    private function mapUser(
        array $data
    ): User {

        $user = new User();

        $user->user_id = (int)$data['user_id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        return $user;
    }
}
