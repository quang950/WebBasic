<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin-style-new.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .filter-section {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px solid #e0e0e0;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .filter-group input, .filter-group select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .filter-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn-filter {
            padding: 0.5rem 1rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-filter:hover { background: #0056b3; }

        .btn-reset {
            padding: 0.5rem 1rem;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-reset:hover { background: #556b7f; }

        /* Orders Table */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .orders-table th {
            background: #2c3e50;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #34495e;
        }

        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .orders-table tr:hover {
            background: #f9f9f9;
        }

        .order-id {
            color: #007bff;
            font-weight: 600;
        }

        .order-customer {
            color: #333;
            font-weight: 500;
        }

        .order-price {
            color: #28a745;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
        }

        .status-new {
            background: #ffc107;
            color: #333;
        }

        .status-processing {
            background: #0dcaf0;
            color: #fff;
        }

        .status-delivered {
            background: #28a745;
            color: #fff;
        }

        .status-cancelled {
            background: #dc3545;
            color: #fff;
        }

        .order-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-view {
            background: #17a2b8;
            color: white;
        }

        .btn-view:hover { background: #138496; }

        .btn-edit {
            background: #007bff;
            color: white;
        }

        .btn-edit:hover { background: #0056b3; }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .close-modal {
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }

        .close-modal:hover { color: #333; }

        .order-detail-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-block {
            background: #f9f9f9;
            padding: 1rem;
            border-r-dius: 6px;
            border-left: 4px solid #007bff;
        }

        .detail-label {
            font-size: 0.85rem;
            color: #999;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            font-size: 1rem;
            color: #333;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }

        .pagination a:hover {
            background: #e0e0e0;
        }

        .pagination .current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <script src="../../assets/js/config.js"></script>
    <script src="../../assets/js/admin.js"></script>

    <div class="dashboard-wrapper">
        <!-- Sidebar sẽ được load bởi admin.js -->
        
        <div class="main-content">
            <div class="dashboard-header">
                <h1><i class="fas fa-box"></i> Quản Lý Đơn Hàng</h1>
                <div class="header-actions">
                    <span id="adminName" style="color: #666;"></span>
                    <a href="#" onclick="logout()" class="logout-btn">Đăng Xuất</a>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <h3>Lọc Đơn Hàng</h3>
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>Trạng Thái:</label>
                        <select id="filterStatus">
                            <option value="">-- Tất Cả --</option>
                            <option value="new">Chưa Xử Lý</option>
                            <option value="processing">Đã Xác Nhận</option>
                            <option value="delivered">Đã Giao</option>
                            <option value="cancelled">Đã Hủy</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Từ Ngày:</label>
                        <input type="date" id="filterDateFrom">
                    </div>

                    <div class="filter-group">
                        <label>Đến Ngày:</label>
                        <input type="date" id="filterDateTo">
                    </div>

                    <div class="filter-group">
                        <label>Tỉnh/Thành Phố:</label>
                        <input type="text" id="filterProvince" placeholder="VD: Hồ Chí Minh">
                    </div>

                    <div class="filter-group">
                        <label>Quận/Huyện:</label>
                        <input type="text" id="filterDistrict" placeholder="VD: Quận 1">
                    </div>

                    <div class="filter-group">
                        <label>Sắp Xếp:</label>
                        <select id="sortBy">
                            <option value="created_at_DESC">Mới Nhất</option>
                            <option value="created_at_ASC">Tựa Nhất</option>
                            <option value="total_price_DESC">Giá Cao Nhất</option>
                            <option value="total_price_ASC">Giá Thấp Nhất</option>
                        </select>
                    </div>
                </div>

                <div class="filter-buttons">
                    <button class="btn-filter" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Lọc Dữ Liệu
                    </button>
                    <button class="btn-reset" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> Đặt Lại
                    </button>
                </div>
            </div>

            <!-- Orders Table -->
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Địa Chỉ Giao</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #999;">
                            Đang tải...
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div id="paginationContainer" class="pagination"></div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Chi Tiết Đơn Hàng #<span id="modalOrderId"></span></h2>
                <span class="close-modal" onclick="closeOrderModal()">&times;</span>
            </div>

            <div class="order-detail-grid" id="orderDetailBlock">
                <!-- Details will be inserted here -->
            </div>

            <h3>Sản Phẩm Đặt Mua</h3>
            <table class="orders-table" style="margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>Tên Sản Phẩm</th>
                        <th>Số Lượng</th>
                        <th>Đơn Giá</th>
                        <th>Thành Tiền</th>
                    </tr>
                </thead>
                <tbody id="modalItemsBody">
                </tbody>
            </table>

            <div class="form-group">
                <label>Cập Nhật Trạng Thái:</label>
                <select id="statusSelect" onchange="updateOrderStatus()">
                    <option value="">-- Chọn Trạng Thái --</option>
                    <option value="new">Chưa Xử Lý</option>
                    <option value="processing">Đã Xác Nhận</option>
                    <option value="delivered">Đã Giao</option>
                    <option value="cancelled">Đã Hủy</option>
                </select>
            </div>

            <button class="btn-sm btn-view" onclick="closeOrderModal()" style="width: 100%;">Đóng</button>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let totalPages = 1;
        let allOrders = [];

        document.addEventListener('DOMContentLoaded', async () => {
            // Check admin login
            if (localStorage.getItem('adminLoggedIn') !== 'true') {
                window.location.href = 'admin-login.php';
                return;
            }

            // Display admin name
            const adminEmail = localStorage.getItem('adminEmail');
            document.getElementById('adminName').textContent = 'Admin: ' + adminEmail;

            // Load orders
            await loadOrders();
        });

        async function loadOrders(page = 1) {
            try {
                const filters = {
                    status: document.getElementById('filterStatus').value,
                    dateFrom: document.getElementById('filterDateFrom').value,
                    dateTo: document.getElementById('filterDateTo').value,
                    province: document.getElementById('filterProvince').value,
                    district: document.getElementById('filterDistrict').value,
                    page: page,
                    limit: 10
                };

                const sortParts = document.getElementById('sortBy').value.split('_');
                filters.sortBy = sortParts[0];
                filters.sortOrder = sortParts[1];

                const queryString = new URLSearchParams(filters).toString();
                const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL)
                    ? BASE_URL + '/BackEnd/api/admin/get_orders_filtered.php?' + queryString
                    : '/WebBasic/BackEnd/api/admin/get_orders_filtered.php?' + queryString;

                const response = await fetch(apiUrl);
                const result = await response.json();

                if (result.success && result.data) {
                    allOrders = result.data;
                    totalPages = result.pagination.pages;
                    currentPage = page;

                    displayOrders();
                    displayPagination();
                } else {
                    document.getElementById('ordersTableBody').innerHTML = `
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #999;">
                                Không có dữ liệu
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Lỗi:', error);
                alert('Lỗi khi tải đơn hàng');
            }
        }

        function displayOrders() {
            let html = '';

            allOrders.forEach(order => {
                const statusClass = 'status-' + order.status;
                const statusLabel = {
                    'new': 'Chưa Xử Lý',
                    'processing': 'Đã Xác Nhận',
                    'delivered': 'Đã Giao',
                    'cancelled': 'Đã Hủy'
                }[order.status] || order.status;

                const customerName = (order.first_name || '') + ' ' + (order.last_name || '');
                const createdDate = new Date(order.created_at).toLocaleDateString('vi-VN');
                const totalPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_price);

                html += `
                    <tr>
                        <td class="order-id">#${order.id}</td>
                        <td class="order-customer">
                            ${customerName}<br>
                            <small style="color: #999;">${order.email}</small>
                        </td>
                        <td>${createdDate}</td>
                        <td class="order-price">${totalPrice}</td>
                        <td>
                            <span class="status-badge ${statusClass}">${statusLabel}</span>
                        </td>
                        <td style="font-size: 0.9rem;">
                            ${order.shipping_district}, ${order.shipping_province}
                        </td>
                        <td class="order-actions">
                            <button class="btn-sm btn-view" onclick="viewOrderDetails(${order.id})">
                                <i class="fas fa-eye"></i> Xem
                            </button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('ordersTableBody').innerHTML = html || `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: #999;">
                        Không có dữ liệu
                    </td>
                </tr>
            `;
        }

        function displayPagination() {
            let html = '';

            // Previous
            if (currentPage > 1) {
                html += `<a onclick="loadOrders(${currentPage - 1})"><i class="fas fa-chevron-left"></i></a>`;
            }

            // Pages
            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `<span class="current">${i}</span>`;
                } else {
                    html += `<a onclick="loadOrders(${i})">${i}</a>`;
                }
            }

            // Next
            if (currentPage < totalPages) {
                html += `<a onclick="loadOrders(${currentPage + 1})"><i class="fas fa-chevron-right"></i></a>`;
            }

            document.getElementById('paginationContainer').innerHTML = html;
        }

        function viewOrderDetails(orderId) {
            const order = allOrders.find(o => o.id === orderId);
            if (!order) return;

            document.getElementById('modalOrderId').textContent = orderId;

            // Display order details
            const customerName = (order.first_name || '') + ' ' + (order.last_name || '');
            const createdDate = new Date(order.created_at).toLocaleDateString('vi-VN');
            const totalPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_price);

            let detailHtml = `
                <div class="detail-block">
                    <div class="detail-label">Khách Hàng</div>
                    <div class="detail-value">${customerName} (${order.email})</div>
                </div>
                <div class="detail-block">
                    <div class="detail-label">Ngày Đặt</div>
                    <div class="detail-value">${createdDate}</div>
                </div>
                <div class="detail-block">
                    <div class="detail-label">Địa Chỉ Giao Hàng</div>
                    <div class="detail-value">${order.shipping_address}</div>
                </div>
                <div class="detail-block">
                    <div class="detail-label">Điện Thoại Giao Hàng</div>
                    <div class="detail-value">${order.shipping_phone}</div>
                </div>
                <div class="detail-block">
                    <div class="detail-label">Phương Thức Thanh Toán</div>
                    <div class="detail-value">${order.payment_method === 'cash' ? 'Tiền Mặt' : order.payment_method === 'transfer' ? 'Chuyển Khoản' : 'Trực Tuyến'}</div>
                </div>
                <div class="detail-block">
                    <div class="detail-label">Tổng Tiền</div>
                    <div class="detail-value" style="color: #28a745; font-size: 1.2rem;">${totalPrice}</div>
                </div>
            `;

            document.getElementById('orderDetailBlock').innerHTML = detailHtml;

            // Display items
            let itemsHtml = '';
            order.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                const itemPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price);
                const itemTotalPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(itemTotal);

                itemsHtml += `
                    <tr>
                        <td>${item.name || 'Sản phẩm (ID: ' + item.product_id + ')'}</td>
                        <td style="text-align: center;">${item.quantity}</td>
                        <td style="text-align: right;">${itemPrice}</td>
                        <td style="text-align: right; color: #28a745; font-weight: 600;">${itemTotalPrice}</td>
                    </tr>
                `;
            });

            document.getElementById('modalItemsBody').innerHTML = itemsHtml;

            // Set current status
            document.getElementById('statusSelect').value = order.status;

            // Store order ID for update
            document.getElementById('orderModal').dataset.orderId = orderId;

            // Show modal
            document.getElementById('orderModal').style.display = 'block';
        }

        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        async function updateOrderStatus() {
            const orderId = document.getElementById('orderModal').dataset.orderId;
            const newStatus = document.getElementById('statusSelect').value;

            if (!newStatus) {
                alert('Vui lòng chọn trạng thái');
                return;
            }

            try {
                const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL)
                    ? BASE_URL + '/BackEnd/api/admin/update_order_status.php'
                    : '/WebBasic/BackEnd/api/admin/update_order_status.php';

                const response = await fetch(apiUrl, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, status: newStatus })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Cập nhật trạng thái thành công');
                    closeOrderModal();
                    await loadOrders(currentPage);
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi:', error);
                alert('Lỗi khi cập nhật');
            }
        }

        function applyFilters() {
            loadOrders(1);
        }

        function resetFilters() {
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            document.getElementById('filterProvince').value = '';
            document.getElementById('filterDistrict').value = '';
            document.getElementById('sortBy').value = 'created_at_DESC';
            loadOrders(1);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
