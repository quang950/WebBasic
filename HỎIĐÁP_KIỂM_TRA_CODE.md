# 📋 KIỂM TRA CODE - GV MODE
## WebBasic Project - Chuẩn bị trả lời câu hỏi GV

---

## PHẦN 1: KIẾN TRÚC & THIẾT KẾ

### **Q1: Admin Login & User Login URL**
**Câu hỏi:**
Em giải thích qua tại sao lại cần có 2 URL riêng cho admin login và user login, trong khi backend dùng cùng 1 endpoint login?

**Câu trả lời mẫu:**
```
Thực ra đó là thiết kế phân tách (separation of concern):

1. **Frontend Level (Different URLs):**
   - Admin: /FrontEnd/pages/admin/admin-login.php
   - User: /FrontEnd/pages/user/login.php
   
   Lý do:
   - UX khác nhau: Admin cần form đơn giản, User cần form + phần đăng ký
   - Styling khác: Admin dashboard vs User homepage
   - Clarity: Người dùng biết là admin hay user page
   - Security: Khó bị phishing vì URL rõ ràng

2. **Backend Level (Same endpoint):**
   - POST /BackEnd/api/login.php được dùng bởi cả 2
   - Backend kiểm tra email/password
   - Trả về is_admin flag trong response
   - Frontend kiểm tra flag để xác định user type

3. **Why not separate backend endpoint?**
   - DRY principle: Không duplicate auth logic
   - Easier maintenance: 1 nơi để fix bugs
   - Consistent: Logic validation cukup 1 lần
   
**Security aspect:**
   - Backend PHẢI kiểm tra $_SESSION['is_admin'] trên mọi admin API
   - Không tin tưởng frontend: User có thể cheat bằng DevTools
   - Mọi admin action phải có middleware check permission
```

---

### **Q2: Database Normalization - import_tickets vs import_items**
**Câu hỏi:**
Database có import_tickets và import_items riêng biệt. Em hãy giải thích mối quan hệ one-to-many này? Tại sao cần tách thành 2 bảng?

**Câu trả lời mẫu:**
```
Đó là chuẩn hóa database (Database Normalization) để tránh dữ liệu dư thừa:

1. **Cấu trúc hiện tại (ĐÚNG):**
   
   import_tickets (1)
   ├── id (PK)
   ├── ticket_number
   ├── import_date
   ├── total_amount
   └── created_by
   
   import_items (Many) - Foreign Key: import_ticket_id
   ├── id (PK)
   ├── import_ticket_id (FK) → import_tickets.id
   ├── product_id (FK) → products.id
   ├── quantity
   └── import_price

2. **Nếu không tách (SAI):**
   
   import_tickets
   ├── id
   ├── product_id_1, qty_1, price_1
   ├── product_id_2, qty_2, price_2
   ├── product_id_3, qty_3, price_3
   ├── product_id_4, qty_4, price_4
   
   Vấn đề:
   - Công khai cột hết: product_id_1 ... product_id_100
   - Dữ liệu dư thừa: ticket_number bị lặp
   - Query phức tạp: phải check tất cả cột
   - Không flexible: Cố định được max 100 sản phẩm

3. **Lợi ích của cách tách:**
   - 1 phiếu nhập có thể có N sản phẩm (unlimited)
   - Dễ query: JOIN và GROUP BY
   - Dễ update: Thêm/xóa item chỉ cần INSERT/DELETE
   - Data integrity: FK constraint tự động kiểm tra
   - Reusability: import_items.product_id link tới products
   
4. **Example Query:**
   ```sql
   -- Lấy tất cả items của 1 phiếu
   SELECT 
     i.product_id, 
     p.name, 
     i.quantity, 
     i.import_price
   FROM import_items i
   JOIN products p ON i.product_id = p.id
   WHERE i.import_ticket_id = 5;
   ```
   
   **Đó là lý do cần tách!**
```

---

## PHẦN 2: BÌNH QUÂN COST PRICE (CRITICAL)

### **Q3: BÌNH QUÂN Formula & Edge Cases**
**Câu hỏi:**
Em vừa implement BÌNH QUÂN cost price ở `complete_import.php` line 88-94. Em hãy:
- Giải thích công thức
- Tại sao không dùng cách sai?
- Cho ví dụ tính toán?

**Câu trả lời mẫu:**
```
1. **BÌNH QUÂN WEIGHTAGE FORMULA:**

   new_cost_price = (existing_stock × existing_cost + new_qty × import_price) / 
                    (existing_stock + new_qty)

   Code em implement:
   ```php
   $new_cost_price = $total_stock > 0 
       ? ($previous_stock * $current_cost_price + $new_quantity * $import_price) / $total_stock
       : $import_price;
   ```

2. **TẠI SAO KHÔNG DÙNG cost_price = import_price (SAI)?**

   Cách sai (chỉ lấy giá nhập mới):
   - Cost price bị thay đổi hoàn toàn
   - Bỏ qua sản phẩm cũ đã tồn
   - Lợi nhuận tính sai hoàn toàn
   
   Example sai:
   - Stock 100 @ cost 20
   - Import 10 @ 15
   - WRONG: cost = 15 (bỏ mất 100 cái @ 20)
   - Profit margin sai: Nếu sell @ 22, tưởng lãi 7/15=46%
   - Thực tế: (22-15)*10 = 70, nhưng 100 cái @ 20 thì lãi (22-20)*100 = 200
   - → Salesforce sẽ định giá sai lệch 60%!

3. **BÌNH QUÂN VÍ DỤ TÍNH:**

   Lần 1: Nhập 10 sản phẩm @ 20
   - existing_stock = 0
   - new_qty = 10
   - import_price = 20
   - new_cost = (0 × 0 + 10 × 20) / (0 + 10) = 200/10 = 20

   Bán 4 cái, tồn 6 cái @ cost 20

   Lần 2: Nhập 10 sản phẩm @ 15
   - existing_stock = 6
   - existing_cost = 20
   - new_qty = 10
   - import_price = 15
   - new_cost = (6 × 20 + 10 × 15) / (6 + 10)
             = (120 + 150) / 16
             = 270 / 16
             = 16.875
             
   **Giải thích:**
   - Total tồn kho: 6 + 10 = 16 cái
   - Total chi phí: 6 × 20 + 10 × 15 = 270
   - Trung bình: 270 / 16 = 16.875 mỗi cái
   - **Một cái hiện tại có giá vốn 16.875, không phải 20 cũng không phải 15**

4. **Edge case: Stock = 0**
   ```php
   $new_cost_price = $total_stock > 0 
       ? ($previous_stock * $current_cost_price + $new_qty * $import_price) / $total_stock
       : $import_price;  // ← Nếu 0, dùng import_price luôn là đúng
   ```
   - Vì lần đầu tiên, chỉ có import_price là reference
```

