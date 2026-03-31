<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập - 3 Boys Auto</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Lobster&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../../assets/css/style.css" />
  </head>
  <body>
    <div class="login-container">
      <div class="login-box">
        <h1>Đăng nhập</h1>
        <div class="logo">
          <i class="fas fa-user-circle"></i>
        </div>
        <form
          action="#"
          method="POST"
          class="login-form"
          id="loginForm"
          autocomplete="off"
          data-lpignore="true"
        >
          <input type="text" style="display: none" autocomplete="new-text" />
          <input
            type="password"
            style="display: none"
            autocomplete="new-password"
          />
          <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email</label>
            <input
              type="email"
              id="email"
              name="email"
              autocomplete="new-email"
              value=""
              data-lpignore="true"
              required
            />
          </div>
          <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
            <input
              type="password"
              id="password"
              name="password"
              autocomplete="new-password"
              value=""
              data-lpignore="true"
              required
            />
          </div>
          <div class="form-options">
            <label class="remember-me">
              <input type="checkbox" name="remember" /> Ghi nhớ đăng nhập
            </label>
            <a href="#" class="forgot-password">Quên mật khẩu?</a>
          </div>
          <button type="submit" class="login-btn">Đăng nhập</button>
        </form>
        <div class="register-link">
          Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>

        <div class="divider">
          <span>hoặc</span>
        </div>

        <div class="google-login-container">
          <button class="google-login-btn" onclick="showGmailLogin()">
            <i class="fab fa-google"></i> Đăng nhập bằng Google
          </button>
        </div>

        <!-- Gmail Login Modal -->
        <div id="gmailModal" class="gmail-modal" style="display: none">
          <div class="gmail-modal-content">
            <div class="gmail-modal-header">
              <h3><i class="fab fa-google"></i> Đăng nhập bằng Google</h3>
              <span class="gmail-close" onclick="closeGmailLogin()"
                >&times;</span
              >
            </div>
            <div class="gmail-modal-body">
              <form id="gmailForm" autocomplete="off">
                <div class="form-group">
                  <label for="gmailEmail">Email hoặc tên người dùng:</label>
                  <input
                    type="text"
                    id="gmailEmail"
                    autocomplete="new-email"
                    value=""
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="gmailPassword">Mật khẩu:</label>
                  <input
                    type="password"
                    id="gmailPassword"
                    autocomplete="new-password"
                    value=""
                    required
                  />
                </div>
                <button type="submit" class="gmail-submit-btn">
                  <i class="fab fa-google"></i> Đăng nhập
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="back-to-site">
          <a href="../../index.php"><i class="fas fa-home"></i> Về trang chủ</a>
        </div>
      </div>
    </div>

    <!-- Admin Login Box - Góc phải -->
    <div class="admin-login-box">
      <div
        class="admin-toggle"
        onclick="window.location.href = '../admin/admin-login.php'"
      >
        <i class="fas fa-user-shield"></i>
        <span>Admin</span>
      </div>
    </div>

    <script>
      // Toast notification (tự ẩn sau 2s rồi chuyển trang)
      function showToast(message, redirectUrl) {
        var t = document.createElement("div");
        t.style.cssText =
          "position:fixed;top:28px;left:50%;transform:translateX(-50%);background:#323232;color:#fff;padding:14px 32px;border-radius:10px;font-size:15px;font-weight:500;z-index:99999;box-shadow:0 4px 20px rgba(0,0,0,0.25);opacity:0;transition:opacity 0.3s;pointer-events:none;white-space:nowrap;";
        t.textContent = message;
        document.body.appendChild(t);
        setTimeout(function () {
          t.style.opacity = "1";
        }, 10);
        setTimeout(function () {
          t.style.opacity = "0";
          setTimeout(function () {
            if (t.parentNode) t.parentNode.removeChild(t);
            if (redirectUrl) window.location.href = redirectUrl;
          }, 300);
        }, 2000);
      }

      // Hiển thị form đăng nhập Gmail
      function showGmailLogin() {
        document.getElementById("gmailModal").style.display = "flex";
      }

      // Đóng form đăng nhập Gmail
      function closeGmailLogin() {
        document.getElementById("gmailModal").style.display = "none";
      }

      // Xử lý đăng nhập Gmail
      document
        .getElementById("gmailForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();

          const email = document.getElementById("gmailEmail").value;
          const password = document.getElementById("gmailPassword").value;

          // Cho phép đăng nhập với bất kỳ thông tin nào
          if (email && password) {
            const userName = email.includes("@") ? email.split("@")[0] : email; // Lấy phần trước @ hoặc toàn bộ nếu không có @

            const userInfo = {
              id: "gmail_" + Date.now(),
              name: userName,
              email: email,
              picture: `https://ui-avatars.com/api/?name=${userName}&background=db4437&color=fff&size=50`,
              loginTime: new Date().toISOString(),
              loginType: "gmail",
            };

            localStorage.setItem("userLoggedIn", "true");
            localStorage.setItem("userInfo", JSON.stringify(userInfo));

            showToast("Đăng nhập thành công: " + email, "../../index.php");
          } else {
            alert("Vui lòng nhập đầy đủ thông tin đăng nhập");
          }
        });

      // Toast notification
      function showToast(message, type = 'success', redirectUrl = null) {
        const container = document.getElementById('toast-container') || (() => {
          const c = document.createElement('div');
          c.id = 'toast-container';
          document.body.appendChild(c);
          return c;
        })();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        toast.style.cssText = `
          position: fixed;
          top: 20px;
          left: 50%;
          transform: translateX(-50%);
          background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
          color: white;
          padding: 15px 25px;
          border-radius: 5px;
          z-index: 9999;
          font-weight: 500;
          animation: slideDown 0.3s ease-in;
        `;
        container.appendChild(toast);
        setTimeout(() => {
          toast.style.animation = 'slideUp 0.3s ease-out';
          setTimeout(() => {
            toast.remove();
            if (redirectUrl) {
              window.location.href = redirectUrl;
            }
          }, 300);
        }, 2000);
      }

      function getStoredUsers() {
        try {
          const users = JSON.parse(localStorage.getItem("users") || "[]");
          return Array.isArray(users) ? users : [];
        } catch (e) {
          return [];
        }
      }

      function normalizeEmail(email) {
        return String(email || "").trim().toLowerCase();
      }

      // Xử lý form đăng nhập thông thường
      document
        .getElementById("loginForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();

          const email = document.getElementById("email").value.trim();
          const password = document.getElementById("password").value;

          if (!email || !password) {
            showToast("Vui lòng nhập email và mật khẩu", "error");
            return;
          }

          // Gọi API login
          fetch('/WebBasic/BackEnd/api/login.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({email, password})
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const userInfo = data.user;
              
              // Lưu vào localStorage
              localStorage.setItem("userLoggedIn", "true");
              localStorage.setItem("userInfo", JSON.stringify(userInfo));
              localStorage.setItem("userEmail", userInfo.email);
              
              if (userInfo.isAdmin) {
                localStorage.setItem("adminLoggedIn", "true");
                showToast("Đăng nhập thành công!", "../../index.php");
                setTimeout(() => {
                  window.location.href = "../admin/admin-themsanpham.php";
                }, 1500);
              } else {
                localStorage.removeItem("adminLoggedIn");
                showToast("Đăng nhập thành công!", "../../index.php");
                setTimeout(() => {
                  window.location.href = "../../index.php";
                }, 1500);
              }
            } else {
              showToast(data.message || "Đăng nhập thất bại", "error");
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showToast("Lỗi kết nối: " + error.message, "error");
          });
        });

      // Kiểm tra nếu đã đăng nhập
      if (localStorage.getItem("userLoggedIn") === "true") {
        window.location.href = "../../index.php";
      }

      // Đảm bảo form luôn trống khi load trang
      document.addEventListener("DOMContentLoaded", function () {
        clearAllForms();

        // Chỉ clear 1 lần sau khi DOM load xong
        setTimeout(function () {
          clearAllForms();
        }, 100);

        // Clear lần cuối sau 500ms
        setTimeout(function () {
          clearAllForms();
        }, 500);
      });

      // Function để clear toàn bộ form
      function clearAllForms() {
        // Clear tất cả input fields
        const emailField = document.getElementById("email");
        const passwordField = document.getElementById("password");
        const gmailEmailField = document.getElementById("gmailEmail");
        const gmailPasswordField = document.getElementById("gmailPassword");

        if (
          emailField &&
          (emailField.value === "wed123123" ||
            emailField.value.includes("wed") ||
            emailField.value === "")
        ) {
          emailField.value = "";
          emailField.setAttribute("value", "");
        }
        if (
          passwordField &&
          (passwordField.value.includes("123") || passwordField.value === "")
        ) {
          passwordField.value = "";
          passwordField.setAttribute("value", "");
        }
        if (gmailEmailField) {
          gmailEmailField.value = "";
          gmailEmailField.setAttribute("value", "");
        }
        if (gmailPasswordField) {
          gmailPasswordField.value = "";
          gmailPasswordField.setAttribute("value", "");
        }
      }

      // Chỉ clear khi focus vào input và có giá trị không mong muốn
      document.addEventListener(
        "focus",
        function (e) {
          if (e.target.type === "email" || e.target.type === "password") {
            if (
              e.target.value === "wed123123" ||
              (e.target.value.includes("wed") && e.target.value.length > 0)
            ) {
              e.target.value = "";
            }
          }
        },
        true,
      );

      // Đóng modal khi click bên ngoài
      window.onclick = function (event) {
        const modal = document.getElementById("gmailModal");
        if (event.target == modal) {
          closeGmailLogin();
        }
      };
    </script>
  </body>
</html>

