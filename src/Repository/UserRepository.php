<?php
namespace App\Repository;

use App\Model\User;
use PDO;

class UserRepository {

    public function __construct(private PDO $pdo) {}

    public function findByUsername(string $username): ?User {
        $sql = "SELECT id, username, role, password_hash FROM users WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        $row = $stmt->fetch();          // one row, not a loop

        if ($row === false) {
            return null;                // no such user
        }
        return new User($row['id'], $row['username'], $row['role'], $row['password_hash']);
    }

    public function create(string $username, string $passwordHash, string $role): void {
        $sql = "INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $passwordHash, $role]);
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT id, username, role, password_hash FROM users ORDER BY username");
        $users = [];
        foreach ($stmt as $row) {
            $users[] = new User($row['id'], $row['username'], $row['role'], $row['password_hash']);
        }
        return $users;
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}