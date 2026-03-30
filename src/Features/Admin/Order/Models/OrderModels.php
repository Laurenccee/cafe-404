<?php

namespace App\Features\Admin\Order\Models;

use PDO;
class OrderModels
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function createOrder($orderData, $items)
    {
        try {
            $this->db->beginTransaction();

            $sqlOrder = "INSERT INTO orders (
                order_number, user_id, total_amount, 
                amount_paid, change_amount
            ) VALUES (
                :order_number, :user_id, :total_amount, 
                :amount_paid, :change_amount
            )";

            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute($orderData);

            $orderId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO order_items (
                order_id, menu_item_id, quantity, unit_price
            ) VALUES (
                :order_id, :menu_item_id, :quantity, :unit_price
            )";

            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $item) {
                $stmtItem->execute([
                    'order_id' => $orderId,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price']
                ]);
            }

            $this->db->commit();
            return $orderId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Order Creation Failed: " . $e->getMessage());
            return false;
        }
    }

    public function getAllOrders($filters = [])
    {
        $sql = "SELECT 
                o.*, 
                u.username,
                GROUP_CONCAT(CONCAT('(', oi.quantity, ')x ', m.name) SEPARATOR ', ') AS item_summary
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE 1=1";

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND o.order_number LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['from'])) {
            $sql .= " AND DATE(o.created_at) >= :from";
            $params[':from'] = $filters['from'];
        }

        if (!empty($filters['to'])) {
            $sql .= " AND DATE(o.created_at) <= :to";
            $params[':to'] = $filters['to'];
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, m.name as item_name 
                FROM order_items oi
                JOIN menu_items m ON oi.menu_item_id = m.id
                WHERE oi.order_id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPopularItems($limit = 4)
    {
        $sql = "SELECT 
            m.*, 
            c.category_name,
            CAST(SUM(oi.quantity) AS UNSIGNED) as sold 
        FROM order_items oi
        JOIN menu_items m ON oi.menu_item_id = m.id
        LEFT JOIN categories c ON m.category_id = c.id
        GROUP BY m.id
        ORDER BY sold DESC
        LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}