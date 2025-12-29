// search-results.js - Xử lý hiển thị kết quả tìm kiếm với phân trang

// Dữ liệu xe Toyota (lấy từ trang toyota.html - prototype mode)
const sampleCars = [
    {
        name: "Toyota Camry",
        brand: "Toyota",
        price: 1235000000,
        image: "assets/images/toyota-camry.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Xăng",
        seats: "5",
        transmission: "Tự động (AT)",
        engine: "2.5L",
        desc: "Sedan hạng D êm ái, tiện nghi, tiết kiệm."
    },
    {
        name: "Toyota Vios",
        brand: "Toyota",
        price: 592000000,
        image: "assets/images/toyota-vios.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Xăng",
        seats: "5",
        transmission: "Tự động (CVT)",
        engine: "1.5L",
        desc: "Sedan đô thị bền bỉ, tiết kiệm nhiên liệu."
    },
    {
        name: "Toyota Fortuner",
        brand: "Toyota",
        price: 1350000000,
        image: "assets/images/toyota-fortuner.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Dầu",
        seats: "7",
        transmission: "Tự động (AT)",
        engine: "2.8L Diesel",
        desc: "SUV 7 chỗ gầm cao, mạnh mẽ và đa dụng."
    },
    {
        name: "Toyota Cross",
        brand: "Toyota",
        price: 820000000,
        image: "assets/images/toyota-cross.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Xăng",
        seats: "5",
        transmission: "Tự động (CVT)",
        engine: "1.8L",
        desc: "Crossover đô thị, vận hành mượt và tiết kiệm."
    },
    {
        name: "Toyota Innova",
        brand: "Toyota",
        price: 755000000,
        image: "assets/images/toyota-innova.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Xăng",
        seats: "7",
        transmission: "Tự động (AT)",
        engine: "2.0L",
        desc: "MPV 7 chỗ rộng rãi, phù hợp gia đình."
    },
    {
        name: "Toyota Yaris",
        brand: "Toyota",
        price: 684000000,
        image: "assets/images/toyota-yaris.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Xăng",
        seats: "5",
        transmission: "Tự động (CVT)",
        engine: "1.5L",
        desc: "Hatchback linh hoạt, dễ lái, tiết kiệm."
    },
    {
        name: "Toyota Corolla",
        brand: "Toyota",
        price: 800000000,
        image: "assets/images/toyota-corolla.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Xăng",
        seats: "5",
        transmission: "Tự động (CVT)",
        engine: "1.8L",
        desc: "Sedan hạng C cân bằng giữa hiệu suất và tiết kiệm."
    },
    {
        name: "Toyota Raize",
        brand: "Toyota",
        price: 510000000,
        image: "assets/images/toyota-raize.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Xăng",
        seats: "5",
        transmission: "Tự động (CVT)",
        engine: "1.0L Turbo",
        desc: "SUV cỡ nhỏ cơ động, tiết kiệm nhiên liệu."
    },
    {
        name: "Toyota Alphard",
        brand: "Toyota",
        price: 4370000000,
        image: "assets/images/toyota-alphard.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Hybrid",
        seats: "7",
        transmission: "e-CVT",
        engine: "2.5L Hybrid",
        desc: "MPV hạng sang, tiện nghi cao cấp, vận hành êm ái."
    },
    {
        name: "Toyota Hilux",
        brand: "Toyota",
        price: 706000000,
        image: "assets/images/toyota-hilux.jpg",
        origin: "Nhật Bản",
        year: 2025,
        fuel: "Dầu",
        seats: "5",
        transmission: "Tự động (AT)",
        engine: "2.4L Diesel",
        desc: "Bán tải mạnh mẽ, bền bỉ, chở hàng tốt."
    }
];

