# Welcome to 3 Boys Auto 🚗

Website bán xe ô tô trực tuyến

## Cấu trúc dự án

```
WebBasic/
├── BackEnd/                 # Mã nguồn phía Server (PHP)
│   ├── api/                 # Tầng ENTRY POINT (FE gọi vào đây)
│   │   ├── cart.php
│   │   ├── check_session.php
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── product_detail.php
│   │   ├── products.php
│   │   ├── register.php
│   │   └── search.php
│   ├── config/              # Cấu hình hệ thống (Database, v.v.)
│   │   └── db_connect.php
│   ├── controllers/         # Tầng xử lý logic
│   │   ├── CartController.php
│   │   ├── ProductController.php
│   │   └── UserController.php
│   └── models/              # Tầng tương tác database
│       ├── CartModel.php
│       ├── ProductModel.php
│       └── UserModel.php
├── DataBase/                # Cơ sở dữ liệu
│   └── car_shop.sql         # File export database
├── FrontEnd/                # Giao diện người dùng
│   ├── assets/              # Tài nguyên tĩnh
│   │   ├── css/
│   │   │   ├── admin-style-new.css  # CSS trang admin
│   │   │   └── style.css            # CSS chính
│   │   ├── images/          # Hình ảnh xe, logo thương hiệu
│   │   │   └── images-index/
│   │   └── js/
│   │       ├── admin.js             # Logic trang quản trị
│   │       ├── brand-page.js        # Logic trang thương hiệu
│   │       ├── main.js              # Logic chính
│   │       └── search-results.js    # Logic trang tìm kiếm
│   ├── detail/              # Trang chi tiết sản phẩm
│   ├── docs/                # Tài liệu hướng dẫn
│   ├── pages/
│   │   ├── admin/           # Trang quản trị
│   │   │   ├── admin-add-category.html
│   │   │   ├── admin-categories.html
│   │   │   ├── admin-login.html
│   │   │   └── admin-themsanpham.html
│   │   ├── brands/          # Trang theo hãng xe
│   │   │   ├── audi.html
│   │   │   ├── bmw.html
│   │   │   ├── honda.html
│   │   │   ├── hyundai.html
│   │   │   ├── kia.html
│   │   │   ├── lexus.html
│   │   │   ├── mercedes.html
│   │   │   ├── toyota.html
│   │   │   └── vinfast.html
│   │   └── user/            # Trang tài khoản/mua sắm
│   │       ├── cart.html
│   │       ├── invoice.html
│   │       ├── login.html
│   │       ├── order-confirmation.html
│   │       ├── orders.html
│   │       ├── profile.html
│   │       ├── register.html
│   │       └── search-results.html
│   ├── index.html           # Trang chủ ⭐
│   └── .gitignore
└── README.md
```

## Tính năng

- **Trang chủ**: Hiển thị danh sách xe theo hãng, tìm kiếm theo tên xe
- **Trang hãng xe**: Chi tiết các dòng xe của từng thương hiệu (Toyota, Mercedes, BMW, Audi, Lexus, Honda, Hyundai, KIA, VinFast)
- **Đăng nhập / Đăng ký**: Xác thực người dùng qua localStorage
- **Giỏ hàng**: Thêm, xóa, thay đổi số lượng xe
- **Đơn hàng**: Xem lịch sử đơn hàng & hoá đơn
- **Trang Admin**: Quản lý sản phẩm, danh mục, đơn hàng

## Công nghệ sử dụng

- HTML5
- CSS3 (Flexbox, Grid, Animation)
- JavaScript (ES6+, localStorage)
- Font Awesome 6
- Google Fonts

## Cách chạy

Mở file `index.html` bằng trình duyệt (hoặc dùng **Live Server** trong VS Code).
