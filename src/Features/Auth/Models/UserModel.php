<?php
namespace App\Features\Auth\Models;
class UserModel
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function getAdminData($username)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.username = ? AND u.deleted_at IS NULL
        ");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    public function getAllUsers()
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.username, u.role_id, u.created_at, r.role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.deleted_at IS NULL 
            ORDER BY u.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getAllRoles()
    {
        $sql = "SELECT id as value, role_name as label FROM roles ORDER BY id ASC";
        return $this->db->query($sql)->fetchAll();
    }
    public function getUserById($id)
{
    $sql = "SELECT u.*, r.role_name 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.id = ? AND u.deleted_at IS NULL"; // Changed :id to ?

    $stmt = $this->db->prepare($sql);
    $stmt->execute([(int) $id]); // Now this positional array works

    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
    public function createUser($username, $hashedPassword, $roleId)
    {
        $sql = "INSERT INTO users (username, password, role_id, created_at) 
            VALUES (?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $username,
                $hashedPassword,
                (int) $roleId
            ]);
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
    public function updateUserWithPassword($id, $username, $roleId, $password)
    {
        $sql = "UPDATE users SET username = ?, role_id = ?, password = ? WHERE id = ?";
        return $this->db->prepare($sql)->execute([$username, (int) $roleId, $password, (int) $id]);
    }
    public function updateUser($id, $username, $roleId)
    {
        $sql = "UPDATE users SET username = ?, role_id = ? WHERE id = ?";
        return $this->db->prepare($sql)->execute([$username, (int) $roleId, (int) $id]);
    }
    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([(int) $id]);
    }
}