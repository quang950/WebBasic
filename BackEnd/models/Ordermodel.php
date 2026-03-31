<?php
require_once __DIR__ . '/../config/db_connect.php';

class OrderModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // TẠO ĐƠN HÀNG (CHECKOUT)
    public function createOrder($user_id, $address, $phone, $payment, $cart_items = []) {
        try {
            $this->conn->begin_transaction();

            // Nếu không có items từ frontend, lấy từ database
            if (empty($cart_items)) {
                $stmt = $this->conn->prepare("
                    SELECT c.product_id, c.quantity, p.name, p.price 
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

                $items = [];
                while ($row = $cart->fetch_assoc()) {
                    $items[] = $row;
                }
            } else {
                // Sử dụng cart items từ frontend
                // Cần lấy product_id từ database dựa trên product name hoặc ID
                $items = [];
                foreach ($cart_items as $item) {
                    // Tìm product_id từ product name
                    $stmtProd = $this->conn->prepare("
                        SELECT id, price FROM products WHERE name = ?
                    ");
                    
                    if (!$stmtProd) {
                        throw new Exception("Prepare product lookup failed: " . $this->conn->error);
                    }
                    
                    $stmtProd->bind_param("s", $item['name']);
                    
                    if (!$stmtProd->execute()) {
                        throw new Exception("Execute product lookup failed: " . $stmtProd->error);
                    }
                    
                    $prodResult = $stmtProd->get_result();
                    
                    if ($prodResult->num_rows == 0) {
                        throw new Exception("Sản phẩm '{$item['name']}' không tồn tại trong database");
                    }
                    
                    $prod = $prodResult->fetch_assoc();
                    $items[] = [
                        'product_id' => $prod['id'],
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity']
                    ];
                    $stmtProd->close();
                }
            }

            // 2. Tính tổng tiền
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // 3. Tạo order
            $stmtOrder = $this->conn->prepare("
                INSERT INTO orders(user_id, total_price, shipping_address, shipping_phone, payment_method)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            if (!$stmtOrder) {
                throw new Exception("Prepare order failed: " . $this->conn->error);
            }
            
            $total_str = (string)$total;
            $stmtOrder->bind_param("issss", $user_id, $total_str, $address, $phone, $payment);
            
            if (!$stmtOrder->execute()) {
                throw new Exception("Insert order failed: " . $stmtOrder->error);
            }

            $order_id = $this->conn->insert_id;

            // 4. Thêm order_details
            foreach ($items as $row) {
                // 1. Trừ kho
                $this->subtractStock($row['product_id'], $row['quantity']);

                //  2. Lưu chi tiết đơn hàng
                $stmtItem = $this->conn->prepare("
                    INSERT INTO order_details(order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");
                
                if (!$stmtItem) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                
                $price_str = (string)$row['price'];
                $stmtItem->bind_param(
                    "iiis",
                    $order_id,
                    $row['product_id'],
                    $row['quantity'],
                    $price_str
                );
                
                if (!$stmtItem->execute()) {
                    throw new Exception("Insert order_details failed: " . $stmtItem->error);
                }
                
                $stmtItem->close();
            }

            // 5. Xoá giỏ hàng
            $stmtClear = $this->conn->prepare("
                DELETE FROM cart WHERE user_id = ?
            ");
            
            if (!$stmtClear) {
                throw new Exception("Prepare delete cart failed: " . $this->conn->error);
            }
            
            $stmtClear->bind_param("i", $user_id);
            
            if (!$stmtClear->execute()) {
                throw new Exception("Execute delete cart failed: " . $stmtClear->error);
            }
            
            $stmtClear->close();

            $this->conn->commit();

            return $order_id;

        } catch (Exception $e) {
            $this->conn->rollback();
            return ["error" => $e->getMessage()];
        }
    }

    // LẤY LỊCH SỬ ĐƠN HÀNG
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
                SELECT product_id, quantity, price
                FROM order_details
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

    // PREVIEW ĐƠN HÀNG (TRƯỚC KHI ĐẶT)
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
    //  TRỪ KHO SẢN PHẨM
    private function subtractStock($productId, $quantity)
    {
        $stmt = $this->conn->prepare("
            UPDATE products 
            SET stock = stock - ?
            WHERE id = ? AND stock >= ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare subtractStock failed: " . $this->conn->error);
        }

        $stmt->bind_param("iii", $quantity, $productId, $quantity);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute subtractStock failed: " . $stmt->error);
        }

        // Nếu không update được dòng nào => hết hàng
        if ($stmt->affected_rows === 0) {
            throw new Exception("Sản phẩm ID $productId không đủ số lượng");
        }

        $stmt->close();
    }
    // HOÀN KHO KHI HUỶ ĐƠN HÀNG
    private function restoreStock($productId, $quantity)
    {
        $stmt = $this->conn->prepare("
            UPDATE products
            SET stock = stock + ?
            WHERE id = ?
        ");

        $stmt->bind_param("ii", $quantity, $productId);
        $stmt->execute();
        $stmt->close();
    }
    // HUỶ ĐƠN HÀNG + HOÀN KHO
    public function cancelOrder($orderId)
    {
        try {
            $this->conn->begin_transaction();

            // 1️ Kiểm tra trạng thái đơn
            $stmt = $this->conn->prepare("
                SELECT status FROM orders WHERE id = ?
            ");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();

            if (!$order) {
                throw new Exception("Đơn hàng không tồn tại");
            }

            if ($order['status'] === 'cancelled') {
                throw new Exception("Đơn hàng đã bị huỷ trước đó");
            }

            // 2️ Lấy các sản phẩm trong đơn
            $stmtItems = $this->conn->prepare("
                SELECT od.product_id, od.quantity
                FROM order_details od
                WHERE od.order_id = ?
            ");
            $stmtItems->bind_param("i", $orderId);
            $stmtItems->execute();
            $items = $stmtItems->get_result();

            // 3️ Hoàn kho cho từng sản phẩm
            while ($item = $items->fetch_assoc()) {
                $this->restoreStock($item['product_id'], $item['quantity']);
            }

            // 4️ Cập nhật trạng thái đơn
            $stmtUpdate = $this->conn->prepare("
                UPDATE orders
                SET status = 'cancelled'
                WHERE id = ?
            ");
            $stmtUpdate->bind_param("i", $orderId);
            $stmtUpdate->execute();

            // 5️ Commit transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}