// Biến phân trang
let currentPage = 1;
const itemsPerPage = 6; // Hiển thị 6 xe mỗi trang
let filteredCars = [];
let searchParams = {};

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    // Lấy thông tin tìm kiếm từ URL
    const urlParams = new URLSearchParams(window.location.search);
    searchParams = {
        name: urlParams.get('name') || '',
        brand: urlParams.get('brand') || '',
        priceFrom: urlParams.get('priceFrom') || '',
        priceTo: urlParams.get('priceTo') || ''
    };
    
    // Luôn hiển thị tất cả xe (không lọc - prototype mode)
    filteredCars = sampleCars;
    
    // Cập nhật tiêu đề
    updateSearchSummary();
    
    // Hiển thị trang đầu tiên
    displayPage(1);
    
    // Gắn sự kiện cho nút phân trang
    document.getElementById('prevPageBtn').addEventListener('click', function() {
        if (currentPage > 1) {
            displayPage(currentPage - 1);
        }
    });
    
    document.getElementById('nextPageBtn').addEventListener('click', function() {
        const totalPages = Math.ceil(filteredCars.length / itemsPerPage);
        if (currentPage < totalPages) {
            displayPage(currentPage + 1);
        }
    });
    
    // Gắn sự kiện cho nút Lọc
    document.getElementById('applyFilterBtn').addEventListener('click', function() {
        applyFilter();
    });
    
    // Gắn sự kiện cho nút Quay lại
    document.getElementById('resetFilterBtn').addEventListener('click', function() {
        resetFilter();
    });
    
    // Cho phép Enter để lọc
    document.getElementById('filterPriceFrom').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilter();
        }
    });
    document.getElementById('filterPriceTo').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilter();
        }
    });
});

// Hàm tìm kiếm (đã bị vô hiệu hóa - prototype mode: luôn hiển thị tất cả xe)
function performSearch() {
    // Prototype mode: không lọc, luôn hiển thị tất cả xe mẫu
    filteredCars = sampleCars;
    
    // Cập nhật tiêu đề tìm kiếm
    updateSearchSummary();
}

// Cập nhật thông tin tóm tắt tìm kiếm (vô hiệu hóa - không hiển thị gì)
function updateSearchSummary() {
    // Không hiển thị thông tin tìm kiếm
}

// Hàm áp dụng bộ lọc - hiển thị 4 xe Toyota bất kỳ
function applyFilter() {
    // Lấy 4 xe Toyota đầu tiên từ danh sách
    filteredCars = sampleCars.filter(car => car.name.includes('Toyota')).slice(0, 4);
    
    // Nếu không đủ 4 xe Toyota, lấy thêm từ danh sách chung
    if (filteredCars.length < 4) {
        const remaining = sampleCars.filter(car => !car.name.includes('Toyota')).slice(0, 4 - filteredCars.length);
        filteredCars = [...filteredCars, ...remaining];
    }
    
    // Hiển thị nút Quay lại
    document.getElementById('resetFilterBtn').style.display = 'block';
    
    // Hiển thị từ trang đầu tiên
    displayPage(1);
}

// Hàm quay lại kết quả ban đầu
function resetFilter() {
    // Reset về tất cả xe
    filteredCars = sampleCars;
    
    // Xóa giá trị trong các ô lọc
    document.getElementById('filterBrand').value = '';
    document.getElementById('filterPriceFrom').value = '';
    document.getElementById('filterPriceTo').value = '';
    
    // Ẩn nút Quay lại
    document.getElementById('resetFilterBtn').style.display = 'none';
    
    // Hiển thị từ trang đầu tiên
    displayPage(1);
}

