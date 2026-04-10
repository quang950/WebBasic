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

    // Admin view mode được quản lý bằng PHP conditional rendering trong navbar (xem index.php)

    // Cập nhật số lượng giỏ hàng trên navbar khi load trang
    updateCartCount();
    bindBuyButtons();

    // Nếu đang ở trang giỏ hàng thì load dữ liệu
    if (document.getElementById('cart-body')) {
        loadCart();
    }

    syncDisplayedPricesFromDatabase();
});

let priceApiBasePromise = null;
const productLookupCache = new Map();

function formatDbPrice(value) {
    try {
        return new Intl.NumberFormat('vi-VN').format(Number(value) || 0) + ' VNĐ';
    } catch (e) {
        return (Number(value) || 0) + ' VNĐ';
    }
}

function normalizePriceKey(text) {
    return String(text || '')
        .toLowerCase()
        .replace(/\b(2024|2025|2026|2023|2022|2021|2020)\b/g, '')
        .replace(/[^a-z0-9\u00c0-\u1ef9]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

async function resolvePriceApiBase() {
    if (priceApiBasePromise) return priceApiBasePromise;

    priceApiBasePromise = (async () => {
        // Use BASE_URL if available (from config.js)
        if (typeof BASE_URL !== 'undefined' && BASE_URL) {
            return BASE_URL + '/BackEnd/api';
        }

        const origin = window.location.origin;
        return `${origin}/WebBasic/BackEnd/api`;
    })();

    return priceApiBasePromise;
}

function buildPriceSearchTerm(text) {
    return normalizePriceKey(text)
        .replace(/\b(toyota|mercedes|bmw|audi|lexus|honda|hyundai|kia|vinfast|maybach|benz)\b/g, '')
        .replace(/\s+/g, ' ')
        .trim();
}

async function fetchProductBySearchTerm(searchTerm) {
    const normalizedTerm = buildPriceSearchTerm(searchTerm);
    if (!normalizedTerm) return null;

    if (productLookupCache.has(normalizedTerm)) {
        return productLookupCache.get(normalizedTerm);
    }

    const promise = (async () => {
        const apiBase = await resolvePriceApiBase();
        const response = await fetch(`${apiBase}/products.php?name=${encodeURIComponent(normalizedTerm)}&limit=20`);
        const data = await response.json();

        if (!response.ok || !data.success || !Array.isArray(data.data) || data.data.length === 0) {
            return null;
        }

        const lowerTerm = normalizedTerm.toLowerCase();
        return data.data.find((product) => normalizePriceKey(product.name).includes(lowerTerm)) || data.data[0] || null;
    })();

    productLookupCache.set(normalizedTerm, promise);
    return promise;
}

async function syncDisplayedPricesFromDatabase() {
    try {
        document.querySelectorAll('.car-card').forEach((card) => {
            const priceNode = card.querySelector('.price');
            const titleNode = card.querySelector('h3');
            if (!priceNode || !titleNode) return;

            fetchProductBySearchTerm(titleNode.textContent).then((matchedProduct) => {
                if (matchedProduct && Number(matchedProduct.price) > 0) {
                    priceNode.textContent = formatDbPrice(matchedProduct.price);
                }
            });
        });

        document.querySelectorAll('.hero-slide').forEach((slide) => {
            const priceNode = slide.querySelector('.slide-price strong');
            if (!priceNode) return;

            const lookupText = [
                slide.querySelector('.slide-model')?.textContent || '',
                slide.querySelector('.slide-name')?.textContent || ''
            ].join(' ');

            fetchProductBySearchTerm(lookupText).then((matchedProduct) => {
                if (matchedProduct && Number(matchedProduct.price) > 0) {
                    priceNode.textContent = formatDbPrice(matchedProduct.price);
                }
            });
        });
    } catch (error) {
        console.warn('[main] Khong dong bo duoc gia tu DB:', error.message);
    }
}

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
    // Use BASE_URL if available (from config.js)
    const apiBase = (typeof BASE_URL !== 'undefined' && BASE_URL) 
        ? BASE_URL + '/BackEnd/api'
        : '/WebBasic/BackEnd/api';
    
    fetch(apiBase + '/cart.php?action=get', {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const totalItems = data.data.reduce((sum, item) => sum + (item.quantity || 1), 0);
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = totalItems;
            }
        }
    })
    .catch(error => {
        console.error('Error updating cart count:', error);
    });
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

