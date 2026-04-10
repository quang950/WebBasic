// Toast notification - DO NOT show toast with redirect (commented out redirect logic)
// Use the unified showToast from search-results.js instead

function formatPrice(price) {
  return new Intl.NumberFormat("vi-VN").format(Number(price) || 0);
}

// Add to cart from search/brand pages - calls API instead of localStorage
function addToCartFromSearch(productId, productName, productPrice) {
  // Check login FIRST
  if (!isUserLoggedIn()) {
    showLoginRequiredModal();
    return false;
  }

  // Use BASE_URL if available (from config.js)
  const apiBase = (typeof BASE_URL !== 'undefined' && BASE_URL) 
    ? BASE_URL + '/BackEnd/api'
    : '/WebBasic/BackEnd/api';
  
  // If productId is text (like "Camry"), search for product by name first
  if (isNaN(productId)) {
    // Search for product by name to get the ID
    fetch(apiBase + '/products.php?name=' + encodeURIComponent(productName), {
      method: 'GET',
      credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success && data.data && data.data.length > 0) {
        // Use first matching product's ID
        const actualId = data.data[0].id;
        addToCartWithId(actualId, productPrice);
      } else {
        alert("Không tìm thấy sản phẩm: " + productName);
      }
    })
    .catch(error => {
      console.error('Error searching product:', error);
      alert("Lỗi tìm kiếm sản phẩm");
    });
  } else {
    // productId is already a number
    addToCartWithId(Number(productId), productPrice);
  }
  
  return false;
}

// Helper function to add to cart with known product ID
function addToCartWithId(productId, productPrice) {
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
      product_id: productId,
      quantity: 1
    }),
    credentials: 'include'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      updateCartCount();
      showToast("Đã thêm vào giỏ hàng!");
    } else {
      // Nếu chưa đăng nhập, redirect tới login page
      if (data.message && data.message.includes('đăng nhập')) {
        alert("Vui lòng đăng nhập để mua hàng!");
        window.location.href = BASE_URL + '/FrontEnd/pages/user/login.php';
      } else {
        alert("Lỗi: " + (data.message || "Không thể thêm vào giỏ hàng"));
      }
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert("Lỗi khi thêm vào giỏ hàng");
  });
}

// Update cart count badge - NOW CALLS API INSTEAD OF LOCALSTORAGE
function updateCartCount() {
  const baseUrl = (typeof BASE_URL !== 'undefined' && BASE_URL) ? BASE_URL : '/WebBasic';
  
  fetch(baseUrl + '/BackEnd/api/cart.php?action=get', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
    credentials: 'include'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.data) {
      const totalItems = data.data.reduce((sum, item) => sum + (item.quantity || 1), 0);
      const cartBadge = document.querySelector(".cart-count");
      if (cartBadge) {
        cartBadge.textContent = totalItems;
      }
    }
  })
  .catch(error => {
    console.error('Error updating cart count:', error);
  });
}

// showToast - simple version without redirect
function showToast(message) {
  const container = document.getElementById('toast-container') || (() => {
    const c = document.createElement('div');
    c.id = 'toast-container';
    document.body.appendChild(c);
    return c;
  })();
  const toast = document.createElement('div');
  toast.className = `toast success`;
  toast.textContent = message;
  toast.style.cssText = `
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #28a745;
    color: white;
    padding: 15px 25px;
    border-radius: 5px;
    font-size: 14px;
    z-index: 99999;
    animation: slideDown 0.3s ease;
  `;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'slideUp 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 2000);
}

let apiBasePromise = null;

async function resolveApiBase() {
  if (apiBasePromise) return apiBasePromise;

  apiBasePromise = (async () => {
    // Use BASE_URL if available (from config.js)
    if (typeof BASE_URL !== 'undefined' && BASE_URL) {
      return BASE_URL + '/BackEnd/api';
    }

    const origin = window.location.origin;
    return `${origin}/WebBasic/BackEnd/api`;
  })();

  return apiBasePromise;
}

