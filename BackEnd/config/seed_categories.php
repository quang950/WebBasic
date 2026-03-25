<?php
/**
 * Seed initial categories data into the database
 */

require_once __DIR__ . '/db_connect.php';

$categories = [
    ['name' => 'Toyota', 'description' => 'Thương hiệu xe Nhật Bản uy tín'],
    ['name' => 'Mercedes', 'description' => 'Thương hiệu xe Đức cao cấp'],
    ['name' => 'BMW', 'description' => 'Xe Đức thể thao sang trọng'],
    ['name' => 'Audi', 'description' => 'Xe Đức công nghệ cao'],
    ['name' => 'Lexus', 'description' => 'Xe Nhật cao cấp'],
    ['name' => 'Honda', 'description' => 'Xe Nhật bền bỉ tiết kiệm'],
    ['name' => 'Hyundai', 'description' => 'Xe Hàn Quốc hiện đại'],
    ['name' => 'Kia', 'description' => 'Xe Hàn Quốc thời trang'],
    ['name' => 'VinFast', 'description' => 'Xe điện Việt Nam'],
    ['name' => 'Mazda', 'description' => 'Xe Nhật thiết kế đẹp'],
    ['name' => 'Ford', 'description' => 'Xe Mỹ mạnh mẽ'],
    ['name' => 'Chevrolet', 'description' => 'Xe Mỹ đa dạng'],
    ['name' => 'Nissan', 'description' => 'Xe Nhật công nghệ'],
    ['name' => 'Mitsubishi', 'description' => 'Xe Nhật bền bỉ'],
    ['name' => 'Suzuki', 'description' => 'Xe Nhật nhỏ gọn'],
    ['name' => 'Subaru', 'description' => 'Xe Nhật off-road'],
    ['name' => 'Volkswagen', 'description' => 'Xe Đức phổ thông'],
    ['name' => 'Porsche', 'description' => 'Xe Đức siêu sang'],
    ['name' => 'Volvo', 'description' => 'Xe Thụy Điển an toàn'],
    ['name' => 'Land Rover', 'description' => 'Xe Anh địa hình']
];

try {
    // Clear existing categories first
    $conn->query("TRUNCATE TABLE categories");
    
    // Insert all categories
    $inserted = 0;
    foreach ($categories as $cat) {
        $stmt = $conn->prepare("INSERT INTO categories (name, description, is_visible) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $cat['name'], $cat['description']);
        
        if ($stmt->execute()) {
            $inserted++;
        } else {
            echo "Failed to insert {$cat['name']}: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Successfully seeded $inserted categories",
        'total' => count($categories)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
