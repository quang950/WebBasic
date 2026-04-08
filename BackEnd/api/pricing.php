<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

require_once __DIR__ . '/../config/db_connect.php';

if (!$dbConnected || !($conn instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

function columnExists($conn, $tableName, $columnName) {
    $safeTable = $conn->real_escape_string($tableName);
    $safeColumn = $conn->real_escape_string($columnName);
    $result = $conn->query("SHOW COLUMNS FROM {$safeTable} LIKE '{$safeColumn}'");
    return $result && $result->num_rows > 0;
}

function detectCostColumn($conn) {
    if (columnExists($conn, 'products', 'cost_price')) {
        return 'cost_price';
    }

    if (columnExists($conn, 'products', 'price_cost')) {
        return 'price_cost';
    }

    return null;
}

function getOptionalFloatParam($key) {
    if (!isset($_GET[$key])) {
        return null;
    }

    $raw = trim((string)$_GET[$key]);
    if ($raw === '') {
        return null;
    }

    return floatval($raw);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action !== 'list') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unsupported action']);
        exit;
    }

    $search = trim((string)($_GET['search'] ?? ''));
    $categoryId = intval($_GET['categoryId'] ?? 0);
    $costMin = getOptionalFloatParam('costMin');
    $costMax = getOptionalFloatParam('costMax');
    $marginMin = getOptionalFloatParam('marginMin');
    $marginMax = getOptionalFloatParam('marginMax');
    $sellMin = getOptionalFloatParam('sellMin');
    $sellMax = getOptionalFloatParam('sellMax');
    $limit = intval($_GET['limit'] ?? 100);
    $limit = max(1, min(500, $limit));

    $costColumn = detectCostColumn($conn);
    if (!$costColumn) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Products table missing cost column (cost_price/price_cost)']);
        exit;
    }

    $hasCategoryId = columnExists($conn, 'products', 'category_id');
    $hasCategoryText = columnExists($conn, 'products', 'category');

    if ($hasCategoryId) {
        $query = "
            SELECT
                p.id,
                p.name,
                p.{$costColumn} AS cost_price,
                p.profit_margin,
                p.price AS selling_price,
                p.stock,
                c.id AS category_id,
                c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE 1=1
        ";
    } elseif ($hasCategoryText) {
        $query = "
            SELECT
                p.id,
                p.name,
                p.{$costColumn} AS cost_price,
                p.profit_margin,
                p.price AS selling_price,
                p.stock,
                NULL AS category_id,
                p.category AS category_name
            FROM products p
            WHERE 1=1
        ";
    } else {
        $query = "
            SELECT
                p.id,
                p.name,
                p.{$costColumn} AS cost_price,
                p.profit_margin,
                p.price AS selling_price,
                p.stock,
                NULL AS category_id,
                '' AS category_name
            FROM products p
            WHERE 1=1
        ";
    }

    $params = [];
    $types = '';

    if ($search !== '') {
        $query .= " AND p.name LIKE ?";
        $params[] = '%' . $search . '%';
        $types .= 's';
    }

    if ($categoryId > 0 && $hasCategoryId) {
        $query .= " AND p.category_id = ?";
        $params[] = $categoryId;
        $types .= 'i';
    }

    if ($costMin !== null) {
        $query .= " AND p.{$costColumn} >= ?";
        $params[] = $costMin;
        $types .= 'd';
    }

    if ($costMax !== null) {
        $query .= " AND p.{$costColumn} <= ?";
        $params[] = $costMax;
        $types .= 'd';
    }

    if ($marginMin !== null) {
        $query .= " AND p.profit_margin >= ?";
        $params[] = $marginMin;
        $types .= 'd';
    }

    if ($marginMax !== null) {
        $query .= " AND p.profit_margin <= ?";
        $params[] = $marginMax;
        $types .= 'd';
    }

    if ($sellMin !== null) {
        $query .= " AND p.price >= ?";
        $params[] = $sellMin;
        $types .= 'd';
    }

    if ($sellMax !== null) {
        $query .= " AND p.price <= ?";
        $params[] = $sellMax;
        $types .= 'd';
    }

    $query .= " ORDER BY p.id DESC LIMIT ?";
    $params[] = $limit;
    $types .= 'i';

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare query']);
        exit;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($rows as &$row) {
        $row['cost_price'] = floatval($row['cost_price'] ?? 0);
        $row['profit_margin'] = floatval($row['profit_margin'] ?? 0);
        $row['selling_price'] = floatval($row['selling_price'] ?? 0);
        $row['stock'] = intval($row['stock'] ?? 0);
        $row['profit_amount'] = $row['selling_price'] - $row['cost_price'];
    }
    unset($row);

    echo json_encode([
        'success' => true,
        'data' => $rows,
        'count' => count($rows)
    ]);
    exit;
}

if ($method === 'PUT') {
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
        exit;
    }

    $action = $payload['action'] ?? 'update_margin';
    if (!in_array($action, ['update_margin', 'update_cost', 'update_pricing'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unsupported action']);
        exit;
    }

    $productId = intval($payload['product_id'] ?? 0);
    $profitMargin = array_key_exists('profit_margin', $payload) ? floatval($payload['profit_margin']) : null;
    $costPriceInput = array_key_exists('cost_price', $payload) ? floatval($payload['cost_price']) : null;

    if ($productId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid product_id']);
        exit;
    }

    if ($action === 'update_margin' && $profitMargin === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'profit_margin is required']);
        exit;
    }

    if ($action === 'update_cost' && $costPriceInput === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'cost_price is required']);
        exit;
    }

    if ($action === 'update_pricing' && $profitMargin === null && $costPriceInput === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'At least one of cost_price or profit_margin is required']);
        exit;
    }

    $costColumn = detectCostColumn($conn);
    if (!$costColumn) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Products table missing cost column (cost_price/price_cost)']);
        exit;
    }

    $conn->begin_transaction();
    try {
        $selectSql = "SELECT {$costColumn} AS cost_price, profit_margin FROM products WHERE id = ?";
        $stmt = $conn->prepare($selectSql);
        if (!$stmt) {
            throw new Exception('Failed to prepare select query');
        }

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$product) {
            throw new Exception('Product not found');
        }

        $currentCostPrice = floatval($product['cost_price'] ?? 0);
        $currentProfitMargin = floatval($product['profit_margin'] ?? 0);

        $newCostPrice = $costPriceInput !== null ? $costPriceInput : $currentCostPrice;
        $newProfitMargin = $profitMargin !== null ? $profitMargin : $currentProfitMargin;

        if ($newCostPrice < 0) {
            throw new Exception('cost_price must be greater than or equal to 0');
        }

        if ($newProfitMargin < 0 || $newProfitMargin > 500) {
            throw new Exception('profit_margin must be between 0 and 500');
        }

        $sellingPrice = $newCostPrice * (1 + ($newProfitMargin / 100));

        $updateStmt = $conn->prepare("UPDATE products SET {$costColumn} = ?, profit_margin = ?, price = ? WHERE id = ?");
        if (!$updateStmt) {
            throw new Exception('Failed to prepare update query');
        }

        $updateStmt->bind_param('dddi', $newCostPrice, $newProfitMargin, $sellingPrice, $productId);
        $updateStmt->execute();
        $updateStmt->close();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Updated pricing successfully',
            'data' => [
                'product_id' => $productId,
                'cost_price' => $newCostPrice,
                'profit_margin' => $newProfitMargin,
                'selling_price' => $sellingPrice,
                'profit_amount' => $sellingPrice - $newCostPrice
            ]
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
