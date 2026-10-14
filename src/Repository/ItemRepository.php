<?php
namespace App\Repository;

use App\Model\Item;
use PDO;

class ItemRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(?string $search = null, ?int $categoryId = null): array {
        $sql = "SELECT items.id, items.category_id, items.name, items.description,
                    items.sku, items.created_at, items.updated_at,
                    categories.name AS category_name
                FROM items
                JOIN categories ON items.category_id = categories.id";

        $conditions = [];
        $params = [];

        if ($search !== null && $search !== '') {
            $conditions[] = "(items.name LIKE ? OR items.sku = ?)";
            $params[] = "%$search%";
            $params[] = $search;
        }
        if ($categoryId !== null && $categoryId > 0) {
            $conditions[] = "items.category_id = ?";
            $params[] = $categoryId;
        }

        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY items.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $items = [];
        foreach ($stmt as $row) {
            $items[] = new Item(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['category_id'],
                $row['sku'],
                $row['created_at'],
                $row['updated_at'],
                $row['category_name']
            );
        }
        return $items;
    }

    public function create(int $categoryId, string $sku, string $name, ?string $description): void {

        $sql = "INSERT INTO items (category_id, sku, name, description) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categoryId, $sku, $name, $description]);
        $id = (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$id]);
    }

}