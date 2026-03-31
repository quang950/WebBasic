<?php
session_start();

// KẾT NỐI DATABASE
require_once __DIR__ . '/../config/db_connect.php';

if (!$dbConnected) {
    die("Không thể kết nối CSDL");
}

// XỬ LÝ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // LẤY DỮ LIỆU TỪ FORM
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // VALIDATE
    if ($email === '' || $password === '') {
        echo "Vui lòng nhập email và mật khẩu";
        exit;
    }

    // LẤY ADMIN TỪ DB
    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE email = ? AND is_admin = 1 
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $admin  = $result->fetch_assoc();

    // KIỂM TRA MẬT KHẨU (plain text – đúng với DB hiện tại)
    if ($admin && $password === $admin['password']) {

        // LƯU SESSION ADMIN
        $_SESSION['admin'] = [
            'id'    => $admin['id'],
            'name'  => $admin['first_name'] . ' ' . $admin['last_name'],
            'email' => $admin['email']
        ];

        header("Location: dashboard.php");
        exit;

    } else {
        echo "Invalid email or password";
    }
}