---

### **Q4: Edge Case - Zero Quantity**
**Câu hỏi:**
Nếu stock = 0 và user nhập 0 cái (ghi quantity=0), code em sẽ xử lý thế nào?

**Câu trả lời mẫu:**
```
Tốt là bạn phát hiện case này! Code em hiện tại CÓ LỖ:

1. **Current Code:**
   ```php
   $total_stock = $previous_stock + $new_quantity;  // = 0 + 0 = 0
   $new_cost_price = $total_stock > 0 
       ? (...) / $total_stock
       : $import_price;  // ← Dùng import_price
   ```
   
   Nhưng: Nếu import_price = 0 (user nhập sai), thì cost_price = 0
   → Sản phẩm có thể bán gratis!

2. **Cách fix (nên add validation):**
   ```php
   // Ở đầu hàm
   if ($new_quantity <= 0) {
       throw new Exception("Số lượng nhập phải > 0");
   }
   
   if ($import_price <= 0) {
       throw new Exception("Giá nhập phải > 0");
   }
   ```

3. **Hoặc ở database level:**
   - ALTER TABLE import_items ADD CONSTRAINT check_quantity CHECK (quantity > 0)
   - ALTER TABLE import_items ADD CONSTRAINT check_price CHECK (import_price > 0)
   
   Khi đó SQL sẽ reject trước khi đến PHP
```

---

## PHẦN 3: TRANSACTION & DATA INTEGRITY

### **Q5: Tại sao cần Transaction?**
**Câu hỏi:**
Tại sao `complete_import.php` cần dùng `begin_transaction()` và `rollback()`? Nếu bỏ đi, điều gì có thể xảy ra?

**Câu trả lời mẫu:**
```
Transaction là cơ chế đảm bảo Data Consistency. Em giải thích bằng ví dụ:

1. **CÓ TRANSACTION (ĐÚNG):**
   
   ```php
   $conn->begin_transaction();
   
   try {
       // Step 1: Update product stock
       UPDATE products SET stock = stock + 10 WHERE id = 5;
       
       // Step 2: Update product cost_price
       UPDATE products SET cost_price = 16.875 WHERE id = 5;
       
       // Step 3: Insert stock history log
       INSERT INTO stock_history (product_id, type, quantity, ...)...;
       
       // Step 4: Mark import as completed
       UPDATE import_tickets SET completed_at = NOW() WHERE id = 1;
       
       $conn->commit();  // ← Tất cả 4 steps thành công = COMMIT
   } catch {
       $conn->rollback();  // ← Nếu step nào fail, ROLLBACK tất cả
   }
   ```

2. **KHÔNG CÓ TRANSACTION (SAI):**
   
   Kịch bản:
   - Step 1 SUCCESS: products.stock = 110 ✓
   - Step 2 SUCCESS: products.cost_price = 16.875 ✓
   - Step 3 SUCCESS: stock_history inserted ✓
   - Step 4 FAIL: completed_at update lỗi (DB connection lost) ✗
   
   **Kết quả:**
   - Stock tăng rồi
   - Cost price update rồi
   - History log có
   - NHƯNG import_tickets.completed_at vẫn NULL
   
   **Tệ hại:**
   - Admin không biết phiếu đã complete
   - Kế toán bối rối: Stock tăng nhưng completed_at trống?
   - Stock history không match import_tickets
   - Báo cáo sai

3. **VỚI TRANSACTION:**
   
   Nếu step 4 fail:
   - ROLLBACK: Step 1, 2, 3 đều bị undo
   - products: stock không đổi, cost_price không đổi
   - stock_history: không có record
   - import_tickets: vẫn draft, chưa complete
   
   **Kết quả:**
   - Database CLEAN/CONSISTENT
   - Admin retry, lần sau thành công
   - Không có dữ liệu corrupt

4. **ACID Properties:**
   - **A**tomicity: Tất cả or không (không có công việc nửa vời)
   - **C**onsistency: Từ state đúng → state đúng
   - **I**solation: Không bị xen vào từ client khác
   - **D**urability: Khi commit, dữ liệu an toàn
   
   **Transaction đảm bảo ACID!**
```

---

### **Q6: Multi-step Transaction Failure**
**Câu hỏi:**
Em có update stock, cost_price, insert history, update ticket tất cả trong 1 transaction. Nếu step 3 (stock_history insert) fail, database sẽ như thế nào? Tại sao?

**Câu trả lời mẫu:**
```
Nếu step 3 fail (INSERT stock_history error):

1. **Chuỗi sự kiện:**
   ```
   Step 1: UPDATE products - SUCCESS ✓
   Step 2: UPDATE products cost_price - SUCCESS ✓
   Step 3: INSERT stock_history - FAIL ✗ (ví dụ: FK constraint failed)
   Step 4: UPDATE import_tickets - KHÔNG chạy (transaction đã fail)
   
   → Điểm quan trọng: Step 4 KHÔNG chạy vì exception đã throw
   ```

2. **Code em implement:**
   ```php
   try {
       $conn->begin_transaction();
       
       // Step 1, 2 chạy được
       ...
       
       // Step 3 fail
       $hstmt->execute();  // ← Nếu lỗi, throw Exception
       
       // Step 4 không bao giờ chạy
       ...
       
       $conn->commit();  // ← Không tới đây
   } catch (Exception $e) {
       $conn->rollback();  // ← Rollback TẤT CẢ those steps 1, 2, 3
   }
   ```

3. **Database state sau error:**
   - products table: **UNCHANGED** (dù step 1, 2 chạy pero transaction rollback)
   - stock_history: **NO NEW RECORD** (step 3 fail + rollback)
   - import_tickets: **STILL DRAFT** (not completed)
   
   **TẤT CẢ như chưa có gì xảy ra!**

4. **Tại sao như vậy:**
   - MySQL (InnoDB engine) theo dõi **tất cả thay đổi** trong transaction
   - Nếu bất kỳ lệnh fail → throw exception
   - Exception được catch → gọi rollback()
   - rollback() **UNDO TẤT CẢ** lệnh từ begin_transaction()
   
   **Đó gọi là "All or Nothing"**

5. **Nếu không có try-catch-rollback:**
   - Step 1, 2: Change database (nhưng chưa commit)
   - Step 3: Fail (exception throw)
   - **NHƯNG nếu không có rollback():**
     - Transaction vẫn active
     - Step 1, 2 vẫn pending
     - Phải resolve error, hoặc kết nối bị close
     - Khi kết nối close: tùy theo driver behavior
       - Một số: auto-commit (dữ liệu sót)
       - Một số: auto-rollback (ok)
   
   **→ Không an toàn! Phải có explicit rollback()**
```

