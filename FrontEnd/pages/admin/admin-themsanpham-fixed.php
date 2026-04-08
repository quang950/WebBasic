<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Quáº£n lÃ½ sáº£n pháº©m</title>
    <link rel="stylesheet" href="../../assets/css/admin-style-new.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="admin-nav">
                <h1>Admin Panel - 3 Boys Auto</h1>
                <div class="admin-actions">
                    <span id="admin-welcome">Xin chÃ o, Admin!</span>
                    <button onclick="goToHomePage()" class="home-btn">
                        <i class="fas fa-home"></i> Trang chá»§
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
                                <i class="fas fa-car"></i> Quáº£n lÃ½ sáº£n pháº©m
                            </a>
                        </li>
                        <li>
                            <a href="#orders" onclick="return showSection('orders')">
                                <i class="fas fa-file-invoice"></i> Quáº£n lÃ½ Ä‘Æ¡n hÃ ng
                            </a>
                        </li>
                        <li>
                            <a href="#imports" onclick="return showSection('imports')">
                                <i class="fas fa-truck"></i> Quáº£n lÃ½ nháº­p hÃ ng
                            </a>
                        </li>
                        <li>
                            <a href="#stock" onclick="return showSection('stock')">
                                <i class="fas fa-boxes"></i> Quáº£n lÃ½ tá»“n kho
                            </a>
                        </li>
                        <li>
                            <a href="#customers" onclick="return showSection('customers')">
                                <i class="fas fa-users"></i> Quáº£n lÃ½ khÃ¡ch hÃ ng
                            </a>
                        </li>
                        <li>
                            <a href="#pricing" onclick="return showSection('pricing')">
                                <i class="fas fa-tags"></i> Quáº£n lÃ½ giÃ¡ bÃ¡n
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="return showSection('categories')">
                                <i class="fas fa-list"></i> Quáº£n lÃ½ loáº¡i sáº£n pháº©m
                            </a>
                        </li>
                        <li>
                            <a href="#dashboard" onclick="return showSection('dashboard')">
                                <i class="fas fa-dashboard"></i> Thá»‘ng kÃª
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="admin-content">
                <!-- Quáº£n lÃ½ Ä‘Æ¡n hÃ ng -->
                <section id="orders-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quáº£n lÃ½ Ä‘Æ¡n hÃ ng</h2>
                    </div>
                    <div style="margin-bottom:18px;display:flex;gap:24px;flex-wrap:wrap;align-items:center;">
                        <label>NgÃ y Ä‘áº·t: <input type="date" id="orderDateFrom"></label>
                        <label>Ä‘áº¿n: <input type="date" id="orderDateTo"></label>
                        <label>TÃ¬nh tráº¡ng: 
                            <select id="orderStatusFilter">
                                <option value="">Táº¥t cáº£</option>
                                <option value="new">Má»›i Ä‘áº·t</option>
                                <option value="processing">ÄÃ£ xá»­ lÃ½</option>
                                <option value="delivered">ÄÃ£ giao</option>
                                <option value="cancelled">Há»§y</option>
                            </select>
                        </label>
                        <button onclick="filterAdminOrders()" class="search-btn"><i class="fas fa-search"></i> Lá»c</button>
                    </div>
                    <div id="adminOrdersGrid"></div>
                </section>

                <!-- Quáº£n lÃ½ phiáº¿u nháº­p hÃ ng -->
                <section id="imports-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quáº£n lÃ½ phiáº¿u nháº­p hÃ ng</h2>
                        <button onclick="openCreateTicketModal()" class="add-btn" style="padding:10px 14px;"><i class="fas fa-plus"></i> ThÃªm phiáº¿u nháº­p</button>
                    </div>

                    <!-- Search & Filter -->
                    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="searchImportInput" placeholder="TÃ¬m mÃ£ phiáº¿u nháº­p (VD: IT20260330...)" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:1;min-width:250px;">
                        <select id="statusImportFilter" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:0 0 150px;">
                            <option value="">Táº¥t cáº£ tráº¡ng thÃ¡i</option>
                            <option value="draft">NhÃ¡p</option>
                            <option value="completed">HoÃ n thÃ nh</option>
                        </select>
                        <input type="date" id="dateFromImportFilter" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:0 0 150px;">
                        <input type="date" id="dateToImportFilter" style="padding:10px 12px;border:1px solid #ddd;border-radius:6px;flex:0 0 150px;">
                        <button onclick="searchImportTickets()" class="search-btn" style="padding:10px 16px;"><i class="fas fa-search"></i> TÃ¬m</button>
                    </div>

                    <!-- Danh sÃ¡ch phiáº¿u nháº­p -->
                    <div id="importTicketsList"></div>
                </section>

                <!-- Quáº£n lÃ½ tá»“n kho -->
                <section id="stock-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quáº£n lÃ½ tá»“n kho</h2>
                    </div>
                    
                    <div style="margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="stockSearchName" placeholder="TÃ¬m theo tÃªn sáº£n pháº©m" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:250px;font-size:14px;">
                        <select id="stockCategoryFilter" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <option value="">Táº¥t cáº£ loáº¡i xe</option>
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="hatchback">Hatchback</option>
                            <option value="pickup">Pickup</option>
                        </select>
                        <button onclick="searchStockProduct()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> TÃ¬m kiáº¿m</button>
                    </div>

                    <!-- Tra cá»©u nháº­p-xuáº¥t-tá»“n -->
                    <div style="background:#f8f9fa;padding:20px;border-radius:8px;border:1px solid #dee2e6;margin-bottom:20px;">
                        <h3 style="color:#0d279d;margin-bottom:16px;font-size:1.1rem;">
                            <i class="fas fa-clipboard-list"></i> Tra cá»©u nháº­p-xuáº¥t-tá»“n cá»§a sáº£n pháº©m
                        </h3>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                            <input type="text" id="inventorySearchProduct" placeholder="Nháº­p mÃ£ xe hoáº·c tÃªn xe" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:300px;font-size:14px;">
                            <input type="date" id="inventoryFromDate" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <span style="color:#666;font-weight:600;">Ä‘áº¿n</span>
                            <input type="date" id="inventoryToDate" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <button onclick="searchInventoryReport()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> Tra cá»©u</button>
                        </div>
                        <div id="inventoryResult" style="margin-top:16px;display:none;">
                            <div style="background:#fff;padding:16px;border-radius:6px;border:1px solid #ddd;">
                                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px;">
                                    <div style="text-align:center;padding:12px;background:#e3f2fd;border-radius:6px;">
                                        <div style="color:#1976d2;font-size:0.9em;margin-bottom:4px;font-weight:600;">Tá»“n Ä‘áº§u ká»³</div>
                                        <div style="color:#0d47a1;font-size:1.5em;font-weight:700;">15</div>
                                    </div>
                                    <div style="text-align:center;padding:12px;background:#e8f5e9;border-radius:6px;">
                                        <div style="color:#2e7d32;font-size:0.9em;margin-bottom:4px;font-weight:600;">Sá»‘ lÆ°á»£ng nháº­p</div>
                                        <div style="color:#1b5e20;font-size:1.5em;font-weight:700;">10</div>
                                    </div>
                                    <div style="text-align:center;padding:12px;background:#fff3e0;border-radius:6px;">
                                        <div style="color:#f57c00;font-size:0.9em;margin-bottom:4px;font-weight:600;">Sá»‘ lÆ°á»£ng xuáº¥t</div>
                                        <div style="color:#e65100;font-size:1.5em;font-weight:700;">8</div>
                                    </div>
                                    <div style="text-align:center;padding:12px;background:#f3e5f5;border-radius:6px;">
                                        <div style="color:#7b1fa2;font-size:0.9em;margin-bottom:4px;font-weight:600;">Tá»“n cuá»‘i ká»³</div>
                                        <div style="color:#4a148c;font-size:1.5em;font-weight:700;">17</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Xe sáº¯p háº¿t hÃ ng -->
                    <div style="background:#fff;padding:20px;border-radius:8px;border:1px solid #e0e0e0;margin-bottom:20px;">
                        <h3 style="color:#ff9800;margin-bottom:16px;font-size:1.2rem;">
                            <i class="fas fa-exclamation-circle"></i> Cáº£nh bÃ¡o: Sáº£n pháº©m sáº¯p háº¿t hÃ ng (Sá»‘ lÆ°á»£ng â‰¤ 1)
                        </h3>
                        <div id="lowStockList"></div>
                    </div>

                    <!-- Xe tá»“n kho lÃ¢u -->
                    <div style="background:#fff;padding:20px;border-radius:8px;border:1px solid #e0e0e0;margin-bottom:20px;">
                        <h3 style="color:#d9534f;margin-bottom:16px;font-size:1.2rem;">
                            <i class="fas fa-exclamation-triangle"></i> Xe tá»“n kho lÃ¢u (TrÃªn 1 nÄƒm)
                        </h3>
                        <div id="oldStockList"></div>
                    </div>

                    <!-- Xe thÆ°á»ng -->
                    <div style="background:#fff;padding:20px;border-radius:8px;border:1px solid #e0e0e0;">
                        <h3 style="color:#28a745;margin-bottom:16px;font-size:1.2rem;">
                            <i class="fas fa-check-circle"></i> Xe thÆ°á»ng
                        </h3>
                        <div id="normalStockList"></div>
                    </div>
                </section>


                <!-- Quáº£n lÃ½ sáº£n pháº©m -->
                <section id="products-section" class="content-section active">
                    <div class="section-header">
                        <h2>Quáº£n lÃ½ sáº£n pháº©m</h2>
                        <button onclick="showAddProductModal()" class="add-btn">
                            <i class="fas fa-plus"></i> ThÃªm sáº£n pháº©m má»›i
                        </button>
                    </div>
                    <div class="products-grid" id="products-grid">
                        <!-- Danh sÃ¡ch sáº£n pháº©m sáº½ Ä‘Æ°á»£c hiá»ƒn thá»‹ á»Ÿ Ä‘Ã¢y -->
                    </div>
                </section>

                <!-- Quáº£n lÃ½ khÃ¡ch hÃ ng -->
                <section id="customers-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quáº£n lÃ½ khÃ¡ch hÃ ng</h2>
                    </div>
                    <div class="customers-grid" id="customers-grid">
                        <!-- Danh sÃ¡ch khÃ¡ch hÃ ng sáº½ Ä‘Æ°á»£c hiá»ƒn thá»‹ á»Ÿ Ä‘Ã¢y -->
                    </div>
                </section>

                <!-- Thá»‘ng kÃª -->
                                </section>

                <!-- Quáº£n lÃ½ giÃ¡ bÃ¡n -->
                <section id="pricing-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quáº£n lÃ½ giÃ¡ bÃ¡n</h2>
                    </div>
                    <div style="margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="pricingSearchProduct" placeholder="TÃ¬m theo tÃªn sáº£n pháº©m" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:250px;font-size:14px;">
                        <select id="pricingCategoryFilter" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <option value="">Táº¥t cáº£ loáº¡i xe</option>
                        </select>
                        <button onclick="searchPricingProduct()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> TÃ¬m kiáº¿m</button>
                    </div>
                    <div id="pricingGrid"></div>
                </section>

                <!-- Quáº£n lÃ½ loáº¡i sáº£n pháº©m -->
                <section id="categories-section" class="content-section" style="display:none">
                    <div class="section-header">
                        <h2>Quáº£n lÃ½ loáº¡i sáº£n pháº©m</h2>
                        <button onclick="window.location.href='admin-add-category.php'" class="add-btn">
                            <i class="fas fa-plus"></i> ThÃªm loáº¡i xe
                        </button>
                    </div>
                    <div style="margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                        <input type="text" id="categorySearchInput" placeholder="TÃ¬m theo tÃªn loáº¡i xe" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;min-width:250px;font-size:14px;">
                        <select id="categoryStatusFilter" style="padding:9px 12px;border-radius:6px;border:1px solid #ddd;font-size:14px;">
                            <option value="">Táº¥t cáº£ tráº¡ng thÃ¡i</option>
                            <option value="visible">Äang hiá»ƒn thá»‹</option>
                            <option value="hidden">Äang áº©n</option>
                        </select>
                        <button onclick="searchCategories()" class="search-btn" style="padding:9px 16px;"><i class="fas fa-search"></i> TÃ¬m kiáº¿m</button>
                    </div>
                    <table class="data-table" style="width:100%;table-layout:auto;">
                        <thead>
                            <tr>
                                <th style="color:#000;text-align:left;padding:12px;">TÃªn loáº¡i xe</th>
                                <th style="width:150px;color:#000;text-align:center;padding:12px;">Sá»‘ sáº£n pháº©m</th>
                                <th style="width:150px;color:#000;text-align:center;padding:12px;">Tráº¡ng thÃ¡i</th>
                                <th style="width:280px;color:#000;text-align:center;padding:12px;">Thao tÃ¡c</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <!-- Dá»¯ liá»‡u loáº¡i xe sáº½ Ä‘Æ°á»£c render á»Ÿ Ä‘Ã¢y -->
                        </tbody>
                    </table>
                </section>

                <!-- Dashboard -->
                <section id="dashboard-section" class="content-section">
                    <h2>Thá»‘ng kÃª</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-car"></i>
                            <div class="stat-info">
                                <h3 id="total-products">0</h3>
                                <p>Tá»•ng sáº£n pháº©m</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-eye"></i>
                            <div class="stat-info">
                                <h3 id="total-views">0</h3>
                                <p>LÆ°á»£t xem</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal thÃªm sáº£n pháº©m -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>ThÃªm sáº£n pháº©m má»›i</h3>
                <span class="close" onclick="closeAddProductModal()">&times;</span>
            </div>
            <form id="addProductForm">
                <div class="form-group">
                    <label for="productName">TÃªn sáº£n pháº©m:</label>
                    <input type="text" id="productName" name="productName" required>
                </div>
                
                <div class="form-group">
                    <label for="productBrand">ThÆ°Æ¡ng hiá»‡u:</label>
                    <select id="productBrand" name="productBrand" required>
                        <option value="">Chá»n thÆ°Æ¡ng hiá»‡u</option>
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
                    <label for="productPrice">GiÃ¡ (VNÄ):</label>
                    <input type="number" id="productPrice" name="productPrice" required min="0">
                </div>

                <div class="form-group">
                    <label for="productYear">NÄƒm sáº£n xuáº¥t:</label>
                    <input type="number" id="productYear" name="productYear" required min="2000" max="2025">
                </div>

                <div class="form-group">
                    <label for="productFuel">Loáº¡i nhiÃªn liá»‡u:</label>
                    <select id="productFuel" name="productFuel" required>
                        <option value="">Chá»n loáº¡i nhiÃªn liá»‡u</option>
                        <option value="XÄƒng">XÄƒng</option>
                        <option value="Dáº§u">Dáº§u</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Äiá»‡n">Äiá»‡n</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productTransmission">Há»™p sá»‘:</label>
                    <select id="productTransmission" name="productTransmission" required>
                        <option value="">Chá»n há»™p sá»‘</option>
                        <option value="Sá»‘ tá»± Ä‘á»™ng">Sá»‘ tá»± Ä‘á»™ng</option>
                        <option value="Sá»‘ sÃ n">Sá»‘ sÃ n</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productCategory">Loáº¡i sáº£n pháº©m:</label>
                    <select id="productCategory" name="productCategory">
                        <option value="">Chá»n loáº¡i</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productImageUrl">URL HÃ¬nh áº£nh:</label>
                    <input type="url" id="productImageUrl" name="productImageUrl" placeholder="https://example.com/image.jpg" required>
                    <small style="color: #666;">Nháº­p Ä‘Æ°á»ng dáº«n URL hÃ¬nh áº£nh sáº£n pháº©m</small>
                </div>

                <div class="form-group">
                    <label for="productDescription">MÃ´ táº£:</label>
                    <textarea id="productDescription" name="productDescription" rows="4"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeAddProductModal()" class="cancel-btn">Há»§y</button>
                    <button type="submit" class="save-btn" onclick="addProduct()">LÆ°u sáº£n pháº©m</button>
                </div>
            </form>
        </div>
    </div>

        <!-- Modal thÃªm / sá»­a phiáº¿u nháº­p -->
        <div id="addImportModal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>ThÃªm phiáº¿u nháº­p</h3>
                    <span class="close" onclick="closeAddImportModal()">&times;</span>
                </div>
                <form id="addImportForm">
                    <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;padding:12px 0;">
                        <label>NgÃ y nháº­p: <input type="date" id="legacyImportDate" name="legacyImportDate" required></label>
                        <label>MÃ£ phiáº¿u (Auto): <input type="text" id="importCode" name="importCode" disabled placeholder="(tá»± Ä‘á»™ng)"></label>
                    </div>

                    <div id="importItemsContainer">
                        <div class="import-item-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                            <select class="import-product-select" style="flex:2;padding:8px;border-radius:6px;border:1px solid #ccc;">
                                <option value="">-- Chá»n sáº£n pháº©m --</option>
                            </select>
                            <input type="number" class="import-price" placeholder="GiÃ¡ nháº­p" min="0" style="flex:1;padding:8px;border-radius:6px;border:1px solid #ccc;">
                            <input type="number" class="import-qty" placeholder="Sá»‘ lÆ°á»£ng" min="1" value="1" style="width:100px;padding:8px;border-radius:6px;border:1px solid #ccc;">
                            <button type="button" class="remove-item-btn" onclick="removeImportItemRow(this)" style="padding:8px 10px;border-radius:6px;background:#dc3545;color:#fff;border:none;">XÃ³a</button>
                        </div>
                    </div>

                    <div style="margin-top:8px;margin-bottom:12px;">
                        <button type="button" onclick="addImportItemRow()" class="add-btn" style="padding:8px 12px;border-radius:6px;"><i class="fas fa-plus"></i> ThÃªm dÃ²ng</button>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="closeAddImportModal()" class="cancel-btn">Há»§y</button>
                        <button type="submit" class="save-btn" onclick="return false;">LÆ°u phiáº¿u nháº­p</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal thÃªm / sá»­a loáº¡i sáº£n pháº©m -->
        <div id="addCategoryModal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="categoryModalTitle">ThÃªm loáº¡i sáº£n pháº©m</h3>
                    <span class="close" onclick="closeAddCategoryModal()">&times;</span>
                </div>
                <form id="addCategoryForm">
                    <input type="hidden" id="categoryEditId" value="">
                    
                    <div class="form-group">
                        <label for="categoryName">TÃªn loáº¡i xe:</label>
                        <input type="text" id="categoryName" name="categoryName" required placeholder="VÃ­ dá»¥: Toyota, Honda, BMW...">
                    </div>

                    <div class="form-group">
                        <label for="categorySlug">Slug (URL):</label>
                        <input type="text" id="categorySlug" name="categorySlug" required placeholder="VÃ­ dá»¥: toyota, honda, bmw...">
                        <small style="color:#666;font-size:12px;">Chá»‰ dÃ¹ng chá»¯ thÆ°á»ng, sá»‘ vÃ  dáº¥u gáº¡ch ngang</small>
                    </div>

                    <div class="form-group">
                        <label for="categoryDescription">MÃ´ táº£:</label>
                        <textarea id="categoryDescription" name="categoryDescription" rows="3" placeholder="MÃ´ táº£ vá» loáº¡i xe nÃ y..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="categoryVisible" name="categoryVisible" checked>
                            Hiá»ƒn thá»‹ trÃªn trang chá»§
                        </label>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="closeAddCategoryModal()" class="cancel-btn">Há»§y</button>
                        <button type="submit" class="save-btn" onclick="addProduct();">LÆ°u</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- MODAL: Create Import Ticket -->
    <div id="createTicketModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:1000;">
        <div style="background:#fff;border-radius:12px;width:95%;max-width:750px;box-shadow:0 8px 24px rgba(0,0,0,0.3);">
            <div style="padding:24px 28px;border-bottom:2px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="margin:0;font-size:1.3rem;color:#333;">ðŸ“‹ Táº¡o Phiáº¿u Nháº­p HÃ ng Má»›i</h3>
                <span onclick="closeCreateTicketModal()" style="cursor:pointer;font-size:28px;color:#999;">&times;</span>
            </div>
            <div style="padding:28px;max-height:70vh;overflow-y:auto;">
                <form id="createTicketForm" style="display:flex;flex-direction:column;gap:20px;">
                    <!-- NgÃ y nháº­p + Ghi chÃº -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;margin-bottom:8px;font-weight:700;color:#333;">ðŸ“… NgÃ y nháº­p <span style="color:red;">*</span></label>
                            <input type="date" id="importDate" style="width:100%;padding:12px;border:1.5px solid #ddd;border-radius:6px;font-size:1rem;" required>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:8px;font-weight:700;color:#333;">ðŸ“ Ghi chÃº</label>
                            <input type="text" id="notes" placeholder="Ghi chÃº phiáº¿u nháº­p..." style="width:100%;padding:12px;border:1.5px solid #ddd;border-radius:6px;font-size:0.95rem;">
                        </div>
                    </div>

                    <!-- TÃ¬m kiáº¿m & thÃªm sáº£n pháº©m -->
                    <div style="border:2px dashed #0d6efd;padding:16px;border-radius:8px;background:#f8f9ff;">
                        <h4 style="margin:0 0 12px 0;color:#0d6efd;">ðŸ” ThÃªm Sáº£n Pháº©m VÃ o Phiáº¿u</h4>
                        
                        <div style="display:grid;grid-template-columns:1fr 0.6fr 0.6fr auto;gap:10px;margin-bottom:12px;position:relative;">
                            <div style="position:relative;">
                                <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">TÃ¬m sáº£n pháº©m</label>
                                <input type="text" id="searchProductForImport" placeholder="Nháº­p tÃªn hoáº·c mÃ£..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                                <div id="productSearchResults" style="position:absolute;background:#fff;border:1px solid #ddd;border-radius:4px;max-height:150px;overflow-y:auto;display:none;width:100%;z-index:2000;top:100%;left:0;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                                    <!-- Results hiá»ƒn thá»‹ á»Ÿ Ä‘Ã¢y -->
                                </div>
                            </div>
                            <div>
                                <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Sá»‘ lÆ°á»£ng</label>
                                <input type="number" id="importQuantity" min="1" value="1" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                            </div>
                            <div>
                                <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">GiÃ¡ nháº­p</label>
                                <input type="number" id="importPrice" min="0" step="0.01" value="0" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <button type="button" onclick="addProductToTicket()" style="background:#0d6efd;color:#fff;border:none;padding:8px 14px;border-radius:4px;cursor:pointer;font-weight:600;white-space:nowrap;">âž• ThÃªm</button>
                            </div>
                        </div>
                        <small style="color:#666;">Ghi chÃº: Nháº­p sá»‘ lÆ°á»£ng & giÃ¡ mua, rá»“i báº¥m nÃºt ThÃªm</small>
                    </div>

                    <!-- Báº£ng sáº£n pháº©m Ä‘Ã£ thÃªm -->
                    <div>
                        <h4 style="margin:0 0 12px 0;color:#333;">ðŸ“¦ Sáº£n Pháº©m Trong Phiáº¿u</h4>
                        <table style="width:100%;border-collapse:collapse;border:1px solid #ddd;">
                            <thead>
                                <tr style="background:#f5f5f5;">
                                    <th style="padding:10px;text-align:left;border-bottom:1px solid #ddd;font-size:12px;">Sáº£n pháº©m</th>
                                    <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:80px;">Sá»‘ lÆ°á»£ng</th>
                                    <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:100px;">GiÃ¡ nháº­p</th>
                                    <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:100px;">ThÃ nh tiá»n</th>
                                    <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:40px;">XÃ³a</th>
                                </tr>
                            </thead>
                            <tbody id="importItemsTable">
                                <tr><td colspan="5" style="padding:20px;text-align:center;color:#999;font-size:12px;">ChÆ°a cÃ³ sáº£n pháº©m nÃ o</td></tr>
                            </tbody>
                        </table>
                        <div style="margin-top:12px;padding:12px;background:#f9f9f9;border-radius:4px;display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#666;font-weight:600;">ðŸ’° Tá»•ng tiá»n nháº­p:</span>
                            <span id="totalImportPrice" style="font-size:18px;font-weight:700;color:#dc3545;">â‚«0</span>
                        </div>
                    </div>
                </form>
            </div>
            <div style="padding:16px 28px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:12px;background:#fafafa;">
                <button onclick="closeCreateTicketModal()" style="background:#e9ecef;color:#333;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">Há»§y</button>
                <button onclick="submitCreateTicket()" style="background:#28a745;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-weight:600;">âœ… Táº¡o Phiáº¿u</button>
            </div>
        </div>
    </div>

    <!-- MODAL: Detail/Edit Import Ticket -->
    <div id="ticketDetailModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:1001;">
        <div style="background:#fff;border-radius:12px;width:95%;max-width:900px;box-shadow:0 8px 24px rgba(0,0,0,0.3);">
            <div style="padding:24px 28px;border-bottom:2px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="margin:0;font-size:1.3rem;color:#333;">ðŸ“„ Chi Tiáº¿t Phiáº¿u Nháº­p: <span id="detailTicketNumber">-</span></h3>
                <span onclick="closeTicketDetailModal()" style="cursor:pointer;font-size:28px;color:#999;">&times;</span>
            </div>

            <div style="padding:22px 28px;max-height:72vh;overflow-y:auto;display:flex;flex-direction:column;gap:16px;">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">Tráº¡ng thÃ¡i</label>
                        <div id="detailTicketStatusBadge" style="display:inline-block;padding:6px 12px;border-radius:16px;font-size:0.85rem;font-weight:700;background:#fff3cd;color:#856404;">NhÃ¡p</div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">ðŸ“… NgÃ y nháº­p</label>
                        <input type="date" id="detailImportDate" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">ðŸ’° Tá»•ng tiá»n</label>
                        <div id="detailTotalImportPrice" style="padding:10px 12px;border-radius:6px;background:#f9f9f9;color:#d9534f;font-weight:700;">â‚«0</div>
                    </div>
                </div>

                <div>
                    <label style="display:block;margin-bottom:6px;font-weight:600;color:#444;">ðŸ“ Ghi chÃº</label>
                    <input type="text" id="detailNotes" placeholder="Ghi chÃº phiáº¿u nháº­p..." style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                </div>

                <div id="detailAddItemSection" style="border:2px dashed #0d6efd;padding:16px;border-radius:8px;background:#f8f9ff;">
                    <h4 style="margin:0 0 12px 0;color:#0d6efd;">âž• ThÃªm sáº£n pháº©m vÃ o phiáº¿u</h4>
                    <div style="display:grid;grid-template-columns:1fr 0.5fr 0.6fr auto;gap:10px;align-items:end;position:relative;">
                        <div style="position:relative;">
                            <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">TÃ¬m sáº£n pháº©m</label>
                            <input type="text" id="searchProductForDetail" placeholder="Nháº­p tÃªn hoáº·c mÃ£..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                            <div id="productSearchResultsDetail" style="position:absolute;background:#fff;border:1px solid #ddd;border-radius:4px;max-height:150px;overflow-y:auto;display:none;width:100%;z-index:2002;top:100%;left:0;box-shadow:0 2px 8px rgba(0,0,0,0.1);"></div>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">Sá»‘ lÆ°á»£ng</label>
                            <input type="number" id="detailQuantity" min="1" value="1" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:12px;font-weight:600;color:#666;">GiÃ¡ nháº­p</label>
                            <input type="number" id="detailPrice" min="0" step="0.01" value="0" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:12px;">
                        </div>
                        <button type="button" onclick="addProductToCurrentTicket()" style="background:#0d6efd;color:#fff;border:none;padding:8px 14px;border-radius:4px;cursor:pointer;font-weight:600;white-space:nowrap;">âž• ThÃªm</button>
                    </div>
                </div>

                <div>
                    <h4 style="margin:0 0 12px 0;color:#333;">ðŸ“¦ Danh sÃ¡ch sáº£n pháº©m</h4>
                    <table style="width:100%;border-collapse:collapse;border:1px solid #ddd;">
                        <thead>
                            <tr style="background:#f5f5f5;">
                                <th style="padding:10px;text-align:left;border-bottom:1px solid #ddd;font-size:12px;">Sáº£n pháº©m</th>
                                <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:90px;">Sá»‘ lÆ°á»£ng</th>
                                <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:120px;">GiÃ¡ nháº­p</th>
                                <th style="padding:10px;text-align:right;border-bottom:1px solid #ddd;font-size:12px;width:120px;">ThÃ nh tiá»n</th>
                                <th style="padding:10px;text-align:center;border-bottom:1px solid #ddd;font-size:12px;width:70px;">XÃ³a</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsTable">
                            <tr><td colspan="5" style="padding:20px;text-align:center;color:#999;font-size:12px;">ChÆ°a cÃ³ sáº£n pháº©m nÃ o</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="padding:16px 28px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:12px;background:#fafafa;">
                <button onclick="closeTicketDetailModal()" style="background:#e9ecef;color:#333;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">ÄÃ³ng</button>
                <button id="saveTicketChangesBtn" onclick="saveCurrentTicketChanges()" style="background:#fd7e14;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">ðŸ’¾ LÆ°u thay Ä‘á»•i</button>
                <button id="completeTicketBtn" onclick="completeCurrentTicket()" style="background:#198754;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600;">âœ… HoÃ n thÃ nh phiáº¿u</button>
            </div>
        </div>
    </div>

        <script src="../../assets/js/admin.js"></script>
    <script>
        // Function Ä‘á»ƒ quay vá» trang chá»§
        function goToHomePage() {
            // LÆ°u tráº¡ng thÃ¡i admin Ä‘ang Ä‘Äƒng nháº­p khi vá» trang chá»§
            localStorage.setItem('adminViewingHome', 'true');
            window.location.href = '../../index.php';
        }

        // Kiá»ƒm tra Ä‘Äƒng nháº­p khi load trang
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('adminLoggedIn')) {
                window.location.href = 'admin-login.php';
                return;
            }
            
            const username = localStorage.getItem('adminUsername');
            if (username) {
                document.getElementById('admin-welcome').textContent = `Xin chÃ o, ${username}!`;
            }
            
            // Khá»Ÿi táº¡o loáº¡i sáº£n pháº©m vÃ  cáº­p nháº­t select
            if (typeof initCategories === 'function') initCategories();
            if (typeof updateCategorySelect === 'function') updateCategorySelect();

            // Tá»± Ä‘á»™ng nháº­p cÃ¡c xe cÃ³ sáºµn tá»« trang chá»§ (náº¿u Ä‘Ã£ Ä‘Æ°á»£c cache)
            if (typeof importHomepageCars === 'function') {
                try { importHomepageCars(true); } catch (e) { /* ignore */ }
            }

            loadProducts();
            updateStats();
            loadCategories(); // Load danh sÃ¡ch loáº¡i sáº£n pháº©m
        });

        // ========== QUáº¢N LÃ LOáº I Sáº¢N PHáº¨M ==========
        
        // Load vÃ  hiá»ƒn thá»‹ danh sÃ¡ch loáº¡i sáº£n pháº©m tá»« database API
        function loadCategories() {
            const tbody = document.getElementById('categoriesTableBody');
            
            if (!tbody) return;
            
            // Show loading
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Äang táº£i...</td></tr>';
            
            // Create abort controller with timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);
            
            // Fetch categories from API
            fetch('/WebBasic/BackEnd/api/categories.php', {
                signal: controller.signal
            })
                .then(response => response.json())
                .then(data => {
                    clearTimeout(timeoutId);
                    if (data.success && data.categories && data.categories.length > 0) {
                        tbody.innerHTML = data.categories.map(cat => `
                            <tr>
                                <td style="padding:12px;text-align:left;">${cat.name}</td>
                                <td style="padding:12px;text-align:center;">${cat.product_count || 0}</td>
                                <td style="padding:12px;text-align:center;">
                                    ${cat.is_visible 
                                        ? '<i class="fas fa-eye" style="color:#28a745;"></i> Hiá»ƒn thá»‹' 
                                        : '<i class="fas fa-eye-slash" style="color:#dc3545;"></i> áº¨n'
                                    }
                                </td>
                                <td style="padding:8px;text-align:center;">
                                    <div style="display:flex;gap:0.5rem;align-items:center;justify-content:center;">
                                        <button onclick="editCategory(${cat.id})" class="edit-btn" title="Sá»­a" style="padding:0.5rem;background:#007bff;color:white;border:none;border-radius:6px;cursor:pointer;">
                                            <i class="fas fa-edit"></i> Sá»­a
                                        </button>
                                        <button onclick="toggleHideCategory(${cat.id})" title="${cat.is_visible ? 'áº¨n' : 'Hiá»‡n'}" style="padding:0.5rem;background:#6c757d;color:white;border:none;border-radius:6px;cursor:pointer;">
                                            <i class="fas fa-${cat.is_visible ? 'eye-slash' : 'eye'}"></i> ${cat.is_visible ? 'áº¨n' : 'Hiá»‡n'}
                                        </button>
                                        <button onclick="deleteCategory(${cat.id})" class="delete-btn" title="XÃ³a" style="padding:0.5rem;background:#dc3545;color:white;border:none;border-radius:6px;cursor:pointer;">
                                            <i class="fas fa-trash"></i> XÃ³a
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#999;">ChÆ°a cÃ³ loáº¡i sáº£n pháº©m nÃ o</td></tr>';
                    }
                })
                .catch(err => {
                    clearTimeout(timeoutId);
                    console.error('Error loading categories:', err);
                    if (err.name === 'AbortError') {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#dc3545;">Kết nối timed out - Vui lòng tải lại trang</td></tr>';
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#dc3545;">Lỗi khi tải danh sách loại sản phẩm</td></tr>';
                    }
                });
        }

        // Hiá»ƒn thá»‹ modal thÃªm loáº¡i sáº£n pháº©m
        function showAddCategoryModal() {
            document.getElementById('categoryModalTitle').textContent = 'ThÃªm loáº¡i sáº£n pháº©m';
            document.getElementById('categoryEditId').value = '';
            document.getElementById('categoryName').value = '';
            document.getElementById('categoryDescription').value = '';
            document.getElementById('categoryVisible').checked = true;
            document.getElementById('addCategoryModal').style.display = 'block';
        }

        // ÄÃ³ng modal
        function closeAddCategoryModal() {
            document.getElementById('addCategoryModal').style.display = 'none';
        }

        // Sá»­a loáº¡i sáº£n pháº©m
        function editCategory(id) {
            alert('Chá»©c nÄƒng sá»­a chÆ°a kháº£ dá»¥ng');
        }

        // XÃ³a loáº¡i sáº£n pháº©m
        function deleteCategory(id) {
            if (!confirm('Báº¡n cÃ³ cháº¯c muá»‘n xÃ³a loáº¡i sáº£n pháº©m nÃ y?')) return;
            
            fetch('/WebBasic/BackEnd/api/categories.php', {
                method: 'DELETE',
                body: new URLSearchParams({id: id})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('XÃ³a thÃ nh cÃ´ng');
                    loadCategories();
                } else {
                    alert('Lá»—i: ' + data.message);
                }
            })
            .catch(err => alert('Lá»—i káº¿t ná»‘i: ' + err));
        }

        // áº¨n/Hiá»‡n loáº¡i sáº£n pháº©m
        function toggleHideCategory(id) {
            alert('Chá»©c nÄƒng áº©n/hiá»‡n chÆ°a kháº£ dá»¥ng');
        }

        // TÃ¬m kiáº¿m loáº¡i sáº£n pháº©m
        function searchCategories() {
            loadCategories();
        }

        // Load láº¡i dá»¯ liá»‡u loáº¡i sáº£n pháº©m
        function loadCategoriesData() {
            loadCategories();
        }


        // NO DUPLICATES

        // TÃ¬m kiáº¿m sáº£n pháº©m tá»“n kho
        function searchStockProduct() {
            const oldStockList = document.getElementById('oldStockList');
            if (!oldStockList) return;
            
            // Hiá»ƒn thá»‹ 1 xe Toyota Fortuner tá»“n kho lÃ¢u
            oldStockList.innerHTML = `
                <div style="margin-bottom: 15px;">
                    <button onclick="loadStockData()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-undo"></i> Quay láº¡i
                    </button>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
                    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                        <img src="/WebBasic/FrontEnd/assets/images/toyota-fortuner.jpg" alt="Toyota Fortuner" style="width:100%;height:180px;object-fit:cover;border-radius:6px;margin-bottom:12px;">
                        <h4 style="color:#333;margin-bottom:8px;">Toyota Fortuner 2023</h4>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>MÃ£:</strong> TK001
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>Loáº¡i:</strong> SUV
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>GiÃ¡ gá»‘c:</strong> 1.450.000.000 VNÄ
                        </div>
                        <div style="color:#d9534f;font-size:0.9em;margin-bottom:6px;">
                            <strong>Giáº£m giÃ¡:</strong> 10% (145.000.000 VNÄ)
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-bottom:6px;">
                            <strong>Sá»‘ lÆ°á»£ng tá»“n:</strong> 3 xe
                        </div>
                        <div style="color:#d9534f;font-size:0.9em;margin-bottom:6px;">
                            <strong>NgÃ y nháº­p:</strong> 15/09/2023 (410 ngÃ y)
                        </div>
                        <div style="color:#666;font-size:0.9em;margin-top:8px;padding:8px;background:#fff3cd;border-radius:6px;">
                            <strong>LÃ½ do tá»“n:</strong> Ãt ngÆ°á»i mua do giÃ¡ cao
                        </div>
                    </div>
                </div>
            `;
        }

        // Tra cá»©u nháº­p-xuáº¥t-tá»“n
        function searchInventoryReport() {
            const inventoryResult = document.getElementById('inventoryResult');
            if (!inventoryResult) return;
            
            inventoryResult.style.display = 'block';
            
            // ThÃªm nÃºt quay láº¡i
            const resultDiv = inventoryResult.querySelector('div > div:first-child');
            if (resultDiv && !document.getElementById('inventoryBackBtn')) {
                const backBtn = document.createElement('div');
                backBtn.id = 'inventoryBackBtn';
                backBtn.style.cssText = 'margin-bottom: 15px;';
                backBtn.innerHTML = `
                    <button onclick="hideInventoryReport()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-undo"></i> Quay láº¡i
                    </button>
                `;
                inventoryResult.insertBefore(backBtn, inventoryResult.firstChild);
            }
        }

        // áº¨n káº¿t quáº£ tra cá»©u
        function hideInventoryReport() {
            const inventoryResult = document.getElementById('inventoryResult');
            const backBtn = document.getElementById('inventoryBackBtn');
            if (inventoryResult) inventoryResult.style.display = 'none';
            if (backBtn) backBtn.remove();
        }

        // Load láº¡i dá»¯ liá»‡u tá»“n kho
        function loadStockData() {
            // Gá»i láº¡i hÃ m load dá»¯ liá»‡u ban Ä‘áº§u tá»« admin.js
            if (typeof window.initStockSection === 'function') {
                window.initStockSection();
            }
        }

        // TÃ¬m kiáº¿m sáº£n pháº©m giÃ¡ bÃ¡n
        function searchPricingProduct() {
            if (typeof window.loadPricing === 'function') {
                window.loadPricing();
            }
            return false;
        }

        // Load láº¡i dá»¯ liá»‡u giÃ¡ bÃ¡n
        function loadPricingData() {
            if (typeof window.loadPricing === 'function') {
                window.loadPricing();
            }
        }

        // Export cÃ¡c hÃ m ra window
        window.searchStockProduct = searchStockProduct;
        window.searchInventoryReport = searchInventoryReport;
        window.hideInventoryReport = hideInventoryReport;
        window.loadStockData = loadStockData;
        window.searchPricingProduct = searchPricingProduct;
        window.loadPricingData = loadPricingData;
        window.searchCategories = searchCategories;
        window.loadCategoriesData = loadCategoriesData;
    </script>
</body>
</html>
