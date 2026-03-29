<?php
require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
        INSERT INTO users(username, password, role, status)
        VALUES (?, ?, ?, 'active')
    ");
    $stmt->bind_param(
        "sss",
        $_POST['username'],
        $_POST['password'],
        $_POST['role']
    );
    $stmt->execute();

    header("Location: users.php");
}
?>

<form method="POST">
    <h2>Thêm user</h2>
    <input name="username" placeholder="Username"><br>
    <input name="password" placeholder="Password"><br>
    <select name="role">
        <option value="customer">Customer</option>
        <option value="admin">Admin</option>
    </select><br>
    <button>Thêm</button>
</form>