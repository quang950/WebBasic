<?php
require_once __DIR__ . '/../config/db_connect.php';

class CartModel {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    // Thêm vào giỏ hàng (chuẩn theo UNIQUE KEY)
    public function add($user_id, $product_id, $quantity) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO cart (user_id, product_id, quantity)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                 quantity = quantity + VALUES(quantity)"
            );

            $stmt->execute([$user_id, $product_id, $quantity]);

            return ["message" => "Added to cart"];

        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // Lấy giỏ hàng
    public function getByUser($user_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT 
                    c.id as cart_id,
                    p.name,
                    p.price,
                    p.image_url,
                    p.stock,
                    c.quantity,
                    (p.price * c.quantity) as total
                 FROM cart c
                 JOIN products p ON c.product_id = p.id
                 WHERE c.user_id = ?"
            );

            $stmt->execute([$user_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // Cập nhật số lượng
    public function update($cart_id, $quantity) {
        try {
            // Không cho vượt quá tồn kho
            $stmt = $this->conn->prepare(
                "UPDATE cart c
                 JOIN products p ON c.product_id = p.id
                 SET c.quantity = ?
                 WHERE c.id = ? AND ? <= p.stock"
            );

            $stmt->execute([$quantity, $cart_id, $quantity]);

            if ($stmt->rowCount() === 0) {
                return ["error" => "Quantity exceeds stock"];
            }

            return ["message" => "Cart updated"];

        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    //  Xoá sản phẩm
    public function delete($cart_id) {
        try {
            $stmt = $this->conn->prepare(
                "DELETE FROM cart WHERE id = ?"
            );

            $stmt->execute([$cart_id]);

            return ["message" => "Item removed"];

        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    //  Xoá toàn bộ giỏ (sẽ dùng khi đặt hàng)
    public function clearCart($user_id) {
        try {
            $stmt = $this->conn->prepare(
                "DELETE FROM cart WHERE user_id = ?"
            );

            $stmt->execute([$user_id]);

            return ["message" => "Cart cleared"];

        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function updateQuantity($cart_id, $quantity) {
    if ($quantity <= 0) {
        // nếu số lượng = 0 thì xoá luôn
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE id = ?");
        return $stmt->execute([$cart_id]);
    }

    $stmt = $this->conn->prepare("
        UPDATE cart 
        SET quantity = ? 
        WHERE id = ?
    ");

    return $stmt->execute([$quantity, $cart_id]);
    }
}