<?php
/**
 * Admin API: Quản lý Số lượng tồn kho & Báo cáo
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
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
    
    $action = $_GET['action'] ?? '';
    
    if ($action === 'low_stock') {
        // Cảnh báo sắp hết hàng
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 2;
        
        $stmt = $pdo->prepare("
            SELECT p.id, p.product_code, p.name, p.stock, c.name as category 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.stock <= :threshold AND p.status = 1
            ORDER BY p.stock ASC
        ");
        $stmt->execute([':threshold' => $threshold]);
        $products = $stmt->fetchAll();
        
        echo json_encode(['status' => 'success', 'data' => $products]);

    } elseif ($action === 'stock_at_time') {
        // Lấy tồn kho tại một thời điểm quá khứ
        $productId = $_GET['searchName'] ?? '';
        $targetDate = $_GET['targetDate'] ?? ''; // Format: YYYY-MM-DD HH:ii:ss
        
        if (empty($targetDate) || empty($productId)) {
            throw new Exception("Vui lòng cung cấp mã/tên sản phẩm và thời gian cần tra cứu.");
        }

        // Tìm Id sản phẩm trước tiên để đảm bảo chính xác
        $stmtSearch = $pdo->prepare("SELECT id, name, stock FROM products WHERE product_code = :kw OR name LIKE :kwLIKE LIMIT 1");
        $stmtSearch->execute([':kw' => $productId, ':kwLIKE' => "%$productId%"]);
        $productInfo = $stmtSearch->fetch();

        if (!$productInfo) {
            throw new Exception("Không tìm thấy sản phẩm này");
        }
        $pid = $productInfo['id'];

        // Công thức: Tồn hiện tại - (Các lượng nhập SAU ngày T) + (Các lượng xuất SAU ngày T) = Tồn kho TẠI NGÀY T
        $stmtHistory = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END) as total_import_after,
                SUM(CASE WHEN type = 'export' THEN quantity ELSE 0 END) as total_export_after
            FROM stock_history 
            WHERE product_id = :pid AND created_at > :targetDate
        ");
        $stmtHistory->execute([':pid' => $pid, ':targetDate' => $targetDate]);
        $calcData = $stmtHistory->fetch();

        $importAfter = $calcData['total_import_after'] ?? 0;
        $exportAfter = $calcData['total_export_after'] ?? 0;

        $stockAtTime = $productInfo['stock'] - $importAfter + $exportAfter;

        echo json_encode([
            'status' => 'success', 
            'data' => [
                'product_name' => $productInfo['name'],
                'target_date' => $targetDate,
                'stock_at_time' => $stockAtTime,
                'current_stock' => $productInfo['stock']
            ]
        ]);

    } elseif ($action === 'report_in_out') {
        // Báo cáo nhập xuất trong 1 khoảng thời gian
        $productId = $_GET['searchName'] ?? '';
        $fromDate = $_GET['fromDate'] ?? '';
        $toDate = $_GET['toDate'] ?? ''; 

        if (empty($fromDate) || empty($toDate) || empty($productId)) {
            throw new Exception("Vui lòng cung cấp đầy đủ Mốc thời gian và Sản phẩm.");
        }

        $toDate = $toDate . ' 23:59:59'; // Bao phủ tới cuối ngày

        $stmtSearch = $pdo->prepare("SELECT id, name FROM products WHERE product_code = :kw OR name LIKE :kwLIKE LIMIT 1");
        $stmtSearch->execute([':kw' => $productId, ':kwLIKE' => "%$productId%"]);
        $productInfo = $stmtSearch->fetch();

        if (!$productInfo) {
            throw new Exception("Không tìm thấy sản phẩm");
        }
        $pid = $productInfo['id'];

        // Lấy Tổng Nhập Xuất trong Khoảng Thời gian
        $stmtRange = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END) as total_import,
                SUM(CASE WHEN type = 'export' THEN quantity ELSE 0 END) as total_export
            FROM stock_history 
            WHERE product_id = :pid AND created_at >= :fromDate AND created_at <= :toDate
        ");
        $stmtRange->execute([':pid' => $pid, ':fromDate' => $fromDate, ':toDate' => $toDate]);
        $rangeData = $stmtRange->fetch();

        // Lấy Tồn Đầu Kỳ ( = Tồn hiện tại - (tất cả Nhập sau T1) + (tất cả Xuất sau T1) )
        $stmtHistoryBefore = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END) as import_after_T1,
                SUM(CASE WHEN type = 'export' THEN quantity ELSE 0 END) as export_after_T1
            FROM stock_history 
            WHERE product_id = :pid AND created_at >= :fromDate
        ");
        $stmtHistoryBefore->execute([':pid' => $pid, ':fromDate' => $fromDate]);
        $calcBefore = $stmtHistoryBefore->fetch();

        // Query tồn hiện tại
        $stmtCurrent = $pdo->query("SELECT stock FROM products WHERE id = " . (int)$pid);
        $currentStock = $stmtCurrent->fetchColumn();

        $stockBegin = $currentStock - ($calcBefore['import_after_T1'] ?? 0) + ($calcBefore['export_after_T1'] ?? 0);
        $totalImport = $rangeData['total_import'] ?? 0;
        $totalExport = $rangeData['total_export'] ?? 0;
        $stockEnd = $stockBegin + $totalImport - $totalExport;

        echo json_encode([
            'status' => 'success', 
            'data' => [
                'product_name' => $productInfo['name'],
                'stock_begin' => $stockBegin,
                'total_import' => $totalImport,
                'total_export' => $totalExport,
                'stock_end' => $stockEnd
            ]
        ]);

    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'data' => null]);
}
?>