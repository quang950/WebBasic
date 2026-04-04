// Admin Panel JavaScript
// ========== Quản lý khách hàng ==========
function initSampleCustomers() {
  // Luôn sử dụng dữ liệu mẫu cho prototype, không lấy từ localStorage users
  const sampleCustomers = [
    {
      firstName: "Nguyễn Văn",
      lastName: "An",
      email: "nguyenvanan@gmail.com",
      phone: "0901234567",
      province: "TP. Hồ Chí Minh",
      locked: false,
    },
    {
      firstName: "Trần Thị",
      lastName: "Bình",
      email: "tranthibinh@gmail.com",
      phone: "0912345678",
      province: "Hà Nội",
      locked: false,
    },
    {
      firstName: "Lê Minh",
      lastName: "Cường",
      email: "leminhcuong@gmail.com",
      phone: "0923456789",
      province: "Đà Nẵng",
      locked: true,
    },
    {
      firstName: "Phạm Thu",
      lastName: "Hà",
      email: "phamthuha@gmail.com",
      phone: "0934567890",
      province: "Cần Thơ",
      locked: true,
    },
  ];
  return sampleCustomers;
}

function loadCustomers() {
  const grid = document.getElementById("customers-grid");
  if (!grid) return;

  // Hiển thị loading spinner
  grid.innerHTML =
    '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải khách hàng...</div>';

  // Gọi API để lấy tất cả khách hàng
  fetch("/WebBasic/BackEnd/api/admin/get_all_customers.php", {
    method: "GET",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => {
      if (!response.ok) {
        console.error(
          "Customer API Error:",
          response.status,
          response.statusText,
        );
        throw new Error(`HTTP ${response.status}`);
      }
      return response.json();
    })
    .then((result) => {
      console.log("Customers API Result:", result);
      if (result.success && result.customers) {
        const customers = result.customers;

        if (customers.length === 0) {
          grid.innerHTML = `
                    <div style="margin-bottom:20px;">
                        <button onclick="showAddUserModal()" style="padding:10px 20px;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:8px;">
                            <i class="fas fa-plus"></i> Thêm khách hàng
                        </button>
                    </div>
                    <div class="empty-state">Chưa có khách hàng nào đăng ký.</div>
                `;
          return;
        }

        grid.innerHTML = `
                <div style="margin-bottom:20px;">
                    <button onclick="showAddUserModal()" style="padding:10px 20px;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:8px;">
                        <i class="fas fa-plus"></i> Thêm khách hàng
                    </button>
                </div>
                <table class="customers-table" style="font-family:Arial,sans-serif;width:100%;table-layout:auto;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#007bff;color:#fff;">
                            <th style="min-width:150px;padding:12px;text-align:left;border-bottom:2px solid #0056b3;">Họ tên</th>
                            <th style="min-width:200px;padding:12px;text-align:left;border-bottom:2px solid #0056b3;">Email</th>
                            <th style="min-width:120px;padding:12px;text-align:left;border-bottom:2px solid #0056b3;">Điện thoại</th>
                            <th style="min-width:130px;padding:12px;text-align:left;border-bottom:2px solid #0056b3;">Tỉnh/TP</th>
                            <th style="min-width:100px;padding:12px;text-align:center;border-bottom:2px solid #0056b3;">Số đơn</th>
                            <th style="min-width:150px;padding:12px;text-align:right;border-bottom:2px solid #0056b3;">Tổng chi tiêu</th>
                            <th style="min-width:300px;padding:12px;text-align:center;border-bottom:2px solid #0056b3;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${customers
                          .map(
                            (u) => `
                            <tr style="border-bottom:1px solid #f0f0f0;transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                                <td style="padding:12px;"><strong>${u.first_name} ${u.last_name}</strong></td>
                                <td style="padding:12px;">${u.email}</td>
                                <td style="padding:12px;">${u.phone || "-"}</td>
                                <td style="padding:12px;">${u.province || "-"}</td>
                                <td style="padding:12px;text-align:center;"><span style="background:#e7f3ff;padding:4px 8px;border-radius:4px;font-weight:600;">${u.order_count || 0}</span></td>
                                <td style="padding:12px;text-align:right;"><strong>${formatPrice(u.total_spent || 0)} VNĐ</strong></td>
                                <td style="padding:12px;text-align:center;white-space:nowrap;">
                                    <button onclick="showResetPasswordModal(${u.id}, '${u.first_name} ${u.last_name}')" style="padding:6px 12px;font-size:0.9em;border-radius:6px;background:#007bff;color:#fff;border:none;cursor:pointer;margin-right:4px;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-key"></i> Reset MK</button>
                                    <button onclick="toggleLockUser(${u.id}, '${u.first_name} ${u.last_name}')" style="padding:6px 12px;font-size:0.9em;border-radius:6px;background:#ffc107;color:#000;border:none;cursor:pointer;margin-right:4px;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-lock"></i> Khóa</button>
                                    <button onclick="deleteUser(${u.id}, '${u.first_name} ${u.last_name}')" style="padding:6px 12px;font-size:0.9em;border-radius:6px;background:#dc3545;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-trash"></i> Xóa</button>
                                </td>
                            </tr>
                        `,
                          )
                          .join("")}
                    </tbody>
                </table>
            `;
      } else {
        grid.innerHTML = '<div class="empty-state">Lỗi tải khách hàng</div>';
      }
    })
    .catch((error) => {
      console.error("Error loading customers:", error);
      grid.innerHTML =
        '<div class="empty-state" style="color:red;">Lỗi: ' +
        error.message +
        "</div>";
    });
}

function toggleLockUser(idx) {
  // Chức năng khóa/mở khóa đã bị vô hiệu hóa (Prototype mode)
  return;
}

// Không còn export toggleLockUser

// Dữ liệu sản phẩm và loại sản phẩm được lưu trong localStorage
let products = JSON.parse(localStorage.getItem("products")) || [];
let categories = JSON.parse(localStorage.getItem("categories")) || [];

// Khởi tạo dữ liệu đơn hàng mẫu nếu chưa có
function initSampleOrders() {
  let orders = JSON.parse(localStorage.getItem("orders")) || [];
  if (orders.length === 0) {
    const sampleOrders = [
      {
        id: "DH001",
        date: "15/10/2025, 14:30",
        name: "Nguyễn Văn An",
        phone: "0901234567",
        email: "nguyenvanan@gmail.com",
        address: "123 Nguyễn Huệ, Quận 1, TP.HCM",
        paymentMethod: "COD",
        status: "Đã xử lý",
        items: [
          {
            id: "SP001",
            brand: "Toyota",
            name: "Camry 2024",
            price: 1200000000,
            quantity: 1,
            image: "assets/images/toyota-camry.jpg",
          },
        ],
        total: 1200000000,
        note: "Giao hàng trong giờ hành chính",
      },
      {
        id: "DH002",
        date: "20/10/2025, 10:15",
        name: "Trần Thị Bình",
        phone: "0912345678",
        email: "tranthibinh@gmail.com",
        address: "456 Lê Lợi, Quận 3, TP.HCM",
        paymentMethod: "Chuyển khoản",
        status: "Mới đặt",
        items: [
          {
            id: "SP002",
            brand: "Honda",
            name: "City RS 2024",
            price: 569000000,
            quantity: 1,
            image: "assets/images/honda-city.jpg",
          },
        ],
        total: 569000000,
        note: "Khách hàng yêu cầu gọi trước khi giao",
      },
    ];
    localStorage.setItem("orders", JSON.stringify(sampleOrders));
  }
}

// Gọi hàm khởi tạo dữ liệu mẫu khi load trang
initSampleOrders();

// Hiển thị/ẩn các section
function showSection(sectionName) {
  document.querySelectorAll(".content-section").forEach((section) => {
    section.classList.remove("active");
    section.style.display = "none";
  });
  document.querySelectorAll(".sidebar-nav li").forEach((item) => {
    item.classList.remove("active");
  });
  const sec = document.getElementById(sectionName + "-section");
  if (sec) {
    sec.classList.add("active");
    sec.style.display = "";
    // Mark nav active BEFORE loading section data to avoid UI flicker if a loader throws
    const link = document.querySelector(
      `.sidebar-nav a[href="#${sectionName}"]`,
    );
    const li = link ? link.closest("li") : null;
    if (li) li.classList.add("active");

    // Load section data safely (guard undefined functions)
    try {
      if (sectionName === "customers" && typeof loadCustomers === "function")
        loadCustomers();
      if (sectionName === "orders" && typeof loadAdminOrders === "function")
        loadAdminOrders();
      if (
        sectionName === "imports" &&
        typeof searchImportTickets === "function"
      )
        searchImportTickets();
      if (sectionName === "stock" && typeof loadOldStock === "function")
        loadOldStock();
      if (sectionName === "pricing" && typeof loadPricing === "function")
        loadPricing();
    } catch (e) {
      console.warn("Section init error for", sectionName, e);
    }
  }
  return false;
}

// Đăng xuất
function logout() {
  if (confirm("Bạn có chắc chắn muốn đăng xuất?")) {
    localStorage.removeItem("adminLoggedIn");
    localStorage.removeItem("adminUsername");
    localStorage.removeItem("adminViewingHome");
    window.location.href = "admin-login.php";
  }
}

// Hiển thị modal thêm sản phẩm
function showAddProductModal() {
  const modal = document.getElementById("addProductModal");
  const form = document.getElementById("addProductForm");
  const submitBtn = form.querySelector(".save-btn");
  const modalTitle = document.querySelector(
    "#addProductModal .modal-header h3",
  );

  // Reset về chế độ thêm mới
  form.dataset.editId = "";
  submitBtn.textContent = "Lưu sản phẩm";
  submitBtn.setAttribute("onclick", "return false;");
  modalTitle.textContent = "Thêm sản phẩm mới";

  modal.style.display = "block";
  updateCategorySelect();
}

// Đóng modal thêm sản phẩm
function closeAddProductModal() {
  const modal = document.getElementById("addProductModal");
  const form = document.getElementById("addProductForm");
  const submitBtn = form.querySelector(".save-btn");
  const modalTitle = document.querySelector(
    "#addProductModal .modal-header h3",
  );

  modal.style.display = "none";
  form.reset();

  // Reset về chế độ thêm mới
  form.dataset.editId = "";
  submitBtn.textContent = "Lưu sản phẩm";
  submitBtn.setAttribute("onclick", "return false;");
  modalTitle.textContent = "Thêm sản phẩm mới";
}

// Add product function
function addProduct() {
  const form = document.getElementById("addProductForm");
  if (!form) return false;

  const name = document.getElementById("productName").value.trim();
  const code = document.getElementById("productCode").value.trim() || "";
  const brand = document.getElementById("productBrand").value.trim();
  const price = parseFloat(document.getElementById("productPrice").value);
  const cost = parseFloat(document.getElementById("productCost").value || "0");
  const margin = parseFloat(
    document.getElementById("productMargin").value || "10",
  );
  const stock = parseInt(document.getElementById("productStock").value || "0");
  const unit = document.getElementById("productUnit").value.trim() || "chiếc";
  const year = parseInt(document.getElementById("productYear").value);
  const fuel = document.getElementById("productFuel").value.trim();
  const transmission = document
    .getElementById("productTransmission")
    .value.trim();
  const category = document.getElementById("productCategory").value.trim();
  const image = document.getElementById("productImageUrl").value.trim();
  const description = document
    .getElementById("productDescription")
    .value.trim();
  const status = document.getElementById("productStatus").checked ? 1 : 0;

  // Validate
  if (!name || !brand || !price) {
    alert(
      "Vui lòng điền đầy đủ thông tin bắt buộc (Tên, Thương hiệu, Giá bán)!",
    );
    return false;
  }

  if (price <= 0) {
    alert("Giá phải lớn hơn 0!");
    return false;
  }

  // Show loading
  const submitBtn = form.querySelector(".save-btn");
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = "Đang lưu...";

  // Send to API
  fetch("/WebBasic/BackEnd/api/admin/add_product.php", {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      name: `${brand.charAt(0).toUpperCase() + brand.slice(1)} ${name}`,
      product_code: code,
      brand: brand,
      price: price,
      price_cost: cost,
      profit_margin: margin,
      stock: stock,
      initial_stock: stock,
      unit: unit,
      year: year,
      fuel: fuel,
      transmission: transmission,
      category: category,
      image: image || `/WebBasic/FrontEnd/assets/images/logo-${brand}.png`,
      description: description,
      status: status,
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      console.log("Add product result:", result);

      if (result.success) {
        alert("✓ Thêm sản phẩm thành công!");
        closeAddProductModal();

        // Reload products list
        if (typeof loadProducts === "function") {
          loadProducts();
        }
      } else {
        alert("Lỗi: " + (result.message || "Không thể thêm sản phẩm"));
      }
    })
    .catch((error) => {
      console.error("Error adding product:", error);
      alert("Lỗi: " + error.message);
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    });

  return false;
}

// Make addProduct globally available
window.addProduct = addProduct;

// Preview ảnh khi chọn file
function previewImage(input) {
  const preview = document.getElementById("imagePreview");
  const previewImg = document.getElementById("previewImg");

  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(input.files[0]);

    // Xóa URL nếu có file upload
    document.getElementById("productImageUrl").value = "";
  } else {
    preview.style.display = "none";
  }
}

