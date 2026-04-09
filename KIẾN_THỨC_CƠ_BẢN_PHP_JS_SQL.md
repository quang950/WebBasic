# Kiến Thức Cơ Bản PHP, JavaScript, SQL

## Mục Lục
1. [PHP Basics](#php-basics)
2. [JavaScript Basics](#javascript-basics)
3. [SQL Basics](#sql-basics)
4. [Kết Nối và Tương Tác](#kết-nối-và-tương-tác)

---

## PHP Basics

### 1. Connection (Kết Nối Database)

**Connection là gì?**
- Một đối tượng thiết lập kết nối giữa PHP và MySQL database
- Giống như "cầu nối" để truyền dữ liệu

**Ví dụ:**
```php
// File: BackEnd/config/db_connect.php
$conn = new mysqli($server, $user, $pass, $db);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

**Connection object chứa:**
- Thông tin host, user, password, database name
- Các method như `query()`, `prepare()`, `close()`
- Status của kết nối

### 2. Prepared Statements (Câu lệnh chuẩn bị)

**Tại sao dùng?**
- Ngăn SQL Injection (lỗi bảo mật)
- Tách code SQL từ data

**Cách hoạt động:**
```php
// Bước 1: Chuẩn bị template SQL với placeholder (?)
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");

// Bước 2: Bind (gắn) giá trị vào placeholder
// "i" = integer, "s" = string, "d" = double
$stmt->bind_param("i", $id);
$id = 5;

// Bước 3: Thực thi
$stmt->execute();

// Bước 4: Lấy kết quả
$result = $stmt->get_result();
$row = $result->fetch_assoc();
```

**Tại sao an toàn?**
- Input được chuẩn bị trước khi gửi SQL
- Nếu attacker gửi: `1; DROP TABLE products;`
  - Prepared statement sẽ coi nó là giá trị số, không phải SQL code

### 3. Sessions (Phiên làm việc)

**Session là gì?**
- Dữ liệu tạm thời lưu trên server cho mỗi user
- Giống "nhớ" người dùng sau khi đăng nhập

**Cách dùng:**
```php
// Bắt đầu session (phải ở đầu file, trước output)
session_start();

// Lưu thông tin user
$_SESSION['user_id'] = 123;
$_SESSION['username'] = 'john';
$_SESSION['is_admin'] = false;

// Lấy thông tin
echo $_SESSION['username'];  // Output: john

// Xóa session
$_SESSION = [];
session_destroy();
```

**Session vs Login:**
- User login → PHP lưu vào session
- Browser gửi request → PHP kiểm tra session
- Nếu session tồn tại → User "vẫn login"
- Nếu session bị xóa → User phải login lại

**Trong project:**
```php
// File: BackEnd/api/login.php
session_start();
// ... kiểm tra username/password ...
$_SESSION['user_id'] = $user['id'];
$_SESSION['is_admin'] = $user['is_admin'];
```

### 4. Arrays & Data Types

**Kiểu dữ liệu chính:**
```php
// String
$name = "John";

// Integer
$age = 25;

// Float/Double
$price = 99.99;

// Boolean
$is_active = true;

// Array - danh sách
$colors = ["red", "green", "blue"];
echo $colors[0];  // red

// Array - key-value (giống dictionary)
$user = [
    "id" => 1,
    "name" => "John",
    "email" => "john@example.com"
];
echo $user["name"];  // John

// Array từ database query
$result = $conn->query("SELECT * FROM users");
while ($row = $result->fetch_assoc()) {
    echo $row["name"];  // In ra tên mỗi user
}
```

### 5. Error Handling (Xử lý Lỗi)

**Try-Catch:**
```php
try {
    // Code có thể gây lỗi
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
} catch (Exception $e) {
    // Xử lý khi có lỗi
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
```

### 6. Transactions (Giao Dịch)

**Transaction là gì?**
- Nhóm câu lệnh SQL thực thi "tất cả hoặc không gì cả"
- Nếu 1 câu lệnh lỗi → rollback tất cả

**Ví dụ:**
```php
try {
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    // Câu lệnh 1: Trừ tiền từ Account A
    $conn->query("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    
    // Câu lệnh 2: Cộng tiền vào Account B
    $conn->query("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
    
    // Nếu tất cả OK → commit (lưu)
    $conn->commit();
    echo "Transfer successful";
} catch (Exception $e) {
    // Nếu có lỗi → rollback (hủy tất cả)
    $conn->rollback();
    echo "Transfer failed";
}
```

### 7. JSON (JavaScript Object Notation)

**JSON là gì?**
- Định dạng dữ liệu để trao đổi giữa PHP và JavaScript

**Chuyển đổi:**
```php
// PHP array → JSON (gửi để JavaScript nhận)
$data = ["id" => 1, "name" => "John", "role" => "admin"];
echo json_encode($data);
// Output: {"id":1,"name":"John","role":"admin"}

// JSON → PHP array (nhận từ JavaScript)
$json_string = '{"id":1,"name":"John"}';
$data = json_decode($json_string, true);  // true = associative array
echo $data["name"];  // John
```

---

## JavaScript Basics

### 1. Variables (Biến)

```javascript
// var (cũ, tránh dùng)
var name = "John";

// let (mới, phạm vi block)
let age = 25;

// const (hằng số, không thể thay đổi)
const PI = 3.14159;
```

### 2. Data Types

```javascript
// String
let text = "Hello";

// Number (cả integer và float)
let count = 42;
let price = 99.99;

// Boolean
let isActive = true;

// Array
let colors = ["red", "green", "blue"];
console.log(colors[0]);  // red

// Object (giống PHP array với key-value)
let user = {
    id: 1,
    name: "John",
    email: "john@example.com"
};
console.log(user.name);  // John
console.log(user["email"]);  // john@example.com
```

### 3. DOM (Document Object Model)

**DOM là gì?**
- Cách để JavaScript truy cập và thay đổi HTML elements

```html
<!-- HTML -->
<button id="myBtn">Click me</button>
<div class="message" data-user="john">Hello</div>
```

```javascript
// Lấy element bằng ID
let btn = document.getElementById("myBtn");

// Lấy element bằng class
let msg = document.querySelector(".message");

// Lấy tất cả elements có class nào đó
let all_msgs = document.querySelectorAll(".message");

// Thay đổi text
btn.textContent = "New text";
msg.innerHTML = "<strong>Bold text</strong>";

// Thay đổi attribute
msg.setAttribute("data-user", "jane");
let user = msg.getAttribute("data-user");  // jane

// Thêm/xóa class
btn.classList.add("active");
btn.classList.remove("active");

// Lấy giá trị từ input
let input = document.getElementById("search");
console.log(input.value);

// Thay đổi style
btn.style.backgroundColor = "blue";
btn.style.padding = "10px";
```

### 4. Functions (Hàm)

```javascript
// Hàm đơn giản
function greet(name) {
    return "Hello, " + name;
}
console.log(greet("John"));  // Hello, John

// Arrow function (mới)
const add = (a, b) => {
    return a + b;
};
console.log(add(5, 3));  // 8

// Arrow function ngắn gọn
const multiply = (a, b) => a * b;
console.log(multiply(5, 3));  // 15
```

### 5. Events (Sự kiện)

```html
<button id="submitBtn">Submit</button>
<input id="searchBox" type="text">
```

```javascript
// Click event
let btn = document.getElementById("submitBtn");
btn.addEventListener("click", function() {
    console.log("Button clicked!");
});

// Input event (khi user nhập)
let search = document.getElementById("searchBox");
search.addEventListener("input", function(event) {
    console.log(event.target.value);  // In ra khi user đang nhập
});

// Submit event (form)
document.getElementById("myForm").addEventListener("submit", function(event) {
    event.preventDefault();  // Chặn reload trang
    console.log("Form submitted!");
});
```

### 6. Fetch API (Gọi API)

**Fetch là gì?**
- Gửi request từ JavaScript đến PHP API

```javascript
// GET request
fetch("/BackEnd/api/products.php?category=laptop")
    .then(response => response.json())  // Chuyển JSON
    .then(data => {
        console.log(data);  // In ra kết quả
        // data = [{id:1, name:"Dell"}, {id:2, name:"HP"}]
    })
    .catch(error => console.log("Error:", error));

// POST request (gửi dữ liệu)
fetch("/BackEnd/api/add_to_cart.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        product_id: 5,
        quantity: 2
    })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.log("Error:", error));

// Async/Await (cách mới, dễ đọc hơn)
async function getProducts() {
    try {
        const response = await fetch("/BackEnd/api/products.php");
        const data = await response.json();
        console.log(data);
    } catch (error) {
        console.log("Error:", error);
    }
}
```

### 7. localStorage (Lưu dữ liệu trong browser)

```javascript
// Lưu
localStorage.setItem("username", "john");
localStorage.setItem("user_data", JSON.stringify({id: 1, name: "John"}));

// Lấy
let username = localStorage.getItem("username");  // "john"
let user_data = JSON.parse(localStorage.getItem("user_data"));  // Object

// Xóa
localStorage.removeItem("username");
localStorage.clear();  // Xóa tất cả

// Kiểm tra tồn tại
if (localStorage.getItem("username")) {
    console.log("User found");
} else {
    console.log("No user");
}
```

### 8. String & Number Operations

```javascript
// String
let text = "Hello World";
console.log(text.length);  // 11
console.log(text.toUpperCase());  // HELLO WORLD
console.log(text.includes("World"));  // true
console.log(text.substring(0, 5));  // Hello

// Number
let num = 42.567;
console.log(Math.floor(num));  // 42
console.log(Math.ceil(num));   // 43
console.log(Math.round(num));  // 43
console.log(num.toFixed(2));   // 42.57

// Array methods
let arr = [1, 2, 3, 4, 5];
console.log(arr.length);  // 5
arr.push(6);              // Thêm phần tử
arr.pop();                // Xóa phần tử cuối
console.log(arr.includes(3));  // true

// Map (biến đổi mỗi phần tử)
let doubled = arr.map(x => x * 2);  // [2, 4, 6, 8, 10]

// Filter (lọc)
let evens = arr.filter(x => x % 2 === 0);  // [2, 4]
```

---

## SQL Basics

### 1. SELECT (Truy vấn dữ liệu)

```sql
-- Lấy tất cả cột từ bảng
SELECT * FROM products;

-- Lấy cột cụ thể
SELECT id, name, price FROM products;

-- WHERE: Điều kiện
SELECT * FROM products WHERE price > 1000000;
SELECT * FROM products WHERE status = 'active';

-- AND / OR: Nhiều điều kiện
SELECT * FROM products 
WHERE price > 1000000 AND category_id = 1;

SELECT * FROM products 
WHERE price < 500000 OR category_id = 5;

-- LIKE: Tìm chuỗi
SELECT * FROM products WHERE name LIKE '%laptop%';
-- % = bất kỳ ký tự nào

-- ORDER BY: Sắp xếp
SELECT * FROM products ORDER BY price ASC;  -- Từ nhỏ đến lớn
SELECT * FROM products ORDER BY price DESC; -- Từ lớn đến nhỏ

-- LIMIT: Giới hạn số dòng
SELECT * FROM products LIMIT 10;        -- Lấy 10 dòng đầu
SELECT * FROM products LIMIT 10 OFFSET 20;  -- Lấy 10 dòng, bỏ qua 20 dòng đầu

-- COUNT: Đếm
SELECT COUNT(*) FROM products;          -- Đếm tất cả
SELECT COUNT(*) FROM products WHERE status = 'active';  -- Đếm có điều kiện

-- GROUP BY: Nhóm
SELECT category_id, COUNT(*) as total FROM products GROUP BY category_id;
```

### 2. JOIN (Kết hợp bảng)

**INNER JOIN: Chỉ lấy dòng khớp**
```sql
SELECT products.name, categories.name as category
FROM products
INNER JOIN categories ON products.category_id = categories.id;
```

**LEFT JOIN: Lấy tất cả từ bảng trái**
```sql
SELECT customers.name, COUNT(orders.id) as total_orders
FROM customers
LEFT JOIN orders ON customers.id = orders.customer_id
GROUP BY customers.id;
```

### 3. INSERT (Thêm dữ liệu)

```sql
-- Thêm 1 dòng
INSERT INTO products (name, price, category_id) 
VALUES ('Laptop Dell', 15000000, 1);

-- Thêm nhiều dòng
INSERT INTO products (name, price, category_id) VALUES 
('Phone Samsung', 5000000, 2),
('Tablet iPad', 10000000, 3);
```

### 4. UPDATE (Cập nhật dữ liệu)

```sql
-- Cập nhật 1 dòng
UPDATE products SET price = 16000000 WHERE id = 1;

-- Cập nhật nhiều cột
UPDATE products 
SET price = 20000000, status = 'sale' 
WHERE category_id = 1;

-- Cập nhật với phép tính
UPDATE products SET stock = stock - 5 WHERE id = 3;
```

### 5. DELETE (Xóa dữ liệu)

```sql
-- Xóa 1 dòng
DELETE FROM products WHERE id = 5;

-- Xóa nhiều dòng
DELETE FROM products WHERE price < 100000;

-- Xóa tất cả (cẩn thận!)
DELETE FROM products;
```

### 6. Data Types (Kiểu dữ liệu)

```sql
VARCHAR(255)    -- Chuỗi 255 ký tự
INT             -- Số nguyên
DECIMAL(10,2)   -- Số có 2 chữ số sau dấu phẩy (9999999.99)
TEXT            -- Chuỗi dài
DATE            -- Ngày (YYYY-MM-DD)
DATETIME        -- Ngày giờ
TIMESTAMP       -- Thời gian tự động
BOOLEAN         -- true/false (0/1)
```

### 7. Constraints (Ràng buộc)

```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,      -- Khóa chính, tự tăng
    name VARCHAR(255) NOT NULL,             -- Bắt buộc
    price DECIMAL(10,2) DEFAULT 0,          -- Giá trị mặc định
    category_id INT NOT NULL,
    FOREIGN KEY (category_id) 
        REFERENCES categories(id)            -- Liên kết với bảng khác
);
```

### 8. Transactions (Giao dịch)

```sql
START TRANSACTION;  -- Bắt đầu

UPDATE accounts SET balance = balance - 100 WHERE id = 1;
UPDATE accounts SET balance = balance + 100 WHERE id = 2;

COMMIT;    -- Lưu tất cả
-- hoặc
ROLLBACK;  -- Hủy tất cả
```

---

## Kết Nối và Tương Tác

### Flow của một Request

```
1. User nhấn nút trên trang (FrontEnd/index.php)
   ↓
2. JavaScript lắng nghe event (addEventListener)
   ↓
3. JavaScript gửi request qua Fetch API (POST/GET)
   ↓
4. Request đến PHP API (BackEnd/api/products.php)
   ↓
5. PHP kết nối database qua $conn
   ↓
6. PHP dùng SQL query lấy dữ liệu
   ↓
7. PHP chuyển dữ liệu sang JSON
   ↓
8. Gửi JSON response về JavaScript
   ↓ 
9. JavaScript nhận response và cập nhật DOM
   ↓
10. User nhấy thấy thay đổi trên trang
```

### Ví dụ thực tế từ project

**Thêm sản phẩm vào giỏ hàng:**

**Frontend (JavaScript):**
```javascript
// FrontEnd/assets/js/main.js
document.getElementById("addToCartBtn").addEventListener("click", async function() {
    let product_id = document.getElementById("product_id").value;
    let quantity = document.getElementById("quantity").value;
    
    // Gửi request đến API
    const response = await fetch("/BackEnd/api/add_to_cart.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            product_id: product_id,
            quantity: quantity
        })
    });
    
    const data = await response.json();
    console.log(data);  // {success: true, message: "Added to cart"}
});
```

**Backend (PHP):**
```php
// BackEnd/api/add_to_cart.php
session_start();
$conn = new mysqli("localhost", "root", "", "car_shop");

// Lấy dữ liệu từ request (JSON → PHP array)
$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data["product_id"];
$quantity = $data["quantity"];
$user_id = $_SESSION["user_id"];

try {
    // Kiểm tra sản phẩm tồn tại
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        throw new Exception("Product not found");
    }
    
    // Thêm vào giỏ hàng
    $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
    
    // Trả về response (PHP → JSON)
    echo json_encode([
        "success" => true,
        "message" => "Added to cart"
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
```

### Connection vs Query vs Transaction

| Khái niệm | Là gì | Ví dụ |
|-----------|-------|-------|
| **Connection** | "Cầu nối" giữa PHP và MySQL | `$conn = new mysqli(...)` |
| **Query** | Câu lệnh SQL duy nhất | `SELECT * FROM products` |
| **Prepared Statement** | Query chuẩn bị sẵn, an toàn | `$stmt->bind_param()` |
| **Transaction** | Nhóm câu lệnh (tất cả hoặc không) | `BEGIN TRANSACTION ... COMMIT` |
| **Session** | Dữ liệu lưu user sau login | `$_SESSION["user_id"]` |

### Bảo mật (Security)

**Các nguy hiểm thường gặp:**

1. **SQL Injection**
```php
// ❌ Không an toàn
$user_input = "1' OR '1'='1";
$query = "SELECT * FROM users WHERE id = $user_input";

// ✅ An toàn
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_input);
```

2. **Password hashing**
```php
// ❌ Không an toàn (lưu plain text)
INSERT INTO users (password) VALUES ('password123');

// ✅ An toàn (bcrypt)
INSERT INTO users (password) VALUES (password_hash('password123', PASSWORD_BCRYPT));
```

3. **XSS (Cross Site Scripting)**
```php
// ❌ Không an toàn
echo $_GET["name"];  // Attacker gửi: <script>alert('hack')</script>

// ✅ An toàn
echo htmlspecialchars($_GET["name"]);  // Chuyển <> thành &lt;&gt;
```

4. **Session timeout**
```php
// Logout chính xác (không chỉ unset)
$_SESSION = [];                                    // Xóa dữ liệu
setcookie(session_name(), '', time()-42000);     // Xóa cookie
session_destroy();                                // Xóa file session
```

---

## Tóm Tắt

| Công Nghệ | Chức Năng | Ví Dụ |
|-----------|-----------|-------|
| **PHP** | Xử lý backend, kết nối DB | Login, insert dữ liệu |
| **JavaScript** | Tương tác giao diện | Click button, validation form |
| **SQL** | Lưu trữ dữ liệu | Bảng users, products, orders |
| **JSON** | Trao đổi dữ liệu | API request/response |
| **Session** | Nhớ user | Sau khi login |
| **Connection** | Kết nối DB | `$conn` object |