---

## PHẦN 4: BUSINESS LOGIC

### **Q7: Concurrency - Product Deleted While in Cart**
**Câu hỏi:**
Khi khách hàng đặt hàng, em lấy sản phẩm từ `cart` table. Nếu giữa lúc khách xem, admin xóa sản phẩm đó khỏi database thì sao? Backend em có check gì?

**Câu trả lời mẫu:**
```
Tốt là câu hỏi! Đó là race condition issue:

1. **Kịch bản:**
   ```
   T1: User add Product#5 to cart
   T2: User goes to checkout
   T3: ADMIN xóa Product#5 (DELETE from products)
   T4: User click "Place Order"
   ```

2. **Em check ở đâu? (Xem place_order.php):**
   ```php
   // Lấy items từ cart
   $stmt = $conn->prepare("
       SELECT c.product_id, c.quantity, p.name, p.price 
       FROM cart c
       JOIN products p ON c.product_id = p.id  // ← IMPORTANT!
       WHERE c.user_id = ?
   ");
   ```
   
   **Nếu product bị xóa:**
   - INNER JOIN với products sẽ không trả về dòng đó
   - cart record vẫn tồn (orphan)
   - Result sẽ thiếu item đó
   
   → **Problem: Validate không đủ!**

3. **Cách em FIX (nên thêm):**
   ```php
   // Sau khi lấy items
   if (empty($items)) {
       throw new Exception("Giỏ hàng trống hoặc sản phẩm không tồn tại");
   }
   
   // Kiểm tra từng item
   foreach ($items as $item) {
       $stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND is_visible = 1");
       $stmt->bind_param("i", $item['product_id']);
       $stmt->execute();
       if ($stmt->get_result()->num_rows === 0) {
           throw new Exception("Sản phẩm #{$item['product_id']} không còn có sẵn");
       }
   }
   
   // Sau khi validate OK mới tạo order
   ```

4. **Better approach (Pessimistic Locking):**
   ```php
   // Lock product rows ngay từ bắt đầu checkout
   BEGIN TRANSACTION;
   SELECT * FROM products WHERE id IN (...) FOR UPDATE;  // ← Lock!
   
   // Check stock, price, status
   // Nếu ok: CREATE ORDER
   // Nếu not ok: ROLLBACK
   COMMIT;
   ```
   
   // Trong transaction:
   - Admin không thể xóa (locked)
   - Nếu admin chờ lock release thì okay
   - Nếu user order thành công: lock release, order saved

5. **Em implement lúc nào:**
   - Hiện tại: **Chưa có** (basic validation tiên)
   - Cách này: Tùy requirements (bao nhiêu concurrent users)
   - Nếu traffic cao: Nên dùng pessimistic locking
   - Nếu traffic thấp: Current simple validation đủ
```

---

### **Q8: Online Payment Status**
**Câu hỏi:**
Em có 3 cách thanh toán: cash, transfer, online. Nhưng online thì sao em chưa implement? Đó là feature hay format rồi?

**Câu trả lời mẫu:**
```
Đó là **INCOMPLETE FEATURE** theo requirement:

1. **Requirement nói:**
   "Cho phép chọn thanh toán tiền mặt, chuyển khoản (hiển thị thông tin 
   chuyển khoản) hoặc thanh toán trực tuyến (nhưng khi chọn thanh toán 
   trực tuyến thì chưa cần xử lý tiếp)."
   
   → **"chưa cần xử lý tiếp"** = không cần implement payment gateway (Stripe, PayPal...)

2. **Em implement:**
   ```php
   // Backend accept payment_method = 'cash', 'transfer', 'online'
   $payment_method = $_POST['payment_method'];
   if (!in_array($payment_method, ['cash', 'transfer', 'online'])) {
       throw new Exception("Invalid payment method");
   }
   
   // Lưu vào database
   INSERT INTO orders (payment_method) VALUES ($payment_method);
   ```
   
   **Chỉ validate + lưu, không process.**

3. **Em KHÔNG implement:**
   - Gọi API Stripe/PayPal
   - Generate payment token
   - Handle callback từ payment gateway
   - Confirm payment status
   
   **→ Vì requirement nói "chưa cần"**

4. **Frontend:**
   - User chọn "Online payment" → hiệu ứng (modal/popup)
   - Message: "Thanh toán online sẽ được implement sớm"
   - Cho phép select nhưng disable checkout nút
   
   OR
   
   - Full form nhưng backend reject: "Online payment not available yet"

5. **Nếu sau này implement thật:**
   ```php
   if ($payment_method === 'online') {
       // 1. Generate payment intent với Stripe
       $session = \Stripe\Checkout\Session::create([...]);
       
       // 2. Trả cho frontend URL thanh toán
       return ['redirect_url' => $session->url];
       
       // 3. Frontend redirect user tới Stripe
       
       // 4. Stripe redirect về webhook `/callback/payment.php`
       // 5. Em update order status = 'paid'
   }
   ```
   
   **Nhưng hiện tại: Scope out, nên just 'ok, feature exists, not implemented'**
```

---

## PHẦN 5: SECURITY

### **Q9: Password Hashing - bcrypt vs MD5**
**Câu hỏi:**
Password em hash bằng bcrypt (`password_hash()` & `password_verify()`). Tại sao không dùng MD5 hay SHA1?

**Câu trả lời mẫu:**
```
Đó là security best practice. Em giải thích:

1. **MD5 & SHA1: TẠI SAO KHÔNG DÙNG:**

   MD5 (Hashing):
   ```
   password = "123456"
   md5("123456") = "e10adc3949ba59abbe56e057f20f883e"
   ```
   
   **Vấn đề:**
   - RAINBOW TABLE: Các hacker có bảng quy đổi sẵn
     - "e10adc3949ba59abbe56e057f20f883e" → "123456" (1 giây)
   - FAST: Tính toán quá nhanh (1 triệu lần/giây)
   - ĐẬU CÓ SALT: Không có random component
   
   SHA1: Tương tự, chỉ khác là output dài hơn

2. **BCRYPT: TẠI SAO DÙNG:**

   ```php
   password = "123456"
   hash = password_hash("123456", PASSWORD_BCRYPT);
   // Output: $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36yGst5C
   ```
   
   **Công nghệ:**
   - **Slow by design:** Tính toán mất 0.3 giây (có thể tùy chỉnh)
   - **Salt included:** Random 128-bit salt + hash (không rainbow table)
   - **Adaptive:** Tăng cost factor khi CPU mạnh hơn
   
   Decoding bcrypt output:
   ```
   $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36yGst5C
   ├─ $2y$ → bcrypt algorithm
   ├─ 10 → cost factor (2^10 iterations)
   ├─ N9qo8uLOickgx2ZMRZoMy → 16 bytes salt (base64)
   └─ eIjZAgcg7b3XeKeUxWdeS86E36yGst5C → hashed password
   ```

3. **CRACKING TIME COMPARISON:**
   
   MD5: "e10adc3949ba59abbe56e057f20f883e"
   - Time to crack: Instant (exists in rainbow table)
   - 💣 BROKEN
   
   SHA1: (sha1)
   - Time to crack: Seconds (rainbow table)
   - ⚠️ DEPRECATED
   
   bcrypt: $2y$10$...
   - Time to crack: 0.3 seconds per try (need 100 tries = 30 seconds)
   - 🔒 SAFE (1 triệu lần attempt = 83 ngày)

4. **Code em implement:**
   ```php
   // Register/Update password
   $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
   // cost = 10 (mặc định), có thể tăng lên 12, 13 nếu cần slower
   
   // Login - Verify
   if (password_verify($password, $stored_hash)) {
       // Correct!
   }
   ```
   
   **Perfect! Theo PHP best practice**

5. **So sánh nhanh:**
   | Hash | Speed | Salt | Rainbow |
   |------|-------|------|---------|
   | MD5 | Fast ✓ | ✗ | ✓ exist |
   | SHA1 | Medium ✓ | ✗ | ✓ exist |
   | bcrypt | Slow ✓ | ✓ | ✗ safe |
   | Argon2 | Slow ✓ | ✓ | ✗ safer |
   
   **→ bcrypt là chuẩn vàng cho 2024**
```

---

### **Q10: Authorization Check - Admin Permission**
**Câu hỏi:**
Nếu admin muốn xem order của user nào đó qua API, em có check `is_admin` flag trong session không? Nếu không check, nguy hiểm gì? Em có file nào enforce admin check không?

**Câu trả lời mẫu:**
```
Câu hỏi security này rất quan trọng! Em check lại code:

1. **KIỂM TRA AUTHORIZATION LỀN NÀO:**

   Ví dụ: GET /BackEnd/api/admin/get_all_orders.php
   
   ```php
   // Em CÓ CHECK không? Xem file:
   session_start();
   
   // ← Check 1: Session có tồn tại không?
   if (!isset($_SESSION['user_id'])) {
       http_response_code(401);
       exit;
   }
   
   // ← Check 2: User có admin role không?
   // LAM GÌ ĐÃY? CÓ FILE NÀO KHÔNG?
   if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
       http_response_code(403);
       exit;
   }
   ```

2. **NGUY HIỂM NẾU KHÔNG CHECK:**
   
   Kịch bản tấn công:
   ```
   User "vinh" login bằng /FrontEnd/pages/user/login.php
   → $_SESSION['is_admin'] = false
   → $_SESSION['user_id'] = 123
   
   "vinh" mở DevTools, gửi:
   GET /BackEnd/api/admin/get_all_orders.php
   
   Nếu em KHÔNG check $_SESSION['is_admin']:
   → Trả về TẤT CẢ orders của mọi user
   → "vinh" xem được order của người khác!
   
   Payload attack:
   - Nếu em dùng order_id từ user request:
     GET /admin/get_order.php?order_id=999
     → Nếu không check: Trả về order#999 của ai đó
   - User có thể thay đổi order_id = 1, 2, 3, ... lần lượt xem hết
   
   NGUY HIỂM!
   ```

3. **EM CÓ CHECK KHÔNG? KIỂM TRA SAU:**
   - [ ] `/BackEnd/api/admin/get_all_orders.php` - CHECK?
   - [ ] `/BackEnd/api/admin/update_order_status.php` - CHECK?
   - [ ] `/BackEnd/api/admin/get_products.php` - CHECK?
   - [ ] Tất cả file `admin/` folder - CÓ MIDDLEWARE KHÔNG?

4. **CÁCH IMPLEMENT ĐÚNG:**

   **Option A: Check ở mỗi file:**
   ```php
   // Đầu mỗi file admin API
   session_start();
   
   if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
       http_response_code(403);
       echo json_encode(['success' => false, 'message' => 'Unauthorized']);
       exit;
   }
   ```
   
   **Option B: Middleware (BETTER):**
   ```php
   // /BackEnd/middleware/AdminAuth.php
   <?php
   function requireAdmin() {
       session_start();
       if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
           http_response_code(403);
           echo json_encode(['success' => false, 'message' => 'Unauthorized']);
           exit;
       }
   }
   ?>
   
   // Mỗi file admin API
   require_once __DIR__ . '/../middleware/AdminAuth.php';
   requireAdmin();
   ```

5. **ADDITIONAL CHECKS (RECOMMENDED):**
   ```php
   // User không phải admin
   if ($_SESSION['is_admin'] !== 1) {
       log_unauthorized_access($_SESSION['user_id'], $_SERVER['REQUEST_URI']);
       http_response_code(403);
       exit;
   }
   
   // Kiểm tra account không bị lock
   $stmt = $conn->prepare("SELECT locked FROM users WHERE id = ?");
   $stmt->bind_param("i", $_SESSION['user_id']);
   $stmt->execute();
   $user = $stmt->get_result()->fetch_assoc();
   if ($user['locked']) {
       session_destroy();
       http_response_code(403);
       exit;
   }
   ```

6. **SUMMARY:**
   - ✓ PHẢI có check `is_admin` trong MỖI admin API
   - ✓ PHẢI có trong TẤT CẢ `/admin/` endpoints
   - ✓ KHÔNG trust frontend (có thể DevTools cheat)
   - ✓ PHẢI check ở backend
   - ✓ NÊN dùng middleware để centralize
   - ✓ NÊN log unauthorized attempts
```

