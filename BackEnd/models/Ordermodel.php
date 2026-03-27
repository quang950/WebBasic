<?php
require_once __DIR__ . '/../config/db_connect.php';

class OrderModel {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function createOrder($user_id, $address, $payment_method) {
        try {
            //  BẮT ĐẦU TRANSACTION
            $this->conn->beginTransaction();

            //  1. Lấy giỏ hàng
            $stmt = $this->conn->prepare(
                "SELECT c.product_id, c.quantity, p.price, p.stock
                 FROM cart c
                 JOIN products p ON c.product_id = p.id
                 WHERE c.user_id = ?"
            );
            $stmt->execute([$user_id]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($cartItems)) {
                return ["error" => "Cart is empty"];
            }

            //  2. Check stock + tính tổng tiền
            $total = 0;
            foreach ($cartItems as $item) {
                if ($item['quantity'] > $item['stock']) {
                    return ["error" => "Product out of stock"];
                }
                $total += $item['price'] * $item['quantity'];
            }

            //  3. Tạo order
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (user_id, total_price, address, payment_method)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$user_id, $total, $address, $payment_method]);

            $order_id = $this->conn->lastInsertId();

            //  4. Lưu order_details + trừ kho
            foreach ($cartItems as $item) {
                // lưu chi tiết
                $stmt = $this->conn->prepare(
                    "INSERT INTO order_details (order_id, product_id, quantity, price)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);

                // trừ kho
                $stmt = $this->conn->prepare(
                    "UPDATE products 
                     SET stock = stock - ? 
                     WHERE id = ?"
                );
                $stmt->execute([
                    $item['quantity'],
                    $item['product_id']
                ]);
            }

            //  5. Xoá giỏ hàng
            $stmt = $this->conn->prepare(
                "DELETE FROM cart WHERE user_id = ?"
            );
            $stmt->execute([$user_id]);

            //  COMMIT
            $this->conn->commit();

            return [
                "message" => "Order created successfully",
                "order_id" => $order_id
            ];

        } catch (Exception $e) {
            //  ROLLBACK nếu lỗi
            $this->conn->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
    // 6. Lấy lịch sử mua hàng (Yêu cầu: Đơn hàng mới nhất ở trên cùng)
    public function getOrderHistory($user_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM orders 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC" // DESC đảm bảo đơn mới nhất lên đầu
            );
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    // 7. Lấy chi tiết đơn hàng (Dùng để hiển thị tóm tắt đơn hàng sau khi mua)
    public function getOrderDetails($order_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT od.*, p.name as product_name, p.image_url 
                 FROM order_details od
                 JOIN products p ON od.product_id = p.id
                 WHERE od.order_id = ?"
            );
            $stmt->execute([$order_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}