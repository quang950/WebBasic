<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hóa Đơn - 3 Boys Auto</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="/WebBasic/FrontEnd/assets/js/config.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin: 0;
      padding: 40px 20px;
    }
    .invoice-container {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      overflow: hidden;
    }
    .invoice-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      padding: 30px;
      text-align: center;
    }
    .invoice-header h1 {
      margin: 0 0 10px 0;
      font-size: 2rem;
    }
    .invoice-header p {
      margin: 5px 0;
      opacity: 0.9;
    }
    .invoice-body {
      padding: 40px;
    }
    .invoice-info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 2px solid #e0e0e0;
    }
    .info-section h3 {
      margin: 0 0 15px 0;
      color: #667eea;
      font-size: 1.1rem;
    }
    .info-section p {
      margin: 8px 0;
      color: #555;
      line-height: 1.6;
    }
    .info-section strong {
      color: #333;
      display: inline-block;
      min-width: 120px;
    }
    .invoice-items {
      margin: 30px 0;
    }
    .invoice-items h3 {
      color: #667eea;
      margin-bottom: 20px;
      font-size: 1.2rem;
    }
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .items-table thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
    }
    .items-table th {
      padding: 15px;
      text-align: left;
      font-weight: 600;
    }
    .items-table td {
      padding: 15px;
      border-bottom: 1px solid #e0e0e0;
    }
    .items-table tbody tr:hover {
      background: #f8f9ff;
    }
    .item-img {
      width: 80px;
      height: 60px;
      object-fit: cover;
      border-radius: 6px;
    }
    .invoice-total {
      text-align: right;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 3px solid #667eea;
    }
    .invoice-total .total-row {
      display: flex;
      justify-content: flex-end;
      margin: 10px 0;
      font-size: 1.1rem;
    }
    .invoice-total .total-row span:first-child {
      margin-right: 20px;
      color: #666;
    }
    .invoice-total .total-row.grand-total {
      font-size: 1.5rem;
      font-weight: bold;
      color: #667eea;
      margin-top: 15px;
    }
    .invoice-footer {
      text-align: center;
      margin-top: 40px;
      padding-top: 30px;
      border-top: 2px solid #e0e0e0;
      color: #888;
    }
    .invoice-footer p {
      margin: 8px 0;
    }
    .btn-group {
      display: flex;
      gap: 15px;
      justify-content: center;
      margin-top: 30px;
    }
    .btn {
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
    }
    .btn-secondary {
      background: #fff;
      color: #667eea;
      border: 2px solid #667eea;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .status-badge {
      display: inline-block;
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      background: #fef3c7;
      color: #f59e0b;
    }
  </style>
