// Admin Panel JavaScript
// ========== Quản lý khách hàng ==========
function initSampleCustomers() {
    // Luôn sử dụng dữ liệu mẫu cho prototype, không lấy từ localStorage users
    const sampleCustomers = [
        {
            firstName: 'Nguyễn Văn',
            lastName: 'An',
            email: 'nguyenvanan@gmail.com',
            phone: '0901234567',
            province: 'TP. Hồ Chí Minh',
            locked: false
        },
        {
            firstName: 'Trần Thị',
            lastName: 'Bình',
            email: 'tranthibinh@gmail.com',
            phone: '0912345678',
            province: 'Hà Nội',
            locked: false
        },
        {
            firstName: 'Lê Minh',
            lastName: 'Cường',
            email: 'leminhcuong@gmail.com',
            phone: '0923456789',
            province: 'Đà Nẵng',
            locked: true
        },
        {
            firstName: 'Phạm Thu',
            lastName: 'Hà',
            email: 'phamthuha@gmail.com',
            phone: '0934567890',
            province: 'Cần Thơ',
            locked: true
        }
    ];
    return sampleCustomers;
}

function loadCustomers() {
    const grid = document.getElementById('customers-grid');
    if (!grid) return;
    
    // Chỉ sử dụng dữ liệu mẫu, không lấy từ localStorage
    const users = initSampleCustomers();
    
    if (!users.length) {
        grid.innerHTML = '<div class="empty-state">Chưa có khách hàng nào đăng ký.</div>';
        return;
    }
    grid.innerHTML = `
        <table class=\"customers-table\" style=\"font-family:Arial,sans-serif;width:100%;table-layout:auto;\">
            <thead>
                <tr>
                    <th style=\"min-width:150px;\">Họ tên</th>
                    <th style=\"min-width:200px;\">Email</th>
                    <th style=\"min-width:120px;\">Điện thoại</th>
                    <th style=\"min-width:130px;\">Tỉnh/TP</th>
                    <th style=\"min-width:120px;\">Trạng thái</th>
                    <th style=\"min-width:280px;\">Hành động</th>
                </tr>
            </thead>
            <tbody>
                ${users.map((u, idx) => `
                    <tr>
                        <td>${u.firstName} ${u.lastName}</td>
                        <td>${u.email}</td>
                        <td>${u.phone || ''}</td>
                        <td>${u.province || ''}</td>
                        <td>
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.9em; font-weight: 600; white-space: nowrap; ${u.locked ? 'background: #ffebee; color: #c62828;' : 'background: #e8f5e9; color: #2e7d32;'}">
                                <i class="fas ${u.locked ? 'fa-lock' : 'fa-check-circle'}"></i> ${u.locked ? 'Đã khóa' : 'Hoạt động'}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <button onclick="showResetPasswordModal('${u.email}', '${u.firstName} ${u.lastName}')" class="reset-btn" style="padding:6px 12px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:5px;margin-right:6px;${u.locked ? 'opacity:0.5;cursor:not-allowed;' : ''}"><i class="fas fa-key"></i> Đặt lại mật khẩu</button>
                            <button onclick="return false;" class="lock-btn" style="padding:6px 12px;border-radius:6px;display:inline-flex;align-items:center;gap:5px;${u.locked ? 'background: #4caf50; border-color: #4caf50;' : 'background: #f44336; border-color: #f44336;'}">
                                <i class="fas ${u.locked ? 'fa-lock-open' : 'fa-lock'}"></i> ${u.locked ? 'Mở khóa' : 'Khóa'}
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function toggleLockUser(idx) {
    // Chức năng khóa/mở khóa đã bị vô hiệu hóa (Prototype mode)
    return;
}

// Không còn export toggleLockUser

// Dữ liệu sản phẩm và loại sản phẩm được lưu trong localStorage
let products = JSON.parse(localStorage.getItem('products')) || [];
let categories = JSON.parse(localStorage.getItem('categories')) || [];

// Khởi tạo dữ liệu đơn hàng mẫu nếu chưa có
function initSampleOrders() {
    let orders = JSON.parse(localStorage.getItem('orders')) || [];
    if (orders.length === 0) {
        const sampleOrders = [
            {
                id: 'DH001',
                date: '15/10/2025, 14:30',
                name: 'Nguyễn Văn An',
                phone: '0901234567',
                email: 'nguyenvanan@gmail.com',
                address: '123 Nguyễn Huệ, Quận 1, TP.HCM',
                paymentMethod: 'COD',
                status: 'Đã xử lý',
                items: [
                    {
                        id: 'SP001',
                        brand: 'Toyota',
                        name: 'Camry 2024',
                        price: 1200000000,
                        quantity: 1,
                        image: 'assets/images/toyota-camry.jpg'
                    }
                ],
                total: 1200000000,
                note: 'Giao hàng trong giờ hành chính'
            },
            {
                id: 'DH002',
                date: '20/10/2025, 10:15',
                name: 'Trần Thị Bình',
                phone: '0912345678',
                email: 'tranthibinh@gmail.com',
                address: '456 Lê Lợi, Quận 3, TP.HCM',
                paymentMethod: 'Chuyển khoản',
                status: 'Mới đặt',
                items: [
                    {
                        id: 'SP002',
                        brand: 'Honda',
                        name: 'City RS 2024',
                        price: 569000000,
                        quantity: 1,
                        image: 'assets/images/honda-city.jpg'
                    }
                ],
                total: 569000000,
                note: 'Khách hàng yêu cầu gọi trước khi giao'
            }
        ];
        localStorage.setItem('orders', JSON.stringify(sampleOrders));
    }
}

// Gọi hàm khởi tạo dữ liệu mẫu khi load trang
initSampleOrders();

// Hiển thị/ẩn các section
function showSection(sectionName) {
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
        section.style.display = 'none';
    });
    document.querySelectorAll('.sidebar-nav li').forEach(item => {
        item.classList.remove('active');
    });
    const sec = document.getElementById(sectionName + '-section');
    if (sec) {
        sec.classList.add('active');
        sec.style.display = '';
        // Mark nav active BEFORE loading section data to avoid UI flicker if a loader throws
        const link = document.querySelector(`.sidebar-nav a[href="#${sectionName}"]`);
        const li = link ? link.closest('li') : null;
        if (li) li.classList.add('active');

        // Load section data safely (guard undefined functions)
        try {
            if (sectionName === 'customers' && typeof loadCustomers === 'function') loadCustomers();
            if (sectionName === 'orders' && typeof loadAdminOrders === 'function') loadAdminOrders();
            if (sectionName === 'imports' && typeof loadImports === 'function') loadImports();
            if (sectionName === 'stock' && typeof loadOldStock === 'function') loadOldStock();
            if (sectionName === 'pricing' && typeof loadPricing === 'function') loadPricing();
        } catch (e) {
            console.warn('Section init error for', sectionName, e);
        }
    }
}

