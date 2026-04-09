<?php
/**
 * Admin API: Quản lý Số lượng tồn kho
 * GET /BackEnd/api/admin/get_stock_report.php
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/db_connect.php';

// Kiểm tra quyền Admin
$isAdmin = true;
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin'])) {
    $isAdmin = ($_SESSION['is_admin'] == 1);
}

if (!$isAdmin) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền.', 'data' => null]);
    exit;
}

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $action = $_GET['action'] ?? 'all';
    
    // Get all products with stock info
    if ($action === 'all') {
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.stock,
                p.price,
                p.brand,
                COALESCE(c.name, 'Uncategorized') as category_name,
                CASE 
                    WHEN p.stock <= 2 THEN 'lowStock'
                    WHEN p.stock <= 5 THEN 'normalStock'
                    ELSE 'goodStock'
                END as stock_level
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.stock ASC, p.name ASC
            LIMIT 500
        ";
        
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("Query error: " . $conn->error);
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'data' => ['products' => $products, 'count' => count($products)]]);
        exit;
    }
    
    // Get low stock products
    if ($action === 'low_stock') {
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 2;
        
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.stock,
                p.brand,
                COALESCE(c.name, 'Uncategorized') as category_name,
                p.price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.stock <= " . intval($threshold) . "
            ORDER BY p.stock ASC
            LIMIT 100
        ";
        
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("Query error: " . $conn->error);
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'data' => ['products' => $products, 'count' => count($products)]]);
        exit;
    }
    
    // Search product stock
    if ($action === 'search_stock') {
        $searchName = isset($_GET['searchName']) ? trim($_GET['searchName']) : '';
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        $searchPattern = '%' . $searchName . '%';
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.stock,
                p.brand,
                COALESCE(c.name, 'Uncategorized') as category_name,
                p.price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE ? OR p.brand LIKE ?
            ORDER BY 
                CASE 
                    WHEN p.name = ? OR p.brand = ? THEN 0
                    WHEN p.name LIKE CONCAT(?, '%') OR p.brand LIKE CONCAT(?, '%') THEN 1
                    ELSE 2
                END,
                p.name ASC
            LIMIT 50
        ";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss",
            $searchPattern, $searchPattern,
            $searchName, $searchName,
            $searchName, $searchName
        );
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'data' => ['products' => $products, 'count' => count($products)]]);
        exit;
    }
    
    // Historical stock at specific time
    if ($action === 'stock_at_time') {
        $searchName = isset($_GET['searchName']) ? $conn->real_escape_string($_GET['searchName']) : '';
        $targetDate = isset($_GET['targetDate']) ? $_GET['targetDate'] : date('Y-m-d H:i:s');
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        // Find product - prioritize exact/better matches
        $sql = "
            SELECT id, name, stock, price, brand
            FROM products
            WHERE name LIKE ? OR brand LIKE ?
            ORDER BY 
                CASE 
                    WHEN name = ? OR brand = ? THEN 0
                    WHEN name LIKE CONCAT(?, '%') OR brand LIKE CONCAT(?, '%') THEN 1
                    WHEN name LIKE CONCAT('%', ?) OR brand LIKE CONCAT('%', ?) THEN 2
                    ELSE 3
                END,
                name ASC
            LIMIT 1
        ";
        
        $searchPattern = '%' . $searchName . '%';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssss",
            $searchPattern, $searchPattern,
            $searchName, $searchName,
            $searchName, $searchName,
            $searchName, $searchName
        );
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Không tìm thấy sản phẩm phù hợp với '{$searchName}'");
        }

        $product = $result->fetch_assoc();
        $product_id = $product['id'];
        $current_stock = $product['stock'];
        
        // Calculate stock at target time by summing all import/export UP TO target date
        // Query 1: Get all imports UP TO target time
        $importSql = "
            SELECT COALESCE(SUM(ii.quantity), 0) as total_imported
            FROM import_items ii
            INNER JOIN import_tickets it ON ii.import_ticket_id = it.id
            WHERE ii.product_id = ? AND DATE(it.import_date) <= DATE(?) AND it.completed_at IS NOT NULL
        ";
        
        $stmt = $conn->prepare($importSql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("is", $product_id, $targetDate);
        $stmt->execute();
        $importResult = $stmt->get_result();
        $importData = $importResult->fetch_assoc();
        $total_imported = intval($importData['total_imported']);

        // Query 2: Get all exports (orders) UP TO target time
        $exportSql = "
            SELECT COALESCE(SUM(od.quantity), 0) as total_exported
            FROM order_details od
            INNER JOIN orders o ON od.order_id = o.id
            WHERE od.product_id = ? AND o.created_at <= ?
        ";
        
        $stmt2 = $conn->prepare($exportSql);
        $stmt2->bind_param("is", $product_id, $targetDate);
        $stmt2->execute();
        $exportResult = $stmt2->get_result();
        $exportData = $exportResult->fetch_assoc();
        $total_exported = intval($exportData['total_exported']);
        
        // Calculate: stock_at_time = current - (all_imports - all_exports after target time)
        // = current - (imports_after - exports_after)
        $allAfterImportSql = "
            SELECT COALESCE(SUM(ii.quantity), 0) as total_imported_after
            FROM import_items ii
            INNER JOIN import_tickets it ON ii.import_ticket_id = it.id
            WHERE ii.product_id = ? AND DATE(it.import_date) > DATE(?) AND it.completed_at IS NOT NULL
        ";
        
        $stmt3 = $conn->prepare($allAfterImportSql);
        $stmt3->bind_param("is", $product_id, $targetDate);
        $stmt3->execute();
        $afterImportResult = $stmt3->get_result();
        $afterImportData = $afterImportResult->fetch_assoc();
        $total_imported_after = intval($afterImportData['total_imported_after']);
        
        $allAfterExportSql = "
            SELECT COALESCE(SUM(od.quantity), 0) as total_exported_after
            FROM order_details od
            INNER JOIN orders o ON od.order_id = o.id
            WHERE od.product_id = ? AND o.created_at > ?
        ";
        
        $stmt4 = $conn->prepare($allAfterExportSql);
        $stmt4->bind_param("is", $product_id, $targetDate);
        $stmt4->execute();
        $afterExportResult = $stmt4->get_result();
        $afterExportData = $afterExportResult->fetch_assoc();
        $total_exported_after = intval($afterExportData['total_exported_after']);
        
        // stock_at_time = current_stock - (imports_after - exports_after)
        $net_change_after = $total_imported_after - $total_exported_after;
        $stock_at_time = $current_stock - $net_change_after;
        
        // Ensure non-negative
        if ($stock_at_time < 0) {
            $stock_at_time = 0;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'product_name' => $product['name'],
                'target_date' => $targetDate,
                'stock_at_time' => $stock_at_time,
                'current_stock' => $current_stock,
                'debug' => [
                    'product_id' => $product_id,
                    'total_imported_upto_date' => $total_imported,
                    'total_exported_upto_date' => $total_exported,
                    'total_imported_after_date' => $total_imported_after,
                    'total_exported_after_date' => $total_exported_after,
                    'net_change_after_target' => $net_change_after,
                    'calculation' => "current_stock($current_stock) - net_change_after($net_change_after) = $stock_at_time"
                ]
            ]
        ]);
        exit;
    }
    
    // Inventory report for date range
    if ($action === 'report_in_out') {
        $searchName = isset($_GET['searchName']) ? trim($_GET['searchName']) : '';
        $fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-d');
        $toDate = isset($_GET['toDate']) ? $_GET['toDate'] : date('Y-m-d');
        
        // Convert DATE to DATETIME if needed
        if (strlen($fromDate) === 10) {
            $fromDate = $fromDate . ' 00:00:00';
        }
        if (strlen($toDate) === 10) {
            $toDate = $toDate . ' 23:59:59';
        }
        
        if (empty($searchName)) {
            throw new Exception("Vui lòng nhập tên sản phẩm");
        }
        
        // Find product - search in both name and brand with exact match priority
        $searchPattern = '%' . $searchName . '%';
        $sql = "
            SELECT id, name, stock, price, brand
            FROM products
            WHERE name LIKE ? OR brand LIKE ?
            ORDER BY 
                CASE 
                    WHEN name = ? OR brand = ? THEN 0
                    WHEN name LIKE CONCAT(?, '%') OR brand LIKE CONCAT(?, '%') THEN 1
                    WHEN name LIKE CONCAT('%', ?) OR brand LIKE CONCAT('%', ?) THEN 2
                    ELSE 3
                END,
                name ASC
            LIMIT 1
        ";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssss", 
            $searchPattern, $searchPattern,
            $searchName, $searchName,
            $searchName, $searchName,
            $searchName, $searchName
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute error: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Không tìm thấy sản phẩm phù hợp với '{$searchName}'");
        }
        
        $product = $result->fetch_assoc();
        $product_id = $product['id'];
        $current_stock = $product['stock'];
        
        // Get total imports from import_items IN date range
        // Use import_tickets.import_date (date when ticket is completed) instead of created_at (when draft was created)
        $importInSql = "
            SELECT COALESCE(SUM(ii.quantity), 0) as total_imported
            FROM import_items ii
            INNER JOIN import_tickets it ON ii.import_ticket_id = it.id
            WHERE ii.product_id = ? 
            AND DATE(it.import_date) >= DATE(?)
            AND DATE(it.import_date) <= DATE(?)
            AND it.completed_at IS NOT NULL
        ";
        
        $stmt = $conn->prepare($importInSql);
        if (!$stmt) {
            throw new Exception("Lỗi prepare query import in: " . $conn->error);
        }
        $stmt->bind_param("iss", $product_id, $fromDate, $toDate);
        $stmt->execute();
        $importInResult = $stmt->get_result();
        $importInData = $importInResult->fetch_assoc();
        $total_import_in = intval($importInData['total_imported']);
        
        // Get total exports from orders IN date range
        // Use order_details.created_at instead of orders.created_at
        $exportInSql = "
            SELECT COALESCE(SUM(od.quantity), 0) as total_exported
            FROM order_details od
            WHERE od.product_id = ? AND od.created_at >= ? AND od.created_at <= ?
        ";
        
        $stmt = $conn->prepare($exportInSql);
        $stmt->bind_param("iss", $product_id, $fromDate, $toDate);
        $stmt->execute();
        $exportInResult = $stmt->get_result();
        $exportInData = $exportInResult->fetch_assoc();
        $total_export_in = intval($exportInData['total_exported']);
        
        // Calculate stock_begin = current - (import_in - export_in)
        $net_change_in = $total_import_in - $total_export_in;
        $stock_begin = $current_stock - $net_change_in;
        $stock_end = $current_stock;
        
        if ($stock_begin < 0) $stock_begin = 0;
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'product_name' => $product['name'],
                'product_id' => $product_id,
                'period_from' => str_replace(' 00:00:00', '', $fromDate),
                'period_to' => str_replace(' 23:59:59', '', $toDate),
                'stock_begin' => intval($stock_begin),
                'total_import' => $total_import_in,
                'total_sale' => $total_export_in,
                'net_change' => $net_change_in,
                'stock_end' => intval($stock_end),
                'current_stock' => intval($current_stock)
            ]
        ]);
        exit;
    }
    
    throw new Exception("Action not found: $action");

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'data' => null]);
}
?>