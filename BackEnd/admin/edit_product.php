<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!$dbConnected) {
    die("Lỗi DB: " . $dbError);
}

$id = (int)$_GET['id'];

$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $status = $_POST['status'];

    if (!empty($_FILES['image']['name'])) {

        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);

        $stmt = $conn->prepare("
            UPDATE products 
            SET name=?, price=?, stock=?, image=?, status=?
            WHERE id=?
        ");

        $stmt->bind_param("sdissi", $name, $price, $stock, $image, $status, $id);

    } else {

        $stmt = $conn->prepare("
            UPDATE products 
            SET name=?, price=?, stock=?, status=?
            WHERE id=?
        ");

        $stmt->bind_param("sdisi", $name, $price, $stock, $status, $id);
    }

    $stmt->execute();

    header("Location: products.php");
}
?>

<h2>Sửa sản phẩm</h2>

<form method="POST" enctype="multipart/form-data">
    Tên: <input name="name" value="<?= $product['name'] ?>"><br>
    Giá: <input name="price" value="<?= $product['price'] ?>"><br>
    Tồn: <input name="stock" value="<?= $product['stock'] ?>"><br>

    Ảnh: <input type="file" name="image"><br>

    Trạng thái:
    <select name="status">
        <option value="active" <?= $product['status']=='active'?'selected':'' ?>>Hiển thị</option>
        <option value="hidden" <?= $product['status']=='hidden'?'selected':'' ?>>Ẩn</option>
    </select><br>

    <button>Cập nhật</button>
</form>