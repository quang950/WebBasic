// search-results.js - API-driven search and pagination

const state = {
  items: [],
  currentPage: 1,
  totalPages: 1,
  totalItems: 0,
  limit: 6,
  filters: {
    name: "",
    category: "",
    priceFrom: "",
    priceTo: "",
  },
  apiBase: null,
  productsData: {}, // Cache sản phẩm từ JSON để lấy thông tin chi tiết
};

// Load dữ liệu từ products.json để có đầy đủ thông tin chi tiết
async function loadProductsData() {
  try {
    const response = await fetch('../../DataBase/products.json');
    if (!response.ok) throw new Error('Cannot load products.json: ' + response.status);
    const data = await response.json();
    console.log('[SUCCESS] Loaded', data.length, 'products from JSON');
    data.forEach(product => {
      state.productsData[product.id] = product;
    });
    console.log('[SUCCESS] state.productsData now has keys:', Object.keys(state.productsData).slice(0, 10));
  } catch (err) {
    console.error('[ERROR] Failed to load products.json:', err.message);
    console.warn('[FALLBACK] Will load product details from API on demand');
  }
}

function bindEvents() {
  document.getElementById("prevPageBtn").addEventListener("click", () => {
    if (state.currentPage > 1) {
      fetchAndRender(state.currentPage - 1);
    }
  });

  document.getElementById("nextPageBtn").addEventListener("click", () => {
    if (state.currentPage < state.totalPages) {
      fetchAndRender(state.currentPage + 1);
    }
  });

  document.getElementById("applyFilterBtn").addEventListener("click", () => {
    state.filters.category = document.getElementById("filterBrand").value.trim();
    state.filters.priceFrom = document.getElementById("filterPriceFrom").value.trim();
    state.filters.priceTo = document.getElementById("filterPriceTo").value.trim();
    updateUrlQuery();
    fetchAndRender(1);
  });

  document.getElementById("resetFilterBtn").addEventListener("click", () => {
    state.filters = { ...state.filters, category: "", priceFrom: "", priceTo: "" };
    setFilterInputs();
    updateUrlQuery();
    fetchAndRender(1);
  });

  ["filterPriceFrom", "filterPriceTo"].forEach((id) => {
    document.getElementById(id).addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        document.getElementById("applyFilterBtn").click();
      }
    });
  });
}

function hydrateFiltersFromUrl() {
  const query = new URLSearchParams(window.location.search);
  state.filters.name = query.get("name") || "";
  state.filters.category = query.get("category") || query.get("brand") || "";
  state.filters.priceFrom = query.get("priceFrom") || query.get("minPrice") || "";
  state.filters.priceTo = query.get("priceTo") || query.get("maxPrice") || "";
}

function setFilterInputs() {
  document.getElementById("filterBrand").value = state.filters.category;
  document.getElementById("filterPriceFrom").value = state.filters.priceFrom;
  document.getElementById("filterPriceTo").value = state.filters.priceTo;
}

function updateUrlQuery() {
  const params = new URLSearchParams();
  if (state.filters.name) params.set("name", state.filters.name);
  if (state.filters.category) params.set("category", state.filters.category);
  if (state.filters.priceFrom) params.set("priceFrom", state.filters.priceFrom);
  if (state.filters.priceTo) params.set("priceTo", state.filters.priceTo);

  const newUrl = `${window.location.pathname}${params.toString() ? "?" + params.toString() : ""}`;
  window.history.replaceState({}, "", newUrl);
}

async function resolveApiBase() {
  if (state.apiBase) return state.apiBase;

  const origin = window.location.origin;
  const candidates = [
    `${origin}/WebBasic/BackEnd/api`,
    `${origin}/BackEnd/api`,
    `${window.location.protocol}//${window.location.hostname}:8000/BackEnd/api`,
    "http://localhost:8000/BackEnd/api",
    "http://127.0.0.1:8000/BackEnd/api",
  ];

  for (const base of candidates) {
    try {
      const response = await fetch(`${base}/test.php`);
      const text = await response.text();
      if (response.ok && text.includes('"test"')) {
        state.apiBase = base;
        return base;
      }
    } catch (_err) {
      // Continue trying next base URL
    }
  }

  throw new Error("Khong tim thay API backend. Hay chay PHP server tren cong 8000.");
}

