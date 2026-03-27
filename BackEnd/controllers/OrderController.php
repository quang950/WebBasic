<?php
require_once __DIR__ . '/../models/OrderModel.php';

class OrderController {
    private $model;

    public function __construct() {
        $this->model = new OrderModel();
    }

    // 🟢 Tạo đơn hàng
    public function createOrder($user_id, $address, $payment_method) {
        // validate cơ bản
        if (!$user_id || !$address || !$payment_method) {
            return ["error" => "Missing required fields"];
        }

        return $this->model->createOrder($user_id, $address, $payment_method);
    }
}