---

## PHẦN 6: LOGOUT ENDPOINT (NEW)

### **Q11: Session Destruction Order**
**Câu hỏi:**
Em vừa tạo logout.php. Trong code, em destroy session, clear cookie, rồi gọi `session_destroy()`. Thứ tự này có quan trọng không? Nếu đảo lại thứ tự thì sao?

**Câu trả lời mẫu:**
```
Tốt là câu hỏi chi tiết! Thứ tự CÓ QUAN TRỌNG:

1. **CURRENT ORDER (EM IMPLEMENT):**
   ```php
   $_SESSION = [];              // 1. Xóa session data
   setcookie(..., time()-42000) // 2. Clear cookie
   session_destroy();           // 3. Destroy session handler
   ```

2. **TƯƠNG ĐỚI CÓ THỂ THAY ĐỔI:**
   
   **Correct order (Secured):**
   ```
   1. $_SESSION = []           // Ram memory: clear
   2. setcookie(...)           // Client: delete cookie
   3. session_destroy()        // Server: destroy session file
   ```
   
   **Wrong order (Less efficient):**
   ```
   1. session_destroy()        // Destroy file first
   2. $_SESSION = []           // Clear memory (but file đã gone)
   3. setcookie(...)           // Xóa cookie sau
   ```

3. **ĐẢO NGƯỢC SAO?**
   
   **Bad order (DANGER):**
   ```php
   setcookie(...);             // Clear cookie
   session_destroy();          // Destroy server
   $_SESSION = [];             // ← TOO LATE! $_SESSION ALREADY DESTROYED
   ```
   
   **Problem:**
   - session_destroy() đã gọi
   - Sau đó access $_SESSION = [] không có effect
   - session_start() mới sẽ khởi tạo session mới
   - Có thể create new empty session

4. **DETAIL ANALYSIS:**

   ```php
   // STEP 1: $_SESSION = []
   $_SESSION = [];
   // Effect: Clear all data in PHP's $_SESSION array
   // Location: Server memory
   // Risk: Nếu bỏ step này → $_SESSION vẫn chứa user data → danger!
   
   // STEP 2: setcookie(..., time()-42000)
   setcookie(session_name(), '', time()-42000, ...);
   // Effect: Tell browser "delete cookie"
   // Location: HTTP Response header (Set-Cookie)
   // Time-42000: Thời gian quá khứ (browser nhận = "cookie expired")
   // Risk: Nếu bỏ step này → browser vẫn gửi cookie lên server
   
   // STEP 3: session_destroy()
   session_destroy();
   // Effect: Delete server-side session file (e.g., /tmp/sess_abc123)
   // Location: File system
   // Risk: Nếu bỏ step này → session file vẫn tồn (nếu user re-send cookie)
   ```

5. **SCENARIO: WRONG ORDER**
   ```
   setcookie('/logout', ..., time()-42000);  // Send Set-Cookie header
   session_destroy();                        // Delete /tmp/sess_abc123
   $_SESSION = [];                           // ✗ TOO LATE!
   
   Lệnh `$_SESSION = []` không có effect vì:
   - session_destroy() đã gọi → session handler đã close
   - Ghi vào $_SESSION después destroy = vô nghĩa
   
   ← PLUS: Trong logout.php, chắc session vẫn active khi gọi $_SESSION = []
        Nên thứ tự ít ảnh hưởng, NHƯNG best practice:
        - Xóa data trước (an toàn)
        - Clear cookie (tell client)
        - Destroy file (clean server)
   ```

6. **BEST PRACTICE:**
   ```php
   session_start();  // Session đang active
   
   // 1. Unset all session variables
   $_SESSION = [];
   
   // 2. Delete the session cookie
   if (ini_get("session.use_cookies")) {
       $params = session_get_cookie_params();
       setcookie(
           session_name(),
           '',
           time() - 42000,
           $params["path"],
           $params["domain"],
           $params["secure"],
           $params["httponly"]
       );
   }
   
   // 3. Destroy the session
   session_destroy();
   
   echo json_encode(['success' => true, 'message' => 'Logged out']);
   ```
```

