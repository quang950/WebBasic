# WebBasic Database Setup Guide

## Backend Integration Complete ✅

Các API đã được tạo để kết nối Frontend với Database:

### API Files đã tạo:
1. **`BackEnd/api/register.php`** - API đăng ký user
2. **`BackEnd/api/login.php`** - API đăng nhập user
3. **`BackEnd/api/place_order.php`** - API đặt hàng
4. **`BackEnd/api/get_orders.php`** - API lấy danh sách đơn hàng
5. **`BackEnd/config/init_db.php`** - Tạo database schema

### Frontend Updated ✅
1. **`pages/user/register.php`** - Gọi `/WebBasic/BackEnd/api/register.php` thay vì localStorage
2. **`pages/user/login.php`** - Gọi `/WebBasic/BackEnd/api/login.php` thay vì localStorage
3. Các `return false` sẽ được xóa dần khi implement các feature còn lại

## Bước 1: Khởi tạo Database

Truy cập vào:
```
http://localhost/WebBasic/BackEnd/config/init_db.php
```

Nếu thấy dòng "Database tables created/verified successfully!" là thành công.

## Bước 2: Test APIs

### Test Register:
```bash
curl -X POST http://localhost/WebBasic/BackEnd/api/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "email":"test@example.com",
    "password":"password123",
    "firstName":"Test",
    "lastName":"User",
    "phone":"0909123456",
    "birthDate":"2000-01-01",
    "province":"HCM",
    "address":"123 Test Street"
  }'
```

### Test Login:
```bash
curl -X POST http://localhost/WebBasic/BackEnd/api/login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email":"test@example.com",
    "password":"password123"
  }'
```

## Database Schema

### users table
- id (INT, PRIMARY KEY)
- email (VARCHAR, UNIQUE)
- password (VARCHAR, hashed with bcrypt)
- first_name, last_name (VARCHAR)
- phone, birth_date, province, address (VARCHAR/DATE)
- is_admin (TINYINT)
- created_at, updated_at (TIMESTAMP)

### orders table
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY to users)
- total_price (DECIMAL)
- shipping_address, shipping_phone (VARCHAR/TEXT)
- status (ENUM: pending, confirmed, shipped, delivered, cancelled)
- created_at, updated_at (TIMESTAMP)

### order_items table
- id (INT, PRIMARY KEY)
- order_id (INT, FOREIGN KEY to orders)
- product_name (VARCHAR)
- quantity (INT)
- unit_price (DECIMAL)
- created_at (TIMESTAMP)

### products table
- id (INT, PRIMARY KEY)
- name, category, price, description, image_url (VARCHAR/TEXT)
- stock (INT)
- origin, year, fuel, seats, transmission, engine (VARCHAR/INT)
- created_at, updated_at (TIMESTAMP)

### categories table
- id (INT, PRIMARY KEY)
- name (VARCHAR, UNIQUE)
- description (TEXT)
- is_visible (TINYINT)
- created_at, updated_at (TIMESTAMP)

## Next Steps - Fix Prototype Issues

### `onclick="return false;"` sẽ được fix cho các feature:
1. ✅ **Register** - Đã fix, gọi API
2. ✅ **Login** - Đã fix, gọi API  
3. ⏳ **Cart** - Cần implement real checkout
4. ⏳ **Profile** - Cần lưu thay đổi vào database
5. ⏳ **Admin - Thêm Category** - Cần implement
6. ⏳ **Admin - Thêm Sản phẩm** - Cần implement

## Notes for Developer

- Tất cả password đã được hash bằng bcrypt
- API endpoints đều kiểm tra input validation
- Database transactions được sử dụng cho order creation
- CORS headers được set để allow frontend requests
