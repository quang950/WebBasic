<?php
require_once __DIR__ . '/../config/db_connect.php';

class CartModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function add($user_id, $product_id, $quantity) {
        $stmt = $this->conn->prepare("
            INSERT INTO cart(user_id, product_id, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
        ");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        return $stmt->execute();
    }

    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("
            SELECT c.id, p.name, p.price, c.quantity
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function update($cart_id, $quantity) {
        if ($quantity <= 0) {
            $stmt = $this->conn->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->bind_param("i", $cart_id);
            return $stmt->execute();
        }

        $stmt = $this->conn->prepare("
            UPDATE cart SET quantity = ? WHERE id = ?
        ");
        $stmt->bind_param("ii", $quantity, $cart_id);
        return $stmt->execute();
    }
}