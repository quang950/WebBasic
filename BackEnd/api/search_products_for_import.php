<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/db_connect.php';

if (!$dbConnected || !$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// GET - Tìm kiếm sản phẩm để thêm vào phiếu nhập
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$limit = intval($_GET['limit'] ?? 20);

$query = "SELECT id, name, category, price, image_url, stock FROM products WHERE 1=1";
$params = [];
$types = '';

if ($search) {
    $query .= " AND (name LIKE ? OR category LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

$query .= " ORDER BY name ASC LIMIT ?";
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Đảm bảo hình ảnh có đường dẫn tuyệt đối
$products = array_map(function($product) {
    if (!empty($product['image_url'])) {
        $filename = basename($product['image_url']);
        $product['image_url'] = '/WebBasic/FrontEnd/assets/images/' . $filename;
    } else {
        $product['image_url'] = '/WebBasic/FrontEnd/assets/images/1.jpg';
    }
    return $product;
}, $products);

echo json_encode([
    'success' => true,
    'data' => $products,
    'count' => count($products)
]);

$conn->close();
?>
