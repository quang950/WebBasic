<?php
require_once __DIR__ . '/../models/OrderModel.php';

class OrderController {
    private $model;

    public function __construct() {
        $this->model = new OrderModel();
    }

    // API chính
    public function create($data) {
        if (
            !isset($data['user_id']) ||
            !isset($data['shipping_address']) ||
            !isset($data['payment_method'])
        ) {
            return ["error" => "Missing required fields"];
        }

        return $this->model->createOrder(
            $data['user_id'],
            $data['shipping_address'],
            $data['shipping_phone'] ?? '',
            $data['payment_method']
        );
    }
}