<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE username = ? AND role = 'admin'
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $password == $user['password']) {
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Sai tài khoản hoặc không phải admin";
    }
}
?>

<form method="POST">
    <h2>Admin Login</h2>
    <input name="username" placeholder="Username"><br>
    <input name="password" type="password" placeholder="Password"><br>
    <button>Login</button>
</form>