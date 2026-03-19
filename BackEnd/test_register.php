<?php
// test_register.php - File test để debug
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test 1: Include files
try {
    require_once 'config/db_connect.php';
    echo json_encode(['test' => '1. Database connection OK']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
?>