// Hiển thị trang
function displayPage(pageNumber) {
    currentPage = pageNumber;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const carsToDisplay = filteredCars.slice(startIndex, endIndex);
    
    // Hiển thị xe
    const container = document.getElementById('searchResultsContainer');
    
    if (carsToDisplay.length === 0) {
        container.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: #fff;">
                <i class="fas fa-car" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Không tìm thấy xe phù hợp</h3>
                <p style="opacity: 0.8;">Vui lòng thử lại với tiêu chí tìm kiếm khác</p>
            </div>
        `;
        document.getElementById('paginationControls').style.display = 'none';
        return;
    }
    
    container.innerHTML = carsToDisplay.map(car => `
        <div class="car-card" data-origin="${car.origin}" data-year="${car.year}" data-fuel="${car.fuel}" 
             data-seats="${car.seats}" data-transmission="${car.transmission}" data-engine="${car.engine}" 
             data-desc="${car.desc}">
            <img src="${car.image}" alt="${car.name}" onerror="this.src='assets/images/default-car.jpg'">
            <h3>${car.name}</h3>
            <p class="price">${formatPrice(car.price)} VNĐ</p>
            <div class="button-container">
                <button class="buy-btn" onclick="return false;" style="cursor: pointer; opacity: 1;">Mua hàng</button>
                <a href="#" class="view-details">Chi tiết</a>
            </div>
        </div>
    `).join('');
    
    // Cập nhật phân trang
    updatePagination();
    
    // Gắn lại sự kiện cho nút chi tiết
    attachDetailEventListeners();
    
    // Cuộn lên đầu kết quả
    document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
}

// Cập nhật giao diện phân trang
function updatePagination() {
    const totalPages = Math.ceil(filteredCars.length / itemsPerPage);
    
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent = totalPages;
    
    // Vô hiệu hóa/bật nút
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    
    if (currentPage === 1) {
        prevBtn.style.opacity = '0.5';
        prevBtn.style.cursor = 'not-allowed';
    } else {
        prevBtn.style.opacity = '1';
        prevBtn.style.cursor = 'pointer';
    }
    
    if (currentPage === totalPages) {
        nextBtn.style.opacity = '0.5';
        nextBtn.style.cursor = 'not-allowed';
    } else {
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
    }
    
    // Hiển thị pagination controls
    document.getElementById('paginationControls').style.display = 'flex';
}

// Format giá tiền
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Gắn sự kiện cho nút "Chi tiết"
function attachDetailEventListeners() {
    const viewDetailsBtns = document.querySelectorAll('.view-details');
    viewDetailsBtns.forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const card = this.closest('.car-card');
            showCarDetails(card);
        });
    });
}

// Hiển thị modal chi tiết xe
function showCarDetails(card) {
    const modal = document.getElementById('carModal');
    const modalImg = document.getElementById('modalImg');
    const modalTitle = document.getElementById('modalTitle');
    const modalPrice = document.getElementById('modalPrice');
    const modalDesc = document.getElementById('modalDesc');
    
    const img = card.querySelector('img');
    const title = card.querySelector('h3');
    const price = card.querySelector('.price');
    
    modalImg.src = img.src;
    modalTitle.textContent = title.textContent;
    modalPrice.textContent = 'Giá: ' + price.textContent;
    
    // Tạo mô tả chi tiết
    const origin = card.getAttribute('data-origin');
    const year = card.getAttribute('data-year');
    const fuel = card.getAttribute('data-fuel');
    const seats = card.getAttribute('data-seats');
    const transmission = card.getAttribute('data-transmission');
    const engine = card.getAttribute('data-engine');
    const desc = card.getAttribute('data-desc');
    
    modalDesc.innerHTML = `
        <p><strong>Xuất xứ:</strong> ${origin}</p>
        <p><strong>Năm sản xuất:</strong> ${year}</p>
        <p><strong>Nhiên liệu:</strong> ${fuel}</p>
        <p><strong>Số chỗ ngồi:</strong> ${seats}</p>
        <p><strong>Hộp số:</strong> ${transmission}</p>
        <p><strong>Động cơ:</strong> ${engine}</p>
        <p><strong>Mô tả:</strong> ${desc}</p>
    `;
    
    modal.style.display = 'block';
    
    // Đóng modal
    const closeBtn = modal.querySelector('.close-btn');
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
}