---

## PHẦN 7: IMPORT EDITING

### **Q12: Security - Prevent Hack via API**
**Câu hỏi:**
File `update_import.php` mới em tạo - nó check `completed_at` để quyết định có cho edit không. Nếu lỡ ai đó hack API và xóa `completed_at`, sao? Em có cách nào prevent được không?

**Câu trả lời mẫu:**
```
Tốt là bạn phát hiện security gap! Đó là **Potential Vulnerability**:

1. **CURRENT LOGIC (CÓ LỖ):**
   ```php
   // check_completed.php
   if ($ticket['completed_at']) {
       return error("Already completed, cannot edit");
   }
   // If completed_at = NULL → allow edit
   ```
   
   **Attack:**
   ```sql
   Hacker:
   UPDATE import_tickets SET completed_at = NULL WHERE id = 5;
   
   Sau đó:
   PUT /update_import.php?ticket_id=5
   → Check: completed_at = NULL → ALLOW EDIT ✗
   ```

2. **CÁCH FIX - ADD FLAGS:**
   ```sql
   ALTER TABLE import_tickets ADD COLUMN status ENUM('draft', 'completed') DEFAULT 'draft';
   ```
   
   New code:
   ```php
   if ($ticket['status'] === 'completed') {
       throw new Exception("Phiếu đã hoàn thành, không thể sửa");
   }
   
   // Khi complete_import:
   UPDATE import_tickets SET status = 'completed', completed_at = NOW() WHERE id = ?;
   ```
   
   **Error nếu hack:**
   ```
   Hacker: UPDATE import_tickets SET completed_at = NULL;
   
   Nhưng: status = 'completed' vẫn còn! ← Check này fail
   → Không thể edit!
   ```

3. **CÁCH FIX - ADMIN PERMISSION:**
   ```php
   // update_import.php - ĐẦU FILE
   requireAdmin();  // ← Chỉ admin mới gọi được
   
   // Sau đó
   if (ticket['status'] === 'completed') {
       throw new Exception("Cannot edit completed ticket");
   }
   ```
   
   Lợi ích:
   - Chỉ admin (có session is_admin=1) mới access được
   - Hacker (user thường) không thể gọi API này
   - Double layer defense

4. **CÁCH FIX - AUDIT LOG:**
   ```php
   // Khi edit, log lại
   INSERT INTO audit_log (user_id, action, ticket_id, old_status, new_status)
   VALUES (?, 'EDIT_IMPORT', ?, ?, ?);
   
   // Sau này giám sát:
   - Ai edit phiếu nào
   - Edit khi nào
   - Từ trạng thái nào sang trạng thái nào
   
   Nếu thấy someone edit completed ticket → RED FLAG
   ```

5. **RECOMMENDED FIX (DO):**
   ```php
   // update_import.php
   session_start();
   
   // Layer 1: AUTH - Chỉ admin
   requireAdmin();
   
   // Layer 2: Get ticket & validate
   $stmt = $conn->prepare("SELECT id, status FROM import_tickets WHERE id = ?");
   $stmt->bind_param("i", $ticket_id);
   $stmt->execute();
   $ticket = $stmt->get_result()->fetch_assoc();
   
   if (!$ticket) {
       throw new Exception("Ticket not found");
   }
   
   // Layer 3: STATUS CHECK - Enum field (stronger)
   if ($ticket['status'] === 'completed') {
       throw new Exception("Cannot edit completed ticket");
   }
   
   // Layer 4: AUDIT LOG - Log action
   insertAuditLog($_SESSION['user_id'], 'EDIT_IMPORT', $ticket_id, ...);
   
   // Layer 5: UPDATE
   updateImportItems(...);
   ```

6. **SECURITY LAYERS:**
   | Layer | Check | What if breach |
   |-------|-------|-----------------|
   | 1 AUTH | is_admin | User can't call API |
   | 2 ENTITY | ticket exists? | Prevent 404 → 500 |
   | 3 STATUS | ENUM field | Prevent logic bypass |
   | 4 AUDIT | Log action | Detect attack forensics |
   
   **→ 多層防衛 (Defense in Depth)**
```

