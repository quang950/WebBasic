<?php
// UserModel - Xử lý tất cả các thao tác với user trong database

class UserModel {
    private $conn;
    private $usersFile;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->usersFile = __DIR__ . '/../../DataBase/users.json';
    }

    private function isDbAvailable() {
        return $this->conn instanceof mysqli;
    }

    private function loadUsersFromFile() {
        if (!file_exists($this->usersFile)) {
            return [];
        }

        $content = file_get_contents($this->usersFile);
        if ($content === false || trim($content) === '') {
            return [];
        }

        $users = json_decode($content, true);
        return is_array($users) ? $users : [];
    }

    private function saveUsersToFile($users) {
        $dir = dirname($this->usersFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents(
            $this->usersFile,
            json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ) !== false;
    }

    private function buildFileUserPublic($user) {
        return [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'province' => $user['province'] ?? '',
            'address' => $user['address'] ?? '',
            'is_admin' => !empty($user['is_admin']) ? 1 : 0
        ];
    }

    private function syncUserToFile($userId, $firstName, $lastName, $phone, $birthDate, $province, $address, $email = '') {
        $users = $this->loadUsersFromFile();
        $updated = false;
        $numericUserId = (int)$userId;

        foreach ($users as &$u) {
            $idMatch = $numericUserId > 0 && isset($u['id']) && (int)$u['id'] === $numericUserId;
            $emailMatch =
                !$idMatch &&
                !empty($email) &&
                isset($u['email']) &&
                strtolower($u['email']) === strtolower($email);

            if ($idMatch || $emailMatch) {
                if ($numericUserId > 0) {
                    $u['id'] = $numericUserId;
                } else if (!isset($u['id']) || (int)$u['id'] <= 0) {
                    $u['id'] = empty($users) ? 1 : (max(array_column($users, 'id')) + 1);
                }
                $u['first_name'] = $firstName;
                $u['last_name'] = $lastName;
                $u['phone'] = $phone;
                $u['birth_date'] = $birthDate;
                $u['province'] = $province;
                $u['address'] = $address;
                if (!empty($email)) {
                    $u['email'] = $email;
                }
                $updated = true;
                break;
            }
        }
        unset($u);

        if (!$updated && !empty($email)) {
            $newId = $numericUserId > 0 ? $numericUserId : (empty($users) ? 1 : (max(array_column($users, 'id')) + 1));
            $users[] = [
                'id' => $newId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => '',
                'phone' => $phone,
                'birth_date' => $birthDate,
                'province' => $province,
                'address' => $address,
                'is_admin' => 0,
                'created_at' => date('c')
            ];
            $updated = true;
        }

        if (!$updated) {
            return false;
        }

        return $this->saveUsersToFile($users);
    }
    
    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists($email) {
        if (!$this->isDbAvailable()) {
            $users = $this->loadUsersFromFile();
            foreach ($users as $user) {
                if (isset($user['email']) && strtolower($user['email']) === strtolower($email)) {
                    return true;
                }
            }
            return false;
        }

        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->num_rows > 0;
    }
    
    /**
     * Đăng ký người dùng mới
     */
    public function register($firstName, $lastName, $email, $password, $phone, $birthDate, $province, $address) {
        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email đã được đăng ký'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if (!$this->isDbAvailable()) {
            $users = $this->loadUsersFromFile();
            $newId = empty($users) ? 1 : (max(array_column($users, 'id')) + 1);

            $users[] = [
                'id' => $newId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $hashedPassword,
                'phone' => $phone,
                'birth_date' => $birthDate,
                'province' => $province,
                'address' => $address,
                'is_admin' => 0,
                'created_at' => date('c')
            ];

            if (!$this->saveUsersToFile($users)) {
                return ['success' => false, 'message' => 'Không thể lưu dữ liệu người dùng'];
            }

            return [
                'success' => true,
                'message' => 'Đăng ký thành công',
                'user_id' => $newId,
                'storage' => 'file'
            ];
        }
        
        // Insert new user
        $query = "INSERT INTO users (first_name, last_name, email, password, phone, birth_date, province, address) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi server: ' . $this->conn->error];
        }
        
        $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $hashedPassword, $phone, $birthDate, $province, $address);
        
        if ($stmt->execute()) {
            $stmt->close();
            return [
                'success' => true,
                'message' => 'Đăng ký thành công',
                'user_id' => $this->conn->insert_id,
                'storage' => 'database'
            ];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Lỗi khi đăng ký: ' . $error];
        }
    }
    
    /**
     * Đăng nhập
     */
    public function login($email, $password) {
        if (!$this->isDbAvailable()) {
            $users = $this->loadUsersFromFile();
            $found = null;

            foreach ($users as $u) {
                if (isset($u['email']) && strtolower($u['email']) === strtolower($email)) {
                    $found = $u;
                    break;
                }
            }

            if (!$found || !password_verify($password, $found['password'])) {
                return ['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác'];
            }

            return [
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => $this->buildFileUserPublic($found),
                'storage' => 'file'
            ];
        }

        $query = "SELECT id, first_name, last_name, email, password, phone, province, address, is_admin FROM users WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi server: ' . $this->conn->error];
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            $stmt->close();
            return ['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác'];
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác'];
        }
        
        // Return user info without password
        return [
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'user' => [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'province' => $user['province'],
                'address' => $user['address'],
                'is_admin' => $user['is_admin']
            ],
            'storage' => 'database'
        ];
    }
    
    /**
     * Lấy thông tin user theo email
     */
    public function getUserByEmail($email) {
        if (!$this->isDbAvailable()) {
            $users = $this->loadUsersFromFile();
            foreach ($users as $u) {
                if (isset($u['email']) && strtolower($u['email']) === strtolower($email)) {
                    return [
                        'id' => $u['id'],
                        'first_name' => $u['first_name'],
                        'last_name' => $u['last_name'],
                        'email' => $u['email'],
                        'phone' => $u['phone'],
                        'birth_date' => $u['birth_date'],
                        'province' => $u['province'],
                        'address' => $u['address']
                    ];
                }
            }
            return null;
        }

        $query = "SELECT id, first_name, last_name, email, phone, birth_date, province, address FROM users WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            $stmt->close();
            return null;
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
    /**
     * Cập nhật thông tin user
     */
    public function updateUser($userId, $firstName, $lastName, $phone, $birthDate, $province, $address, $email = '') {
        if (!$this->isDbAvailable()) {
            if (!$this->syncUserToFile($userId, $firstName, $lastName, $phone, $birthDate, $province, $address, $email)) {
                return ['success' => false, 'message' => 'Không tìm thấy người dùng'];
            }

            return ['success' => true, 'message' => 'Cập nhật thông tin thành công'];
        }

        $query = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, birth_date = ?, province = ?, address = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi server'];
        }
        
        $stmt->bind_param("ssssssi", $firstName, $lastName, $phone, $birthDate, $province, $address, $userId);
        
        if ($stmt->execute()) {
            $stmt->close();
            // Đồng bộ users.json để đảm bảo frontend fallback luôn cập nhật.
            $this->syncUserToFile($userId, $firstName, $lastName, $phone, $birthDate, $province, $address, $email);
            return ['success' => true, 'message' => 'Cập nhật thông tin thành công'];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $error];
        }
    }
}
?>