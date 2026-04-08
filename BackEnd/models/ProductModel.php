<?php

class ProductModel {
	private $conn;
	private $productsFile;

	public function __construct($connection) {
		$this->conn = $connection;
		$this->productsFile = __DIR__ . '/../../DataBase/products.json';
	}

	private function isDbAvailable() {
		return $this->conn instanceof mysqli;
	}

	private function loadProductsFromFile() {
		if (!file_exists($this->productsFile)) {
			return [];
		}

		$content = file_get_contents($this->productsFile);
		if ($content === false || trim($content) === '') {
			return [];
		}

		$content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

		$data = json_decode($content, true);
		return is_array($data) ? $data : [];
	}

	private function normalizeCategory($category) {
		return strtolower(trim((string)$category));
	}

	private function toInt($value, $default) {
		if ($value === null || $value === '') {
			return $default;
		}
		return (int)$value;
	}

	public function getProducts($filters = []) {
		$page = max(1, $this->toInt($filters['page'] ?? 1, 1));
		$limit = max(1, min(50, $this->toInt($filters['limit'] ?? 6, 6)));
		$offset = ($page - 1) * $limit;

		$name = trim((string)($filters['name'] ?? ''));
		$category = trim((string)($filters['category'] ?? ''));
		$minPrice = $filters['minPrice'] ?? null;
		$maxPrice = $filters['maxPrice'] ?? null;

		if ($this->isDbAvailable()) {
			$where = [];
			$params = [];
			$types = '';

			if ($name !== '') {
				$where[] = 'p.name LIKE ?';
				$types .= 's';
				$params[] = '%' . $name . '%';
			}

			if ($category !== '') {
				$where[] = 'LOWER(c.name) = ?';
				$types .= 's';
				$params[] = $this->normalizeCategory($category);
			}

			if ($minPrice !== null && $minPrice !== '') {
				$where[] = 'p.price >= ?';
				$types .= 'd';
				$params[] = (float)$minPrice;
			}

			if ($maxPrice !== null && $maxPrice !== '') {
				$where[] = 'p.price <= ?';
				$types .= 'd';
				$params[] = (float)$maxPrice;
			}
			
			// Only show products from visible categories
			$where[] = '(c.id IS NULL OR c.status = 1)';

			$whereSql = empty($where) ? '' : ('WHERE ' . implode(' AND ', $where));

			$countSql = "SELECT COUNT(*) AS total
						 FROM products p
						 LEFT JOIN categories c ON p.category_id = c.id
						 $whereSql";
			$countStmt = $this->conn->prepare($countSql);
			if (!$countStmt) {
				return ['success' => false, 'message' => 'Lỗi truy vấn số lượng sản phẩm'];
			}

			if (!empty($params)) {
				$countStmt->bind_param($types, ...$params);
			}
			$countStmt->execute();
			$countRes = $countStmt->get_result()->fetch_assoc();
			$total = (int)($countRes['total'] ?? 0);
			$countStmt->close();

			$sql = "SELECT p.id, p.name, c.name AS category, p.price, p.description,
						   p.image_url, p.stock, p.created_at
					FROM products p
					LEFT JOIN categories c ON p.category_id = c.id
					$whereSql
					ORDER BY p.id DESC
					LIMIT ? OFFSET ?";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				return ['success' => false, 'message' => 'Lỗi truy vấn danh sách sản phẩm'];
			}

			$dataTypes = $types . 'ii';
			$dataParams = $params;
			$dataParams[] = $limit;
			$dataParams[] = $offset;
			$stmt->bind_param($dataTypes, ...$dataParams);

			$stmt->execute();
			$result = $stmt->get_result();
			$items = [];
			while ($row = $result->fetch_assoc()) {
				// Đảm bảo hình ảnh có đường dẫn tuyệt đối - chỉ lấy tên file nếu có đường dẫn sai
				if (!empty($row['image_url'])) {
					$filename = basename($row['image_url']);
					$row['image_url'] = '/WebBasic/FrontEnd/assets/images/' . $filename;
				} else {
					$row['image_url'] = '/WebBasic/FrontEnd/assets/images/1.jpg';
				}
				$items[] = $row;
			}
			$stmt->close();

			return [
				'success' => true,
				'data' => $items,
				'pagination' => [
					'page' => $page,
					'limit' => $limit,
					'totalItems' => $total,
					'totalPages' => max(1, (int)ceil($total / $limit))
				],
				'storage' => 'database'
			];
		}

		$all = $this->loadProductsFromFile();

