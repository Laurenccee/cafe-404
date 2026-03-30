<?php
// src/Shared/Config/Database.php
namespace App\Shared\Config;

use PDO;
use PDOException;

class Database
{
    private static $host = 'localhost';
    private static $dbName = 'coffee_404';
    private static $user = 'root';
    private static $pass = '';
    private static $charset = 'utf8mb4';
    public static function getConnection()
    {
        try {
            $pdo = new PDO("mysql:host=" . self::$host . ";charset=" . self::$charset, self::$user, self::$pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . self::$dbName . "` 
                        DEFAULT CHARACTER SET utf8mb4 
                        COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `" . self::$dbName . "`;");
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::initDatabase($pdo);

            return $pdo;

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    private static function initDatabase($pdo)
    {

        $sqlRoles = "CREATE TABLE IF NOT EXISTS roles (
        id INT PRIMARY KEY,
        role_name VARCHAR(50) NOT NULL UNIQUE
    ) ENGINE=InnoDB;";
        $pdo->exec($sqlRoles);

        $roles = [
            0 => 'admin',
            1 => 'manager',
            2 => 'staff'
        ];

        foreach ($roles as $id => $name) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO roles (id, role_name) VALUES (?, ?)");
            $stmt->execute([$id, $name]);
        }

        $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role_id INT DEFAULT 2, 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;";
        $pdo->exec($sqlUsers);

        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 0");
        if ($stmt->fetchColumn() == 0) {
            $adminUsername = 'admin';
            $adminPass = password_hash('admin123', PASSWORD_DEFAULT);

            $insert = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, 0)");
            $insert->execute([$adminUsername, $adminPass]);
        }

        $sqlCats = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_name VARCHAR(50) NOT NULL UNIQUE
        ) ENGINE=InnoDB;";
        $pdo->exec($sqlCats);

        $categories = ['Espresso', 'Non-Coffee', 'Pastry',];
        foreach ($categories as $cat) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO categories (category_name) VALUES (?)");
            $stmt->execute([$cat]);
        }

        $sqlCats = "CREATE TABLE IF NOT EXISTS availability (
            id INT AUTO_INCREMENT PRIMARY KEY,
            label VARCHAR(50) NOT NULL UNIQUE
        ) ENGINE=InnoDB;";
        $pdo->exec($sqlCats);

        $availability = [
            'Available',
            'Sold Out',
            'Not Available',
        ];
        foreach ($availability as $availabilityLabel) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO availability (label) VALUES (?)");
            $stmt->execute([$availabilityLabel]);
        }

        $sqlMenus = "CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_code VARCHAR(20) UNIQUE NOT NULL,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        category_id INT,
        
        image_path VARCHAR(255) DEFAULT 'default-coffee.jpg',
        pos_x DECIMAL(5,2) DEFAULT 50.00,
        pos_y DECIMAL(5,2) DEFAULT 50.00,

        is_available TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;";

        $pdo->exec($sqlMenus);

        $sqlOrders = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(20) UNIQUE NOT NULL,
        user_id INT, 
        total_amount DECIMAL(10, 2) NOT NULL,
        amount_paid DECIMAL(10, 2) NOT NULL,
        change_amount DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;";
        $pdo->exec($sqlOrders);

        $sqlOrderItems = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        menu_item_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        unit_price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
    ) ENGINE=InnoDB;";
        $pdo->exec($sqlOrderItems);

    }
}
