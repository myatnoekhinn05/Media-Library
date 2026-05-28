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

    // ✅ Stored procedures support
    protected ?string $getAllProcedure = null;
    protected ?string $getByIdProcedure = null;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL (SQL OR PROCEDURE)
    |--------------------------------------------------------------------------
    */
    public function getAll(?int $limit = null, int $offset = 0): array
    {
        // PROCEDURE MODE
        if ($this->getAllProcedure !== null) {

            $stmt = $this->db->prepare("CALL {$this->getAllProcedure}(:limit, :offset)");

            $stmt->bindValue(':limit', $limit, $limit ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $data;
        }

        // TABLE MODE
        $cols = $this->columns ? implode(', ', $this->columns) : '*';

        $sql = "SELECT {$cols} FROM {$this->table}";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | GET BY ID (SQL OR PROCEDURE)
    |--------------------------------------------------------------------------
    */
    public function getById(int $id): array
    {
        // PROCEDURE MODE
        if ($this->getByIdProcedure !== null) {

            $stmt = $this->db->prepare("CALL {$this->getByIdProcedure}(:id)");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // --------------------------
            // 1. MAIN ITEM
            // --------------------------
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                $stmt->closeCursor();
                return [];
            }

            // --------------------------
            // 2. INIT RELATION ARRAYS
            // --------------------------
            $item['authors'] = [];
            $item['directors'] = [];
            $item['stars'] = [];
            $item['artists'] = [];

            // --------------------------
            // 3. NEXT RESULT SET (RELATIONS)
            // --------------------------
            if ($stmt->nextRowset()) {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $role = strtolower(trim($row['role'] ?? ''));
                    $name = $row['fullname'] ?? null;

                    if (!$name) continue;

                    switch ($role) {

                        case 'author':
                        case 'writer':
                            $item['authors'][] = $name;
                            break;

                        case 'direct':
                        case 'director':
                            $item['directors'][] = $name;
                            break;

                        case 'star':
                        case 'actor':
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

        // --------------------------
        // TABLE MODE (UNCHANGED)
        // --------------------------
        $cols = $this->columns ? implode(', ', $this->columns) : '*';

        $stmt = $this->db->prepare("
        SELECT {$cols}
        FROM {$this->table}
        WHERE {$this->primaryKey} = :id
        LIMIT 1
    ");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data): bool
    {
        $fields = [];
        $placeholders = [];

        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $fields[] = $field;
                $placeholders[] = ':' . $field;
            }
        }

        $sql = "
            INSERT INTO {$this->table}
            (" . implode(',', $fields) . ")
            VALUES
            (" . implode(',', $placeholders) . ")
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($fields as $field) {
            $stmt->bindValue(':' . $field, $data[$field]);
        }

        return $stmt->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(int $id, array $data): bool
    {
        $sets = [];

        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $sets[] = "{$field} = :{$field}";
            }
        }

        $sql = "
            UPDATE {$this->table}
            SET " . implode(',', $sets) . "
            WHERE {$this->primaryKey} = :id
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $stmt->bindValue(':' . $field, $data[$field]);
            }
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE {$this->primaryKey} = :id
        ");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