		$filtered = array_values(array_filter($all, function ($item) use ($name, $category, $minPrice, $maxPrice) {
			if ($name !== '' && stripos((string)$item['name'], $name) === false) {
				return false;
			}

			if ($category !== '' && $this->normalizeCategory($item['category'] ?? '') !== $this->normalizeCategory($category)) {
				return false;
			}

			$price = (float)($item['price'] ?? 0);
			if ($minPrice !== null && $minPrice !== '' && $price < (float)$minPrice) {
				return false;
			}
			if ($maxPrice !== null && $maxPrice !== '' && $price > (float)$maxPrice) {
				return false;
			}

			return true;
		}));

		$total = count($filtered);
		$items = array_slice($filtered, $offset, $limit);
		
		// Đảm bảo hình ảnh có đường dẫn tuyệt đối
		$items = array_map(function($item) {
			if (!empty($item['image']) && strpos($item['image'], 'http') === false && strpos($item['image'], '/') !== 0) {
				$item['image'] = '/WebBasic/FrontEnd/assets/images/' . ltrim($item['image'], '/');
			} elseif (empty($item['image'])) {
				$item['image'] = '/WebBasic/FrontEnd/assets/images/1.jpg';
			}
			return $item;
		}, $items);

		return [
			'success' => true,
			'data' => $items,
			'pagination' => [
				'page' => $page,
				'limit' => $limit,
				'totalItems' => $total,
				'totalPages' => max(1, (int)ceil($total / $limit))
			],
			'storage' => 'file'
		];
	}

	public function getProductById($id) {
		$productId = (int)$id;
		if ($productId <= 0) {
			return ['success' => false, 'message' => 'ID sản phẩm không hợp lệ'];
		}

		if ($this->isDbAvailable()) {
			$sql = "SELECT p.id, p.name, c.name AS category, p.price, p.description,
						   p.image_url, p.stock, p.created_at
					FROM products p
					LEFT JOIN categories c ON p.category_id = c.id
					WHERE p.id = ?
					LIMIT 1";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				return ['success' => false, 'message' => 'Lỗi truy vấn chi tiết sản phẩm'];
			}

			$stmt->bind_param('i', $productId);
			$stmt->execute();
			$result = $stmt->get_result();
			$product = $result->fetch_assoc();
			$stmt->close();

			if (!$product) {
				return ['success' => false, 'message' => 'Không tìm thấy sản phẩm'];
			}
			
			// Đảm bảo hình ảnh có đường dẫn tuyệt đối - chỉ lấy tên file nếu có đường dẫn sai
			if (!empty($product['image_url'])) {
				$filename = basename($product['image_url']);
				$product['image_url'] = '/WebBasic/FrontEnd/assets/images/' . $filename;
			} else {
				$product['image_url'] = '/WebBasic/FrontEnd/assets/images/1.jpg';
			}

			return ['success' => true, 'data' => $product, 'storage' => 'database'];
		}

		$all = $this->loadProductsFromFile();
		foreach ($all as $item) {
			if ((int)($item['id'] ?? 0) === $productId) {
				// Đảm bảo hình ảnh có đường dẫn tuyệt đối - chỉ lấy tên file nếu có đường dẫn sai
				if (!empty($item['image'])) {
					$filename = basename($item['image']);
					$item['image'] = '/WebBasic/FrontEnd/assets/images/' . $filename;
				} else {
					$item['image'] = '/WebBasic/FrontEnd/assets/images/1.jpg';
				}
				return ['success' => true, 'data' => $item, 'storage' => 'file'];
			}
		}

		return ['success' => false, 'message' => 'Không tìm thấy sản phẩm'];
	}
		// Thêm sản phẩm
	public function create($data) {
		$stmt = $this->conn->prepare("
			INSERT INTO products (name, price, description, image_url, stock, category_id)
			VALUES (?, ?, ?, ?, ?, ?)
		");

		$stmt->bind_param(
			"sdssii",
			$data['name'],
			$data['price'],
			$data['description'],
			$data['image_url'],
			$data['stock'],
			$data['category_id']
		);

		$stmt->execute();
		return $this->conn->insert_id;
	}

	// Cập nhật
	public function update($id, $data) {
		$stmt = $this->conn->prepare("
			UPDATE products
			SET name=?, price=?, description=?, image_url=?, stock=?, category_id=?
			WHERE id=?
		");

		$stmt->bind_param(
			"sdssiii",
			$data['name'],
			$data['price'],
			$data['description'],
			$data['image_url'],
			$data['stock'],
			$data['category_id'],
			$id
		);

		return $stmt->execute();
	}

	// Xoá
	public function delete($id) {
		$stmt = $this->conn->prepare("DELETE FROM products WHERE id=?");
		$stmt->bind_param("i", $id);
		return $stmt->execute();
	}
		
}

?>