<?php
/**
 * POST /BackEnd/api/place_order.php
 * Tạo đơn hàng mới
 */
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once __DIR__ . '/../config/db_connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate dữ liệu
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $address_id = isset($data['address_id']) ? intval($data['address_id']) : 0;
    $payment_method = isset($data['payment_method']) ? trim($data['payment_method']) : 'cash'; // cash, transfer, online
    $cart_items = isset($data['cart_items']) ? (array)$data['cart_items'] : [];

    // Validate
    if (!$user_id || !$address_id || empty($cart_items)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'user_id, address_id và cart_items bắt buộc'
        ]);
        exit;
    }

    // Validate payment method
    if (!in_array($payment_method, ['cash', 'transfer', 'online'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức thanh toán không hợp lệ'
        ]);
        exit;
    }

    // Kiểm tra user tồn tại
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User không tồn tại']);
        exit;
    }

    // Lấy thông tin địa chỉ giao hàng
    $stmt = $conn->prepare("
        SELECT recipient_name, phone, address_detail, ward, district, province, postal_code
        FROM user_shipping_addresses 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $address_id, $user_id);
    $stmt->execute();
    $shipping = $stmt->get_result()->fetch_assoc();
    
    if (!$shipping) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Địa chỉ giao hàng không tồn tại']);
        exit;
    }

    // Build shipping address string
    $shipping_address = $shipping['address_detail'] . ', ' . $shipping['ward'] . ', ' . $shipping['district'] . ', ' . $shipping['province'];
    if (!empty($shipping['postal_code'])) {
        $shipping_address .= ' ' . $shipping['postal_code'];
    }

    // Tính tổng tiền từ cart items
    $total_price = 0;
    $validated_items = [];
    
    foreach ($cart_items as $item) {
        $product_id = isset($item['product_id']) ? intval($item['product_id']) : 0;
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
        $price = isset($item['price']) ? floatval($item['price']) : 0;

        if (!$product_id || $quantity <= 0 || $price <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dữ liệu sản phẩm không hợp lệ']);
            exit;
        }

        // Kiểm tra sản phẩm tồn tại
        $pstmt = $conn->prepare("SELECT id, stock, price FROM products WHERE id = ?");
        $pstmt->bind_param("i", $product_id);
        $pstmt->execute();
        $product = $pstmt->get_result()->fetch_assoc();
        
        if (!$product) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => "Sản phẩm ID $product_id không tồn tại"]);
            exit;
        }

        // Kiểm tra tồn kho
        if ($product['stock'] < $quantity) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Sản phẩm ID $product_id không đủ hàng. Tồn kho: {$product['stock']}, Yêu cầu: $quantity"
            ]);
            exit;
        }

        $validated_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $product['price']
        ];

        $total_price += $product['price'] * $quantity;
    }

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Tạo order
        $stmt = $conn->prepare("
            INSERT INTO orders 
            (user_id, total_price, shipping_address, shipping_phone, shipping_ward, shipping_district, shipping_province, payment_method, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new')
        ");
        
        $stmt->bind_param(
            "idssssss",
            $user_id,
            $total_price,
            $shipping_address,
            $shipping['phone'],
            $shipping['ward'],
            $shipping['district'],
            $shipping['province'],
            $payment_method
        );
        
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Thêm order details và update stock
        foreach ($validated_items as $item) {
            // Thêm order detail
            $dstmt = $conn->prepare("
                INSERT INTO order_details (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $dstmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $dstmt->execute();

            // Update stock
            $ustmt = $conn->prepare("
                UPDATE products 
                SET stock = stock - ?, updated_at = NOW()
                WHERE id = ?
            ");
            $ustmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $ustmt->execute();

            // Record stock history
            $hstmt = $conn->prepare("
                INSERT INTO stock_history (product_id, type, quantity, reason, created_by)
                VALUES (?, 'export', ?, ?, ?)
            ");
            $reason = "Order #$order_id";
            $null_user = null;
            $hstmt->bind_param("iiss", $item['product_id'], $item['quantity'], $reason, $null_user);
            $hstmt->execute();
        }

        // Xóa cart items của user
        $cstmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $cstmt->bind_param("i", $user_id);
        $cstmt->execute();

        // Commit transaction
        $conn->commit();

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Tạo đơn hàng thành công',
            'data' => [
                'order_id' => $order_id,
                'user_id' => $user_id,
                'total_price' => $total_price,
                'payment_method' => $payment_method,
                'shipping_address' => $shipping_address,
                'status' => 'new',
                'items_count' => count($validated_items)
            ]
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>
