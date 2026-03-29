<?php

namespace App\Features\Auth\Controllers;

class AuthController
{
    private $authService;
    private $userModel;

    public function __construct($authService, $userModel)
    {
        $this->authService = $authService;
        $this->userModel = $userModel;
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['adminUsername'] ?? '';
            $password = $_POST['adminPassword'] ?? '';

            $user = $this->authService->login($username, $password);

            if ($user) {
                // 1. Start the session to save data
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // 2. Set the "Passport" keys your index.php expects
                $_SESSION['is_logged_in'] = true;
                $_SESSION['admin_user'] = $user['username'];
                $_SESSION['role'] = $user['role']; // THIS IS THE MISSING LINK
                $_SESSION['user_id'] = $user['id'];

                // 3. Now redirect
                header('Location: /cafe_404/dashboard');
                exit;
            } else {
                echo "Invalid credentials!";
            }
        }
        include __DIR__ . '/../Views/login.php';
    }

    public function manageUsers()
    {
        $adminInfo = $this->authService->getCurrentUser();
        $users = $this->userModel->getAllUsers();

        require_once ROOT_PATH . 'src/Features/Admin/User/View/users.php';
    }

    public function getAdmin()
    {
        $username = $_SESSION['admin_user'];
        $adminInfo = $this->userModel->getAdminData($username);

        include ROOT_PATH . 'src/Features/Admin/Dashboard/View/dashboard.php';
    }


}