// Preview ảnh khi nhập URL
function previewUrlImage(input) {
  const preview = document.getElementById("imagePreview");
  const previewImg = document.getElementById("previewImg");

  if (input.value) {
    previewImg.src = input.value;
    preview.style.display = "block";

    // Xóa file nếu có URL
    document.getElementById("productImage").value = "";
  } else {
    preview.style.display = "none";
  }
}

// Đóng modal khi click bên ngoài
window.onclick = function (event) {
  const modal = document.getElementById("addProductModal");
  if (event.target == modal) {
    closeAddProductModal();
  }
};

// Thêm sản phẩm mới
document
  .getElementById("addProductForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    // Chức năng thêm sản phẩm đã bị vô hiệu hóa (Prototype mode)
    return false;
  });

// Tải và hiển thị danh sách sản phẩm
function loadProducts() {
  const productsGrid = document.getElementById("products-grid");

  if (!productsGrid) return;

  // Show loading spinner
  productsGrid.innerHTML =
    '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải sản phẩm...</div>';

  // Fetch products from API
  fetch("/WebBasic/BackEnd/api/admin/get_products.php", {
    method: "GET",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((result) => {
      console.log("Products API result:", result);

      if (result.success && result.products) {
        const productsList = result.products;

        if (productsList.length === 0) {
          productsGrid.innerHTML =
            '<div class="empty-state">Chưa có sản phẩm nào. Hãy thêm từ form trên!</div>';
          return;
        }

        productsGrid.innerHTML = `
                <div class="products-search-bar" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-search" style="color: #666;"></i>
                        <input type="text" id="productSearchInput" placeholder="Tìm kiếm sản phẩm..." 
                            style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; font-family: Arial, sans-serif;"
                            onkeyup="return false;">
                        <button onclick="showSingleProduct()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </div>
                </div>
                <div class="products-list">
                    ${productsList
                      .map(
                        (product) => `
                        <div class="product-card" data-id="${product.id}">
                            <div class="product-image">
                                <img src="${product.image}" alt="${product.name}" onerror="this.src='/WebBasic/FrontEnd/assets/images/logo-${product.brand}.png'">
                            </div>
                            <div class="product-info">
                                <h3>${product.name}</h3>
                                <p class="brand">${product.brand.toUpperCase()}</p>
                                <p class="price">${formatPrice(product.price)} VNĐ</p>
                                <div class="product-details">
                                    <span><i class="fas fa-calendar"></i> ${product.year || ""}</span>
                                    <span><i class="fas fa-gas-pump"></i> ${product.fuel || ""}</span>
                                    <span><i class="fas fa-cogs"></i> ${product.transmission || ""}</span>
                                    ${product.category ? `<span><i class="fas fa-tags"></i> ${product.category}</span>` : ""}
                                </div>
                                <div class="product-actions">
                                    <button onclick="showEditProductModal(${product.id})" class="edit-btn" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;background:#17a2b8;color:#fff;border:none;cursor:pointer;"><i class="fas fa-edit"></i> Sửa</button>
                                    <button onclick="confirmDeleteProduct(${product.id})" class="delete-btn" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;background:#dc3545;color:#fff;border:none;cursor:pointer;"><i class="fas fa-trash"></i> Xóa</button>
                                </div>
                            </div>
                        </div>
                    `,
                      )
                      .join("")}
                </div>
            `;
      } else {
        productsGrid.innerHTML =
          '<div class="empty-state">Lỗi tải sản phẩm</div>';
      }
    })
    .catch((error) => {
      console.error("Error loading products:", error);
      productsGrid.innerHTML =
        '<div class="empty-state" style="color:red;">Lỗi: ' +
        error.message +
        "</div>";
    });
}

// Các chức năng sửa/xóa/ẩn sản phẩm đã bị vô hiệu hóa (Prototype mode)

// Sửa sản phẩm - chỉ hiển thị modal, không cho lưu
function editProduct(productId) {
  let product = products.find((p) => p.id === productId);

  // Nếu không tìm thấy, dùng dữ liệu Toyota Camry mặc định
  if (!product) {
    product = {
      id: 1,
      name: "Camry",
      brand: "toyota",
      price: 1235000000,
      year: 2025,
      fuel: "Xăng",
      transmission: "Tự động (AT)",
      image: "assets/images/toyota-camry.jpg",
      category: "sedan",
      description: "Sedan hạng D êm ái, tiện nghi, tiết kiệm.",
    };
  }

  // Điền thông tin vào form
  document.getElementById("productName").value = product.name;
  document.getElementById("productBrand").value = product.brand;
  document.getElementById("productPrice").value = product.price;
  document.getElementById("productYear").value = product.year;
  document.getElementById("productFuel").value = product.fuel;
  document.getElementById("productTransmission").value = product.transmission;
  document.getElementById("productImageUrl").value = product.image;
  document.getElementById("productCategory").value = product.category || "";
  document.getElementById("productDescription").value = product.description;

  // Thay đổi form để chế độ chỉnh sửa
  const form = document.getElementById("addProductForm");
  form.dataset.editId = productId;

  // Thay đổi nút submit - thêm onclick="return false;" để không lưu
  const submitBtn = form.querySelector(".save-btn");
  submitBtn.textContent = "Cập nhật sản phẩm";
  submitBtn.setAttribute("onclick", "return false;");

  // Thay đổi tiêu đề modal
  document.querySelector("#addProductModal .modal-header h3").textContent =
    "Chỉnh sửa sản phẩm";

  showAddProductModal();
}

// Cập nhật thống kê
function updateStats() {
  document.getElementById("total-products").textContent = products.length;

  // Tính tổng lượt xem (giả lập)
  const totalViews = products.reduce(
    (sum, product) => sum + (product.views || Math.floor(Math.random() * 100)),
    0,
  );
  document.getElementById("total-views").textContent = totalViews;
}

// Format giá tiền
function formatPrice(price) {
  return new Intl.NumberFormat("vi-VN").format(price);
}

// Định dạng ngày về dd/mm/yyyy
function formatDateVN(dateStr) {
  if (!dateStr) return "";
  // Nếu đã đúng định dạng dd/mm/yyyy thì trả về luôn
  if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) return dateStr;

  // Parse ISO date hoặc MySQL datetime
  try {
    const date = new Date(dateStr);
    if (isNaN(date)) return dateStr;

    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  } catch (e) {
    return dateStr;
  }
}

// Lightweight accessor for orders used by stock helpers
function loadOrders() {
  return JSON.parse(localStorage.getItem("orders")) || [];
}

let currentAdminOrders = [];

// ========== Quản lý đơn hàng cho admin ==========
function loadAdminOrders() {
  const ordersGrid = document.getElementById("adminOrdersGrid");
  if (!ordersGrid) return;

  ordersGrid.innerHTML =
    '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải đơn hàng...</div>';

  fetch("/WebBasic/BackEnd/api/admin/get_all_orders.php", {
    method: "GET",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.status === "success" && result.data && result.data.orders) {
        const rawOrders = result.data.orders;
        const displayOrders = rawOrders.map((order) => ({
          id: order.id,
          date: formatDateVN(order.created_at),
          name: (order.first_name || "") + " " + (order.last_name || ""),
          phone: order.shipping_phone || "",
          email: order.user_email || "",
          address: order.shipping_address || "",
          paymentMethod: order.payment || "COD",
          status: order.status,
          items: order.items.map((item) => ({
            brand: extractBrand(item.product_name),
            name: item.product_name,
            quantity: item.quantity,
            price: item.unit_price,
            img: "assets/images/default-car.jpg", // Default image if API doesn't return
          })),
          total: order.total_price,
        }));

        // Lưu biến global để hiển thị modal chi tiết
        currentAdminOrders = displayOrders;

        if (displayOrders.length === 0) {
          ordersGrid.innerHTML =
            '<div class="empty-state">Chưa có đơn hàng nào.</div>';
        } else {
          ordersGrid.innerHTML = renderAdminOrdersTable(displayOrders);
        }
      } else {
        ordersGrid.innerHTML =
          '<div class="empty-state">Lỗi tải dữ liệu đơn hàng.</div>';
      }
    })
    .catch((error) => {
      ordersGrid.innerHTML =
        '<div class="empty-state" style="color:red;">Lỗi tải dữ liệu: ' +
        error.message +
        "</div>";
    });
}