---

## PHẦN 8: PAGINATION

### **Q13: Pagination Performance**
**Câu hỏi:**
API `products.php` và `search.php` em đều implement pagination. Em dùng `LIMIT` `OFFSET` hay `cursor-based`? Nếu có 1000 sản phẩm, user request page 999, performance thế nào?

**Câu trả lời mẫu:**
```
Performance question - điểm khó! Em giải thích:

1. **EM DÙNG CÁI NÀO:**
   ```php
   // products.php
   $page = $_GET['page'] ?? 1;
   $limit = $_GET['limit'] ?? 6;
   $offset = ($page - 1) * $limit;
   
   SELECT * FROM products 
   LIMIT $limit OFFSET $offset;
   ```
   
   **Em dùng: OFFSET-based pagination**
   (NOT cursor-based)

2. **PERFORMANCE ISSUE - OFFSET:**
   
   **Page 1:** OFFSET 0, LIMIT 6
   ```sql
   SELECT * FROM products LIMIT 6 OFFSET 0;
   → Scan từ row 0 → row 6 (6 scan)
   → FAST ✓
   ```
   
   **Page 5:** OFFSET 24, LIMIT 6
   ```sql
   SELECT * FROM products LIMIT 6 OFFSET 24;
   → Scan từ row 0 → row 30 mới lấy ra 6 cái
   → Phải bỏ qua 24 rows
   → Still OK
   ```
   
   **Page 999:** OFFSET 5994, LIMIT 6
   ```sql
   SELECT * FROM products LIMIT 6 OFFSET 5994;
   → MySQL phải scan từ row 0 → row 6000
   → Bỏ qua 5994 rows, lấy 6 rows
   → **SLOW!** Phải skip 5994 rows!
   
   Performance: O(n) where n = offset
   ```

3. **VÍ DỤ CỤ THỂ (1000 rows):**
   
   ```
   Page 1 (offset=0):   ~1ms
   Page 10 (offset=54): ~2ms
   Page 100 (offset=594): ~10ms
   Page 500 (offset=2994): ~50ms
   Page 999 (offset=5994): ~100ms+ ← SLOW!
   ```

4. **CÁCH OPTIMIZE - CURSOR-BASED:**
   ```php
   // Instead of OFFSET
   // Use WHERE id > last_id
   
   $last_id = $_GET['after'] ?? 0;
   
   SELECT * FROM products 
   WHERE id > $last_id 
   LIMIT 6;
   
   // Return: 6 rows + last_id (cursor)
   // Frontend: Pass cursor lên lần sau
   ```
   
   **Performance:**
   - Luôn scan từ index (O(log n))
   - Không phụ thuộc page number
   - Page 1 = ~1ms
   - Page 999 = ~1ms (same)
   
   ← **CONSTANT, much better!**

5. **EM CÓ OPTIMIZE KHÔNG:**
   - [ ] Dùng OFFSET (current) - okay cho small dataset
   - [ ] Add INDEX on sorting key
   - [ ] Implement cursor-based (ideal)
   
   **Recommendation:**
   ```sql
   -- Add index để LIMIT OFFSET nhanh hơn
   ALTER TABLE products ADD INDEX idx_created_at (created_at);
   
   SELECT * FROM products 
   ORDER BY created_at DESC, id DESC  -- ← index này giúp
   LIMIT 6 OFFSET 5994;  // Still O(n) but faster
   ```

6. **PRACTICAL RECOMMENDATION:**
   
   **Nếu < 10,000 products:**
   - OFFSET-based được
   - Performance OK
   
   **Nếu > 100,000 products (e-commerce lớn):**
   - Implement cursor-based MUST
   - Hoặc use database pagination extension
   
   **EM (WebBasic):**
   - Current: ~80 products (smlall)
   - OFFSET paginate OK
   - Nhưng nên thêm INDEX ↑

7. **CODE IMPROVEMENT:**
   ```php
   // products.php - current version
   $page = intval($_GET['page'] ?? 1);
   $limit = intval($_GET['limit'] ?? 6);
   $offset = ($page - 1) * $limit;
   
   // ← Add validation
   if ($page < 1) $page = 1;
   if ($limit < 1 || $limit > 100) $limit = 6;  // ← Prevent abuse
   if ($offset > 999999) { // ← Prevent huge offset query
       return error("Page too far");
   }
   ```
```

---

## PHẦN 9: ERROR HANDLING

### **Q14: Authentication Error Messages**
**Câu hỏi:**
Login API:
- Nếu user không tồn tại: return gì?
- Nếu password sai: return gì?
- Nếu account bị lock: return gì?
Tại sao các error khác nhau? Hay return giống nhau cho security?

