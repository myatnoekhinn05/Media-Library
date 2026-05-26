<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
    protected PDO $db;

    protected string $table;

    protected string $primaryKey = 'id';

    protected array $columns = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // =====================================
    // BUILD COLUMN LIST
    // =====================================

    protected function getColumnList(): string
    {
        return implode(', ', $this->columns);
    }

    // =====================================
    // GET ALL
    // =====================================

    public function getAll(
        ?int $limit = null,
        int $offset = 0
    ): array {

        $columns = $this->getColumnList();

        $sql = "
            SELECT {$columns}
            FROM {$this->table}
        ";

        if ($limit !== null) {

            $sql .= "
                LIMIT :limit
                OFFSET :offset
            ";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {

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
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================
    // GET BY ID
    // =====================================

    public function getById(
        int $id
    ): array {

        $columns = $this->getColumnList();

        $sql = "
            SELECT {$columns}
            FROM {$this->table}
            WHERE {$this->primaryKey} = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
