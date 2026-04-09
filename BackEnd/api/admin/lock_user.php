<?php
/**
 * Admin API: Lock/Unlock user account
 * POST /BackEnd/api/admin/lock_user.php
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['user_id']) || !isset($data['locked'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields'
        ]);
        exit;
    }
    
    $user_id = intval($data['user_id']);
    // Using a simple approach: if locked=1, set a flag or mark somehow
    // For now, we'll use an 'is_locked' column if it exists, otherwise use password manipulation
    // Better approach: add a status column or use a different field
    
    // Check if is_locked column exists, otherwise just return success
    // For this implementation, we'll create a simple lock status
    
    // Get user first
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }
    
    // For simplicity, we'll add a comment or use a status field
    // Use locked column instead of modifying password
    if ($data['locked']) {
        // Lock: set locked = 1
        $stmt = $conn->prepare("UPDATE users SET locked = 1 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $message = 'User locked successfully';
    } else {
        // Unlock: set locked = 0 (password stays the same!)
        $stmt = $conn->prepare("UPDATE users SET locked = 0 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $message = 'User unlocked successfully! Password không thay đổi';
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Update failed: " . $stmt->error);
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
