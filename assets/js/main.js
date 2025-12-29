// ========================================
// PROTOTYPE: Chỉ giữ login/logout logic
// Tất cả tính năng khác đã bị tắt/chuyển thành static HTML
// ========================================

document.addEventListener('DOMContentLoaded', () => {
    // Seed giỏ hàng với 1 xe mặc định (để hiển thị)
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (!cart || cart.length === 0) {
        cart = [{
            name: 'Toyota Camry',
            price: 1235000000,
            img: 'assets/images/toyota-camry.jpg',
            quantity: 1
        }];
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    // Hiển thị avatar trên navbar nếu có
    try {
        const email = localStorage.getItem('userEmail');
        let users = JSON.parse(localStorage.getItem('users')) || [];
        const user = users.find(u => u.email === email);
        if (user && user.avatar) {
            const userAvatar = document.getElementById('userAvatar');
            if (userAvatar) userAvatar.src = user.avatar;
        }
    } catch (e) {}

    // Nếu admin đang đăng nhập, hiển thị nút quay lại Admin trên navbar
    try {
        const isAdmin = localStorage.getItem('adminLoggedIn') === 'true';
        const actions = document.querySelector('.user-actions');
        if (isAdmin) {
            if (actions && !actions.querySelector('.admin-return-btn')) {
                const btn = document.createElement('a');
                btn.href = 'admin-themsanpham.html';
                btn.className = 'admin-return-btn blob-btn login-btn';
                btn.innerHTML = '<span class="blob-btn__inner"><span class="blob-btn__blobs"><span class="blob-btn__blob"></span><span class="blob-btn__blob"></span><span class="blob-btn__blob"></span><span class="blob-btn__blob"></span></span></span>Quay lại Admin';
                actions.insertBefore(btn, actions.firstChild);
            }
            const cartIcon = document.querySelector('.cart-icon');
            if (cartIcon) cartIcon.style.display = 'none';
        }
    } catch (e) {}

    // Cập nhật số lượng giỏ hàng trên navbar khi load trang
    updateCartCount();

    // Nếu đang ở trang giỏ hàng thì load dữ liệu
    if (document.getElementById('cart-body')) {
        loadCart();
    }
});

// ========================================
// HELPER FUNCTIONS (cần thiết cho hiển thị)
// ========================================

// Định dạng tiền VNĐ
function formatCurrency(value) {
    try {
        return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
    } catch (e) {
        return value + ' VNĐ';
    }
}

// Parse chuỗi tiền tệ thành số
function parseCurrencyToNumber(text) {
    if (!text) return 0;
    return Number(String(text).replace(/[^0-9]/g, '')) || 0;
}

// Cập nhật badge số lượng giỏ hàng
function updateCartCount() {
    const stored = JSON.parse(localStorage.getItem('cart')) || [];
    const count = stored.reduce((sum, it) => sum + (Number(it.quantity) || 0), 0);
    const badge = document.querySelector('.cart-count');
    if (badge) badge.textContent = count;
}

// Toast thông báo
function getToastContainer() {
    let c = document.getElementById('toast-container');
    if (!c) {
        c = document.createElement('div');
        c.id = 'toast-container';
        document.body.appendChild(c);
    }
    return c;
}

function showToast(message, type = 'success') {
    const container = getToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 220);
    }, 1800);
}

// ========================================
// CART FUNCTIONS (chỉ để hiển thị, không cho phép thay đổi)
// ========================================

// Hàm thêm vào giỏ hàng  TẮT (prototype)
function addToCart(name, price, img) {
    // No-op: click has no effect in prototype
    return false;
}

// Hàm xóa sản phẩm trong giỏ (prototype mode: chỉ thông báo, không xóa thật)
function removeFromCart(index) {
    console.log('removeFromCart called for index:', index);
    return false;
}

