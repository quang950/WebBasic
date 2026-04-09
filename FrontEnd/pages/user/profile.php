<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - 3 Boys Auto</title>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="/WebBasic/FrontEnd/assets/js/config.js"></script>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 8% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { 
                opacity: 0;
                transform: translateY(-50px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            padding: 24px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            color: white;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .close {
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            line-height: 1;
        }
        
        .close:hover {
            transform: scale(1.2);
        }
        
        .modal-body {
            padding: 24px;
        }
        
        .modal-form-group {
            margin-bottom: 20px;
        }
        
        .modal-form-group label {
            display: block;
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .modal-form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            background: rgba(255,255,255,0.95);
            font-size: 1rem;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        .modal-form-group input:focus {
            outline: none;
            border-color: white;
            background: white;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.2);
        }
        
        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        
        .modal-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .modal-btn-save {
            background: white;
            color: #667eea;
        }
        
        .modal-btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        
        .modal-btn-cancel {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.5);
        }
        
        .modal-btn-cancel:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Thông tin cá nhân</h1>
            <div class="logo">
                <i class="fas fa-user-circle"></i>
            </div>
            <form class="login-form" id="profileForm" onsubmit="return false;">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" readonly>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                    <input type="password" id="password" value="••••••••" readonly>
                    <a href="#" onclick="showChangePasswordModal(); return false;" style="display:block;margin-top:8px;font-size:0.85em;color:#4CAF50;text-decoration:none;transition:color 0.3s;">
                        <i class="fas fa-key"></i> Đổi mật khẩu
                    </a>
                </div>
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Số điện thoại <span style="color: rgba(255, 255, 255, 0.7); font-weight: 400;">(Tùy chọn)</span></label>
                    <input type="text" id="phone" name="phone" placeholder="Nhập số điện thoại" readonly>
                    <a href="#" onclick="showChangePhoneModal(); return false;" style="display:block;margin-top:8px;font-size:0.85em;color:#4CAF50;text-decoration:none;transition:color 0.3s;">
                        <i class="fas fa-edit"></i> Đổi số điện thoại
                    </a>
                </div>
                <div class="form-group">
                    <label for="birthDate"><i class="fas fa-birthday-cake"></i> Ngày sinh <span style="color: rgba(255, 255, 255, 0.7); font-weight: 400;">(Tùy chọn)</span></label>
                    <input type="date" id="birthDate" name="birthDate" readonly>
                    <a href="#" onclick="showChangeBirthDateModal(); return false;" style="display:block;margin-top:8px;font-size:0.85em;color:#4CAF50;text-decoration:none;transition:color 0.3s;">
                        <i class="fas fa-calendar-alt"></i> Đổi ngày sinh
                    </a>
                </div>
                <div class="button-group">
                    <button type="button" class="back-btn" onclick="window.location.href='../../index.php'">
                        <i class="fas fa-arrow-left"></i> <span>Quay lại</span>
                    </button>
                    <button type="submit" class="login-btn">
                        <span>Lưu thông tin</span>
                    </button>
                </div>
            </form>
            <div class="back-to-site">
                <a href="../../index.php"><i class="fas fa-home"></i> Về trang chủ</a>
            </div>
        </div>
    </div>

    <!-- Modal Đổi mật khẩu -->
    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-key"></i> Đổi mật khẩu</h2>
                <span class="close" onclick="closeChangePasswordModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm" onsubmit="savePassword(event)">
                    <div class="modal-form-group">
                        <label for="currentPassword"><i class="fas fa-lock"></i> Mật khẩu hiện tại</label>
                        <input type="password" id="currentPassword" required placeholder="Nhập mật khẩu hiện tại">
                    </div>
                    <div class="modal-form-group">
                        <label for="newPassword"><i class="fas fa-key"></i> Mật khẩu mới</label>
                        <input type="password" id="newPassword" required placeholder="Nhập mật khẩu mới" minlength="6">
                    </div>
                    <div class="modal-form-group">
                        <label for="confirmPassword"><i class="fas fa-check-circle"></i> Xác nhận mật khẩu mới</label>
                        <input type="password" id="confirmPassword" required placeholder="Nhập lại mật khẩu mới" minlength="6">
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="modal-btn modal-btn-cancel" onclick="closeChangePasswordModal()">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="modal-btn modal-btn-save">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Đổi số điện thoại -->
    <div id="changePhoneModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-phone"></i> Đổi số điện thoại</h2>
                <span class="close" onclick="closeChangePhoneModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="changePhoneForm" onsubmit="savePhone(event)">
                    <div class="modal-form-group">
                        <label for="newPhone"><i class="fas fa-mobile-alt"></i> Số điện thoại mới</label>
                        <input type="tel" id="newPhone" required placeholder="Ví dụ: 0901234567" pattern="[0-9]{10}">
                        <small style="color: rgba(255,255,255,0.8); display: block; margin-top: 6px; font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> Nhập 10 chữ số
                        </small>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="modal-btn modal-btn-cancel" onclick="closeChangePhoneModal()">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="modal-btn modal-btn-save">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Đổi ngày sinh -->
    <div id="changeBirthDateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-birthday-cake"></i> Đổi ngày sinh</h2>
                <span class="close" onclick="closeChangeBirthDateModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="changeBirthDateForm" onsubmit="saveBirthDate(event)">
                    <div class="modal-form-group">
                        <label for="newBirthDate"><i class="fas fa-calendar-alt"></i> Ngày sinh mới</label>
                        <input type="date" id="newBirthDate" required>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="modal-btn modal-btn-cancel" onclick="closeChangeBirthDateModal()">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="modal-btn modal-btn-save">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ==== MODAL FUNCTIONS ====
        
        // Đổi mật khẩu
        function showChangePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'block';
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        }
        
        function closeChangePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'none';
        }
        
        function savePassword(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Kiểm tra mật khẩu mới và xác nhận khớp nhau
            if (newPassword !== confirmPassword) {
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
            
            // Lấy user từ localStorage
            const userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}');
            
            if (!userInfo.id) {
                alert('Vui lòng đăng nhập lại');
                return false;
            }
            
            // Call API to change password
            fetch(BASE_URL + '/BackEnd/api/user.php', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId: userInfo.id,
                    action: 'changePassword',
                    currentPassword: currentPassword,
                    newPassword: newPassword
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✓ Đổi mật khẩu thành công!');
                    closeChangePasswordModal();
                } else {
                    alert(data.message || 'Mật khẩu hiện tại không đúng');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Có lỗi xảy ra!');
            });
            
            return false;
        }
        
        // Đổi số điện thoại
        function showChangePhoneModal() {
            document.getElementById('changePhoneModal').style.display = 'block';
            // Pre-fill với số hiện tại nếu có
            const currentPhone = document.getElementById('phone').value;
            document.getElementById('newPhone').value = currentPhone;
        }
        
        function closeChangePhoneModal() {
            document.getElementById('changePhoneModal').style.display = 'none';
        }
        
        function savePhone(e) {
            e.preventDefault();
            
            // Prototype mode - không lưu thay đổi
            closeChangePhoneModal();
            
            return false;
        }
        
        // Đổi ngày sinh
        function showChangeBirthDateModal() {
            document.getElementById('changeBirthDateModal').style.display = 'block';
            // Pre-fill với ngày sinh hiện tại nếu có
            const currentBirthDate = document.getElementById('birthDate').value;
            document.getElementById('newBirthDate').value = currentBirthDate;
        }
        
        function closeChangeBirthDateModal() {
            document.getElementById('changeBirthDateModal').style.display = 'none';
        }
        
        function saveBirthDate(e) {
            e.preventDefault();
            
            // Prototype mode - không lưu thay đổi
            closeChangeBirthDateModal();
            
            return false;
        }
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Load thông tin user khi trang load
        window.addEventListener('DOMContentLoaded', function() {
            const userInfo = localStorage.getItem('userInfo');
            
            if (!userInfo) {
                window.location.href = '../../index.php';
                return;
            }
            
            const user = JSON.parse(userInfo);
            const userId = user.id;
            
            // Fetch user data từ API để cập nhật real-time
            fetch(BASE_URL + '/BackEnd/api/user.php?id=' + userId, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.user) {
                        const apiUser = data.user;
                        
                        // Check if user is locked
                        if (apiUser.locked) {
                            // Show locked message
                            const profileForm = document.getElementById('profileForm');
                            const lockedMsg = document.createElement('div');
                            lockedMsg.style.cssText = 'background: #ff6b6b; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center;';
                            lockedMsg.innerHTML = '<i class="fas fa-lock"></i> Tài khoản của bạn đã bị khóa. Vui lòng liên hệ admin.';
                            profileForm.parentNode.insertBefore(lockedMsg, profileForm);
                            
                            // Disable all form fields
                            const inputs = profileForm.querySelectorAll('input, button, a');
                            inputs.forEach(input => {
                                input.disabled = true;
                                input.style.opacity = '0.5';
                            });
                        }
                        
                        // Populate form with API data
                        document.getElementById('email').value = apiUser.email || '';
                        document.getElementById('phone').value = apiUser.phone || '';
                        document.getElementById('birthDate').value = apiUser.birth_date || '';
                        
                        // Store user ID for later use
                        window.currentUserId = userId;
                    }
                })
                .catch(error => {
                    console.error('Error loading user profile:', error);
                });
        });

        // Xử lý form submit - Save thông tin user
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userId = window.currentUserId;
            if (!userId) {
                alert('Không thể lưu: Thiếu user ID');
                return false;
            }
            
            const formData = {
                id: userId,
                phone: document.getElementById('phone').value,
                birthDate: document.getElementById('birthDate').value
            };
            
            fetch(BASE_URL + '/BackEnd/api/user.php', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✓ Cập nhật thông tin thành công!');
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể lưu'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi: ' + error.message);
                });
            
            return false;
        });
    </script>
</body>
</html>

