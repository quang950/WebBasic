CREATE DATABASE IF NOT EXISTS car_shop;
USE car_shop;

DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS cart;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    birth_date DATE,
    province VARCHAR(100),
    address TEXT,
    avatar VARCHAR(255),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

INSERT INTO categories (name) VALUES 
('Toyota'),
('BMW'),
('Audi'),
('Mercedes'),
('Honda'),
('Hyundai'),
('Kia'),
('Lexus'),
('Vinfast');

INSERT INTO products (name, category_id, price, description, stock) VALUES
('Toyota Camry', 1, 1235000000, 'Mẫu sedan hạng D bình dân', 10),
('Toyota Vios', 1, 535000000, 'Xe bán tải yêu thích', 15),
('BMW X5', 2, 2500000000, 'SUV hạng sang từ Đức', 5),
('Audi A4', 3, 1950000000, 'Sedan hạng sang', 8),
('Mercedes C-Class', 4, 2150000000, 'Xe hạng sang Đức', 6),
('Honda Civic', 5, 730000000, 'Sedan bình dân Nhật', 12),
('Hyundai Tucson', 6, 850000000, 'SUV cA0m cỡ', 9),
('Kia Sorento', 7, 920000000, 'SUV 7 chỗ', 7),
('Lexus ES', 8, 1800000000, 'Sedan hạng sang Toyota', 4),
('VinFast Lux A2.0', 9, 1240000000, 'Xe hạng D Việt Nam', 11);
