<?php
namespace App\Repository;

use App\Model\Category;
use PDO;

class CategoryRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        $sql = "SELECT id, name, description FROM categories";
        $stmt = $this->pdo->query($sql);

        $categories = [];
        foreach ($stmt as $row) {
            $categories[] = new Category($row['id'],
            $row['name'],
            $row['description']);
        }
        return $categories;
    }

    public function create(string $name, ?string $description): Category {
        $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
        // prepare and execute the statement
        $stmt = $this->pdo->prepare($sql);
        // execute the statement with the given name and description
        $stmt->execute([$name, $description]);
        // obtain the last inserted ID
        $id = (int)$this->pdo->lastInsertId();
        // return a new Category object with the inserted data
        return new Category($id, $name, $description);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
    }

}