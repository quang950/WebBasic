<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

//  check DB
if (!$dbConnected) {
    die("Lỗi DB: " . $dbError);
}

$result = $conn->query("SELECT * FROM products");
?>

<h2>Quản lý sản phẩm</h2>

<a href="add_product.php">+ Thêm sản phẩm</a>

<table border="1">
<tr>
    <th>ID</th>
    <th>Tên</th>
    <th>Giá</th>
    <th>Tồn</th>
    <th>Ảnh</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['price'] ?></td>
    <td><?= $row['stock'] ?></td>

    <td>
        <?php if ($row['image']): ?>
            <img src="../uploads/<?= $row['image'] ?>" width="80">
        <?php endif; ?>
    </td>

    <td><?= $row['status'] ?></td>

    <td>
        <a href="edit_product.php?id=<?= $row['id'] ?>">Sửa</a> |
        <a href="delete_product.php?id=<?= $row['id'] ?>">Xoá</a>
    </td>
</tr>
<?php endwhile; ?>
</table>