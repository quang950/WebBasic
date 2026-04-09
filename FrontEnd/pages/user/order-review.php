<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem Tóm Tắt Đơn - 3 Boys Auto</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .review-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .review-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 2rem;
        }

        .review-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .review-section-icon {
            color: #007bff;
        }

        /* Shipping Info */
        .shipping-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .info-block {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }

        .info-label {
            font-size: 0.85rem;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.1rem;
            color: #333;
            font-weight: 500;
        }

        .info-value-big {
            font-size: 1.3rem;
            color: #ff6b6b;
            font-weight: 700;
        }

        /* Cart Items */
        .order-items {
            margin-bottom: 2rem;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .items-table th {
            background: #f0f0f0;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }

        .items-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .item-name {
            color: #333;
            font-weight: 500;
        }

        .item-price {
            color: #007bff;
            font-weight: 600;
        }

        .item-total {
            color: #dc3545;
            font-weight: 700;
        }

        /* Summary */
        .summary-section {
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 6px;
            margin-top: 2rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            font-size: 1rem;
            color: #333;
        }

        .summary-row.subtotal {
            border-bottom: 1px solid #e0e0e0;
        }

        .summary-row.total {
            margin-top: 1rem;
            padding: 1rem 0;
            border-top: 2px solid #e0e0e0;
            border-bottom: 2px solid #e0e0e0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #dc3545;
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
            background: #28a745;
            color: white;
        }

        .btn-primary:hover {
            background: #218838;
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .confirmation-icon {
            font-size: 3rem;
            color: #28a745;
            text-align: center;
            margin: 2rem 0;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 2rem;
            display: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .shipping-info-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .items-table {
                font-size: 0.85rem;
            }

            .items-table th, .items-table td {
                padding: 0.75rem 0.5rem;
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
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="review-container">
            <h1 style="text-align: center; margin-bottom: 2rem;">Xem Tóm Tắt Đơn Hàng</h1>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Đơn hàng đã được tạo thành công!
            </div>

            <!-- Shipping Address -->
            <div class="review-card">
                <div class="review-section-title">
                    <i class="fas fa-map-marker-alt review-section-icon"></i>
                    Địa Chỉ Giao Hàng
                </div>
                <div class="shipping-info-grid">
                    <div class="info-block">
                        <div class="info-label">Người Nhận</div>
                        <div class="info-value" id="recipientName">-</div>
                    </div>
                    <div class="info-block">
                        <div class="info-label">Điện Thoại</div>
                        <div class="info-value" id="recipientPhone">-</div>
                    </div>
                    <div class="info-block" style="grid-column: 1 / -1;">
                        <div class="info-label">Địa Chỉ</div>
                        <div class="info-value" id="recipientAddress">-</div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="review-card">
                <div class="review-section-title">
                    <i class="fas fa-credit-card review-section-icon"></i>
                    Phương Thức Thanh Toán
                </div>
                <div class="info-block">
                    <div class="info-label">Phương Thức</div>
                    <div class="info-value" id="paymentMethodDisplay">-</div>
                    <div style="margin-top: 1rem; font-size: 0.9rem; color: #666;" id="paymentInfo"></div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="review-card">
                <div class="review-section-title">
                    <i class="fas fa-shopping-cart review-section-icon"></i>
                    Sản Phẩm Đặt Mua
                </div>

                <div class="order-items">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Tên Sản Phẩm</th>
                                <th style="text-align: center;">Số Lượng</th>
                                <th style="text-align: right;">Đơn Giá</th>
                                <th style="text-align: right;">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">Đang tải...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="summary-section">
                    <div class="summary-row subtotal">
                        <span>Tạm Tính:</span>
                        <span id="subtotalAmount">0 ₫</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí Vận Chuyển:</span>
                        <span id="shippingFee">0 ₫</span>
                    </div>
                    <div class="summary-row total">
                        <span>Tổng Cộng:</span>
                        <span id="totalAmount">0 ₫</span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button class="btn btn-primary" onclick="confirmAndPlaceOrder()" id="confirmBtn">
                    <i class="fas fa-check"></i> Xác Nhận & Đặt Hàng
                </button>
                <button class="btn btn-secondary" onclick="goBackToCheckout()">
                    <i class="fas fa-arrow-left"></i> Quay Lại Thanh Toán
                </button>
            </div>
        </div>
    </main>

    <script>
        let checkoutData = null;
        let addressData = null;
        let currentUserId = null;

        document.addEventListener('DOMContentLoaded', async () => {
            const email = localStorage.getItem('userEmail');
            if (!email) {
                alert('Vui lòng đăng nhập');
                window.location.href = 'login.php';
                return;
            }

            currentUserId = localStorage.getItem('userId');
            checkoutData = JSON.parse(sessionStorage.getItem('checkoutData'));

            if (!checkoutData || !checkoutData.addressId || !checkoutData.cartItems) {
                alert('Dữ liệu đơn hàng không hợp lệ');
                window.location.href = 'checkout.php';
                return;
            }

            await loadAddressData();
            displayOrderReview();
        });

        async function loadAddressData() {
            try {
                const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL)
                    ? BASE_URL + '/BackEnd/api/user/get_shipping_addresses.php?user_id=' + currentUserId
                    : '/WebBasic/BackEnd/api/user/get_shipping_addresses.php?user_id=' + currentUserId;

                const response = await fetch(apiUrl);
                const result = await response.json();

                if (result.success && result.data) {
                    addressData = result.data.find(addr => addr.id == checkoutData.addressId);
                }
            } catch (error) {
                console.error('Lỗi:', error);
            }
        }

        function displayOrderReview() {
            if (!addressData) return;

            // Display shipping address
            document.getElementById('recipientName').textContent = addressData.recipient_name;
            document.getElementById('recipientPhone').textContent = addressData.phone;

            const fullAddress = [
                addressData.address_detail,
                addressData.ward,
                addressData.district,
                addressData.province,
                addressData.postal_code
            ].filter(Boolean).join(', ');
            document.getElementById('recipientAddress').textContent = fullAddress;

            // Display payment method
            const paymentLabels = {
                cash: 'Thanh Toán Tiền Mặt (COD)',
                transfer: 'Chuyển Khoản Ngân Hàng',
                online: 'Thanh Toán Trực Tuyến'
            };

            document.getElementById('paymentMethodDisplay').textContent = paymentLabels[checkoutData.paymentMethod] || 'Không xác định';

            if (checkoutData.paymentMethod === 'transfer') {
                document.getElementById('paymentInfo').innerHTML = `
                    <strong>Thông tin chuyển khoản:</strong><br>
                    Tài khoản: 123456789 (ACB)<br>
                    Tên: 3 BOYS AUTO
                `;
            } else if (checkoutData.paymentMethod === 'cash') {
                document.getElementById('paymentInfo').innerHTML = `
                    Thanh toán khi nhân viên giao hàng
                `;
            }

            // Display cart items
            displayCartItems();
        }

        function displayCartItems() {
            let html = '';
            let subtotal = 0;

            checkoutData.cartItems.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                html += `
                    <tr>
                        <td class="item-name">${item.name}</td>
                        <td style="text-align: center;">${item.quantity}</td>
                        <td class="item-price" style="text-align: right;">${formatPrice(item.price)}</td>
                        <td class="item-total" style="text-align: right;">${formatPrice(itemTotal)}</td>
                    </tr>
                `;
            });

            document.getElementById('itemsTableBody').innerHTML = html;
            document.getElementById('subtotalAmount').textContent = formatPrice(subtotal);
            document.getElementById('totalAmount').textContent = formatPrice(subtotal);
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
        }

        async function confirmAndPlaceOrder() {
            const btn = document.getElementById('confirmBtn');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Đang xử lý...';

            try {
                // Prepare order data
                const orderData = {
                    user_id: currentUserId,
                    address_id: checkoutData.addressId,
                    payment_method: checkoutData.paymentMethod,
                    cart_items: checkoutData.cartItems.map(item => ({
                        product_id: item.product_id || 0,
                        quantity: item.quantity,
                        price: item.price
                    }))
                };

                const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL)
                    ? BASE_URL + '/BackEnd/api/place_order.php'
                    : '/WebBasic/BackEnd/api/place_order.php';

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });

                const result = await response.json();

                if (result.success) {
                    // Show success
                    document.getElementById('successMessage').style.display = 'block';
                    document.getElementById('confirmBtn').innerHTML = `
                        <i class="fas fa-check-circle"></i> Đặt Hàng Thành Công!
                    `;

                    // Clear cart and checkout data
                    localStorage.removeItem('cart');
                    sessionStorage.removeItem('checkoutData');

                    // Redirect to orders page after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'orders.php';
                    }, 2000);
                } else {
                    alert('Lỗi: ' + (result.message || 'Không thể tạo đơn hàng'));
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Lỗi:', error);
                alert('Lỗi khi tạo đơn hàng: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function goBackToCheckout() {
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html>
