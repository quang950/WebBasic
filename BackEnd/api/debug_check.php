<?php
/**
 * Debug kiểm tra tài khoản & login
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db_connect.php';

$results = [
    'database_status' => [],
    'users_list' => [],
    'debug_info' => []
];

// 1. Kiểm tra kết nối database
if (!$conn) {
    $results['database_status'] = [
        'connected' => false,
        'error' => $dbError
    ];
    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    exit;
}

$results['database_status'] = [
    'connected' => true,
    'database' => DB_NAME,
    'host' => DB_HOST
];

// 2. Kiểm tra bảng users tồn tại
$checkTable = $conn->query("SHOW TABLES LIKE 'users'");
if ($checkTable->num_rows === 0) {
    $results['debug_info'][] = '❌ Bảng "users" không tồn tại!';
    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    exit;
}

$results['debug_info'][] = '✅ Bảng "users" tồn tại';

// 3. Kiểm tra cấu trúc bảng
$tableInfo = $conn->query("DESCRIBE users");
$columns = [];
while ($col = $tableInfo->fetch_assoc()) {
    $columns[] = [
        'field' => $col['Field'],
        'type' => $col['Type'],
        'null' => $col['Null'],
        'key' => $col['Key']
    ];
}
$results['table_columns'] = $columns;

// 4. Lấy tất cả users
$usersQuery = $conn->query("SELECT id, email, first_name, last_name, password, is_admin, created_at FROM users ORDER BY created_at DESC");

if (!$usersQuery) {
    $results['debug_info'][] = '❌ Query lỗi: ' . $conn->error;
    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    exit;
}

$userCount = $usersQuery->num_rows;
$results['debug_info'][] = "📊 Tổng tài khoản: $userCount";

while ($user = $usersQuery->fetch_assoc()) {
    $results['users_list'][] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => trim($user['first_name'] . ' ' . $user['last_name']),
        'password_hash' => substr($user['password'], 0, 20) . '...',
        'is_admin' => (bool)$user['is_admin'],
        'created_at' => $user['created_at'],
        'password_valid' => [
            'starts_with_dollar' => strpos($user['password'], '$') === 0,
            'length' => strlen($user['password']),
            'is_bcrypt' => preg_match('/^\$2[aby]\$/', $user['password']) ? '✅ Bcrypt' : '❌ Không phải Bcrypt'
        ]
    ];
}

// 5. Tìm test credentials
$results['debug_info'][] = '❌ Cần phải cung cấp email & password để test login';

// 6. Hướng dẫn sử dụng
$results['usage'] = [
    'method' => 'POST',
    'endpoint' => '/WebBasic/BackEnd/api/debug_check.php',
    'body' => json_encode([
        'action' => 'test_login',
        'email' => 'user@example.com',
        'password' => 'password123'
    ])
];

// Nếu có tham số test
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'test_login' && isset($data['email'], $data['password'])) {
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $password = $data['password'];
        
        $results['test_login_request'] = [
            'email' => $email,
            'password' => str_repeat('*', strlen($password))
        ];
        
        // Query user
        $stmt = $conn->prepare("SELECT id, email, password, first_name, last_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $results['test_login_result'] = '❌ Không tìm thấy tài khoản với email: ' . $email;
        } else {
            $user = $result->fetch_assoc();
            $passwordMatch = password_verify($password, $user['password']);
            
            $results['test_login_result'] = [
                'email_found' => true,
                'user_name' => $user['first_name'] . ' ' . $user['last_name'],
                'password_correct' => $passwordMatch,
                'status' => $passwordMatch ? '✅ Đăng nhập thành công' : '❌ Mật khẩu không đúng'
            ];
        }
        $stmt->close();
    }
}

echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
