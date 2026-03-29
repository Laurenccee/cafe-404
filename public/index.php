<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// public/index.php
define('BASE_URL', '/cafe_404');
// 1. Setup Environment
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/cafe_404/');
require_once ROOT_PATH . 'src/autoload.php';

use App\Features\Admin\User\Controllers\UsersController;
use App\Shared\Config\Database;
use App\Features\Auth\Services\AuthService;
use App\Features\Auth\Controllers\AuthController;
use App\Features\Auth\Models\UserModel;
use App\Features\Admin\Menu\Controllers\MenuController;
use App\Features\Admin\Menu\Models\MenuModel;
use App\Features\Admin\Order\Controllers\OrderController;
use App\Features\Admin\Order\Models\OrderModels;

function protect_admin_only($basePath)
{
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    if (!isset($_SESSION['is_logged_in']) || (int) $_SESSION['role_id'] !== 0) {
        http_response_code(403);
        die("403: Only Admins can manage the User Ledger.");
    }
}
function protect_manager_access($basePath)
{
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    $role = (int) ($_SESSION['role_id'] ?? -1);
    if (!isset($_SESSION['is_logged_in']) || ($role !== 0 && $role !== 1)) {
        http_response_code(403);
        die("403: You do not have permission to edit the Menu.");
    }
}
function protect_logged_in($basePath)
{
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    if (!isset($_SESSION['is_logged_in'])) {
        header('Location: ' . $basePath . '/login');
        exit;
    }
}
// 2. Initialize Core Services
$db = Database::getConnection();
$userModel = new UserModel($db);
$authService = new AuthService($db);

$authController = new AuthController($authService, $userModel);
$userController = new UsersController($userModel, $authService);

$menuModel = new MenuModel($db);
$menuController = new MenuController($menuModel, $authService);

$orderModel = new OrderModels($db);
$orderController = new OrderController($orderModel, $authService, $menuModel);

// 3. Routing Logic
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/cafe_404'; // Adjust if project folder changes
$route = rtrim(str_replace($basePath, '', $request), '/') ?: '/';

switch ($route) {
    case '/':
    case '/login':
        $authController->handleLogin();
        break;

    case '/logout':
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        session_unset();
        session_destroy();
        header('Location: ' . $basePath . '/login');
        exit;

    case '/dashboard':
        protect_manager_access($basePath);
        $authController->getAdmin();
        break;

    case '/users':
        protect_admin_only($basePath);
        $userController->index();
        break;

    case '/users/add':
        protect_admin_only($basePath);
        $userController->store();
        break;

    case '/users/edit':
        protect_admin_only($basePath);
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        $userController->edit($id);
        break;

    case '/users/update':
        protect_admin_only($basePath);
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        $userController->update($id);
        break;

    case '/menu':
        protect_manager_access($basePath);
        $menuController->index();
        break;

    case '/menu/add':
        protect_manager_access($basePath);
        $menuController->create();
        break;

    case '/menu/store':
        protect_manager_access($basePath);
        $menuController->store();
        break;

    case '/menu/edit':
        protect_manager_access($basePath);
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        $menuController->edit($id);
        break;

    case '/menu/update':
        protect_manager_access($basePath);
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        $menuController->update($id);
        break;

    case '/pos':
        protect_logged_in($basePath);
        $orderController->index();
        break;

    case '/pos/checkout':
        protect_logged_in($basePath);
        $orderController->checkout();
        break;

    case '/order/history':
        protect_logged_in($basePath);
        $orderController->history();
        break;

    default:
        http_response_code(404);
        echo "404 - Page Not Found";
        break;
}