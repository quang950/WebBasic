<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đơn hàng đã đặt</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    .orders-container { max-width: 900px; margin: 50px auto; padding: 24px; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    h2 { text-align:center; margin-bottom:24px; }
    .order-card { background:#f8f9fa; border-radius:8px; margin-bottom:24px; padding:18px 16px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    .order-header { font-weight:600; margin-bottom:8px; }
    .order-items th, .order-items td { padding:8px 12px; text-align:center; }
    .order-items th { background:#007bff; color:#fff; }
    .order-items { width:100%; border-collapse:collapse; margin-bottom:8px; }
    .order-total { text-align:right; font-weight:600; font-size:1.1rem; margin-top:8px; }
  </style>
</head>
<body>
  <div class="orders-container">
    <h2>Đơn hàng đã đặt</h2>
    <div style="display:flex;justify-content:center;gap:24px;margin-bottom:32px;">
      <a href="../../index.php" style="padding:12px 32px;background:#007bff;color:#fff;border-radius:8px;font-weight:600;text-decoration:none;box-shadow:0 2px 8px rgba(0,123,255,0.08);transition:background 0.2s;">Về trang chủ</a>
      <a href="cart.php" style="padding:12px 32px;background:#28a745;color:#fff;border-radius:8px;font-weight:600;text-decoration:none;box-shadow:0 2px 8px rgba(40,167,69,0.08);transition:background 0.2s;">Về giỏ hàng</a>
    </div>
    <div id="ordersList"></div>
  </div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
      const ordersList = document.getElementById('ordersList');

      fetch('/WebBasic/BackEnd/api/get_orders.php', {
          credentials: 'include' 
      })
      .then(res => res.json())
      .then(data => {
          if (!data.success || !data.orders || data.orders.length === 0) {
              ordersList.innerHTML = `
                  <div style="text-align:center;padding:40px;color:#999;">
                      <p style="font-size:1.1rem;">Bạn chưa có đơn hàng nào</p>
                  </div>
              `;
              return;
          }

          let html = '';
          data.orders.forEach(order => {
              html += `<div class="order-card">`;
              html += `<div class="order-header">
                          Mã đơn: ${order.id} | Ngày đặt: ${order.created_at}
                      </div>`;
              html += `<div><strong>Thanh toán:</strong> ${order.payment_method}</div>`;

              html += `<table class="order-items">
                          <thead>
                            <tr>
                              <th>Tên xe</th>
                              <th>Giá</th>
                              <th>Số lượng</th>
                              <th>Tổng</th>
                            </tr>
                          </thead>
                          <tbody>`;

              let orderTotal = 0;
              order.items.forEach(item => {
                  const itemTotal = item.unit_price * item.quantity;
                  orderTotal += itemTotal;

                  html += `
                      <tr>
                          <td>${item.product_name}</td>
                          <td>${item.unit_price.toLocaleString('vi-VN')} VNĐ</td>
                          <td>${item.quantity}</td>
                          <td>${itemTotal.toLocaleString('vi-VN')} VNĐ</td>
                      </tr>
                  `;
              });

              html += `</tbody></table>`;
              html += `<div class="order-total">
                          Tổng cộng: ${orderTotal.toLocaleString('vi-VN')} VNĐ
                      </div>`;
              html += `</div>`;
          });

          ordersList.innerHTML = html;
      })
      .catch(err => {
          console.error(err);
          ordersList.innerHTML = `
              <div style="text-align:center;padding:40px;color:#999;">
                  <p style="font-size:1.1rem;">Lỗi tải đơn hàng</p>
              </div>
          `;
      });
  });
</script>
</body>
</html>

