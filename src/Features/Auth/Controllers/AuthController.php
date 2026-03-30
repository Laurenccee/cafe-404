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
                $role = (int) $user['role_id'];

                if ($role === 0 || $role === 1) {
                    header('Location: /cafe_404/dashboard');
                } else {
                    header('Location: /cafe_404/pos');
                }
                exit;
            } else {
                $error = "Invalid credentials!";
            }
        }
        include ROOT_PATH . 'src/Features/Auth/Views/login.php';
    }


}