<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db_connect.php';

if (!$dbConnected) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

function hasColumn($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $result && $result->num_rows > 0;
}

function normalizedTicketStatus($ticket, $hasStatusColumn) {
    if ($hasStatusColumn && isset($ticket['status'])) {
        return $ticket['status'];
    }

    return !empty($ticket['completed_at']) ? 'completed' : 'draft';
}

$hasTicketStatusColumn = hasColumn($conn, 'import_tickets', 'status');
$ticketTotalColumn = hasColumn($conn, 'import_tickets', 'total_import_price')
    ? 'total_import_price'
    : (hasColumn($conn, 'import_tickets', 'total_amount') ? 'total_amount' : null);

function refreshTicketTotal($conn, $ticketId, $ticketTotalColumn) {
    if (!$ticketTotalColumn) {
        return true;
    }

    $sumStmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) AS total FROM import_items WHERE import_ticket_id = ?");
    if (!$sumStmt) {
        return false;
    }

    $sumStmt->bind_param('i', $ticketId);
    $sumStmt->execute();
    $sumResult = $sumStmt->get_result()->fetch_assoc();
    $sumStmt->close();

    $total = floatval($sumResult['total'] ?? 0);

    $updateStmt = $conn->prepare("UPDATE import_tickets SET `{$ticketTotalColumn}` = ? WHERE id = ?");
    if (!$updateStmt) {
        return false;
    }

    $updateStmt->bind_param('di', $total, $ticketId);
    $ok = $updateStmt->execute();
    $updateStmt->close();

    return $ok;
}

function detectProductCostColumn($conn) {
    $result = $conn->query("SHOW COLUMNS FROM products LIKE 'cost_price'");
    if ($result && $result->num_rows > 0) {
        return 'cost_price';
    }

    $result = $conn->query("SHOW COLUMNS FROM products LIKE 'price_cost'");
    if ($result && $result->num_rows > 0) {
        return 'price_cost';
    }

    return null;
}

// GET - Lấy danh sách phiếu nhập hoặc chi tiết phiếu
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    
    if ($action === 'list') {
        // Lấy danh sách phiếu nhập
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['dateFrom'] ?? '';
        $dateTo = $_GET['dateTo'] ?? '';
        
        $statusExpr = $hasTicketStatusColumn
            ? "t.status"
            : "CASE WHEN t.completed_at IS NULL THEN 'draft' ELSE 'completed' END";
        $totalExpr = $ticketTotalColumn ? "t.`{$ticketTotalColumn}`" : "0";

        $query = "
            SELECT t.*, {$statusExpr} AS status, COALESCE(s.total, {$totalExpr}, 0) AS total_import_price
            FROM import_tickets t
            LEFT JOIN (
                SELECT import_ticket_id, SUM(total_price) AS total
                FROM import_items
                GROUP BY import_ticket_id
            ) s ON s.import_ticket_id = t.id
            WHERE 1=1
        ";
        $params = [];
        $types = '';
        
        if ($search) {
            $query .= " AND t.ticket_number LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }
        
        if ($status) {
            if ($hasTicketStatusColumn) {
                $query .= " AND t.status = ?";
                $params[] = $status;
                $types .= 's';
            } elseif ($status === 'draft') {
                $query .= " AND t.completed_at IS NULL";
            } elseif ($status === 'completed') {
                $query .= " AND t.completed_at IS NOT NULL";
            }
        }
        
        if ($dateFrom) {
            $query .= " AND t.import_date >= ?";
            $params[] = $dateFrom;
            $types .= 's';
        }
        
        if ($dateTo) {
            $query .= " AND t.import_date <= ?";
            $params[] = $dateTo;
            $types .= 's';
        }
        
        $query .= " ORDER BY t.id DESC";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error preparing ticket list query']);
            exit;
        }

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $tickets = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        echo json_encode(['success' => true, 'data' => $tickets]);
        
    } elseif ($action === 'detail') {
        // Lấy chi tiết phiếu nhập
        $ticketId = intval($_GET['id'] ?? 0);
        
        if ($ticketId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
            exit;
        }
        
        // Lấy info phiếu
        $stmt = $conn->prepare("SELECT * FROM import_tickets WHERE id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error loading ticket detail']);
            exit;
        }
        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $ticket['status'] = normalizedTicketStatus($ticket, $hasTicketStatusColumn);
        
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            exit;
        }
        
        // Lấy items
        $stmt = $conn->prepare("
            SELECT ii.*, p.name, p.image_url 
            FROM import_items ii
            JOIN products p ON ii.product_id = p.id
            WHERE ii.import_ticket_id = ?
            ORDER BY ii.created_at
        ");
        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        echo json_encode([
            'success' => true,
            'ticket' => $ticket,
            'items' => $items
        ]);
    }
    exit;
}

