<?php

require_once __DIR__ . '/../models/CartModel.php';

class CartController {
	private $model;

	public function __construct($connection) {
		$this->model = new CartModel($connection);
	}

	public function get($user_id) {
		if (!$user_id) {
			return ['success' => false, 'message' => 'Thiếu user_id'];
		}
		return $this->model->getByUser($user_id);
	}

	public function add($data) {
		if (!isset($data['user_id'], $data['product_id'], $data['quantity'])) {
			return ['success' => false, 'message' => 'Thiếu dữ liệu'];
		}

		return $this->model->add(
			(int)$data['user_id'],
			(int)$data['product_id'],
			(int)$data['quantity']
		);
	}

	public function update($data) {
		if (!isset($data['cart_id'], $data['quantity'])) {
			return ['success' => false, 'message' => 'Thiếu dữ liệu'];
		}

		return $this->model->update(
			(int)$data['cart_id'],
			(int)$data['quantity']
		);
	}
}