// Cart operations now use API only - NO localStorage
// Get cart from API instead of localStorage
async function getCartItems() {
    const apiBase = (typeof BASE_URL !== 'undefined' && BASE_URL) 
        ? BASE_URL + '/BackEnd/api'
        : '/WebBasic/BackEnd/api';
    
    try {
        const response = await fetch(apiBase + '/cart.php', {
            method: 'GET',
            credentials: 'include'
        });
        const data = await response.json();
        return (data.success && Array.isArray(data.data)) ? data.data : [];
    } catch (error) {
        console.error('Error fetching cart:', error);
        return [];
    }
}

// Cart saved automatically to server via API - no localStorage needed
function saveCartItems(cartItems) {
    // Cart is persisted on server automatically, just update UI
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

    // Call API to add to cart instead of just localStorage
    isAddingToCart = true;
    
    // Use BASE_URL if available (from config.js)
    const apiBase = (typeof BASE_URL !== 'undefined' && BASE_URL) 
        ? BASE_URL + '/BackEnd/api'
        : '/WebBasic/BackEnd/api';
    
    fetch(apiBase + '/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: Number(product_id) || 0,
            quantity: Math.max(1, Number(quantity) || 1)
        }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showToast('Đã thêm vào giỏ hàng', 'success');
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể thêm vào giỏ hàng'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Lỗi kết nối khi thêm vào giỏ hàng');
    })
    .finally(() => {
        isAddingToCart = false;
    });
    
    return false;
}

// Hàm xóa sản phẩm trong giỏ
function removeFromCart(cartId) {
    // Use API to remove from database
    const baseUrl = localStorage.getItem('baseUrl') || '/WebBasic';
    
    fetch(baseUrl + '/BackEnd/api/remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_id: cartId
        }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCart();
            showToast('Đã xóa sản phẩm khỏi giỏ', 'success');
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể xóa sản phẩm'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Lỗi khi xóa sản phẩm');
    });
    return false;
}

// Hàm tăng/giảm số lượng
function changeQuantity(cartId, delta) {
    const baseUrl = localStorage.getItem('baseUrl') || '/WebBasic';
    
    // Use API to update quantity in database
    fetch(baseUrl + '/BackEnd/api/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_id: cartId,
            delta: Number(delta)
        }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCart();
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể cập nhật số lượng'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Lỗi khi cập nhật số lượng');
    });
    return false;
}

// Hàm load giỏ hàng không cập nhật tổng (prototype mode)
async function loadCartWithoutTotalUpdate() {
    const tbody = document.getElementById('cart-body');
    const totalEl = document.getElementById('cart-total');
    
    // Load cart from API, not localStorage
    const cartItems = await getCartItems();

    if (!tbody) return;

    if (cartItems.length === 0) {
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

    if (!tbody) return;

    // Fetch cart from API instead of localStorage
    const baseUrl = localStorage.getItem('baseUrl') || '/WebBasic';
    
    fetch(baseUrl + '/BackEnd/api/cart.php?action=get', {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success || !data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">Giỏ hàng trống</td></tr>';
            if (totalEl) totalEl.textContent = 'Tổng cộng: 0 VNĐ';
            updateCartCount();
            return;
        }

        let rows = '';
        let total = 0;
        data.data.forEach((item, idx) => {
            const lineTotal = (Number(item.price) || 0) * (Number(item.quantity) || 0);
            total += lineTotal;
            const itemId = item.id; // cart.id từ database
            rows += `
                <tr>
                    <td>${item.name}</td>
                    <td>${formatCurrency(item.price)}</td>
                    <td>
                        <button class="remove-btn" onclick="changeQuantity(${itemId}, -1)">-</button>
                        <span style="display:inline-block;min-width:32px;text-align:center">${item.quantity}</span>
                        <button class="remove-btn" style="background:#28a745" onclick="changeQuantity(${itemId}, 1)">+</button>
                        <button class="remove-btn" style="margin-left:8px" onclick="removeFromCart(${itemId})">Xóa</button>
                    </td>
                </tr>`;
        });
        tbody.innerHTML = rows;
        if (totalEl) totalEl.textContent = `Tổng cộng: ${formatCurrency(total)}`;
        updateCartCount();
    })
    .catch(error => {
        console.error('Error loading cart:', error);
        tbody.innerHTML = '<tr><td colspan="3">Lỗi tải giỏ hàng</td></tr>';
    });
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
    window.location.href = _basePath + 'FrontEnd/pages/user/login.php';
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

