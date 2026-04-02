<?php

namespace App\Features\Admin\User\Controllers;

class UsersController
{
    private $userModel;
    private $authService;
    public function __construct($userModel, $authService)
    {
        $this->userModel = $userModel;
        $this->authService = $authService;
    }
    public function index()
    {
        $adminInfo = $this->authService->getCurrentUser();
        $users = $this->userModel->getAllUsers();
        $roles = $this->userModel->getAllRoles();

        require_once ROOT_PATH . 'src/Features/Admin/User/View/users.php';
    }
    public function edit($id)
    {

        if (!$id) {
            header('Location: /cafe_404/users?error=missing_id');
            exit;
        }

        $user = $this->userModel->getUserById($id);
        $roles = $this->userModel->getAllRoles();

        if (!$user) {
            header('Location: /cafe_404/users?error=not_found');
            exit;
        }

        require_once ROOT_PATH . 'src/Features/Admin/User/View/edit.php';
    }

    public function store()
    {
        $currentUser = $this->authService->getCurrentUser();

        if (!$currentUser || (int) $currentUser['role_id'] !== 0) {
            header('Location: /cafe_404/dashboard?error=unauthorized');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            $role = $_POST['role'] ?? null;

            if (empty($username) || empty($password) || $role === null) {
                header('Location: /cafe_404/users?error=empty_fields');
                exit;
            }

            if ($password !== $confirmPassword) {
                header('Location: /cafe_404/users?error=password_mismatch');
                exit;
            }

            if ($this->userModel->getAdminData($username)) {
                header('Location: /cafe_404/users?error=user_exists');
                exit;
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $success = $this->userModel->createUser($username, $hashed, $role);

            if ($success) {
                header('Location: /cafe_404/users?success=added');
            } else {
                header('Location: /cafe_404/users?error=db_failure');
            }
            exit;
        }
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cafe_404/admin/users');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $role = $_POST['role'] ?? null;
        $password = $_POST['password'] ?? '';

        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $success = $this->userModel->updateUserWithPassword($id, $username, $role, $hashed);
        } else {
            $success = $this->userModel->updateUser($id, $username, $role);
        }

        if ($success) {
            header('Location: /cafe_404/users?success=updated');
        } else {
            header("Location: /cafe_404/users/edit?id=$id&error=update_failed");
        }
        exit;

    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($this->userModel->deleteUser($id)) {
                header('Location: /cafe_404/users?deleted=1');
                exit;
            } else {
                header('Location: /cafe_404/users?error=delete_failed');
                exit;
            }
        }
    }
}