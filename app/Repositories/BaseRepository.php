<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Interfaces\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
    protected PDO $db;

    protected ?string $table = null;

    protected string $primaryKey = 'id';

    protected array $columns = [];

    protected array $fillable = [];

    /*
    |--------------------------------------------------------------------------
    | STORED PROCEDURES
    |--------------------------------------------------------------------------
    */

    protected ?string $getAllProcedure = null;

    protected ?string $getByIdProcedure = null;

    protected ?string $createProcedure = null;

    protected ?string $updateProcedure = null;

    protected ?string $deleteProcedure = null;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    |--------------------------------------------------------------------------
    */

    public function getAll(?int $limit = null, int $offset = 0): array
    {
        if ($this->getAllProcedure !== null) {

            $stmt = $this->db->prepare(
                "CALL {$this->getAllProcedure}(:limit, :offset)"
            );

            $stmt->bindValue(
                ':limit',
                $limit,
                $limit !== null
                    ? PDO::PARAM_INT
                    : PDO::PARAM_NULL
            );

            $stmt->bindValue(
                ':offset',
                $offset,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            return $data;
        }

        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | GET BY ID
    |--------------------------------------------------------------------------
    */

    public function getById(int $id): array
    {
        if ($this->getByIdProcedure !== null) {

            $stmt = $this->db->prepare(
                "CALL {$this->getByIdProcedure}(:id)"
            );

            $stmt->bindValue(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                $stmt->closeCursor();
                return [];
            }

            /*
            |--------------------------------------------------------------------------
            | HANDLE MULTIPLE RESULT SETS
            |--------------------------------------------------------------------------
            */

            $item['authors'] = [];
            $item['directors'] = [];
            $item['stars'] = [];
            $item['artists'] = [];

            if ($stmt->nextRowset()) {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $role = strtolower(
                        trim($row['role'] ?? '')
                    );

                    $name = $row['fullname'] ?? null;

                    if (!$name) {
                        continue;
                    }

                    switch ($role) {

                        case 'author':
                        case 'writer':
                            $item['authors'][] = $name;
                            break;

                        case 'director':
                        case 'direct':
                            $item['directors'][] = $name;
                            break;

                        case 'actor':
                        case 'star':
                            $item['stars'][] = $name;
                            break;

                        case 'artist':
                            $item['artists'][] = $name;
                            break;
                    }
                }
            }

            $stmt->closeCursor();

            return $item;
        }

        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(array $data): bool
    {
        if ($this->createProcedure === null) {
            return false;
        }

        $params = [];

        foreach ($this->fillable as $field) {
            $params[] = ':' . $field;
        }

        $sql = sprintf(
            'CALL %s(%s)',
            $this->createProcedure,
            implode(', ', $params)
        );

        $stmt = $this->db->prepare($sql);

        foreach ($this->fillable as $field) {

            $stmt->bindValue(
                ':' . $field,
                $data[$field] ?? null
            );
        }

        $success = $stmt->execute();

        $stmt->closeCursor();

        return $success;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(int $id, array $data): bool
    {
        if ($this->updateProcedure === null) {
            return false;
        }

        $params = [':id'];

        foreach ($this->fillable as $field) {
            $params[] = ':' . $field;
        }

        $sql = sprintf(
            'CALL %s(%s)',
            $this->updateProcedure,
            implode(', ', $params)
        );

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        foreach ($this->fillable as $field) {

            $stmt->bindValue(
                ':' . $field,
                $data[$field] ?? null
            );
        }

        $success = $stmt->execute();

        $stmt->closeCursor();

        return $success;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(int $id): bool
    {
        if ($this->deleteProcedure === null) {
            return false;
        }

        $stmt = $this->db->prepare(
            "CALL {$this->deleteProcedure}(:id)"
        );

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $success = $stmt->execute();

        $stmt->closeCursor();

        return $success;
    }
}
