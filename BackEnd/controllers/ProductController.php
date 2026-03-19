<?php

require_once __DIR__ . '/../models/ProductModel.php';

class ProductController {
	private $productModel;

	public function __construct($connection) {
		$this->productModel = new ProductModel($connection);
	}

	private function parseFilters($query) {
		return [
			'name' => trim((string)($query['name'] ?? '')),
			'category' => trim((string)($query['category'] ?? $query['brand'] ?? '')),
			'minPrice' => $query['minPrice'] ?? $query['priceFrom'] ?? null,
			'maxPrice' => $query['maxPrice'] ?? $query['priceTo'] ?? null,
			'page' => isset($query['page']) ? (int)$query['page'] : 1,
			'limit' => isset($query['limit']) ? (int)$query['limit'] : 6
		];
	}

	public function handleGetProducts($query) {
		$filters = $this->parseFilters($query);
		return $this->productModel->getProducts($filters);
	}

	public function handleSearch($query) {
		$filters = $this->parseFilters($query);
		return $this->productModel->getProducts($filters);
	}

	public function handleGetProductDetail($query) {
		$id = $query['id'] ?? null;
		if ($id === null || $id === '') {
			return ['success' => false, 'message' => 'Thiếu ID sản phẩm'];
		}
		return $this->productModel->getProductById($id);
	}
}

?>
