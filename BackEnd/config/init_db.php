<?php
/**
 * Database initialization and schema creation
 */

require_once __DIR__ . '/db_connect.php';

// Check database connection
if (!$conn) {
    die("Lỗi kết nối cơ sở dữ liệu: " . ($dbError ?? "Unknown error"));
}

// SQL để tạo các tables
$tables = [
    // Users table
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(255),
        last_name VARCHAR(255),
        phone VARCHAR(20),
        birth_date DATE,
        province VARCHAR(255),
        address TEXT,
        is_admin TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );",
    
    // Products table
    "CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(255),
        price DECIMAL(15, 2),
        cost_price DECIMAL(15, 2) DEFAULT 0,
        description TEXT,
        image_url VARCHAR(500),
        stock INT DEFAULT 0,
        origin VARCHAR(255),
        year INT,
        fuel VARCHAR(100),
        seats INT,
        transmission VARCHAR(100),
        engine VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );",
    
    // Orders table
    "CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        total_price DECIMAL(15, 2),
        shipping_address TEXT,
        shipping_phone VARCHAR(20),
        status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );",
    
    // Order items table
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        product_name VARCHAR(255),
        quantity INT DEFAULT 1,
        unit_price DECIMAL(15, 2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    );",
    
    // Categories table
    "CREATE TABLE IF NOT EXISTS categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) UNIQUE NOT NULL,
        description TEXT,
        is_visible TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );",
    
    // Import tickets table
    "CREATE TABLE IF NOT EXISTS import_tickets (
        id INT PRIMARY KEY AUTO_INCREMENT,
        ticket_number VARCHAR(50) UNIQUE NOT NULL,
        import_date DATE NOT NULL,
        status ENUM('draft', 'completed') DEFAULT 'draft',
        total_import_price DECIMAL(15, 2) DEFAULT 0,
        notes TEXT,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    );",
    
    // Import items table (chi tiết phiếu nhập)
    "CREATE TABLE IF NOT EXISTS import_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        import_ticket_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        import_price DECIMAL(15, 2) NOT NULL,
        total_price DECIMAL(15, 2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (import_ticket_id) REFERENCES import_tickets(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );",
    
    // Stock history table (lịch sử thay đổi tồn kho)
    "CREATE TABLE IF NOT EXISTS stock_history (
        id INT PRIMARY KEY AUTO_INCREMENT,
        product_id INT NOT NULL,
        type ENUM('import', 'sale', 'adjustment') DEFAULT 'import',
        quantity INT NOT NULL,
        reason TEXT,
        recorded_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_product_date (product_id, created_at)
    );"
];

// Execute table creation
foreach ($tables as $sql) {
    if (!@$conn->query($sql)) {
        die("Error creating table: " . (@$conn->error ?? "Unknown error"));
    }
}

echo "Database tables created/verified successfully!";
if ($conn) {
    $conn->close();
}
?>