function buildPriceSearchTerm(text) {
  return String(text || "")
    .toLowerCase()
    .replace(/\b(2024|2025|2026|2023|2022|2021|2020)\b/g, "")
    .replace(/\b(toyota|mercedes|bmw|audi|lexus|honda|hyundai|kia|vinfast|maybach|benz)\b/g, "")
    .replace(/[^a-z0-9\u00c0-\u1ef9]+/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

async function fetchProductBySearchTerm(searchTerm) {
  const normalizedTerm = buildPriceSearchTerm(searchTerm);
  if (!normalizedTerm) return null;

  if (productLookupCache.has(normalizedTerm)) {
    return productLookupCache.get(normalizedTerm);
  }

  const promise = (async () => {
    const apiBase = await resolveApiBase();
    const response = await fetch(`${apiBase}/products.php?name=${encodeURIComponent(normalizedTerm)}&limit=20`);
    const data = await response.json();

    if (!response.ok || !data.success || !Array.isArray(data.data) || data.data.length === 0) {
      return null;
    }

    const lowerTerm = normalizedTerm.toLowerCase();
    return data.data.find((item) => buildPriceSearchTerm(item.name).includes(lowerTerm)) || data.data[0] || null;
  })();

  productLookupCache.set(normalizedTerm, promise);
  return promise;
}

async function syncCardPricesFromDatabase() {
  const cards = Array.from(document.querySelectorAll(".car-card"));
  if (cards.length === 0) return;

  try {
    await Promise.all(cards.map(async (card) => {
      const nameNode = card.querySelector("h3");
      const priceNode = card.querySelector(".price");

      if (!nameNode || !priceNode) return;

      const productName = buildPriceSearchTerm(nameNode.textContent.trim());
      if (!productName) return;

      const matchedProduct = await fetchProductBySearchTerm(productName);
      const updatedPrice = Number(matchedProduct?.price || 0);
      if (updatedPrice > 0) {
        priceNode.textContent = `${formatPrice(updatedPrice)} VNĐ`;
      }
    }));
  } catch (error) {
    console.warn("[brand-page] Khong dong bo duoc gia tu API:", error.message);
  }
}

// Kiểm tra trạng thái đăng nhập và hiển thị thông tin user/admin
function checkUserLoginStatus() {
  const isUserLoggedIn = localStorage.getItem("userLoggedIn") === "true";
  const isAdminLoggedIn = localStorage.getItem("adminLoggedIn") === "true";

  const userInfo = JSON.parse(localStorage.getItem("userInfo") || "{}");
  const adminInfo = JSON.parse(localStorage.getItem("adminInfo") || "{}");
  const adminUsername = localStorage.getItem("adminUsername") || "Admin";

  const loginBtn = document.getElementById("loginBtn");
  const userInfoDiv = document.getElementById("userInfo");

  // Nếu admin đã đăng nhập
  if (isAdminLoggedIn) {
    // Ẩn nút đăng nhập, hiển thị thông tin admin
    loginBtn.style.display = "none";
    userInfoDiv.style.display = "flex";

    // Cập nhật thông tin admin
    const displayName = adminInfo.name || adminUsername;
    document.getElementById("userName").textContent = displayName;
    document.getElementById("userAvatar").src =
      adminInfo.picture ||
      `https://ui-avatars.com/api/?name=${displayName}&background=dc3545&color=fff&size=35`;
  } else if (isUserLoggedIn && userInfo.name) {
    // Ẩn nút đăng nhập, hiển thị thông tin user
    loginBtn.style.display = "none";
    userInfoDiv.style.display = "flex";

    // Cập nhật thông tin user
    document.getElementById("userName").textContent = userInfo.name;
    document.getElementById("userAvatar").src =
      userInfo.picture ||
      `https://ui-avatars.com/api/?name=${userInfo.name}&background=007bff&color=fff&size=35`;
  } else {
    // Hiển thị nút đăng nhập, ẩn thông tin user
    loginBtn.style.display = "inline-block";
    userInfoDiv.style.display = "none";
  }
}

// Function đăng xuất
function logout() {
  const isAdmin = localStorage.getItem("adminLoggedIn") === "true";

  if (isAdmin) {
    // Đăng xuất admin
    localStorage.removeItem("adminLoggedIn");
    localStorage.removeItem("adminUsername");
    localStorage.removeItem("adminInfo");
    showToast("Admin đã đăng xuất thành công!");
  } else {
    // Đăng xuất user thường
    localStorage.removeItem("userLoggedIn");
    localStorage.removeItem("userEmail");
    localStorage.removeItem("userInfo");
    // Xóa giỏ hàng khi đăng xuất
    // Clear cart after order via API instead of localStorage
    const apiBase = (typeof BASE_URL !== 'undefined' && BASE_URL) 
      ? BASE_URL + '/BackEnd/api'
      : '/WebBasic/BackEnd/api';
    fetch(apiBase + '/clear_cart.php', {
      method: 'POST',
      credentials: 'include'
    }).catch(err => console.log('Cart cleared'));
    // Cập nhật badge giỏ hàng về 0
    const cartBadge = document.querySelector(".cart-count");
    if (cartBadge) cartBadge.textContent = "0";
    showToast("Đã đăng xuất thành công!");
  }

  checkUserLoginStatus();
}

function checkLoginAndGoToCart() {
  const isUserLoggedIn = localStorage.getItem("userLoggedIn") === "true";
  const isAdminLoggedIn = localStorage.getItem("adminLoggedIn") === "true";

  if (!isUserLoggedIn && !isAdminLoggedIn) {
    alert("Vui lòng đăng nhập để xem giỏ hàng!");
    window.location.href = BASE_URL + '/FrontEnd/pages/user/login.php';
    return false;
  }
  window.location.href = "../user/cart.php";
  return false;
}

// Gọi function kiểm tra khi trang load
document.addEventListener("DOMContentLoaded", function () {
  checkUserLoginStatus();
  initPagination();
  syncCardPricesFromDatabase();
});

// ===== PAGINATION =====
let currentPage = 1;
const itemsPerPage = 5;
let totalItems = 0;
let totalPages = 0;

function initPagination() {
  const carCards = document.querySelectorAll(".car-card");
  totalItems = carCards.length;
  totalPages = Math.ceil(totalItems / itemsPerPage);

  // Cập nhật UI
  const totalPagesElement = document.getElementById("totalPages");
  if (totalPagesElement) {
    totalPagesElement.textContent = totalPages;
  }

  // Hiển thị trang đầu tiên
  showPage(1);
}

function showPage(page) {
  currentPage = page;
  const carCards = document.querySelectorAll(".car-card");

  // Ẩn tất cả các card
  carCards.forEach((card) => {
    card.style.display = "none";
  });

  // Hiển thị card của trang hiện tại
  const startIndex = (page - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;

  for (let i = startIndex; i < endIndex && i < totalItems; i++) {
    carCards[i].style.display = "block";
  }

  // Cập nhật số trang hiện tại
  const currentPageElement = document.getElementById("currentPage");
  if (currentPageElement) {
    currentPageElement.textContent = currentPage;
  }

  // Cuộn lên đầu danh sách xe
  const carsSection = document.querySelector(".cars-grid");
  if (carsSection) {
    carsSection.scrollIntoView({ behavior: "smooth", block: "start" });
  }
}

function changePage(direction) {
  if (direction === "next" && currentPage < totalPages) {
    showPage(currentPage + 1);
  } else if (direction === "prev" && currentPage > 1) {
    showPage(currentPage - 1);
  }
}

// Expose functions to global scope
window.changePage = changePage;