// POST - Tạo phiếu nhập mới
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'create';
    
    if ($action === 'create') {
        // Tạo phiếu nhập mới
        $importDate = $data['import_date'] ?? date('Y-m-d');
        $notes = htmlspecialchars($data['notes'] ?? '');
        $createdBy = intval($data['created_by'] ?? 1); // Admin ID
        
        // Tạo mã phiếu theo thứ tự trong ngày (ITyyyymmdd001, ITyyyymmdd002...)
        $prefix = 'IT' . date('Ymd');
        $seqStmt = $conn->prepare("SELECT COUNT(*) AS total FROM import_tickets WHERE ticket_number LIKE CONCAT(?, '%')");
        if (!$seqStmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error generating ticket number']);
            exit;
        }

        $seqStmt->bind_param('s', $prefix);
        $seqStmt->execute();
        $seqRow = $seqStmt->get_result()->fetch_assoc();
        $seqStmt->close();

        $nextSeq = intval($seqRow['total'] ?? 0) + 1;
        $ticketNumber = $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        
        if ($hasTicketStatusColumn) {
            $stmt = $conn->prepare("
                INSERT INTO import_tickets (ticket_number, import_date, status, notes, created_by)
                VALUES (?, ?, 'draft', ?, ?)
            ");
        } else {
            $stmt = $conn->prepare("
                INSERT INTO import_tickets (ticket_number, import_date, notes, created_by)
                VALUES (?, ?, ?, ?)
            ");
        }

        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error preparing create ticket query']);
            exit;
        }

        $stmt->bind_param('sssi', $ticketNumber, $importDate, $notes, $createdBy);
        
        if ($stmt->execute()) {
            $ticketId = $stmt->insert_id;
            $stmt->close();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Import ticket created successfully',
                'ticketId' => $ticketId,
                'ticketNumber' => $ticketNumber
            ]);
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error creating ticket']);
        }
    }
    
    elseif ($action === 'add_item') {
        // Thêm sản phẩm vào phiếu nhập
        $ticketId = intval($data['ticket_id'] ?? 0);
        $productId = intval($data['product_id'] ?? 0);
        $quantity = intval($data['quantity'] ?? 0);
        $importPrice = floatval($data['import_price'] ?? 0);
        
        if (!$ticketId || !$productId || !$quantity || !$importPrice) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }
        
        // Kiểm tra phiếu chưa hoàn thành
        $statusSelect = $hasTicketStatusColumn ? 'status, completed_at' : 'completed_at';
        $stmt = $conn->prepare("SELECT {$statusSelect} FROM import_tickets WHERE id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error validating ticket status']);
            exit;
        }
        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $ticketStatus = $ticket ? normalizedTicketStatus($ticket, $hasTicketStatusColumn) : null;
        
        if (!$ticket || $ticketStatus === 'completed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot edit completed ticket']);
            exit;
        }
        
        $totalPrice = $quantity * $importPrice;
        
        // Kiểm tra sản phẩm đã có trong phiếu chưa
        $stmt = $conn->prepare("
            SELECT id FROM import_items 
            WHERE import_ticket_id = ? AND product_id = ?
        ");
        $stmt->bind_param('ii', $ticketId, $productId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($existing) {
            // Update quantity
            $stmt = $conn->prepare("
                UPDATE import_items 
                SET quantity = quantity + ?, import_price = ?, total_price = (quantity + ?) * ?
                WHERE import_ticket_id = ? AND product_id = ?
            ");
            $stmt->bind_param('ididii', $quantity, $importPrice, $quantity, $importPrice, $ticketId, $productId);
        } else {
            // Insert new
            $stmt = $conn->prepare("
                INSERT INTO import_items (import_ticket_id, product_id, quantity, import_price, total_price)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('iiidd', $ticketId, $productId, $quantity, $importPrice, $totalPrice);
        }
        
        if ($stmt->execute()) {
            $stmt->close();

            if (!refreshTicketTotal($conn, $ticketId, $ticketTotalColumn)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Item added but failed to refresh ticket total']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Item added successfully']);
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error adding item']);
        }
    }
    
    exit;
}

// PUT - Cập nhật phiếu nhập
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'update';
    
    if ($action === 'update') {
        $ticketId = intval($data['ticket_id'] ?? 0);
        $notes = htmlspecialchars($data['notes'] ?? '');
        $importDate = $data['import_date'] ?? '';

        if (!$ticketId || !$importDate) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid ticket ID or import date']);
            exit;
        }
        
        // Kiểm tra phiếu chưa hoàn thành
        $statusSelect = $hasTicketStatusColumn ? 'status, completed_at' : 'completed_at';
        $stmt = $conn->prepare("SELECT {$statusSelect} FROM import_tickets WHERE id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error validating ticket status']);
            exit;
        }
        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $ticketStatus = $ticket ? normalizedTicketStatus($ticket, $hasTicketStatusColumn) : null;
        
        if (!$ticket || $ticketStatus === 'completed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot edit completed ticket']);
            exit;
        }
        
        $stmt = $conn->prepare("UPDATE import_tickets SET notes = ?, import_date = ? WHERE id = ?");
        $stmt->bind_param('ssi', $notes, $importDate, $ticketId);
        
        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'Ticket updated successfully']);
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating ticket']);
        }
    }
    
    elseif ($action === 'complete') {
        // Hoàn thành phiếu nhập - cập nhật cost_price (theo BÌNH QUÂN) và stock
        $ticketId = intval($data['ticket_id'] ?? 0);
        
        if (!$ticketId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
            exit;
        }

        // Chỉ cho hoàn thành phiếu nháp
        $statusSelect = $hasTicketStatusColumn ? 'status, completed_at' : 'completed_at';
        $stmt = $conn->prepare("SELECT {$statusSelect} FROM import_tickets WHERE id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error validating ticket status']);
            exit;
        }
        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $ticketStatus = $ticket ? normalizedTicketStatus($ticket, $hasTicketStatusColumn) : null;

        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            exit;
        }

        if ($ticketStatus === 'completed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ticket already completed']);
            exit;
        }

        $costColumn = detectProductCostColumn($conn);
        if (!$costColumn) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Products table missing cost column (cost_price/price_cost)']);
            exit;
        }
        
        try {
            $conn->begin_transaction();
            
            // Lấy tất cả items trong phiếu
            $stmt = $conn->prepare("
                SELECT ii.product_id, ii.quantity, ii.import_price
                FROM import_items ii
                WHERE ii.import_ticket_id = ?
            ");
            $stmt->bind_param('i', $ticketId);
            $stmt->execute();
            $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (!$items || count($items) === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot complete empty ticket']);
                exit;
            }
            
            // Cập nhật từng sản phẩm sử dụng BÌNH QUÂN giá vốn
            foreach ($items as $item) {
                // Lấy thông tin hiện tại
                $stmt = $conn->prepare("
                    SELECT stock, {$costColumn} AS cost_price, profit_margin 
                    FROM products 
                    WHERE id = ?
                ");
                $stmt->bind_param('i', $item['product_id']);
                $stmt->execute();
                $product = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                
                if (!$product) continue;
                
                $currentStock = intval($product['stock']);
                $currentCostPrice = floatval($product['cost_price']);
                $newQuantity = intval($item['quantity']);
                $newImportPrice = floatval($item['import_price']);
                $profitMargin = floatval($product['profit_margin'] ?? 0);
                
                // Tính giá vốn BÌNH QUÂN
                // Công thức: (tồn_hiện_tại * giá_vốn_hiện_tại + số_nhập_mới * giá_nhập_mới) / (tồn_hiện_tại + số_nhập_mới)
                $newCostPrice = ($currentStock * $currentCostPrice + $newQuantity * $newImportPrice) / ($currentStock + $newQuantity);
                
                // Tính giá bán mới dựa trên giá vốn mới
                // Công thức: selling_price = cost_price * (100% + profit_margin%) = cost_price * (1 + margin/100)
                $newSellingPrice = $newCostPrice * (1 + $profitMargin / 100);
                
                // Cập nhật sản phẩm: cost_price, stock, price
                $stmt = $conn->prepare("
                    UPDATE products 
                    SET {$costColumn} = ?, 
                        price = ?,
                        stock = stock + ?
                    WHERE id = ?
                ");
                $stmt->bind_param('ddii', $newCostPrice, $newSellingPrice, $newQuantity, $item['product_id']);
                $stmt->execute();
                $stmt->close();
            }
            
            // Tính tổng tiền nhập
            $stmt = $conn->prepare("
                SELECT SUM(total_price) as total FROM import_items
                WHERE import_ticket_id = ?
            ");
            $stmt->bind_param('i', $ticketId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $totalPrice = $result['total'] ?? 0;
            $stmt->close();
            
            // Cập nhật status phiếu thành completed
            $completeSetSql = [];
            $completeTypes = '';
            $completeParams = [];

            if ($hasTicketStatusColumn) {
                $completeSetSql[] = "status = 'completed'";
            }

            if ($ticketTotalColumn) {
                $completeSetSql[] = "`{$ticketTotalColumn}` = ?";
                $completeTypes .= 'd';
                $completeParams[] = $totalPrice;
            }

            $completeSetSql[] = "completed_at = NOW()";

            $stmt = $conn->prepare("
                UPDATE import_tickets 
                SET " . implode(', ', $completeSetSql) . "
                WHERE id = ?
            ");
            if (!$stmt) {
                throw new Exception('Cannot prepare complete ticket query');
            }

            $completeTypes .= 'i';
            $completeParams[] = $ticketId;

            $stmt->bind_param($completeTypes, ...$completeParams);
            $stmt->execute();
            $stmt->close();
            
            $conn->commit();
            
            echo json_encode(['success' => true, 'message' => 'Import ticket completed successfully']);
            
        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error completing ticket: ' . $e->getMessage()]);
        }
    }
    
    exit;
}

// DELETE - Xoá sản phẩm khỏi phiếu nhập
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'delete_item';
    
    if ($action === 'delete_item') {
        $itemId = intval($data['item_id'] ?? 0);
        $ticketId = intval($data['ticket_id'] ?? 0);
        
        if (!$itemId || !$ticketId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }
        
        // Kiểm tra phiếu chưa hoàn thành
        $statusSelect = $hasTicketStatusColumn ? 'status, completed_at' : 'completed_at';
        $stmt = $conn->prepare("SELECT {$statusSelect} FROM import_tickets WHERE id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error validating ticket status']);
            exit;
        }
        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $ticketStatus = $ticket ? normalizedTicketStatus($ticket, $hasTicketStatusColumn) : null;
        
        if (!$ticket || $ticketStatus === 'completed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot edit completed ticket']);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM import_items WHERE id = ? AND import_ticket_id = ?");
        $stmt->bind_param('ii', $itemId, $ticketId);
        
        if ($stmt->execute()) {
            $stmt->close();

            if (!refreshTicketTotal($conn, $ticketId, $ticketTotalColumn)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Item deleted but failed to refresh ticket total']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting item']);
        }
    } elseif ($action === 'delete_ticket') {
        $ticketId = intval($data['ticket_id'] ?? 0);

        if (!$ticketId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
            exit;
        }

        // Chỉ cho xóa phiếu nháp
        $statusSelect = $hasTicketStatusColumn ? 'status, completed_at' : 'completed_at';
        $stmt = $conn->prepare("SELECT {$statusSelect} FROM import_tickets WHERE id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error validating ticket status']);
            exit;
        }
        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $ticketStatus = $ticket ? normalizedTicketStatus($ticket, $hasTicketStatusColumn) : null;

        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            exit;
        }

        if ($ticketStatus === 'completed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot delete completed ticket']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM import_tickets WHERE id = ?");
        $stmt->bind_param('i', $ticketId);

        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'Ticket deleted successfully']);
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting ticket']);
        }
    }
    
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
