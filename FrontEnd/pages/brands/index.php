<?php
// Fetch brand products from database - DYNAMIC BRANDS PAGE
header('Content-Type: text/html; charset=UTF-8');
require_once '../../../BackEnd/config/db_connect.php';

// Get brand from URL parameter
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : 'Toyota';
$brand = htmlspecialchars($brand, ENT_QUOTES, 'UTF-8');

// Validate brand to prevent SQL injection
$validBrands = ['Toyota', 'Mercedes', 'BMW', 'Audi', 'Lexus', 'Honda', 'Hyundai', 'KIA', 'Vinfast'];
if (!in_array($brand, $validBrands)) {
    $brand = 'Toyota';
}

$products = [];
$error = null;

try {
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT id, name, price, description, image_url, stock FROM products WHERE brand = ? ORDER BY name LIMIT 20");
        $stmt->execute([$brand]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $brand; ?> - 3 Boys Auto</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="../../index.php">3 Boys Auto</a>
            </div>
            <div class="nav-links">
                <a href="../../index.php">Trang chủ</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-trigger">Loại xe <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-content">
                        <a href="index.php?brand=Toyota" <?php echo $brand === 'Toyota' ? 'class="active"' : ''; ?>>Toyota</a>
                        <a href="index.php?brand=Mercedes" <?php echo $brand === 'Mercedes' ? 'class="active"' : ''; ?>>Mercedes</a>
                        <a href="index.php?brand=BMW" <?php echo $brand === 'BMW' ? 'class="active"' : ''; ?>>BMW</a>
                        <a href="index.php?brand=Audi" <?php echo $brand === 'Audi' ? 'class="active"' : ''; ?>>Audi</a>
                        <a href="index.php?brand=Lexus" <?php echo $brand === 'Lexus' ? 'class="active"' : ''; ?>>Lexus</a>
                        <a href="index.php?brand=Honda" <?php echo $brand === 'Honda' ? 'class="active"' : ''; ?>>Honda</a>
                        <a href="index.php?brand=Hyundai" <?php echo $brand === 'Hyundai' ? 'class="active"' : ''; ?>>Hyundai</a>
                        <a href="index.php?brand=KIA" <?php echo $brand === 'KIA' ? 'class="active"' : ''; ?>>KIA</a>
                        <a href="index.php?brand=Vinfast" <?php echo $brand === 'Vinfast' ? 'class="active"' : ''; ?>>Vinfast</a>
                    </div>
                </div>
                <a href="../../index.php#about">Giới thiệu</a>
                <a href="../../index.php#contact">Liên hệ</a>
            </div>
            <div class="user-actions">
                <a href="#" onclick="checkLoginAndGoToCart()" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-text">Xem giỏ hàng</span>
                    <span class="cart-count">0</span>
                </a>
                <div class="login-options" id="loginOptions">
                    <a href="../user/login.php" class="blob-btn login-btn" id="loginBtn">
                        <span class="blob-btn__inner">
                            <span class="blob-btn__blobs">
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                            </span>
                        </span>
                        Đăng nhập
                    </a>
                </div>
                <div class="user-info" id="userInfo" style="display: none;">
                    <img src="" alt="Avatar" class="user-avatar" id="userAvatar" style="cursor:pointer" onclick="window.location.href='../user/profile.php'">
                    <span class="user-name" id="userName" style="cursor:pointer" onclick="window.location.href='../user/profile.php'"></span>
                    <a href="#" class="logout-link" onclick="logout()">Đăng xuất</a>
                </div>
            </div>
        </nav>
    </header>

    <main style="padding-top: 2rem;">
        <!-- Hero Section -->
        <section class="hero" style="margin-bottom: 3rem; padding: 4rem 5%;">
            <div style="text-align: center;">
                <h1 style="font-size: 3rem; color: #fff; text-shadow: 2px 2px 8px rgba(0,0,0,0.7); margin-bottom: 1rem;"><?php echo $brand; ?></h1>
                <p style="font-size: 1.3rem; color: #fff; text-shadow: 1px 1px 4px rgba(0,0,0,0.6);">Những dòng xe <?php echo $brand; ?> đẳng cấp và bền bỉ</p>
            </div>
        </section>

        <!-- Products Section -->
        <section id="products" class="brands-section" style="padding: 0 5%;">
            <div class="brand-container" style="margin-bottom: 0;">
                <div class="car-slider" style="margin: 0;">
                    <div class="car-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                        <?php
                        if (!empty($products)) {
                            foreach ($products as $product) {
                                $imageSrc = '../../assets/images/' . $product['image_url'];
                                $priceFormatted = number_format($product['price']);
                                $productName = htmlspecialchars($product['name']);
                                $productDesc = json_encode($product['description'], JSON_UNESCAPED_UNICODE);
                                $productId = intval($product['id']);
                                ?>
                                <div class="car-card" data-id="<?php echo $productId; ?>" data-desc='<?php echo $productDesc; ?>' data-origin="" data-year="" data-fuel="" data-seats="" data-transmission="" data-engine="">
                                    <img src="<?php echo $imageSrc; ?>" alt="<?php echo $productName; ?>">
                                    <h3><?php echo $productName; ?></h3>
                                    <p class="price"><?php echo $priceFormatted; ?> VNĐ</p>
                                    <div class="button-container">
                                        <button class="buy-btn" onclick="addToCartFromSearch(<?php echo $productId; ?>, '<?php echo addslashes($productName); ?>', <?php echo $product['price']; ?>)" style="cursor: pointer; opacity: 1;">Mua hàng</button>
                                        <a href="#" class="view-details">Chi tiết</a>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div style="grid-column: 1/-1; text-align: center; padding: 2rem; color: #fff;"><p>Không tìm thấy sản phẩm</p></div>';
                        }
                        ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 3rem;">
                        <button class="page-btn" onclick="changePage('prev')" style="
                            padding: 10px 20px;
                            background: rgba(255, 255, 255, 0.2);
                            backdrop-filter: blur(10px);
                            border: 1px solid rgba(255, 255, 255, 0.3);
                            border-radius: 8px;
                            color: #fff;
                            cursor: pointer;
                            font-weight: 600;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'"
                           onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">
                            <i class="fas fa-chevron-left"></i> Trước
                        </button>
                        
                        <span class="page-info" style="
                            color: #fff;
                            font-weight: 600;
                            font-size: 1.1rem;
                            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
                        ">
                            Trang <span id="currentPage">1</span> / <span id="totalPages">2</span>
                        </span>
                        
                        <button class="page-btn" onclick="changePage('next')" style="
                            padding: 10px 20px;
                            background: rgba(255, 255, 255, 0.2);
                            backdrop-filter: blur(10px);
                            border: 1px solid rgba(255, 255, 255, 0.3);
                            border-radius: 8px;
                            color: #fff;
                            cursor: pointer;
                            font-weight: 600;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'"
                           onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">
                            Sau <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="contact" style="margin-top: 4rem; padding: 3rem 5%; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); border-top: 1px solid rgba(255, 255, 255, 0.2);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; color: #fff;">
            <div>
                <h3 style="margin-bottom: 1rem; color: #fff; text-shadow: 1px 1px 4px rgba(0,0,0,0.6);">3 Boys Auto</h3>
                <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Đại lý xe hơi uy tín, cung cấp các dòng xe chất lượng cao từ nhiều thương hiệu nổi tiếng.</p>
            </div>
            <div>
                <h3 style="margin-bottom: 1rem; color: #fff; text-shadow: 1px 1px 4px rgba(0,0,0,0.6);">Liên hệ</h3>
                <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><i class="fas fa-phone"></i> 1900-xxxx</p>
                <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><i class="fas fa-envelope"></i> contact@3boysauto.vn</p>
                <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, TP.HCM</p>
            </div>
            <div>
                <h3 style="margin-bottom: 1rem; color: #fff; text-shadow: 1px 1px 4px rgba(0,0,0,0.6);">Theo dõi chúng tôi</h3>
                <div style="display: flex; gap: 1rem; font-size: 1.5rem;">
                    <a href="#" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.2); color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
            <p>&copy; 2024 3 Boys Auto. All rights reserved.</p>
        </div>
    </footer>

    <!-- Modal chi tiết xe -->
    <div id="carModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <img id="modalImg" src="" alt="Xe ô tô">
            <h2 id="modalTitle"></h2>
            <p id="modalPrice" style="font-size: 18px; font-weight: bold; color: #ffc107; margin: 10px 0;"></p>
            
            <!-- Chi tiết thông tin sản phẩm -->
            <div id="modalDetails" style="margin: 15px 0; font-size: 14px; line-height: 1.8;">
              <div id="modalOrigin" style="display: none;"><i class="fas fa-location-dot" style="color: #e74c3c; margin-right: 8px;"></i><span>Xuất xứ: </span><span id="modalOriginValue"></span></div>
              <div id="modalYear" style="display: none;"><i class="fas fa-calendar" style="color: #3498db; margin-right: 8px;"></i><span>Năm sản xuất: </span><span id="modalYearValue"></span></div>
              <div id="modalFuel" style="display: none;"><i class="fas fa-gas-pump" style="color: #e67e22; margin-right: 8px;"></i><span>Nhiên liệu: </span><span id="modalFuelValue"></span></div>
              <div id="modalSeats" style="display: none;"><i class="fas fa-car" style="color: #27ae60; margin-right: 8px;"></i><span>Số ghế: </span><span id="modalSeatsValue"></span></div>
              <div id="modalTransmission" style="display: none;"><i class="fas fa-cog" style="color: #9b59b6; margin-right: 8px;"></i><span>Hộp số: </span><span id="modalTransmissionValue"></span></div>
              <div id="modalEngine" style="display: none;"><i class="fas fa-wrench" style="color: #34495e; margin-right: 8px;"></i><span>Động cơ: </span><span id="modalEngineValue"></span></div>
            </div>
            
            <p id="modalDesc" style="margin: 15px 0; line-height: 1.6; color: #ccc;"></p>
            <button id="addToCartBtn" class="btn" style="cursor: pointer; opacity: 1; width: 100%; padding: 12px; font-size: 16px; margin-top: 10px;">Thêm vào giỏ</button>
        </div>
    </div>

    <script src="/WebBasic/FrontEnd/assets/js/config.js"></script>
    <script src="../../assets/js/main.js?v=20260408-3"></script>
    <script src="../../assets/js/brand-page.js?v=20260408-3"></script>
    
    <script>
    // Add to cart function - calls API instead of localStorage
    function addToCartFromSearch(productId, productName, productPrice) {
      const isLoggedIn = localStorage.getItem("userLoggedIn") === "true";
      if (!isLoggedIn) {
        alert("Vui lòng đăng nhập để mua hàng!");
        window.location.href = "../user/login.php";
        return false;
      }

      // Ensure productId is always a number
      productId = Number(productId);

      // Call API to add to cart
      fetch(BASE_URL + '/BackEnd/api/add_to_cart.php', {
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
        console.log('API Response:', data);
        if (data.success) {
          updateCartCount();
          showToast("Đã thêm vào giỏ hàng!");
        } else {
          alert("Lỗi: " + (data.message || "Không thể thêm vào giỏ hàng"));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert("Lỗi kết nối: " + error);
      });

      return false;
    }

    function updateCartCount() {
      // Fetch from API to get actual count from database
      fetch(BASE_URL + '/BackEnd/api/cart.php?action=get', {
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

    function showToast(message) {
      const t = document.createElement("div");
      t.style.cssText = "position:fixed;top:28px;left:50%;transform:translateX(-50%);background:#28a745;color:#fff;padding:14px 32px;border-radius:10px;font-size:15px;font-weight:500;z-index:99999;box-shadow:0 4px 20px rgba(0,0,0,0.25);opacity:0;transition:opacity 0.3s;pointer-events:none;white-space:nowrap;";
      t.textContent = message;
      document.body.appendChild(t);
      setTimeout(function () {
        t.style.opacity = "1";
      }, 10);
      setTimeout(function () {
        t.style.opacity = "0";
        setTimeout(function () {
          if (t.parentNode) t.parentNode.removeChild(t);
        }, 300);
      }, 2000);
    }

    // Modal handlers for product details
    let currentProductData = {};
    const modal = document.getElementById('carModal');
    const closeBtn = document.querySelector('.close-btn');
    const addToCartBtn = document.getElementById('addToCartBtn');

    // Helper function to display or hide detail field
    function setDetailField(elementId, valueId, value) {
      const elem = document.getElementById(elementId);
      const valElem = document.getElementById(valueId);
      if (elem && valElem) {
        if (value && value !== 'Không rõ' && value !== '' && value !== 'undefined') {
          valElem.innerText = value;
          elem.style.display = 'block';
        } else {
          elem.style.display = 'none';
        }
      }
    }

    // Close modal
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Setup view-details links
    document.querySelectorAll('.view-details').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const card = this.closest('.car-card');
            if (card) {
                const productId = card.dataset.id;
                const productName = card.querySelector('h3').innerText;
                const productPrice = card.querySelector('.price').innerText;
                let productDesc = card.dataset.desc || 'Không có thông tin';
                const productImg = card.querySelector('img').src;
                
                // Parse JSON if desc is JSON encoded
                try {
                    if (productDesc.trim().startsWith('"')) {
                        productDesc = JSON.parse(productDesc);
                    }
                } catch (e) {
                    // If not JSON, use as-is
                }
                
                // Get detail attributes from card
                const origin = card.dataset.origin || '';
                const year = card.dataset.year || '';
                const fuel = card.dataset.fuel || '';
                const seats = card.dataset.seats || '';
                const transmission = card.dataset.transmission || '';
                const engine = card.dataset.engine || '';

                currentProductData = {
                    id: productId,
                    name: productName,
                    price: productPrice,
                    desc: productDesc,
                    img: productImg,
                    origin, year, fuel, seats, transmission, engine
                };

                document.getElementById('modalTitle').innerText = productName;
                document.getElementById('modalPrice').innerText = productPrice;
                document.getElementById('modalDesc').innerText = productDesc;
                document.getElementById('modalImg').src = productImg;
                
                // Update detail fields
                setDetailField('modalOrigin', 'modalOriginValue', origin);
                setDetailField('modalYear', 'modalYearValue', year);
                setDetailField('modalFuel', 'modalFuelValue', fuel);
                setDetailField('modalSeats', 'modalSeatsValue', seats);
                setDetailField('modalTransmission', 'modalTransmissionValue', transmission);
                setDetailField('modalEngine', 'modalEngineValue', engine);
                
                modal.style.display = 'block';
            }
        });
    });

    // Add to cart from modal
    addToCartBtn.onclick = function() {
        if (currentProductData.id) {
            const priceNum = parseInt(currentProductData.price.replace(/[^0-9]/g, ''));
            addToCartFromSearch(parseInt(currentProductData.id), currentProductData.name, priceNum);
            modal.style.display = 'none';
        }
    };
    </script>
</body>
</html>
