<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CatalogRepositoryInterface;
use PDO;

class CatalogRepository extends BaseRepository implements CatalogRepositoryInterface
{
    protected ?string $getAllProcedure = 'sp_get_full_catalog';

    protected ?string $getByIdProcedure = 'sp_get_item_full_detail';

    public function count(array $filters = []): int
    {
        $stmt = $this->db->prepare("
            CALL sp_search_catalog_count(:search, :category)
        ");

        $stmt->execute([
            ':search' => $filters['search'] ?? null,
            ':category' => $filters['category'] ?? null
        ]);

        $count = (int) $stmt->fetchColumn();

        $stmt->closeCursor();

        return $count;
    }

    public function getCategoryCatalog(string $category, ?int $limit = null, int $offset = 0): array
    {
        $stmt = $this->db->prepare("CALL sp_get_catalog(:category, :limit, :offset)");

        $stmt->execute([
            ':category' => $category,
            ':limit' => $limit,
            ':offset' => $offset
        ]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    public function getSearchCatalog(?string $search, ?string $category = null, ?int $limit = null, int $offset = 0): array
    {
        $stmt = $this->db->prepare("CALL sp_search_catalog(:search, :category, :limit, :offset)");

        $stmt->execute([
            ':search' => $search,
            ':category' => $category,
            ':limit' => $limit,
            ':offset' => $offset
        ]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    public function getRandomCatalog(): array
    {
        $stmt = $this->db->query("SELECT * FROM view_random");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
