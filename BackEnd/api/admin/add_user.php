<?php
/**
 * Admin API: Add new user
 * POST /BackEnd/api/admin/add_user.php
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    // Check if database connection is successful
    if (!$conn || !$dbConnected) {
        throw new Exception('Database connection failed: ' . ($dbError ?? 'Unknown error'));
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['first_name'], $data['last_name'], $data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields'
        ]);
        exit;
    }
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $data['email']);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists'
        ]);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    
    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO users (first_name, last_name, email, password, phone, province, is_admin)
        VALUES (?, ?, ?, ?, ?, ?, 0)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $phone = $data['phone'] ?? '';
    $province = $data['province'] ?? '';
    
    if (!$stmt->bind_param("ssssss", $data['first_name'], $data['last_name'], $data['email'], $hashedPassword, $phone, $province)) {
        throw new Exception("Bind param failed: " . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    
    $user_id = $conn->insert_id;
    
    echo json_encode([
        'success' => true,
        'user_id' => $user_id,
        'message' => 'User added successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
