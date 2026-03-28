<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['admin'];
?>

<h2>Xin chào: <?= $admin['username'] ?></h2>

<ul>
    <li><a href="users.php">Quản lý người dùng</a></li>
</ul>

<a href="logout.php">Đăng xuất</a>