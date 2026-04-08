<?php
/**
 * Seed initial categories data into the database
 */

require_once __DIR__ . '/db_connect.php';

$categories = [
    ['name' => 'SUV', 'description' => 'Xe thể thao đa dụng'],
    ['name' => 'Sedan', 'description' => 'Xe sedan 4 cửa'],
    ['name' => 'MPV', 'description' => 'Xe đa dụng gia đình'],
    ['name' => 'Hatchback', 'description' => 'Xe hatchback gọn nhẹ'],
    ['name' => 'Bán tải', 'description' => 'Xe bán tải']
];

try {
    // Disable foreign key checks temporarily
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    
    // Clear existing products first (due to foreign key)
    $conn->query("DELETE FROM products");
    
    // Clear categories
    $conn->query("DELETE FROM categories");
    
    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    // Insert all categories
    $inserted = 0;
    foreach ($categories as $cat) {
        $stmt = $conn->prepare("INSERT INTO categories (name, description, status, is_visible) VALUES (?, ?, 1, 1)");
        $stmt->bind_param("ss", $cat['name'], $cat['description']);
        
        if ($stmt->execute()) {
            $inserted++;
            echo "✓ Đã thêm: " . $cat['name'] . "\n";
        } else {
            echo "✗ Lỗi: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
    
    echo "\n=== KẾT QUẢ ===\n";
    echo "✓ Đã seed thành công $inserted / " . count($categories) . " loại sản phẩm\n";
    
} catch (Exception $e) {
    echo "✗ Lỗi: " . $e->getMessage();
}

$conn->close();
?>

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
