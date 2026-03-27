<?php
require_once __DIR__ . '/../config/db_connect.php';

class OrderModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // 🔥 1. TẠO ĐƠN HÀNG (CHECKOUT)
    public function createOrder($user_id, $address, $phone, $payment) {
        try {
            $this->conn->begin_transaction();

            // 1. Lấy giỏ hàng
            $stmt = $this->conn->prepare("
                SELECT c.*, p.name, p.price 
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart = $stmt->get_result();

            if ($cart->num_rows == 0) {
                throw new Exception("Cart is empty");
            }

            // 2. Tính tổng tiền
            $total = 0;
            $items = [];
            while ($row = $cart->fetch_assoc()) {
                $total += $row['price'] * $row['quantity'];
                $items[] = $row;
            }

            // 3. Tạo order
            $stmtOrder = $this->conn->prepare("
                INSERT INTO orders(user_id, total_price, shipping_address, shipping_phone, payment_method)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmtOrder->bind_param("idsss", $user_id, $total, $address, $phone, $payment);
            $stmtOrder->execute();

            $order_id = $this->conn->insert_id;

            // 4. Thêm order_items
            foreach ($items as $row) {
                $stmtItem = $this->conn->prepare("
                    INSERT INTO order_items(order_id, product_name, quantity, unit_price)
                    VALUES (?, ?, ?, ?)
                ");
                $stmtItem->bind_param(
                    "isid",
                    $order_id,
                    $row['name'],
                    $row['quantity'],
                    $row['price']
                );
                $stmtItem->execute();
            }

            // 5. Xoá giỏ hàng
            $stmtClear = $this->conn->prepare("
                DELETE FROM cart WHERE user_id = ?
            ");
            $stmtClear->bind_param("i", $user_id);
            $stmtClear->execute();

            $this->conn->commit();

            return $order_id;

        } catch (Exception $e) {
            $this->conn->rollback();
            return ["error" => $e->getMessage()];
        }
    }

    // 🔥 2. LẤY LỊCH SỬ ĐƠN HÀNG
    public function getOrdersByUser($user_id) {
        $stmt = $this->conn->prepare("
            SELECT id, total_price, shipping_address, shipping_phone, payment_method, status, created_at
            FROM orders
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = [];

        while ($order = $result->fetch_assoc()) {
            $order_id = $order['id'];

            // Lấy item
            $stmtItems = $this->conn->prepare("
                SELECT product_name, quantity, unit_price
                FROM order_items
                WHERE order_id = ?
            ");
            $stmtItems->bind_param("i", $order_id);
            $stmtItems->execute();
            $itemsResult = $stmtItems->get_result();

            $items = [];
            while ($item = $itemsResult->fetch_assoc()) {
                $items[] = $item;
            }

            $order['items'] = $items;
            $orders[] = $order;
        }

        return $orders;
    }

    // 🔥 3. PREVIEW ĐƠN HÀNG (TRƯỚC KHI ĐẶT)
    public function previewOrder($user_id) {
        $stmt = $this->conn->prepare("
            SELECT c.quantity, p.name, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        $total = 0;

        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;

            $row['subtotal'] = $subtotal;
            $items[] = $row;
        }

        return [
            "items" => $items,
            "total_price" => $total
        ];
    }
}