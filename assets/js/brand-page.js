// Kiểm tra trạng thái đăng nhập và hiển thị thông tin user/admin
function checkUserLoginStatus() {
    const isUserLoggedIn = localStorage.getItem('userLoggedIn') === 'true';
    const isAdminLoggedIn = localStorage.getItem('adminLoggedIn') === 'true';
    
    const userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}');
    const adminInfo = JSON.parse(localStorage.getItem('adminInfo') || '{}');
    const adminUsername = localStorage.getItem('adminUsername') || 'Admin';
    
    const loginBtn = document.getElementById('loginBtn');
    const userInfoDiv = document.getElementById('userInfo');
    
    // Nếu admin đã đăng nhập
    if (isAdminLoggedIn) {
        // Ẩn nút đăng nhập, hiển thị thông tin admin
        loginBtn.style.display = 'none';
        userInfoDiv.style.display = 'flex';
        
        // Cập nhật thông tin admin
        const displayName = adminInfo.name || adminUsername;
        document.getElementById('userName').textContent = displayName;
        document.getElementById('userAvatar').src = adminInfo.picture || `https://ui-avatars.com/api/?name=${displayName}&background=dc3545&color=fff&size=35`;
        
    } else if (isUserLoggedIn && userInfo.name) {
        // Ẩn nút đăng nhập, hiển thị thông tin user
        loginBtn.style.display = 'none';
        userInfoDiv.style.display = 'flex';
        
        // Cập nhật thông tin user
        document.getElementById('userName').textContent = userInfo.name;
        document.getElementById('userAvatar').src = userInfo.picture || `https://ui-avatars.com/api/?name=${userInfo.name}&background=007bff&color=fff&size=35`;
        
    } else {
        // Hiển thị nút đăng nhập, ẩn thông tin user
        loginBtn.style.display = 'inline-block';
        userInfoDiv.style.display = 'none';
    }
}

// Function đăng xuất
function logout() {
    const isAdmin = localStorage.getItem('adminLoggedIn') === 'true';
    
    if (isAdmin) {
        // Đăng xuất admin
        localStorage.removeItem('adminLoggedIn');
        localStorage.removeItem('adminUsername');
        localStorage.removeItem('adminInfo');
        alert('Admin đã đăng xuất thành công!');
    } else {
        // Đăng xuất user thường
        localStorage.removeItem('userLoggedIn');
        localStorage.removeItem('userEmail');
        localStorage.removeItem('userInfo');
        // Xóa giỏ hàng khi đăng xuất
        localStorage.removeItem('cart');
        // Cập nhật badge giỏ hàng về 0
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) cartBadge.textContent = '0';
        alert('Đã đăng xuất thành công!');
    }
    
    checkUserLoginStatus();
}

function checkLoginAndGoToCart() {
    const isUserLoggedIn = localStorage.getItem('userLoggedIn') === 'true';
    const isAdminLoggedIn = localStorage.getItem('adminLoggedIn') === 'true';
    
    if (!isUserLoggedIn && !isAdminLoggedIn) {
        alert('Vui lòng đăng nhập để xem giỏ hàng!');
        window.location.href = 'login.html';
        return false;
    }
    window.location.href = 'cart.html';
    return false;
}

// Gọi function kiểm tra khi trang load
document.addEventListener('DOMContentLoaded', function() {
    checkUserLoginStatus();
    initPagination();
});

// ===== PAGINATION =====
let currentPage = 1;
const itemsPerPage = 5;
let totalItems = 0;
let totalPages = 0;

function initPagination() {
    const carCards = document.querySelectorAll('.car-card');
    totalItems = carCards.length;
    totalPages = Math.ceil(totalItems / itemsPerPage);
    
    // Cập nhật UI
    const totalPagesElement = document.getElementById('totalPages');
    if (totalPagesElement) {
        totalPagesElement.textContent = totalPages;
    }
    
    // Hiển thị trang đầu tiên
    showPage(1);
}

function showPage(page) {
    currentPage = page;
    const carCards = document.querySelectorAll('.car-card');
    
    // Ẩn tất cả các card
    carCards.forEach(card => {
        card.style.display = 'none';
    });
    
    // Hiển thị card của trang hiện tại
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    
    for (let i = startIndex; i < endIndex && i < totalItems; i++) {
        carCards[i].style.display = 'block';
    }
    
    // Cập nhật số trang hiện tại
    const currentPageElement = document.getElementById('currentPage');
    if (currentPageElement) {
        currentPageElement.textContent = currentPage;
    }
    
    // Cuộn lên đầu danh sách xe
    const carsSection = document.querySelector('.cars-grid');
    if (carsSection) {
        carsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function changePage(direction) {
    if (direction === 'next' && currentPage < totalPages) {
        showPage(currentPage + 1);
    } else if (direction === 'prev' && currentPage > 1) {
        showPage(currentPage - 1);
    }
}

// Expose functions to global scope
window.changePage = changePage;
