<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected ?string $table = 'users';   // ✅ FIXED HERE

    protected string $primaryKey = 'user_id';

    protected array $columns = [
        'user_id',
        'username',
        'email',
        'password'
    ];

    protected array $fillable = [
        'username',
        'email',
        'password'
    ];

    public function findByEmail(string $email): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}
