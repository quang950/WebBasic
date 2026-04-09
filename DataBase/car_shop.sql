CREATE DATABASE IF NOT EXISTS car_shop;
USE car_shop;

-- Disable foreign key checks for safe drops
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS import_items;
DROP TABLE IF EXISTS import_tickets;
DROP TABLE IF EXISTS stock_history;
DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS user_shipping_addresses;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

SET FOREIGN_KEY_CHECKS = 1;

-- Create categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_visible BOOLEAN DEFAULT TRUE,
    status INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(100),
    category_id INT,
    price DECIMAL(15, 2) NOT NULL,
    cost_price DECIMAL(15, 2) DEFAULT 0,
    profit_margin FLOAT DEFAULT 10,
    description TEXT,
    image_url VARCHAR(255),
    stock INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 10,
    is_visible TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create users table
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
    locked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create user_shipping_addresses table
CREATE TABLE user_shipping_addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    recipient_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_detail TEXT NOT NULL,
    ward VARCHAR(100),
    district VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_default (user_id, is_default)
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(15,2),
    shipping_address TEXT,
    shipping_phone VARCHAR(20),
    shipping_ward VARCHAR(100),
    shipping_district VARCHAR(100),
    shipping_province VARCHAR(100),
    payment_method VARCHAR(50),
    status ENUM('new', 'processing', 'delivered', 'cancelled') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create order_details table
CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create cart table
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

-- Create stock_history table
CREATE TABLE stock_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    type VARCHAR(20) NOT NULL COMMENT 'import/export/adjustment',
    quantity INT NOT NULL,
    reason TEXT,
    previous_stock INT,
    new_stock INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_product_date (product_id, created_at)
);

-- Create import_tickets table
CREATE TABLE import_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(50) UNIQUE,
    supplier_name VARCHAR(100),
    import_date DATE,
    total_amount DECIMAL(15, 2) DEFAULT 0,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create import_items table
CREATE TABLE import_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    import_ticket_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    import_price DECIMAL(15, 2) NOT NULL,
    total_price DECIMAL(15, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_ticket_id) REFERENCES import_tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert default users
INSERT INTO users (first_name, last_name, email, password, phone, province, is_admin)
VALUES 
('Nguyễn', 'Vinh', 'vinhlhox2122006@gmail.com', 'ILOCKED!', '0923365944', 'hcm', 1),
('Vinh', 'Nguyen', 'vinh', '$2y$10$66qxPdWTX.0u/sFEiYP7HuzeAczLYFUtL1Gho/gREaVNy4AfCx0uC', NULL, NULL, 1),
('Trung', 'Nguyen', 'trung', '$2y$10$aUWJLvKQTx9kWrSge0IaK.Ou6vqNWjgSUTy5xIS2/F9dsZBOJIhom', NULL, NULL, 1),
('Tuấn', 'Nguyen', 'tuan', '$2y$10$7WFsZKhQ0h9.4cvcKy6XTuxrbJ7cKjQIeWq/ZVElH03Cmj3ehER5y', NULL, NULL, 1),
('Quang', 'Nguyen', 'quang', '$2y$10$mkYffWQ4t74.sZ/2WNz9lOjLDI2yltV2IWgFCo/Fgi4fIjk9r55H6', NULL, NULL, 1),
('Admin', 'System', 'admin', '123456', NULL, NULL, 1);

-- Insert default categories
INSERT INTO categories (name, description, is_visible, status) VALUES 
('Toyota', 'Thương hiệu xe Nhật Bản uy tín', 1, 1),
('Honda', 'Xe Nhật bền bỉ tiết kiệm', 1, 1),
('BMW', 'Xe Đức thể thao sang trọng', 1, 1),
('Mercedes', 'Thương hiệu xe Đức cao cấp', 1, 1),
('Audi', 'Xe Đức công nghệ cao', 1, 1),
('Lexus', 'Xe Nhật cao cấp', 1, 1),
('Hyundai', 'Xe Hàn Quốc hiện đại', 1, 1),
('Kia', 'Xe Hàn Quốc thời trang', 1, 1),
('VinFast', 'Xe điện Việt Nam', 1, 1),
('Mazda', 'Xe Nhật thiết kế đẹp', 1, 1),
('Ford', 'Xe Mỹ mạnh mẽ', 1, 1),
('Nissan', 'Xe Nhật công nghệ', 1, 1),
('Mitsubishi', 'Xe Nhật bền bỉ', 1, 1);

