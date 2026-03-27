<?php
require_once __DIR__ . '/../models/CartModel.php';

class CartController {
    private $model;

    public function __construct() {
        $this->model = new CartModel();
    }

    // Thêm vào giỏ hàng
    public function addToCart($user_id, $product_id, $quantity) {
        if (!$user_id || !$product_id || $quantity <= 0) {
            return ["error" => "Invalid data"];
        }

        return $this->model->add($user_id, $product_id, $quantity);
    }

    // Lấy giỏ hàng theo user
    public function getCart($user_id) {
        if (!$user_id) {
            return ["error" => "User ID required"];
        }

        return $this->model->getByUser($user_id);
    }

    // Cập nhật số lượng
    public function updateCart($cart_id, $quantity) {
        if (!$cart_id || $quantity <= 0) {
            return ["error" => "Invalid data"];
        }

        return $this->model->update($cart_id, $quantity);
    }

    // Xoá sản phẩm khỏi giỏ
    public function deleteCart($cart_id) {
        if (!$cart_id) {
            return ["error" => "Cart ID required"];
        }

        return $this->model->delete($cart_id);
    }

    // alias để giữ tương thích với các tên API cũ
    public function removeFromCart($cart_id) {
        return $this->deleteCart($cart_id);
    }

    public function updateQuantity($cart_id, $quantity) {
    if (!$cart_id || $quantity === null) {
        return ["error" => "Missing data"];
    }

    $result = $this->model->updateQuantity($cart_id, $quantity);

    return $result
        ? ["message" => "Cart updated"]
        : ["error" => "Update failed"];
    }
}