function filterAdminOrders() {
  const ordersGrid = document.getElementById("adminOrdersGrid");
  if (!ordersGrid) return false;

  const dateFrom = document.getElementById("orderDateFrom").value || "";
  const dateTo = document.getElementById("orderDateTo").value || "";
  const status = document.getElementById("orderStatusFilter").value || "";
  const ward = document.getElementById("orderWardFilter").value || "";
  const sortBy = document.getElementById("orderSortBy").value || "created_at";

  ordersGrid.innerHTML =
    '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải đơn hàng...</div>';

  const params = new URLSearchParams();
  if (dateFrom) params.append("dateFrom", dateFrom);
  if (dateTo) params.append("dateTo", dateTo);
  if (status) params.append("status", status);
  if (ward) params.append("ward", ward);
  if (sortBy) params.append("sortBy", sortBy);

  const url =
    "/WebBasic/BackEnd/api/admin/get_all_orders.php?" + params.toString();

  fetch(url, {
    method: "GET",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.status === "success" && result.data && result.data.orders) {
        const rawOrders = result.data.orders;
        const displayOrders = rawOrders.map((order) => ({
          id: order.id,
          date: formatDateVN(order.created_at),
          name: (order.first_name || "") + " " + (order.last_name || ""),
          phone: order.shipping_phone || "",
          email: order.user_email || "",
          address: order.shipping_address || "",
          paymentMethod: order.payment || "COD",
          status: order.status,
          items: order.items.map((item) => ({
            brand: extractBrand(item.product_name),
            name: item.product_name,
            quantity: item.quantity,
            price: item.unit_price,
            img: "assets/images/default-car.jpg",
          })),
          total: order.total_price,
        }));

        // Cập nhật lại array cache cho modal
        currentAdminOrders = displayOrders;

        ordersGrid.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <button onclick="loadAdminOrders()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-undo"></i> Reset Bộ Lọc
                    </button>
                    <span style="margin-left: 10px; font-weight: bold; color: #0d6efd;">Tổng số lượng: ${result.data.count}</span>
                </div>
                ${renderAdminOrdersTable(displayOrders)}
            `;
      } else {
        ordersGrid.innerHTML =
          '<div class="empty-state">Không tìm thấy đơn hàng nào phù hợp bộ lọc.</div>';
      }
    })
    .catch((error) => {
      ordersGrid.innerHTML =
        '<div class="empty-state" style="color:red;">Lỗi tải dữ liệu r: ' +
        error.message +
        "</div>";
    });

  return false;
}

// Helper functions
function mapOrderStatus(dbStatus) {
  const statusMap = {
    new: "Mới đặt (Chưa xử lý)",
    pending: "Mới đặt (Chưa xử lý)",
    processing: "Đã xác nhận",
    delivered: "Đã giao thành công",
    cancelled: "Đã hủy",
  };
  return statusMap[dbStatus] || dbStatus || "Mới đặt (Chưa xử lý)";
}

function extractBrand(productName) {
  // Trích xuất hãng từ tên sản phẩm (ví dụ: "Toyota Camry" -> "Toyota")
  if (!productName) return "";
  const brands = [
    "Toyota",
    "Honda",
    "BMW",
    "Mercedes",
    "Audi",
    "Lexus",
    "Hyundai",
    "Kia",
    "Vinfast",
  ];
  for (let brand of brands) {
    if (productName.toLowerCase().includes(brand.toLowerCase())) {
      return brand;
    }
  }
  return productName.split(" ")[0] || "";
}

function renderAdminOrdersTable(orders) {
  if (!orders.length)
    return '<div class="empty-state">Không có đơn hàng phù hợp.</div>';

  return `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(400px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${orders
          .slice()
          .reverse()
          .map((order) => {
            const statusText = order.status || "Mới đặt";

            const itemsHtml = order.items
              .map(
                (item) => `
                <div style='margin-bottom:8px;padding:8px;background:#f9f9f9;border-radius:6px;font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;font-family:Arial,sans-serif;font-weight:normal;'>Xe: ${item.brand} ${item.name}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Số lượng: ${item.quantity}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Giá: ${formatPrice(item.price)} VNĐ</div>
                </div>
            `,
              )
              .join("");

            return `
                <div style='background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;box-shadow:0 2px 4px rgba(0,0,0,0.05);font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Mã đơn: <strong>#${order.id}</strong></div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Ngày đặt: ${order.date}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Người nhận: ${order.name}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Số điện thoại: ${order.phone}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Địa chỉ: ${order.address}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:12px;font-family:Arial,sans-serif;font-weight:normal;display:flex;align-items:center;gap:10px;'>
                        <span>Tình trạng:</span>
                        <select onchange='updateOrderStatus(${order.id}, this.value)' style='padding:6px 10px;border-radius:6px;border:1px solid #ddd;font-size:0.95em;background:#fff;'>
                            <option value='new' ${order.status === "new" || !order.status ? "selected" : ""}>Mới đặt</option>
                            <option value='processing' ${order.status === "processing" ? "selected" : ""}>Đã xác nhận</option>
                            <option value='delivered' ${order.status === "delivered" ? "selected" : ""}>Đã giao</option>
                            <option value='cancelled' ${order.status === "cancelled" ? "selected" : ""}>Đã hủy</option>
                        </select>
                    </div>
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Chi tiết xe đã mua: <button onclick="showOrderDetail(${order.id})" style="float:right;background:none;border:none;color:#007bff;cursor:pointer;"><i class="fas fa-eye"></i> Xem thêm</button></div>
                        ${itemsHtml}
                    </div>
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1.1em;text-align:right;font-family:Arial,sans-serif;font-weight:normal;'>Tổng: ${formatPrice(order.total)} VNĐ</div>
                    </div>
                </div>
            `;
          })
          .join("")}
    </div>`;
}

function renderOrderStatusSelect(order) {
  const status = order.status || "new";
  return `<select onchange='updateOrderStatus(${order.id}, this.value)' style='padding:4px 8px;border-radius:6px;'>
        <option value='new' ${status === "new" ? "selected" : ""}>Mới đặt (Chưa xử lý)</option>
        <option value='processing' ${status === "processing" ? "selected" : ""}>Đã xác nhận</option>
        <option value='delivered' ${status === "delivered" ? "selected" : ""}>Đã giao thành công</option>
        <option value='cancelled' ${status === "cancelled" ? "selected" : ""}>Đã hủy</option>
    </select>`;
}

function updateOrderStatus(orderId, newStatus) {
  if (!confirm("Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này?")) {
    loadAdminOrders(); // reload to reset UI if cancelled
    return false;
  }

  fetch("/WebBasic/BackEnd/api/admin/update_order_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ order_id: orderId, status: newStatus }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.status === "success") {
        showNotification("Cập nhật trạng thái thành công !", "success");
        // Cập nhật lại list ở global
        const oIndex = currentAdminOrders.findIndex((o) => o.id == orderId);
        if (oIndex !== -1) currentAdminOrders[oIndex].status = newStatus;
      } else {
        showNotification(result.message || "Lỗi cập nhật", "error");
        loadAdminOrders(); // reload
      }
    })
    .catch((error) => {
      showNotification("Lỗi kết nối máy chủ", "error");
      loadAdminOrders(); // reload
    });

  return true;
}

// Export function
window.updateOrderStatus = updateOrderStatus;

function showOrderDetail(orderId) {
  const order = currentAdminOrders.find((o) => o.id === orderId);
  if (!order) {
    showNotification("Không tìm thấy đơn hàng", "error");
    return;
  }

  let html = `<div class='order-detail-modal' style='position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:9999;display:flex;align-items:center;justify-content:center;'>
        <div style='background:#fff;padding:32px 24px;border-radius:12px;max-width:600px;width:100%;box-shadow:0 2px 16px rgba(0,0,0,0.12);position:relative;max-height:90vh;overflow-y:auto;'>
            <button onclick='this.parentElement.parentElement.remove()' style='position:absolute;top:12px;right:12px;background:#dc3545;color:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:1.2em;cursor:pointer;'>&times;</button>
            <h3 style='margin-bottom:12px;color:#0d6efd;'>Chi tiết đơn hàng #${order.id}</h3>
            <div style='margin-bottom:8px;'><strong>Ngày đặt:</strong> ${order.date}</div>
            <div style='margin-bottom:8px;'><strong>Người nhận:</strong> ${order.name} | <strong>ĐT:</strong> ${order.phone}</div>
            <div style='margin-bottom:8px;'><strong>Địa chỉ:</strong> ${order.address}</div>
            <div style='margin-bottom:8px;'><strong>Thanh toán:</strong> ${order.paymentMethod === "cod" ? "Tiền mặt khi nhận hàng" : order.paymentMethod === "bank" ? "Chuyển khoản" : order.paymentMethod}</div>
            <div style='margin-bottom:15px;display:flex;align-items:center;gap:10px;'><strong>Tình trạng:</strong> ${renderOrderStatusSelect(order)}</div>
            <table style='width:100%;border-collapse:collapse;margin-top:12px;border:1px solid #ddd;'>
                <thead><tr style='background:#f8f9fa;color:#333;text-align:left;'>
                    <th style="padding:10px;border-bottom:2px solid #ddd;">Ảnh</th>
                    <th style="padding:10px;border-bottom:2px solid #ddd;">Tên xe</th>
                    <th style="padding:10px;border-bottom:2px solid #ddd;">Giá</th>
                    <th style="padding:10px;border-bottom:2px solid #ddd;">SL</th>
                </tr></thead>
                <tbody>
                    ${order.items
                      .map(
                        (item) => `
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;"><img src='${item.img}' alt='${item.name}' style='width:60px;height:40px;object-fit:cover;border-radius:6px;'></td>
                        <td style="padding:10px;font-weight:600;">${item.name}</td>
                        <td style="padding:10px;color:#d9534f;">${formatPrice(item.price)} VNĐ</td>
                        <td style="padding:10px;text-align:center;">${item.quantity}</td>
                    </tr>`,
                      )
                      .join("")}
                </tbody>
            </table>
            <div style='text-align:right;font-weight:bold;font-size:1.2rem;margin-top:20px;color:#198754;border-top:2px solid #eee;padding-top:15px;'>
                Tổng hóa đơn: ${formatPrice(order.total)} đ
            </div>
        </div>
    </div>`;
  document.body.insertAdjacentHTML("beforeend", html);
}

// Hiển thị thông báo
function showNotification(message, type = "info") {
  // Tạo element thông báo
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.innerHTML = `
        <i class="fas fa-${type === "success" ? "check-circle" : "info-circle"}"></i>
        <span>${message}</span>
    `;

  // Thêm vào body
  document.body.appendChild(notification);

  // Hiển thị với animation
  setTimeout(() => notification.classList.add("show"), 100);

  // Ẩn sau 3 giây
  setTimeout(() => {
    notification.classList.remove("show");
    setTimeout(() => document.body.removeChild(notification), 300);
  }, 3000);
}

// Các chức năng thêm/cập nhật sản phẩm đã bị vô hiệu hóa (Prototype mode)
// Submit handlers đã bị remove

// Export dữ liệu cho trang index
function exportProductsForIndex() {
  return products;
}

// Làm cho function có thể truy cập globally
window.exportProductsForIndex = exportProductsForIndex;

// ========== Quản lý Tồn kho và Thống kê báo cáo ==========

// 1. Cảnh báo sản phẩm sắp hết hàng
function loadLowStockAlert() {
  const listDiv = document.getElementById("lowStockList");
  if (!listDiv) return;

  const threshold = document.getElementById("alertThreshold")
    ? document.getElementById("alertThreshold").value
    : 2;
  listDiv.innerHTML =
    '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải danh sách cảnh báo...</div>';

  fetch(
    `/WebBasic/BackEnd/api/admin/get_stock_report.php?action=low_stock&threshold=${threshold}`,
  )
    .then((r) => r.json())
    .then((res) => {
      if (res.status === "success" && res.data.length > 0) {
        let html = `<ul style="list-style:none;padding:0;margin:0;">`;
        res.data.forEach((item) => {
          html += `<li style="padding:12px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <strong>${item.name}</strong> <span style="color:#666;font-size:0.9em;">(${item.product_code || "Không có mã"})</span>
                        <div style="font-size:0.85em;color:#007bff;">${item.category || "Chưa phân loại"}</div>
                    </div>
                    <div style="background:#dc3545;color:white;padding:4px 10px;border-radius:20px;font-weight:bold;font-size:0.9em;">
                        Tồn: ${item.stock}
                    </div>
                </li>`;
        });
        html += `</ul>`;
        listDiv.innerHTML = html;
      } else {
        listDiv.innerHTML =
          '<div style="padding:15px;color:green;text-align:center;"><i class="fas fa-check-circle"></i> Chuẩn rồi, kho hàng không có sản phẩm nào chạm ngưỡng hết!</div>';
      }
    })
    .catch(
      (e) =>
        (listDiv.innerHTML = `<div style="color:red;padding:10px;">Lỗi: ${e.message}</div>`),
    );
}

// 2. Tra cứu tồn kho tại 1 thời điểm
function searchStockHistory() {
  const sName = document.getElementById("historyProductId").value;
  const tDate = document.getElementById("historyDate").value;
  const resDiv = document.getElementById("historyResult");

  if (!sName || !tDate) {
    showNotification("Vui lòng điền đủ mã/tên xe và thời gian", "error");
    return;
  }

  resDiv.style.display = "block";
  resDiv.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Đang tính toán dữ liệu nội suy...';

  // Format targetDate (HTML input datetime-local => YYYY-MM-DD HH:ii:ss)
  const formattedDate = tDate.replace("T", " ") + ":00";

  fetch(
    `/WebBasic/BackEnd/api/admin/get_stock_report.php?action=stock_at_time&searchName=${encodeURIComponent(sName)}&targetDate=${encodeURIComponent(formattedDate)}`,
  )
    .then((r) => r.json())
    .then((res) => {
      if (res.status === "success") {
        const d = res.data;
        resDiv.innerHTML = `
                <div style="font-size:1.1rem;margin-bottom:8px;">Kết quả tra cứu sản phẩm: <strong>${d.product_name}</strong></div>
                <div style="margin-bottom:8px;">Tại thời điểm: <strong>${d.target_date}</strong></div>
                <div style="font-size:1.5rem;color:#17a2b8;font-weight:bold;margin-top:10px;">Số lượng tồn kho: ${d.stock_at_time} chiếc</div>
                <div style="font-size:0.9em;color:#666;margin-top:8px;">(Tồn kho thực tế hiện nay: ${d.current_stock})</div>
            `;
      } else {
        resDiv.innerHTML = `<span style="color:red;">Lỗi: ${res.message}</span>`;
      }
    })
    .catch(
      (e) => (resDiv.innerHTML = `<span style="color:red;">Lỗi kết nối</span>`),
    );
}

// 3. Báo cáo Nhập Xuất trong 1 khoảng thời gian
function searchInventoryReport() {
  const sName = document.getElementById("inventorySearchProduct").value;
  const fDate = document.getElementById("inventoryFromDate").value;
  const tDate = document.getElementById("inventoryToDate").value;
  const resDiv = document.getElementById("inventoryResult");
  const listDiv = document.getElementById("inventoryDataList");

  if (!sName || !fDate || !tDate) {
    showNotification(
      "Vui lòng điền đủ Tên/Mã sản phẩm và Khoảng thời gian",
      "error",
    );
    return;
  }

  resDiv.style.display = "block";
  listDiv.innerHTML =
    '<div style="grid-column:1/-1;text-align:center;"><i class="fas fa-spinner fa-spin"></i> Đang truy xuất sổ kho...</div>';

  fetch(
    `/WebBasic/BackEnd/api/admin/get_stock_report.php?action=report_in_out&searchName=${encodeURIComponent(sName)}&fromDate=${encodeURIComponent(fDate)}&toDate=${encodeURIComponent(tDate)}`,
  )
    .then((r) => r.json())
    .then((res) => {
      if (res.status === "success") {
        const d = res.data;
        listDiv.innerHTML = `
                <div style="grid-column:1/-1;margin-bottom:10px;font-size:1.1rem;color:#0d279d;">
                    Báo cáo thẻ kho sản phẩm: <strong>${d.product_name}</strong> 
                    <span style="font-size:0.9em;color:#555;">(Từ ${fDate} đến ${tDate})</span>
                </div>
                <div style="text-align:center;padding:12px;background:#e3f2fd;border-radius:6px;">
                    <div style="color:#1976d2;font-size:0.9em;margin-bottom:4px;font-weight:600;">Tồn đầu kỳ</div>
                    <div style="color:#0d47a1;font-size:1.5em;font-weight:700;">${d.stock_begin}</div>
                </div>
                <div style="text-align:center;padding:12px;background:#e8f5e9;border-radius:6px;">
                    <div style="color:#2e7d32;font-size:0.9em;margin-bottom:4px;font-weight:600;">Số lượng Nhập</div>
                    <div style="color:#1b5e20;font-size:1.5em;font-weight:700;">+ ${d.total_import}</div>
                </div>
                <div style="text-align:center;padding:12px;background:#fff3e0;border-radius:6px;">
                    <div style="color:#f57c00;font-size:0.9em;margin-bottom:4px;font-weight:600;">Số lượng Xuất</div>
                    <div style="color:#e65100;font-size:1.5em;font-weight:700;">- ${d.total_export}</div>
                </div>
                <div style="text-align:center;padding:12px;background:#f3e5f5;border-radius:6px;">
                    <div style="color:#7b1fa2;font-size:0.9em;margin-bottom:4px;font-weight:600;">Tồn cuối kỳ</div>
                    <div style="color:#4a148c;font-size:1.5em;font-weight:700;">${d.stock_end}</div>
                </div>
            `;
      } else {
        listDiv.innerHTML = `<div style="grid-column:1/-1;color:red;text-align:center;">Lỗi: ${res.message}</div>`;
      }
    })
    .catch(
      (e) =>
        (listDiv.innerHTML = `<div style="grid-column:1/-1;color:red;text-align:center;">Lỗi kết nối</div>`),
    );
}

// Gọi mặc định cho Cảnh báo hết hàng khi tab được hiển thị
function onStockTabSelect() {
  loadLowStockAlert();
}

function initCategories() {
  // Nếu lần đầu chưa có categories thì tạo vài loại cơ bản
  if (!categories || !categories.length) {
    categories = [
      { id: 1, name: "SUV", slug: "suv", hidden: false },
      { id: 2, name: "Sedan", slug: "sedan", hidden: false },
      { id: 3, name: "MPV", slug: "mpv", hidden: false },
      { id: 4, name: "Hatchback", slug: "hatchback", hidden: false },
      { id: 5, name: "Bán tải", slug: "pickup", hidden: false },
    ];
    localStorage.setItem("categories", JSON.stringify(categories));
  }
}

function loadCategories() {
  const grid = document.getElementById("categories-grid");
  if (!grid) return;
  if (!categories.length) {
    grid.innerHTML =
      '<div class="empty-state">Chưa có loại sản phẩm nào.</div>';
    return;
  }
  grid.innerHTML = categories
    .map(
      (c) => `
        <div class="product-card" data-id="${c.id}">
            <div class="product-info">
                <h3>${c.name}</h3>
                <p class="brand">/${c.slug}</p>
                <div class="product-actions">
                    <button onclick="return false;" class="edit-btn" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas fa-edit"></i> Sửa
                    </button>
                    <button onclick="return false;" class="${c.hidden ? "unhide-btn" : "hide-btn"}" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas ${c.hidden ? "fa-eye" : "fa-eye-slash"}"></i> ${c.hidden ? "Hiện" : "Ẩn"}
                    </button>
                    <button onclick="return false;" class="delete-btn" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    `,
    )
    .join("");
}

// Các chức năng thêm/sửa/xóa danh mục đã bị vô hiệu hóa (Prototype mode)

function updateCategorySelect() {
  const sel = document.getElementById("productCategory");
  if (!sel) return;
  const visible = (categories || []).filter((c) => !c.hidden);
  sel.innerHTML =
    '<option value="">Chọn loại</option>' +
    visible.map((c) => `<option value="${c.name}">${c.name}</option>`).join("");
}

// ========== Nhập xe đã có từ trang chủ ==========
function importHomepageCars(silent = false) {
  const cached = JSON.parse(localStorage.getItem("homepageCars") || "[]");
  if (!cached.length) {
    if (!silent)
      showNotification("Không tìm thấy dữ liệu xe trang chủ để nhập.", "info");
    return 0;
  }
  let imported = 0;
  cached.forEach((item) => {
    // Bỏ qua nếu đã tồn tại theo name+brand
    if (products.some((p) => p.name === item.name && p.brand === item.brand))
      return;
    products.push({
      id: Date.now() + Math.floor(Math.random() * 1000),
      name: item.name,
      brand: item.brand,
      price: item.price || 0,
      year: item.year || new Date().getFullYear(),
      fuel: item.fuel || "Xăng",
      transmission: item.transmission || "Tự động (AT)",
      image: item.image,
      description: item.description || "",
      category: item.category || "",
      dateAdded: new Date().toISOString(),
      hidden: false,
    });
    imported++;
  });
  localStorage.setItem("products", JSON.stringify(products));
  loadProducts();
  updateStats();
  if (!silent)
    showNotification(`Đã nhập ${imported} sản phẩm từ trang chủ`, "success");
  return imported;
}

// Gắn import vào window để gọi từ HTML nếu cần
window.importHomepageCars = importHomepageCars;

// ========== Quản lý phiếu nhập (imports) ==========
let importsData = JSON.parse(localStorage.getItem("imports")) || [];

// Khởi tạo dữ liệu phiếu nhập mẫu nếu chưa có
function initSampleImports() {
  let imports = JSON.parse(localStorage.getItem("imports")) || [];
  if (imports.length === 0) {
    const sampleImports = [
      {
        id: 1,
        code: "PN001",
        date: "05/10/2025",
        supplier: "Công ty TNHH Ô tô Thành Công",
        items: [
          {
            productId: "SP001",
            brand: "Toyota",
            name: "Camry 2024",
            price: 1100000000,
            qty: 3,
          },
          {
            productId: "SP002",
            brand: "Toyota",
            name: "Vios 2024",
            price: 480000000,
            qty: 5,
          },
        ],
        subtotal: 5700000000,
        tax: 570000000,
        total: 6270000000,
        completed: true,
        note: "Đã kiểm tra chất lượng và nhập kho đầy đủ",
      },
      {
        id: 2,
        code: "PN002",
        date: "12/10/2025",
        supplier: "Tổng công ty Ô tô Sài Gòn",
        items: [
          {
            productId: "SP003",
            brand: "Honda",
            name: "City RS 2024",
            price: 550000000,
            qty: 4,
          },
          {
            productId: "SP004",
            brand: "Honda",
            name: "CR-V 2024",
            price: 1100000000,
            qty: 2,
          },
        ],
        subtotal: 4400000000,
        tax: 440000000,
        total: 4840000000,
        completed: false,
        note: "Đang chờ thanh toán đợt 2",
      },
    ];
    localStorage.setItem("imports", JSON.stringify(sampleImports));
    importsData = sampleImports;
  }
}

function saveImports() {
  localStorage.setItem("imports", JSON.stringify(importsData));
}

function loadImports() {
  initSampleImports(); // Khởi tạo dữ liệu mẫu nếu chưa có
  importsData = JSON.parse(localStorage.getItem("imports")) || [];
  const grid = document.getElementById("importsGrid");
  if (!grid) return;
  if (!importsData.length) {
    grid.innerHTML = '<div class="empty-state">Chưa có phiếu nhập nào.</div>';
    return;
  }
  grid.innerHTML = renderImportsTable(importsData);
}

function renderImportsTable(list) {
  if (!list || !list.length)
    return '<div class="empty-state">Không có phiếu nhập phù hợp.</div>';

  return `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(450px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${list
          .slice()
          .reverse()
          .map((i) => {
            const itemsHtml = i.items
              .map(
                (item) => `
                <div style='margin-bottom:8px;padding:8px;background:#f9f9f9;border-radius:6px;font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;font-family:Arial,sans-serif;font-weight:normal;'>Xe: ${item.brand} ${item.name}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Số lượng: ${item.qty}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Giá nhập: ${formatPrice(item.price)} VNĐ</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Thành tiền: ${formatPrice(item.price * item.qty)} VNĐ</div>
                </div>
            `,
              )
              .join("");

            const statusText = i.completed
              ? "Đã hoàn thành"
              : "Chưa hoàn thành";
            const statusColor = i.completed ? "green" : "#ff9800";

            return `
                <div style='background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;box-shadow:0 2px 4px rgba(0,0,0,0.05);font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Mã phiếu: ${i.code || i.id}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Ngày nhập hàng: ${formatDateVN(i.date)}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Nhà cung cấp: ${i.supplier || "Chưa cập nhật"}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:12px;font-family:Arial,sans-serif;font-weight:normal;'>Tình trạng: <span style='color:${statusColor};'>${statusText}</span></div>
                    
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Chi tiết xe đã nhập:</div>
                        ${itemsHtml}
                    </div>
                    
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1em;margin-bottom:4px;font-family:Arial,sans-serif;font-weight:normal;'>Tạm tính: ${formatPrice(i.subtotal || i.items.reduce((s, it) => s + (Number(it.price) || 0) * Number(it.qty), 0))} VNĐ</div>
                        <div style='color:#000;font-size:1em;margin-bottom:4px;font-family:Arial,sans-serif;font-weight:normal;'>Thuế (10%): ${formatPrice(i.tax || (i.subtotal || i.items.reduce((s, it) => s + (Number(it.price) || 0) * Number(it.qty), 0)) * 0.1)} VNĐ</div>
                        <div style='color:#000;font-size:1.1em;text-align:right;font-family:Arial,sans-serif;font-weight:normal;'>Tổng tiền: ${formatPrice(i.total || (i.subtotal || i.items.reduce((s, it) => s + (Number(it.price) || 0) * Number(it.qty), 0)) * 1.1)} VNĐ</div>
                    </div>
                    
                    ${i.note ? `<div style='color:#000;font-size:1em;margin-top:12px;padding:8px;background:#fff3cd;border-radius:6px;font-family:Arial,sans-serif;font-weight:normal;'>Ghi chú: ${i.note}</div>` : ""}
                    
                    <div style='margin-top:16px;display:flex;gap:8px;justify-content:flex-end;'>
                        ${
                          !i.completed
                            ? `
                            <button onclick="return false;" class="edit-btn" style="padding:6px 10px;font-size:0.9em;border-radius:6px;background:#007bff;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;width:100px;white-space:nowrap;">
                                <i class="fas fa-edit" style="font-size:0.85em;"></i> <span>Sửa</span>
                            </button>
                            <button onclick="return false;" class="save-btn" style="padding:6px 10px;font-size:0.9em;border-radius:6px;background:#28a745;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;width:130px;white-space:nowrap;">
                                <i class="fas fa-check" style="font-size:0.85em;"></i> <span>Hoàn thành</span>
                            </button>
                        `
                            : `
                            <span style="padding:6px 12px;font-size:0.9em;background:#e8f5e9;color:#2e7d32;border-radius:6px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas fa-check-circle"></i> Đã hoàn thành
                            </span>
                        `
                        }
                    </div>
                </div>
            `;
          })
          .join("")}
    </div>`;
}

function filterImports() {
  const importsGrid = document.getElementById("importsGrid");
  if (!importsGrid) return false;

  // Hiển thị 1 phiếu nhập hàng mẫu
  const sampleImport = {
    id: "PN001",
    code: "PN001",
    date: "2025-11-11",
    supplier: "Toyota Việt Nam",
    completed: false,
    items: [
      {
        brand: "Toyota",
        name: "Camry",
        qty: 5,
        price: 1100000000,
      },
    ],
    subtotal: 5500000000,
    tax: 550000000,
    total: 6050000000,
    note: "Nhập lô hàng mới tháng 11/2025",
  };

  importsGrid.innerHTML = `
        <div style="margin-bottom: 20px;">
            <button onclick="loadImports()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-undo"></i> Quay lại
            </button>
        </div>
        ${renderImportsTable([sampleImport])}
    `;
  return false;
}

// Các chức năng modal/form thêm/sửa phiếu nhập đã bị vô hiệu hóa (Prototype mode)

// ========== Quản lý tồn kho ==========
function initOldStockData() {
  let oldStock = JSON.parse(localStorage.getItem("oldStock")) || [];
  if (oldStock.length === 0) {
    const sampleOldStock = [
      {
        id: "TK001",
        brand: "Toyota",
        name: "Fortuner 2023",
        year: 2023,
        category: "suv",
        originalPrice: 1450000000,
        discount: 10,
        stockDate: "15/09/2023",
        daysInStock: 410,
        quantity: 3,
        lowStock: false,
        reason: "Ít người mua do giá cao",
        image: "assets/images/toyota-fortuner.jpg",
      },
      {
        id: "TK002",
        brand: "Honda",
        name: "Accord 2023",
        year: 2023,
        category: "sedan",
        originalPrice: 1319000000,
        discount: 15,
        stockDate: "20/08/2023",
        daysInStock: 436,
        quantity: 2,
        lowStock: false,
        reason: "Màu sắc ít phổ biến",
        image: "assets/images/honda-accord.jpg",
      },
      {
        id: "TK003",
        brand: "Mazda",
        name: "CX-8 2023",
        year: 2023,
        category: "suv",
        originalPrice: 1179000000,
        discount: 20,
        stockDate: "10/07/2023",
        daysInStock: 477,
        quantity: 5,
        lowStock: false,
        reason: "Model cũ, sắp có phiên bản mới",
        image: "assets/images/mazda-cx8.jpg",
      },
      {
        id: "TK004",
        brand: "Hyundai",
        name: "Santa Fe 2024",
        year: 2024,
        category: "suv",
        originalPrice: 1340000000,
        discount: 5,
        stockDate: "15/10/2024",
        daysInStock: 15,
        quantity: 1,
        lowStock: true,
        reason: "Bán chạy, sắp hết hàng",
        image: "assets/images/hyundai-santafe.jpg",
      },
      {
        id: "TK005",
        brand: "KIA",
        name: "Sorento 2024",
        year: 2024,
        category: "suv",
        originalPrice: 1149000000,
        discount: 3,
        stockDate: "20/10/2024",
        daysInStock: 10,
        quantity: 1,
        lowStock: true,
        reason: "Nhu cầu cao, cần nhập thêm",
        image: "assets/images/kia-sorento.jpg",
      },
      {
        id: "TK006",
        brand: "Ford",
        name: "Everest 2024",
        year: 2024,
        category: "suv",
        originalPrice: 1525000000,
        discount: 2,
        stockDate: "25/10/2024",
        daysInStock: 5,
        quantity: 1,
        lowStock: true,
        reason: "Xe hot, gần hết hàng",
        image: "assets/images/ford-everest.jpg",
      },
      {
        id: "TK007",
        brand: "Toyota",
        name: "Vios 2024",
        year: 2024,
        category: "sedan",
        originalPrice: 558000000,
        discount: 0,
        stockDate: "27/10/2024",
        daysInStock: 3,
        quantity: 1,
        lowStock: true,
        reason: "Xe bán chạy nhất phân khúc",
        image: "assets/images/toyota-vios.jpg",
      },
      {
        id: "TK008",
        brand: "Honda",
        name: "City 2024",
        year: 2024,
        category: "sedan",
        originalPrice: 599000000,
        discount: 0,
        stockDate: "01/08/2024",
        daysInStock: 90,
        quantity: 4,
        lowStock: false,
        normalStock: true,
        reason: "Xe sedan hạng B phổ biến",
        image: "assets/images/honda-city.jpg",
      },
      {
        id: "TK009",
        brand: "Mazda",
        name: "CX-5 2024",
        year: 2024,
        category: "suv",
        originalPrice: 859000000,
        discount: 0,
        stockDate: "15/07/2024",
        daysInStock: 107,
        quantity: 6,
        lowStock: false,
        normalStock: true,
        reason: "SUV 5 chỗ được ưa chuộng",
        image: "assets/images/mazda-cx5.jpg",
      },
      {
        id: "TK010",
        brand: "Hyundai",
        name: "Tucson 2024",
        year: 2024,
        category: "suv",
        originalPrice: 769000000,
        discount: 0,
        stockDate: "20/08/2024",
        daysInStock: 71,
        quantity: 5,
        lowStock: false,
        normalStock: true,
        reason: "Thiết kế hiện đại, tiện nghi",
        image: "assets/images/hyundai-tucson.jpg",
      },
      {
        id: "TK011",
        brand: "Ford",
        name: "Ranger 2024",
        year: 2024,
        category: "pickup",
        originalPrice: 799000000,
        discount: 0,
        stockDate: "05/09/2024",
        daysInStock: 55,
        quantity: 3,
        lowStock: false,
        normalStock: true,
        reason: "Bán tải bán chạy nhất",
        image: "assets/images/ford-ranger.jpg",
      },
      {
        id: "TK012",
        brand: "Mitsubishi",
        name: "Xpander 2024",
        year: 2024,
        category: "suv",
        originalPrice: 555000000,
        discount: 0,
        stockDate: "10/09/2024",
        daysInStock: 50,
        quantity: 7,
        lowStock: false,
        normalStock: true,
        reason: "MPV 7 chỗ tiết kiệm",
        image: "assets/images/mitsubishi-xpander.jpg",
      },
    ];
    localStorage.setItem("oldStock", JSON.stringify(sampleOldStock));
    oldStock = sampleOldStock;
  }
  return oldStock;
}

function loadOldStock() {
  const allStock = initOldStockData();

  // Tách xe theo 3 loại
  const lowStockItems = allStock.filter((item) => item.lowStock === true);
  const normalStockItems = allStock.filter((item) => item.normalStock === true);
  const oldStockItems = allStock.filter(
    (item) => !item.lowStock && !item.normalStock,
  );

  // Render xe sắp hết hàng
  const lowStockContainer = document.getElementById("lowStockList");
  if (lowStockContainer) {
    if (lowStockItems.length === 0) {
      lowStockContainer.innerHTML =
        '<div style="color:#666;font-style:italic;">Không có sản phẩm nào sắp hết hàng.</div>';
    } else {
      lowStockContainer.innerHTML = renderStockItems(lowStockItems, "warning");
    }
  }

  // Render xe thường
  const normalStockContainer = document.getElementById("normalStockList");
  if (normalStockContainer) {
    if (normalStockItems.length === 0) {
      normalStockContainer.innerHTML =
        '<div style="color:#666;font-style:italic;">Không có sản phẩm tồn kho thường.</div>';
    } else {
      normalStockContainer.innerHTML = renderStockItems(
        normalStockItems,
        "normal",
      );
    }
  }

  // Render xe tồn kho lâu
  const oldStockContainer = document.getElementById("oldStockList");
  if (oldStockContainer) {
    if (oldStockItems.length === 0) {
      oldStockContainer.innerHTML =
        '<div style="color:#666;font-style:italic;">Không có sản phẩm tồn kho lâu.</div>';
    } else {
      oldStockContainer.innerHTML = renderStockItems(oldStockItems, "old");
    }
  }
}

function renderStockItems(items, type) {
  // type: 'warning' (sắp hết), 'normal' (thường), hoặc 'old' (tồn lâu)
  const borderColor =
    type === "warning" ? "#ff9800" : type === "normal" ? "#28a745" : "#dc3545";
  const badgeColor =
    type === "warning" ? "#ff9800" : type === "normal" ? "#28a745" : "#dc3545";

  return `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${items
          .map((item, index) => {
            // Tính giá sau giảm dựa trên discount
            const currentPrice = item.originalPrice * (1 - item.discount / 100);
            const savedAmount = item.originalPrice - currentPrice;

            // Xử lý tên loại xe
            const categoryName =
              item.category === "suv"
                ? "SUV"
                : item.category === "sedan"
                  ? "Sedan"
                  : item.category === "hatchback"
                    ? "Hatchback"
                    : item.category === "pickup"
                      ? "Pickup"
                      : "Sedan";

            // Xử lý số lượng tồn (mặc định là 1 nếu chưa có)
            const quantity = item.quantity || 1;

            return `
            <div style='background:#fff;border:2px solid ${borderColor};border-radius:10px;padding:16px;box-shadow:0 3px 8px rgba(220,53,69,0.15);font-family:Arial,sans-serif;position:relative;'>
                ${
                  item.discount > 0
                    ? `<div style='position:absolute;top:10px;right:10px;background:${badgeColor};color:#fff;padding:6px 12px;border-radius:20px;font-weight:600;font-size:0.9em;'>
                    Giảm ${item.discount}%
                </div>`
                    : ""
                }
                <div style='color:#333;font-size:1.1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:600;margin-top:8px;'>${item.brand} ${item.name}</div>
                <div style='color:#666;font-size:0.95em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Mã xe: ${item.id}</div>
                <div style='margin-bottom:12px;'>
                    <span style='display:inline-block;background:#e3f2fd;color:#1976d2;padding:5px 10px;border-radius:6px;font-weight:600;font-size:0.85em;margin-right:8px;'>
                        ${categoryName}
                    </span>
                    <span style='display:inline-block;background:${quantity <= 1 ? "#ffebee" : "#e8f5e9"};color:${quantity <= 1 ? "#c62828" : "#2e7d32"};padding:5px 10px;border-radius:6px;font-weight:600;font-size:0.85em;'>
                        <i class="fas fa-warehouse" style="margin-right:4px;"></i>SL tồn: ${quantity}
                    </span>
                </div>
                
                <div style='margin:12px 0;padding:12px;background:#fff3cd;border-radius:6px;border-left:4px solid #ffc107;'>
                    <div style='color:#856404;font-size:0.95em;font-family:Arial,sans-serif;font-weight:normal;'>
                        <i class="fas fa-clock" style="margin-right:6px;"></i>Tồn kho: ${item.daysInStock} ngày (Từ ${item.stockDate})
                    </div>
                </div>
                
                <div style='margin-top:12px;padding:12px;background:#f8f9fa;border-radius:6px;border:1px solid #dee2e6;'>
                    <label style='display:block;margin-bottom:8px;font-weight:600;color:#495057;font-family:Arial,sans-serif;font-size:0.95em;'>
                        Sửa % giảm giá:
                    </label>
                    <div style='display:flex;gap:8px;align-items:center;'>
                        <input 
                            type="number" 
                            value="${item.discount}" 
                            min="0" 
                            max="100" 
                            step="1"
                            style='flex:1;padding:8px 10px;border:1px solid #ced4da;border-radius:6px;font-size:1em;font-family:Arial,sans-serif;'
                        />
                        <span style='color:#6c757d;font-size:1em;font-weight:600;'>%</span>
                    </div>
                </div>
                
                <div style='margin-top:12px;'>
                    ${item.discount > 0 ? `<div style='color:#666;font-size:0.95em;margin-bottom:6px;font-family:Arial,sans-serif;font-weight:normal;text-decoration:line-through;'>Giá gốc: ${formatPrice(item.originalPrice)} VNĐ</div>` : ""}
                    <div style='color:${type === "warning" ? "#ff9800" : type === "normal" ? "#28a745" : "#dc3545"};font-size:1.2em;font-family:Arial,sans-serif;font-weight:600;'>${item.discount > 0 ? "Giá ưu đãi" : "Giá bán"}: ${formatPrice(currentPrice)} VNĐ</div>
                    ${item.discount > 0 ? `<div style='color:#28a745;font-size:0.95em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Tiết kiệm: ${formatPrice(savedAmount)} VNĐ</div>` : ""}
                </div>
                
                <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                    <div style='color:#666;font-size:0.9em;font-family:Arial,sans-serif;font-weight:normal;font-style:italic;'>
                        <i class="fas fa-info-circle" style="margin-right:6px;"></i>${item.reason}
                    </div>
                </div>
            </div>
        `;
          })
          .join("")}
    </div>`;
}

function loadOldStock_original() {
  const oldStock = initOldStockData();
  const container = document.getElementById("oldStockList");
  if (!container) return;

  const html = `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${oldStock
          .map((item, index) => {
            // Tính giá sau giảm dựa trên discount
            const currentPrice = item.originalPrice * (1 - item.discount / 100);
            const savedAmount = item.originalPrice - currentPrice;

            // Xử lý tên loại xe
            const categoryName =
              item.category === "suv"
                ? "SUV"
                : item.category === "sedan"
                  ? "Sedan"
                  : item.category === "hatchback"
                    ? "Hatchback"
                    : item.category === "pickup"
                      ? "Pickup"
                      : "Sedan";

            // Xử lý số lượng tồn (mặc định là 1 nếu chưa có)
            const quantity = item.quantity || 7;

            return `
            <div style='background:#fff;border:2px solid #dc3545;border-radius:10px;padding:16px;box-shadow:0 3px 8px rgba(220,53,69,0.15);font-family:Arial,sans-serif;position:relative;'>
                <div style='position:absolute;top:10px;right:10px;background:#dc3545;color:#fff;padding:6px 12px;border-radius:20px;font-weight:600;font-size:0.9em;'>
                    Giảm ${item.discount}%
                </div>
                <div style='color:#333;font-size:1.1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:600;margin-top:8px;'>${item.brand} ${item.name}</div>
                <div style='color:#666;font-size:0.95em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Mã xe: ${item.id}</div>
                <div style='margin-bottom:12px;'>
                    <span style='display:inline-block;background:#e3f2fd;color:#1976d2;padding:5px 10px;border-radius:6px;font-weight:600;font-size:0.85em;margin-right:8px;'>
                        ${categoryName}
                    </span>
                    <span style='display:inline-block;background:#e8f5e9;color:#2e7d32;padding:5px 10px;border-radius:6px;font-weight:600;font-size:0.85em;'>
                        <i class="fas fa-warehouse" style="margin-right:4px;"></i>SL tồn: ${quantity}
                    </span>
                </div>
                
                <div style='margin:12px 0;padding:12px;background:#fff3cd;border-radius:6px;border-left:4px solid #ffc107;'>
                    <div style='color:#856404;font-size:0.95em;font-family:Arial,sans-serif;font-weight:normal;'>
                        <i class="fas fa-clock" style="margin-right:6px;"></i>Tồn kho: ${item.daysInStock} ngày (Từ ${item.stockDate})
                    </div>
                </div>
                
                <div style='margin-top:12px;padding:12px;background:#f8f9fa;border-radius:6px;border:1px solid #dee2e6;'>
                    <label style='display:block;margin-bottom:8px;font-weight:600;color:#495057;font-family:Arial,sans-serif;font-size:0.95em;'>
                        Sửa % giảm giá:
                    </label>
                    <div style='display:flex;gap:8px;align-items:center;'>
                        <input 
                            type="number" 
                            value="${item.discount}" 
                            min="0" 
                            max="100" 
                            step="1"
                            style='flex:1;padding:8px 10px;border:1px solid #ced4da;border-radius:6px;font-size:1em;font-family:Arial,sans-serif;'
                        />
                        <span style='color:#6c757d;font-size:1em;font-weight:600;'>%</span>
                    </div>
                </div>
                
                <div style='margin-top:12px;'>
                    <div style='color:#666;font-size:0.95em;margin-bottom:6px;font-family:Arial,sans-serif;font-weight:normal;text-decoration:line-through;'>Giá gốc: ${formatPrice(item.originalPrice)} VNĐ</div>
                    <div style='color:#dc3545;font-size:1.2em;font-family:Arial,sans-serif;font-weight:600;'>Giá ưu đãi: ${formatPrice(currentPrice)} VNĐ</div>
                    <div style='color:#28a745;font-size:0.95em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Tiết kiệm: ${formatPrice(savedAmount)} VNĐ</div>
                </div>
                
                <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                    <div style='color:#666;font-size:0.9em;font-family:Arial,sans-serif;font-weight:normal;font-style:italic;'>
                        <i class="fas fa-info-circle" style="margin-right:6px;"></i>${item.reason}
                    </div>
                </div>
            </div>
        `;
          })
          .join("")}
    </div>`;

  container.innerHTML = html;
}

// ========== Quản lý giá bán ==========
function updateProductMargin(productId, productName) {
  const marginInput = document.getElementById(`margin_${productId}`);
  if (!marginInput) return;

  const newMargin = parseFloat(marginInput.value);

  if (isNaN(newMargin) || newMargin < 0 || newMargin > 500) {
    showToast("Vui lòng nhập % lợi nhuận hợp lệ (0-500)", 3000, "error");
    return;
  }

  // Xác nhận trước khi cập nhật
  if (
    !confirm(
      `Cập nhật lợi nhuận cho "${productName}" thành ${newMargin.toFixed(1)}%?\n\nGiá bán sẽ được tính lại tự động.`,
    )
  ) {
    return;
  }

  marginInput.disabled = true;
  const btn = document.querySelector(
    `button[onclick="updateProductMargin(${productId}"]`,
  );
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
  }

  fetch("/WebBasic/BackEnd/api/pricing.php", {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "update_margin",
      product_id: productId,
      profit_margin: newMargin,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast(
          `✓ Cập nhật thành công!\nGiá bán mới: ${formatPrice(data.data.selling_price)}`,
          2000,
          "success",
        );
        setTimeout(() => loadPricing(), 1500);
      } else {
        showToast(
          "❌ Lỗi: " + (data.message || "Cập nhật thất bại"),
          3000,
          "error",
        );
        marginInput.disabled = false;
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-save"></i> Cập nhật';
        }
      }
    })
    .catch((err) => {
      console.error("Error:", err);
      showToast("❌ Lỗi kết nối: " + err.message, 3000, "error");
      marginInput.disabled = false;
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Cập nhật';
      }
    });
}

function showToast(message, duration = 2000, type = "info") {
  const toast = document.createElement("div");
  const bgColor =
    type === "success" ? "#4CAF50" : type === "error" ? "#f44336" : "#2196F3";
  toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 14px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
        max-width: 300px;
    `;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "slideOut 0.3s ease-out";
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// ========== Quản lý giá bán ==========
function initPricingData() {
  // Dữ liệu mẫu cho quản lý giá bán (lấy từ sản phẩm có sẵn)
  const products = JSON.parse(localStorage.getItem("products")) || [];

  // Nếu chưa có sản phẩm, tạo dữ liệu mẫu
  if (products.length === 0) {
    const sampleProducts = [
      {
        id: 1,
        name: "Camry 2024",
        brand: "Toyota",
        category: "sedan",
        price: 1235000000,
        profitMargin: 8.5,
        sellingPrice: 1339975000,
        image: "assets/images/toyota-camry.jpg",
      },
      {
        id: 2,
        name: "CR-V 2024",
        brand: "Honda",
        category: "suv",
        price: 1029000000,
        profitMargin: 10,
        sellingPrice: 1131900000,
        image: "assets/images/honda-crv.jpg",
      },
      {
        id: 3,
        name: "Mazda3 2024",
        brand: "Mazda",
        category: "sedan",
        price: 669000000,
        profitMargin: 12,
        sellingPrice: 749280000,
        image: "assets/images/mazda3.jpg",
      },
      {
        id: 4,
        name: "VF 8 2024",
        brand: "VinFast",
        category: "suv",
        price: 999000000,
        profitMargin: 7,
        sellingPrice: 1068930000,
        image: "assets/images/vinfast-vf8.jpg",
      },
    ];
    return sampleProducts;
  }

  // Thêm profitMargin và sellingPrice cho sản phẩm nếu chưa có
  return products.map((p) => {
    if (!p.profitMargin) p.profitMargin = 10; // Mặc định 10%
    if (!p.sellingPrice) p.sellingPrice = p.price * (1 + p.profitMargin / 100);
    return p;
  });
}

function loadPricing() {
  const pricingGrid = document.getElementById("pricingGrid");
  if (!pricingGrid) return;

  pricingGrid.innerHTML =
    '<div style="text-align:center;padding:40px;"><p>Đang tải dữ liệu...</p></div>';

  // Gọi API backend để lấy dữ liệu giá từ database
  fetch("/WebBasic/BackEnd/api/pricing.php?action=list&limit=500")
    .then((response) => response.json())
    .then((data) => {
      if (!data.success || !data.data || data.data.length === 0) {
        pricingGrid.innerHTML =
          '<div class="empty-state">Chưa có sản phẩm nào.</div>';
        return;
      }

      const products = data.data;
      const categories = JSON.parse(localStorage.getItem("categories")) || [];

      if (categoryFilter) {
        categoryFilter.innerHTML =
          '<option value="">Tất cả loại xe</option>' +
          categories
            .map((cat) => `<option value="${cat.id}">${cat.name}</option>`)
            .join("");
      }

      if (!products.length) {
        pricingGrid.innerHTML =
          '<div class="empty-state">Chưa có sản phẩm nào.</div>';
        return;
      }

      const html = `
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                        <thead>
                            <tr style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;">
                                <th style="padding:14px;text-align:left;font-weight:600;min-width:200px;">Tên sản phẩm</th>
                                <th style="padding:14px;text-align:left;font-weight:600;min-width:120px;">Loại xe</th>
                                <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá nhập (VNĐ)</th>
                                <th style="padding:14px;text-align:center;font-weight:600;min-width:140px;">% Lợi nhuận</th>
                                <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá bán (VNĐ)</th>
                                <th style="padding:14px;text-align:center;font-weight:600;min-width:100px;">Tồn kho</th>
                                <th style="padding:14px;text-align:center;font-weight:600;min-width:120px;">Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products
                              .map((product, index) => {
                                const costPrice = product.cost_price || 0;
                                const profitMargin = product.profit_margin || 0;
                                const sellingPrice = product.selling_price || 0;
                                const stock = product.stock || 0;
                                const categoryName =
                                  product.category_name || "N/A";
                                const profitAmount = sellingPrice - costPrice;

                                return `
                                <tr style="border-bottom:1px solid #f0f0f0;transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
                                    <td style="padding:12px;">
                                        <div style="font-weight:600;color:#333;margin-bottom:4px;">${product.name}</div>
                                        <div style="font-size:0.85em;color:#666;">ID: ${product.id}</div>
                                    </td>
                                    <td style="padding:12px;color:#555;">${categoryName}</td>
                                    <td style="padding:12px;text-align:right;font-weight:500;color:#333;">${formatPrice(costPrice)}</td>
                                    <td style="padding:12px;text-align:center;">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                            <input type="number" id="margin_${product.id}" value="${profitMargin.toFixed(1)}" min="0" max="500" step="0.1" 
                                                style="width:60px;padding:6px 8px;border:1px solid #ddd;border-radius:6px;text-align:center;font-weight:600;font-size:0.9em;">
                                            <span style="font-weight:600;color:#2e7d32;">%</span>
                                        </div>
                                    </td>
                                    <td style="padding:12px;text-align:right;">
                                        <div style="font-weight:600;color:#0d279d;font-size:1.05em;">${formatPrice(sellingPrice)}</div>
                                        <div style="font-size:0.85em;color:#28a745;margin-top:4px;">+${formatPrice(profitAmount)}</div>
                                    </td>
                                    <td style="padding:12px;text-align:center;">
                                        <span style="display:inline-block;background:#${stock > 0 ? "e8f5e9;color:#2e7d32" : "ffebee;color:#c62828"};padding:6px 12px;border-radius:6px;font-weight:600;font-size:0.9em;">${stock} chiếc</span>
                                    </td>
                                    <td style="padding:12px;text-align:center;">
                                        <button type="button" style="background:#4CAF50;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;font-weight:600;font-size:0.85em;transition:background 0.2s;"
                                            onmouseover="this.style.background='#45a049'" onmouseout="this.style.background='#4CAF50'"
                                            onclick="updateProductMargin(${product.id}, '${product.name}')">
                                            <i class="fas fa-save"></i> Cập nhật
                                        </button>
                                    </td>
                                </tr>
                                `;
                              })
                              .join("")}
                        </tbody>
                    </table>
                </div>
            `;

      pricingGrid.innerHTML = html;
    })
    .catch((err) => {
      console.error("Error loading pricing data:", err);
      pricingGrid.innerHTML =
        '<div style="color:red;padding:20px;">Lỗi khi tải dữ liệu giá. Vui lòng kiểm tra kết nối.</div>';
    });
}

function filterPricing() {
  const searchInput =
    document.getElementById("pricingSearchProduct")?.value.toLowerCase() || "";
  const categoryFilter =
    document.getElementById("pricingCategoryFilter")?.value || "";
  const pricingGrid = document.getElementById("pricingGrid");
  if (!pricingGrid) return;

  pricingGrid.innerHTML =
    '<div style="text-align:center;padding:40px;"><p>Đang tải dữ liệu...</p></div>';

  // Gọi API backend với tham số lọc
  let apiUrl = "/WebBasic/BackEnd/api/pricing.php?action=list&limit=500";
  if (searchInput) {
    apiUrl += "&search=" + encodeURIComponent(searchInput);
  }
  if (categoryFilter) {
    apiUrl += "&categoryId=" + encodeURIComponent(categoryFilter);
  }

  fetch(apiUrl)
    .then((response) => response.json())
    .then((data) => {
      if (!data.success || !data.data || data.data.length === 0) {
        pricingGrid.innerHTML =
          '<div class="empty-state">Không tìm thấy sản phẩm phù hợp.</div>';
        return;
      }

      const products = data.data;

      if (!products.length) {
        pricingGrid.innerHTML =
          '<div class="empty-state">Không tìm thấy sản phẩm phù hợp.</div>';
        return;
      }

      const html = `
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                        <thead>
                            <tr style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;">
                                <th style="padding:14px;text-align:left;font-weight:600;min-width:200px;">Tên sản phẩm</th>
                                <th style="padding:14px;text-align:left;font-weight:600;min-width:120px;">Loại xe</th>
                                <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá nhập (VNĐ)</th>
                                <th style="padding:14px;text-align:center;font-weight:600;min-width:140px;">% Lợi nhuận</th>
                                <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá bán (VNĐ)</th>
                                <th style="padding:14px;text-align:center;font-weight:600;min-width:100px;">Tồn kho</th>
                                <th style="padding:14px;text-align:center;font-weight:600;min-width:120px;">Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products
                              .map((product, index) => {
                                const costPrice = product.cost_price || 0;
                                const profitMargin = product.profit_margin || 0;
                                const sellingPrice = product.selling_price || 0;
                                const stock = product.stock || 0;
                                const categoryName =
                                  product.category_name || "N/A";
                                const profitAmount = sellingPrice - costPrice;

                                return `
                                <tr style="border-bottom:1px solid #f0f0f0;transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
                                    <td style="padding:12px;">
                                        <div style="font-weight:600;color:#333;margin-bottom:4px;">${product.name}</div>
                                        <div style="font-size:0.85em;color:#666;">ID: ${product.id}</div>
                                    </td>
                                    <td style="padding:12px;color:#555;">${categoryName}</td>
                                    <td style="padding:12px;text-align:right;font-weight:500;color:#333;">${formatPrice(costPrice)}</td>
                                    <td style="padding:12px;text-align:center;">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                            <input type="number" id="margin_${product.id}" value="${profitMargin.toFixed(1)}" min="0" max="500" step="0.1" 
                                                style="width:60px;padding:6px 8px;border:1px solid #ddd;border-radius:6px;text-align:center;font-weight:600;font-size:0.9em;">
                                            <span style="font-weight:600;color:#2e7d32;">%</span>
                                        </div>
                                    </td>
                                    <td style="padding:12px;text-align:right;">
                                        <div style="font-weight:600;color:#0d279d;font-size:1.05em;">${formatPrice(sellingPrice)}</div>
                                        <div style="font-size:0.85em;color:#28a745;margin-top:4px;">+${formatPrice(profitAmount)}</div>
                                    </td>
                                    <td style="padding:12px;text-align:center;">
                                        <span style="display:inline-block;background:#${stock > 0 ? "e8f5e9;color:#2e7d32" : "ffebee;color:#c62828"};padding:6px 12px;border-radius:6px;font-weight:600;font-size:0.9em;">${stock} chiếc</span>
                                    </td>
                                    <td style="padding:12px;text-align:center;">
                                        <button type="button" style="background:#4CAF50;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;font-weight:600;font-size:0.85em;transition:background 0.2s;"
                                            onmouseover="this.style.background='#45a049'" onmouseout="this.style.background='#4CAF50'"
                                            onclick="updateProductMargin(${product.id}, '${product.name}')">
                                            <i class="fas fa-save"></i> Cập nhật
                                        </button>
                                    </td>
                                </tr>
                                `;
                              })
                              .join("")}
                        </tbody>
                    </table>
                </div>
            `;

      pricingGrid.innerHTML = html;
    })
    .catch((err) => {
      console.error("Error filtering pricing data:", err);
      pricingGrid.innerHTML =
        '<div style="color:red;padding:20px;">Lỗi khi tải dữ liệu giá. Vui lòng kiểm tra kết nối.</div>';
    });
}

// Export functions (chỉ giữ load và filter)
window.loadPricing = loadPricing;
window.filterPricing = filterPricing;

// Filter cho đơn hàng đã bị vô hiệu hóa (Prototype mode)
window.filterAdminOrders = filterAdminOrders;

// ========== Quản lý đặt lại mật khẩu khách hàng ==========
function showResetPasswordModal(email, fullName) {
  // Tạo modal HTML
  const modalHTML = `
        <div id="resetPasswordModal" class="modal" style="display: flex;">
            <div class="modal-content" style="max-width: 450px;">
                <div class="modal-header">
                    <h3><i class="fas fa-key"></i> Đặt lại mật khẩu</h3>
                    <span class="close" onclick="closeResetPasswordModal()">&times;</span>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div style="margin-bottom: 16px; padding: 12px; background: #e3f2fd; border-radius: 6px; border-left: 4px solid #2196f3;">
                        <div style="font-weight: 600; color: #1976d2; margin-bottom: 4px;">Khách hàng:</div>
                        <div style="color: #333;">${fullName}</div>
                        <div style="color: #666; font-size: 0.9em; margin-top: 4px;">${email}</div>
                    </div>
                    <form id="resetPasswordForm" onsubmit="return false;">
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="newPassword" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                <i class="fas fa-lock"></i> Mật khẩu mới:
                            </label>
                            <input type="password" id="newPassword" placeholder="Nhập mật khẩu mới" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="confirmPassword" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                <i class="fas fa-lock"></i> Xác nhận mật khẩu:
                            </label>
                            <input type="password" id="confirmPassword" placeholder="Nhập lại mật khẩu mới" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em;" required>
                        </div>
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="button" onclick="closeResetPasswordModal()" 
                                style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <button type="submit" 
                                style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-save"></i> Lưu mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

  // Thêm modal vào body
  document.body.insertAdjacentHTML("beforeend", modalHTML);

  // Đóng modal khi click bên ngoài
  document
    .getElementById("resetPasswordModal")
    .addEventListener("click", function (e) {
      if (e.target === this) {
        closeResetPasswordModal();
      }
    });

  // Xử lý form submit (prototype mode - không làm gì cả)
  document
    .getElementById("resetPasswordForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      // Prototype mode: không làm gì cả khi ấn lưu
      return false;
    });
}

