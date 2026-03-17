# 3 Boys Auto 🚗

Website bán xe ô tô trực tuyến 

## Cấu trúc dự án

```
WebBasic/
├── assets/                  # Tài nguyên tĩnh
│   ├── css/
│   │   ├── style.css        # CSS chính (user & brand pages)
│   │   └── admin-style-new.css  # CSS trang admin
│   ├── js/
│   │   ├── main.js          # Logic chính (login, cart, navbar)
│   │   ├── brand-page.js    # Logic trang thương hiệu xe
│   │   ├── search-results.js# Logic trang kết quả tìm kiếm
│   │   └── admin.js         # Logic trang quản trị
│   └── images/              # Hình ảnh xe, logo thương hiệu
│       └── images-index/    # Hình nền trang chủ
├── pages/
│   ├── admin/               # Trang quản trị
│   │   ├── admin-login.html
│   │   ├── admin-categories.html
│   │   ├── admin-add-category.html
│   │   └── admin-themsanpham.html
│   ├── brands/              # Trang từng hãng xe
│   │   ├── toyota.html
│   │   ├── mercedes.html
│   │   ├── bmw.html
│   │   ├── audi.html
│   │   ├── lexus.html
│   │   ├── honda.html
│   │   ├── hyundai.html
│   │   ├── kia.html
│   │   └── vinfast.html
│   └── user/                # Trang người dùng
│       ├── login.html
│       ├── register.html
│       ├── profile.html
│       ├── cart.html
│       ├── orders.html
│       ├── invoice.html
│       ├── order-confirmation.html
│       └── search-results.html
├── docs/                    # Tài liệu hướng dẫn
├── index.html               # Trang chủ ⭐
├── .gitignore
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
