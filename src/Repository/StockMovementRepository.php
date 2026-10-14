<?php
namespace App\Repository;

use App\Model\StockMovement;
use PDO;

class StockMovementRepository {

    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        $sql = "SELECT items.name AS item_name,
        stock_movements.id, stock_movements.item_id, stock_movements.type,
        stock_movements.quantity, stock_movements.note,
        stock_movements.created_at, stock_movements.updated_at
        FROM stock_movements
        JOIN items ON stock_movements.item_id = items.id
        JOIN categories ON items.category_id = categories.id
        ORDER BY stock_movements.created_at DESC";
        $stmt = $this->pdo->query($sql);

        // this will hold the StockMovements objects
        $stockMovements = [];
        // this loop will iterate over each row returned by the query
        foreach ($stmt as $row) {
            $stockMovements[] = new StockMovement(
                $row['id'],
                $row['item_id'],
                $row['type'],
                $row['quantity'],
                $row['note'],
                $row['created_at'],
                $row['updated_at'],
                $row['item_name']
            );
        }
        return $stockMovements;
    }

    public function create(int $itemId, int $quantity, string $type, ?string $note): void {
        $sql = "INSERT INTO stock_movements (item_id, type, quantity, note) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$itemId, $type, $quantity, $note]);
        $id = (int)$this->pdo->lastInsertId();
    }

    public function getCurrentStock(int $itemId): int {
    $sql = "SELECT COALESCE(SUM(
                CASE WHEN type = 'in' THEN quantity ELSE -quantity END
            ), 0) AS current_stock
            FROM stock_movements
            WHERE item_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$itemId]);
    return (int)$stmt->fetchColumn();
}

}