<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\CatalogRepositoryInterface;

class CatalogService extends BaseService
{
    private CatalogRepositoryInterface $repo;

    public function __construct(
        CatalogRepositoryInterface $repo
    ) {
        $this->repo = $repo;
    }

    /*
    |--------------------------------------------------------------------------
    | HOME PAGE
    |--------------------------------------------------------------------------
    */
    public function getHomePageData(): array
    {
        return [
            'random' => $this->repo->getRandomCatalog(),
            'pageTitle' => 'Personal Media Library',
            'section' => 'catalog'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CATALOG PAGE
    |--------------------------------------------------------------------------
    */
    public function getCatalogPage(
        array $queryParams
    ): array {

        $section = $this->getCategory($queryParams);

        $search = $this->getSearchTerm($queryParams);

        $currentPage = $this->getCurrentPage($queryParams);

        $totalItems = $this->repo->count([
            'category' => $section,
            'search' => $search
        ]);

        $pagination = $this->buildPagination(
            $totalItems,
            $currentPage
        );

        $catalog = $this->loadCatalogData(
            $section,
            $search,
            $pagination['limit'],
            $pagination['offset']
        );

        return [
            'catalog' => $catalog,
            'section' => $section,
            'search' => $search,
            'currentPage' => $pagination['currentPage'],
            'totalPages' => $pagination['totalPages'],
            'pageTitle' => $this->buildPageTitle($section),
            'queryString' => $this->buildQueryString(
                $section,
                $search
            )
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE CATEGORY
    |--------------------------------------------------------------------------
    */
    private function getCategory(
        array $params
    ): ?string {

        $category = trim($params['cat'] ?? '');

        if ($category === '') {
            return null;
        }

        $allowed = [
            'books',
            'movies',
            'music'
        ];

        if (!in_array($category, $allowed, true)) {

            throw new ValidationException([
                'category' => 'Invalid category selected.'
            ]);
        }

        return $category;
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH TERM
    |--------------------------------------------------------------------------
    */
    private function getSearchTerm(
        array $params
    ): ?string {

        $search = trim($params['s'] ?? '');

        if ($search === '') {
            return null;
        }

        if (mb_strlen($search) > 100) {

            throw new ValidationException([
                'search' => 'Search term is too long.'
            ]);
        }

        return $search;
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD CATALOG DATA
    |--------------------------------------------------------------------------
    */
    private function loadCatalogData(
        ?string $section,
        ?string $search,
        int $limit,
        int $offset
    ): array {

        if ($search !== null && $section !== null) {

            return $this->repo->getSearchCatalog(
                $search,
                $section,
                $limit,
                $offset
            );
        }

        if ($search !== null) {

            return $this->repo->getSearchCatalog(
                $search,
                null,
                $limit,
                $offset
            );
        }

        if ($section !== null) {

            return $this->repo->getCategoryCatalog(
                $section,
                $limit,
                $offset
            );
        }

        return $this->repo->getAll(
            $limit,
            $offset
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAGE TITLE
    |--------------------------------------------------------------------------
    */
    private function buildPageTitle(
        ?string $section
    ): string {

        return $section
            ? ucfirst($section)
            : 'Full Catalog';
    }

    /*
    |--------------------------------------------------------------------------
    | GET ITEM BY ID
    |--------------------------------------------------------------------------
    */
    public function getById(
        int $id
    ): array {

        if ($id <= 0) {

            throw new ValidationException([
                'id' => 'Invalid catalog item ID.'
            ]);
        }

        $item = $this->repo->getById($id);

        if (!$item) {

            throw new NotFoundException(
                'Catalog item not found.'
            );
        }

        return $item;
    }
}
