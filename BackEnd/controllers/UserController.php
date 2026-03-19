<?php
// UserController - Xử lý business logic cho user

require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private $userModel;
    
    public function __construct($connection) {
        $this->userModel = new UserModel($connection);
    }
    
    /**
     * Xử lý đăng ký
     */
    public function handleRegister($data) {
        // Validate input
        $errors = [];
        
        if (empty($data['firstName'])) {
            $errors[] = 'Họ không được để trống';
        }
        if (empty($data['lastName'])) {
            $errors[] = 'Tên không được để trống';
        }
        if (empty($data['email'])) {
            $errors[] = 'Email không được để trống';
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        if (empty($data['password'])) {
            $errors[] = 'Mật khẩu không được để trống';
        } else if (strlen($data['password']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }
        if (empty($data['phone'])) {
            $errors[] = 'Số điện thoại không được để trống';
        } else if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }
        if (empty($data['birthDate'])) {
            $errors[] = 'Ngày sinh không được để trống';
        }
        if (empty($data['province'])) {
            $errors[] = 'Tỉnh/Thành phố không được để trống';
        }
        if (empty($data['address'])) {
            $errors[] = 'Địa chỉ giao hàng mặc định không được để trống';
        }
        
        // Return errors if any
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Register user
        $address = trim($data['address'] ?? '');
        return $this->userModel->register(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            $data['password'],
            $data['phone'],
            $data['birthDate'],
            $data['province'],
            $address
        );
    }
    
    /**
     * Xử lý đăng nhập
     */
    public function handleLogin($data) {
        // Validate input
        $errors = [];
        
        if (empty($data['email'])) {
            $errors[] = 'Email không được để trống';
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        if (empty($data['password'])) {
            $errors[] = 'Mật khẩu không được để trống';
        }
        
        // Return errors if any
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Login user
        return $this->userModel->login($data['email'], $data['password']);
    }
    
    /**
     * Lấy thông tin user theo email
     */
    public function getUserByEmail($email) {
        return $this->userModel->getUserByEmail($email);
    }
    
    /**
     * Cập nhật thông tin user
     */
    public function handleUpdateUser($userId, $data) {
        // Validate input
        if (empty($data['firstName']) || empty($data['lastName'])) {
            return ['success' => false, 'message' => 'Họ và tên không được để trống'];
        }
        
        return $this->userModel->updateUser(
            $userId,
            $data['firstName'],
            $data['lastName'],
            $data['phone'] ?? '',
            $data['birthDate'] ?? null,
            $data['province'] ?? '',
            $data['address'] ?? '',
            $data['email'] ?? ''
        );
    }
}
?>