**Câu trả lời mẫu:**
```
Security question quan trọng! Có 2 schools of thought:

1. **EM IMPLEMENT CÁI NÀO:**
   Xem file login.php:
   ```php
   // Check email exists
   if (user_not_found) {
       return error("Email không tồn tại");  // ← Specific error?
   }
   
   // Check password
   if (password_wrong) {
       return error("Mật khẩu sai");  // ← Specific error?
   }
   
   // Check lock
   if (locked) {
       return error("Tài khoản bị khóa");  // ← Specific error?
   }
   ```

2. **SECURITY PERSPECTIVE - BEST PRACTICE:**
   
   **GENERIC ERROR (Recommended for security):**
   ```php
   // Không nói cụ thể sai chỗ nào
   return error("Email hoặc mật khẩu sai");
   
   // Hoặc
   return error("Đăng nhập thất bại");
   ```
   
   **Lý do:**
   - Hacker không biết user có tồn tại hay không
   - Brute force attack khó hơn
   - Không leak sensitive info
   
   **Công kích brute-force:**
   ```
   Hacker gửi:
   POST login.php
   {"email": "admin@shop.com", "password": "123"}
   
   Response: "Email không tồn tại"
   → Hacker biết không có user "admin@shop.com"
   
   Response: "Mật khẩu sai"
   → Hacker biết user này tồn tại!
   
   Response: "Đăng nhập thất bại"
   → Hacker không biết gì, phải try blind
   ```

3. **USER EXPERIENCE PERSPECTIVE:**
   
   **SPECIFIC ERROR (Better UX):**
   ```
   "Email không tồn tại" → User biết call support
   "Mật khẩu sai" → User biết reset password
   "Tài khoản bị khóa" → User biết liên hệ admin
   ```
   
   Nhưng đánh với bảo mật!

4. **COMPROMISE - TĂNG SECURITY NHƯNG KEEP UX:**
   
   ```php
   // Backend
   if (user_not_found) {
       // Không return error
       // Thay vào đó: hash password anyway (timing attack)
       password_verify($password, '$2y$10$fake_hash');
       return error("Email hoặc mật khẩu sai");  // Generic
   }
   
   if (password_wrong) {
       return error("Email hoặc mật khẩu sai");  // Generic
   }
   
   if (locked) {
       return error("Tài khoản bị khóa");  // OK reveal này
   }
   
   // Success
   return success(...);
   ```
   
   **Giải thích:**
   - password_verify() phải gọi để tránh **timing attack**
   - Timing attack: Attacker đo thời gian response
     - 0.1ms = "user not found" (fail fast)
     - 0.3ms = "password wrong" (phải verify)
     - → Attacker biết user exists!
   
   - Gọi password_verify() anyway → delay tương tự
   - Attacker không thể phân biệt

5. **EM IMPLEMENT HIỆN TẠI:**
   Kiểm tra file login.php, em return cái gì?
   ```
   - [ ] Specific error (User-friendly nhưng less secure)
   - [ ] Generic error (Secure nhưng less user-friendly)
   - [ ] Mixed (secure + call support link)
   ```

6. **RECOMMENDED HYBRID:**
   ```php
   if (user_not_found) {
       password_verify($password, '$2y$10$fake');  // Timing attack prevent
   } else if (password_wrong) {
       // null
   } else if (locked) {
       return error("Tài khoản bị khóa. Liên hệ: support@shop.com");
   }
   
   // Generic response cho both (not found / wrong password)
   return error("Email hoặc mật khẩu không chính xác");
   
   // Return same status code (200, not 404 or 401)
   // Return same response time
   ```
```

---

## PHẦN 10: CODE CLEANUP

### **Q15: File Cleanup & Version Control**
**Câu hỏi:**
Em xóa 3 file không dùng (Adminmodel.php, invoice.php, seed_categories.php). Làm sao em biết file nào không dùng? Em search toàn bộ codebase hay sao? Nếu xóa nhầm file quan trọng, cách nào recover?

**Câu trả lời mẫu:**
```
Tốt là câu hỏi về process! Làm sao em identify unused files:

1. **CÁCH EM TÌM (Step by step):**
   
   **Step 1: Liệt kê tất cả Files**
   ```
   - BackEnd/models/Adminmodel.php
   - FrontEnd/pages/user/invoice.php
   - BackEnd/config/seed_categories.php
   ```
   
   **Step 2: Search reference trong codebase**
   ```bash
   # Tìm Adminmodel
   grep -r "Adminmodel" /WebBasic --include="*.php"
   → Result: NO MATCH (không reference ở đâu)
   
   # Tìm invoice
   grep -r "invoice" /WebBasic --include="*.php"
   → Result: NO MATCH
   
   # Tìm seed_categories
   grep -r "seed_categories" /WebBasic --include="*.php"
   → Result: NO MATCH
   ```

2. **VERIFICATION (Double check):**
   
   ```javascript
   // JavaScript search
   Ctrl+Shift+F (Find in Files)
   Search: "Adminmodel"
   
   // VS Code output
   No results found
   
   // Then
   # Manual check
   - Không import/require?
   - Không gọi trong API?
   - Không link trong HTML?
   - Không comment về file này?
   ```

3. **SAFE DELETION PROCESS:**
   
   ```bash
   # Step 1: Create backup branch
   git branch backup_before_cleanup
   
   # Step 2: Stage deletion
   git rm BackEnd/models/Adminmodel.php
   git rm FrontEnd/pages/user/invoice.php
   git rm BackEnd/config/seed_categories.php
   
   # Step 3: Commit with message
   git commit -m "Remove unused files: Adminmodel.php, invoice.php, seed_categories.php"
   
   # Step 4: Run tests
   npm test  # hoặc manual test
   
   # Step 5: If OK → Push
   git push origin main
   
   # Step 6: If NOT OK → Rollback
   git reset --hard HEAD~1
   # OR recover từ branch
   git checkout backup_before_cleanup
   ```

4. **RECOVER NGAY NGƯƠI HOẶC SAU:**
   
   **Ngay ngay (Before commit):**
   ```bash
   git restore --staged Adminmodel.php
   # Hủy `git rm` command
   ```
   
   **Sau commit (Oops, I deleted wrong file):**
   ```bash
   # Check git history
   git log --oneline
   
   # 3bac0f0 Remove unused files
   # a4c2e39 (Previous commit)
   
   # Recover file từ commit trước đó
   git show a4c2e39:BackEnd/models/Adminmodel.php > BackEnd/models/Adminmodel.php
   
   # Hoặc revert entire commit
   git revert 3bac0f0
   ```
   
   **Nếu push rồi (Even worse):**
   ```bash
   # Revert commit
   git revert 3bac0f0
   git push origin main
   
   # Commit history vẫn lưu, file được restore
   ```

5. **BEST PRACTICES EM NÊN LÀM:**
   
   ```bash
   # Trước khi xóa, backup
   git checkout -b cleanup/remove-unused
   
   # Xóa file
   git rm Adminmodel.php
   
   # Test thật kỹ
   - Run unit tests
   - Manual test all features
   - No broken links/imports
   
   # Only then merge
   git checkout main
   git merge cleanup/remove-unused
   git push
   ```

6. **VERSION CONTROL PHILOSOPHY:**
   
   **Em:
   - ✗ Đã xóa hẳn file (may mắn là search trước)
   - ✓ Nên commit từng sửa (để trace được)
   - ✓ Nên keep backup branch
   - ✓ Nên test trước push
   
   **Trong tương lai:**
   - Dùng `git rm` thay vì delete trực tiếp
   - Create feature branch
   - Do code review trước merge
   - Keep detailed commit messages
```

