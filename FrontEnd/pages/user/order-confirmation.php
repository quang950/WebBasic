<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Xác nhận đơn hàng</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="/WebBasic/FrontEnd/assets/js/config.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }
    
    .confirmation-container {
      max-width: 900px;
      margin: 30px auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      overflow: hidden;
    }
    
    .success-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
      text-align: center;
      padding: 40px 20px;
    }
    
    .success-header i {
      font-size: 4rem;
      margin-bottom: 16px;
      animation: scaleIn 0.5s ease-out;
    }
    
    @keyframes scaleIn {
      0% { transform: scale(0); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
    
    .success-header h1 {
      font-size: 2rem;
      margin: 0 0 8px 0;
    }
    
    .success-header p {
      font-size: 1.1rem;
      margin: 0;
      opacity: 0.95;
    }
    
    .order-content {
      padding: 32px;
    }
    
    .order-info-section {
      margin-bottom: 32px;
      padding: 24px;
      background: #f8f9fa;
      border-radius: 12px;
      border-left: 4px solid #667eea;
    }
    
    .order-info-section h3 {
      color: #333;
      font-size: 1.3rem;
      margin: 0 0 16px 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .order-info-section h3 i {
      color: #667eea;
    }
    
    .info-row {
      display: flex;
      margin-bottom: 12px;
      padding: 8px 0;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .info-row:last-child {
      border-bottom: none;
    }
    
    .info-label {
      font-weight: 600;
      color: #555;
      width: 150px;
      flex-shrink: 0;
    }
    
    .info-value {
      color: #333;
      flex: 1;
    }
    
    .products-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
    }
    
    .products-table th {
      background: #667eea;
      color: white;
      padding: 14px;
      text-align: left;
      font-weight: 600;
    }
    
    .products-table td {
      padding: 14px;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .products-table tr:last-child td {
      border-bottom: none;
    }
    
    .products-table .product-name {
      font-weight: 600;
      color: #333;
    }
    
    .products-table .product-price {
      color: #667eea;
      font-weight: 600;
    }
    
    .total-section {
      margin-top: 24px;
      padding: 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      text-align: right;
    }
    
    .total-section .total-label {
      color: rgba(255,255,255,0.9);
      font-size: 1.1rem;
      margin-bottom: 8px;
    }
    
    .total-section .total-amount {
      color: white;
      font-size: 2rem;
      font-weight: 700;
    }
    
    .action-buttons {
      display: flex;
      gap: 16px;
      justify-content: center;
      margin-top: 32px;
      flex-wrap: wrap;
    }
    
    .btn {
      padding: 14px 32px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1.05rem;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    
    .btn-primary {
      background: #007bff;
      color: white;
    }
    
    .btn-primary:hover {
      background: #0056b3;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,123,255,0.3);
    }
    
    .btn-success {
      background: #28a745;
      color: white;
    }
    
    .btn-success:hover {
      background: #218838;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(40,167,69,0.3);
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    
    .btn-secondary:hover {
      background: #545b62;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(108,117,125,0.3);
    }
    
    .note-box {
      background: #fff3cd;
      border-left: 4px solid #ffc107;
      padding: 16px;
      border-radius: 8px;
      margin-top: 24px;
    }
    
    .note-box p {
      margin: 0;
      color: #856404;
      display: flex;
      align-items: start;
      gap: 10px;
    }
    
    .note-box i {
      color: #ffc107;
      margin-top: 3px;
    }
  </style>
</head>
<body>
  <div class="confirmation-container">
    <div class="success-header">
      <i class="fas fa-check-circle"></i>
      <h1>Đặt hàng thành công!</h1>
      <p>Cảm ơn bạn đã đặt hàng tại showroom của chúng tôi</p>
    </div>
    
    <div class="order-content">
      <!-- Thông tin đơn hàng -->
      <div class="order-info-section">
        <h3><i class="fas fa-receipt"></i> Thông tin đơn hàng</h3>
        <div class="info-row">
          <span class="info-label">Mã đơn hàng:</span>
          <span class="info-value" id="orderId">DH001</span>
        </div>
        <div class="info-row">
          <span class="info-label">Ngày đặt:</span>
          <span class="info-value" id="orderDate">11/11/2025 10:30</span>
        </div>
        <div class="info-row">
          <span class="info-label">Trạng thái:</span>
          <span class="info-value" style="color: #ffc107; font-weight: 600;">
            <i class="fas fa-clock"></i> Đang xử lý
          </span>
        </div>
      </div>
      
      <!-- Thông tin nhận hàng -->
      <div class="order-info-section">
        <h3><i class="fas fa-user"></i> Thông tin người nhận</h3>
        <div class="info-row">
          <span class="info-label">Họ và tên:</span>
          <span class="info-value" id="receiverName">Nguyễn Văn A</span>
        </div>
        <div class="info-row">
          <span class="info-label">Số điện thoại:</span>
          <span class="info-value" id="receiverPhone">0901234567</span>
        </div>
        <div class="info-row">
          <span class="info-label">Email:</span>
          <span class="info-value" id="receiverEmail">nguyenvana@example.com</span>
        </div>
        
        <!-- Địa chỉ chi tiết (4 thành phần tách riêng) -->
        <div style="padding-top:12px;border-top:2px solid #e0e0e0;margin-top:12px;">
          <p style="color:#666;font-size:0.9rem;margin:12px 0 12px 0;font-weight:600;"><i class="fas fa-map-marker-alt" style="color:#667eea;margin-right:6px;"></i>Địa chỉ giao hàng</p>
        </div>
        <div class="info-row">
          <span class="info-label">Đường:</span>
          <span class="info-value" id="addressStreet">123 Nguyễn Văn Linh</span>
        </div>
        <div class="info-row">
          <span class="info-label">Phường/Xã:</span>
          <span class="info-value" id="addressWard">Tân Thuận Đông</span>
        </div>
        <div class="info-row">
          <span class="info-label">Quận/Huyện:</span>
          <span class="info-value" id="addressDistrict">Quận 7</span>
        </div>
        <div class="info-row">
          <span class="info-label">Tỉnh/TP:</span>
          <span class="info-value" id="addressProvince">TP. Hồ Chí Minh</span>
        </div>
      </div>
      
      <!-- Phương thức thanh toán -->
      <div class="order-info-section">
        <h3><i class="fas fa-credit-card"></i> Phương thức thanh toán</h3>
        <div class="info-row">
          <span class="info-label">Hình thức:</span>
          <span class="info-value" id="paymentMethod">
            <i class="fas fa-money-bill-wave" style="color: #28a745;"></i> Thanh toán khi nhận hàng (COD)
          </span>
        </div>
        
        <!-- QR Code Section (hiển thị nếu chọn chuyển khoản) -->
        <div id="bankTransferSection" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
          <h4 style="color: #333; margin: 0 0 16px 0;">
            <i class="fas fa-qrcode"></i> Thông tin chuyển khoản
          </h4>
          
          <!-- QR Code -->
          <div style="text-align: center; margin-bottom: 20px;">
            <div id="qrCodeContainer" style="display: inline-block; padding: 16px; background: white; border: 2px solid #667eea; border-radius: 8px;">
              <img src="" alt="QR Code" style="width: 200px; height: 200px;">
            </div>
            <p style="margin-top: 12px; font-size: 0.9rem; color: #666;">Quét mã QR VietQR để chuyển khoản</p>
          </div>
          
          <!-- Thông tin chi tiết -->
          <div style="background: #f0f8ff; padding: 16px; border-radius: 8px; border-left: 4px solid #007bff;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px;">
              <div>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Ngân hàng</p>
                <p style="margin: 4px 0 0 0; color: #333; font-weight: 600;" id="bankName">Ngân hàng TMCP Ngoại thương Việt Nam (Vietcombank)</p>
              </div>
              <div>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Số tài khoản</p>
                <p style="margin: 4px 0 0 0; color: #333; font-weight: 600;">1032703862</p>
              </div>
            </div>
            
            <div style="margin-bottom: 12px;">
              <p style="margin: 0; color: #666; font-size: 0.9rem;">Chủ tài khoản</p>
              <p style="margin: 4px 0 0 0; color: #333; font-weight: 600;">NGUYEN QUANG VINH</p>
            </div>
            
            <div>
              <p style="margin: 0; color: #666; font-size: 0.9rem;">Nội dung chuyển khoản</p>
              <p style="margin: 4px 0 0 0; color: #333; font-weight: 600; word-break: break-all;" id="transferContent">
                DH001 - Nguyen Van A
              </p>
            </div>
          </div>
          
          <div style="background: #fffbea; padding: 12px; border-radius: 8px; margin-top: 12px; border-left: 4px solid #ffc107;">
            <p style="margin: 0; color: #856404; font-size: 0.9rem;">
              <i class="fas fa-info-circle"></i> 
              <strong>Lưu ý:</strong> Vui lòng nhập đúng nội dung chuyển khoản để chúng tôi dễ dàng xác nhận đơn hàng của bạn
            </p>
          </div>
        </div>
      </div>
      
      <!-- Danh sách sản phẩm -->
      <div class="order-info-section">
        <h3><i class="fas fa-car"></i> Sản phẩm đã đặt</h3>
        <table class="products-table">
          <thead>
            <tr>
              <th>Sản phẩm</th>
              <th style="text-align: center;">Số lượng</th>
              <th style="text-align: right;">Đơn giá</th>
              <th style="text-align: right;">Thành tiền</th>
            </tr>
          </thead>
          <tbody id="productsBody">
            <!-- Sẽ được load bằng JS -->
          </tbody>
        </table>
        
        <div class="total-section">
          <div class="total-label">Tổng cộng:</div>
          <div class="total-amount" id="totalAmount">0 VNĐ</div>
        </div>
      </div>
      
      <!-- Ghi chú -->
      <div class="note-box">
        <p>
          <i class="fas fa-info-circle"></i>
          <span>
            <strong>Lưu ý:</strong> Đơn hàng của bạn đang được xử lý. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận và giao hàng. 
            Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ hotline: <strong>1900-xxxx</strong>
          </span>
        </p>
      </div>
      
      <!-- Nút hành động -->
      <div class="action-buttons">
        <a href="../../index.php" class="btn btn-primary">
          <i class="fas fa-home"></i> Về trang chủ
        </a>
        <a href="orders.php" class="btn btn-success">
          <i class="fas fa-list"></i> Xem đơn hàng của tôi
        </a>
        <a href="cart.php" class="btn btn-secondary">
          <i class="fas fa-shopping-cart"></i> Tiếp tục mua hàng
        </a>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', async function() {
      // Kiểm tra đăng nhập
      const isLoggedIn = localStorage.getItem('userLoggedIn') === 'true';
      if (!isLoggedIn) {
        alert('Vui lòng đăng nhập!');
        window.location.href = 'login.php';
        return;
      }
      
      // Lấy thông tin đơn hàng từ sessionStorage
      const orderData = JSON.parse(sessionStorage.getItem('currentOrder') || '{}');
      const apiBase = (typeof BASE_URL !== 'undefined') ? BASE_URL + '/BackEnd/api' : '/WebBasic/BackEnd/api';
      
      if (!orderData.products || orderData.products.length === 0) {
        // Load from API instead of localStorage
        try {
          const response = await fetch(apiBase + '/cart.php', {
            method: 'GET',
            credentials: 'include'
          });
          const data = await response.json();
          const cart = (data.success && Array.isArray(data.data)) ? data.data : [];
          if (cart.length === 0) {
            alert('Không có thông tin đơn hàng!');
            window.location.href = 'cart.php';
            return;
          }
        
          // Tạo dữ liệu đơn hàng mẫu
          orderData.orderId = 'DH' + Date.now().toString().slice(-6);
          orderData.orderDate = new Date().toLocaleString('vi-VN');
          orderData.products = cart;
          orderData.receiverName = 'Nguyễn Văn A';
          orderData.receiverPhone = '0901234567';
          orderData.receiverEmail = localStorage.getItem('userEmail') || 'customer@example.com';
          orderData.receiverAddress = '123 Nguyễn Văn Linh, Phường Tân Thuận Đông, Quận 7, TP. Hồ Chí Minh';
          orderData.paymentMethod = 'Thanh toán khi nhận hàng (COD)';
        } catch(err) {
          console.error('Error loading cart:', err);
          alert('Không thể tải thông tin đơn hàng!');
          return;
        }
      }
      
      // Hiển thị thông tin đơn hàng
      document.getElementById('orderId').textContent = orderData.orderId || 'DH001';
      document.getElementById('orderDate').textContent = orderData.orderDate || new Date().toLocaleString('vi-VN');
      document.getElementById('receiverName').textContent = orderData.receiverName || 'N/A';
      document.getElementById('receiverPhone').textContent = orderData.receiverPhone || 'N/A';
      document.getElementById('receiverEmail').textContent = orderData.receiverEmail || 'N/A';
      
      // Parse địa chỉ thành 4 thành phần
      const fullAddress = orderData.receiverAddress || 'N/A';
      const addressParts = fullAddress.split(',').map(part => part.trim());
      
      document.getElementById('addressStreet').textContent = addressParts[0] || 'N/A';
      document.getElementById('addressWard').textContent = addressParts[1] || 'N/A';
      document.getElementById('addressDistrict').textContent = addressParts[2] || 'N/A';
      document.getElementById('addressProvince').textContent = addressParts[3] || 'N/A';
      
      // Hiển thị phương thức thanh toán
      const paymentMethod = orderData.paymentMethod || 'Thanh toán khi nhận hàng (COD)';
      let paymentIcon = '<i class="fas fa-money-bill-wave" style="color: #28a745;"></i>';
      if (paymentMethod.includes('ngân hàng')) {
        paymentIcon = '<i class="fas fa-university" style="color: #007bff;"></i>';
        // Hiển thị QR code section khi chọn chuyển khoản ngân hàng
        showBankTransferInfo(orderData);
      } else if (paymentMethod.includes('trực tuyến')) {
        paymentIcon = '<i class="fas fa-wallet" style="color: #17a2b8;"></i>';
      }
      document.getElementById('paymentMethod').innerHTML = paymentIcon + ' ' + paymentMethod;
      
      // Hiển thị danh sách sản phẩm
      const productsBody = document.getElementById('productsBody');
      let totalAmount = 0;
      
      orderData.products.forEach(product => {
        const quantity = product.quantity || 1;
        const price = product.price || 0;
        const subtotal = price * quantity;
        totalAmount += subtotal;
        
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <div class="product-name">
              <i class="fas fa-car" style="color: #667eea; margin-right: 8px;"></i>
              ${product.brand || ''} ${product.name || 'Sản phẩm'}
            </div>
          </td>
          <td style="text-align: center;">${quantity}</td>
          <td style="text-align: right;" class="product-price">${formatPrice(price)}</td>
          <td style="text-align: right;" class="product-price">${formatPrice(subtotal)}</td>
        `;
        productsBody.appendChild(row);
      });
      
      // Hiển thị tổng tiền
      document.getElementById('totalAmount').textContent = formatPrice(totalAmount);
      
      // Clear cart via API after order confirmation
      fetch(apiBase + '/clear_cart.php', {
        method: 'POST',
        credentials: 'include'
      }).catch(err => console.log('Cart cleared'));
    });
    
    function formatPrice(price) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(price);
    }
    
    // Hàm hiển thị thông tin chuyển khoản và QR code
    function showBankTransferInfo(orderData) {
      const bankTransferSection = document.getElementById('bankTransferSection');
      bankTransferSection.style.display = 'block';
      
      // Thông tin chuyển khoản
      const bankCode = '970436'; // Vietcombank BIN
      const accountNo = '1032703862'; // STK thực
      const accountHolder = 'NGUYEN QUANG VINH'; // Chủ TK
      const orderId = orderData.orderId || 'DH001';
      const customerName = orderData.receiverName || 'Khách hàng';
      const amount = orderData.totalPrice || 0;
      
      // Nội dung chuyển khoản
      const transferContent = `${orderId} - ${customerName}`;
      document.getElementById('transferContent').textContent = transferContent;
      
      // Lấy QR code từ VietQR API
      // Format: https://img.vietqr.io/image/{BANK_CODE}-{ACCOUNT}-{TEMPLATE}.png?amount={AMOUNT}&addInfo={CONTENT}&accountName={NAME}
      const qrImageUrl = `https://img.vietqr.io/image/${bankCode}-${accountNo}-compact2.png?amount=${Math.floor(amount)}&addInfo=${encodeURIComponent(transferContent)}&accountName=${encodeURIComponent(accountHolder)}`;
      
      // Hiển thị hình ảnh QR từ VietQR
      const qrContainer = document.getElementById('qrCodeContainer');
      qrContainer.innerHTML = `<img src="${qrImageUrl}" alt="VietQR Code" style="width: 200px; height: 200px; border-radius: 8px;">`;
    }
  </script>
</body>
</html>

