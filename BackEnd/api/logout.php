<?php
/**
 * POST /BackEnd/api/logout.php
 * User logout endpoint - destroys session
 */
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức không được hỗ trợ. Sử dụng POST'
        ]);
        exit;
    }

    // Store user info before destroying session (optional, for logging)
    $user_id = $_SESSION['user_id'] ?? null;

    // Destroy all session data
    $_SESSION = [];
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Đăng xuất thành công',
        'user_id' => $user_id
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi đăng xuất: ' . $e->getMessage()
    ]);
}
?>
