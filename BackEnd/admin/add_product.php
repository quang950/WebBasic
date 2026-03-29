<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!$dbConnected) {
    die("Lỗi DB: " . $dbError);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $cost = (float)$_POST['cost_price'];
    $profit = (int)$_POST['profit_percent'];
    $status = $_POST['status'];

    $image = "";

    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
    }

    $stmt = $conn->prepare("
        INSERT INTO products(name, price, stock, cost_price, profit_percent, image, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sdiidss", $name, $price, $stock, $cost, $profit, $image, $status);
    $stmt->execute();

    header("Location: products.php");
}
?>

<h2>Thêm sản phẩm</h2>

<form method="POST" enctype="multipart/form-data">
    Tên: <input name="name" required><br>
    Giá bán: <input name="price" required><br>
    Tồn kho: <input name="stock" required><br>
    Giá vốn: <input name="cost_price"><br>
    % lợi nhuận: <input name="profit_percent"><br>

    Ảnh: <input type="file" name="image"><br>

    Trạng thái:
    <select name="status">
        <option value="active">Hiển thị</option>
        <option value="hidden">Ẩn</option>
    </select><br>

    <button>Thêm</button>
</form>