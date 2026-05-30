<?php

declare(strict_types=1);

namespace App\Controllers;

use Throwable;
use App\Services\CatalogService;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;

class CatalogController extends BaseController
{
    private CatalogService $catalogService;

    public function __construct(
        CatalogService $catalogService
    ) {
        $this->catalogService = $catalogService;
    }

    /*
    |--------------------------------------------------------------------------
    | HOME PAGE
    |--------------------------------------------------------------------------
    */
    public function home(): void
    {
        try {

            $this->requireLogin();

            $data = $this->catalogService->getHomePageData();

            extract($data);

            require BASE_PATH . '/view/home.php';
        } catch (Throwable $e) {

            $this->handleSystemError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CATALOG PAGE
    |--------------------------------------------------------------------------
    */
    public function index(): void
    {
        try {

            $this->requireLogin();

            $data = $this->catalogService->getCatalogPage($_GET);

            extract($data);

            require BASE_PATH . '/view/catalog.php';
        } catch (ValidationException $e) {

            $_SESSION['error'] = $e->getMessage();

            $this->redirect(
                BASE_URL . '/Public/index.php?page=catalog'
            );
        } catch (Throwable $e) {

            $this->handleSystemError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW ITEM
    |--------------------------------------------------------------------------
    */
    public function show(): void
    {
        try {

            $this->requireLogin();

            $id = (int) ($_GET['id'] ?? 0);

            $item = $this->catalogService->getById($id);

            require BASE_PATH . '/view/details.php';
        } catch (ValidationException | NotFoundException $e) {

            $_SESSION['error'] = $e->getMessage();

            $this->redirect(
                BASE_URL . '/Public/index.php?page=catalog'
            );
        } catch (Throwable $e) {

            $this->handleSystemError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE SYSTEM ERROR
    |--------------------------------------------------------------------------
    */
    private function handleSystemError(
        Throwable $e
    ): void {

        error_log($e);

        $_SESSION['error'] =
            'Something went wrong. Please try again later.';

        $this->redirect(
            BASE_URL . '/Public/index.php?page=home'
        );
    }
}