-- Insert sample products with images as filenames only
INSERT INTO products (name, brand, category_id, price, cost_price, profit_margin, description, image_url, stock) VALUES
('Alphard 2024', 'Toyota', 1, 4370000000, 3933000000, 10, 'MPV hạng sang, tiện nghi cao cấp', 'toyota-alphard.jpg', 3),
('Camry 2024', 'Toyota', 1, 1235000000, 1110000000, 10, 'Sedan hạng D êm ái, tiện nghi, tiết kiệm', 'toyota-camry.jpg', 10),
('Corolla 2024', 'Toyota', 1, 800000000, 720000000, 10, 'Sedan hạng C cân bằng, tiết kiệm', 'toyota-corolla.jpg', 12),
('Cross 2024', 'Toyota', 1, 820000000, 738000000, 10, 'Crossover đô thị, vận hành mượt', 'toyota-cross.jpg', 9),
('Fortuner 2024', 'Toyota', 1, 1350000000, 1215000000, 10, 'SUV 7 chỗ gầm cao, mạnh mẽ', 'toyota-fortuner.jpg', 10),
('Hilux 2024', 'Toyota', 1, 549000000, 494000000, 10, 'Pickup công dụng, khỏe khoắn', 'toyota-hilux.jpg', 8),
('Innova 2024', 'Toyota', 1, 755000000, 680000000, 10, 'MPV 7 chỗ rộng rãi', 'toyota-innova.jpg', 8),
('Raize 2024', 'Toyota', 1, 510000000, 459000000, 10, 'SUV cỡ nhỏ cơ động', 'toyota-raize.jpg', 11),
('Vios 2024', 'Toyota', 1, 592000000, 533000000, 10, 'Sedan đô thị bền bỉ', 'toyota-vios.jpg', 10),
('Yaris 2024', 'Toyota', 1, 684000000, 615000000, 10, 'Hatchback linh hoạt, dễ lái', 'toyota-yaris.jpg', 9),
('Accord 2024', 'Honda', 2, 1319000000, 1187000000, 10, 'Sedan hạng D cao cấp', 'honda-accord.jpg', 10),
('BR-V 2024', 'Honda', 2, 705000000, 635000000, 10, 'MPV 7 chỗ thực dụng', 'honda-brv.jpg', 6),
('City 2024', 'Honda', 2, 599000000, 539000000, 10, 'Sedan đô thị, vận hành mượt', 'honda-city.jpg', 10),
('Civic 2024', 'Honda', 2, 889000000, 800000000, 10, 'Sedan hạng C thể thao', 'honda-civic.jpg', 7),
('CR-V 2024', 'Honda', 2, 1029000000, 926000000, 10, 'SUV 5 chỗ được ưa chuộng', 'honda-crv.jpg', 10),
('HR-V 2024', 'Honda', 2, 869000000, 782000000, 10, 'Crossover đô thị linh hoạt', 'honda-hrv.jpg', 8),
('Jazz 2024', 'Honda', 2, 624000000, 562000000, 10, 'Hatchback 5 chỗ nhỏ gọn', 'honda-jazz.jpg', 10),
('NSX 2024', 'Honda', 2, 4883000000, 4395000000, 10, 'Siêu xe hybrid, hiệu suất cao', 'honda-nsx.jpg', 1),
('Odyssey 2024', 'Honda', 2, 1376000000, 1238000000, 10, 'MPV 7 chỗ cao cấp', 'honda-odyssey.jpg', 5),
('Pilot 2024', 'Honda', 2, 1289000000, 1160000000, 10, 'SUV 7 chỗ mạnh mẽ, rộng rãi', 'honda-pilot.jpg', 6),
('320i 2024', 'BMW', 3, 1399000000, 1259000000, 10, 'Sedan thể thao cân bằng', 'bmw-320i.jpg', 8),
('330i 2024', 'BMW', 3, 1719000000, 1547000000, 10, 'Sedan thể thao mạnh mẽ', 'bmw-330i.jpg', 6),
('430i 2024', 'BMW', 3, 3399000000, 3059000000, 10, 'Coupe 4 chỗ, thiết kế thể thao', 'bmw-430i.jpg', 4),
('520i 2024', 'BMW', 3, 1979000000, 1781000000, 10, 'Sedan hạng E êm ái', 'bmw-520i.jpg', 4),
('530i 2024', 'BMW', 3, 2499000000, 2249000000, 10, 'Sedan hiệu suất cao', 'bmw-530i.jpg', 3),
('730li 2024', 'BMW', 3, 4369000000, 3932000000, 10, 'Sedan hạng sang cỡ lớn', 'bmw-730li.jpg', 2),
('M8 2024', 'BMW', 3, 6999000000, 6299000000, 10, 'Coupe siêu thể thao, hiệu suất cực cao', 'bmw-m8.jpg', 1),
('X5 2024', 'BMW', 3, 4479000000, 4031000000, 10, 'SUV hạng sang rộng rãi', 'bmw-x5.jpg', 2),
('X7 2024', 'BMW', 3, 6889000000, 6200000000, 10, 'SUV full-size 7 chỗ đẳng cấp', 'bmw-x7.jpg', 1),
('Z4 2024', 'BMW', 3, 4239000000, 3815000000, 10, 'Roadster 2 chỗ mui trần', 'bmw-z4.jpg', 2),
('C200 2024', 'Mercedes', 4, 1669000000, 1502000000, 10, 'Sedan hạng C sang trọng', 'mercedes-c200.jpg', 7),
('E200 2024', 'Mercedes', 4, 2310000000, 2079000000, 10, 'Sedan hạng E êm ái', 'mercedes-e200.jpg', 4),
('GLC200 2024', 'Mercedes', 4, 1859000000, 1673000000, 10, 'SUV 5 chỗ cao cấp', 'mercedes-glc200.jpg', 6),
('GLC300 2024', 'Mercedes', 4, 2639000000, 2375000000, 10, 'SUV hạng sang, thiết kế thể thao', 'mercedes-glc300.jpg', 4),
('GLE450 2024', 'Mercedes', 4, 4409000000, 3968000000, 10, 'SUV hạng sang mạnh mẽ', 'mercedes-gle450.jpg', 2),
('Maybach GLS600 2024', 'Mercedes', 4, 12119000000, 10907000000, 10, 'SUV 7 chỗ siêu sang', 'mercedes-maybach-GLS600.jpg', 1),
('Maybach S450 2024', 'Mercedes', 4, 4969000000, 4472000000, 10, 'Sedan siêu sang, tiện nghi đẳng cấp', 'mercedes-maybach-S450.jpg', 1),
('Maybach S480 2024', 'Mercedes', 4, 8799000000, 7919000000, 10, 'Sedan siêu sang với công nghệ tối tân', 'mercedes-maybach-S480.jpg', 1),
('Maybach S650 2024', 'Mercedes', 4, 14899000000, 13409000000, 10, 'Sedan siêu sang đỉnh cao', 'mercedes-maybach-s650.jpg', 1),
('Maybach S680 2024', 'Mercedes', 4, 15999000000, 14399000000, 10, 'Sedan siêu sang mới nhất', 'mercedes-maybach-S680.jpg', 1),
('S450 2024', 'Mercedes', 4, 5500000000, 4950000000, 10, 'Sedan hạng sang, cách âm tuyệt vời', 'mercedes-S450.jpg', 1),
('AMG GT 2024', 'Mercedes', 4, 6888000000, 6199000000, 10, 'Coupe thể thao, cảm giác lái cực đỉnh', 'merc-amg.jpg', 1),
('A4 2024', 'Audi', 5, 1539000000, 1385000000, 10, 'Sedan hạng C công nghệ', 'audi-a4.jpg', 5),
('A6 2024', 'Audi', 5, 2099000000, 1889000000, 10, 'Sedan hạng D high-tech', 'audi-a6.jpg', 3),
('A8 2024', 'Audi', 5, 5299000000, 4769000000, 10, 'Sedan siêu sang, tiện nghi đỉnh cao', 'audi-a8.jpg', 1),
('Q3 2024', 'Audi', 5, 1089000000, 980000000, 10, 'SUV cỡ nhỏ cao cấp', 'audi-q3.jpg', 5),
('Q5 2024', 'Audi', 5, 1929000000, 1736000000, 10, 'SUV 5 chỗ thể thao', 'audi-q5.jpg', 4),
('Q7 2024', 'Audi', 5, 3300000000, 2970000000, 10, 'SUV 7 chỗ sang trọng', 'audi-q7.jpg', 2),
('RS 2024', 'Audi', 5, 4750000000, 4275000000, 10, 'Dòng RS hiệu suất cao', 'audi-rs.jpg', 1),
('RS7 2024', 'Audi', 5, 2300000000, 2070000000, 10, 'Sportback hiệu suất cao', 'audi-rs7.jpg', 1),
('S5 2024', 'Audi', 5, 2670000000, 2403000000, 10, 'Coupe thể thao tinh tế', 'audi-s5.jpg', 2),
('S8 2024', 'Audi', 5, 4599000000, 4139000000, 10, 'Sedan thể thao siêu sang', 'audi-s8.jpg', 1),
('ES250 2024', 'Lexus', 6, 2360000000, 2124000000, 10, 'Sedan hạng sang êm ái', 'lexus-es250.jpg', 4),
('GX550M 2024', 'Lexus', 6, 6200000000, 5580000000, 10, 'SUV địa hình hạng sang', 'lexus-gx550m.jpg', 1),
('LC500 2024', 'Lexus', 6, 2500000000, 2250000000, 10, 'Coupe GT sang trọng', 'lexus-lc500.jpg', 1),
('LM500h 2024', 'Lexus', 6, 8710000000, 7839000000, 10, 'MPV siêu sang, không gian đỉnh cao', 'lexus-lm500h.jpg', 1),
('LS500 2024', 'Lexus', 6, 7650000000, 6885000000, 10, 'Sedan flagship sang trọng', 'lexus-ls500.jpg', 1),
('LX600 2024', 'Lexus', 6, 9000000000, 8100000000, 10, 'SUV full-size hạng sang', 'lexus-lx600.jpg', 1),
('NX350h 2024', 'Lexus', 6, 3420000000, 3078000000, 10, 'SUV hybrid tiết kiệm', 'lexus-nx350h.jpg', 3),
('RX350 2024', 'Lexus', 6, 3430000000, 3087000000, 10, 'SUV hạng sang bền bỉ', 'lexus-rx350.jpg', 2),
('RX500h 2024', 'Lexus', 6, 4940000000, 4446000000, 10, 'SUV hybrid hiệu suất cao', 'lexus-rx500h.jpg', 1),
('UX 2024', 'Lexus', 6, 1800000000, 1620000000, 10, 'SUV cỡ nhỏ sang trọng, tiết kiệm', 'lexus-ux.jpg', 4),
('Accent 2024', 'Hyundai', 7, 569000000, 512000000, 10, 'Sedan hạng B tiết kiệm', 'hyundai-accent.jpg', 9),
('Creta 2024', 'Hyundai', 7, 715000000, 644000000, 10, 'SUV cỡ B thời trang', 'hyundai-creta.jpg', 8),
('Custin 2024', 'Hyundai', 7, 559000000, 503000000, 10, 'Sedan hạng B, vận hành mượt', 'hyundai-custin.jpg', 7),
('Elantra 2024', 'Hyundai', 7, 769000000, 692000000, 10, 'Sedan hạng C hiện đại', 'hyundai-elantra.jpg', 7),
('Grand i10 2024', 'Hyundai', 7, 435000000, 392000000, 10, 'Xe đô thị nhỏ gọn', 'hyundai-grand.jpg', 10),
('Palisade 2024', 'Hyundai', 7, 1589000000, 1430000000, 10, 'SUV 7 chỗ cỡ lớn', 'hyundai-palisade.jpg', 3),
('Santa Fe 2024', 'Hyundai', 7, 1340000000, 1206000000, 10, 'SUV 7 chỗ rộng rãi', 'hyundai-santafe.jpg', 5),
('Sonata 2024', 'Hyundai', 7, 705000000, 635000000, 10, 'Sedan hạng D trẻ trung', 'hyundai-sonata.jpg', 6),
('Stargazer 2024', 'Hyundai', 7, 685000000, 617000000, 10, 'MPV 7 chỗ gia đình', 'hyundai-stargazer.jpg', 5),
('Tucson 2024', 'Hyundai', 7, 769000000, 692000000, 10, 'SUV 5 chỗ hiện đại', 'hyundai-tucson.jpg', 8),
('Carens 2024', 'Kia', 8, 769000000, 692000000, 10, 'MPV 5+2 linh hoạt', 'kia-carens.jpg', 6),
('Carnival 2024', 'Kia', 8, 1589000000, 1430000000, 10, 'MPV 7 chỗ cỡ lớn', 'kia-carnival.jpg', 3),
('Cerato 2024', 'Kia', 8, 685000000, 617000000, 10, 'Sedan hạng C rộng rãi', 'kia-cerato.jpg', 6),
('K3 2024', 'Kia', 8, 819000000, 737000000, 10, 'Sedan hạng C thiết kế trẻ', 'kia-k3.jpg', 7),
('K5 2024', 'Kia', 8, 999000000, 899000000, 10, 'Sedan hạng D thiết kế mới', 'kia-k5.jpg', 5),
('Morning 2024', 'Kia', 8, 424000000, 382000000, 10, 'Hatchback đô thị nhỏ gọn', 'kia-morning.jpg', 10),
('Soluto 2024', 'Kia', 8, 482000000, 434000000, 10, 'Sedan hạng B giá tốt', 'kia-soluto.jpg', 8),
('Sonet 2024', 'Kia', 8, 624000000, 562000000, 10, 'SUV cỡ B trẻ trung', 'kia-sonet.jpg', 7),
('Sorento 2024', 'Kia', 8, 1149000000, 1034000000, 10, 'SUV 7 chỗ cao cấp', 'kia-sorento.jpg', 6),
('Sportage 2024', 'Kia', 8, 769000000, 692000000, 10, 'SUV 5 chỗ phong cách', 'kia-sportage.jpg', 7),
('Fadil 2024', 'VinFast', 9, 389000000, 350000000, 10, 'Sedan hạng A tiết kiệm', 'vinfast-fadil.jpg', 8),
('Lux A 2024', 'VinFast', 9, 781000000, 703000000, 10, 'Sedan tiện nghi, vận hành mạnh', 'vinfast-luxa.jpg', 4),
('President 2024', 'VinFast', 9, 1580000000, 1422000000, 10, 'Sedan tiêu chuẩn quốc gia, sang trọng', 'vinfast-president.jpg', 2),
('VF3 2024', 'VinFast', 9, 315000000, 284000000, 10, 'Xe đô thị cỡ nhỏ', 'vinfast-vf3.jpg', 8),
('VF5 2024', 'VinFast', 9, 529000000, 476000000, 10, 'Crossover đô thị hiện đại', 'vinfast-vf5.jpg', 6),
('VF6 2024', 'VinFast', 9, 689000000, 620000000, 10, 'SUV cỡ nhỏ, công nghệ hỗ trợ', 'vinfast-vf6.jpg', 5),
('VF7 2024', 'VinFast', 9, 949000000, 854000000, 10, 'SUV hạng C thiết kế cá tính', 'vinfast-vf7.jpg', 4),
('VF8 2024', 'VinFast', 9, 1215000000, 1094000000, 10, 'SUV 5 chỗ vận hành êm ái', 'vinfast-vf8.jpg', 3),
('VF9 2024', 'VinFast', 9, 1680000000, 1512000000, 10, 'SUV 7 chỗ cao cấp', 'vinfast-vf9.jpg', 2),
('VF e34 2024', 'VinFast', 9, 710000000, 639000000, 10, 'SUV tiện nghi, hiệu suất cao', 'vinfast-vfe34.jpg', 3);
