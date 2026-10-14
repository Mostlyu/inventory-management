<?php

namespace App\Controller;

use App\Repository\CategoryRepository;

use App\Database;

class CategoryController {
    public function index() {
        //Gets a PDO connection (Database::connect())
        $pdo = (Database::connect());

        // Creates a CategoryRepository with it,
        $categoryRepository = new CategoryRepository($pdo);

        // Calls findAll() to get the categories,
        $categories = $categoryRepository->findAll();

        // make them available to a template file(a require of templates/categories.php)
        require __DIR__ . '/../templates/categories.php';

    }

    public function create() {
        // gets a pdo connection (Database::connect())
        // and pdo means php data object, which is a way to connect to a database in php.
        $pdo = (Database::connect());
        // Creates a CategoryRepository with it,
        $repo = new CategoryRepository($pdo);
        // Gets the name and description from the POST request
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? null;

        // calls create() to create a new category with the data from the POST request.
        $repo->create($name, $description);

        header('Location: /');
        exit;
    }

    public function delete() {
        $id = (int)($_POST['id'] ?? 0);
        $pdo = Database::connect();
        try {
            (new CategoryRepository($pdo))->delete($id);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                header('Location: /?action=categories&error=in_use');
                exit;
            }
            throw $e;
        }
        header('Location: /?action=categories&success=deleted');
        exit;
    }
}