# PROJECT CONTEXT & RULES - WEB NÂNG CAO

## 1. Cấu trúc thư mục (Strictly Follow)
- **Models:** Nằm trong `BackEnd/models/`. Tất cả logic truy vấn DB, tính toán giá vốn bình quân (Average Cost) phải viết ở đây.
- **Controllers:** Nằm trong `BackEnd/controllers/`. Điều hướng và xử lý logic nghiệp vụ.
- **API:** Nằm trong `BackEnd/api/`. Chuyên xử lý các request AJAX/Fetch từ phía Client.
- **Views/Pages:** Nằm trong `FrontEnd/pages/`.
- **Assets:** JS validation và logic frontend phải nằm trong `FrontEnd/assets/js/`. Trình bày giao diện sử dụng CSS trong `FrontEnd/assets/css/`.
- **Database:** Schema mới và dữ liệu export phải được cập nhật vào thư mục gốc `DataBase/`.

## 2. Logic Giá Vốn & Giá Bán (BẮT BUỘC)
- **Công thức Giá vốn mới (Bình quân gia quyền - Moving Average):** `(Tồn * Giá vốn cũ + Nhập * Giá mới) / (Tồn + Nhập)`.
- **Công thức Giá bán:** `Giá bán = Giá vốn * (100% + Tỷ lệ lợi nhuận)`.
- **Trigger cập nhật:** Thực hiện tính toán và lưu đè giá vốn/giá bán ngay tại thời điểm thao tác Xác nhận nhập hàng (Import Ticket) thành công.

## 3. Quy tắc Code, Giao tiếp & Bảo mật
- **Database Security:** Sử dụng PDO kết hợp Prepared Statements. Tuyệt đối không sử dụng nối chuỗi SQL (SQL Concatenation) để phòng chống SQL Injection.
- **Database Design:** Đảm bảo chặt chẽ quan hệ 1-N (VD: Đơn hàng -> Chi tiết đơn hàng, Phiếu nhập -> Chi tiết nhập). Sử dụng `ON DELETE CASCADE` / `SET NULL` hợp lý.
- **Validation (Xác thực 2 lớp bắt buộc):**
    - *Client-side:* Phải có JS validation hiển thị cảnh báo ngay trên form trước khi submit. (Nằm trong `assets/js/`).
    - *Server-side:* Tuyệt đối không tin tưởng client, Controller/API phải kiểm tra lại tính hợp lệ, kiểu dữ liệu trước khi Insert/Update.
- **Chuẩn API Response:** Mọi endpoint nằm trong `BackEnd/api/` bắt buộc trả về định dạng `JSON`. Cấu trúc chuẩn cần có: `{"status": "success|error", "message": "...", "data": mixed}`.
- **Error Handling (Xử lý lỗi):** Bắt buộc dùng `try-catch` khi thao tác với Database. Quản lý lỗi an toàn: Log lỗi chi tiết ở server nhưng chỉ trả về câu thông báo chung chung cho client (Không bao giờ in nguyên Exception/SQL Error ra response).
- **Giao diện:** Tuân thủ hệ thống CSS có sẵn trong `assets/css/`, giữ nguyên phong cách (style) đồng bộ với giao diện cũ.

## 4. Yêu cầu nghiệp vụ cụ thể
- **Lọc đơn hàng:** Hỗ trợ bộ lọc động nhiều tiêu chí: khoảng thời gian (Từ ngày - Đến ngày), trạng thái đơn hàng và địa lý khu vực (như Tỉnh/Thành phố hoặc Phường/Xã).
- **Tồn kho:** Cho phép truy vấn số lượng tồn kho tại một thời điểm quá khứ bất kỳ bằng cách đối chiếu và nội suy từ bảng `stock_history` (Lịch sử nhập xuất).
- **Cảnh báo hết hàng:** Cho phép người dùng thiết lập cấu hình ngưỡng tối thiểu (threshold). Hệ thống cảnh báo tự động khi `stock` chạm hoặc rớt xuống dưới ngưỡng này.