// Đăng xuất
function logout() {
    if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        localStorage.removeItem('adminLoggedIn');
        localStorage.removeItem('adminUsername');
        localStorage.removeItem('adminViewingHome');
        window.location.href = 'admin-login.html';
    }
}

// Hiển thị modal thêm sản phẩm
function showAddProductModal() {
    const modal = document.getElementById('addProductModal');
    const form = document.getElementById('addProductForm');
    const submitBtn = form.querySelector('.save-btn');
    const modalTitle = document.querySelector('#addProductModal .modal-header h3');
    
    // Reset về chế độ thêm mới
    form.dataset.editId = '';
    submitBtn.textContent = 'Lưu sản phẩm';
    submitBtn.setAttribute('onclick', 'return false;');
    modalTitle.textContent = 'Thêm sản phẩm mới';
    
    modal.style.display = 'block';
    updateCategorySelect();
}

// Đóng modal thêm sản phẩm
function closeAddProductModal() {
    const modal = document.getElementById('addProductModal');
    const form = document.getElementById('addProductForm');
    const submitBtn = form.querySelector('.save-btn');
    const modalTitle = document.querySelector('#addProductModal .modal-header h3');
    
    modal.style.display = 'none';
    form.reset();
    
    // Reset về chế độ thêm mới
    form.dataset.editId = '';
    submitBtn.textContent = 'Lưu sản phẩm';
    submitBtn.setAttribute('onclick', 'return false;');
    modalTitle.textContent = 'Thêm sản phẩm mới';
}

// Preview ảnh khi chọn file
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
        
        // Xóa URL nếu có file upload
        document.getElementById('productImageUrl').value = '';
    } else {
        preview.style.display = 'none';
    }
}

// Preview ảnh khi nhập URL
function previewUrlImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.value) {
        previewImg.src = input.value;
        preview.style.display = 'block';
        
        // Xóa file nếu có URL
        document.getElementById('productImage').value = '';
    } else {
        preview.style.display = 'none';
    }
}

// Đóng modal khi click bên ngoài
window.onclick = function(event) {
    const modal = document.getElementById('addProductModal');
    if (event.target == modal) {
        closeAddProductModal();
    }
}

// Thêm sản phẩm mới
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Chức năng thêm sản phẩm đã bị vô hiệu hóa (Prototype mode)
    return false;
});

