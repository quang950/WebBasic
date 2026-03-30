<?php
session_start();
require_once "db_connect.php";

if (!$dbConnected) {
    die("Lỗi kết nối CSDL");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // TÀI KHOẢN + MẬT KHẨU
    $account  = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($account === '' || $password === '') {
        echo "Vui lòng nhập tài khoản và mật khẩu";
        exit;
    }

    // Chỉ cho phép admin
    $stmt = $conn->prepare("
        SELECT * FROM users
        WHERE email = ? AND is_admin = 1
        LIMIT 1
    ");
    $stmt->bind_param("s", $account);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    // So sánh mật khẩu (plain text – theo DB hiện tại)
    if ($admin && $password === $admin['password']) {

        $_SESSION['admin'] = [
            'id'   => $admin['id'],
            'name' => $admin['first_name'] . ' ' . $admin['last_name'],
            'email'=> $admin['email']
        ];

        header("Location: dashboard.php");
        exit;

    } else {
        echo "Sai tài khoản hoặc mật khẩu";
    }
}
?>