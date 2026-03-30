<?php

namespace App\Features\Auth\Services;

class AuthService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function login($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE)
                session_start();

            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];

            return $user;
        }
        return false;
    }

    public function getCurrentUser()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role_id' => $_SESSION['role_id'],
            ];
        }
        return null;
    }
    public function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }
}