<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - 3 Boys Auto</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .shipping-section, .payment-section {
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }

        /* Address List */
        .address-list {
            margin-bottom: 2rem;
        }

        .address-item {
            background: white;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .address-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.1);
        }

        .address-item.selected {
            border-color: #007bff;
            background: #f0f7ff;
        }

        .address-item-radio {
            margin-right: 1rem;
            cursor: pointer;
        }

        .address-item-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .address-item-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .address-item-badge {
            background: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }

        .address-item-content {
            margin-left: 2rem;
        }

        .address-item-detail {
            color: #666;
            margin: 0.3rem 0;
            font-size: 0.95rem;
        }

        .address-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .address-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .address-btn:hover { background: #0056b3; }
        .address-btn.delete { background: #dc3545; }
        .address-btn.delete:hover { background: #c82333; }

        /* Form */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        /* Payment Methods */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #007bff;
        }

        .payment-method.selected {
            border-color: #007bff;
            background: #f0f7ff;
        }

        .payment-method input[type="radio"] {
            margin-right: 1rem;
        }

        .payment-method-label {
            flex: 1;
            font-weight: 500;
            color: #333;
        }

        .payment-method-icon {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }

        /* Order Summary */
        .order-summary {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .cart-items {
            margin-bottom: 1.5rem;
            max-height: 300px;
            overflow-y: auto;
        }

        .cart-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
            font-size: 0.95rem;
        }

        .cart-item-row:last-child {
            border-bottom: none;
        }

        .cart-item-name {
            flex: 1;
            color: #333;
        }

        .cart-item-qty {
            color: #666;
            min-width: 30px;
            text-align: center;
        }

        .cart-item-price {
            color: #007bff;
            font-weight: 600;
            min-width: 80px;
            text-align: right;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            font-size: 0.95rem;
        }

        .summary-row.total {
            border-top: 2px solid #e0e0e0;
            border-bottom: 2px solid #e0e0e0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #dc3545;
            padding: 1rem 0;
            margin: 1rem 0;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
            }
        }
    </style>
</head>
<body>
    <script src="../../assets/js/config.js"></script>
    <script src="../../assets/js/main.js"></script>

    <header>
        <nav class="navbar">
            <div class="container">
                <div class="nav-content">
                    <div class="logo">
                        <a href="../../index.php" style="color: white; text-decoration: none; font-weight: bold;">3 BOYS AUTO</a>
                    </div>
                    <div class="user-actions">
                        <a href="#" onclick="checkLoginAndGoToCart()" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-text">Giỏ hàng</span>
                        </a>
                        <div class="login-options" id="loginOptions">
                            <a href="login.php" class="blob-btn login-btn">Đăng nhập</a>
                        </div>
                        <div class="user-info" id="userInfo" style="display: none;">
                            <span class="user-name" id="userName"></span>
                            <a href="#" class="logout-link" onclick="logout()">Đăng xuất</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="checkout-container">
            <h1>Thanh Toán</h1>

            <div class="checkout-grid">
                <!-- Left Column: Shipping & Payment -->
                <div>
                    <!-- Shipping Address Section -->
                    <div class="shipping-section">
                        <div class="section-title">
                            <i class="fas fa-map-marker-alt"></i> Địa Chỉ Giao Hàng
                        </div>

                        <div class="address-list" id="addressList">
                            <div style="text-align: center; color: #999; padding: 2rem;">
                                Đang tải địa chỉ...
                            </div>
                        </div>

                        <button class="btn btn-secondary" onclick="showAddAddressForm()">
                            <i class="fas fa-plus"></i> Thêm Địa Chỉ Mới
                        </button>
                    </div>

                    <!-- Add Address Form (Hidden by default) -->
                    <div id="addAddressForm" style="display: none; margin-top: 2rem;">
                        <div class="shipping-section">
                            <div class="section-title">Thêm Địa Chỉ Giao Hàng Mới</div>

                            <form id="newAddressFormElement" onsubmit="submitNewAddress(event)">
                                <div class="form-group">
                                    <label>Tên Người Nhận *</label>
                                    <input type="text" id="newRecipientName" required>
                                </div>

                                <div class="form-group">
                                    <label>Số Điện Thoại *</label>
                                    <input type="tel" id="newPhone" placeholder="0900000000" required>
                                </div>

                                <div class="form-group">
                                    <label>Địa Chỉ Chi Tiết *</label>
                                    <input type="text" id="newAddressDetail" placeholder="Số nhà, đường phố" required>
                                </div>

                                <div class="form-group">
                                    <label>Phường/Xã</label>
                                    <input type="text" id="newWard">
                                </div>

                                <div class="form-group">
                                    <label>Quận/Huyện *</label>
                                    <input type="text" id="newDistrict" required>
                                </div>

                                <div class="form-group">
                                    <label>Tỉnh/Thành Phố *</label>
                                    <input type="text" id="newProvince" required>
                                </div>

                                <div class="form-group">
                                    <label>Mã Bưu Điện</label>
                                    <input type="text" id="newPostalCode">
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="newIsDefault"> Đặt làm địa chỉ mặc định
                                    </label>
                                </div>

                                <div class="button-group">
                                    <button type="submit" class="btn btn-primary">Lưu Địa Chỉ</button>
                                    <button type="button" class="btn btn-secondary" onclick="hideAddAddressForm()">Hủy</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Method Section -->
                    <div class="payment-section" style="margin-top: 2rem;">
                        <div class="section-title">
                            <i class="fas fa-credit-card"></i> Phương Thức Thanh Toán
                        </div>

                        <div class="payment-methods">
                            <label class="payment-method selected">
                                <input type="radio" name="paymentMethod" value="cash" checked onchange="updatePaymentInfo()">
                                <i class="fas fa-money-bill payment-method-icon"></i>
                                <div>
                                    <div class="payment-method-label">Thanh Toán Tiền Mặt (COD)</div>
                                    <small style="color: #999;">Thanh toán khi nhận hàng</small>
                                </div>
                            </label>

                            <label class="payment-method" onclick="selectPaymentMethod(this)">
                                <input type="radio" name="paymentMethod" value="transfer" onchange="updatePaymentInfo()">
                                <i class="fas fa-bank payment-method-icon"></i>
                                <div>
                                    <div class="payment-method-label">Chuyển Khoản Ngân Hàng</div>
                                    <small style="color: #999;">Chuyển khoản trước khi giao hàng</small>
                                </div>
                            </label>

                            <label class="payment-method" onclick="selectPaymentMethod(this)">
                                <input type="radio" name="paymentMethod" value="online" onchange="updatePaymentInfo()">
                                <i class="fas fa-laptop payment-method-icon"></i>
                                <div>
                                    <div class="payment-method-label">Thanh Toán Trực Tuyến</div>
                                    <small style="color: #999;">VNPay, Stripe (chưa xử lý)</small>
                                </div>
                            </label>
                        </div>

                        <div id="paymentInfo" style="margin-top: 1.5rem; display: none; padding: 1rem; background: #fffbea; border-radius: 6px; border-left: 4px solid #ffc107;">
                            <!-- Payment info will show here -->
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div>
                    <div class="order-summary">
                        <div class="summary-title">
                            <i class="fas fa-receipt"></i> Tóm Tắt Đơn Hàng
                        </div>

                        <div class="cart-items" id="cartItemsSummary">
                            <div style="text-align: center; color: #999;">Đang tải...</div>
                        </div>

                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span id="subtotalAmount">0 ₫</span>
                        </div>

                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span id="shippingFee">0 ₫</span>
                        </div>

                        <div class="summary-row total">
                            <span>Tổng Cộng:</span>
                            <span id="totalAmount">0 ₫</span>
                        </div>

                        <button class="btn btn-primary" onclick="proceedToReview()" id="proceedBtn" disabled>
                            <i class="fas fa-arrow-right"></i> Xem Tóm Tắt Đơn
                        </button>

                        <button class="btn btn-secondary" onclick="goBackToCart()" style="margin-top: 1rem;">
                            <i class="fas fa-arrow-left"></i> Quay Lại Giỏ Hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let currentUserId = null;
        let selectedAddressId = null;
        let cartData = [];

        document.addEventListener('DOMContentLoaded', async () => {
            // Check login
            const email = localStorage.getItem('userEmail');
            if (!email) {
                alert('Vui lòng đăng nhập');
                window.location.href = 'login.php';
                return;
            }

            currentUserId = localStorage.getItem('userId');
            if (!currentUserId) {
                // Get userId from DB quá phức tạp, lấy từ localStorage được set khi login
                alert('Vui lòng đăng nhập lại');
                window.location.href = 'login.php';
                return;
            }

            // Load shipping addresses
            await loadShippingAddresses();

            // Load cart items
            await loadCartItems();
        });

        async function loadShippingAddresses() {
            try {
                const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL) 
                    ? BASE_URL + '/BackEnd/api/user/get_shipping_addresses.php?user_id=' + currentUserId
                    : '/WebBasic/BackEnd/api/user/get_shipping_addresses.php?user_id=' + currentUserId;

                const response = await fetch(apiUrl);
                const result = await response.json();

                if (!result.success || !result.data || result.data.length === 0) {
                    document.getElementById('addressList').innerHTML = `
                        <div style="text-align: center; color: #999; padding: 2rem;">
                            <p>Chưa có địa chỉ giao hàng</p>
                            <p style="font-size: 0.9rem;">Vui lòng thêm địa chỉ để tiếp tục</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                result.data.forEach((address, index) => {
                    const isDefault = address.is_default ? true : false;
                    if (index === 0 || isDefault) {
                        selectedAddressId = address.id;
                    }

                    html += `
                        <div class="address-item ${(index === 0 || isDefault) ? 'selected' : ''}" 
                             onclick="selectAddress(${address.id}, this)">
                            <div class="address-item-header">
                                <input type="radio" class="address-item-radio" name="address" value="${address.id}" 
                                       ${(index === 0 || isDefault) ? 'checked' : ''}>
                                <span class="address-item-name">${address.recipient_name}</span>
                                ${isDefault ? '<span class="address-item-badge">Mặc Định</span>' : ''}
                            </div>
                            <div class="address-item-content">
                                <div class="address-item-detail"><strong>Điện thoại:</strong> ${address.phone}</div>
                                <div class="address-item-detail"><strong>Địa chỉ:</strong> ${address.address_detail}, ${address.ward}, ${address.district}, ${address.province}</div>
                                <div class="address-actions">
                                    <button class="address-btn" onclick="editAddress(${address.id}, event)">Sửa</button>
                                    <button class="address-btn delete" onclick="deleteAddress(${address.id}, event)">Xóa</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                document.getElementById('addressList').innerHTML = html;
                updateProceedButton();

            } catch (error) {
                console.error('Lỗi khi tải địa chỉ:', error);
                alert('Lỗi khi tải địa chỉ');
            }
        }

        function selectAddress(addressId, element) {
            selectedAddressId = addressId;
            document.querySelectorAll('.address-item').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            updateProceedButton();
        }

        function showAddAddressForm() {
            document.getElementById('addAddressForm').style.display = 'block';
        }

        function hideAddAddressForm() {
            document.getElementById('addAddressForm').style.display = 'none';
            document.getElementById('newAddressFormElement').reset();
        }

        async function submitNewAddress(event) {
            event.preventDefault();

            const newAddress = {
                user_id: currentUserId,
                recipient_name: document.getElementById('newRecipientName').value,
                phone: document.getElementById('newPhone').value,
                address_detail: document.getElementById('newAddressDetail').value,
                ward: document.getElementById('newWard').value,
                district: document.getElementById('newDistrict').value,
                province: document.getElementById('newProvince').value,
                postal_code: document.getElementById('newPostalCode').value,
                is_default: document.getElementById('newIsDefault').checked ? 1 : 0
            };

            try {
                const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL)
                    ? BASE_URL + '/BackEnd/api/user/add_shipping_address.php'
                    : '/WebBasic/BackEnd/api/user/add_shipping_address.php';

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newAddress)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Thêm địa chỉ thành công');
                    hideAddAddressForm();
                    await loadShippingAddresses();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi:', error);
                alert('Lỗi khi thêm địa chỉ');
            }
        }

        async function deleteAddress(addressId, event) {
            event.stopPropagation();
            if (!confirm('Xác nhận xóa địa chỉ này?')) return;

            try {
                const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL)
                    ? BASE_URL + '/BackEnd/api/user/delete_shipping_address.php?id=' + addressId + '&user_id=' + currentUserId
                    : '/WebBasic/BackEnd/api/user/delete_shipping_address.php?id=' + addressId + '&user_id=' + currentUserId;

                const response = await fetch(apiUrl, { method: 'DELETE' });
                const result = await response.json();

                if (result.success) {
                    alert('Xóa địa chỉ thành công');
                    await loadShippingAddresses();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi:', error);
                alert('Lỗi khi xóa địa chỉ');
            }
        }

        function editAddress(addressId, event) {
            event.stopPropagation();
            alert('Chức năng sửa đang phát triển');
        }

        async function loadCartItems() {
            // Load từ localStorage hoặc API
        // Get cart from API
        const apiBase = (typeof BASE_URL !== 'undefined') ? BASE_URL + '/BackEnd/api' : '/WebBasic/BackEnd/api';
        const response = await fetch(apiBase + '/cart.php', {
          method: 'GET',
          credentials: 'include'
        });
        const data = await response.json();
        const cart = (data.success && Array.isArray(data.data)) ? data.data : [];
            cartData = cart;

            if (cart.length === 0) {
                alert('Giỏ hàng trống');
                window.location.href = 'cart.php';
                return;
            }

            let html = '';
            let subtotal = 0;

            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                html += `
                    <div class="cart-item-row">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-qty">x${item.quantity}</div>
                        <div class="cart-item-price">${formatPrice(itemTotal)}</div>
                    </div>
                `;
            });

            document.getElementById('cartItemsSummary').innerHTML = html;
            document.getElementById('subtotalAmount').textContent = formatPrice(subtotal);
            document.getElementById('totalAmount').textContent = formatPrice(subtotal);

            updateProceedButton();
        }

        function selectPaymentMethod(element) {
            document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            updatePaymentInfo();
        }

        function updatePaymentInfo() {
            const method = document.querySelector('input[name="paymentMethod"]:checked').value;
            const infoEl = document.getElementById('paymentInfo');

            if (method === 'transfer') {
                infoEl.style.display = 'block';
                infoEl.innerHTML = `
                    <strong>Thông tin chuyển khoản:</strong><br>
                    Tên tài khoản: <strong>3 BOYS AUTO</strong><br>
                    Số tài khoản: <strong>123456789</strong><br>
                    Ngân hàng: <strong>ACB</strong><br>
                    <small style="color: #999;">Vui lòng chuyển khoản trước khi giao hàng</small>
                `;
            } else if (method === 'cash') {
                infoEl.style.display = 'block';
                infoEl.innerHTML = `
                    <strong>Thanh toán tiền mặt:</strong><br>
                    Bạn sẽ thanh toán khi nhân viên giao hàng đến. Vui lòng chuẩn bị tiền đúng số lượng.
                `;
            } else {
                infoEl.style.display = 'none';
            }
        }

        function updateProceedButton() {
            const btn = document.getElementById('proceedBtn');
            btn.disabled = !selectedAddressId || cartData.length === 0;
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
        }

        function goBackToCart() {
            window.location.href = 'cart.php';
        }

        function proceedToReview() {
            if (!selectedAddressId) {
                alert('Vui lòng chọn địa chỉ giao hàng');
                return;
            }

            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

            // Save to sessionStorage for next page
            sessionStorage.setItem('checkoutData', JSON.stringify({
                addressId: selectedAddressId,
                paymentMethod: paymentMethod,
                cartItems: cartData
            }));

            window.location.href = 'order-review.php';
        }

        // Initialize payment method display
        updatePaymentInfo();
    </script>
</body>
</html>
