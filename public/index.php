<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\AuthController;
use App\Controller\CategoryController;
use App\Controller\ItemController;
use App\Controller\StockMovementController;

$action = $_GET['action'] ?? 'index';

// pages anyone can reach without logging in
$publicActions = ['login', 'login_submit'];

// everything else requires a logged-in user
if (!in_array($action, $publicActions, true) && !isset($_SESSION['user_id'])) {
    header('Location: /?action=login');
    exit;
}

$adminActions = ['create', 'item_create', 'users',
'user_create', 'user_delete', 'category_delete', 'item_delete'];

if (in_array($action, $adminActions, true) && ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: /?action=items&error=forbidden');
    exit;
}

if (in_array($action, $adminActions, true) && ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: /?action=items&error=forbidden');
    exit;
}

switch ($action) {
    case 'login':
        (new AuthController())->showLogin();
        break;
    case 'login_submit':
        (new AuthController())->login();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'create':
        (new CategoryController())->create();
        break;
    case 'items':
        (new ItemController())->index();
        break;
    case 'item_create':
        (new ItemController())->create();
        break;
    case 'stockmovements':
        (new StockMovementController())->index();
        break;
    case 'stockmovement_create':
        (new StockMovementController())->create();
        break;
    case 'items_export': (new ItemController())->export();
        break;
    case 'users':
        (new AuthController())->showUserForm();
        break;
    case 'user_create':
        (new AuthController())->createUser();
        break;
    case 'user_delete':
        (new AuthController())->deleteUser();
        break;
    case 'category_delete':
        (new CategoryController())->delete();
        break;
    case 'item_delete':
        (new ItemController())->delete();
        break;
    default:
        (new CategoryController())->index();
        break;
}

?>
