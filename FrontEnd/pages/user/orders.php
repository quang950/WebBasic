<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đơn hàng đã đặt</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="/WebBasic/FrontEnd/assets/js/config.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f7fa;
    }
    
    .orders-container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0;
    }
    
    .orders-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 40px 24px;
      border-radius: 12px 12px 0 0;
      text-align: center;
    }
    
    .orders-header h2 {
      margin: 0;
      font-size: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }
    
    .orders-content {
      background: white;
      border-radius: 0 0 12px 12px;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-bottom: 32px;
      flex-wrap: wrap;
    }
    
    .action-buttons a {
      padding: 12px 32px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .action-buttons a:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-home {
      background: #007bff;
      color: white;
    }
    
    .btn-home:hover {
      background: #0056b3;
    }
    
    .btn-cart {
      background: #28a745;
      color: white;
    }
    
    .btn-cart:hover {
      background: #218838;
    }
    
    .order-card {
      background: #f8f9fa;
      border-radius: 12px;
      margin-bottom: 24px;
      padding: 24px;
      border-left: 4px solid #667eea;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      transition: all 0.3s ease;
    }
    
    .order-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .order-header-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
      padding-bottom: 16px;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .order-header-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .order-header-item strong {
      color: #333;
      font-weight: 600;
    }
    
    .order-header-item span {
      color: #666;
    }
    
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    .status-new {
      background: #e3f2fd;
      color: #1565c0;
    }
    
    .status-processing {
      background: #fff3e0;
      color: #e65100;
    }
    
    .status-delivered {
      background: #e8f5e9;
      color: #2e7d32;
    }
    
    .status-cancelled {
      background: #ffebee;
      color: #c62828;
    }
    
    .order-shipping-info {
      background: #e7f3ff;
      padding: 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      border-left: 4px solid #2196f3;
    }
    
    .shipping-label {
      font-weight: 600;
      color: #0066cc;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .shipping-address {
      color: #333;
      line-height: 1.6;
      margin-bottom: 8px;
    }
    
    .shipping-phone {
      color: #666;
      font-size: 0.95rem;
    }
    
    .order-items {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      overflow: hidden;
    }
    
    .order-items thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    
    .order-items th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
    }
    
    .order-items td {
      padding: 12px;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .order-items tbody tr:last-child td {
      border-bottom: none;
    }
    
    .order-items .product-name {
      font-weight: 600;
      color: #333;
    }
    
    .order-items .price {
      color: #667eea;
      font-weight: 600;
      text-align: right;
    }
    
    .order-items .quantity {
      text-align: center;
      font-weight: 600;
    }
    
    .order-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 16px;
      border-top: 2px solid #e0e0e0;
    }
    
    .order-payment {
      color: #666;
      font-size: 0.95rem;
    }
    
    .order-total {
      font-size: 1.3rem;
      font-weight: 700;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 24px;
      color: #999;
    }
    
    .empty-state i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 16px;
    }
    
    .empty-state p {
      font-size: 1.1rem;
      margin-bottom: 24px;
    }
    
    .empty-state a {
      display: inline-block;
      padding: 12px 32px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .empty-state a:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="orders-container">
    <div class="orders-header">
      <h2>
        <i class="fas fa-receipt"></i> Đơn hàng của tôi
      </h2>
    </div>
    
    <div class="orders-content">
      <div class="action-buttons">
        <a href="../../index.php" class="btn-home">
          <i class="fas fa-home"></i> Về trang chủ
        </a>
        <a href="cart.php" class="btn-cart">
          <i class="fas fa-shopping-cart"></i> Về giỏ hàng
        </a>
      </div>
      
      <div id="ordersList"></div>
    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      const ordersList = document.getElementById('ordersList');

      fetch('../../../BackEnd/api/get_orders.php', {
          credentials: 'include' 
      })
      .then(res => res.json())
      .then(data => {
          if (!data.success || !data.orders || data.orders.length === 0) {
              ordersList.innerHTML = `
                  <div class="empty-state">
                      <i class="fas fa-inbox"></i>
                      <p>Bạn chưa có đơn hàng nào</p>
                      <a href="../../index.php">
                        <i class="fas fa-arrow-right" style="margin-right: 8px;"></i>
                        Tiếp tục mua sắm
                      </a>
                  </div>
              `;
              return;
          }

          let html = '';
          data.orders.forEach(order => {
              const statusMap = {
                  'new': { label: 'Mới nhận', icon: 'fas fa-clock', class: 'status-new' },
                  'processing': { label: 'Đang xử lý', icon: 'fas fa-cog', class: 'status-processing' },
                  'delivered': { label: 'Đã giao', icon: 'fas fa-check-circle', class: 'status-delivered' },
                  'cancelled': { label: 'Đã hủy', icon: 'fas fa-times-circle', class: 'status-cancelled' }
              };
              const statusInfo = statusMap[order.status] || { label: order.status, icon: 'fas fa-info-circle', class: 'status-new' };
              
              html += `<div class="order-card">`;
              
              // Order header with ID, Date, and Status
              html += `<div class="order-header-row">
                          <div class="order-header-item">
                              <i class="fas fa-tag"></i>
                              <strong>Mã đơn:</strong>
                              <span>DH-${order.id}</span>
                          </div>
                          <div class="order-header-item">
                              <i class="fas fa-calendar"></i>
                              <strong>Ngày đặt:</strong>
                              <span>${new Date(order.created_at).toLocaleDateString('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' })}</span>
                          </div>
                          <div class="order-header-item" style="justify-content: flex-end;">
                              <span class="status-badge ${statusInfo.class}">
                                  <i class="${statusInfo.icon}"></i>
                                  ${statusInfo.label}
                              </span>
                          </div>
                      </div>`;
              
              // Shipping and payment info
              html += `<div class="order-shipping-info">
                          <div class="shipping-label">
                              <i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng
                          </div>
                          <div class="shipping-address">
                              ${order.shipping_address || 'Chưa có thông tin'}
                          </div>
                          <div class="shipping-phone">
                              <i class="fas fa-phone"></i> ${order.shipping_phone || 'Chưa có thông tin'}
                          </div>
                          <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(0,0,0,0.1);">
                              <strong style="color: #0066cc;">Phương thức:</strong> ${order.payment_method || 'Chưa xác định'}
                          </div>
                      </div>`;
              
              // Products table
              html += `<table class="order-items">
                          <thead>
                            <tr>
                              <th style="width: 50%;">Tên sản phẩm</th>
                              <th style="width: 15%;">Số lượng</th>
                              <th style="width: 17%;">Đơn giá</th>
                              <th style="width: 18%;">Thành tiền</th>
                            </tr>
                          </thead>
                          <tbody>`;

              let orderTotal = 0;
              order.items.forEach(item => {
                  const itemTotal = item.unit_price * item.quantity;
                  orderTotal += itemTotal;

                  html += `
                      <tr>
                          <td>
                              <div class="product-name">
                                  <i class="fas fa-car" style="color: #667eea; margin-right: 8px;"></i>
                                  ${item.product_name}
                              </div>
                          </td>
                          <td class="quantity">${item.quantity}</td>
                          <td class="price">${formatPrice(item.unit_price)}</td>
                          <td class="price">${formatPrice(itemTotal)}</td>
                      </tr>
                  `;
              });

              html += `</tbody></table>`;
              
              // Order footer with totals
              html += `<div class="order-footer">
                          <div class="order-payment">
                              <i class="fas fa-credit-card"></i> ${order.payment_method}
                          </div>
                          <div class="order-total">
                              Tổng: ${formatPrice(orderTotal)}
                          </div>
                      </div>`;
              
              html += `</div>`;
          });

          ordersList.innerHTML = html;
      })
      .catch(err => {
          console.error(err);
          ordersList.innerHTML = `
              <div class="empty-state">
                  <i class="fas fa-exclamation-circle"></i>
                  <p>Lỗi khi tải đơn hàng</p>
                  <a href="../../index.php">Quay lại trang chủ</a>
              </div>
          `;
      });
      
      // Helper function to format price
      function formatPrice(price) {
          return new Intl.NumberFormat('vi-VN', {
              style: 'currency',
              currency: 'VND'
          }).format(price);
      }
  });
</script>
</body>
</html>