function closeResetPasswordModal() {
  const modal = document.getElementById("resetPasswordModal");
  if (modal) {
    modal.remove();
  }
}

// Export functions
window.showResetPasswordModal = showResetPasswordModal;
window.closeResetPasswordModal = closeResetPasswordModal;

// ========== Quản lý modal thêm phiếu nhập ==========
function showAddImportModal() {
  const modal = document.getElementById("addImportModal");
  if (!modal) return;

  // Reset form
  document.getElementById("addImportForm").reset();

  // Tự động set ngày hiện tại
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("importDate").value = today;

  // Load danh sách sản phẩm vào dropdown
  loadProductsToImportModal();

  // Hiển thị modal
  modal.style.display = "block";
}

function closeAddImportModal() {
  const modal = document.getElementById("addImportModal");
  if (modal) {
    modal.style.display = "none";
  }
}

function loadProductsToImportModal() {
  const products = JSON.parse(localStorage.getItem("products")) || [];
  const selects = document.querySelectorAll(".import-product-select");

  const optionsHTML =
    '<option value="">-- Chọn sản phẩm --</option>' +
    products
      .map(
        (p) =>
          `<option value="${p.id}">${p.brand} ${p.name} (${p.year || ""})</option>`,
      )
      .join("");

  selects.forEach((select) => {
    select.innerHTML = optionsHTML;
  });
}