// Hàm tăng/giảm số lượng (prototype mode: chỉ thay đổi số lượng hiển thị, không ảnh hưởng giá)
function changeQuantity(index, delta) {
    console.log('changeQuantity called:', index, delta);
    const stored = JSON.parse(localStorage.getItem('cart')) || [];
    if (!stored[index]) {
        console.log('Item not found at index:', index);
        return false;
    }
    
    // Thay đổi số lượng trong localStorage
    const oldQty = stored[index].quantity;
    stored[index].quantity = Math.max(1, stored[index].quantity + delta);
    console.log('Quantity changed from', oldQty, 'to', stored[index].quantity);
    localStorage.setItem('cart', JSON.stringify(stored));
    
    // Chỉ cập nhật số lượng hiển thị, không reload toàn bộ table (tránh rung)
    const quantitySpan = document.querySelector(`span[data-quantity-index="${index}"]`);
    if (quantitySpan) {
        quantitySpan.textContent = stored[index].quantity;
    }
    updateCartCount();
    
    return false;
}

// Hàm load giỏ hàng không cập nhật tổng (prototype mode)
function loadCartWithoutTotalUpdate() {
    const tbody = document.getElementById('cart-body');
    const totalEl = document.getElementById('cart-total');
    const stored = JSON.parse(localStorage.getItem('cart')) || [];

    if (!tbody) return;

    if (stored.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3">Giỏ hàng trống</td></tr>';
        if (totalEl) totalEl.textContent = 'Tổng cộng: 0 VNĐ';
        updateCartCount();
        return;
    }

    // Giữ nguyên giá tổng hiện tại
    const currentTotal = totalEl ? totalEl.textContent : 'Tổng cộng: 0 VNĐ';
    
    let rows = '';
    stored.forEach((item, idx) => {
        rows += `
            <tr>
                <td>${item.name}</td>
                <td>${formatCurrency(item.price)}</td>
                <td>
                    <button class="remove-btn" onclick="changeQuantity(${idx}, -1)">-</button>
                    <span style="display:inline-block;min-width:32px;text-align:center" data-quantity-index="${idx}">${item.quantity}</span>
                    <button class="remove-btn" style="background:#28a745" onclick="changeQuantity(${idx}, 1)">+</button>
                    <button class="remove-btn" style="margin-left:8px" onclick="removeFromCart(${idx})">Xóa</button>
                </td>
            </tr>`;
    });
    tbody.innerHTML = rows;
    
    // Giữ nguyên tổng tiền (không cập nhật)
    if (totalEl) totalEl.textContent = currentTotal;
    updateCartCount();
}

// Hàm load giỏ hàng (dùng trong cart.html) - chỉ hiển thị, controls disabled
function loadCart() {
    const tbody = document.getElementById('cart-body');
    const totalEl = document.getElementById('cart-total');
    const stored = JSON.parse(localStorage.getItem('cart')) || [];

    if (!tbody) return;

    if (stored.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3">Giỏ hàng trống</td></tr>';
        if (totalEl) totalEl.textContent = 'Tổng cộng: 0 VNĐ';
        updateCartCount();
        return;
    }

    let rows = '';
    let total = 0;
    stored.forEach((item, idx) => {
        const lineTotal = (Number(item.price) || 0) * (Number(item.quantity) || 0);
        total += lineTotal;
        rows += `
            <tr>
                <td>${item.name}</td>
                <td>${formatCurrency(item.price)}</td>
                <td>
                    <button class="remove-btn" onclick="changeQuantity(${idx}, -1)">-</button>
                    <span style="display:inline-block;min-width:32px;text-align:center" data-quantity-index="${idx}">${item.quantity}</span>
                    <button class="remove-btn" style="background:#28a745" onclick="changeQuantity(${idx}, 1)">+</button>
                    <button class="remove-btn" style="margin-left:8px" onclick="removeFromCart(${idx})">Xóa</button>
                </td>
            </tr>`;
    });
    tbody.innerHTML = rows;
    if (totalEl) totalEl.textContent = `Tổng cộng: ${formatCurrency(total)}`;
    updateCartCount();
}

// ========================================
// LOGIN/LOGOUT RELATED FUNCTIONS
// ========================================

