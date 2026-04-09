/**
 * ========================================
 * WebBasic - CLIENT-SIDE VALIDATION LIBRARY
 * Validation trước khi submit form
 * ========================================
 */
// ---------- UTILITY FUNCTIONS ----------

/**
 * Hiển thị error message dưới field
 */
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    // Di chuyển focus
    field.focus();
    field.classList.add('is-invalid');
    
    // Tạo hoặc update error message
    let errorDiv = field.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

/**
 * Xóa error message
 */
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.remove('is-invalid');
    const errorDiv = field.nextElementSibling;
    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
        errorDiv.style.display = 'none';
    }
}

/**
 * Xóa tất cả error messages
 */
function clearAllErrors() {
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.style.display = 'none';
    });
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
}

// ---------- VALIDATION RULES ----------

/**
 * Kiểm tra Email
 * @param {string} email - Email cần kiểm tra
 * @returns {object} { valid: boolean, message: string }
 */
function validateEmail(email) {
    if (!email || email.trim() === '') {
        return { valid: false, message: 'Email không được để trống' };
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        return { valid: false, message: 'Email không hợp lệ' };
    }
    
    if (email.length > 100) {
        return { valid: false, message: 'Email tối đa 100 ký tự' };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Mật khẩu
 * @param {string} password - Mật khẩu cần kiểm tra
 * @param {boolean} checkStrength - Kiểm tra độ mạnh (default: false)
 * @returns {object}
 */
function validatePassword(password, checkStrength = false) {
    if (!password) {
        return { valid: false, message: 'Mật khẩu không được để trống' };
    }
    
    if (password.length < 6) {
        return { valid: false, message: 'Mật khẩu tối thiểu 6 ký tự' };
    }
    
    if (password.length > 255) {
        return { valid: false, message: 'Mật khẩu tối đa 255 ký tự' };
    }
    
    // Kiểm tra độ mạnh: ít nhất 1 chữ cái, 1 số
    if (checkStrength) {
        if (!/[a-zA-Z]/.test(password)) {
            return { valid: false, message: 'Mật khẩu phải chứa chữ cái' };
        }
        if (!/[0-9]/.test(password)) {
            return { valid: false, message: 'Mật khẩu phải chứa số' };
        }
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Xác nhận Mật khẩu
 * @param {string} password - Mật khẩu gốc
 * @param {string} confirmPassword - Xác nhận mật khẩu
 * @returns {object}
 */
function validatePasswordConfirm(password, confirmPassword) {
    if (!confirmPassword) {
        return { valid: false, message: 'Vui lòng xác nhận mật khẩu' };
    }
    
    if (password !== confirmPassword) {
        return { valid: false, message: 'Mật khẩu không khớp' };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Tên người dùng
 * @param {string} name - Tên cần kiểm tra
 * @param {string} fieldName - Tên trường (Họ/Tên/etc)
 * @returns {object}
 */
function validateName(name, fieldName = 'Tên') {
    if (!name || name.trim() === '') {
        return { valid: false, message: `${fieldName} không được để trống` };
    }
    
    if (name.trim().length < 2) {
        return { valid: false, message: `${fieldName} tối thiểu 2 ký tự` };
    }
    
    if (name.length > 50) {
        return { valid: false, message: `${fieldName} tối đa 50 ký tự` };
    }
    
    // Không được chứa số
    if (/[0-9]/.test(name)) {
        return { valid: false, message: `${fieldName} không được chứa số` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Số điện thoại Việt Nam
 * @param {string} phone - Số điện thoại
 * @returns {object}
 */
function validatePhone(phone) {
    if (!phone || phone.trim() === '') {
        return { valid: false, message: 'Số điện thoại không được để trống' };
    }
    
    // Loại bỏ khoảng trắng
    const cleanPhone = phone.replace(/\s/g, '');
    
    // Kiểm tra: 10-11 số, bắt đầu 0
    const phoneRegex = /^0[0-9]{9,10}$/;
    if (!phoneRegex.test(cleanPhone)) {
        return { valid: false, message: 'Số điện thoại phải là 10-11 số, bắt đầu 0' };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Ngày sinh (phải >= 18 tuổi)
 * @param {string} dateStr - Ngày sinh (format: YYYY-MM-DD)
 * @returns {object}
 */
function validateBirthDate(dateStr) {
    if (!dateStr) {
        return { valid: false, message: 'Ngày sinh không được để trống' };
    }
    
    const birthDate = new Date(dateStr);
    const today = new Date();
    
    // Kiểm tra ngày có hợp lệ
    if (isNaN(birthDate.getTime())) {
        return { valid: false, message: 'Ngày sinh không hợp lệ' };
    }
    
    // Kiểm tra không được > hôm nay
    if (birthDate >= today) {
        return { valid: false, message: 'Ngày sinh không được lớn hơn hôm nay' };
    }
    
    // Kiểm tra >= 18 tuổi
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age < 18) {
        return { valid: false, message: 'Bạn phải >= 18 tuổi' };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Địa chỉ
 * @param {string} address - Địa chỉ cần kiểm tra
 * @param {string} fieldName - Tên trường
 * @param {number} minLength - Độ dài tối thiểu
 * @param {number} maxLength - Độ dài tối đa
 * @returns {object}
 */
function validateAddress(address, fieldName = 'Địa chỉ', minLength = 5, maxLength = 255) {
    if (!address || address.trim() === '') {
        return { valid: false, message: `${fieldName} không được để trống` };
    }
    
    if (address.trim().length < minLength) {
        return { valid: false, message: `${fieldName} tối thiểu ${minLength} ký tự` };
    }
    
    if (address.length > maxLength) {
        return { valid: false, message: `${fieldName} tối đa ${maxLength} ký tự` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Select (không được rỗng)
 * @param {string} value - Giá trị
 * @param {string} fieldName - Tên trường
 * @returns {object}
 */
function validateSelect(value, fieldName = 'Lựa chọn') {
    if (!value || value.trim() === '') {
        return { valid: false, message: `Vui lòng chọn ${fieldName}` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Checkbox (phải được check)
 * @param {string} checkboxId - ID của checkbox
 * @param {string} fieldName - Tên trường
 * @returns {object}
 */
function validateCheckbox(checkboxId, fieldName = 'Điều khoản') {
    const checkbox = document.getElementById(checkboxId);
    if (!checkbox || !checkbox.checked) {
        return { valid: false, message: `Vui lòng chấp nhận ${fieldName}` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Số tiền (phải > 0)
 * @param {number|string} amount - Số tiền
 * @param {string} fieldName - Tên trường
 * @returns {object}
 */
function validateCurrency(amount, fieldName = 'Giá tiền') {
    if (amount === '' || amount === null || amount === undefined) {
        return { valid: false, message: `${fieldName} không được để trống` };
    }
    
    const numAmount = parseFloat(amount);
    
    if (isNaN(numAmount)) {
        return { valid: false, message: `${fieldName} phải là số` };
    }
    
    if (numAmount <= 0) {
        return { valid: false, message: `${fieldName} phải > 0` };
    }
    
    if (numAmount > 999999999999) {
        return { valid: false, message: `${fieldName} quá lớn` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Số lượng (phải > 0, là số nguyên)
 * @param {number|string} quantity - Số lượng
 * @param {number} maxQuantity - Số lượng tối đa (optional)
 * @returns {object}
 */
function validateQuantity(quantity, maxQuantity = null) {
    if (quantity === '' || quantity === null || quantity === undefined) {
        return { valid: false, message: 'Số lượng không được để trống' };
    }
    
    const numQuantity = parseInt(quantity);
    
    if (isNaN(numQuantity)) {
        return { valid: false, message: 'Số lượng phải là số' };
    }
    
    if (numQuantity <= 0) {
        return { valid: false, message: 'Số lượng phải > 0' };
    }
    
    if (!Number.isInteger(numQuantity)) {
        return { valid: false, message: 'Số lượng phải là số nguyên' };
    }
    
    if (maxQuantity !== null && numQuantity > maxQuantity) {
        return { valid: false, message: `Số lượng tối đa ${maxQuantity}` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Hình ảnh
 * @param {File} file - File hình ảnh
 * @param {number} maxSizeMB - Kích thước tối đa (MB)
 * @returns {object}
 */
function validateImage(file, maxSizeMB = 5) {
    if (!file) {
        return { valid: false, message: 'Vui lòng chọn hình ảnh' };
    }
    
    // Kiểm tra loại file
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        return { valid: false, message: 'Chỉ chấp nhận JPG, PNG, GIF, WebP' };
    }
    
    // Kiểm tra kích thước
    const maxSizeBytes = maxSizeMB * 1024 * 1024;
    if (file.size > maxSizeBytes) {
        return { valid: false, message: `Hình ảnh tối đa ${maxSizeMB}MB` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra Mô tả (Text area)
 * @param {string} text - Nội dung
 * @param {number} minLength - Độ dài tối thiểu
 * @param {number} maxLength - Độ dài tối đa
 * @returns {object}
 */
function validateDescription(text, minLength = 10, maxLength = 1000) {
    if (!text || text.trim() === '') {
        return { valid: false, message: 'Mô tả không được để trống' };
    }
    
    if (text.trim().length < minLength) {
        return { valid: false, message: `Mô tả tối thiểu ${minLength} ký tự` };
    }
    
    if (text.length > maxLength) {
        return { valid: false, message: `Mô tả tối đa ${maxLength} ký tự` };
    }
    
    return { valid: true, message: '' };
}

/**
 * Kiểm tra URL hình ảnh
 * @param {string} url - URL
 * @returns {object}
 */
function validateImageUrl(url) {
    if (!url || url.trim() === '') {
        return { valid: false, message: 'URL hình ảnh không được để trống' };
    }
    
    const urlRegex = /^(https?:\/\/.+\.(jpg|jpeg|png|gif|webp))$/i;
    if (!urlRegex.test(url)) {
        return { valid: false, message: 'URL hình ảnh không hợp lệ' };
    }
    
    return { valid: true, message: '' };
}

// ---------- FORM VALIDATORS (CHO CÁC FORM CỤ THỂ) ----------

/**
 * Validate Register Form
 * @returns {boolean}
 */
function validateRegisterForm() {
    clearAllErrors();
    let isValid = true;
    
    // Họ
    const firstName = document.getElementById('firstName')?.value;
    const firstNameCheck = validateName(firstName, 'Họ');
    if (!firstNameCheck.valid) {
        showFieldError('firstName', firstNameCheck.message);
        isValid = false;
    } else {
        clearFieldError('firstName');
    }
    
    // Tên
    const lastName = document.getElementById('lastName')?.value;
    const lastNameCheck = validateName(lastName, 'Tên');
    if (!lastNameCheck.valid) {
        showFieldError('lastName', lastNameCheck.message);
        isValid = false;
    } else {
        clearFieldError('lastName');
    }
    
    // Email
    const email = document.getElementById('email')?.value;
    const emailCheck = validateEmail(email);
    if (!emailCheck.valid) {
        showFieldError('email', emailCheck.message);
        isValid = false;
    } else {
        clearFieldError('email');
    }
    
    // Số điện thoại
    const phone = document.getElementById('phone')?.value;
    const phoneCheck = validatePhone(phone);
    if (!phoneCheck.valid) {
        showFieldError('phone', phoneCheck.message);
        isValid = false;
    } else {
        clearFieldError('phone');
    }
    
    // Ngày sinh
    const birthDate = document.getElementById('birthDate')?.value;
    const birthDateCheck = validateBirthDate(birthDate);
    if (!birthDateCheck.valid) {
        showFieldError('birthDate', birthDateCheck.message);
        isValid = false;
    } else {
        clearFieldError('birthDate');
    }
    
    // Tỉnh/Thành phố
    const province = document.getElementById('province')?.value;
    const provinceCheck = validateSelect(province, 'Tỉnh/Thành phố');
    if (!provinceCheck.valid) {
        showFieldError('province', provinceCheck.message);
        isValid = false;
    } else {
        clearFieldError('province');
    }
    
    // Quận/Huyện
    const district = document.getElementById('district')?.value;
    const districtCheck = validateAddress(district, 'Quận/Huyện', 2, 100);
    if (!districtCheck.valid) {
        showFieldError('district', districtCheck.message);
        isValid = false;
    } else {
        clearFieldError('district');
    }
    
    // Phường/Xã
    const ward = document.getElementById('ward')?.value;
    const wardCheck = validateAddress(ward, 'Phường/Xã', 2, 100);
    if (!wardCheck.valid) {
        showFieldError('ward', wardCheck.message);
        isValid = false;
    } else {
        clearFieldError('ward');
    }
    
    // Địa chỉ chi tiết
    const addressDetail = document.getElementById('addressDetail')?.value;
    const addressCheck = validateAddress(addressDetail, 'Địa chỉ giao hàng', 5, 255);
    if (!addressCheck.valid) {
        showFieldError('addressDetail', addressCheck.message);
        isValid = false;
    } else {
        clearFieldError('addressDetail');
    }
    
    // Mật khẩu
    const password = document.getElementById('password')?.value;
    const passwordCheck = validatePassword(password, true);
    if (!passwordCheck.valid) {
        showFieldError('password', passwordCheck.message);
        isValid = false;
    } else {
        clearFieldError('password');
    }
    
    // Xác nhận mật khẩu
    const confirmPassword = document.getElementById('confirmPassword')?.value;
    const passwordConfirmCheck = validatePasswordConfirm(password, confirmPassword);
    if (!passwordConfirmCheck.valid) {
        showFieldError('confirmPassword', passwordConfirmCheck.message);
        isValid = false;
    } else {
        clearFieldError('confirmPassword');
    }
    
    // Điều khoản
    const agreeTermsCheck = validateCheckbox('agreeTerms', 'Điều khoản sử dụng');
    if (!agreeTermsCheck.valid) {
        showFieldError('agreeTerms', agreeTermsCheck.message);
        isValid = false;
    } else {
        clearFieldError('agreeTerms');
    }
    
    return isValid;
}

/**
 * Validate Login Form
 * @returns {boolean}
 */
function validateLoginForm() {
    clearAllErrors();
    let isValid = true;
    
    // Email
    const email = document.getElementById('loginEmail')?.value;
    const emailCheck = validateEmail(email);
    if (!emailCheck.valid) {
        showFieldError('loginEmail', emailCheck.message);
        isValid = false;
    } else {
        clearFieldError('loginEmail');
    }
    
    // Password
    const password = document.getElementById('loginPassword')?.value;
    const passwordCheck = validatePassword(password, false);
    if (!passwordCheck.valid) {
        showFieldError('loginPassword', passwordCheck.message);
        isValid = false;
    } else {
        clearFieldError('loginPassword');
    }
    
    return isValid;
}

/**
 * Validate Checkout Form
 * @returns {boolean}
 */
function validateCheckoutForm() {
    clearAllErrors();
    let isValid = true;
    
    // Kiểm tra có địa chỉ được chọn
    const selectedAddress = document.querySelector('.address-item.selected');
    if (!selectedAddress) {
        // Hoặc nhập địa chỉ mới
        const addressDetail = document.getElementById('shippingAddress')?.value;
        if (!addressDetail || addressDetail.trim() === '') {
            alert('Vui lòng chọn hoặc nhập địa chỉ giao hàng');
            isValid = false;
        }
    }
    
    // Kiểm tra phương thức thanh toán
    const paymentMethod = document.getElementById('paymentMethod')?.value;
    const paymentCheck = validateSelect(paymentMethod, 'phương thức thanh toán');
    if (!paymentCheck.valid) {
        showFieldError('paymentMethod', paymentCheck.message);
        isValid = false;
    } else {
        clearFieldError('paymentMethod');
    }
    
    return isValid;
}

/**
 * Validate Product Form (Admin)
 * @returns {boolean}
 */
function validateProductForm() {
    clearAllErrors();
    let isValid = true;
    
    // Tên sản phẩm
    const productName = document.getElementById('productName')?.value;
    const nameCheck = validateAddress(productName, 'Tên sản phẩm', 3, 255);
    if (!nameCheck.valid) {
        showFieldError('productName', nameCheck.message);
        isValid = false;
    } else {
        clearFieldError('productName');
    }
    
    // Loại sản phẩm
    const categoryId = document.getElementById('categoryId')?.value;
    const categoryCheck = validateSelect(categoryId, 'loại sản phẩm');
    if (!categoryCheck.valid) {
        showFieldError('categoryId', categoryCheck.message);
        isValid = false;
    } else {
        clearFieldError('categoryId');
    }
    
    // Giá bán
    const price = document.getElementById('price')?.value;
    const priceCheck = validateCurrency(price, 'Giá bán');
    if (!priceCheck.valid) {
        showFieldError('price', priceCheck.message);
        isValid = false;
    } else {
        clearFieldError('price');
    }
    
    // Giá nhập
    const costPrice = document.getElementById('costPrice')?.value;
    const costPriceCheck = validateCurrency(costPrice, 'Giá nhập');
    if (!costPriceCheck.valid) {
        showFieldError('costPrice', costPriceCheck.message);
        isValid = false;
    } else {
        clearFieldError('costPrice');
    }
    
    // Kiểm tra giá nhập < giá bán
    if (parseFloat(costPrice) >= parseFloat(price)) {
        showFieldError('costPrice', 'Giá nhập phải < giá bán');
        isValid = false;
    }
    
    // Số lượng
    const stock = document.getElementById('stock')?.value;
    const stockCheck = validateQuantity(stock);
    if (!stockCheck.valid) {
        showFieldError('stock', stockCheck.message);
        isValid = false;
    } else {
        clearFieldError('stock');
    }
    
    // Mô tả
    const description = document.getElementById('productDescription')?.value;
    const descCheck = validateDescription(description, 10, 1000);
    if (!descCheck.valid) {
        showFieldError('productDescription', descCheck.message);
        isValid = false;
    } else {
        clearFieldError('productDescription');
    }
    
    return isValid;
}

/**
 * Validate Category Form (Admin)
 * @returns {boolean}
 */
function validateCategoryForm() {
    clearAllErrors();
    let isValid = true;
    
    // Tên danh mục
    const categoryName = document.getElementById('categoryName')?.value;
    const nameCheck = validateAddress(categoryName, 'Tên danh mục', 3, 100);
    if (!nameCheck.valid) {
        showFieldError('categoryName', nameCheck.message);
        isValid = false;
    } else {
        clearFieldError('categoryName');
    }
    
    // Mô tả (không bắt buộc)
    const description = document.getElementById('categoryDescription')?.value;
    if (description && description.length > 500) {
        showFieldError('categoryDescription', 'Mô tả tối đa 500 ký tự');
        isValid = false;
    } else {
        clearFieldError('categoryDescription');
    }
    
    return isValid;
}

// Export cho sử dụng
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateEmail,
        validatePassword,
        validatePasswordConfirm,
        validateName,
        validatePhone,
        validateBirthDate,
        validateAddress,
        validateSelect,
        validateCheckbox,
        validateCurrency,
        validateQuantity,
        validateImage,
        validateDescription,
        validateImageUrl,
        validateRegisterForm,
        validateLoginForm,
        validateCheckoutForm,
        validateProductForm,
        validateCategoryForm,
        showFieldError,
        clearFieldError,
        clearAllErrors
    };
}