// Tải và hiển thị danh sách sản phẩm
function loadProducts() {
    const productsGrid = document.getElementById('products-grid');

    if (!productsGrid) return;

    if (products.length === 0) {
        productsGrid.innerHTML = '<div class="empty-state">Chưa có sản phẩm nào. Hãy thêm hoặc nhập từ trang chủ!</div>';
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
            ${products.map(product => `
                <div class="product-card" data-id="${product.id}">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}" onerror="this.src='assets/images/logo-${product.brand}.png'">
                    </div>
                    <div class="product-info">
                        <h3>${product.name}</h3>
                        <p class="brand">${product.brand.toUpperCase()}</p>
                        <p class="price">${formatPrice(product.price)} VNĐ</p>
                        <div class="product-details">
                            <span><i class="fas fa-calendar"></i> ${product.year || ''}</span>
                            <span><i class="fas fa-gas-pump"></i> ${product.fuel || ''}</span>
                            <span><i class="fas fa-cogs"></i> ${product.transmission || ''}</span>
                            ${product.category ? `<span><i class="fas fa-tags"></i> ${product.category}</span>` : ''}
                        </div>
                        <div class="product-actions">
                            <button onclick="editProduct(${product.id})" class="edit-btn" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-edit"></i> Sửa</button>
                            <button onclick="return false;" class="${product.hidden ? 'unhide-btn' : 'hide-btn'}" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas ${product.hidden ? 'fa-eye' : 'fa-eye-slash'}"></i> ${product.hidden ? 'Hiện' : 'Ẩn'}
                            </button>
                            <button onclick="return false;" class="delete-btn" style="padding:4px 10px;font-size:0.95em;border-radius:6px;min-width:0;line-height:1.2;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-trash"></i> Xóa</button>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Các chức năng sửa/xóa/ẩn sản phẩm đã bị vô hiệu hóa (Prototype mode)

// Sửa sản phẩm - chỉ hiển thị modal, không cho lưu
function editProduct(productId) {
    let product = products.find(p => p.id === productId);
    
    // Nếu không tìm thấy, dùng dữ liệu Toyota Camry mặc định
    if (!product) {
        product = {
            id: 1,
            name: 'Camry',
            brand: 'toyota',
            price: 1235000000,
            year: 2025,
            fuel: 'Xăng',
            transmission: 'Tự động (AT)',
            image: 'assets/images/toyota-camry.jpg',
            category: 'sedan',
            description: 'Sedan hạng D êm ái, tiện nghi, tiết kiệm.'
        };
    }
    
    // Điền thông tin vào form
    document.getElementById('productName').value = product.name;
    document.getElementById('productBrand').value = product.brand;
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productYear').value = product.year;
    document.getElementById('productFuel').value = product.fuel;
    document.getElementById('productTransmission').value = product.transmission;
    document.getElementById('productImageUrl').value = product.image;
    document.getElementById('productCategory').value = product.category || '';
    document.getElementById('productDescription').value = product.description;
    
    // Thay đổi form để chế độ chỉnh sửa
    const form = document.getElementById('addProductForm');
    form.dataset.editId = productId;
    
    // Thay đổi nút submit - thêm onclick="return false;" để không lưu
    const submitBtn = form.querySelector('.save-btn');
    submitBtn.textContent = 'Cập nhật sản phẩm';
    submitBtn.setAttribute('onclick', 'return false;');
    
    // Thay đổi tiêu đề modal
    document.querySelector('#addProductModal .modal-header h3').textContent = 'Chỉnh sửa sản phẩm';
    
    showAddProductModal();
}

// Cập nhật thống kê
function updateStats() {
    document.getElementById('total-products').textContent = products.length;
    
    // Tính tổng lượt xem (giả lập)
    const totalViews = products.reduce((sum, product) => sum + (product.views || Math.floor(Math.random() * 100)), 0);
    document.getElementById('total-views').textContent = totalViews;
}

// Format giá tiền
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

// Định dạng ngày về dd/mm/yyyy
function formatDateVN(dateStr) {
    if (!dateStr) return '';
    // Nếu đã đúng định dạng dd/mm/yyyy thì trả về luôn
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) return dateStr;
    // Nếu là yyyy-mm-dd hoặc yyyy/mm/dd
    let d = dateStr.split(/[-\/]/);
    if (d.length === 3) {
        // yyyy-mm-dd -> dd/mm/yyyy
        return `${d[2].padStart(2,'0')}/${d[1].padStart(2,'0')}/${d[0]}`;
    }
    return dateStr;
}

// Lightweight accessor for orders used by stock helpers
function loadOrders() {
    return JSON.parse(localStorage.getItem('orders')) || [];
}


// ========== Quản lý đơn hàng cho admin ==========
function loadAdminOrders() {
    const orders = JSON.parse(localStorage.getItem('orders')) || [];
    const grid = document.getElementById('adminOrdersGrid');
    if (!grid) return;
    if (!orders.length) {
        grid.innerHTML = '<div class="empty-state">Chưa có đơn hàng nào.</div>';
        return;
    }
    grid.innerHTML = renderAdminOrdersTable(orders);
}

function filterAdminOrders() {
    const ordersGrid = document.getElementById('adminOrdersGrid');
    if (!ordersGrid) return false;
    
    // Hiển thị 1 đơn hàng mẫu
    const sampleOrder = {
        id: 'DH001',
        date: '11/11/2025',
        name: 'Nguyễn Văn A',
        phone: '0901234567',
        address: '123 Đường ABC, Quận 1, TP.HCM',
        paymentMethod: 'Chuyển khoản',
        status: 'Mới đặt',
        items: [
            {
                brand: 'Toyota',
                name: 'Camry',
                quantity: 1,
                price: 1235000000
            }
        ],
        total: 1235000000,
        note: 'Giao hàng trong giờ hành chính'
    };
    
    ordersGrid.innerHTML = `
        <div style="margin-bottom: 20px;">
            <button onclick="loadAdminOrders()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-undo"></i> Quay lại
            </button>
        </div>
        ${renderAdminOrdersTable([sampleOrder])}
    `;
    return false;
}

function renderAdminOrdersTable(orders) {
    if (!orders.length) return '<div class="empty-state">Không có đơn hàng phù hợp.</div>';
    
    return `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(400px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${orders.slice().reverse().map(order => {
            const statusText = order.status === 'Mới đặt' ? 'Mới đặt' : 
                              order.status === 'Đã xử lý' ? 'Đã xử lý' : 
                              order.status === 'Đã giao' ? 'Đã giao' : 
                              order.status === 'Đã hủy' ? 'Đã hủy' : order.status;
            
            const itemsHtml = order.items.map(item => `
                <div style='margin-bottom:8px;padding:8px;background:#f9f9f9;border-radius:6px;font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;font-family:Arial,sans-serif;font-weight:normal;'>Xe: ${item.brand} ${item.name}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Số lượng: ${item.quantity}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Giá: ${formatPrice(item.price)} VNĐ</div>
                </div>
            `).join('');
            
            return `
                <div style='background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;box-shadow:0 2px 4px rgba(0,0,0,0.05);font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Mã đơn: ${order.id}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Ngày đặt: ${order.date}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Người nhận: ${order.name}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Số điện thoại: ${order.phone}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Địa chỉ: ${order.address}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Thanh toán: ${order.paymentMethod}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:12px;font-family:Arial,sans-serif;font-weight:normal;display:flex;align-items:center;gap:10px;'>
                        <span>Tình trạng:</span>
                        <select onchange='updateOrderStatus("${order.id}", this.value)' style='padding:6px 10px;border-radius:6px;border:1px solid #ddd;font-size:0.95em;background:#fff;cursor:pointer;'>
                            <option value='Mới đặt' ${order.status==='Mới đặt'?'selected':''}>Mới đặt</option>
                            <option value='Đã xử lý' ${order.status==='Đã xử lý'?'selected':''}>Đã xử lý</option>
                            <option value='Đã giao' ${order.status==='Đã giao'?'selected':''}>Đã giao</option>
                            <option value='Đã hủy' ${order.status==='Đã hủy'?'selected':''}>Đã hủy</option>
                        </select>
                    </div>
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Chi tiết xe đã mua:</div>
                        ${itemsHtml}
                    </div>
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1.1em;text-align:right;font-family:Arial,sans-serif;font-weight:normal;'>Tổng: ${formatPrice(order.total)} VNĐ</div>
                    </div>
                    ${order.note ? `<div style='color:#000;font-size:1em;margin-top:12px;padding:8px;background:#fff3cd;border-radius:6px;font-family:Arial,sans-serif;font-weight:normal;'>Ghi chú: ${order.note}</div>` : ''}
                </div>
            `;
        }).join('')}
    </div>`;
}

function renderOrderStatusSelect(order) {
    const status = order.status || 'new';
    return `<select onchange='updateOrderStatus(${order.id}, this.value)' style='padding:4px 8px;border-radius:6px;'>
        <option value='new' ${status==='new'?'selected':''}>Mới đặt</option>
        <option value='processing' ${status==='processing'?'selected':''}>Đã xử lý</option>
        <option value='delivered' ${status==='delivered'?'selected':''}>Đã giao</option>
        <option value='cancelled' ${status==='cancelled'?'selected':''}>Hủy</option>
    </select>`;
}

function updateOrderStatus(orderId, newStatus) {
    // Chức năng cập nhật trạng thái đã bị vô hiệu hóa (Prototype mode)
    // Dropdown vẫn có thể thay đổi nhưng không lưu dữ liệu
    return false;
}

// Export function
window.updateOrderStatus = updateOrderStatus;

function showOrderDetail(orderId) {
    const orders = JSON.parse(localStorage.getItem('orders')) || [];
    const order = orders.find(o => o.id === orderId);
    if (!order) return;
    let html = `<div class='order-detail-modal' style='position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);z-index:9999;display:flex;align-items:center;justify-content:center;'>
        <div style='background:#fff;padding:32px 24px;border-radius:12px;max-width:600px;width:100%;box-shadow:0 2px 16px rgba(0,0,0,0.12);position:relative;'>
            <button onclick='this.parentElement.parentElement.remove()' style='position:absolute;top:12px;right:12px;background:#dc3545;color:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:1.2em;cursor:pointer;'>&times;</button>
            <h3 style='margin-bottom:12px;'>Chi tiết đơn hàng #${order.id}</h3>
            <div style='margin-bottom:8px;'><strong>Ngày đặt:</strong> ${order.date}</div>
            <div style='margin-bottom:8px;'><strong>Người nhận:</strong> ${order.name} | <strong>ĐT:</strong> ${order.phone}</div>
            <div style='margin-bottom:8px;'><strong>Địa chỉ:</strong> ${order.address}</div>
            <div style='margin-bottom:8px;'><strong>Thanh toán:</strong> ${order.payment === 'cod' ? 'Tiền mặt khi nhận hàng' : (order.payment === 'bank' ? 'Chuyển khoản' : 'Thanh toán trực tuyến')}</div>
            <div style='margin-bottom:8px;'><strong>Tình trạng:</strong> ${renderOrderStatusSelect(order)}</div>
            <table style='width:100%;border-collapse:collapse;margin-top:12px;'>
                <thead><tr style='background:#007bff;color:#fff;'><th>Ảnh</th><th>Tên xe</th><th>Giá</th><th>Số lượng</th></tr></thead>
                <tbody>
                    ${order.items.map(item => `<tr><td><img src='${item.img}' alt='${item.name}' style='width:60px;border-radius:8px;'></td><td>${item.name}</td><td>${formatPrice(item.price)} VNĐ</td><td>${item.quantity}</td></tr>`).join('')}
                </tbody>
            </table>
            <div style='text-align:right;font-weight:600;font-size:1.1rem;margin-top:12px;'>Tổng cộng: ${formatPrice(order.total)} VNĐ</div>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
    // Tạo element thông báo
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Thêm vào body
    document.body.appendChild(notification);
    
    // Hiển thị với animation
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Ẩn sau 3 giây
    setTimeout(() => {
        notification.classList.remove('show');
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

// ========== Quản lý loại sản phẩm ==========

function initCategories() {
    // Nếu lần đầu chưa có categories thì tạo vài loại cơ bản
    if (!categories || !categories.length) {
        categories = [
            { id: 1, name: 'SUV', slug: 'suv', hidden: false },
            { id: 2, name: 'Sedan', slug: 'sedan', hidden: false },
            { id: 3, name: 'MPV', slug: 'mpv', hidden: false },
            { id: 4, name: 'Hatchback', slug: 'hatchback', hidden: false },
            { id: 5, name: 'Bán tải', slug: 'pickup', hidden: false }
        ];
        localStorage.setItem('categories', JSON.stringify(categories));
    }
}

function loadCategories() {
    const grid = document.getElementById('categories-grid');
    if (!grid) return;
    if (!categories.length) {
        grid.innerHTML = '<div class="empty-state">Chưa có loại sản phẩm nào.</div>';
        return;
    }
    grid.innerHTML = categories.map(c => `
        <div class="product-card" data-id="${c.id}">
            <div class="product-info">
                <h3>${c.name}</h3>
                <p class="brand">/${c.slug}</p>
                <div class="product-actions">
                    <button onclick="return false;" class="edit-btn" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas fa-edit"></i> Sửa
                    </button>
                    <button onclick="return false;" class="${c.hidden ? 'unhide-btn' : 'hide-btn'}" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas ${c.hidden ? 'fa-eye' : 'fa-eye-slash'}"></i> ${c.hidden ? 'Hiện' : 'Ẩn'}
                    </button>
                    <button onclick="return false;" class="delete-btn" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Các chức năng thêm/sửa/xóa danh mục đã bị vô hiệu hóa (Prototype mode)

function updateCategorySelect() {
    const sel = document.getElementById('productCategory');
    if (!sel) return;
    const visible = (categories || []).filter(c => !c.hidden);
    sel.innerHTML = '<option value="">Chọn loại</option>' + visible.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
}

// ========== Nhập xe đã có từ trang chủ ==========
function importHomepageCars(silent = false) {
    const cached = JSON.parse(localStorage.getItem('homepageCars') || '[]');
    if (!cached.length) {
        if (!silent) showNotification('Không tìm thấy dữ liệu xe trang chủ để nhập.', 'info');
        return 0;
    }
    let imported = 0;
    cached.forEach(item => {
        // Bỏ qua nếu đã tồn tại theo name+brand
        if (products.some(p => p.name === item.name && p.brand === item.brand)) return;
        products.push({
            id: Date.now() + Math.floor(Math.random()*1000),
            name: item.name,
            brand: item.brand,
            price: item.price || 0,
            year: item.year || new Date().getFullYear(),
            fuel: item.fuel || 'Xăng',
            transmission: item.transmission || 'Tự động (AT)',
            image: item.image,
            description: item.description || '',
            category: item.category || '',
            dateAdded: new Date().toISOString(),
            hidden: false
        });
        imported++;
    });
    localStorage.setItem('products', JSON.stringify(products));
    loadProducts();
    updateStats();
    if (!silent) showNotification(`Đã nhập ${imported} sản phẩm từ trang chủ`, 'success');
    return imported;
}

// Gắn import vào window để gọi từ HTML nếu cần
window.importHomepageCars = importHomepageCars;

// ========== Quản lý phiếu nhập (imports) ==========
let importsData = JSON.parse(localStorage.getItem('imports')) || [];

// Khởi tạo dữ liệu phiếu nhập mẫu nếu chưa có
function initSampleImports() {
    let imports = JSON.parse(localStorage.getItem('imports')) || [];
    if (imports.length === 0) {
        const sampleImports = [
            {
                id: 1,
                code: 'PN001',
                date: '05/10/2025',
                supplier: 'Công ty TNHH Ô tô Thành Công',
                items: [
                    {
                        productId: 'SP001',
                        brand: 'Toyota',
                        name: 'Camry 2024',
                        price: 1100000000,
                        qty: 3
                    },
                    {
                        productId: 'SP002',
                        brand: 'Toyota',
                        name: 'Vios 2024',
                        price: 480000000,
                        qty: 5
                    }
                ],
                subtotal: 5700000000,
                tax: 570000000,
                total: 6270000000,
                completed: true,
                note: 'Đã kiểm tra chất lượng và nhập kho đầy đủ'
            },
            {
                id: 2,
                code: 'PN002',
                date: '12/10/2025',
                supplier: 'Tổng công ty Ô tô Sài Gòn',
                items: [
                    {
                        productId: 'SP003',
                        brand: 'Honda',
                        name: 'City RS 2024',
                        price: 550000000,
                        qty: 4
                    },
                    {
                        productId: 'SP004',
                        brand: 'Honda',
                        name: 'CR-V 2024',
                        price: 1100000000,
                        qty: 2
                    }
                ],
                subtotal: 4400000000,
                tax: 440000000,
                total: 4840000000,
                completed: false,
                note: 'Đang chờ thanh toán đợt 2'
            }
        ];
        localStorage.setItem('imports', JSON.stringify(sampleImports));
        importsData = sampleImports;
    }
}

function saveImports() {
    localStorage.setItem('imports', JSON.stringify(importsData));
}

function loadImports() {
    initSampleImports(); // Khởi tạo dữ liệu mẫu nếu chưa có
    importsData = JSON.parse(localStorage.getItem('imports')) || [];
    const grid = document.getElementById('importsGrid');
    if (!grid) return;
    if (!importsData.length) {
        grid.innerHTML = '<div class="empty-state">Chưa có phiếu nhập nào.</div>';
        return;
    }
    grid.innerHTML = renderImportsTable(importsData);
}

function renderImportsTable(list) {
    if (!list || !list.length) return '<div class="empty-state">Không có phiếu nhập phù hợp.</div>';
    
    return `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(450px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${list.slice().reverse().map(i => {
            const itemsHtml = i.items.map(item => `
                <div style='margin-bottom:8px;padding:8px;background:#f9f9f9;border-radius:6px;font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;font-family:Arial,sans-serif;font-weight:normal;'>Xe: ${item.brand} ${item.name}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Số lượng: ${item.qty}</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Giá nhập: ${formatPrice(item.price)} VNĐ</div>
                    <div style='color:#000;font-size:1em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Thành tiền: ${formatPrice(item.price * item.qty)} VNĐ</div>
                </div>
            `).join('');
            
            const statusText = i.completed ? 'Đã hoàn thành' : 'Chưa hoàn thành';
            const statusColor = i.completed ? 'green' : '#ff9800';
            
            return `
                <div style='background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:16px;box-shadow:0 2px 4px rgba(0,0,0,0.05);font-family:Arial,sans-serif;'>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Mã phiếu: ${i.code || i.id}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Ngày nhập hàng: ${formatDateVN(i.date)}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Nhà cung cấp: ${i.supplier || 'Chưa cập nhật'}</div>
                    <div style='color:#000;font-size:1em;margin-bottom:12px;font-family:Arial,sans-serif;font-weight:normal;'>Tình trạng: <span style='color:${statusColor};'>${statusText}</span></div>
                    
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Chi tiết xe đã nhập:</div>
                        ${itemsHtml}
                    </div>
                    
                    <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                        <div style='color:#000;font-size:1em;margin-bottom:4px;font-family:Arial,sans-serif;font-weight:normal;'>Tạm tính: ${formatPrice(i.subtotal || i.items.reduce((s,it)=>s+ (Number(it.price)||0)*Number(it.qty),0))} VNĐ</div>
                        <div style='color:#000;font-size:1em;margin-bottom:4px;font-family:Arial,sans-serif;font-weight:normal;'>Thuế (10%): ${formatPrice(i.tax || (i.subtotal || i.items.reduce((s,it)=>s+ (Number(it.price)||0)*Number(it.qty),0)) * 0.1)} VNĐ</div>
                        <div style='color:#000;font-size:1.1em;text-align:right;font-family:Arial,sans-serif;font-weight:normal;'>Tổng tiền: ${formatPrice(i.total || (i.subtotal || i.items.reduce((s,it)=>s+ (Number(it.price)||0)*Number(it.qty),0)) * 1.1)} VNĐ</div>
                    </div>
                    
                    ${i.note ? `<div style='color:#000;font-size:1em;margin-top:12px;padding:8px;background:#fff3cd;border-radius:6px;font-family:Arial,sans-serif;font-weight:normal;'>Ghi chú: ${i.note}</div>` : ''}
                    
                    <div style='margin-top:16px;display:flex;gap:8px;justify-content:flex-end;'>
                        ${!i.completed ? `
                            <button onclick="return false;" class="edit-btn" style="padding:6px 10px;font-size:0.9em;border-radius:6px;background:#007bff;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;width:100px;white-space:nowrap;">
                                <i class="fas fa-edit" style="font-size:0.85em;"></i> <span>Sửa</span>
                            </button>
                            <button onclick="return false;" class="save-btn" style="padding:6px 10px;font-size:0.9em;border-radius:6px;background:#28a745;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;width:130px;white-space:nowrap;">
                                <i class="fas fa-check" style="font-size:0.85em;"></i> <span>Hoàn thành</span>
                            </button>
                        ` : `
                            <span style="padding:6px 12px;font-size:0.9em;background:#e8f5e9;color:#2e7d32;border-radius:6px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas fa-check-circle"></i> Đã hoàn thành
                            </span>
                        `}
                    </div>
                </div>
            `;
        }).join('')}
    </div>`;
}

function filterImports() {
    const importsGrid = document.getElementById('importsGrid');
    if (!importsGrid) return false;
    
    // Hiển thị 1 phiếu nhập hàng mẫu
    const sampleImport = {
        id: 'PN001',
        code: 'PN001',
        date: '2025-11-11',
        supplier: 'Toyota Việt Nam',
        completed: false,
        items: [
            {
                brand: 'Toyota',
                name: 'Camry',
                qty: 5,
                price: 1100000000
            }
        ],
        subtotal: 5500000000,
        tax: 550000000,
        total: 6050000000,
        note: 'Nhập lô hàng mới tháng 11/2025'
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
    let oldStock = JSON.parse(localStorage.getItem('oldStock')) || [];
    if (oldStock.length === 0) {
        const sampleOldStock = [
            {
                id: 'TK001',
                brand: 'Toyota',
                name: 'Fortuner 2023',
                year: 2023,
                category: 'suv',
                originalPrice: 1450000000,
                discount: 10,
                stockDate: '15/09/2023',
                daysInStock: 410,
                quantity: 3,
                lowStock: false,
                reason: 'Ít người mua do giá cao',
                image: 'assets/images/toyota-fortuner.jpg'
            },
            {
                id: 'TK002',
                brand: 'Honda',
                name: 'Accord 2023',
                year: 2023,
                category: 'sedan',
                originalPrice: 1319000000,
                discount: 15,
                stockDate: '20/08/2023',
                daysInStock: 436,
                quantity: 2,
                lowStock: false,
                reason: 'Màu sắc ít phổ biến',
                image: 'assets/images/honda-accord.jpg'
            },
            {
                id: 'TK003',
                brand: 'Mazda',
                name: 'CX-8 2023',
                year: 2023,
                category: 'suv',
                originalPrice: 1179000000,
                discount: 20,
                stockDate: '10/07/2023',
                daysInStock: 477,
                quantity: 5,
                lowStock: false,
                reason: 'Model cũ, sắp có phiên bản mới',
                image: 'assets/images/mazda-cx8.jpg'
            },
            {
                id: 'TK004',
                brand: 'Hyundai',
                name: 'Santa Fe 2024',
                year: 2024,
                category: 'suv',
                originalPrice: 1340000000,
                discount: 5,
                stockDate: '15/10/2024',
                daysInStock: 15,
                quantity: 1,
                lowStock: true,
                reason: 'Bán chạy, sắp hết hàng',
                image: 'assets/images/hyundai-santafe.jpg'
            },
            {
                id: 'TK005',
                brand: 'KIA',
                name: 'Sorento 2024',
                year: 2024,
                category: 'suv',
                originalPrice: 1149000000,
                discount: 3,
                stockDate: '20/10/2024',
                daysInStock: 10,
                quantity: 1,
                lowStock: true,
                reason: 'Nhu cầu cao, cần nhập thêm',
                image: 'assets/images/kia-sorento.jpg'
            },
            {
                id: 'TK006',
                brand: 'Ford',
                name: 'Everest 2024',
                year: 2024,
                category: 'suv',
                originalPrice: 1525000000,
                discount: 2,
                stockDate: '25/10/2024',
                daysInStock: 5,
                quantity: 1,
                lowStock: true,
                reason: 'Xe hot, gần hết hàng',
                image: 'assets/images/ford-everest.jpg'
            },
            {
                id: 'TK007',
                brand: 'Toyota',
                name: 'Vios 2024',
                year: 2024,
                category: 'sedan',
                originalPrice: 558000000,
                discount: 0,
                stockDate: '27/10/2024',
                daysInStock: 3,
                quantity: 1,
                lowStock: true,
                reason: 'Xe bán chạy nhất phân khúc',
                image: 'assets/images/toyota-vios.jpg'
            },
            {
                id: 'TK008',
                brand: 'Honda',
                name: 'City 2024',
                year: 2024,
                category: 'sedan',
                originalPrice: 599000000,
                discount: 0,
                stockDate: '01/08/2024',
                daysInStock: 90,
                quantity: 4,
                lowStock: false,
                normalStock: true,
                reason: 'Xe sedan hạng B phổ biến',
                image: 'assets/images/honda-city.jpg'
            },
            {
                id: 'TK009',
                brand: 'Mazda',
                name: 'CX-5 2024',
                year: 2024,
                category: 'suv',
                originalPrice: 859000000,
                discount: 0,
                stockDate: '15/07/2024',
                daysInStock: 107,
                quantity: 6,
                lowStock: false,
                normalStock: true,
                reason: 'SUV 5 chỗ được ưa chuộng',
                image: 'assets/images/mazda-cx5.jpg'
            },
            {
                id: 'TK010',
                brand: 'Hyundai',
                name: 'Tucson 2024',
                year: 2024,
                category: 'suv',
                originalPrice: 769000000,
                discount: 0,
                stockDate: '20/08/2024',
                daysInStock: 71,
                quantity: 5,
                lowStock: false,
                normalStock: true,
                reason: 'Thiết kế hiện đại, tiện nghi',
                image: 'assets/images/hyundai-tucson.jpg'
            },
            {
                id: 'TK011',
                brand: 'Ford',
                name: 'Ranger 2024',
                year: 2024,
                category: 'pickup',
                originalPrice: 799000000,
                discount: 0,
                stockDate: '05/09/2024',
                daysInStock: 55,
                quantity: 3,
                lowStock: false,
                normalStock: true,
                reason: 'Bán tải bán chạy nhất',
                image: 'assets/images/ford-ranger.jpg'
            },
            {
                id: 'TK012',
                brand: 'Mitsubishi',
                name: 'Xpander 2024',
                year: 2024,
                category: 'suv',
                originalPrice: 555000000,
                discount: 0,
                stockDate: '10/09/2024',
                daysInStock: 50,
                quantity: 7,
                lowStock: false,
                normalStock: true,
                reason: 'MPV 7 chỗ tiết kiệm',
                image: 'assets/images/mitsubishi-xpander.jpg'
            }
        ];
        localStorage.setItem('oldStock', JSON.stringify(sampleOldStock));
        oldStock = sampleOldStock;
    }
    return oldStock;
}

function loadOldStock() {
    const allStock = initOldStockData();
    
    // Tách xe theo 3 loại
    const lowStockItems = allStock.filter(item => item.lowStock === true);
    const normalStockItems = allStock.filter(item => item.normalStock === true);
    const oldStockItems = allStock.filter(item => !item.lowStock && !item.normalStock);
    
    // Render xe sắp hết hàng
    const lowStockContainer = document.getElementById('lowStockList');
    if (lowStockContainer) {
        if (lowStockItems.length === 0) {
            lowStockContainer.innerHTML = '<div style="color:#666;font-style:italic;">Không có sản phẩm nào sắp hết hàng.</div>';
        } else {
            lowStockContainer.innerHTML = renderStockItems(lowStockItems, 'warning');
        }
    }
    
    // Render xe thường
    const normalStockContainer = document.getElementById('normalStockList');
    if (normalStockContainer) {
        if (normalStockItems.length === 0) {
            normalStockContainer.innerHTML = '<div style="color:#666;font-style:italic;">Không có sản phẩm tồn kho thường.</div>';
        } else {
            normalStockContainer.innerHTML = renderStockItems(normalStockItems, 'normal');
        }
    }
    
    // Render xe tồn kho lâu
    const oldStockContainer = document.getElementById('oldStockList');
    if (oldStockContainer) {
        if (oldStockItems.length === 0) {
            oldStockContainer.innerHTML = '<div style="color:#666;font-style:italic;">Không có sản phẩm tồn kho lâu.</div>';
        } else {
            oldStockContainer.innerHTML = renderStockItems(oldStockItems, 'old');
        }
    }
}

function renderStockItems(items, type) {
    // type: 'warning' (sắp hết), 'normal' (thường), hoặc 'old' (tồn lâu)
    const borderColor = type === 'warning' ? '#ff9800' : type === 'normal' ? '#28a745' : '#dc3545';
    const badgeColor = type === 'warning' ? '#ff9800' : type === 'normal' ? '#28a745' : '#dc3545';
    
    return `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${items.map((item, index) => {
            // Tính giá sau giảm dựa trên discount
            const currentPrice = item.originalPrice * (1 - item.discount / 100);
            const savedAmount = item.originalPrice - currentPrice;
            
            // Xử lý tên loại xe
            const categoryName = item.category === 'suv' ? 'SUV' : 
                                item.category === 'sedan' ? 'Sedan' : 
                                item.category === 'hatchback' ? 'Hatchback' : 
                                item.category === 'pickup' ? 'Pickup' : 'Sedan';
            
            // Xử lý số lượng tồn (mặc định là 1 nếu chưa có)
            const quantity = item.quantity || 1;
            
            return `
            <div style='background:#fff;border:2px solid ${borderColor};border-radius:10px;padding:16px;box-shadow:0 3px 8px rgba(220,53,69,0.15);font-family:Arial,sans-serif;position:relative;'>
                ${item.discount > 0 ? `<div style='position:absolute;top:10px;right:10px;background:${badgeColor};color:#fff;padding:6px 12px;border-radius:20px;font-weight:600;font-size:0.9em;'>
                    Giảm ${item.discount}%
                </div>` : ''}
                <div style='color:#333;font-size:1.1em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:600;margin-top:8px;'>${item.brand} ${item.name}</div>
                <div style='color:#666;font-size:0.95em;margin-bottom:8px;font-family:Arial,sans-serif;font-weight:normal;'>Mã xe: ${item.id}</div>
                <div style='margin-bottom:12px;'>
                    <span style='display:inline-block;background:#e3f2fd;color:#1976d2;padding:5px 10px;border-radius:6px;font-weight:600;font-size:0.85em;margin-right:8px;'>
                        ${categoryName}
                    </span>
                    <span style='display:inline-block;background:${quantity <= 1 ? '#ffebee' : '#e8f5e9'};color:${quantity <= 1 ? '#c62828' : '#2e7d32'};padding:5px 10px;border-radius:6px;font-weight:600;font-size:0.85em;'>
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
                    ${item.discount > 0 ? `<div style='color:#666;font-size:0.95em;margin-bottom:6px;font-family:Arial,sans-serif;font-weight:normal;text-decoration:line-through;'>Giá gốc: ${formatPrice(item.originalPrice)} VNĐ</div>` : ''}
                    <div style='color:${type === 'warning' ? '#ff9800' : type === 'normal' ? '#28a745' : '#dc3545'};font-size:1.2em;font-family:Arial,sans-serif;font-weight:600;'>${item.discount > 0 ? 'Giá ưu đãi' : 'Giá bán'}: ${formatPrice(currentPrice)} VNĐ</div>
                    ${item.discount > 0 ? `<div style='color:#28a745;font-size:0.95em;margin-top:4px;font-family:Arial,sans-serif;font-weight:normal;'>Tiết kiệm: ${formatPrice(savedAmount)} VNĐ</div>` : ''}
                </div>
                
                <div style='margin-top:12px;padding-top:12px;border-top:1px solid #e0e0e0;'>
                    <div style='color:#666;font-size:0.9em;font-family:Arial,sans-serif;font-weight:normal;font-style:italic;'>
                        <i class="fas fa-info-circle" style="margin-right:6px;"></i>${item.reason}
                    </div>
                </div>
            </div>
        `;
        }).join('')}
    </div>`;
}

function loadOldStock_original() {
    const oldStock = initOldStockData();
    const container = document.getElementById('oldStockList');
    if (!container) return;
    
    const html = `<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px;font-family:Arial,sans-serif;'>
        ${oldStock.map((item, index) => {
            // Tính giá sau giảm dựa trên discount
            const currentPrice = item.originalPrice * (1 - item.discount / 100);
            const savedAmount = item.originalPrice - currentPrice;
            
            // Xử lý tên loại xe
            const categoryName = item.category === 'suv' ? 'SUV' : 
                                item.category === 'sedan' ? 'Sedan' : 
                                item.category === 'hatchback' ? 'Hatchback' : 
                                item.category === 'pickup' ? 'Pickup' : 'Sedan';
            
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
        }).join('')}
    </div>`;
    
    container.innerHTML = html;
}

// Function updateDiscount đã bị vô hiệu hóa (Prototype mode)

// Export function (không còn cần thiết)

// ========== Quản lý giá bán ==========
function initPricingData() {
    // Dữ liệu mẫu cho quản lý giá bán (lấy từ sản phẩm có sẵn)
    const products = JSON.parse(localStorage.getItem('products')) || [];
    
    // Nếu chưa có sản phẩm, tạo dữ liệu mẫu
    if (products.length === 0) {
        const sampleProducts = [
            {
                id: 1,
                name: 'Camry 2024',
                brand: 'Toyota',
                category: 'sedan',
                price: 1235000000,
                profitMargin: 8.5,
                sellingPrice: 1339975000,
                image: 'assets/images/toyota-camry.jpg'
            },
            {
                id: 2,
                name: 'CR-V 2024',
                brand: 'Honda',
                category: 'suv',
                price: 1029000000,
                profitMargin: 10,
                sellingPrice: 1131900000,
                image: 'assets/images/honda-crv.jpg'
            },
            {
                id: 3,
                name: 'Mazda3 2024',
                brand: 'Mazda',
                category: 'sedan',
                price: 669000000,
                profitMargin: 12,
                sellingPrice: 749280000,
                image: 'assets/images/mazda3.jpg'
            },
            {
                id: 4,
                name: 'VF 8 2024',
                brand: 'VinFast',
                category: 'suv',
                price: 999000000,
                profitMargin: 7,
                sellingPrice: 1068930000,
                image: 'assets/images/vinfast-vf8.jpg'
            }
        ];
        return sampleProducts;
    }
    
    // Thêm profitMargin và sellingPrice cho sản phẩm nếu chưa có
    return products.map(p => {
        if (!p.profitMargin) p.profitMargin = 10; // Mặc định 10%
        if (!p.sellingPrice) p.sellingPrice = p.price * (1 + p.profitMargin / 100);
        return p;
    });
}

function loadPricing() {
    const pricingGrid = document.getElementById('pricingGrid');
    if (!pricingGrid) return;
    
    const products = initPricingData();
    const categories = JSON.parse(localStorage.getItem('categories')) || [];
    
    // Cập nhật category filter
    const categoryFilter = document.getElementById('pricingCategoryFilter');
    if (categoryFilter) {
        categoryFilter.innerHTML = '<option value="">Tất cả loại xe</option>' + 
            categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
    }
    
    if (!products.length) {
        pricingGrid.innerHTML = '<div class="empty-state">Chưa có sản phẩm nào.</div>';
        return;
    }
    
    const html = `
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;">
                        <th style="padding:14px;text-align:left;font-weight:600;min-width:200px;">Tên sản phẩm</th>
                        <th style="padding:14px;text-align:left;font-weight:600;min-width:120px;">Hãng</th>
                        <th style="padding:14px;text-align:left;font-weight:600;min-width:120px;">Loại xe</th>
                        <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá nhập (VNĐ)</th>
                        <th style="padding:14px;text-align:center;font-weight:600;min-width:120px;">% Lợi nhuận</th>
                        <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá bán (VNĐ)</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map((product, index) => {
                        // Xử lý loại xe: nếu không tìm thấy trong categories thì gán mặc định
                        let categoryName = 'Sedan'; // Mặc định
                        if (product.category) {
                            const cat = categories.find(c => c.id === product.category);
                            if (cat) {
                                categoryName = cat.name;
                            } else {
                                // Nếu category là string trực tiếp (sedan, suv, ...)
                                categoryName = product.category.toUpperCase();
                            }
                        }
                        
                        const importPrice = product.price || 0;
                        const profitMargin = product.profitMargin || 0;
                        const sellingPrice = product.sellingPrice || (importPrice * (1 + profitMargin / 100));
                        
                        return `
                        <tr style="border-bottom:1px solid #f0f0f0;transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
                            <td style="padding:12px;">
                                <div style="font-weight:600;color:#333;margin-bottom:4px;">${product.name}</div>
                                <div style="font-size:0.85em;color:#666;">ID: ${product.id}</div>
                            </td>
                            <td style="padding:12px;">
                                <span style="display:inline-block;background:#e3f2fd;color:#1976d2;padding:6px 12px;border-radius:6px;font-weight:600;font-size:0.9em;">
                                    ${product.brand || 'N/A'}
                                </span>
                            </td>
                            <td style="padding:12px;color:#555;">${categoryName}</td>
                            <td style="padding:12px;text-align:right;font-weight:500;color:#333;">${formatPrice(importPrice)}</td>
                            <td style="padding:12px;text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                    <input type="number" value="${profitMargin.toFixed(1)}" min="0" max="100" step="0.1" 
                                        style="width:70px;padding:6px 8px;border:1px solid #ddd;border-radius:6px;text-align:center;font-weight:600;font-size:0.95em;"
                                        onchange="return false;">
                                    <span style="font-weight:600;color:#2e7d32;">%</span>
                                </div>
                            </td>
                            <td style="padding:12px;text-align:right;">
                                <div style="font-weight:600;color:#0d279d;font-size:1.05em;">${formatPrice(sellingPrice)}</div>
                                <div style="font-size:0.85em;color:#28a745;margin-top:4px;">
                                    +${formatPrice(sellingPrice - importPrice)}
                                </div>
                            </td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    pricingGrid.innerHTML = html;
}

function filterPricing() {
    const searchInput = document.getElementById('pricingSearchProduct')?.value.toLowerCase() || '';
    const categoryFilter = document.getElementById('pricingCategoryFilter')?.value || '';
    
    const pricingGrid = document.getElementById('pricingGrid');
    if (!pricingGrid) return;
    
    let products = initPricingData();
    const categories = JSON.parse(localStorage.getItem('categories')) || [];
    
    // Lọc theo tên sản phẩm
    if (searchInput) {
        products = products.filter(p => {
            const fullName = `${p.brand || ''} ${p.name}`.toLowerCase();
            return fullName.includes(searchInput);
        });
    }
    
    // Lọc theo loại xe
    if (categoryFilter) {
        products = products.filter(p => p.category === categoryFilter);
    }
    
    if (!products.length) {
        pricingGrid.innerHTML = '<div class="empty-state">Không tìm thấy sản phẩm phù hợp.</div>';
        return;
    }
    
    // Render lại bảng với dữ liệu đã lọc
    const html = `
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;">
                        <th style="padding:14px;text-align:left;font-weight:600;min-width:200px;">Tên sản phẩm</th>
                        <th style="padding:14px;text-align:left;font-weight:600;min-width:120px;">Hãng</th>
                        <th style="padding:14px;text-align:left;font-weight:600;min-width:120px;">Loại xe</th>
                        <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá nhập (VNĐ)</th>
                        <th style="padding:14px;text-align:center;font-weight:600;min-width:120px;">% Lợi nhuận</th>
                        <th style="padding:14px;text-align:right;font-weight:600;min-width:140px;">Giá bán (VNĐ)</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map((product, index) => {
                        // Xử lý loại xe: nếu không tìm thấy trong categories thì gán mặc định
                        let categoryName = 'Sedan'; // Mặc định
                        if (product.category) {
                            const cat = categories.find(c => c.id === product.category);
                            if (cat) {
                                categoryName = cat.name;
                            } else {
                                // Nếu category là string trực tiếp (sedan, suv, ...)
                                categoryName = product.category.toUpperCase();
                            }
                        }
                        
                        const importPrice = product.price || 0;
                        const profitMargin = product.profitMargin || 0;
                        const sellingPrice = product.sellingPrice || (importPrice * (1 + profitMargin / 100));
                        
                        return `
                        <tr style="border-bottom:1px solid #f0f0f0;transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'">
                            <td style="padding:12px;">
                                <div style="font-weight:600;color:#333;margin-bottom:4px;">${product.name}</div>
                                <div style="font-size:0.85em;color:#666;">ID: ${product.id}</div>
                            </td>
                            <td style="padding:12px;">
                                <span style="display:inline-block;background:#e3f2fd;color:#1976d2;padding:6px 12px;border-radius:6px;font-weight:600;font-size:0.9em;">
                                    ${product.brand || 'N/A'}
                                </span>
                            </td>
                            <td style="padding:12px;color:#555;">${categoryName}</td>
                            <td style="padding:12px;text-align:right;font-weight:500;color:#333;">${formatPrice(importPrice)}</td>
                            <td style="padding:12px;text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                    <input type="number" value="${profitMargin.toFixed(1)}" min="0" max="100" step="0.1" 
                                        style="width:70px;padding:6px 8px;border:1px solid #ddd;border-radius:6px;text-align:center;font-weight:600;font-size:0.95em;"
                                        onchange="return false;">
                                    <span style="font-weight:600;color:#2e7d32;">%</span>
                                </div>
                            </td>
                            <td style="padding:12px;text-align:right;">
                                <div style="font-weight:600;color:#0d279d;font-size:1.05em;">${formatPrice(sellingPrice)}</div>
                                <div style="font-size:0.85em;color:#28a745;margin-top:4px;">
                                    +${formatPrice(sellingPrice - importPrice)}
                                </div>
                            </td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    pricingGrid.innerHTML = html;
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
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Đóng modal khi click bên ngoài
    document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeResetPasswordModal();
        }
    });
    
    // Xử lý form submit (prototype mode - không làm gì cả)
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Prototype mode: không làm gì cả khi ấn lưu
        return false;
    });
}

function closeResetPasswordModal() {
    const modal = document.getElementById('resetPasswordModal');
    if (modal) {
        modal.remove();
    }
}

// Export functions
window.showResetPasswordModal = showResetPasswordModal;
window.closeResetPasswordModal = closeResetPasswordModal;

// ========== Quản lý modal thêm phiếu nhập ==========
function showAddImportModal() {
    const modal = document.getElementById('addImportModal');
    if (!modal) return;
    
    // Reset form
    document.getElementById('addImportForm').reset();
    
    // Tự động set ngày hiện tại
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('importDate').value = today;
    
    // Load danh sách sản phẩm vào dropdown
    loadProductsToImportModal();
    
    // Hiển thị modal
    modal.style.display = 'block';
}

function closeAddImportModal() {
    const modal = document.getElementById('addImportModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function loadProductsToImportModal() {
    const products = JSON.parse(localStorage.getItem('products')) || [];
    const selects = document.querySelectorAll('.import-product-select');
    
    const optionsHTML = '<option value="">-- Chọn sản phẩm --</option>' + 
        products.map(p => `<option value="${p.id}">${p.brand} ${p.name} (${p.year || ''})</option>`).join('');
    
    selects.forEach(select => {
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
    const productsGrid = document.getElementById('products-grid');
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