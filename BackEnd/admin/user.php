<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT * FROM users");
?>

<h2>Danh sách user</h2>

<table border="1">
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Role</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['username'] ?></td>
    <td><?= $row['role'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <a href="reset_password.php?id=<?= $row['id'] ?>">Reset</a> |
        <a href="toggle_status.php?id=<?= $row['id'] ?>">Khoá/Mở</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<a href="add_user.php">+ Thêm user</a>