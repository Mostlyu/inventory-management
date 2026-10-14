<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use App\Repository\CategoryRepository;
use App\Repository\StockMovementRepository;

use App\Database;

class ItemController {
    public function index() {
        $pdo = Database::connect();

        $search     = $_GET['search'] ?? null;
        $categoryId = isset($_GET['category']) && $_GET['category'] !== ''
                    ? (int)$_GET['category'] : null;

        $items      = (new ItemRepository($pdo))->findAll($search, $categoryId);
        $categories = (new CategoryRepository($pdo))->findAll();

        $stockRepo = new StockMovementRepository($pdo);
        $stockLevels = [];
        foreach ($items as $item) {
            $stockLevels[$item->id] = $stockRepo->getCurrentStock($item->id);
        }

        require __DIR__ . '/../templates/items.php';
    }

    public function create() {
        $pdo = (Database::connect());
        $repo = new ItemRepository($pdo);
        $categoryId = $_POST['category_id'] ?? 0;
        $sku = $_POST['sku'] ?? '';
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? null;

        try {
            $repo->create($categoryId, $sku, $name, $description);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                header('Location: /?action=items&error=sku');
                exit;
            }
            throw $e;
        }

        header('Location: /?action=items');
        exit;
    }

    public function export() {
        $pdo = Database::connect();
        $items = (new ItemRepository($pdo))->findAll();
        $stockRepo = new StockMovementRepository($pdo);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="items.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Name', 'SKU', 'Kategorie', 'Bestand', 'Beschreibung']);
        foreach ($items as $item) {
            fputcsv($out, [
                $item->id,
                $item->name,
                $item->sku,
                $item->categoryName,
                $stockRepo->getCurrentStock($item->id),
                $item->description,
            ]);
        }
        fclose($out);
        exit;
    }

    public function delete() {
        $id = (int)($_POST['id'] ?? 0);
        $pdo = Database::connect();
        try {
            (new ItemRepository($pdo))->delete($id);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                header('Location: /?action=items&error=in_use');
                exit;
            }
            throw $e;
        }
        header('Location: /?action=items&success=deleted');
        exit;
    }
}