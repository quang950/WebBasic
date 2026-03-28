<?php
require_once __DIR__ . '/../config/db_connect.php';

$id = $_GET['id'];

$newPass = "123456";

$stmt = $conn->prepare("
    UPDATE users SET password = ? WHERE id = ?
");
$stmt->bind_param("si", $newPass, $id);
$stmt->execute();

header("Location: users.php");