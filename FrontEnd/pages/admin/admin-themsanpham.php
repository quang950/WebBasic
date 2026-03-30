<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Quản lý sản phẩm</title>
    <link rel="stylesheet" href="../../assets/css/admin-style-new.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="admin-nav">
                <h1>Admin Panel - 3 Boys Auto</h1>
                <div class="admin-actions">
                    <span id="admin-welcome">Xin chào, Admin!</span>
                    <button onclick="goToHomePage()" class="home-btn">
                        <i class="fas fa-home"></i> Trang chủ
                    </button>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <div class="admin-sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li class="active">
                            <a href="#products" onclick="return showSection('products')">
                                <i class="fas fa-car"></i> Quản lý sản phẩm
                            </a>
                        </li>
                        <li>
                            <a href="#orders" onclick="return showSection('orders')">
                                <i class="fas fa-file-invoice"></i> Quản lý đơn hàng
                            </a>
                        </li>
                        <li>
                            <a href="#imports" onclick="return showSection('imports')">
                                <i class="fas fa-truck"></i> Quản lý nhập hàng
                            </a>
                        </li>
                        <li>
                            <a href="#stock" onclick="return showSection('stock')">
                                <i class="fas fa-boxes"></i> Quản lý tồn kho
                            </a>
                        </li>
                        <li>
                            <a href="#customers" onclick="return showSection('customers')">
                                <i class="fas fa-users"></i> Quản lý khách hàng
                            </a>
                        </li>
                        <li>
                            <a href="#pricing" onclick="return showSection('pricing')">
                                <i class="fas fa-tags"></i> Quản lý giá bán
                            </a>
                        </li>
                        <li>
                            <a href="#categories" onclick="return showSection('categories')">
                                <i class="fas fa-list"></i> Quản lý loại sản phẩm
                            </a>
                        </li>
                        <li>
                            <a href="#dashboard" onclick="return showSection('dashboard')">
                                <i class="fas fa-dashboard"></i> Thống kê
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="admin-content">
                <!-- Quản lý đơn hàng -->
                <section id="orders-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quản lý đơn hàng</h2>
                    </div>
                    <div style="margin-bottom:18px;display:flex;gap:24px;flex-wrap:wrap;align-items:center;">
                        <label>Ngày đặt: <input type="date" id="orderDateFrom"></label>
                        <label>đến: <input type="date" id="orderDateTo"></label>
                        <label>Tình trạng: 
                            <select id="orderStatusFilter">
                                <option value="">Tất cả</option>
                                <option value="new">Mới đặt</option>
                                <option value="processing">Đã xử lý</option>
                                <option value="delivered">Đã giao</option>
                                <option value="cancelled">Hủy</option>
                            </select>
                        </label>
                        <button onclick="filterAdminOrders()" class="search-btn"><i class="fas fa-search"></i> Lọc</button>
                    </div>
                    <div id="adminOrdersGrid"></div>
                </section>

                <!-- Quản lý phiếu nhập hàng -->
                <section id="imports-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quản lý phiếu nhập hàng</h2>
                        <button onclick="openCreateTicketModal()" class="add-btn" style="padding:10px 14px;"><i class="fas fa-plus"></i> Thêm phiếu nhập</button>
                    </div>

                    <!-- Search & Filter -->
                    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="searchImportInput" placeholder="Tìm mã phiếu nhập (VD: IT20260330...)" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:1;min-width:250px;">
                        <select id="statusImportFilter" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:0 0 150px;">
                            <option value="">Tất cả trạng thái</option>
                            <option value="draft">Nháp</option>
                            <option value="completed">Hoàn thành</option>
                        </select>
                        <input type="date" id="dateFromImportFilter" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:0 0 150px;">
                        <input type="date" id="dateToImportFilter" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:0 0 150px;">
                        <button onclick="searchImportTickets()" class="search-btn" style="padding:10px 16px;"><i class="fas fa-search"></i> Tìm</button>
                    </div>

                    <!-- Danh sách phiếu nhập -->
                    <div id="importTicketsList"></div>
                </section>

                <!-- Quản lý tồn kho -->
                <section id="stock-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quản lý tồn kho</h2>
                    </div>
                    
                    <div style="margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="stockSearchName" placeholder="Tìm theo tên sản phẩm" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:250px;font-size:14px;">
                        <select id="stockCategoryFilter" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <option value="">Tất cả loại xe</option>
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="hatchback">Hatchback</option>
                            <option value="pickup">Pickup</option>
                        </select>
                        <button onclick="searchStockProduct()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> Tìm kiếm</button>
                    </div>

                    <!-- Tra cứu nhập-xuất-tồn -->
                    <div style="background:#f8f9fa;padding:20px;border-radius:8px;border:1px solid #dee2e6;margin-bottom:20px;">
                        <h3 style="color:#0d279d;margin-bottom:16px;font-size:1.1rem;">
                            <i class="fas fa-clipboard-list"></i> Tra cứu nhập-xuất-tồn của sản phẩm
                        </h3>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                            <input type="text" id="inventorySearchProduct" placeholder="Nhập mã xe hoặc tên xe" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:300px;font-size:14px;">
                            <input type="date" id="inventoryFromDate" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <span style="color:#666;font-weight:600;">đến</span>
                            <input type="date" id="inventoryToDate" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <button onclick="searchInventoryReport()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> Tra cứu</button>
                        </div>
                        <div id="inventoryResult" style="margin-top:16px;display:none;">
                            <div style="background:#fff;padding:16px;border-radius:6px;border:1px solid #ddd;">
                                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px;">
                                    <div style="text-align:center;padding:12px;background:#e3f2fd;border-radius:6px;">
                                        <div style="color:#1976d2;font-size:0.9em;margin-bottom:4px;font-weight:600;">Tồn đầu kỳ</div>
                                        <div style="color:#0d47a1;font-size:1.5em;font-weight:700;">15</div>
                                    </div>
                                    <div style="text-align:center;padding:12px;background:#e8f5e9;border-radius:6px;">
                                        <div style="color:#2e7d32;font-size:0.9em;margin-bottom:4px;font-weight:600;">Số lượng nhập</div>
                                        <div style="color:#1b5e20;font-size:1.5em;font-weight:700;">10</div>
                                    </div>
                                    <div style="text-align:center;padding:12px;background:#fff3e0;border-radius:6px;">
                                        <div style="color:#f57c00;font-size:0.9em;margin-bottom:4px;font-weight:600;">Số lượng xuất</div>
                                        <div style="color:#e65100;font-size:1.5em;font-weight:700;">8</div>
                                    </div>
                                    <div style="text-align:center;padding:12px;background:#f3e5f5;border-radius:6px;">
                                        <div style="color:#7b1fa2;font-size:0.9em;margin-bottom:4px;font-weight:600;">Tồn cuối kỳ</div>
                                        <div style="color:#4a148c;font-size:1.5em;font-weight:700;">17</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Xe sắp hết hàng -->
                    <div style="background:#fff;padding:20px;border-radius:8px;border:1px solid #e0e0e0;margin-bottom:20px;">
                        <h3 style="color:#ff9800;margin-bottom:16px;font-size:1.2rem;">
                            <i class="fas fa-exclamation-circle"></i> Cảnh báo: Sản phẩm sắp hết hàng (Số lượng ≤ 1)
                        </h3>
                        <div id="lowStockList"></div>
                    </div>

                    <!-- Xe tồn kho lâu -->
                    <div style="background:#fff;padding:20px;border-radius:8px;border:1px solid #e0e0e0;margin-bottom:20px;">
                        <h3 style="color:#d9534f;margin-bottom:16px;font-size:1.2rem;">
                            <i class="fas fa-exclamation-triangle"></i> Xe tồn kho lâu (Trên 1 năm)
                        </h3>
                        <div id="oldStockList"></div>
                    </div>

                    <!-- Xe thường -->
                    <div style="background:#fff;padding:20px;border-radius:8px;border:1px solid #e0e0e0;">
                        <h3 style="color:#28a745;margin-bottom:16px;font-size:1.2rem;">
                            <i class="fas fa-check-circle"></i> Xe thường
                        </h3>
                        <div id="normalStockList"></div>
                    </div>
                </section>


                <!-- Quản lý sản phẩm -->
                <section id="products-section" class="content-section active">
                    <div class="section-header">
                        <h2>Quản lý sản phẩm</h2>
                        <button onclick="showAddProductModal()" class="add-btn">
                            <i class="fas fa-plus"></i> Thêm sản phẩm mới
                        </button>
                    </div>
                    <div class="products-grid" id="products-grid">
                        <!-- Danh sách sản phẩm sẽ được hiển thị ở đây -->
                    </div>
                </section>

                <!-- Quản lý khách hàng -->
                <section id="customers-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quản lý khách hàng</h2>
                    </div>
                    <div class="customers-grid" id="customers-grid">
                        <!-- Danh sách khách hàng sẽ được hiển thị ở đây -->
                    </div>
                </section>

                <!-- Thống kê -->
                                </section>

                <!-- Quản lý giá bán -->
                <section id="pricing-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quản lý giá bán</h2>
                    </div>
                    <div style="margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="pricingSearchProduct" placeholder="Tìm theo tên sản phẩm" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:250px;font-size:14px;">
                        <select id="pricingCategoryFilter" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <option value="">Tất cả loại xe</option>
                        </select>
                        <button onclick="searchPricingProduct()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> Tìm kiếm</button>
                    </div>
                    <div id="pricingGrid"></div>
                </section>

                <!-- Quản lý loại sản phẩm -->
                <section id="categories-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quản lý loại sản phẩm</h2>
                        <button onclick="window.location.href='admin-add-category.php'" class="add-btn">
                            <i class="fas fa-plus"></i> Thêm loại xe
                        </button>
                    </div>
                    <div style="margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="categorySearchInput" placeholder="Tìm theo tên loại xe" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:250px;font-size:14px;">
                        <select id="categoryStatusFilter" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <option value="">Tất cả trạng thái</option>
                            <option value="visible">Đang hiển thị</option>
                            <option value="hidden">Đang ẩn</option>
                        </select>
                        <button onclick="searchCategories()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> Tìm kiếm</button>
                    </div>
                    <table class="data-table" style="width:100%;table-layout:auto;">
                        <thead>
                            <tr>
                                <th style="color:#000;text-align:left;padding:12px;">Tên loại xe</th>
                                <th style="width:150px;color:#000;text-align:center;padding:12px;">Số sản phẩm</th>
                                <th style="width:150px;color:#000;text-align:center;padding:12px;">Trạng thái</th>
                                <th style="width:280px;color:#000;text-align:center;padding:12px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <!-- Dữ liệu loại xe sẽ được render ở đây -->
                        </tbody>
                    </table>
                </section>

                <!-- Dashboard -->
                <section id="dashboard-section" class="content-section">
                    <h2>Thống kê</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-car"></i>
                            <div class="stat-info">
                                <h3 id="total-products">0</h3>
                                <p>Tổng sản phẩm</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-eye"></i>
                            <div class="stat-info">
                                <h3 id="total-views">0</h3>
                                <p>Lượt xem</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal thêm sản phẩm -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Thêm sản phẩm mới</h3>
                <span class="close" onclick="closeAddProductModal()">&times;</span>
            </div>
            <form id="addProductForm">
                <div class="form-group">
                    <label for="productName">Tên sản phẩm:</label>
                    <input type="text" id="productName" name="productName" required>
                </div>
                
                <div class="form-group">
                    <label for="productBrand">Thương hiệu:</label>
                    <select id="productBrand" name="productBrand" required>
                        <option value="">Chọn thương hiệu</option>
                        <option value="toyota">Toyota</option>
                        <option value="mercedes">Mercedes</option>
                        <option value="bmw">BMW</option>
                        <option value="audi">Audi</option>
                        <option value="lexus">Lexus</option>
                        <option value="honda">Honda</option>
                        <option value="hyundai">Hyundai</option>
                        <option value="kia">KIA</option>
                        <option value="vinfast">Vinfast</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productPrice">Giá (VNĐ):</label>
                    <input type="number" id="productPrice" name="productPrice" required min="0">
                </div>

                <div class="form-group">
                    <label for="productYear">Năm sản xuất:</label>
                    <input type="number" id="productYear" name="productYear" required min="2000" max="2025">
                </div>

                <div class="form-group">
                    <label for="productFuel">Loại nhiên liệu:</label>
                    <select id="productFuel" name="productFuel" required>
                        <option value="">Chọn loại nhiên liệu</option>
                        <option value="Xăng">Xăng</option>
                        <option value="Dầu">Dầu</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Điện">Điện</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productTransmission">Hộp số:</label>
                    <select id="productTransmission" name="productTransmission" required>
                        <option value="">Chọn hộp số</option>
                        <option value="Số tự động">Số tự động</option>
                        <option value="Số sàn">Số sàn</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productCategory">Loại sản phẩm:</label>
                    <select id="productCategory" name="productCategory">
                        <option value="">Chọn loại</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productImageUrl">URL Hình ảnh:</label>
                    <input type="url" id="productImageUrl" name="productImageUrl" placeholder="https://example.com/image.jpg" required>
                    <small style="color: #666;">Nhập đường dẫn URL hình ảnh sản phẩm</small>
                </div>

                <div class="form-group">
                    <label for="productDescription">Mô tả:</label>
                    <textarea id="productDescription" name="productDescription" rows="4"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeAddProductModal()" class="cancel-btn">Hủy</button>
                    <button type="submit" class="save-btn" onclick="addProduct()">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>

        <!-- Modal thêm / sửa phiếu nhập -->
        <div id="addImportModal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Thêm phiếu nhập</h3>
                    <span class="close" onclick="closeAddImportModal()">&times;</span>
                </div>
                <form id="addImportForm">
                    <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;padding:12px 0;">
                        <label>Ngày nhập: <input type="date" id="legacyImportDate" name="legacyImportDate" required></label>
                        <label>Mã phiếu (Auto): <input type="text" id="importCode" name="importCode" disabled placeholder="(tự động)"></label>
                    </div>

                    <div id="importItemsContainer">
                        <div class="import-item-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                            <select class="import-product-select" style="flex:2;padding:8px;border-radius:6px;border:1px solid #ccc;">
                                <option value="">-- Chọn sản phẩm --</option>
                            </select>
                            <input type="number" class="import-price" placeholder="Giá nhập" min="0" style="flex:1;padding:8px;border-radius:6px;border:1px solid #ccc;">
                            <input type="number" class="import-qty" placeholder="Số lượng" min="1" value="1" style="width:100px;padding:8px;border-radius:6px;border:1px solid #ccc;">
                            <button type="button" class="remove-item-btn" onclick="removeImportItemRow(this)" style="padding:8px 10px;border-radius:6px;background:#dc3545;color:#fff;border:none;">Xóa</button>
                        </div>
                    </div>

                    <div style="margin-top:8px;margin-bottom:12px;">
                        <button type="button" onclick="addImportItemRow()" class="add-btn" style="padding:8px 12px;border-radius:6px;"><i class="fas fa-plus"></i> Thêm dòng</button>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="closeAddImportModal()" class="cancel-btn">Hủy</button>
                        <button type="submit" class="save-btn" onclick="return false;">Lưu phiếu nhập</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal thêm / sửa loại sản phẩm -->
        <div id="addCategoryModal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="categoryModalTitle">Thêm loại sản phẩm</h3>
                    <span class="close" onclick="closeAddCategoryModal()">&times;</span>
                </div>
                <form id="addCategoryForm">
                    <input type="hidden" id="categoryEditId" value="">
                    
                    <div class="form-group">
                        <label for="categoryName">Tên loại xe:</label>
                        <input type="text" id="categoryName" name="categoryName" required placeholder="Ví dụ: Toyota, Honda, BMW...">
                    </div>

                    <div class="form-group">
                        <label for="categorySlug">Slug (URL):</label>
                        <input type="text" id="categorySlug" name="categorySlug" required placeholder="Ví dụ: toyota, honda, bmw...">
                        <small style="color:#666;font-size:12px;">Chỉ dùng chữ thường, số và dấu gạch ngang</small>
                    </div>

                    <div class="form-group">
                        <label for="categoryDescription">Mô tả:</label>
                        <textarea id="categoryDescription" name="categoryDescription" rows="3" placeholder="Mô tả về loại xe này..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="categoryVisible" name="categoryVisible" checked>
                            Hiển thị trên trang chủ
                        </label>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="closeAddCategoryModal()" class="cancel-btn">Hủy</button>
                        <button type="submit" class="save-btn" onclick="addProduct();">Lưu</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- MODAL: Create Import Ticket -->
    <div id="createTicketModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:1000;">
        <div style="background:#fff;border-radius:12px;width:95%;max-width:750px;box-shadow:0 8px 24px rgba(0,0,0,0.3);">
            <div style="padding:24px 28px;border-bottom:2px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="margin:0;font-size:1.3rem;color:#333;">📋 Tạo Phiếu Nhập Hàng Mới</h3>
                <span onclick="closeCreateTicketModal()" style="cursor:pointer;font-size:28px;color:#999;">&times;</span>
            </div>
            <div style="padding:28px;max-height:70vh;overflow-y:auto;">
                <form id="createTicketForm" style="display:flex;flex-direction:column;gap:20px;">
                    <!-- Ngày nhập + Ghi chú -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;margin-bottom:8px;font-weight:700;color:#333;">📅 Ngày nhập <span style="color:red;">*</span></label>
                            <input type="date" id="importDate" style="width:100%;padding:12px;border:1.5px solid #ddd;border-radius:6px;font-size:1rem;" required>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:8px;font-weight:700;color:#333;">📝 Ghi chú</label>
                            <input type="text" id="notes" placeholder="Ghi chú phiếu nhập..." style="width:100%;padding:12px;border:1.5px solid #ddd;border-radius:6px;font-size:0.95rem;">
                        </div>
                    </div>

                    <!-- Tìm kiếm & thêm sản phẩm -->
                    <div style="border:2px dashed #0d6efd;padding:16px;border-radius:8px;background:#f8f9ff;">
                        <h4 style="margin:0 0 12px 0;color:#0d6efd;">🔍 Thêm Sản Phẩm Vào Phiếu</h4>
                        
                        <div style="display:grid;grid-template-columns:1fr 0.6fr 0.6fr auto;gap:10px;margin-bottom:12px;position:relative;">
                            <div style="position:relative;">
                                <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Tìm sản phẩm</label>
                                <input type="text" id="searchProductForImport" placeholder="Nhập tên hoặc mã..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                                <div id="productSearchResults" style="position:absolute;background:#fff;border:1px solid #ddd;border-radius:4px;max-height:150px;overflow-y:auto;display:none;width:100%;z-index:2000;top:100%;left:0;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                                    <!-- Results hiển thị ở đây -->
                                </div>
                            </div>
                            <div>
                                <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Số lượng</label>
                                <input type="number" id="importQuantity" min="1" value="1" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                            </div>
                            <div>
                                <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Giá nhập</label>
                                <input type="number" id="importPrice" min="0" step="0.01" value="0" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <button type="button" onclick="addProductToTicket()" style="background:#0d6efd;color:#fff;border:none;padding:8px 14px;border-radius:4px;cursor:pointer;font-weight:600;white-space:nowrap;">➕ Thêm</button>
                            </div>
                        </div>
                        <small style="color:#666;">Ghi chú: Nhập số lượng & giá mua, rồi bấm nút Thêm</small>
                    </div>

                    <!-- Bảng sản phẩm đã thêm -->
                    <div>
                        <h4 style="margin:0 0 12px 0;color:#333;">📦 Sản Phẩm Trong Phiếu</h4>
                        <table style="width:100%;border-collapse:collapse;border:1px solid #ddd;">
                            <thead>
                                <tr style="background:#f5f5f5;">
                                    <th style="padding:10px;text-align:left;border-bottom:1px solid #ddd;font-size:12px;">Sản phẩm</th>
                                    <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:80px;">Số lượng</th>
                                    <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:100px;">Giá nhập</th>
                                    <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:100px;">Thành tiền</th>
                                    <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:40px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="importItemsTable">
                                <tr><td colspan="5" style="padding:20px;text-align:center;color:#999;font-size:12px;">Chưa có sản phẩm nào</td></tr>
                            </tbody>
                        </table>
                        <div style="margin-top:12px;padding:12px;background:#f9f9f9;border-radius:4px;display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#666;font-weight:600;">💰 Tổng tiền nhập:</span>
                            <span id="totalImportPrice" style="font-size:18px;font-weight:700;color:#dc3545;">₫0</span>
                        </div>
                    </div>
                </form>
            </div>
            <div style="padding:16px 28px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:12px;background:#fafafa;">
                <button onclick="closeCreateTicketModal()" style="background:#e9ecef;color:#333;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">Hủy</button>
                <button onclick="submitCreateTicket()" style="background:#28a745;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-weight:600;">✅ Tạo Phiếu</button>
            </div>
        </div>
    </div>

    <!-- MODAL: Detail/Edit Import Ticket -->
    <div id="ticketDetailModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:1001;">
        <div style="background:#fff;border-radius:12px;width:95%;max-width:900px;box-shadow:0 8px 24px rgba(0,0,0,0.3);">
            <div style="padding:24px 28px;border-bottom:2px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="margin:0;font-size:1.3rem;color:#333;">📄 Chi Tiết Phiếu Nhập: <span id="detailTicketNumber">-</span></h3>
                <span onclick="closeTicketDetailModal()" style="cursor:pointer;font-size:28px;color:#999;">&times;</span>
            </div>

            <div style="padding:22px 28px;max-height:72vh;overflow-y:auto;display:flex;flex-direction:column;gap:16px;">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">Trạng thái</label>
                        <div id="detailTicketStatusBadge" style="display:inline-block;padding:6px 12px;border-radius:16px;font-size:0.85rem;font-weight:700;background:#fff3cd;color:#856404;">Nháp</div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">📅 Ngày nhập</label>
                        <input type="date" id="detailImportDate" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">💰 Tổng tiền</label>
                        <div id="detailTotalImportPrice" style="padding:10px 12px;border-radius:6px;background:#f9f9f9;color:#d9534f;font-weight:700;">₫0</div>
                    </div>
                </div>

                <div>
                    <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">📝 Ghi chú</label>
                    <input type="text" id="detailNotes" placeholder="Ghi chú phiếu nhập..." style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                </div>

                <div id="detailAddItemSection" style="border:2px dashed #0d6efd;padding:16px;border-radius:8px;background:#f8f9ff;">
                    <h4 style="margin:0 0 12px 0;color:#0d6efd;">➕ Thêm sản phẩm vào phiếu</h4>
                    <div style="display:grid;grid-template-columns:1fr 0.5fr 0.6fr auto;gap:10px;align-items:end;position:relative;">
                        <div style="position:relative;">
                            <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Tìm sản phẩm</label>
                            <input type="text" id="searchProductForDetail" placeholder="Nhập tên hoặc mã..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                            <div id="productSearchResultsDetail" style="position:absolute;background:#fff;border:1px solid #ddd;border-radius:4px;max-height:150px;overflow-y:auto;display:none;width:100%;z-index:2002;top:100%;left:0;box-shadow:0 2px 8px rgba(0,0,0,0.1);"></div>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Số lượng</label>
                            <input type="number" id="detailQuantity" min="1" value="1" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Giá nhập</label>
                            <input type="number" id="detailPrice" min="0" step="0.01" value="0" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                        </div>
                        <button type="button" onclick="addProductToCurrentTicket()" style="background:#0d6efd;color:#fff;border:none;padding:8px 14px;border-radius:4px;cursor:pointer;font-weight:600;white-space:nowrap;">➕ Thêm</button>
                    </div>
                </div>

                <div>
                    <h4 style="margin:0 0 12px 0;color:#333;">📦 Danh sách sản phẩm</h4>
                    <table style="width:100%;border-collapse:collapse;border:1px solid #ddd;">
                        <thead>
                            <tr style="background:#f5f5f5;">
                                <th style="padding:10px;text-align:left;border-bottom:1px solid #ddd;font-size:12px;">Sản phẩm</th>
                                <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:90px;">Số lượng</th>
                                <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:120px;">Giá nhập</th>
                                <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:120px;">Thành tiền</th>
                                <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:70px;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsTable">
                            <tr><td colspan="5" style="padding:20px;text-align:center;color:#999;font-size:12px;">Chưa có sản phẩm nào</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="padding:16px 28px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:12px;background:#fafafa;">
                <button onclick="closeTicketDetailModal()" style="background:#e9ecef;color:#333;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">Đóng</button>
                <button id="saveTicketChangesBtn" onclick="saveCurrentTicketChanges()" style="background:#fd7e14;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">💾 Lưu thay đổi</button>
                <button id="completeTicketBtn" onclick="completeCurrentTicket()" style="background:#198754;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">✅ Hoàn thành phiếu</button>
            </div>
        </div>
    </div>

        <script src="../../assets/js/admin.js"></script>
    <script>
        // Function để quay về trang chủ
        function goToHomePage() {
            // Lưu trạng thái admin đang đăng nhập khi về trang chủ
            localStorage.setItem('adminViewingHome', 'true');
            window.location.href = '../../index.php';
        }

        // Kiểm tra đăng nhập khi load trang
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('adminLoggedIn')) {
                window.location.href = 'admin-login.php';
                return;
            }
            
            const username = localStorage.getItem('adminUsername');
            if (username) {
                document.getElementById('admin-welcome').textContent = `Xin chào, ${username}!`;
            }
            
            // Khởi tạo loại sản phẩm và cập nhật select
            if (typeof initCategories === 'function') initCategories();
            if (typeof updateCategorySelect === 'function') updateCategorySelect();

            // Tự động nhập các xe có sẵn từ trang chủ (nếu đã được cache)
            if (typeof importHomepageCars === 'function') {
                try { importHomepageCars(true); } catch (e) { /* ignore */ }
            }

            loadProducts();
            updateStats();
            loadCategories(); // Load danh sách loại sản phẩm
        });

        // ========== QUẢN LÝ LOẠI SẢN PHẨM ==========
        
        // Load và hiển thị danh sách loại sản phẩm từ database API
        function loadCategories() {
            const tbody = document.getElementById('categoriesTableBody');
            
            if (!tbody) return;
            
            // Show loading
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';
            
            // Fetch categories from API
            fetch('/WebBasic/BackEnd/api/categories.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.categories && data.categories.length > 0) {
                        tbody.innerHTML = data.categories.map(cat => `
                            <tr>
                                <td style="padding:12px;text-align:left;">${cat.name}</td>
                                <td style="padding:12px;text-align:center;">${cat.product_count || 0}</td>
                                <td style="padding:12px;text-align:center;">
                                    ${cat.is_visible 
                                        ? '<i class="fas fa-eye" style="color:#28a745;"></i> Hiển thị' 
                                        : '<i class="fas fa-eye-slash" style="color:#dc3545;"></i> Ẩn'
                                    }
                                </td>
                                <td style="padding:8px;text-align:center;">
                                    <div style="display:flex;gap:0.5rem;align-items:center;justify-content:center;">
                                        <button onclick="editCategory(${cat.id})" class="edit-btn" title="Sửa" style="padding:0.5rem;background:#007bff;color:white;border:none;border-radius:6px;cursor:pointer;">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button onclick="toggleHideCategory(${cat.id})" title="${cat.is_visible ? 'Ẩn' : 'Hiện'}" style="padding:0.5rem;background:#6c757d;color:white;border:none;border-radius:6px;cursor:pointer;">
                                            <i class="fas fa-${cat.is_visible ? 'eye-slash' : 'eye'}"></i> ${cat.is_visible ? 'Ẩn' : 'Hiện'}
                                        </button>
                                        <button onclick="deleteCategory(${cat.id})" class="delete-btn" title="Xóa" style="padding:0.5rem;background:#dc3545;color:white;border:none;border-radius:6px;cursor:pointer;">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#999;">Chưa có loại sản phẩm nào</td></tr>';
                    }
                })
                .catch(err => {
                    console.error('Error loading categories:', err);
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#dc3545;">Lỗi khi tải danh sách loại sản phẩm</td></tr>';
                });
        }

        // Hiển thị modal thêm loại sản phẩm
        function showAddCategoryModal() {
            document.getElementById('categoryModalTitle').textContent = 'Thêm loại sản phẩm';
            document.getElementById('categoryEditId').value = '';
            document.getElementById('categoryName').value = '';
            document.getElementById('categoryDescription').value = '';
            document.getElementById('categoryVisible').checked = true;
            document.getElementById('addCategoryModal').style.display = 'block';
        }

        // Đóng modal
        function closeAddCategoryModal() {
            document.getElementById('addCategoryModal').style.display = 'none';
        }

        // Sửa loại sản phẩm
        function editCategory(id) {
            alert('Chức năng sửa chưa khả dụng');
        }

        // Xóa loại sản phẩm
        function deleteCategory(id) {
            if (!confirm('Bạn có chắc muốn xóa loại sản phẩm này?')) return;
            
            fetch('/WebBasic/BackEnd/api/categories.php', {
                method: 'DELETE',
                body: new URLSearchParams({id: id})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Xóa thành công');
                    loadCategories();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => alert('Lỗi kết nối: ' + err));
        }

        // Ẩn/Hiện loại sản phẩm
        function toggleHideCategory(id) {
            alert('Chức năng ẩn/hiện chưa khả dụng');
        }

        // Tìm kiếm loại sản phẩm
        function searchCategories() {
            loadCategories();
        }

        // Load lại dữ liệu loại sản phẩm
        function loadCategoriesData() {
            loadCategories();
        }


        // NO DUPLICATES

        // Tìm kiếm sản phẩm tồn kho
        function searchStockProduct() {
            const oldStockList = document.getElementById('oldStockList');
            if (!oldStockList) return;
            
            // Hiển thị 1 xe Toyota Fortuner tồn kho lâu
            oldStockList.innerHTML = `
                <div style="margin-bottom: 15px;">
                    <button onclick="loadStockData()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-undo"></i> Quay lại
                    </button>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
                    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                        <img src="../../assets/images/toyota-fortuner.jpg" alt="Toyota Fortuner" style="width:100%;height:180px;object-fit:cover;border-radius:6px;margin-bottom:12px;">
                        <h4 style="color:#333;margin-bottom:8px;">Toyota Fortuner 2023</h4>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>Mã:</strong> TK001
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>Loại:</strong> SUV
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>Giá gốc:</strong> 1.450.000.000 VNĐ
                        </div>
                        <div style="color:#d9534f;font-size:0.9em;margin-bottom:6px;">
                            <strong>Giảm giá:</strong> 10% (145.000.000 VNĐ)
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>Số lượng tồn:</strong> 3 xe
                        </div>
                        <div style="color:#d9534f;font-size:0.9em;margin-bottom:6px;">
                            <strong>Ngày nhập:</strong> 15/09/2023 (410 ngày)
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-top:8px;padding:8px;background:#fff3cd;border-radius:6px;">
                            <strong>Lý do tồn:</strong> Ít người mua do giá cao
                        </div>
                    </div>
                </div>
            `;
        }

        // Tra cứu nhập-xuất-tồn
        function searchInventoryReport() {
            const inventoryResult = document.getElementById('inventoryResult');
            if (!inventoryResult) return;
            
            inventoryResult.style.display = 'block';
            
            // Thêm nút quay lại
            const resultDiv = inventoryResult.querySelector('div > div:first-child');
            if (resultDiv && !document.getElementById('inventoryBackBtn')) {
                const backBtn = document.createElement('div');
                backBtn.id = 'inventoryBackBtn';
                backBtn.style.cssText = 'margin-bottom: 15px;';
                backBtn.innerHTML = `
                    <button onclick="hideInventoryReport()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-undo"></i> Quay lại
                    </button>
                `;
                inventoryResult.insertBefore(backBtn, inventoryResult.firstChild);
            }
        }

        // Ẩn kết quả tra cứu
        function hideInventoryReport() {
            const inventoryResult = document.getElementById('inventoryResult');
            const backBtn = document.getElementById('inventoryBackBtn');
            if (inventoryResult) inventoryResult.style.display = 'none';
            if (backBtn) backBtn.remove();
        }

        // Load lại dữ liệu tồn kho
        function loadStockData() {
            // Gọi lại hàm load dữ liệu ban đầu từ admin.js
            if (typeof window.initStockSection === 'function') {
                window.initStockSection();
            }
        }

        // Tìm kiếm sản phẩm giá bán
        function searchPricingProduct() {
            if (typeof window.loadPricing === 'function') {
                window.loadPricing();
            }
            return false;
        }

        // Load lại dữ liệu giá bán
        function loadPricingData() {
            if (typeof window.loadPricing === 'function') {
                window.loadPricing();
            }
        }

        // Export các hàm ra window
        window.searchStockProduct = searchStockProduct;
        window.searchInventoryReport = searchInventoryReport;
        window.hideInventoryReport = hideInventoryReport;
        window.loadStockData = loadStockData;
        window.searchPricingProduct = searchPricingProduct;
        window.loadPricingData = loadPricingData;
        window.searchCategories = searchCategories;
        window.loadCategoriesData = loadCategoriesData;
    </script>
    <script>
    async function loadProducts(page = 1) {
        try {
            const res = await fetch(`/api/products.php?page=${page}`);
            const data = await res.json();

            if (!data.success) {
                console.error(data.message);
                return;
            }

            let html = '';
            data.data.forEach(p => {
                html += `
                    <tr>
                        <td>${p.id}</td>
                        <td>${p.name}</td>
                        <td>${p.price}</td>
                    </tr>
                `;
            });

            document.getElementById('product-list').innerHTML = html;

        } catch (err) {
            console.error("Lỗi gọi API:", err);
        }
    }

    // load khi mở trang
    loadProducts();
<<<<<<< HEAD
    function addProduct() {
        const formData = new FormData();

        formData.append('name', document.getElementById('product-name').value);
        formData.append('price', document.getElementById('product-price').value);
        formData.append('stock', document.getElementById('product-stock').value);
        formData.append('category_id', document.getElementById('product-category').value);

        fetch('../../BackEnd/api/admin/Create.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Thêm sản phẩm thành công');
                loadProducts(); // load lại danh sách
            } else {
                alert('' + data.message);
            }
        })
        .catch(err => console.error(err));
    }
    function deleteProduct(id) {
        if (!confirm('Bạn chắc chắn muốn xoá sản phẩm này?')) return;

        const formData = new FormData();
        formData.append('id', id);

        fetch('../../BackEnd/api/admin/Delete.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(' Đã xoá sản phẩm');
                loadProducts();
            } else {
                alert(' ' + data.message);
            }
        })
        .catch(err => console.error(err));
    }

    function updateProduct(id) {
        const price = document.getElementById(`price-${id}`).value;
        const stock = document.getElementById(`stock-${id}`).value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('price', price);
        formData.append('stock', stock);

        fetch('../../BackEnd/api/admin/update.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(' Cập nhật thành công');
                loadProducts();
            } else {
                alert(' ' + data.message);
            }
        })
        .catch(err => console.error(err));
    }
=======

    // ==========================================  
    // IMPORT MANAGEMENT FUNCTIONS
    // ==========================================
    const API_BASE = '/WebBasic/BackEnd/api/';
    const PRICING_API = `${API_BASE}pricing.php`;
    let currentImportTicketId = null;
    let ticketItemsForCreate = []; // Lưu danh sách sản phẩm sẽ thêm vào phiếu
    let selectedProductForImport = null; // Lưu sản phẩm được chọn từ dropdown
    let selectedProductForDetail = null;
    let currentTicketStatus = 'draft';
    let currentTicketItems = [];
    let isCreatingTicket = false;
    let pricingData = [];

    function formatMoney(value) {
        const num = Number(value || 0);
        return num.toLocaleString('vi-VN');
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ==========================================
    // PRICING MANAGEMENT FUNCTIONS
    // ==========================================
    async function loadPricingCategories() {
        const categoryFilter = document.getElementById('pricingCategoryFilter');
        if (!categoryFilter) return;

        const currentValue = categoryFilter.value || '';
        try {
            const response = await fetch(`${API_BASE}categories.php?action=list`);
            const result = await response.json();

            if (response.ok && result.success && Array.isArray(result.categories)) {
                categoryFilter.innerHTML = '<option value="">Tất cả loại xe</option>' +
                    result.categories.map(cat => `<option value="${cat.id}">${escapeHtml(cat.name)}</option>`).join('');

                if (currentValue) {
                    categoryFilter.value = currentValue;
                }
            }
        } catch (error) {
            console.error('Load pricing categories error:', error);
        }
    }

    function renderPricingTable(products) {
        const pricingGrid = document.getElementById('pricingGrid');
        if (!pricingGrid) return;

        if (!Array.isArray(products) || products.length === 0) {
            pricingGrid.innerHTML = '<div class="empty-state">Không tìm thấy sản phẩm phù hợp.</div>';
            return;
        }

        pricingGrid.innerHTML = `
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                    <thead>
                        <tr style="background:linear-gradient(135deg,#1b4f72 0%,#2874a6 100%);color:#fff;">
                            <th style="padding:14px;text-align:left;font-weight:600;min-width:230px;">Sản phẩm</th>
                            <th style="padding:14px;text-align:left;font-weight:600;min-width:140px;">Loại xe</th>
                            <th style="padding:14px;text-align:right;font-weight:600;min-width:150px;">Giá vốn (VNĐ)</th>
                            <th style="padding:14px;text-align:center;font-weight:600;min-width:200px;">% Lợi nhuận</th>
                            <th style="padding:14px;text-align:right;font-weight:600;min-width:170px;">Giá bán (VNĐ)</th>
                            <th style="padding:14px;text-align:right;font-weight:600;min-width:170px;">Lợi nhuận (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${products.map(product => {
                            const costPrice = Number(product.cost_price || 0);
                            const profitMargin = Number(product.profit_margin || 0);
                            const sellingPrice = Number(product.selling_price || 0);
                            const profitAmount = Number(product.profit_amount || (sellingPrice - costPrice));

                            return `
                                <tr style="border-bottom:1px solid #f0f0f0;transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
                                    <td style="padding:12px;">
                                        <div style="font-weight:600;color:#333;margin-bottom:4px;">${escapeHtml(product.name)}</div>
                                        <div style="font-size:0.85em;color:#666;">ID: ${product.id} | Tồn kho: ${product.stock ?? 0}</div>
                                    </td>
                                    <td style="padding:12px;color:#555;">${escapeHtml(product.category_name || 'N/A')}</td>
                                    <td style="padding:12px;text-align:right;font-weight:500;color:#333;">${formatMoney(costPrice)}</td>
                                    <td style="padding:12px;text-align:center;">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                            <input
                                                id="pricing-margin-${product.id}"
                                                type="number"
                                                value="${profitMargin.toFixed(2)}"
                                                min="0"
                                                max="500"
                                                step="0.1"
                                                style="width:84px;padding:6px 8px;border:1px solid #ddd;border-radius:6px;text-align:center;font-weight:600;font-size:0.95em;"
                                            >
                                            <span style="font-weight:600;color:#2e7d32;">%</span>
                                            <button
                                                type="button"
                                                onclick="updateProductProfitMargin(${product.id})"
                                                style="padding:6px 10px;border:none;border-radius:6px;cursor:pointer;background:#0d6efd;color:#fff;font-weight:600;">
                                                Cập nhật
                                            </button>
                                        </div>
                                    </td>
                                    <td style="padding:12px;text-align:right;">
                                        <div style="font-weight:700;color:#0d279d;font-size:1.02em;">${formatMoney(sellingPrice)}</div>
                                    </td>
                                    <td style="padding:12px;text-align:right;">
                                        <div style="font-weight:600;color:${profitAmount >= 0 ? '#28a745' : '#dc3545'};">${profitAmount >= 0 ? '+' : ''}${formatMoney(profitAmount)}</div>
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    async function loadPricing() {
        const pricingGrid = document.getElementById('pricingGrid');
        if (!pricingGrid) return;

        const search = document.getElementById('pricingSearchProduct')?.value.trim() || '';
        const categoryId = document.getElementById('pricingCategoryFilter')?.value || '';

        pricingGrid.innerHTML = '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu giá bán...</div>';

        try {
            await loadPricingCategories();

            const params = new URLSearchParams({ action: 'list', limit: '500' });
            if (search) params.set('search', search);
            if (categoryId) params.set('categoryId', categoryId);

            const response = await fetch(`${PRICING_API}?${params.toString()}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Không tải được dữ liệu giá bán');
            }

            pricingData = result.data || [];
            renderPricingTable(pricingData);
        } catch (error) {
            console.error('Load pricing error:', error);
            pricingGrid.innerHTML = `<div style="text-align:center;color:#dc3545;padding:20px;">${escapeHtml(error.message || 'Lỗi tải dữ liệu giá bán')}</div>`;
        }
    }

    function searchPricingProduct() {
        loadPricing();
        return false;
    }

    function loadPricingData() {
        loadPricing();
        return false;
    }

    async function updateProductProfitMargin(productId) {
        const input = document.getElementById(`pricing-margin-${productId}`);
        if (!input) return;

        const marginValue = parseFloat(input.value);
        if (Number.isNaN(marginValue) || marginValue < 0 || marginValue > 500) {
            alert('❌ % lợi nhuận phải từ 0 đến 500');
            input.focus();
            return;
        }

        try {
            input.disabled = true;

            const response = await fetch(PRICING_API, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_margin',
                    product_id: productId,
                    profit_margin: marginValue
                })
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Cập nhật % lợi nhuận thất bại');
            }

            await loadPricing();
        } catch (error) {
            console.error('Update margin error:', error);
            alert('❌ ' + (error.message || 'Lỗi cập nhật % lợi nhuận'));
        } finally {
            input.disabled = false;
        }
    }

    window.loadPricing = loadPricing;
    window.searchPricingProduct = searchPricingProduct;
    window.loadPricingData = loadPricingData;
    window.updateProductProfitMargin = updateProductProfitMargin;

    function searchImportTickets() {
        const search = document.getElementById('searchImportInput')?.value || '';
        const status = document.getElementById('statusImportFilter')?.value || '';
        const dateFrom = document.getElementById('dateFromImportFilter')?.value || '';
        const dateTo = document.getElementById('dateToImportFilter')?.value || '';

        let url = `${API_BASE}import_tickets.php?action=list`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${status}`;
        if (dateFrom) url += `&dateFrom=${dateFrom}`;
        if (dateTo) url += `&dateTo=${dateTo}`;

        fetch(url)
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    displayImportTickets(result.data);
                }
            })
            .catch(err => console.error('Error:', err));
    }

    // Hàm tìm kiếm sản phẩm
    async function searchProductsForImport() {
        const searchInput = document.getElementById('searchProductForImport');
        const resultsDiv = document.getElementById('productSearchResults');
        const query = searchInput.value.trim();

        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`${API_BASE}products.php?name=${encodeURIComponent(query)}&limit=20`);
            const data = await response.json();
            
            if (!data.success || !data.data || data.data.length === 0) {
                resultsDiv.innerHTML = '<div style="padding:10px;color:#999;font-size:11px;">Không tìm thấy sản phẩm</div>';
                resultsDiv.style.display = 'block';
                return;
            }

            resultsDiv.innerHTML = data.data.map(product => `
                <div onclick="selectProductForImport(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price || 0})" 
                     style="padding:10px;border-bottom:1px solid #f0f0f0;cursor:pointer;background:#f9f9f9;font-size:11px;transition:all 0.2s;">
                    <strong>${product.name}</strong><br>
                    <small style="color:#666;">ID: ${product.id} | Giá: ₫${parseFloat(product.price || 0).toLocaleString('vi-VN')}</small>
                </div>
            `).join('');
            resultsDiv.style.display = 'block';
        } catch (err) {
            console.error('Error searching products:', err);
            resultsDiv.innerHTML = '<div style="padding:10px;color:red;font-size:11px;">Lỗi tìm kiếm sản phẩm</div>';
            resultsDiv.style.display = 'block';
        }
    }

    // Khi người dùng chọn một sản phẩm từ dropdown
    function selectProductForImport(productId, productName, costPrice) {
        selectedProductForImport = { id: productId, name: productName, costPrice: costPrice };
        document.getElementById('searchProductForImport').value = productName;
        document.getElementById('productSearchResults').style.display = 'none';
    }

    // Thêm sản phẩm vào danh sách (lưu trữ local, chưa gửi API)
    async function addProductToTicket() {
        const quantity = parseInt(document.getElementById('importQuantity').value) || 0;
        const price = parseFloat(document.getElementById('importPrice').value) || 0;
        const searchInputValue = document.getElementById('searchProductForImport').value.trim();

        if (!searchInputValue) {
            alert('❌ Vui lòng nhập tên sản phẩm!');
            return;
        }
        if (quantity < 1) {
            alert('❌ Số lượng phải >= 1!');
            return;
        }
        if (price < 0) {
            alert('❌ Giá nhập không được âm!');
            return;
        }

        let product = null;

        // Nếu có sản phẩm được chọn từ dropdown, dùng nó
        if (selectedProductForImport) {
            product = selectedProductForImport;
            selectedProductForImport = null; // Reset
        } else {
            // Nếu không, tìm kiếm bằng tên/mã
            try {
                const response = await fetch(`${API_BASE}products.php?name=${encodeURIComponent(searchInputValue)}&limit=20`);
                const data = await response.json();
                
                if (!data.success || !data.data || data.data.length === 0) {
                    alert('❌ Không tìm thấy sản phẩm với tên: ' + searchInputValue);
                    return;
                }
                product = data.data[0];
            } catch (err) {
                console.error('Error searching product:', err);
                alert('❌ Lỗi tìm kiếm sản phẩm!');
                return;
            }
        }

        const subtotal = quantity * price;

        // Kiểm tra xem sản phẩm đã có trong danh sách chưa
        const existingIndex = ticketItemsForCreate.findIndex(item => item.product_id === product.id);
        if (existingIndex >= 0) {
            // Nếu có rồi thì cập nhật
            if (confirm(`Sản phẩm "${product.name}" đã có trong phiếu. Cập nhật số lượng thêm ${quantity}?`)) {
                ticketItemsForCreate[existingIndex].quantity += quantity;
                ticketItemsForCreate[existingIndex].import_price = price;
                ticketItemsForCreate[existingIndex].total_price = ticketItemsForCreate[existingIndex].quantity * price;
            }
        } else {
            // Nếu chưa có thì thêm mới
            ticketItemsForCreate.push({
                product_id: product.id,
                product_name: product.name,
                quantity: quantity,
                import_price: price,
                total_price: subtotal
            });
        }

        updateImportItemsTable();
        calculateTotalPrice();

        // Reset input fields
        document.getElementById('searchProductForImport').value = '';
        document.getElementById('importQuantity').value = '1';
        document.getElementById('importPrice').value = '0';
    }

    // Cập nhật bảng hiển thị sản phẩm đã thêm
    function updateImportItemsTable() {
        const tbody = document.getElementById('importItemsTable');
        
        if (ticketItemsForCreate.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px;text-align:center;color:#999;font-size:12px;">Chưa có sản phẩm nào</td></tr>';
            return;
        }

        tbody.innerHTML = ticketItemsForCreate.map((item, index) => `
            <tr style="border-bottom:1px solid #ddd;transition:all 0.2s;">
                <td style="padding:10px;">${item.product_name}</td>
                <td style="padding:10px;text-align:center;">${item.quantity}</td>
                <td style="padding:10px;text-align:right;">₫${parseFloat(item.import_price).toLocaleString('vi-VN')}</td>
                <td style="padding:10px;text-align:right;font-weight:600;">₫${item.total_price.toLocaleString('vi-VN')}</td>
                <td style="padding:10px;text-align:center;">
                    <button type="button" onclick="removeProductFromTicket(${index})" style="background:#dc3545;color:#fff;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;font-size:12px;transition:all 0.2s;">❌</button>
                </td>
            </tr>
        `).join('');
    }

    // Xóa sản phẩm khỏi danh sách
    function removeProductFromTicket(index) {
        if (confirm('Xóa sản phẩm này khỏi phiếu?')) {
            ticketItemsForCreate.splice(index, 1);
            updateImportItemsTable();
            calculateTotalPrice();
        }
    }

    // Tính tổng tiền nhập
    function calculateTotalPrice() {
        const total = ticketItemsForCreate.reduce((sum, item) => sum + item.total_price, 0);
        document.getElementById('totalImportPrice').textContent = '₫' + total.toLocaleString('vi-VN');
    }

    function displayImportTickets(tickets) {
        const list = document.getElementById('importTicketsList');
        if (!list) return;
        
        if (!tickets || tickets.length === 0) {
            list.innerHTML = '<p style="text-align:center;color:#999;">Không có phiếu nhập nào</p>';
            return;
        }

        list.innerHTML = tickets.map(ticket => `
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:start;">
                    <div>
                        <div style="font-weight:600;font-size:1.1rem;color:#333;">${ticket.ticket_number}</div>
                        <div style="color:#666;font-size:0.9rem;margin-top:4px;">📅 Ngày: ${new Date(ticket.import_date).toLocaleDateString('vi-VN')}</div>
                        <div style="margin-top:8px;">
                            <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:0.85rem;font-weight:600;background:${ticket.status === 'draft' ? '#fff3cd' : '#d4edda'};color:${ticket.status === 'draft' ? '#856404' : '#155724'};">
                                ${ticket.status === 'draft' ? '🟡 Nháp' : '🟢 Hoàn thành'}
                            </span>
                            <span style="margin-left:16px;color:#0066cc;font-weight:600;">
                                💰 ${formatMoney(ticket.total_import_price)} VNĐ
                            </span>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                        <button onclick="openTicketDetailModal(${ticket.id})" style="background:#007bff;color:#fff;border:none;padding:10px 16px;border-radius:4px;cursor:pointer;white-space:nowrap;">
                            ${ticket.status === 'draft' ? '✏️ Sửa phiếu' : '🔍 Xem chi tiết'}
                        </button>
                        ${ticket.status === 'draft' ? `
                            <button onclick="completeTicketById(${ticket.id})" style="background:#198754;color:#fff;border:none;padding:10px 16px;border-radius:4px;cursor:pointer;white-space:nowrap;">
                                ✅ Hoàn thành
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }

    async function openTicketDetailModal(ticketId) {
        currentImportTicketId = ticketId;
        try {
            await loadCurrentTicketDetail();
            document.getElementById('ticketDetailModal').style.display = 'flex';
        } catch (err) {
            console.error('Open detail modal error:', err);
            alert('❌ ' + (err.message || 'Không mở được chi tiết phiếu nhập'));
        }
    }

    function closeTicketDetailModal() {
        document.getElementById('ticketDetailModal').style.display = 'none';
        currentImportTicketId = null;
        currentTicketItems = [];
        selectedProductForDetail = null;
    }

    function updateTicketDetailPermissionUI() {
        const isDraft = currentTicketStatus === 'draft';
        const statusBadge = document.getElementById('detailTicketStatusBadge');
        const saveBtn = document.getElementById('saveTicketChangesBtn');
        const completeBtn = document.getElementById('completeTicketBtn');
        const addSection = document.getElementById('detailAddItemSection');
        const dateInput = document.getElementById('detailImportDate');
        const notesInput = document.getElementById('detailNotes');

        statusBadge.textContent = isDraft ? '🟡 Nháp' : '🟢 Hoàn thành';
        statusBadge.style.background = isDraft ? '#fff3cd' : '#d4edda';
        statusBadge.style.color = isDraft ? '#856404' : '#155724';

        if (addSection) addSection.style.display = isDraft ? 'block' : 'none';
        if (saveBtn) saveBtn.style.display = isDraft ? 'inline-block' : 'none';
        if (completeBtn) completeBtn.style.display = isDraft ? 'inline-block' : 'none';
        if (dateInput) dateInput.disabled = !isDraft;
        if (notesInput) notesInput.disabled = !isDraft;
    }

    function renderCurrentTicketItems() {
        const tbody = document.getElementById('detailItemsTable');
        if (!tbody) return;

        if (!currentTicketItems || currentTicketItems.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px;text-align:center;color:#999;font-size:12px;">Chưa có sản phẩm nào</td></tr>';
            document.getElementById('detailTotalImportPrice').textContent = '₫0';
            return;
        }

        const isDraft = currentTicketStatus === 'draft';
        let total = 0;

        tbody.innerHTML = currentTicketItems.map(item => {
            const lineTotal = Number(item.total_price || (Number(item.quantity || 0) * Number(item.import_price || 0)));
            total += lineTotal;

            return `
                <tr>
                    <td style="padding:10px;border-bottom:1px solid #ddd;">${escapeHtml(item.name)}</td>
                    <td style="padding:10px;text-align:center;border-bottom:1px solid #ddd;">${item.quantity}</td>
                    <td style="padding:10px;text-align:right;border-bottom:1px solid #ddd;">₫${formatMoney(item.import_price)}</td>
                    <td style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-weight:600;">₫${formatMoney(lineTotal)}</td>
                    <td style="padding:10px;text-align:center;border-bottom:1px solid #ddd;">
                        ${isDraft ? `
                            <button type="button" onclick="removeItemFromCurrentTicket(${item.id})" style="background:#dc3545;color:#fff;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;">❌</button>
                        ` : '-'}
                    </td>
                </tr>
            `;
        }).join('');

        document.getElementById('detailTotalImportPrice').textContent = '₫' + formatMoney(total);
    }

    async function loadCurrentTicketDetail() {
        if (!currentImportTicketId) return;

        const res = await fetch(`${API_BASE}import_tickets.php?action=detail&id=${currentImportTicketId}`);
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.message || 'Không tải được chi tiết phiếu');
        }

        const ticket = data.ticket || {};
        currentTicketStatus = ticket.status || 'draft';
        currentTicketItems = data.items || [];

        document.getElementById('detailTicketNumber').textContent = ticket.ticket_number || '-';
        document.getElementById('detailImportDate').value = (ticket.import_date || '').split(' ')[0];
        document.getElementById('detailNotes').value = ticket.notes || '';

        updateTicketDetailPermissionUI();
        renderCurrentTicketItems();
    }

    async function saveCurrentTicketChanges() {
        if (!currentImportTicketId) return;
        if (currentTicketStatus !== 'draft') {
            alert('❌ Chỉ phiếu ở trạng thái nháp mới được sửa.');
            return;
        }

        const importDate = document.getElementById('detailImportDate').value;
        const notes = document.getElementById('detailNotes').value || '';

        if (!importDate) {
            alert('❌ Vui lòng chọn ngày nhập.');
            return;
        }

        try {
            const res = await fetch(`${API_BASE}import_tickets.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update',
                    ticket_id: currentImportTicketId,
                    import_date: importDate,
                    notes: notes
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Lưu thay đổi thất bại');
            }

            alert('✅ Đã lưu thay đổi phiếu nhập.');
            await loadCurrentTicketDetail();
            searchImportTickets();
        } catch (err) {
            console.error('Save ticket error:', err);
            alert('❌ ' + (err.message || 'Lỗi khi lưu thay đổi phiếu nhập'));
        }
    }

    async function completeCurrentTicket() {
        if (!currentImportTicketId) return;
        if (currentTicketStatus !== 'draft') {
            alert('❌ Phiếu đã hoàn thành, không thể thao tác lại.');
            return;
        }

        if (!confirm('Xác nhận hoàn thành phiếu nhập này? Sau khi hoàn thành sẽ không thể sửa.')) {
            return;
        }

        try {
            const res = await fetch(`${API_BASE}import_tickets.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'complete',
                    ticket_id: currentImportTicketId
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Hoàn thành phiếu thất bại');
            }

            alert('✅ Hoàn thành phiếu nhập thành công.');
            await loadCurrentTicketDetail();
            searchImportTickets();
        } catch (err) {
            console.error('Complete ticket error:', err);
            alert('❌ ' + (err.message || 'Lỗi khi hoàn thành phiếu nhập'));
        }
    }

    async function completeTicketById(ticketId) {
        if (!confirm('Xác nhận hoàn thành phiếu này? Sau khi hoàn thành sẽ không thể sửa.')) {
            return;
        }

        try {
            const res = await fetch(`${API_BASE}import_tickets.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'complete',
                    ticket_id: ticketId
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Hoàn thành phiếu thất bại');
            }

            alert('✅ Hoàn thành phiếu nhập thành công.');
            searchImportTickets();
        } catch (err) {
            console.error('Quick complete error:', err);
            alert('❌ ' + (err.message || 'Lỗi khi hoàn thành phiếu nhập'));
        }
    }

    async function searchProductsForDetail() {
        const searchInput = document.getElementById('searchProductForDetail');
        const resultsDiv = document.getElementById('productSearchResultsDetail');
        const query = searchInput.value.trim();

        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`${API_BASE}products.php?name=${encodeURIComponent(query)}&limit=20`);
            const data = await response.json();

            if (!data.success || !data.data || data.data.length === 0) {
                resultsDiv.innerHTML = '<div style="padding:10px;color:#999;font-size:11px;">Không tìm thấy sản phẩm</div>';
                resultsDiv.style.display = 'block';
                return;
            }

            resultsDiv.innerHTML = data.data.map(product => `
                <div onclick="selectProductForDetail(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price || 0})"
                     style="padding:10px;border-bottom:1px solid #f0f0f0;cursor:pointer;background:#f9f9f9;font-size:11px;transition:all 0.2s;">
                    <strong>${escapeHtml(product.name)}</strong><br>
                    <small style="color:#666;">ID: ${product.id} | Giá: ₫${formatMoney(product.price || 0)}</small>
                </div>
            `).join('');
            resultsDiv.style.display = 'block';
        } catch (err) {
            console.error('Search detail product error:', err);
            resultsDiv.innerHTML = '<div style="padding:10px;color:red;font-size:11px;">Lỗi tìm kiếm sản phẩm</div>';
            resultsDiv.style.display = 'block';
        }
    }

    function selectProductForDetail(productId, productName, costPrice) {
        selectedProductForDetail = { id: productId, name: productName, costPrice: costPrice };
        document.getElementById('searchProductForDetail').value = productName;
        document.getElementById('productSearchResultsDetail').style.display = 'none';
        if ((Number(document.getElementById('detailPrice').value) || 0) === 0 && Number(costPrice || 0) > 0) {
            document.getElementById('detailPrice').value = Number(costPrice).toFixed(2);
        }
    }

    async function addProductToCurrentTicket() {
        if (!currentImportTicketId) return;
        if (currentTicketStatus !== 'draft') {
            alert('❌ Phiếu đã hoàn thành, không thể thêm sản phẩm.');
            return;
        }

        const quantity = parseInt(document.getElementById('detailQuantity').value, 10) || 0;
        const importPrice = parseFloat(document.getElementById('detailPrice').value) || 0;
        const query = document.getElementById('searchProductForDetail').value.trim();

        if (!query) {
            alert('❌ Vui lòng chọn sản phẩm.');
            return;
        }
        if (quantity < 1) {
            alert('❌ Số lượng phải >= 1.');
            return;
        }
        if (importPrice <= 0) {
            alert('❌ Giá nhập phải lớn hơn 0.');
            return;
        }

        let productId = selectedProductForDetail?.id || 0;

        if (!productId) {
            try {
                const response = await fetch(`${API_BASE}products.php?name=${encodeURIComponent(query)}&limit=20`);
                const data = await response.json();
                if (!data.success || !data.data || data.data.length === 0) {
                    throw new Error('Không tìm thấy sản phẩm để thêm');
                }
                productId = data.data[0].id;
            } catch (err) {
                alert('❌ ' + (err.message || 'Lỗi tìm sản phẩm'));
                return;
            }
        }

        try {
            const res = await fetch(`${API_BASE}import_tickets.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'add_item',
                    ticket_id: currentImportTicketId,
                    product_id: productId,
                    quantity: quantity,
                    import_price: importPrice
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Thêm sản phẩm thất bại');
            }

            document.getElementById('searchProductForDetail').value = '';
            document.getElementById('detailQuantity').value = '1';
            document.getElementById('detailPrice').value = '0';
            selectedProductForDetail = null;

            await loadCurrentTicketDetail();
            searchImportTickets();
        } catch (err) {
            console.error('Add current ticket item error:', err);
            alert('❌ ' + (err.message || 'Lỗi khi thêm sản phẩm vào phiếu'));
        }
    }

    async function removeItemFromCurrentTicket(itemId) {
        if (!currentImportTicketId) return;
        if (currentTicketStatus !== 'draft') {
            alert('❌ Phiếu đã hoàn thành, không thể xóa sản phẩm.');
            return;
        }

        if (!confirm('Xác nhận xóa sản phẩm này khỏi phiếu?')) {
            return;
        }

        try {
            const res = await fetch(`${API_BASE}import_tickets.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'delete_item',
                    item_id: itemId,
                    ticket_id: currentImportTicketId
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Xóa sản phẩm thất bại');
            }

            await loadCurrentTicketDetail();
            searchImportTickets();
        } catch (err) {
            console.error('Remove current ticket item error:', err);
            alert('❌ ' + (err.message || 'Lỗi khi xóa sản phẩm'));
        }
    }

    function openCreateTicketModal() {
        // Reset form
        ticketItemsForCreate = [];
        document.getElementById('importDate').valueAsDate = new Date();
        document.getElementById('notes').value = '';
        document.getElementById('searchProductForImport').value = '';
        document.getElementById('importQuantity').value = '1';
        document.getElementById('importPrice').value = '0';
        updateImportItemsTable();
        calculateTotalPrice();
        
        document.getElementById('createTicketModal').style.display = 'flex';
    }

    function closeCreateTicketModal() {
        document.getElementById('createTicketModal').style.display = 'none';
    }

    async function submitCreateTicket() {
        if (isCreatingTicket) return;

        const importDate = document.getElementById('importDate')?.value;
        const notes = document.getElementById('notes')?.value || '';
        const createBtn = document.querySelector('#createTicketModal button[onclick="submitCreateTicket()"]');
        let createdTicketId = null;

        // Kiểm tra xem có sản phẩm nào không
        if (ticketItemsForCreate.length === 0) {
            alert('❌ Vui lòng thêm ít nhất một sản phẩm vào phiếu!');
            return;
        }

        if (!importDate) {
            alert('❌ Vui lòng chọn ngày nhập!');
            return;
        }

        try {
            isCreatingTicket = true;
            if (createBtn) {
                createBtn.disabled = true;
                createBtn.style.opacity = '0.7';
                createBtn.textContent = 'Đang tạo...';
            }

            // Bước 1: Tạo phiếu
            const createRes = await fetch(`${API_BASE}import_tickets.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'create',
                    import_date: importDate,
                    notes: notes,
                    created_by: 1
                })
            });

            const createText = await createRes.text();
            let createData;
            try {
                createData = JSON.parse(createText);
            } catch (e) {
                throw new Error('Phản hồi tạo phiếu không phải JSON: ' + createText.slice(0, 120));
            }

            if (!createRes.ok || !createData.success) {
                throw new Error(createData.message || 'Tạo phiếu thất bại');
            }

            const ticketId = createData.ticketId;
            const ticketNumber = createData.ticketNumber;
            createdTicketId = ticketId;

            // Bước 2: Thêm từng item tuần tự để biết chính xác item lỗi
            for (const item of ticketItemsForCreate) {
                const addRes = await fetch(`${API_BASE}import_tickets.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'add_item',
                        ticket_id: ticketId,
                        product_id: item.product_id,
                        quantity: item.quantity,
                        import_price: item.import_price
                    })
                });

                const addText = await addRes.text();
                let addData;
                try {
                    addData = JSON.parse(addText);
                } catch (e) {
                    throw new Error(`Lỗi item "${item.product_name}": phản hồi không hợp lệ`);
                }

                if (!addRes.ok || !addData.success) {
                    throw new Error(`Lỗi item "${item.product_name}": ${addData.message || 'thất bại'}`);
                }
            }

            alert('✅ Tạo phiếu nhập thành công!\nMã phiếu: ' + ticketNumber);
            closeCreateTicketModal();

            // Reset form
            ticketItemsForCreate = [];
            document.getElementById('importDate').valueAsDate = new Date();
            document.getElementById('notes').value = '';
            document.getElementById('searchProductForImport').value = '';
            document.getElementById('importQuantity').value = '1';
            document.getElementById('importPrice').value = '0';
            updateImportItemsTable();
            calculateTotalPrice();

            // Reload danh sách phiếu
            searchImportTickets();
        } catch (err) {
            console.error('Create ticket flow error:', err);

            // Rollback phiếu nháp đã tạo nếu thêm item bị lỗi giữa chừng
            if (createdTicketId) {
                try {
                    await fetch(`${API_BASE}import_tickets.php`, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'delete_ticket',
                            ticket_id: createdTicketId
                        })
                    });
                } catch (rollbackErr) {
                    console.warn('Rollback ticket failed:', rollbackErr);
                }
            }

            alert('❌ ' + (err.message || 'Lỗi khi tạo phiếu nhập'));
        } finally {
            isCreatingTicket = false;
            if (createBtn) {
                createBtn.disabled = false;
                createBtn.style.opacity = '1';
                createBtn.textContent = '✅ Tạo Phiếu';
            }
        }
    }

    // Set default date + bind import events
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('importDate');
        if (dateInput) {
            dateInput.valueAsDate = new Date();
        }

        const createSearchInput = document.getElementById('searchProductForImport');
        if (createSearchInput) {
            createSearchInput.addEventListener('input', function() {
                searchProductsForImport();
            });
        }

        const detailSearchInput = document.getElementById('searchProductForDetail');
        if (detailSearchInput) {
            detailSearchInput.addEventListener('input', function() {
                searchProductsForDetail();
            });
        }

        const importSearchInput = document.getElementById('searchImportInput');
        if (importSearchInput) {
            importSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchImportTickets();
                }
            });
        }

        const pricingSearchInput = document.getElementById('pricingSearchProduct');
        if (pricingSearchInput) {
            pricingSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchPricingProduct();
                }
            });
        }

        const pricingCategoryFilter = document.getElementById('pricingCategoryFilter');
        if (pricingCategoryFilter) {
            pricingCategoryFilter.addEventListener('change', searchPricingProduct);
        }

        ['statusImportFilter', 'dateFromImportFilter', 'dateToImportFilter'].forEach(function(id) {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', searchImportTickets);
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const createResultsDiv = document.getElementById('productSearchResults');
            const detailResultsDiv = document.getElementById('productSearchResultsDetail');

            if (!e.target.closest('#searchProductForImport') && !e.target.closest('#productSearchResults')) {
                if (createResultsDiv) createResultsDiv.style.display = 'none';
            }

            if (!e.target.closest('#searchProductForDetail') && !e.target.closest('#productSearchResultsDetail')) {
                if (detailResultsDiv) detailResultsDiv.style.display = 'none';
            }
        });

        // Nếu mở trực tiếp tab imports thì tải danh sách ngay
        if (window.location.hash === '#imports') {
            searchImportTickets();
        }

        // Nếu mở trực tiếp tab pricing thì tải dữ liệu giá bán
        if (window.location.hash === '#pricing') {
            loadPricing();
        }
    });
>>>>>>> 937abcfea67d46dc7ea503bd772e4a33421a79f7
    </script>
</body>
</html>
