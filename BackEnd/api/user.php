<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db_connect.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

/** @var mysqli $conn */

// GET - Lấy thông tin user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$userId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        exit;
    }
    
    $stmt = $conn->prepare("
        SELECT id, email, first_name, last_name, phone, birth_date, province, address, is_admin, locked
        FROM users WHERE id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // locked field trực tiếp từ database
    $user['locked'] = (bool)$user['locked'];
    
    $stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
    exit;
}

// POST - Update user info hoặc change password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['userId']) && !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        exit;
    }
    
    $userId = intval($data['userId'] ?? $data['id']);
    
    // Handle change password action
    if (isset($data['action']) && $data['action'] === 'changePassword') {
        if (!isset($data['currentPassword']) || !isset($data['newPassword'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Current and new password required']);
            exit;
        }
        
        $currentPassword = $data['currentPassword'];
        $newPassword = $data['newPassword'];
        
        // Get current password hash
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            $stmt->close();
            exit;
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        // Update password
        $updateStmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $updateStmt->bind_param("si", $hashedPassword, $userId);
        
        if ($updateStmt->execute()) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to change password']);
        }
        
        $updateStmt->close();
        $conn?->close();
        exit;
    }
    
    // Handle profile update
    $firstName = htmlspecialchars($data['firstName'] ?? '');
    $lastName = htmlspecialchars($data['lastName'] ?? '');
    $phone = htmlspecialchars($data['phone'] ?? '');
    $birthDate = htmlspecialchars($data['birthDate'] ?? '');
    $province = htmlspecialchars($data['province'] ?? '');
    $address = htmlspecialchars($data['address'] ?? '');
    
    $stmt = $conn->prepare("
        UPDATE users 
        SET first_name = ?, last_name = ?, phone = ?, birth_date = ?, province = ?, address = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->bind_param("ssssssi", $firstName, $lastName, $phone, $birthDate, $province, $address, $userId);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update user']);
    }
    
    $stmt->close();
    $conn?->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn?->close();
?>
