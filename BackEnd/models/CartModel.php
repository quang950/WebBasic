<?php

class CartModel {
	private $conn;

	public function __construct($connection) {
		$this->conn = $connection;
	}

	public function getByUser($user_id) {
		if (!$this->conn) {
			return ['success' => false, 'message' => 'Lỗi DB'];
		}

		$sql = "SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image_url
				FROM cart c
				JOIN products p ON c.product_id = p.id
				WHERE c.user_id = ?";

		$stmt = $this->conn->prepare($sql);
		if (!$stmt) {
            return [
                'success' => false,
                'message' => 'SQL lỗi',
                'error' => $this->conn->error
            ];
}

		$stmt->bind_param("i", $user_id);
		$stmt->execute();

		$result = $stmt->get_result();
		$data = [];

		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		return [
			'success' => true,
			'data' => $data
		];
	}

    public function add($user_id, $product_id, $quantity) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Lỗi DB'];
        }

        // Try INSERT first
        $sql = "INSERT INTO cart(user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'SQL lỗi',
                'error' => $this->conn->error
            ];
        }

        $stmt->bind_param("iii", $user_id, $product_id, $quantity);

        if ($stmt->execute()) {
            return ['success' => true];
        }

        // If INSERT fails (duplicate key), UPDATE quantity instead
        $error = $stmt->error;
        if (strpos($error, 'Duplicate') !== false) {
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'SQL lỗi',
                    'error' => $this->conn->error
                ];
            }

            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            if ($stmt->execute()) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'message' => 'Không thể cập nhật giỏ hàng',
                'error' => $stmt->error
            ];
        }

        return [
            'success' => false,
            'message' => 'Execute lỗi',
            'error' => $error
        ];
    }
	public function update($cart_id, $quantity) {
		if (!$this->conn) {
			return ['success' => false, 'message' => 'Lỗi DB'];
		}

		if ($quantity <= 0) {
			$stmt = $this->conn->prepare("DELETE FROM cart WHERE id=?");
			$stmt->bind_param("i", $cart_id);
			return ['success' => $stmt->execute()];
		}

		$stmt = $this->conn->prepare("UPDATE cart SET quantity=? WHERE id=?");

		if (!$stmt) {
			return ['success' => false, 'message' => 'SQL lỗi'];
		}

		$stmt->bind_param("ii", $quantity, $cart_id);

		return ['success' => $stmt->execute()];
	}

	public function remove($cart_id) {
		if (!$this->conn) {
			return ['success' => false, 'message' => 'Lỗi DB'];
		}

		$stmt = $this->conn->prepare("DELETE FROM cart WHERE id = ?");
		if (!$stmt) {
			return ['success' => false, 'message' => 'SQL lỗi'];
		}

		$stmt->bind_param("i", $cart_id);
		if (!$stmt->execute()) {
			return ['success' => false, 'message' => 'Execute lỗi'];
		}

		return ['success' => true];
	}

	public function clear($user_id) {
		if (!$this->conn) {
			return ['success' => false, 'message' => 'Lỗi DB'];
		}

		$stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
		if (!$stmt) {
			return ['success' => false, 'message' => 'SQL lỗi'];
		}

		$stmt->bind_param("i", $user_id);
		if (!$stmt->execute()) {
			return ['success' => false, 'message' => 'Execute lỗi'];
		}

		return ['success' => true];
	}
}