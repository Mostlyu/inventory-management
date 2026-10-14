<?php

namespace App\Controller;

use App\Repository\StockMovementRepository;
use App\Repository\CategoryRepository;
use App\Repository\ItemRepository;

use App\Database;

class StockMovementController {

    public function index() {
        $pdo = Database::connect();
        $stockMovements = (new StockMovementRepository($pdo))->findAll();
        $items = (new ItemRepository($pdo))->findAll();   // for the item dropdown

        require __DIR__ . '/../templates/stockmovements.php';
    }

    public function create() {
        $pdo = (Database::connect());
        $repo = new StockMovementRepository($pdo);

        $itemId = (int)($_POST['item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $type = $_POST['type'] ?? '';
        $note = $_POST['note'] ?? null;

        // 1) basic input validation
        if ($itemId <= 0 || !in_array($type, ['in', 'out'], true) || $quantity <= 0) {
            header('Location: /?action=stockmovements&error=invalid');
            exit;
        }

        // 2) business rule: no negeative stock
        if ($type === 'out' && $quantity > $repo->getCurrentStock($itemId)) {
            header('Location: /?action=stockmovements&error=stock');
            exit;
        }

        // 3) book it
        $repo->create($itemId, $quantity, $type, $note);
        header('Location: /?action=stockmovements');
        exit;
    }

}