function addImportItemRow() {
  // Prototype mode: không làm gì cả
  return false;
}

function removeImportItemRow(button) {
  // Prototype mode: không làm gì cả
  return false;
}

// Hiển thị 1 xe Toyota Camry khi tìm kiếm
function showSingleProduct() {
  const productsGrid = document.getElementById("products-grid");
  if (!productsGrid) return;

  // Hiển thị 1 xe Toyota Camry
  productsGrid.innerHTML = `
        <div class="products-search-bar" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <i class="fas fa-search" style="color: #666;"></i>
                <input type="text" id="productSearchInput" placeholder="Tìm kiếm sản phẩm..." 
                    style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; font-family: Arial, sans-serif;"
                    onkeyup="return false;">
                <button onclick="showSingleProduct()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
            <button onclick="loadProducts()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-undo"></i> Quay lại
            </button>
        </div>
        <div class="products-list">
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/toyota-camry.jpg" alt="Toyota Camry">
                </div>
                <div class="product-info">
                    <h3>Toyota Camry</h3>
                    <p class="brand">TOYOTA</p>
                    <p class="price">1.235.000.000 VNĐ</p>
                    <div class="product-details">
                        <span><i class="fas fa-calendar"></i> 2025</span>
                        <span><i class="fas fa-gas-pump"></i> Xăng</span>
                        <span><i class="fas fa-cogs"></i> Tự động (AT)</span>
                        <span><i class="fas fa-tags"></i> Sedan</span>
                    </div>
                    <div class="product-actions">
                        <button onclick="editProduct(1)" class="edit-btn" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <button onclick="return false;" class="hide-btn" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;">
                            <i class="fas fa-eye-slash"></i> Ẩn
                        </button>
                        <button onclick="return false;" class="delete-btn" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Export functions
window.showAddImportModal = showAddImportModal;
window.closeAddImportModal = closeAddImportModal;
window.addImportItemRow = addImportItemRow;

// ========== USER MANAGEMENT FUNCTIONS ==========

// Show modal to add new user
function showAddUserModal() {
  const html = `
        <div id="addUserModal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
            <div style="background:#fff;padding:30px;border-radius:12px;max-width:500px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,0.15);position:relative;">
                <button onclick="document.getElementById('addUserModal').remove()" style="position:absolute;top:12px;right:12px;background:#dc3545;color:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:1.2em;cursor:pointer;">&times;</button>
                <h3 style="margin-bottom:20px;">Thêm khách hàng mới</h3>
                <form id="addUserForm" style="font-family:Arial,sans-serif;">
                    <div style="margin-bottom:15px;">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Họ:</label>
                        <input type="text" id="newUserFirstName" placeholder="Họ" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Tên:</label>
                        <input type="text" id="newUserLastName" placeholder="Tên" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Email:</label>
                        <input type="email" id="newUserEmail" placeholder="Email" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Mật khẩu:</label>
                        <input type="password" id="newUserPassword" placeholder="Mật khẩu" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Điện thoại (tuỳ chọn):</label>
                        <input type="tel" id="newUserPhone" placeholder="Điện thoại" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Tỉnh/TP (tuỳ chọn):</label>
                        <input type="text" id="newUserProvince" placeholder="Tỉnh/TP" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('addUserModal').remove()" style="padding:10px 20px;background:#6c757d;color:#fff;border:none;border-radius:6px;cursor:pointer;">Hủy</button>
                        <button type="button" onclick="addUser()" style="padding:10px 20px;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Thêm khách hàng</button>
                    </div>
                </form>
            </div>
        </div>
    `;
  document.body.insertAdjacentHTML("beforeend", html);
}

// Add new user
function addUser() {
  const firstName = document.getElementById("newUserFirstName").value.trim();
  const lastName = document.getElementById("newUserLastName").value.trim();
  const email = document.getElementById("newUserEmail").value.trim();
  const password = document.getElementById("newUserPassword").value;
  const phone = document.getElementById("newUserPhone").value.trim();
  const province = document.getElementById("newUserProvince").value.trim();

  if (!firstName || !lastName || !email || !password) {
    alert("Vui lòng điền đầy đủ thông tin bắt buộc!");
    return;
  }

  if (password.length < 6) {
    alert("Mật khẩu phải có ít nhất 6 ký tự!");
    return;
  }

  fetch("/WebBasic/BackEnd/api/admin/add_user.php", {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      first_name: firstName,
      last_name: lastName,
      email: email,
      password: password,
      phone: phone,
      province: province,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      return response.text();
    })
    .then((text) => {
      try {
        return JSON.parse(text);
      } catch (e) {
        console.error("Invalid JSON response:", text);
        throw new Error("Server returned invalid JSON. Response: " + text);
      }
    })
    .then((result) => {
      if (result.success) {
        alert("✓ Thêm khách hàng thành công!");
        document.getElementById("addUserModal").remove();
        loadCustomers();
      } else {
        alert("Lỗi: " + (result.message || "Không thể thêm khách hàng"));
      }
    })
    .catch((error) => {
      console.error("Error adding user:", error);
      alert("Lỗi: " + error.message);
    });
}

// Show modal to reset password
function showResetPasswordModal(userId, userName) {
  const newPassword = prompt(`Nhập mật khẩu mới cho ${userName}:`);
  if (!newPassword) return;

  if (newPassword.length < 6) {
    alert("Mật khẩu phải có ít nhất 6 ký tự!");
    return;
  }

  if (!confirm(`Bạn chắc chắn muốn reset mật khẩu cho ${userName}?`)) {
    return;
  }

  resetPassword(userId, newPassword);
}

// Reset user password
function resetPassword(userId, newPassword) {
  fetch("/WebBasic/BackEnd/api/admin/reset_password.php", {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      user_id: userId,
      new_password: newPassword,
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ Reset mật khẩu thành công!");
        loadCustomers();
      } else {
        alert("Lỗi: " + (result.message || "Không thể reset mật khẩu"));
      }
    })
    .catch((error) => {
      console.error("Error resetting password:", error);
      alert("Lỗi: " + error.message);
    });
}

// Lock/Unlock user
function toggleLockUser(userId, userName) {
  if (!confirm(`Bạn chắc chắn muốn khóa tài khoản của ${userName}?`)) {
    return;
  }

  fetch("/WebBasic/BackEnd/api/admin/lock_user.php", {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      user_id: userId,
      locked: 1,
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ Khóa tài khoản thành công!");
        loadCustomers();
      } else {
        alert("Lỗi: " + (result.message || "Không thể khóa tài khoản"));
      }
    })
    .catch((error) => {
      console.error("Error locking user:", error);
      alert("Lỗi: " + error.message);
    });
}

// Delete user
function deleteUser(userId, userName) {
  if (
    !confirm(
      `Bạn chắc chắn muốn xóa tài khoản của ${userName}?\n\nHành động này không thể hoàn tác!`,
    )
  ) {
    return;
  }

  fetch("/WebBasic/BackEnd/api/admin/delete_user.php", {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      user_id: userId,
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ Xóa tài khoản thành công!");
        loadCustomers();
      } else {
        alert("Lỗi: " + (result.message || "Không thể xóa tài khoản"));
      }
    })
    .catch((error) => {
      console.error("Error deleting user:", error);
      alert("Lỗi: " + error.message);
    });
}

// ========== CATEGORY MANAGEMENT FUNCTIONS ==========

function loadCategories() {
  const tbody = document.getElementById("categoriesTableBody");
  if (!tbody) return;

  tbody.innerHTML =
    '<tr><td colspan="4" style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';

  fetch("/WebBasic/BackEnd/api/admin/get_categories.php", {
    method: "GET",
    credentials: "include",
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success && result.data) {
        tbody.innerHTML = "";
        result.data.forEach((cat) => {
          const row = `
                    <tr>
                        <td style="padding:12px;text-align:left;">${cat.name}</td>
                        <td style="padding:12px;text-align:center;"><strong>${cat.product_count}</strong></td>
                        <td style="padding:12px;text-align:center;">
                            <span style="padding:4px 8px;border-radius:4px;background:${cat.status == 1 ? "#d4edda" : "#f8d7da"};color:${cat.status == 1 ? "#155724" : "#721c24"};font-size:0.9em;">${cat.status_text}</span>
                        </td>
                        <td style="padding:12px;text-align:center;display:flex;gap:6px;justify-content:center;">
                            <button onclick="showEditCategoryModal(${cat.id}, '${cat.name}', '${cat.description || ""}', ${cat.status})" class="edit-btn" style="padding:6px 12px;background:#007bff;color:#fff;border:none;border-radius:4px;cursor:pointer;">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <button onclick="deleteCategory(${cat.id}, '${cat.name}')" class="delete-btn" style="padding:6px 12px;background:#dc3545;color:#fff;border:none;border-radius:4px;cursor:pointer;">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </td>
                    </tr>
                `;
          tbody.innerHTML += row;
        });
      } else {
        tbody.innerHTML =
          '<tr><td colspan="4" style="text-align:center;padding:20px;color:#999;">Chưa có loại sản phẩm nào</td></tr>';
      }
    })
    .catch((error) => {
      console.error("Error loading categories:", error);
      tbody.innerHTML =
        '<tr><td colspan="4" style="text-align:center;padding:20px;color:red;">Lỗi tải dữ liệu</td></tr>';
    });
}

function showAddCategoryModal() {
  const html = `
        <div id="addCategoryModal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
            <div style="background:#fff;padding:30px;border-radius:12px;max-width:500px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <h3 style="margin:0;">Thêm loại sản phẩm mới</h3>
                    <button onclick="closeAddCategoryModal()" style="background:#dc3545;color:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:1.2em;cursor:pointer;">&times;</button>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:600;">Tên loại sản phẩm:</label>
                    <input type="text" id="categoryName" placeholder="VD: Toyota, BMW..." required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;font-family:Arial,sans-serif;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:600;">Mô tả (tuỳ chọn):</label>
                    <textarea id="categoryDescription" placeholder="Mô tả loại sản phẩm..." style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;font-family:Arial,sans-serif;" rows="3"></textarea>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="closeAddCategoryModal()" style="padding:10px 20px;background:#6c757d;color:#fff;border:none;border-radius:6px;cursor:pointer;">Hủy</button>
                    <button type="button" onclick="addCategory()" style="padding:10px 20px;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Thêm loại</button>
                </div>
            </div>
        </div>
    `;
  document.body.insertAdjacentHTML("beforeend", html);
}

function closeAddCategoryModal() {
  const modal = document.getElementById("addCategoryModal");
  if (modal) modal.remove();
}

function addCategory() {
  const name = document.getElementById("categoryName")?.value?.trim();
  const description =
    document.getElementById("categoryDescription")?.value?.trim() || "";

  if (!name) {
    alert("Vui lòng nhập tên loại sản phẩm");
    return;
  }

  fetch("/WebBasic/BackEnd/api/admin/add_category.php", {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, description }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ Thêm loại sản phẩm thành công!");
        closeAddCategoryModal();
        loadCategories();
      } else {
        alert("Lỗi: " + (result.message || "Không thể thêm loại"));
      }
    })
    .catch((error) => alert("Lỗi: " + error.message));
}

function showEditCategoryModal(id, name, description, status) {
  const html = `
        <div id="editCategoryModal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
            <div style="background:#fff;padding:30px;border-radius:12px;max-width:500px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <h3 style="margin:0;">Chỉnh sửa loại sản phẩm</h3>
                    <button onclick="closeEditCategoryModal()" style="background:#dc3545;color:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:1.2em;cursor:pointer;">&times;</button>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:600;">Tên:</label>
                    <input type="text" id="editCategoryName" value="${name}" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:600;">Mô tả:</label>
                    <textarea id="editCategoryDescription" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;" rows="3">${description}</textarea>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:600;">Trạng thái:</label>
                    <select id="editCategoryStatus" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        <option value="1" ${status == 1 ? "selected" : ""}>Đang hiển thị</option>
                        <option value="0" ${status == 0 ? "selected" : ""}>Đang ẩn</option>
                    </select>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="closeEditCategoryModal()" style="padding:10px 20px;background:#6c757d;color:#fff;border:none;border-radius:6px;cursor:pointer;">Hủy</button>
                    <button type="button" onclick="submitEditCategory(${id})" style="padding:10px 20px;background:#17a2b8;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Cập nhật</button>
                </div>
            </div>
        </div>
    `;
  document.body.insertAdjacentHTML("beforeend", html);
}

function closeEditCategoryModal() {
  const modal = document.getElementById("editCategoryModal");
  if (modal) modal.remove();
}

function submitEditCategory(id) {
  const name = document.getElementById("editCategoryName")?.value?.trim();
  const description =
    document.getElementById("editCategoryDescription")?.value?.trim() || "";
  const status = parseInt(
    document.getElementById("editCategoryStatus")?.value || "1",
  );

  if (!name) {
    alert("Vui lòng nhập tên loại sản phẩm");
    return;
  }

  fetch("/WebBasic/BackEnd/api/admin/edit_category.php", {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id, name, description, status }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ Cập nhật loại sản phẩm thành công!");
        closeEditCategoryModal();
        loadCategories();
      } else {
        alert("Lỗi: " + (result.message || "Không thể cập nhật"));
      }
    })
    .catch((error) => alert("Lỗi: " + error.message));
}

function deleteCategory(id, name) {
  if (
    !confirm(
      `Bạn chắc chắn muốn xóa loại sản phẩm "${name}"?\n\nVui lòng chắc chắn loại này không còn sản phẩm nào!`,
    )
  ) {
    return;
  }

  fetch("/WebBasic/BackEnd/api/admin/delete_category.php", {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ Xóa loại sản phẩm thành công!");
        loadCategories();
      } else {
        alert("Lỗi: " + (result.message || "Không thể xóa loại sản phẩm"));
      }
    })
    .catch((error) => alert("Lỗi: " + error.message));
}

// ========== PRODUCT EDIT/DELETE FUNCTIONS ==========

function showEditProductModal(id) {
  fetch("/WebBasic/BackEnd/api/admin/get_product.php?id=" + id, {
    method: "GET",
    credentials: "include",
  })
    .then((response) => response.json())
    .then((result) => {
      if (!result.success) {
        alert("Lỗi: " + (result.message || "Không thể tải sản phẩm"));
        return;
      }

      const p = result.data;
      const html = `
            <div id="editProductModal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;overflow-y:auto;">
                <div style="background:#fff;padding:30px;border-radius:12px;max-width:600px;width:100%;margin:20px auto;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="margin:0;">Chỉnh sửa sản phẩm</h3>
                        <button onclick="closeEditProductModal()" style="background:#dc3545;color:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:1.2em;cursor:pointer;">&times;</button>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Mã sản phẩm:</label>
                            <input type="text" id="editProductCode" value="${p.product_code || ""}" placeholder="SKU..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Tên sản phẩm:</label>
                            <input type="text" id="editProductName" value="${p.name}" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Loại sản phẩm:</label>
                            <select id="editProductCategory" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                                <option value="${p.category_id}">${p.category_name}</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Đơn vị tính:</label>
                            <input type="text" id="editProductUnit" value="${p.unit || "chiếc"}" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:15px;">
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Giá bán (VNĐ):</label>
                            <input type="number" id="editProductPrice" value="${p.price}" required min="0" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Giá vốn (VNĐ):</label>
                            <input type="number" id="editProductCost" value="${p.price_cost}" min="0" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Margin (%):</label>
                            <input type="number" id="editProductMargin" value="${p.profit_margin}" min="0" step="0.1" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Số lượng tồn:</label>
                            <input type="number" id="editProductStock" value="${p.stock}" required min="0" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Hình ảnh URL:</label>
                            <input type="url" id="editProductImage" value="${p.image_url || ""}" placeholder="https://..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom:15px;">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Mô tả:</label>
                        <textarea id="editProductDescription" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;" rows="3">${p.description || ""}</textarea>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Trạng thái:</label>
                            <select id="editProductStatus" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;">
                                <option value="1" ${p.status == 1 ? "selected" : ""}>Đang bán</option>
                                <option value="0" ${p.status == 0 ? "selected" : ""}>Ẩn/Không bán</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="closeEditProductModal()" style="padding:10px 20px;background:#6c757d;color:#fff;border:none;border-radius:6px;cursor:pointer;">Hủy</button>
                        <button type="button" onclick="submitEditProduct(${id})" style="padding:10px 20px;background:#17a2b8;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Cập nhật sản phẩm</button>
                        ${p.has_stock_history ? "" : '<button type="button" onclick="confirmDeleteProduct(' + id + ')" style="padding:10px 20px;background:#dc3545;color:#fff;border:none;border-radius:6px;cursor:pointer;">Xóa sản phẩm</button>'}
                    </div>
                </div>
            </div>
        `;
      document.body.insertAdjacentHTML("beforeend", html);
    })
    .catch((error) => alert("Lỗi: " + error.message));
}

function closeEditProductModal() {
  const modal = document.getElementById("editProductModal");
  if (modal) modal.remove();
}

function submitEditProduct(id) {
  const name = document.getElementById("editProductName")?.value?.trim();
  const code = document.getElementById("editProductCode")?.value?.trim() || "";
  const category = parseInt(
    document.getElementById("editProductCategory")?.value || "1",
  );
  const price = parseFloat(
    document.getElementById("editProductPrice")?.value || "0",
  );
  const cost = parseFloat(
    document.getElementById("editProductCost")?.value || "0",
  );
  const margin = parseFloat(
    document.getElementById("editProductMargin")?.value || "0",
  );
  const stock = parseInt(
    document.getElementById("editProductStock")?.value || "0",
  );
  const unit =
    document.getElementById("editProductUnit")?.value?.trim() || "chiếc";
  const image =
    document.getElementById("editProductImage")?.value?.trim() || "";
  const description =
    document.getElementById("editProductDescription")?.value?.trim() || "";
  const status = parseInt(
    document.getElementById("editProductStatus")?.value || "1",
  );

  if (!name || price <= 0) {
    alert("Vui lòng nhập tên và giá bán hợp lệ");
    return;
  }

  fetch("/WebBasic/BackEnd/api/admin/edit_product.php", {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id,
      name,
      product_code: code,
      category_id: category,
      price,
      price_cost: cost,
      profit_margin: margin,
      stock,
      unit,
      image_url: image,
      description,
      status,
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ Cập nhật sản phẩm thành công!");
        closeEditProductModal();
        loadProducts();
      } else {
        alert("Lỗi: " + (result.message || "Không thể cập nhật"));
      }
    })
    .catch((error) => alert("Lỗi: " + error.message));
}

function confirmDeleteProduct(id) {
  if (
    !confirm(
      "Bạn chắc chắn muốn xóa sản phẩm này?\n\nNếu sản phẩm chưa có nhập hàng sẽ xóa hẳn, ngược lại sẽ chỉ ẩn!",
    )
  ) {
    return;
  }
  deleteProduct(id);
}

function deleteProduct(id) {
  fetch("/WebBasic/BackEnd/api/admin/delete_product.php", {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("✓ " + result.message);
        closeEditProductModal();
        loadProducts();
      } else {
        alert("Lỗi: " + (result.message || "Không thể xóa sản phẩm"));
      }
    })
    .catch((error) => alert("Lỗi: " + error.message));
}

// Export user management functions
window.showAddUserModal = showAddUserModal;
window.addUser = addUser;
window.showResetPasswordModal = showResetPasswordModal;
window.resetPassword = resetPassword;
window.toggleLockUser = toggleLockUser;
window.deleteUser = deleteUser;
window.removeImportItemRow = removeImportItemRow;
window.showSingleProduct = showSingleProduct;
window.editProduct = editProduct;
window.showAddProductModal = showAddProductModal;
window.closeAddProductModal = closeAddProductModal;
window.filterAdminOrders = filterAdminOrders;
window.loadAdminOrders = loadAdminOrders;
window.filterImports = filterImports;
window.loadImports = loadImports;
window.loadOldStock = loadOldStock;
window.initStockSection = loadOldStock;
window.loadPricing = loadPricing;
window.loadCategories = loadCategories;
// Export category management functions
window.showAddCategoryModal = showAddCategoryModal;
window.closeAddCategoryModal = closeAddCategoryModal;
window.addCategory = addCategory;
window.showEditCategoryModal = showEditCategoryModal;
window.closeEditCategoryModal = closeEditCategoryModal;
window.submitEditCategory = submitEditCategory;
window.deleteCategory = deleteCategory;
// Export product edit/delete functions
window.showEditProductModal = showEditProductModal;
window.closeEditProductModal = closeEditProductModal;
window.submitEditProduct = submitEditProduct;
window.confirmDeleteProduct = confirmDeleteProduct;
window.deleteProduct = deleteProduct;
