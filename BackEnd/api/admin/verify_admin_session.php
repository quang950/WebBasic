<?php
/**
 * API: Verify admin session - check if admin is still logged in
 * GET /BackEnd/api/admin/verify_admin_session.php
 * Returns: {valid: true/false}
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');

// Check if admin session exists and valid
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true && isset($_SESSION['user_id'])) {
    http_response_code(200);
    echo json_encode([
        'valid' => true,
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'] ?? ''
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'valid' => false,
        'message' => 'Session expired or not admin'
    ]);
}
?>
