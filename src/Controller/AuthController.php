<?php
namespace App\Controller;

use App\Repository\UserRepository;
use App\Database;

class AuthController {

    public function showLogin() {
        require __DIR__ . '/../templates/login.php';
    }

    public function login() {
        $pdo = Database::connect();
        $repo = new UserRepository($pdo);

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $repo->findByUsername($username);

        // ONE generic failure for both "no such user" AND "wrong password"
        if ($user === null || !password_verify($password, $user->passwordHash)) {
            header('Location: /?action=login&error=1');
            exit;
        }

        // success → store minimal identity in the session
        $_SESSION['user_id']  = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role']     = $user->role;

        header('Location: /');
        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: /?action=login');
        exit;
    }

    public function createUser() {
        $pdo = Database::connect();
        $repo = new UserRepository($pdo);

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'lagermitarbeiter';

        if ($username === '' || $password === '' || !in_array($role, ['admin', 'lagermitarbeiter'],
        true)) {
            header('Location: /?action=users&error=invalid');
            exit;
        }


        // hash the password before storing it
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $repo->create($username, $passwordHash, $role);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                header('Location: /?action=login&error=username_taken');
                exit;
            }
            throw $e;
        }

        header('Location: /?action=login');
        exit;
    }

    public function showUserForm() {
        $pdo = Database::connect();
        $users = (new UserRepository($pdo))->findAll();   // ← add this
        require __DIR__ . '/../templates/users.php';
    }

    public function deleteUser() {
        $id = (int)($_POST['id'] ?? 0);

        // lockout safety: can't delete yourself
        if ($id === ($_SESSION['user_id'] ?? 0)) {
            header('Location: /?action=users&error=self');
            exit;
        }

        $pdo = Database::connect();
        (new UserRepository($pdo))->delete($id);
        header('Location: /?action=users&success=deleted');
        exit;
    }
}