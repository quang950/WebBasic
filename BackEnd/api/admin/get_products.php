<?php
/**
 * Admin API: Get all products
 * GET /BackEnd/api/admin/get_products.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    $search = $_GET['search'] ?? '';
    
    // Build query
    $sql = "
        SELECT p.id, p.name, p.price, p.description, p.image_url as image,
               c.name as category, p.stock
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
    ";
    
    if (!empty($search)) {
        $search = "%$search%";
        $sql .= " WHERE p.name LIKE ?";
    }
    
    $sql .= " ORDER BY p.id DESC LIMIT 100";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($search)) {
        $stmt->bind_param("s", $search);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Parse brand from name
        $brands = ['toyota', 'honda', 'bmw', 'mercedes', 'audi', 'lexus', 'hyundai', 'kia', 'vinfast'];
        $brand = 'unknown';
        foreach ($brands as $b) {
            if (stripos($row['name'], $b) !== false) {
                $brand = $b;
                break;
            }
        }
        
        $row['brand'] = $brand;
        $row['year'] = date('Y');
        $row['fuel'] = 'Xăng';
        $row['transmission'] = 'Tự động';
        
        // Đảm bảo hình ảnh có đường dẫn tuyệt đối - chỉ lấy tên file nếu có đường dẫn sai
        if (!empty($row['image'])) {
            // Extract filename from any path
            $filename = basename($row['image']);
            $row['image'] = '/WebBasic/FrontEnd/assets/images/' . $filename;
        } else {
            $row['image'] = '/WebBasic/FrontEnd/assets/images/1.jpg';
        }
        
        $products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
