<?php
require_once __DIR__ . '/../models/CartModel.php';

class CartController {
    private $model;

    public function __construct() {
        $this->model = new CartModel();
    }

    // Lấy giỏ hàng
    public function get($user_id) {
        if (!$user_id) {
            return ["error" => "Thiếu user_id"];
        }

        return $this->model->getByUser($user_id);
    }

    // Thêm vào giỏ
    public function add($data) {
        if (!isset($data['user_id'], $data['product_id'], $data['quantity'])) {
            return ["error" => "Thiếu dữ liệu"];
        }

        return $this->model->add(
            $data['user_id'],
            $data['product_id'],
            (int)$data['quantity']
        );
    }

    // Update số lượng
    public function update($data) {
        if (!isset($data['cart_id'], $data['quantity'])) {
            return ["error" => "Thiếu dữ liệu"];
        }

        return $this->model->update(
            $data['cart_id'],
            (int)$data['quantity']
        );
    }

    // Xoá item (quantity = 0)
    public function delete($cart_id) {
        if (!$cart_id) {
            return ["error" => "Thiếu cart_id"];
        }

        return $this->model->update($cart_id, 0);
    }
}