function buildSearchParams(page) {
  const params = new URLSearchParams();
  params.set("page", String(page));
  params.set("limit", String(state.limit));

  if (state.filters.name) params.set("name", state.filters.name);
  if (state.filters.category) params.set("category", state.filters.category);
  if (state.filters.priceFrom) params.set("priceFrom", state.filters.priceFrom);
  if (state.filters.priceTo) params.set("priceTo", state.filters.priceTo);

  return params;
}

async function fetchAndRender(page) {
  try {
    const apiBase = await resolveApiBase();
    const hasAdvancedFilter =
      !!state.filters.name || !!state.filters.category || !!state.filters.priceFrom || !!state.filters.priceTo;

    const endpoint = hasAdvancedFilter ? "search.php" : "products.php";
    const params = buildSearchParams(page);
    const url = `${apiBase}/${endpoint}?${params.toString()}`;

    const response = await fetch(url);
    const data = await response.json();

    if (!response.ok || !data.success) {
      throw new Error(data.message || "Khong lay duoc du lieu san pham");
    }

    state.items = Array.isArray(data.data) ? data.data : [];
    state.currentPage = data.pagination?.page || page;
    state.totalPages = data.pagination?.totalPages || 1;
    state.totalItems = data.pagination?.totalItems || 0;

    renderSearchSummary();
    renderProducts();
    renderPagination();
  } catch (err) {
    renderError(err.message || "Da xay ra loi khi tim kiem san pham");
  }
}

function renderSearchSummary() {
  const title = document.getElementById("searchTitle");
  const chips = [];
  if (state.filters.name) chips.push(`Ten: ${state.filters.name}`);
  if (state.filters.category) chips.push(`Loai: ${state.filters.category}`);
  if (state.filters.priceFrom) chips.push(`Tu: ${formatPrice(Number(state.filters.priceFrom))} VND`);
  if (state.filters.priceTo) chips.push(`Den: ${formatPrice(Number(state.filters.priceTo))} VND`);

  title.textContent = chips.length > 0 ? `KET QUA: ${chips.join(" | ")}` : "TAT CA SAN PHAM";

  document.getElementById("resetFilterBtn").style.display = chips.length > 0 ? "block" : "none";
}