---

## PHẦN 11+: CODE WALKTHROUGH & QUESTIONS

### **Bonus Q16: Code Explanation - complete_import.php Lines 75-96**
**Câu hỏi:**
Em hãy mở file `complete_import.php` và giải thích từng dòng code từ line 75-96 cho tôi nghe?

**Câu trả lời mẫu:**
```
(Giả sử em explain code)

```php
// Line 75: Get current product info before update
$pstmt = $conn->prepare("SELECT stock, cost_price FROM products WHERE id = ?");

// Line 76-77: Bind product ID
$pstmt->bind_param("i", $item['product_id']);

// Line 78: Execute query
$pstmt->execute();

// Line 79-80: Get result
$presult = $pstmt->get_result();

// Line 81-86: Initialize variables
$previous_stock = 0;
$current_cost_price = 0;
if ($presult->num_rows > 0) {
    $prow = $presult->fetch_assoc();
    $previous_stock = intval($prow['stock']);         // ← Current stock
    $current_cost_price = floatval($prow['cost_price']); // ← Current cost
}

// Line 88-94: Calculate BÌNH QUÂN cost price
$new_quantity = intval($item['quantity']);      // ← New quantity to import
$import_price = floatval($item['import_price']); // ← New import price
$total_stock = $previous_stock + $new_quantity; // ← After adding

// Line 93-94: FORMULA
$new_cost_price = $total_stock > 0 
    ? ($previous_stock * $current_cost_price + $new_quantity * $import_price) / $total_stock
    : $import_price;

// Line 95-97: Update product with new cost price
$ustmt = $conn->prepare("
    UPDATE products 
    SET stock = stock + ?, 
        cost_price = ?,
        updated_at = NOW()
    WHERE id = ?
");

// Line 98-99: Bind values
$ustmt->bind_param("idi", $new_quantity, $new_cost_price, $item['product_id']);

// Line 100: Execute update
$ustmt->execute();
```

**Giải thích theo dòng:**

| Line | Purpose | Why |
|------|---------|-----|
| 75-86 | Get current stock & cost | Need for BÌNH QUÂN formula |
| 88-94 | Calculate weighted avg | (6×20 + 10×15)/16 = 16.875 |
| 95-100 | Update DB | Apply new cost price |

\`\`\`

---

### **Bonus Q17: Difference between place_order.php & complete_import.php**
**Câu hỏi:**
Sự khác nhau giữa `place_order.php` (tạo order) và `complete_import.php` (hoàn thành nhập) là gì? Tại sao cả 2 đều cần transaction?

**Câu trả lời mẫu:**
```
Good observation! Cả 2 đều multi-step:

1. **place_order.php (User checkout):**
   ```
   Step 1: Get cart items
   Step 2: Validate products exist
   Step 3: Create order record
   Step 4: Create order_details (line items)
   Step 5: Clear cart
   → Transaction bảo vệ: Nếu step 5 fail, order không create
   ```

2. **complete_import.php (Admin finish import):**
   ```
   Step 1: For each import_item
       1a: Get current product stock & cost
       1b: Calculate BÌNH QUÂN cost price
       1c: Update product stock & cost_price
       1d: Insert stock_history log
   Step 2: Mark import_ticket completed
   → Transaction bảo vệ: Nếu bất kỳ item fail, import không complete
   ```

3. **TẠI SAO BOTH CẦN TRANSACTION:**
   
   **place_order.php:**
   - Step 1: Create order
   - Step 2: Create 5 order_details
   - Step 3: Clear cart
   
   Nếu step 2 chỉ create 4/5 (fail on last one):
   - Order tạo rồi, nhưng thiếu 1 item
   - Order_details bất consistent
   - Cust thấy order nhưng không complete
   
   **WITH transaction:**
   - All or nothing
   - Order + all 5 items + clear cart
   - OR nothing happens

   **complete_import.php:**
   - Step 1: Update product#1 stock + cost
   - Step 2: Update product#2 stock + cost
   - Step 3: Log to history
   - ...
   - Step N: Mark as completed
   
   Nếu step N (mark completed) fail:
   - Products đã update
   - History logged
   - Nhưng import_ticket vẫn draft
   - Next time admin retry, sẽ try again
   - Hàng được import 2 lần!
   
   **WITH transaction:**
   - All updates happen together
   - OR nothing happens
   - Clean state, no duplicates

4. **COMPARISON:**
   | Aspect | place_order | complete_import |
   |--------|------------|-----------------|
   | Steps | 3 | N (for each item) |
   | Impact | 1 order | M products |
   | Risk | Partial order | Partial stock update |
   | Need transaction | YES | YES |

5. **CONCLUSION:**
   - Cả 2 đều modify multiple tables
   - Cả 2 đều cần consistency
   - Cả 2 = candidate cho transaction
   - GOLDEN RULE: Multi-step modification → transaction needed!
```

---

## GHI CHÚ CHO GV

Nếu là thí sinh, em nên:
- ✅ Knowledgeable về own code
- ✅ Understand WHY mình implement như vậy
- ✅ Know security implications
- ✅ Can explain design choices
- ✅ Know edge cases
- ✅ Know best practices

Nếu không biết, NÊN:
- Nói thật: "Em không biết, nhưng em có thể research"
- Không fake: GV sẽ phát hiện
- Giải thích thinking process: "Suy nghĩ em là..."
- Willing to learn

---

**Hết! Em ready để defend project em rồi** 💪🎓
