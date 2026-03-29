<?php

namespace App\Features\Admin\Order\Models;

class OrderModels
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Creates a full order with its items using a Transaction
     */
    public function createOrder($orderData, $items)
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert into 'orders' (Trailing commas removed)
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

            // 2. Insert items (Mapped to your JS keys: id and price)
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

    public function getAllOrders()
    {
        // FIXED: Removed payment_methods join to match your new schema
        $sql = "SELECT o.*, u.username 
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function getOrderItems($orderId)
    {
        // FIXED: Now correctly queries the order_items table for specific items
        $sql = "SELECT oi.*, m.name as item_name 
                FROM order_items oi
                JOIN menu_items m ON oi.menu_item_id = m.id
                WHERE oi.order_id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}