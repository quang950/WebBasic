<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Giỏ hàng</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="/WebBasic/FrontEnd/assets/js/config.js"></script>
  <style>
    .cart-container {
      max-width: 900px;
      margin: 50px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .cart-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
      vertical-align: middle;
    }
    th {
      background-color: #007bff;
      color: #fff;
    }
    .remove-btn {
      background: #dc3545;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .remove-btn:hover {
      background: #a71d2a;
    }
    td img {
      width: 100px;
      border-radius: 8px;
    }
    .total {
      margin-top: 20px;
      text-align: right;
      font-size: 1.2rem;
      font-weight: bold;
    }
    .back-link {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #007bff;
    }
    .back-link:hover {
      text-decoration: underline;
    }
    .checkout-bar {
      margin-top: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .checkout-btn {
      background: #28a745;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: transform 0.1s ease, box-shadow 0.2s ease;
    }
    .checkout-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(0,0,0,0.12); }
  </style>
</head>
<body>
  <script>
    // Session check is done server-side, no need for client-side localStorage check
  </script>
  
  <div class="cart-container">
    <h2>Giỏ hàng của bạn</h2>
    <table id="cart-table">
      <thead>
        <tr>
          <th>Tên xe</th>
          <th>Giá</th>
          <th>Số lượng</th>
        </tr>
      </thead>
      <tbody id="cart-body">
      </tbody>
    </table>
    <div class="checkout-bar">
      <a href="../../index.php" class="back-link">← Tiếp tục mua sắm</a>
      <button id="ordersBtn" onclick="window.location.href='orders.php'; return false;" style="background:#007bff;color:#fff;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;">Xem đơn hàng đã mua</button>
      <div style="display:flex;align-items:center;gap:16px;">
        <div class="total" id="cart-total" style="font-size:1.3rem;color:#333;">Tổng: 0 VNĐ</div>
        <button id="showOrderFormBtn" style="background:#28a745;color:#fff;border:none;padding:12px 24px;border-radius:6px;font-weight:600;cursor:pointer;font-size:1.05rem;">Đặt hàng</button>
      </div>
    </div>
    <form id="orderForm" style="margin-top:32px;background:#fff;padding:32px 28px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.08);max-width:700px;margin-left:auto;margin-right:auto;display:none;">
      <h3 style="margin-bottom:24px;text-align:center;color:#333;font-size:1.5rem;"><i class="fas fa-shipping-fast" style="color:#007bff;margin-right:8px;"></i>Thông tin nhận hàng</h3>
      
      <!-- Chọn địa chỉ - Dạng danh sách hoặc toggle -->
      <div id="addressSelectionContainer" style="margin-bottom:24px;padding:16px;background:#f8f9fa;border-radius:8px;border-left:4px solid #007bff;">
        <div id="addressList" style="display:none;">
          <!-- Sẽ được load bằng JS -->
        </div>
        <div id="addressToggle" style="display:inline-flex;align-items:center;gap:32px;width:100%;">
          <!-- Fallback khi không load được danh sách -->
          <label style="display:inline-flex;align-items:center;cursor:pointer;">
            <input type="radio" name="addressType" value="account" checked style="margin-right:8px;width:18px;height:18px;cursor:pointer;">
            <span style="font-weight:600;color:#333;"><i class="fas fa-user" style="color:#007bff;margin-right:6px;"></i>Dùng địa chỉ từ tài khoản</span>
          </label>
          <label style="display:inline-flex;align-items:center;cursor:pointer;">
            <input type="radio" name="addressType" value="new" style="margin-right:8px;width:18px;height:18px;cursor:pointer;">
            <span style="font-weight:600;color:#333;"><i class="fas fa-edit" style="color:#28a745;margin-right:6px;"></i>Nhập địa chỉ mới</span>
          </label>
        </div>
      </div>
      
      <!-- Form địa chỉ từ tài khoản -->
      <div id="accountAddressFields" style="margin-bottom:24px;">
        <div style="margin-bottom:16px;">
          <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-user-circle" style="color:#007bff;margin-right:6px;"></i>Họ và tên <span style="color:red;">*</span></label>
          <input type="text" id="accountName" readonly style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;background:#f9f9f9;color:#666;font-size:0.95rem;">
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-phone" style="color:#007bff;margin-right:6px;"></i>Số điện thoại <span style="color:red;">*</span></label>
            <input type="tel" id="accountPhone" readonly style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;background:#f9f9f9;color:#666;font-size:0.95rem;">
          </div>
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-envelope" style="color:#007bff;margin-right:6px;"></i>Email</label>
            <input type="email" id="accountEmail" readonly style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;background:#f9f9f9;color:#666;font-size:0.95rem;">
          </div>
        </div>
        
        <div style="margin-bottom:16px;">
          <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-map-marker-alt" style="color:#007bff;margin-right:6px;"></i>Địa chỉ chi tiết <span style="color:red;">*</span></label>
          <input type="text" id="accountStreet" readonly placeholder="Số nhà, tên đường" style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;background:#f9f9f9;color:#666;font-size:0.95rem;">
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-city" style="color:#007bff;margin-right:6px;"></i>Phường/Xã</label>
            <input type="text" id="accountWard" readonly style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;background:#f9f9f9;color:#666;font-size:0.95rem;">
          </div>
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-building" style="color:#007bff;margin-right:6px;"></i>Quận/Huyện</label>
            <input type="text" id="accountDistrict" readonly style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;background:#f9f9f9;color:#666;font-size:0.95rem;">
          </div>
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-map" style="color:#007bff;margin-right:6px;"></i>Tỉnh/TP</label>
            <input type="text" id="accountProvince" readonly style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;background:#f9f9f9;color:#666;font-size:0.95rem;">
          </div>
        </div>
      </div>
      
      <!-- Form địa chỉ mới -->
      <div id="newAddressFields" style="margin-bottom:24px;display:none;">
        <div style="margin-bottom:16px;">
          <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-user-circle" style="color:#28a745;margin-right:6px;"></i>Họ và tên <span style="color:red;">*</span></label>
          <input type="text" id="newName" placeholder="Nhập họ và tên người nhận" required style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'">
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-phone" style="color:#28a745;margin-right:6px;"></i>Số điện thoại <span style="color:red;">*</span></label>
            <input type="tel" id="newPhone" placeholder="Ví dụ: 0901234567" required pattern="[0-9]{10}" style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'">
          </div>
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-envelope" style="color:#28a745;margin-right:6px;"></i>Email (tuỳ chọn)</label>
            <input type="email" id="newEmail" placeholder="email@example.com" style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'">
          </div>
        </div>
        
        <div style="margin-bottom:16px;">
          <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-map-marker-alt" style="color:#28a745;margin-right:6px;"></i>Địa chỉ chi tiết <span style="color:red;">*</span></label>
          <input type="text" id="newStreet" placeholder="Số nhà, tên đường (Ví dụ: 123 Nguyễn Văn Linh)" required style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'">
        </div>
        
        <div style="padding-top:12px;border-top:2px solid #f0f0f0;margin-bottom:16px;">
          <p style="color:#666;font-size:0.9rem;margin:12px 0 16px 0;"><i class="fas fa-info-circle" style="color:#ff9800;"></i> <strong>Địa chỉ chi tiết (Phường/Xã, Quận/Huyện, Tỉnh/TP)</strong></p>
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px;">
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-city" style="color:#28a745;margin-right:6px;"></i>Phường/Xã <span style="color:red;">*</span></label>
            <input type="text" id="newWard" placeholder="Phường/Xã" required style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'">
          </div>
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-building" style="color:#28a745;margin-right:6px;"></i>Quận/Huyện <span style="color:red;">*</span></label>
            <input type="text" id="newDistrict" placeholder="Quận/Huyện" required style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'">
          </div>
          <div>
            <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-map" style="color:#28a745;margin-right:6px;"></i>Tỉnh/TP <span style="color:red;">*</span></label>
            <select id="newProvince" required style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'">
              <option value="">-- Chọn Tỉnh/TP --</option>
              <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
              <option value="Hà Nội">Hà Nội</option>
              <option value="Đà Nẵng">Đà Nẵng</option>
              <option value="Cần Thơ">Cần Thơ</option>
              <option value="Hải Phòng">Hải Phòng</option>
              <option value="Bình Dương">Bình Dương</option>
              <option value="Đồng Nai">Đồng Nai</option>
              <option value="Khánh Hòa">Khánh Hòa</option>
              <option value="Lâm Đồng">Lâm Đồng</option>
              <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
            </select>
          </div>
        </div>
        
        <div style="margin-bottom:16px;">
          <label style="display:block;margin-bottom:6px;font-weight:600;color:#555;"><i class="fas fa-sticky-note" style="color:#28a745;margin-right:6px;"></i>Ghi chú (tuỳ chọn)</label>
          <textarea id="newNote" placeholder="Ghi chú thêm cho người giao hàng..." rows="3" style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #ddd;font-size:0.95rem;resize:vertical;transition:border 0.3s;" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#ddd'"></textarea>
        </div>
      </div>
      
      <!-- Phương thức thanh toán -->
      <div style="margin-bottom:24px;padding:16px;background:#f8f9fa;border-radius:8px;border-left:4px solid #ffc107;">
        <h4 style="margin-bottom:12px;color:#333;font-size:1.1rem;"><i class="fas fa-credit-card" style="color:#ffc107;margin-right:8px;"></i>Phương thức thanh toán</h4>
        <label style="display:block;margin-bottom:10px;cursor:pointer;padding:8px;border-radius:6px;transition:background 0.2s;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
          <input type="radio" name="paymentType" value="cod" checked style="margin-right:10px;width:16px;height:16px;cursor:pointer;">
          <i class="fas fa-money-bill-wave" style="color:#28a745;margin-right:6px;"></i>
          <span style="font-weight:600;">Thanh toán khi nhận hàng (COD)</span>
        </label>
        <label style="display:block;margin-bottom:10px;cursor:pointer;padding:8px;border-radius:6px;transition:background 0.2s;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
          <input type="radio" name="paymentType" value="bank" style="margin-right:10px;width:16px;height:16px;cursor:pointer;">
          <i class="fas fa-university" style="color:#007bff;margin-right:6px;"></i>
          <span style="font-weight:600;">Chuyển khoản ngân hàng</span>
        </label>
        <label style="display:block;cursor:pointer;padding:8px;border-radius:6px;transition:background 0.2s;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
          <input type="radio" name="paymentType" value="online" style="margin-right:10px;width:16px;height:16px;cursor:pointer;">
          <i class="fas fa-wallet" style="color:#17a2b8;margin-right:6px;"></i>
          <span style="font-weight:600;">Thanh toán trực tuyến (VNPay, Momo...)</span>
        </label>
      </div>
      
      <button type="submit" class="checkout-btn" style="width:100%;padding:14px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:1.1rem;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 4px 8px rgba(0,0,0,0.15);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 12px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'">
        <i class="fas fa-check-circle" style="margin-right:8px;"></i>Xác nhận đặt hàng
      </button>
    </form>
  </div>

  <script src="../../assets/js/main.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Load giỏ hàng từ main.js
      loadCart();
      
      // Auto-refresh cart every 5 seconds (in case user adds from other page/modal)
      let cartRefreshInterval = setInterval(function() {
        loadCart();
      }, 5000);
      
      // Also refresh when page regains focus (user switches tabs)
      window.addEventListener('focus', function() {
        loadCart();
      });
      
      // Load danh sách địa chỉ đã lưu
      loadSavedAddresses();
      
      // Clean up interval when page unloads
      window.addEventListener('beforeunload', function() {
        clearInterval(cartRefreshInterval);
      });
      
      // Hàm load danh sách địa chỉ từ user_shipping_addresses
      async function loadSavedAddresses() {
        try {
          const userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}');
          const userId = userInfo.id;
          const baseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '/WebBasic';
          
          if (!userId) {
            console.log('No user ID, using fallback');
            showAddressToggle();
            return;
          }
          
          const response = await fetch(baseUrl + '/BackEnd/api/get_shipping_addresses.php?user_id=' + userId, {
            method: 'GET',
            credentials: 'include'
          });
          
          if (!response.ok) {
            console.log('API error status:', response.status);
            showAddressToggle();
            return;
          }
          
          const data = await response.json();
          
          if (data.success && data.data && data.data.length > 0) {
            // Có danh sách địa chỉ - hiển thị dạng radio list
            renderAddressList(data.data);
          } else {
            // Không có danh sách - hiển thị toggle account/new
            console.log('No addresses found, using fallback');
            showAddressToggle();
          }
        } catch (error) {
          console.log('Load addresses error:', error);
          // Fallback: hiển thị toggle
          showAddressToggle();
        }
      }
      
      // Hiển thị danh sách địa chỉ dưới dạng radio options
      function renderAddressList(addresses) {
        const addressList = document.getElementById('addressList');
        const addressToggle = document.getElementById('addressToggle');
        
        let html = '<div style="margin-bottom:12px;"><p style="color:#666;font-size:0.9rem;margin:0 0 12px 0;font-weight:600;"><i class="fas fa-map-marker-alt" style="color:#007bff;"></i> Chọn địa chỉ:</p></div>';
        
        // Thêm option "Dùng địa chỉ từ tài khoản" ở đầu
        const userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}');
        const fullName = userInfo.name || `${userInfo.firstName || ''} ${userInfo.lastName || ''}`.trim();
        const addressParts = String(userInfo.address || '').split(',').map(v => v.trim());
        const accountFullAddr = `${addressParts[0] || ''}, ${addressParts[1] || ''}, ${addressParts[2] || ''}, ${addressParts[3] || userInfo.province || ''}`;
        
        if (fullName && accountFullAddr.trim() !== ',,,') {
          html += `
            <label style="display:block;margin-bottom:10px;cursor:pointer;padding:10px;border-radius:6px;border:1px solid #ddd;background:#e7f3ff;transition:background 0.2s;" onmouseover="this.style.background='#cde4f0'" onmouseout="this.style.background='#e7f3ff'">
              <input type="radio" name="addressType" value="account" checked style="margin-right:10px;width:16px;height:16px;cursor:pointer;" onchange="handleAddressSelection(this)">
              <span style="font-weight:600;color:#0066cc;"><i class="fas fa-user-circle" style="margin-right:6px;"></i>Dùng địa chỉ từ tài khoản</span><br>
              <span style="color:#666;font-size:0.9rem;margin-left:26px;">${accountFullAddr}</span>
            </label>
          `;
        }
        
        // Thêm danh sách saved addresses
        addresses.forEach((addr, index) => {
          const isDefault = addr.is_default ? ' (Mặc định)' : '';
          const fullAddr = `${addr.address_detail}, ${addr.ward}, ${addr.district}, ${addr.province}`;
          
          html += `
            <label style="display:block;margin-bottom:10px;cursor:pointer;padding:10px;border-radius:6px;border:1px solid #ddd;transition:background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='#fff'">
              <input type="radio" name="addressType" value="saved" data-address-id="${addr.id}" style="margin-right:10px;width:16px;height:16px;cursor:pointer;" onchange="handleAddressSelection(this)">
              <span style="font-weight:600;color:#333;">${addr.recipient_name}${isDefault}</span> - ${addr.phone}<br>
              <span style="color:#666;font-size:0.9rem;margin-left:26px;">${fullAddr}</span>
            </label>
          `;
        });
        
        // Thêm option "Thêm địa chỉ mới"
        html += `
          <label style="display:block;margin-top:16px;padding-top:16px;border-top:1px solid #ddd;cursor:pointer;padding:10px;border-radius:6px;transition:background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='#fff'">
            <input type="radio" name="addressType" value="new" style="margin-right:10px;width:16px;height:16px;cursor:pointer;" onchange="handleAddressSelection(this)">
            <span style="font-weight:600;color:#28a745;"><i class="fas fa-plus-circle" style="margin-right:6px;"></i>Thêm địa chỉ mới</span>
          </label>
        `;
        
        addressList.innerHTML = html;
        addressList.style.display = 'block';
        addressToggle.style.display = 'none';
      }
      
      // Fallback: Hiển thị toggle account/new (khi không có danh sách)
      function showAddressToggle() {
        const addressList = document.getElementById('addressList');
        const addressToggle = document.getElementById('addressToggle');
        
        addressList.style.display = 'none';
        addressToggle.style.display = 'inline-flex';
      }
      
      // Xử lý khi người dùng chọn địa chỉ
      window.handleAddressSelection = function(radio) {
        const addressType = radio.value;
        const addressId = radio.dataset.addressId;
        
        if (addressType === 'saved' && addressId) {
          // Hiển thị thông tin địa chỉ từ API
          loadAddressDetails(addressId);
        } else if (addressType === 'new') {
          // Hiển thị form nhập địa chỉ mới
          document.getElementById('accountAddressFields').style.display = 'none';
          document.getElementById('newAddressFields').style.display = 'block';
        } else if (addressType === 'account') {
          // Hiển thị thông tin từ tài khoản
          document.getElementById('newAddressFields').style.display = 'none';
          document.getElementById('accountAddressFields').style.display = 'block';
          loadAccountAddress();
        }
      };
      
      // Load thông tin chi tiết của địa chỉ đã lưu
      async function loadAddressDetails(addressId) {
        try {
          const userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}');
          const baseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '/WebBasic';
          
          const response = await fetch(baseUrl + '/BackEnd/api/get_shipping_addresses.php?user_id=' + userInfo.id, {
            method: 'GET',
            credentials: 'include'
          });
          
          const data = await response.json();
          
          if (data.success && data.data) {
            const addr = data.data.find(a => a.id == addressId);
            if (addr) {
              // Hiển thị thông tin địa chỉ trong readonly fields
              displaySavedAddressFields(addr);
            }
          }
        } catch (error) {
          console.log('Load address details error:', error);
        }
      }
      
      // Hiển thị thông tin của địa chỉ đã lưu (readonly)
      function displaySavedAddressFields(addr) {
        // Ẩn cả hai form
        document.getElementById('accountAddressFields').style.display = 'none';
        document.getElementById('newAddressFields').style.display = 'none';
        
        // Hiển thị container cho địa chỉ đã lưu (dùng account fields nhưng giấu)
        document.getElementById('accountAddressFields').style.display = 'block';
        
        // Điền thông tin vào address fields
        document.getElementById('accountName').value = addr.recipient_name || '';
        document.getElementById('accountPhone').value = addr.phone || '';
        document.getElementById('accountEmail').value = '';
        document.getElementById('accountStreet').value = addr.address_detail || '';
        document.getElementById('accountWard').value = addr.ward || '';
        document.getElementById('accountDistrict').value = addr.district || '';
        document.getElementById('accountProvince').value = addr.province || '';
      }
      
      // Xử lý nút "Đặt hàng" - hiển thị form thông tin nhận hàng
      document.getElementById('showOrderFormBtn').onclick = function(e) { 
        e.preventDefault();
        const orderForm = document.getElementById('orderForm');
        
        // Toggle hiển thị form
        if (orderForm.style.display === 'none' || orderForm.style.display === '') {
          orderForm.style.display = 'block';
          // Scroll xuống form
          orderForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
          // Load lại địa chỉ
          loadAccountAddress();
        } else {
          orderForm.style.display = 'none';
        }
        
        return false; 
      };
      
      document.getElementById('ordersBtn').onclick = function(e) { 
        window.location.href='orders.php'; 
        return false; 
      };
      
      // Xử lý submit form đặt hàng
      var orderForm = document.getElementById('orderForm');
      if (orderForm) {
        orderForm.onsubmit = function(e){ 
          e.preventDefault();
          e.stopPropagation();
          
          try {
            // Lấy thông tin từ form
            const addressType = document.querySelector('input[name="addressType"]:checked').value;
            const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
            
            let receiverName, receiverPhone, receiverEmail, receiverAddress;
            
            if (addressType === 'saved' || addressType === 'account') {
              // Dùng địa chỉ đã lưu hoặc từ tài khoản
              receiverName = document.getElementById('accountName').value?.trim();
              receiverPhone = document.getElementById('accountPhone').value?.trim();
              receiverEmail = document.getElementById('accountEmail').value?.trim();
              const street = document.getElementById('accountStreet').value?.trim();
              const ward = document.getElementById('accountWard').value?.trim();
              const district = document.getElementById('accountDistrict').value?.trim();
              const province = document.getElementById('accountProvince').value?.trim();
              
              // Validate address fields
              if (!receiverName || !receiverPhone || !street || !ward || !district || !province) {
                alert('Vui lòng điền đầy đủ thông tin địa chỉ!');
                return false;
              }
              
              receiverAddress = `${street}, ${ward}, ${district}, ${province}`;
            } else if (addressType === 'new') {
              // Nhập địa chỉ mới
              receiverName = document.getElementById('newName').value;
              receiverPhone = document.getElementById('newPhone').value;
              receiverEmail = document.getElementById('newEmail').value || 'N/A';
              const street = document.getElementById('newStreet').value;
              const ward = document.getElementById('newWard').value;
              const district = document.getElementById('newDistrict').value;
              const province = document.getElementById('newProvince').value;
              
              // Validate required fields for new address
              if (!receiverName || !receiverPhone || !street || !ward || !district || !province) {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
                return false;
              }
              
              receiverAddress = `${street}, ${ward}, ${district}, ${province}`;
            }
            
            // Lấy tên phương thức thanh toán
            let paymentMethod = 'Thanh toán khi nhận hàng (COD)';
            if (paymentType === 'bank') {
              paymentMethod = 'Chuyển khoản ngân hàng';
            } else if (paymentType === 'online') {
              paymentMethod = 'Thanh toán trực tuyến (VNPay, Momo...)';
            }
            
            // Validate receiver info
            if (!receiverAddress || !receiverPhone || !paymentType) {
              alert('Vui lòng điền đầy đủ thông tin đơn hàng!');
              return false;
            }
            
            // Fetch cart từ database API thay vì localStorage
            const baseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '/WebBasic';
            
            fetch(baseUrl + '/BackEnd/api/cart.php?action=get', {
              method: 'GET',
              credentials: 'include'
            })
            .then(response => response.json())
            .then(cartData => {
              // Kiểm tra giỏ hàng từ API
              if (!cartData.success || !cartData.data || cartData.data.length === 0) {
                alert('Giỏ hàng trống! Vui lòng thêm sản phẩm trước khi đặt hàng.');
                return;
              }

              // Map cart items từ API format tới order format
              const cartItems = cartData.data.map(item => ({
                product_id: Number(item.product_id) || 0,
                name: String(item.name || ''),
                price: Number(item.price) || 0,
                quantity: Math.max(1, Math.floor(Number(item.quantity) || 1)),
                unit_price: Number(item.price) || 0
              }));

              // Tính tổng tiền
              const totalPrice = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

              // Tạo đối tượng gửi tới backend (format cho create_order.php)
              const backendOrderData = {
                receiver_name: receiverName,
                shipping_address: receiverAddress,
                shipping_phone: receiverPhone,
                payment_method: paymentType,
                cart_items: cartItems
              };

              console.log('Sending order data:', backendOrderData);

              // Tạo đối tượng đơn hàng cho hiển thị trên trang xác nhận
              const orderData = {
                orderId: 'DH' + Date.now().toString().slice(-6),
                orderDate: new Date().toLocaleString('vi-VN'),
                receiverName: receiverName,
                receiverPhone: receiverPhone,
                receiverEmail: receiverEmail,
                receiverAddress: receiverAddress,
                paymentMethod: paymentMethod,
                products: cartItems,
                totalPrice: totalPrice
              };

              // Gửi request tới backend API để lưu order vào database
              submitOrderToBackend(backendOrderData, orderData, baseUrl);
            })
            .catch(error => {
              console.error('Error fetching cart:', error);
              alert('Lỗi tải giỏ hàng: ' + error.message);
            });
            
            return false;
          } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra: ' + error.message);
            return false;
          }
        };
      }
      
      // Hàm gửi đơn hàng tới backend
      function submitOrderToBackend(backendOrderData, orderData, baseUrl) {
        fetch(baseUrl + '/BackEnd/api/create_order.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          credentials: 'include',
          body: JSON.stringify(backendOrderData)
        })
            .then(response => {
              if (!response.ok) {
                return response.json().catch(() => ({ 
                  success: false, 
                  message: 'HTTP ' + response.status + ' ' + response.statusText 
                })).then(data => {
                  throw new Error(data.message || 'HTTP ' + response.status);
                });
              }
              return response.json();
            })
            .then(result => {
              if (result.success) {
                // Lưu vào sessionStorage để trang xác nhận đọc
                sessionStorage.setItem('currentOrder', JSON.stringify(orderData));
                
                // Chuyển sang trang xác nhận
                window.location.replace('order-confirmation.php');
              } else {
                alert('Lỗi đặt hàng: ' + (result.message || 'Unknown error'));
              }
            })
            .catch(error => {
              console.error('Order Error Details:', error);
              alert('Có lỗi xảy ra khi lưu đơn hàng: ' + error.message);
            });
      }
      
      // Load địa chỉ từ tài khoản người dùng
      function loadAccountAddress() {
        const userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}');
        if (!userInfo || !userInfo.email) {
          console.log('Chưa đăng nhập');
          return;
        }

        const fullName = userInfo.name || `${userInfo.firstName || ''} ${userInfo.lastName || ''}`.trim();
        const addressParts = String(userInfo.address || '').split(',').map(v => v.trim());

        document.getElementById('accountName').value = fullName || 'N/A';
        document.getElementById('accountPhone').value = userInfo.phone || 'N/A';
        document.getElementById('accountEmail').value = userInfo.email || 'N/A';
        document.getElementById('accountStreet').value = addressParts[0] || 'N/A';
        document.getElementById('accountWard').value = addressParts[1] || 'N/A';
        document.getElementById('accountDistrict').value = addressParts[2] || 'N/A';
        document.getElementById('accountProvince').value = addressParts[3] || userInfo.province || 'N/A';
      }
      
      // Gọi hàm load địa chỉ khi trang load
      loadAccountAddress();
      
      // Toggle form địa chỉ - fallback listener cho trường hợp dùng toggle thay vì radio list
      document.querySelectorAll('input[name="addressType"]').forEach(radio => {
        radio.addEventListener('change', function() {
          // Chỉ áp dụng nếu đang dùng toggle (không phải radio list)
          if (document.getElementById('addressToggle').style.display !== 'none') {
            // Lấy các trường required trong form địa chỉ mới
            const newNameField = document.getElementById('newName');
            const newPhoneField = document.getElementById('newPhone');
            const newStreetField = document.getElementById('newStreet');
            const newWardField = document.getElementById('newWard');
            const newDistrictField = document.getElementById('newDistrict');
            const newProvinceField = document.getElementById('newProvince');
            
            if (this.value === 'account') {
              document.getElementById('accountAddressFields').style.display = 'block';
              document.getElementById('newAddressFields').style.display = 'none';
              
              // Bỏ required cho các trường địa chỉ mới
              newNameField.removeAttribute('required');
              newPhoneField.removeAttribute('required');
              newStreetField.removeAttribute('required');
              newWardField.removeAttribute('required');
              newDistrictField.removeAttribute('required');
              newProvinceField.removeAttribute('required');
            } else if (this.value === 'new') {
              document.getElementById('accountAddressFields').style.display = 'none';
              document.getElementById('newAddressFields').style.display = 'block';
              
              // Thêm required cho các trường địa chỉ mới
              newNameField.setAttribute('required', 'required');
              newPhoneField.setAttribute('required', 'required');
              newStreetField.setAttribute('required', 'required');
              newWardField.setAttribute('required', 'required');
              newDistrictField.setAttribute('required', 'required');
              newProvinceField.setAttribute('required', 'required');
            }
          }
        });
      });
      
      // Khởi tạo: bỏ required cho các trường địa chỉ mới vì mặc định chọn tài khoản
      document.getElementById('newName').removeAttribute('required');
      document.getElementById('newPhone').removeAttribute('required');
      document.getElementById('newStreet').removeAttribute('required');
      document.getElementById('newWard').removeAttribute('required');
      document.getElementById('newDistrict').removeAttribute('required');
      document.getElementById('newProvince').removeAttribute('required');
    });
  </script>
</body>
</html>
