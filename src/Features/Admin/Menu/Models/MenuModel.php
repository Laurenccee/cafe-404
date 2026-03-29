<?php

namespace App\Features\Admin\Menu\Models;

class MenuModel
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function getAllItems()
    {
        $sql = "SELECT m.*, c.category_name 
            FROM menu_items m 
            LEFT JOIN categories c ON m.category_id = c.id 
            ORDER BY m.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
    public function getItemById($id)
    {
        $sql = "SELECT m.*, c.category_name 
            FROM menu_items m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }
    public function getMenuItemsByCategory($categoryId)
    {
        $stmt = $this->db->prepare("
        SELECT m.*, c.category_name 
        FROM menu_items m
        JOIN categories c ON m.category_id = c.id
        WHERE m.category_id = :cat_id AND m.is_available = 1
    ");
        $stmt->execute(['cat_id' => $categoryId]);
        return $stmt->fetchAll();
    }
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getAllCategories()
    {
        return $this->db->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();
    }

    public function addItem($data)
    {
        $sql = "INSERT INTO menu_items (product_code, name, description, price, category_id, image_path, pos_x, pos_y) 
        VALUES (:product_code, :name, :description, :price, :category_id, :image_path, :pos_x, :pos_y)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    public function updateItem($id, $data)
    {
        $sql = "UPDATE menu_items SET name = :name, description = :description, price = :price, category_id = :category_id, image_path = :image_path, pos_x = :pos_x, pos_y = :pos_y WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function getTotalCount()
    {
        return $this->db->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
    }
    public function getInStockCount()
    {
        return $this->db->query("SELECT COUNT(*) FROM menu_items WHERE is_available = 1")->fetchColumn();
    }

    public function getItemsPaginated($limit, $offset)
    {
        $sql = "SELECT m.*, c.category_name 
            FROM menu_items m 
            LEFT JOIN categories c ON m.category_id = c.id 
            ORDER BY m.created_at DESC 
            LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}