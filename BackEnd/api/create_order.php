<?php
/**
 * POST /BackEnd/api/create_order.php
 * Tạo đơn hàng mới từ giỏ hàng
 * Yêu cầu: user_id từ session
 */
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../config/db_connect.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Kiểm tra user đã đăng nhập
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập trước'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $user_id = intval($_SESSION['user_id']);

    // Lấy dữ liệu từ request
    $data = json_decode(file_get_contents('php://input'), true);
    
    // DEBUG: Log incoming data
    error_log("create_order.php received: " . json_encode($data, JSON_UNESCAPED_UNICODE));
    
    // Validate dữ liệu
    $receiver_name = trim($data['receiver_name'] ?? '');  // <-- Thêm dòng này
    $shipping_address = trim($data['shipping_address'] ?? '');
    $shipping_phone = trim($data['shipping_phone'] ?? '');
    $payment_method = trim($data['payment_method'] ?? 'cash');
    $cart_items = isset($data['cart_items']) ? (array)$data['cart_items'] : [];
    
    error_log("Cart items count: " . count($cart_items));

    if (!$shipping_address || !$shipping_phone || empty($cart_items)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Thông tin giao hàng, số điện thoại và giỏ hàng bắt buộc'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate payment method
    $valid_methods = ['cash', 'bank', 'online'];
    if (!in_array($payment_method, $valid_methods)) {
        $payment_method = 'cash';
    }

    // Kiểm tra user tồn tại
    $stmt = $conn->prepare("SELECT id, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User không tồn tại'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Tính tổng tiền và validate cart items
    $total_price = 0;
    $validated_items = [];
    
    foreach ($cart_items as $item) {
        error_log("Processing item: " . json_encode($item, JSON_UNESCAPED_UNICODE));
        
        $product_id = isset($item['product_id']) ? intval($item['product_id']) : 0;
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
        $price = isset($item['price']) ? floatval($item['price']) : 
                (isset($item['unit_price']) ? floatval($item['unit_price']) : 0);
        $name = trim($item['name'] ?? '');

        error_log("Extracted: product_id=$product_id, quantity=$quantity, price=$price, name=$name");

        if (!$product_id || $quantity <= 0) {
            error_log("Validation failed: product_id or quantity invalid");
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Sản phẩm không hợp lệ: $name (ID: $product_id)"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Kiểm tra sản phẩm tồn tại
        $pstmt = $conn->prepare("SELECT id, stock, price, name FROM products WHERE id = ?");
        $pstmt->bind_param("i", $product_id);
        $pstmt->execute();
        $product = $pstmt->get_result()->fetch_assoc();
        
        if (!$product) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => "Sản phẩm ID $product_id không tồn tại"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Nếu không có price từ request, lấy từ database
        if (!$price) {
            $price = floatval($product['price']);
        }

        // Kiểm tra tồn kho
        if ($product['stock'] < $quantity) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Sản phẩm '{$product['name']}' không đủ hàng. Tồn kho: {$product['stock']}, Yêu cầu: $quantity"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $validated_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'unit_price' => $price,
            'item_total' => $price * $quantity
        ];
        
        $total_price += ($price * $quantity);
    }

    if (empty($validated_items)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Giỏ hàng không có sản phẩm hợp lệ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Tạo đơn hàng
        $order_status = 'new';
        
        // Parse shipping address to extract ward, district, province
        $addressParts = array_map('trim', explode(',', $shipping_address));
        $shipping_street = $addressParts[0] ?? '';
        $shipping_ward = $addressParts[1] ?? '';
        $shipping_district = $addressParts[2] ?? '';
        $shipping_province = $addressParts[3] ?? '';
        
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, shipping_name, shipping_address, shipping_phone, shipping_ward, shipping_district, shipping_province, status, payment_method, total_price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "issssssssd",
            $user_id,
            $receiver_name,
            $shipping_address,
            $shipping_phone,
            $shipping_ward,
            $shipping_district,
            $shipping_province,
            $order_status,
            $payment_method,
            $total_price
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Không thể tạo đơn hàng: " . $stmt->error);
        }

        $order_id = $conn->insert_id;

        // Thêm chi tiết đơn hàng (order_details table)
        $stmt = $conn->prepare("
            INSERT INTO order_details (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($validated_items as $item) {
            $stmt->bind_param(
                "iiid",
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['unit_price']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Không thể thêm chi tiết đơn hàng: " . $stmt->error);
            }

            // Cập nhật tồn kho
            $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $update_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            if (!$update_stmt->execute()) {
                throw new Exception("Không thể cập nhật tồn kho: " . $update_stmt->error);
            }
        }

        // Xóa giỏ hàng của user
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Lưu địa chỉ giao hàng mới vào user_shipping_addresses (nếu chưa tồn tại)
        // Wrapped in try-catch để không làm gián đoạn tạo đơn nếu có lỗi
        try {
            $stmt = $conn->prepare("
                SELECT id FROM user_shipping_addresses 
                WHERE user_id = ? AND address_detail = ? AND ward = ? AND district = ? AND province = ?
            ");
            if ($stmt) {
                $stmt->bind_param("issss", $user_id, $shipping_street, $shipping_ward, $shipping_district, $shipping_province);
                $stmt->execute();
                $existing_addr = $stmt->get_result();
                
                if ($existing_addr && $existing_addr->num_rows === 0) {
                    // Địa chỉ chưa tồn tại - thêm vào
                    $recipient_name = trim($data['receiver_name'] ?? 'Khách hàng');
                    $postal_code = trim($data['postal_code'] ?? '');
                    
                    $insert_stmt = $conn->prepare("
                        INSERT INTO user_shipping_addresses 
                        (user_id, recipient_name, phone, address_detail, ward, district, province, postal_code, is_default)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
                    ");
                    if ($insert_stmt) {
                        $insert_stmt->bind_param(
                            "isssssss",
                            $user_id,
                            $recipient_name,
                            $shipping_phone,
                            $shipping_street,
                            $shipping_ward,
                            $shipping_district,
                            $shipping_province,
                            $postal_code
                        );
                        $insert_stmt->execute();
                        error_log("New address saved for user $user_id");
                    }
                }
            }
        } catch (Exception $addr_error) {
            error_log("Warning: Could not save address - " . $addr_error->getMessage());
            // Continue anyway - order was already created
        }

        // Commit transaction
        $conn->commit();

        // Trả về kết quả thành công
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Đơn hàng được tạo thành công',
            'order_id' => $order_id,
            'total_price' => $total_price,
            'order_date' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
