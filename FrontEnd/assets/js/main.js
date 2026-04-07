// ========================================
// PROTOTYPE: Chỉ giữ login/logout logic
// Tất cả tính năng khác đã bị tắt/chuyển thành static HTML
// ========================================

// Dynamic base path (works at root and inside pages/ subfolders)
// Always use /WebBasic/ as the base path for absolute URLs
const _basePath = '/WebBasic/';

document.addEventListener('DOMContentLoaded', () => {
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
                const btn = document.createElement('button');
                btn.className = 'admin-return-btn blob-btn login-btn';
                btn.innerHTML = '<span class="blob-btn__inner"><span class="blob-btn__blobs"><span class="blob-btn__blob"></span><span class="blob-btn__blob"></span><span class="blob-btn__blob"></span><span class="blob-btn__blob"></span></span></span>Quay lại Admin';
                btn.onclick = (e) => {
                    e.preventDefault();
                    window.location.href = '/WebBasic/FrontEnd/pages/admin/admin-themsanpham.php';
                };
                actions.insertBefore(btn, actions.firstChild);
            }
            const cartIcon = document.querySelector('.cart-icon');
            if (cartIcon) cartIcon.style.display = 'none';
        }
    } catch (e) {}

    // Cập nhật số lượng giỏ hàng trên navbar khi load trang
    updateCartCount();
    bindBuyButtons();

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

function isUserLoggedIn() {
    return localStorage.getItem('userLoggedIn') === 'true';
}

function getCartItems() {
    const cart = JSON.parse(localStorage.getItem('cart'));
    return Array.isArray(cart) ? cart : [];
}

function saveCartItems(cartItems) {
    localStorage.setItem('cart', JSON.stringify(cartItems));
    updateCartCount();
}

function extractCarDataFromCard(card) {
    if (!card) return null;
    const name = (card.querySelector('h3')?.textContent || '').trim();
    const priceText = card.querySelector('.price')?.textContent || '';
    const price = parseCurrencyToNumber(priceText);
    const img = card.querySelector('img')?.getAttribute('src') || '';
    // Get product ID from data attribute (data-id or data-product-id)
    const productId = card.getAttribute('data-id') || card.getAttribute('data-product-id') || '';

    if (!name || !price) {
        return null;
    }

    return {
        name,
        price,
        img,
        product_id: productId,
        quantity: 1
    };
}

function bindBuyButtons() {
    // Dùng capture để vẫn bắt được click kể cả khi inline onclick="return false;"
    document.addEventListener('click', function (e) {
        const buyBtn = e.target.closest('.buy-btn');
        if (!buyBtn) return;

        const card = buyBtn.closest('.car-card');
        if (!card) return;

        e.preventDefault();
        const car = extractCarDataFromCard(card);
        if (!car) {
            showToast('Không lấy được thông tin sản phẩm', 'error');
            return;
        }

        addToCart(car.name, car.price, car.img, 1, car.product_id);
    }, true);
}

// Flag để debounce rapid clicks trên buy button
let isAddingToCart = false;

// Hàm thêm vào giỏ hàng
function addToCart(name, price, img, quantity = 1, product_id = '') {
    // Nếu đang xử lý thêm vào giỏ, bỏ qua
    if (isAddingToCart) {
        return false;
    }
    
    if (!isUserLoggedIn()) {
        isAddingToCart = true;
        showLoginRequiredModal();
        // Reset flag sau 300ms để cho phép click tiếp theo
        setTimeout(() => { isAddingToCart = false; }, 300);
        return false;
    }

    const cart = getCartItems();
    // Try to find by product_id first, then by name
    const idx = product_id ? cart.findIndex(item => item.product_id === product_id) : 
                            cart.findIndex(item => item.name === name);
    const qty = Math.max(1, Number(quantity) || 1);

    if (idx >= 0) {
        cart[idx].quantity = (Number(cart[idx].quantity) || 0) + qty;
    } else {
        cart.push({
            name,
            price: Number(price) || 0,
            img: img || '',
            product_id: product_id || '',
            quantity: qty
        });
    }

    saveCartItems(cart);
    showToast('Đã thêm vào giỏ hàng', 'success');
    return false;
}

// Hàm xóa sản phẩm trong giỏ
function removeFromCart(index) {
    const cart = getCartItems();
    if (!cart[index]) return false;

    cart.splice(index, 1);
    saveCartItems(cart);
    loadCart();
    showToast('Đã xóa sản phẩm khỏi giỏ', 'success');
    return false;
}

// Hàm tăng/giảm số lượng
function changeQuantity(index, delta) {
    const stored = getCartItems();
    if (!stored[index]) {
        return false;
    }

    const nextQty = (Number(stored[index].quantity) || 0) + Number(delta || 0);
    if (nextQty <= 0) {
        stored.splice(index, 1);
    } else {
        stored[index].quantity = nextQty;
    }

    saveCartItems(stored);
    loadCart();
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

// Hàm load giỏ hàng (dùng trong cart.php) - chỉ hiển thị, controls disabled
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

// Flag để tránh show modal nhiều lần khi click nhanh
let isLoginModalVisible = false;
let isRedirectingToLogin = false;

// Modal yêu cầu đăng nhập
function showLoginRequiredModal() {
    // Nếu modal đã hiển thị hoặc đang redirect, không show lại
    if (isLoginModalVisible || isRedirectingToLogin) {
        return;
    }
    
    // Nếu modal đã tồn tại trong DOM, không tạo lại
    if (document.getElementById('loginRequiredModal')) {
        return;
    }
    
    isLoginModalVisible = true;
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
    isLoginModalVisible = false;
}

function goToLogin() {
    // Set flag để ngăn chặn các click tiếp theo
    isRedirectingToLogin = true;
    closeLoginRequiredModal();
    window.location.href = _basePath + 'pages/user/login.php';
}

// Kiểm tra đăng nhập trước khi vào giỏ hàng
function checkLoginAndGoToCart() {
    // Nếu đang redirect, bỏ qua
    if (isRedirectingToLogin) {
        return;
    }
    
    const isLoggedIn = localStorage.getItem('userLoggedIn') === 'true';
    if (!isLoggedIn) {
        showLoginRequiredModal();
    } else {
        isRedirectingToLogin = true;
        window.location.href = _basePath + 'pages/user/cart.php';
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

