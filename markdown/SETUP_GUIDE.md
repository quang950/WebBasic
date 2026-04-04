# 🚀 WebBasic - Setup Guide

## ✅ Hoàn thành (PHP Migration)

Tất cả file HTML đã convert sang PHP. Tất cả links đã cập nhật. Database có 80 sản phẩm.

---

## 📌 Bước 1: Import Database

### Option A: Sử dụng phpMyAdmin
1. Mở **http://localhost/phpmyadmin**
2. Tạo database mới: `car_shop`
3. Chọn `Import` → Chọn file **`DataBase/car_shop.sql`**
4. Nhấn `Go`

### Option B: Sử dụng MySQL CLI
```bash
mysql -u root -p < "DataBase/car_shop.sql"
```

---

## 📂 File Structure (After Migration)

```
WebBasic/
├── FrontEnd/
│   ├── index.php                  ← [Converted] Trang chủ
│   ├── assets/                    ← CSS + JS + Images
│   └── pages/
│       ├── brands/
│       │   ├── toyota.php         ← [Converted]
│       │   ├── mercedes.php       ← [Converted]
│       │   └── ... (7 more)
│       ├── user/
│       │   ├── login.php          ← [Converted]
│       │   ├── cart.php           ← [Converted]
│       │   ├── profile.php        ← [Converted]
│       │   └── ... (5 more)
│       └── admin/
│           ├── admin-login.php    ← [Converted]
│           ├── admin-themsanpham.php ← [Converted]
│           └── ... (2 more)
├── BackEnd/
│   ├── api/
│   │   ├── products.php           ← GET /products
│   │   ├── search.php             ← GET /search
│   │   ├── product_detail.php     ← GET /product_detail
│   │   └── cart.php               ← [TODO]
│   ├── config/
│   │   └── db_connect.php         ← MySQL connection
│   ├── controllers/
│   │   ├── ProductController.php
│   │   └── CartController.php     ← [TODO]
│   └── models/
│       ├── ProductModel.php
│       ├── CartModel.php          ← [TODO]
│       └── UserModel.php
└── DataBase/
    ├── car_shop.sql               ← [UPDATED] 80 products
    └── products.json              ← Backup data
```

---

## 🔌 Database Schema

### 4 Bảng chính:

**categories** (9 hãng xe)
```sql
- Toyota, Mercedes, BMW, Audi, Lexus
- Honda, Hyundai, KIA, VinFast
```

**products** (80 sản phẩm)
```sql
- id, name, category_id, price, description, image_url, stock, created_at
```

**users** (Đăng ký/Login)
```sql
- id, first_name, last_name, email, password, phone, birth_date, province, address, is_admin
```

**cart** (Giỏ hàng)
```sql
- id, user_id, product_id, quantity, added_at
```

---

## 🎯 Dữ liệu sản phẩm

**80 chiếc xe** từ **9 hãng**:
- **Toyota**: 9 models (Camry, Vios, Fortuner, Cross, Innova, Yaris, Corolla, Raize, Alphard)
- **Mercedes**: 9 models (C200, E200, GLC200, GLC300, GLE450, GLS600, S450, S480, S650)
- **BMW**: 9 models (320i, 330i, 430i, 520i, 530i, 730Li, X5, X7, Z4)
- **Audi**: 9 models (A4, A6, A8, Q3, Q5, Q7, RS, RS7, S5)
- **Lexus**: 9 models (ES250, RX350, NX350h, RX500h, GX550M, LS500, LX600, LC500, LM500h)
- **Honda**: 9 models (City, Civic, CR-V, BR-V, HR-V, NSX, Accord, Odyssey, Jazz)
- **Hyundai**: 9 models (Elantra, Accent, Creta, Tucson, Santafe, Grand, Palisade, Stargazer, Sonata)
- **KIA**: 9 models (Morning, Soluto, Sonet, Carnival, Sorento, Sportage, Carens, Cerato, K3)
- **VinFast**: 8 models (VF e34, VF3, VF5, VF6, VF7, VF8, VF9, Lux A)

---

## 🌐 API Endpoints (Backend)

### 1. Danh sách sản phẩm
```
GET /BackEnd/api/products.php
Tham số:
  - category: Lọc theo hãng (toyota, mercedes, ...)
  - name: Tìm theo tên
  - minPrice, maxPrice: Khoảng giá
  - page, limit: Phân trang

Response: JSON
{
  "success": true,
  "data": [...],
  "pagination": { "page": 1, "limit": 6, "totalItems": 80, "totalPages": 14 },
  "storage": "database"
}
```

### 2. Tìm kiếm
```
GET /BackEnd/api/search.php
(Tham số giống products.php)
```

### 3. Chi tiết sản phẩm
```
GET /BackEnd/api/product_detail.php?id=1
Response: Single product object
```

### 4. Giỏ hàng
```
POST /BackEnd/api/cart.php
[Status: TODO - cần hoàn thiện]
```

---

## 📝 Deployment Steps

1. **Copy FrontEnd → Webroot**
   ```
   Copy FrontEnd/ → C:\xampp\htdocs\webbasic\
   ```

2. **Import Database**
   ```
   mysql -u root < DataBase/car_shop.sql
   ```

3. **Verify Connection**
   - Open: `http://localhost/webbasic/index.php`
   - Kiểm tra console cho lỗi

4. **Test API**
   - http://localhost/webbasic/BackEnd/api/products.php
   - http://localhost/webbasic/BackEnd/api/products.php?category=toyota

---

## 🛠️ Cần làm tiếp (TODO)

1. **CartController.php** - Implement add/remove/update cart logic
2. **CartModel.php** - Implement cart DB operations
3. **cart.php** - Hook up API endpoint
4. **Orders** - Create orders table và endpoints
5. **Admin CRUD** - Implement product add/edit/delete
6. **Images** - Upload placeholder images để tránh 404

---

## 🔍 Kiểm tra

### Check Database
```sql
USE car_shop;
SELECT COUNT(*) FROM products;  -- Should be 80
SELECT * FROM categories;       -- Should show 9 brands
```

### Check File Paths
- All `.html` should be converted to `.php` ✅
- All links in PHP files should point to `.php` ✅
- All JS imports should reference `.php` ✅

---

## 📞 Support

Nếu gặp lỗi:
1. Kiểm tra Apache + MySQL đang chạy
2. Kiểm tra database connection string (`BackEnd/config/db_connect.php`)
3. Kiểm tra browser console cho errors
4. Kiểm tra PHP error log

---

**Status**: ✅ Migration complete | 📅 2026-03-20
