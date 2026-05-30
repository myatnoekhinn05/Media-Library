<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Interfaces\UserRepositoryInterface;

class UserRepository
extends BaseRepository
implements UserRepositoryInterface
{
    protected ?string $table = 'users';

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

    /*
    |--------------------------------------------------------------------------
    | STORED PROCEDURES
    |--------------------------------------------------------------------------
    */

    protected ?string $getAllProcedure =
        'sp_get_all_users';

    protected ?string $getByIdProcedure =
        'sp_get_user_by_id';

    protected ?string $createProcedure =
        'sp_create_user';

    protected ?string $updateProcedure =
        'sp_update_user';

    protected ?string $deleteProcedure =
        'sp_delete_user';

    /*
    |--------------------------------------------------------------------------
    | FIND USER BY EMAIL
    |--------------------------------------------------------------------------
    */

    public function findByEmail(
        string $email
    ): array {

        $stmt = $this->db->prepare(
            "CALL sp_find_user_by_email(:email)"
        );

        $stmt->bindValue(
            ':email',
            $email,
            PDO::PARAM_STR
        );

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return $user ?: [];
    }
}