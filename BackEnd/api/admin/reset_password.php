<?php
/**
 * Admin API: Reset user password
 * POST /BackEnd/api/admin/reset_password.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['user_id']) || !isset($data['new_password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields'
        ]);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($data['new_password'], PASSWORD_BCRYPT);
    
    // Update user password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $data['user_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Update failed: " . $stmt->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Password reset successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
