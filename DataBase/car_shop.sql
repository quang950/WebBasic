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

    INSERT INTO users (first_name, last_name, email, password, is_admin)
    VALUES ('Admin', 'System', 'admin', '123456', 1);

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

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(15,2),
    address TEXT,
    payment_method VARCHAR(50),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE orders 
CHANGE address shipping_address TEXT,
ADD COLUMN shipping_phone VARCHAR(20);

CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(15,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
ALTER TABLE products 
ADD COLUMN price_cost DECIMAL(15, 2) DEFAULT 0, 
ADD COLUMN profit_margin FLOAT DEFAULT 10;
-- Cập nhật thử một ít dữ liệu để có giá
UPDATE products SET price_cost = price * 0.9, profit_margin = 10;



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

INSERT INTO products (name, category_id, price, description, image_url, stock) VALUES
('Toyota Camry', 1, 1235000000, 'Sedan hạng D êm ái, tiện nghi, tiết kiệm.', 'assets/images/toyota-camry.jpg', 10),
('Toyota Vios', 1, 592000000, 'Sedan đô thị bền bỉ, tiết kiệm nhiên liệu.', 'assets/images/toyota-vios.jpg', 10),
('Toyota Fortuner', 1, 1350000000, 'SUV 7 chỗ gầm cao, mạnh mẽ và đa dụng.', 'assets/images/toyota-fortuner.jpg', 10),
('Toyota Cross', 1, 820000000, 'Crossover đô thị, vận hành mượt và tiết kiệm.', 'assets/images/toyota-cross.jpg', 10),
('Toyota Innova', 1, 755000000, 'MPV 7 chỗ rộng rãi, phù hợp gia đình.', 'assets/images/toyota-innova.jpg', 10),
('Toyota Yaris', 1, 684000000, 'Hatchback linh hoạt, dễ lái, tiết kiệm.', 'assets/images/toyota-yaris.jpg', 10),
('Toyota Corolla', 1, 800000000, 'Sedan hạng C cân bằng giữa hiệu suất và tiết kiệm.', 'assets/images/toyota-corolla.jpg', 10),
('Toyota Raize', 1, 510000000, 'SUV cỡ nhỏ cơ động, tiết kiệm nhiên liệu.', 'assets/images/toyota-raize.jpg', 10),
('Toyota Alphard', 1, 4370000000, 'MPV hạng sang, tiện nghi cao cấp, vận hành êm ái.', 'assets/images/toyota-alphard.jpg', 10),
('Mercedes C200', 4, 1669000000, 'Sedan hạng C sang trọng, vận hành linh hoạt và tiết kiệm.', 'assets/images/mercedes-c200.jpg', 10),
('Mercedes E200', 4, 2310000000, 'Sedan hạng E êm ái, nhiều công nghệ an toàn và tiện nghi.', 'assets/images/mercedes-e200.jpg', 10),
('Mercedes GLC200', 4, 1859000000, 'SUV 5 chỗ cao cấp, vận hành ổn định và tiết kiệm.', 'assets/images/mercedes-glc200.jpg', 10),
('Maybach GLC300', 4, 2639000000, 'SUV hạng sang, thiết kế thể thao và tiện nghi hiện đại.', 'assets/images/mercedes-glc300.jpg', 10),
('Maybach GLE450', 4, 4409000000, 'SUV hạng sang mạnh mẽ, không gian rộng và êm ái.', 'assets/images/mercedes-gle450.jpg', 10),
('Maybach GLS600', 4, 12119000000, 'SUV 7 chỗ siêu sang, tiện nghi đỉnh cao và êm ái.', 'assets/images/mercedes-maybach-GLS600.jpg', 10),
('Maybach S450', 4, 4969000000, 'Sedan siêu sang, tiện nghi đẳng cấp và cách âm tuyệt vời.', 'assets/images/mercedes-maybach-S450.jpg', 10),
('Maybach S480', 4, 8799000000, 'Sedan siêu sang với công nghệ tối tân và nội thất xa xỉ.', 'assets/images/mercedes-maybach-S480.jpg', 10),
('Maybach S650', 4, 14899000000, 'Đỉnh cao sang trọng, vận hành êm ái và quyền lực.', 'assets/images/mercedes-maybach-S650.jpg', 10),
('BMW 320i', 2, 1399000000, 'Sedan thể thao cân bằng giữa thoải mái và hiệu suất.', 'assets/images/bmw-320i.jpg', 10),
('BMW 330i', 2, 1719000000, 'Sedan thể thao mạnh mẽ hơn, cảm giác lái hứng khởi.', 'assets/images/bmw-330i.jpg', 10),
('BMW 430i', 2, 3399000000, 'Coupe 4 chỗ, thiết kế thể thao và hiện đại.', 'assets/images/bmw-430i.jpg', 10),
('BMW 520i', 2, 1979000000, 'Sedan hạng E sang trọng, êm ái và an toàn.', 'assets/images/bmw-520i.jpg', 10),
('BMW 530i', 2, 2499000000, 'Sedan hạng E hiệu suất cao, nhiều công nghệ hiện đại.', 'assets/images/bmw-530i.jpg', 10),
('BMW 730Li', 2, 4369000000, 'Sedan hạng sang cỡ lớn, êm ái và tiện nghi cao cấp.', 'assets/images/bmw-730li.jpg', 10),
('BMW X5', 2, 4479000000, 'SUV hạng sang rộng rãi, mạnh mẽ và an toàn.', 'assets/images/bmw-x5.jpg', 10),
('BMW X7', 2, 6889000000, 'SUV full-size 7 chỗ đẳng cấp, nội thất xa xỉ.', 'assets/images/bmw-x7.jpg', 10),
('BMW Z4', 2, 4239000000, 'Roadster 2 chỗ mui trần, phong cách và cảm xúc.', 'assets/images/bmw-z4.jpg', 10),
('Audi A4', 3, 1046000000, 'Sedan hạng C sang trọng, cách âm tốt và êm ái.', 'assets/images/audi-a4.jpg', 10),
('Audi A6', 3, 1510000000, 'Sedan hạng E tinh tế, nhiều công nghệ mới.', 'assets/images/audi-a6.jpg', 10),
('Audi A8', 3, 5299000000, 'Sedan hạng sang cỡ lớn, tiện nghi đỉnh cao.', 'assets/images/audi-a8.jpg', 10),
('Audi Q3', 3, 1089000000, 'SUV cỡ nhỏ cao cấp, đa dụng trong đô thị.', 'assets/images/audi-q3.jpg', 10),
('Audi Q5', 3, 2039000000, 'SUV hạng sang 5 chỗ, vận hành êm ái và an toàn.', 'assets/images/audi-q5.jpg', 10),
('Audi Q7', 3, 3300000000, 'SUV 7 chỗ sang trọng, rộng rãi và mạnh mẽ.', 'assets/images/audi-q7.jpg', 10),
('Audi RS', 3, 4750000000, 'Dòng RS hiệu suất cao, thiết kế thể thao cực kỳ ấn tượng.', 'assets/images/audi-rs.jpg', 10),
('Audi RS7', 3, 2300000000, 'Sportback hiệu suất cao, sang trọng và cá tính.', 'assets/images/audi-rs7.jpg', 10),
('Audi S5', 3, 2670000000, 'Coupe thể thao tinh tế, khả năng vận hành ấn tượng.', 'assets/images/audi-s5.jpg', 10),
('Lexus ES250', 8, 2360000000, 'Sedan hạng sang êm ái, hướng đến sự thoải mái.', 'assets/images/lexus-es250.jpg', 10),
('Lexus RX350', 8, 3430000000, 'SUV hạng sang bền bỉ, tiện nghi cao cấp và êm ái.', 'assets/images/lexus-rx350.jpg', 10),
('Lexus NX350h', 8, 3420000000, 'SUV hybrid tiết kiệm, vận hành mượt mà và êm ái.', 'assets/images/lexus-nx350h.jpg', 10),
('Lexus RX500h', 8, 4940000000, 'SUV hybrid hiệu suất cao, công nghệ hiện đại.', 'assets/images/lexus-rx500h.jpg', 10),
('Lexus GX550M', 8, 6200000000, 'SUV địa hình hạng sang, khỏe khoắn và bền bỉ.', 'assets/images/lexus-gx550m.jpg', 10),
('Lexus LS500', 8, 7650000000, 'Sedan flagship sang trọng, cách âm tuyệt vời.', 'assets/images/lexus-ls500.jpg', 10),
('Lexus LX600', 8, 9000000000, 'SUV full-size hạng sang, mạnh mẽ và bền bỉ.', 'assets/images/lexus-lx600.jpg', 10),
('Lexus LC500', 8, 2500000000, 'Coupe GT sang trọng, âm thanh động cơ đầy cảm xúc.', 'assets/images/lexus-lc500.jpg', 10),
('Lexus LM500h', 8, 8710000000, 'MPV siêu sang, không gian đỉnh cao và cực kỳ êm ái.', 'assets/images/lexus-lm500h.jpg', 10),
('Honda City', 5, 568000000, 'Sedan hạng B tiết kiệm, bền bỉ và dễ lái.', 'assets/images/honda-city.jpg', 10),
('Honda Civic', 5, 889000000, 'Sedan hạng C thể thao, cảm giác lái phấn khích.', 'assets/images/honda-civic.jpg', 10),
('Honda CR-V', 5, 1259000000, 'SUV 7 chỗ tiện nghi, rộng rãi cho gia đình.', 'assets/images/honda-crv.jpg', 10),
('Honda BR-V', 5, 705000000, 'MPV 7 chỗ thực dụng, kinh tế và bền bỉ.', 'assets/images/honda-brv.jpg', 10),
('Honda HR-V', 5, 869000000, 'Crossover đô thị linh hoạt, tiết kiệm và hiện đại.', 'assets/images/honda-hrv.jpg', 10),
('Honda NSX', 5, 4883000000, 'Siêu xe hybrid, hiệu suất cao và công nghệ tiên tiến.', 'assets/images/honda-nsx.jpg', 10),
('Honda Accord', 5, 1319000000, 'Sedan hạng D rộng rãi, êm ái và an toàn.', 'assets/images/honda-accord.jpg', 10),
('Honda Odyssey', 5, 1376000000, 'MPV cao cấp 7-8 chỗ, rộng rãi và tiện nghi đầy đủ cho gia đình.', 'assets/images/honda-odyssey.jpg', 10),
('Honda Jazz', 5, 624000000, 'Hatchback 5 chỗ nhỏ gọn, tiết kiệm nhiên liệu và linh hoạt trong đô thị.', 'assets/images/honda-jazz.jpg', 10),
('Hyundai Elantra', 6, 769000000, 'Sedan hạng C hiện đại, trang bị phong phú.', 'assets/images/hyundai-elantra.jpg', 10),
('Hyundai Accent', 6, 569000000, 'Sedan hạng B tiết kiệm và nhiều tiện nghi.', 'assets/images/hyundai-accent.jpg', 10),
('Hyundai Creta', 6, 715000000, 'SUV cỡ B thời trang, phù hợp đô thị.', 'assets/images/hyundai-creta.jpg', 10),
('Hyundai Tucson', 6, 989000000, 'Crossover hạng C rộng rãi, nhiều công nghệ.', 'assets/images/hyundai-tucson.jpg', 10),
('Hyundai Santafe', 6, 1365000000, 'SUV 7 chỗ cao cấp, thiết kế mới ấn tượng.', 'assets/images/hyundai-santafe.jpg', 10),
('Hyundai Grand', 6, 435000000, 'Xe đô thị nhỏ gọn, linh hoạt và tiết kiệm.', 'assets/images/hyundai-grand.jpg', 10),
('Hyundai Palisade', 6, 1589000000, 'SUV 7 chỗ cỡ lớn, sang trọng và mạnh mẽ.', 'assets/images/hyundai-palisade.jpg', 10),
('Hyundai Stargazer', 6, 685000000, 'MPV 7 chỗ gia đình, tiện nghi và giá hợp lý.', 'assets/images/hyundai-stargazer.jpg', 10),
('Hyundai Sonata', 6, 705000000, 'Sedan hạng D trẻ trung, nhiều công nghệ tiện ích.', 'assets/images/hyundai-sonata.jpg', 10),
('KIA Morning', 7, 424000000, 'Hatchback đô thị nhỏ gọn, tiết kiệm và linh hoạt.', 'assets/images/kia-morning.jpg', 10),
('KIA Soluto', 7, 482000000, 'Sedan hạng B giá tốt, thực dụng và tiết kiệm.', 'assets/images/kia-soluto.jpg', 10),
('KIA Sonet', 7, 624000000, 'SUV cỡ B trẻ trung, trang bị phong phú.', 'assets/images/kia-sonet.jpg', 10),
('KIA Carnival', 7, 1589000000, 'MPV 7 chỗ cỡ lớn, êm ái và tiện nghi.', 'assets/images/kia-carnival.jpg', 10),
('KIA Sorento', 7, 1184000000, 'SUV 7 chỗ cao cấp, thiết kế hiện đại và tiện nghi.', 'assets/images/kia-sorento.jpg', 10),
('KIA Sportage', 7, 879000000, 'SUV hạng C thể thao, nhiều công nghệ.', 'assets/images/kia-sportage.jpg', 10),
('KIA Carens', 7, 769000000, 'MPV 5+2 linh hoạt, phù hợp gia đình trẻ.', 'assets/images/kia-carens.jpg', 10),
('KIA Cerato', 7, 685000000, 'Sedan hạng C rộng rãi, vận hành mượt mà.', 'assets/images/kia-cerato.jpg', 10),
('KIA K3', 7, 819000000, 'Sedan hạng C thiết kế trẻ trung, tiết kiệm.', 'assets/images/kia-k3.jpg', 10),
('VinFast VF e34', 9, 710000000, 'SUV tiện nghi với hiệu suất cao, nội thất thoải mái và công nghệ an toàn hiện đại.', 'assets/images/vinfast-vfe34.jpg', 10),
('VinFast VF3', 9, 315000000, 'Xe đô thị cỡ nhỏ, linh hoạt, tiết kiệm chi phí vận hành.', 'assets/images/vinfast-vf3.jpg', 10),
('VinFast VF5', 9, 529000000, 'Crossover đô thị hiện đại, phù hợp di chuyển hằng ngày.', 'assets/images/vinfast-vf5.jpg', 10),
('VinFast VF6', 9, 689000000, 'SUV cỡ nhỏ với hiệu suất cao và công nghệ hỗ trợ lái.', 'assets/images/vinfast-vf6.jpg', 10),
('VinFast VF7', 9, 949000000, 'SUV hạng C thiết kế cá tính, nhiều tiện nghi.', 'assets/images/vinfast-vf7.jpg', 10),
('VinFast VF8', 9, 1215000000, 'SUV 5 chỗ vận hành êm ái, nội thất rộng rãi.', 'assets/images/vinfast-vf8.jpg', 10),
('VinFast VF9', 9, 1680000000, 'SUV 7 chỗ cao cấp, phù hợp gia đình, nhiều công nghệ an toàn.', 'assets/images/vinfast-vf9.jpg', 10),
('VinFast Lux A', 9, 781000000, 'Sedan tiện nghi với khả năng vận hành mạnh mẽ và ổn định.', 'assets/images/vinfast-luxa.jpg', 10);