// Modal yêu cầu đăng nhập
function showLoginRequiredModal() {
    const modalHTML = `
        <div id="loginRequiredModal" class="modal" style="display: block;">
            <div class="modal-content" style="max-width: 400px; text-align: center;">
                <div class="modal-header">
                    <h2><i class="fas fa-lock"></i> Yêu cầu đăng nhập</h2>
                    <span class="close" onclick="closeLoginRequiredModal()">&times;</span>
                </div>
                <div class="modal-body" style="padding: 30px 20px;">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #4CAF50; margin-bottom: 20px;"></i>
                    <p style="font-size: 18px; margin-bottom: 25px; color: rgba(255,255,255,0.9);">
                        Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng
                    </p>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button onclick="goToLogin()" class="login-required-btn primary">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </button>
                        <button onclick="closeLoginRequiredModal()" class="login-required-btn secondary">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.getElementById('loginRequiredModal').addEventListener('click', function(e) {
        if (e.target === this) closeLoginRequiredModal();
    });
}

function closeLoginRequiredModal() {
    const modal = document.getElementById('loginRequiredModal');
    if (modal) modal.remove();
}

function goToLogin() {
    closeLoginRequiredModal();
    window.location.href = 'login.html';
}

// Kiểm tra đăng nhập trước khi vào giỏ hàng
function checkLoginAndGoToCart() {
    const isLoggedIn = localStorage.getItem('userLoggedIn') === 'true';
    if (!isLoggedIn) {
        showLoginRequiredModal();
    } else {
        window.location.href = 'cart.html';
    }
}

// ========================================
// EXPOSE FUNCTIONS TO GLOBAL SCOPE
// (để inline onclick trong HTML không bị lỗi ReferenceError)
// ========================================
window.checkLoginAndGoToCart = checkLoginAndGoToCart;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.changeQuantity = changeQuantity;
window.showLoginRequiredModal = showLoginRequiredModal;
window.closeLoginRequiredModal = closeLoginRequiredModal;
window.goToLogin = goToLogin;

// ===== CHỨC NĂNG XEM CHI TIẾT XE =====
function showCarDetails(button) {
  const card = button.closest('.car-card');
  if (!card) return;
  
  const img = card.querySelector('img').src;
  const title = card.querySelector('h3').textContent;
  const price = card.querySelector('.price').textContent;
  const origin = card.dataset.origin || 'Không rõ';
  const year = card.dataset.year || 'Không rõ';
  const fuel = card.dataset.fuel || 'Không rõ';
  const seats = card.dataset.seats || 'Không rõ';
  const transmission = card.dataset.transmission || 'Không rõ';
  const engine = card.dataset.engine || 'Không rõ';
  const desc = card.dataset.desc || 'Không có mô tả';
  
  // Cập nhật modal
  document.getElementById('modalImg').src = img;
  document.getElementById('modalTitle').textContent = title;
  document.getElementById('modalPrice').textContent = price;
  
  const detailsHTML = `
    <div style="text-align: left; margin: 20px 0;">
      <p><strong>📍 Xuất xứ:</strong> ${origin}</p>
      <p><strong>📅 Năm sản xuất:</strong> ${year}</p>
      <p><strong>⛽ Nhiên liệu:</strong> ${fuel}</p>
      <p><strong>💺 Số ghế:</strong> ${seats}</p>
      <p><strong>⚙️ Hộp số:</strong> ${transmission}</p>
      <p><strong>🔧 Động cơ:</strong> ${engine}</p>
      <p><strong>📝 Mô tả:</strong> ${desc}</p>
    </div>
  `;
  
  document.getElementById('modalDesc').innerHTML = detailsHTML;
  
  // Hiển thị modal
  const modal = document.getElementById('carModal');
  modal.style.display = 'flex';
  
  return false;
}

// Đóng modal khi click vào nút X
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('carModal');
  const closeBtn = modal?.querySelector('.close-btn');
  
  if (closeBtn) {
    closeBtn.onclick = function() {
      modal.style.display = 'none';
    };
  }
  
  // Đóng modal khi click bên ngoài
  window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  };
  
  // Gắn sự kiện cho tất cả nút "Chi tiết"
  document.querySelectorAll('.view-details').forEach(link => {
    link.onclick = function(e) {
      e.preventDefault();
      showCarDetails(this);
      return false;
    };
  });
});

window.showCarDetails = showCarDetails;
