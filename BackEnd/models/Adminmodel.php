<?php
// models/AdminModel.php
require_once __DIR__ . '/../config/db_connect.php';

class AdminModel {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function completeReceipt($receipt_id) {
        try {
            $this->conn->beginTransaction();

            // 1. Lấy chi tiết phiếu nhập
            $stmt = $this->conn->prepare("SELECT * FROM receipt_details WHERE receipt_id = ?");
            $stmt->execute([$receipt_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $p_id = $item['product_id'];
                $qty_nhap = $item['quantity'];
                $gia_nhap = $item['import_price'];

                // 2. Lấy thông tin tồn kho & giá vốn hiện tại [cite: 28]
                $stmt_p = $this->conn->prepare("SELECT stock, cost_price, profit_margin FROM products WHERE id = ?");
                $stmt_p->execute([$p_id]);
                $product = $stmt_p->fetch(PDO::FETCH_ASSOC);

                $ton_cu = $product['stock'];
                $gia_von_cu = $product['cost_price'];
                $margin = $product['profit_margin'];

                // 3. Công thức BÌNH QUÂN[cite: 28]:
                // Gia_von_moi = ((ton_cu * gia_von_cu) + (qty_nhap * gia_nhap)) / (ton_cu + qty_nhap)
                $tong_ton_moi = $ton_cu + $qty_nhap;
                $gia_von_moi = (($ton_cu * $gia_von_cu) + ($qty_nhap * $gia_nhap)) / $tong_ton_moi;

                // 4. Tính giá bán mới[cite: 27]:
                // Gia_ban = Gia_von_moi * (100% + tỷ lệ lợi nhuận)
                $gia_ban_moi = $gia_von_moi * (1 + ($margin / 100));

                // 5. Cập nhật vào bảng Products 
                $update = $this->conn->prepare("
                    UPDATE products 
                    SET cost_price = ?, price = ?, stock = ? 
                    WHERE id = ?
                ");
                $update->execute([$gia_von_moi, $gia_ban_moi, $tong_ton_moi, $p_id]);
            }

            // Đổi trạng thái phiếu nhập
            $this->conn->prepare("UPDATE receipts SET status = 'completed' WHERE id = ?")->execute([$receipt_id]);

            $this->conn->commit();
            return ["success" => true];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
}