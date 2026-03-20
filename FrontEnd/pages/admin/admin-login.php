<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Portal - 3 Boys Auto</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        background-color: #f0f2f5;
        font-family:
          -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
      }

      .portal-card {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 420px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
      }

      .portal-header {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
      }

      .portal-header .back-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        color: #333;
        padding: 4px 8px 4px 0;
        display: flex;
        align-items: center;
        text-decoration: none;
      }

      .portal-header .back-btn:hover {
        color: #1a73e8;
      }

      .portal-header h1 {
        font-size: 17px;
        font-weight: 600;
        color: #1a1a1a;
        margin-left: 6px;
      }

      .banner {
        width: 100%;
        height: 180px;
        background: linear-gradient(
          135deg,
          #1abc9c 0%,
          #16a085 40%,
          #1a8a7a 100%
        );
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
      }

      .banner::before {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, 0.07);
        border-radius: 50%;
        top: -60px;
        left: -60px;
      }

      .banner::after {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.07);
        border-radius: 50%;
        bottom: -50px;
        right: -30px;
      }

      .shield-icon {
        width: 80px;
        height: 80px;
        background: #1a73e8;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
      }

      .shield-icon i {
        font-size: 38px;
        color: #fff;
      }

      .portal-body {
        padding: 28px 28px 20px;
      }

      .welcome-title {
        font-size: 26px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 6px;
      }

      .welcome-subtitle {
        font-size: 14px;
        color: #888;
        margin-bottom: 28px;
      }

      .form-group {
        margin-bottom: 18px;
      }

      .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
      }

      .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
      }

      .input-wrapper .icon-left {
        position: absolute;
        left: 14px;
        color: #aaa;
        font-size: 15px;
        pointer-events: none;
      }

      .input-wrapper input {
        width: 100%;
        padding: 13px 44px 13px 42px;
        border: 1.5px solid #e0e0e0;
        border-radius: 10px;
        font-size: 15px;
        color: #333;
        background: #fafafa;
        transition:
          border-color 0.2s,
          box-shadow 0.2s;
        outline: none;
      }

      .input-wrapper input:focus {
        border-color: #1a73e8;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.12);
      }

      .input-wrapper input::placeholder {
        color: #bbb;
      }

      .toggle-password {
        position: absolute;
        right: 14px;
        background: none;
        border: none;
        cursor: pointer;
        color: #aaa;
        font-size: 16px;
        padding: 0;
        display: flex;
        align-items: center;
      }

      .toggle-password:hover {
        color: #555;
      }

      .form-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
      }

      .remember-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #555;
        cursor: pointer;
      }

      .remember-label input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #1a73e8;
        cursor: pointer;
      }

      .forgot-link {
        font-size: 14px;
        color: #1a73e8;
        text-decoration: none;
        font-weight: 500;
      }
      .forgot-link:hover {
        text-decoration: underline;
      }

      .signin-btn {
        width: 100%;
        padding: 14px;
        background: #1a73e8;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition:
          background 0.2s,
          transform 0.1s;
      }

      .signin-btn:hover {
        background: #1558b0;
      }
      .signin-btn:active {
        transform: scale(0.98);
      }

      .error-msg {
        background: #fff0f0;
        color: #d93025;
        border: 1px solid #f5c6c6;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        margin-top: 14px;
        display: none;
      }

      .portal-footer {
        text-align: center;
        padding: 0 28px 24px;
      }

      .portal-footer p {
        font-size: 12px;
        color: #aaa;
        line-height: 1.7;
      }

      .portal-footer a {
        color: #1a73e8;
        text-decoration: none;
        font-weight: 500;
      }
      .portal-footer a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <div class="portal-card">
      <!-- Header -->
      <div class="portal-header">
        <a href="../user/login.php" class="back-btn" title="Quay lại">
          <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Admin Portal</h1>
      </div>

      <!-- Banner -->
      <div class="banner">
        <div class="shield-icon">
          <i class="fas fa-user-shield"></i>
        </div>
      </div>

      <!-- Body -->
      <div class="portal-body">
        <div class="welcome-title">Welcome Back</div>
        <div class="welcome-subtitle">Sign in to manage your operations</div>

        <form id="adminLoginForm" autocomplete="off" data-lpignore="true">
          <!-- Honeypot fields to prevent autofill -->
          <input type="text" style="display: none" autocomplete="new-text" />
          <input
            type="password"
            style="display: none"
            autocomplete="new-password"
          />

          <div class="form-group">
            <label for="adminUsername">Username or Email</label>
            <div class="input-wrapper">
              <i class="fas fa-user icon-left"></i>
              <input
                type="text"
                id="adminUsername"
                name="adminUsername"
                placeholder="admin@example.com"
                autocomplete="new-username"
                data-lpignore="true"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="adminPassword">Password</label>
            <div class="input-wrapper">
              <i class="fas fa-lock icon-left"></i>
              <input
                type="password"
                id="adminPassword"
                name="adminPassword"
                placeholder="••••••••"
                autocomplete="new-password"
                data-lpignore="true"
                required
              />
              <button
                type="button"
                class="toggle-password"
                id="togglePwd"
                title="Hiện/Ẩn mật khẩu"
              >
                <i class="fas fa-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <label class="remember-label">
              <input type="checkbox" id="rememberAdmin" /> Remember me
            </label>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>

          <button type="submit" class="signin-btn">
            Sign In <i class="fas fa-sign-in-alt"></i>
          </button>

          <div class="error-msg" id="adminError"></div>
        </form>
      </div>

      <!-- Footer -->
      <div class="portal-footer">
        <p>
          Protected by enterprise-grade encryption.<br />
          Need help? <a href="#">Contact Support</a>
        </p>
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

      // Clear form on load to prevent autofill
      document.addEventListener("DOMContentLoaded", function () {
        clearForm();
        setTimeout(clearForm, 100);
        setTimeout(clearForm, 500);
      });

      function clearForm() {
        const u = document.getElementById("adminUsername");
        const p = document.getElementById("adminPassword");
        if (u) u.value = "";
        if (p) p.value = "";
      }

      // Toggle password visibility
      document
        .getElementById("togglePwd")
        .addEventListener("click", function () {
          const pwd = document.getElementById("adminPassword");
          const icon = document.getElementById("eyeIcon");
          if (pwd.type === "password") {
            pwd.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
          } else {
            pwd.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
          }
        });

      // Handle login
      document
        .getElementById("adminLoginForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();

          const username = document
            .getElementById("adminUsername")
            .value.trim();
          const password = document.getElementById("adminPassword").value;
          const errorDiv = document.getElementById("adminError");

          if (!username || !password) {
            errorDiv.textContent = "Vui lòng nhập đầy đủ thông tin đăng nhập!";
            errorDiv.style.display = "block";
            setTimeout(() => {
              errorDiv.style.display = "none";
            }, 3000);
            return;
          }

          const adminInfo = {
            id: "admin_" + Date.now(),
            name: username,
            username: username,
            picture:
              "https://ui-avatars.com/api/?name=" +
              encodeURIComponent(username) +
              "&background=dc3545&color=fff&size=50",
            loginTime: new Date().toISOString(),
            loginType: "admin",
          };

          localStorage.setItem("adminLoggedIn", "true");
          localStorage.setItem("adminUsername", username);
          localStorage.setItem("adminInfo", JSON.stringify(adminInfo));

          showToast(
            "Đăng nhập admin thành công: " + username,
            "admin-themsanpham.php",
          );
        });

      // If already logged in as admin, redirect
      if (localStorage.getItem("adminLoggedIn") === "true") {
        window.location.href = "admin-themsanpham.php";
      }
    </script>
  </body>
</html>