</head>
<body>
  <div class="invoice-container">
    <div class="invoice-header">
      <h1>🚗 HÓA ĐƠN BÁN XE</h1>
      <p>3 Boys Auto - Showroom Ô Tô Cao Cấp</p>
      <p>📍 163/8 Đường Thành Thái, Quận 10, TP.HCM</p>
      <p>📞 0799429767 | 📧 tdinh2753@gmail.com</p>
    </div>
    
    <div class="invoice-body">
      <div class="invoice-info">
        <div class="info-section">
          <h3>Thông Tin Hóa Đơn</h3>
          <p><strong>Mã HĐ:</strong> <span id="invoiceId">-</span></p>
          <p><strong>Ngày lập:</strong> <span id="invoiceDate">-</span></p>
          <p><strong>Trạng thái:</strong> <span class="status-badge" id="invoiceStatus">Đang xử lý</span></p>
        </div>
        
        <div class="info-section">
          <h3>Thông Tin Khách Hàng</h3>
          <p><strong>Họ tên:</strong> <span id="customerName">-</span></p>
          <p><strong>Điện thoại:</strong> <span id="customerPhone">-</span></p>
          <p><strong>Địa chỉ:</strong> <span id="customerAddress">-</span></p>
          <p><strong>Thanh toán:</strong> <span id="paymentMethod">-</span></p>
        </div>
      </div>
      
      <div class="invoice-items">
        <h3>Chi Tiết Sản Phẩm</h3>
        <table class="items-table">
          <thead>
            <tr>
              <th>Hình ảnh</th>
              <th>Tên xe</th>
              <th>Đơn giá</th>
              <th>Số lượng</th>
              <th>Thành tiền</th>
            </tr>
          </thead>
          <tbody id="invoiceItems">
            <!-- Items will be loaded here -->
          </tbody>
        </table>
      </div>
      
      <div class="invoice-total">
        <div class="total-row">
          <span>Tạm tính:</span>
          <span id="subtotal">0 VNĐ</span>
        </div>
        <div class="total-row grand-total">
          <span>Tổng cộng:</span>
          <span id="grandTotal">0 VNĐ</span>
        </div>
      </div>
      
      <div class="invoice-footer">
        <p><strong>Cảm ơn quý khách đã tin tưởng và lựa chọn 3 Boys Auto!</strong></p>
        <p>Mọi thắc mắc vui lòng liên hệ hotline: <strong>0799429767</strong></p>
        <div class="btn-group">
          <a href="cart.php" class="btn btn-secondary">← Quay lại giỏ hàng</a>
          <a href="../../index.php" class="btn btn-primary">Về trang chủ</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', async function() {
      // Fetch orders from API
      let orders = [];
      const userEmail = localStorage.getItem('userEmail');
      if (userEmail) {
          try {
              const response = await fetch(BASE_URL + '/BackEnd/api/get_orders.php?email=' + encodeURIComponent(userEmail), {
                  credentials: 'include'
              });
              const data = await response.json();
              orders = data.success ? data.orders : [];
          } catch (err) {
              console.error('Error fetching orders:', err);
              orders = [];
          }
      }
      
      if (orders.length === 0) {
        alert('Không có hóa đơn nào!');
        window.location.href = 'cart.php';
        return;
      }
      
      // Lấy đơn hàng mới nhất
      const order = orders[orders.length - 1];
      
      // Hiển thị thông tin hóa đơn
      document.getElementById('invoiceId').textContent = `INV-${order.id}`;
      document.getElementById('invoiceDate').textContent = order.date;
      
      // Trạng thái
      const statusBadge = document.getElementById('invoiceStatus');
      if (order.status === 'pending' || !order.status) {
        statusBadge.textContent = 'Đang xử lý';
        statusBadge.style.background = '#fef3c7';
        statusBadge.style.color = '#f59e0b';
      } else if (order.status === 'delivered') {
        statusBadge.textContent = 'Đã giao';
        statusBadge.style.background = '#d1fae5';
        statusBadge.style.color = '#10b981';
      } else if (order.status === 'cancelled') {
        statusBadge.textContent = 'Đã hủy';
        statusBadge.style.background = '#fee2e2';
        statusBadge.style.color = '#ef4444';
      }
      
      // Thông tin khách hàng
      document.getElementById('customerName').textContent = order.name;
      document.getElementById('customerPhone').textContent = order.phone;
      document.getElementById('customerAddress').textContent = order.address;
      
      const paymentText = order.payment === 'cod' ? 'Tiền mặt khi nhận hàng' : 
                         order.payment === 'bank' ? 'Chuyển khoản ngân hàng' : 
                         'Thanh toán trực tuyến';
      document.getElementById('paymentMethod').textContent = paymentText;
      
      // Hiển thị sản phẩm
      const itemsBody = document.getElementById('invoiceItems');
      let itemsHTML = '';
      order.items.forEach(item => {
        const lineTotal = item.price * item.quantity;
        itemsHTML += `
          <tr>
            <td><img src="${item.img}" alt="${item.name}" class="item-img"></td>
            <td><strong>${item.name}</strong></td>
            <td>${item.price.toLocaleString('vi-VN')} VNĐ</td>
            <td>${item.quantity}</td>
            <td><strong>${lineTotal.toLocaleString('vi-VN')} VNĐ</strong></td>
          </tr>
        `;
      });
      itemsBody.innerHTML = itemsHTML;
      
      // Hiển thị tổng tiền
      document.getElementById('subtotal').textContent = order.total.toLocaleString('vi-VN') + ' VNĐ';
      document.getElementById('grandTotal').textContent = order.total.toLocaleString('vi-VN') + ' VNĐ';
    });
  </script>
</body>
</html>