function renderProducts() {
  const container = document.getElementById("searchResultsContainer");

  if (state.items.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: #fff;">
        <i class="fas fa-car" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Khong tim thay san pham phu hop</h3>
        <p style="opacity: 0.8;">Vui long thu lai voi tieu chi khac</p>
      </div>
    `;
    document.getElementById("paginationControls").style.display = "none";
    return;
  }

  console.log('[DEBUG] renderProducts: state.items =', state.items);
  console.log('[DEBUG] renderProducts: state.productsData keys =', Object.keys(state.productsData));

  container.innerHTML = state.items
    .map((item) => {
      console.log('[DEBUG] Processing item:', item.id, item.name);
      // Lấy thông tin chi tiết từ JSON nếu có
      const fullData = state.productsData[item.id] || {};
      console.log('[DEBUG] fullData for id', item.id, ':', fullData);
      
      const dataAttrs = `
        data-id="${item.id}"
        data-origin="${escapeHtml(fullData.origin || 'Không rõ')}"
        data-year="${fullData.year || 'Không rõ'}"
        data-fuel="${escapeHtml(fullData.fuel || 'Không rõ')}"
        data-seats="${fullData.seats || 'Không rõ'}"
        data-transmission="${escapeHtml(fullData.transmission || 'Không rõ')}"
        data-engine="${escapeHtml(fullData.engine || 'Không rõ')}"
        data-desc="${escapeHtml(fullData.description || item.description || 'Đang cập nhật')}"
      `;
      
      // Tính đúng đường dẫn ảnh - sử dụng absolute URL từ server root
      let imageSrc = fullData.image_url || item.image_url || 'assets/images/default-car.jpg';
      // Convert relative path thành absolute URL từ server root: /WebBasic/assets/images/...
      if (imageSrc && !imageSrc.startsWith('http') && !imageSrc.startsWith('/')) {
        imageSrc = '/WebBasic/' + imageSrc;
      }
      console.log('[DEBUG] imageSrc:', imageSrc);
      
      return `
        <div class="car-card" ${dataAttrs}>
          <img src="${imageSrc}" alt="${escapeHtml(item.name)}" onerror="this.src='/WebBasic/assets/images/default-car.jpg'">
          <h3>${escapeHtml(item.name)}</h3>
          <p class="price">${formatPrice(Number(item.price || 0))} VNĐ</p>
          <div class="button-container">
            <button class="buy-btn" onclick="return false;" style="cursor:pointer; opacity:1;">Mua hàng</button>
            <a href="#" class="view-details" data-id="${item.id}">Chi tiết</a>
          </div>
        </div>
      `;
    })
    .join("");

  attachDetailEventListeners();
}

function renderPagination() {
  const controls = document.getElementById("paginationControls");
  controls.style.display = "flex";

  document.getElementById("currentPage").textContent = String(state.currentPage);
  document.getElementById("totalPages").textContent = String(state.totalPages);

  const prevBtn = document.getElementById("prevPageBtn");
  const nextBtn = document.getElementById("nextPageBtn");

  prevBtn.disabled = state.currentPage <= 1;
  nextBtn.disabled = state.currentPage >= state.totalPages;

  prevBtn.style.opacity = prevBtn.disabled ? "0.5" : "1";
  prevBtn.style.cursor = prevBtn.disabled ? "not-allowed" : "pointer";
  nextBtn.style.opacity = nextBtn.disabled ? "0.5" : "1";
  nextBtn.style.cursor = nextBtn.disabled ? "not-allowed" : "pointer";
}

function attachDetailEventListeners() {
  document.querySelectorAll(".view-details").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      e.preventDefault();
      const productId = btn.getAttribute("data-id");
      await showProductDetail(productId);
    });
  });
}

async function showProductDetail(productId) {
  try {
    console.log('[DEBUG] Opening detail for product', productId);
    // Lấy card từ DOM
    const card = document.querySelector(`.car-card[data-id="${productId}"]`);
    if (!card) {
      throw new Error("Không tìm thấy sản phẩm");
    }

    // Extract dữ liệu từ card data attributes (giống homepage)
    let img = card.querySelector('img').src;
    const title = card.querySelector('h3').textContent;
    const price = card.querySelector('.price').textContent;
    let origin = card.dataset.origin || 'Không rõ';
    let year = card.dataset.year || 'Không rõ';
    let fuel = card.dataset.fuel || 'Không rõ';
    let seats = card.dataset.seats || 'Không rõ';
    let transmission = card.dataset.transmission || 'Không rõ';
    let engine = card.dataset.engine || 'Không rõ';
    let desc = card.dataset.desc || 'Không có mô tả';

    // If data attributes are empty (JSON not loaded), try to fetch from API
    const hasCompleteData = !(origin === 'Không rõ' && year === 'Không rõ');
    if (!hasCompleteData && state.productsData[productId]) {
      const fullData = state.productsData[productId];
      origin = fullData.origin || 'Không rõ';
      year = fullData.year || 'Không rõ';
      fuel = fullData.fuel || 'Không rõ';
      seats = fullData.seats || 'Không rõ';
      transmission = fullData.transmission || 'Không rõ';
      engine = fullData.engine || 'Không rõ';
      desc = fullData.description || desc;
      img = fullData.image_url ? '/WebBasic/' + fullData.image_url : img;
    }

    console.log('[DEBUG] Product detail:', { title, price, origin, year, fuel, seats, transmission, engine, img });

    // Cập nhật modal
    const modal = document.getElementById('carModal');
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
    modal.style.display = 'flex';

    const closeBtn = modal.querySelector('.close-btn');
    closeBtn.onclick = function() {
      modal.style.display = 'none';
    };

    window.onclick = function(event) {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    };
  } catch (err) {
    console.error('[DEBUG] showProductDetail error:', err);
    renderError(err.message || "Không thể hiển thị chi tiết sản phẩm");
  }
}

function renderError(message) {
  const container = document.getElementById("searchResultsContainer");
  container.innerHTML = `
    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #fff;">
      <h3>Loi</h3>
      <p>${escapeHtml(message)}</p>
    </div>
  `;
  document.getElementById("paginationControls").style.display = "none";
}

function formatPrice(value) {
  const safe = Number.isFinite(value) ? value : 0;
  return safe.toLocaleString("vi-VN");
}

function escapeHtml(text) {
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", async () => {
  await loadProductsData(); // Load dữ liệu từ products.json
  bindEvents();
  hydrateFiltersFromUrl();
  setFilterInputs();
  await fetchAndRender(1);
});
