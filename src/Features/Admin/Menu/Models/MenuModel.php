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
    public function getAllAvailability()
    {
        return $this->db->query("SELECT * FROM availability ORDER BY label ASC")->fetchAll();
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
        $sql = "UPDATE menu_items SET name = :name, is_available = :is_available, description = :description, price = :price, category_id = :category_id, image_path = :image_path, pos_x = :pos_x, pos_y = :pos_y WHERE id = :id";
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

    public function deleteItem()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /cafe_404/menu?error=missing_id');
            exit;
        }

        if ((int) $_SESSION['role_id'] !== 0) {
            header('Location: /cafe_404/menu?error=unauthorized');
            exit;
        }

        $stmt = $this->db->prepare("DELETE FROM menu_items WHERE id = ?");
        $success = $stmt->execute([$id]);

        if ($success) {
            header('Location: /cafe_404/menu?success=item_deleted');
        } else {
            header('Location: /cafe_404/menu?error=delete_failed');
        }
        exit;
    }
    public function getFilteredItems($filters, $limit, $offset)
    {
        $search = $filters['search'] ?? null;
        $category = $filters['category'] ?? null;
        $availability = $filters['availability'] ?? null;

        $sql = "SELECT m.*, c.category_name 
            FROM menu_items m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE 1=1";

        $params = [];

        if ($search) {
            $sql .= " AND m.name LIKE :search";
            $params[':search'] = "%$search%";
        }

        if ($category) {
            $sql .= " AND m.category_id = :category";
            $params[':category'] = $category;
        }

        if ($availability) {
            $sql .= " AND m.is_available = :availability";
            $params[':availability'] = $availability;
        }

        $sql .= " ORDER BY m.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getFilteredCount($filters)
    {
        $sql = "SELECT COUNT(*) FROM menu_items WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = "%{$filters['search']}%";
        }
        if (!empty($filters['category'])) {
            $sql .= " AND category_id = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['availability'])) {
            $sql .= " AND is_available = :availability";
            $params[':availability'] = $filters['availability'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}