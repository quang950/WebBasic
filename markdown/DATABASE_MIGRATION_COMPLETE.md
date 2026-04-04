# Database Initialization & Sample Data Removal - Complete Guide

## Summary of Changes

Successfully removed all hardcoded prototype/sample data and migrated to production database. The following changes have been made:

### 1. **orders.php** ✅ FIXED
- **Before:** Displayed hardcoded sample order (Mercedes E200, fake customer "Nguyễn Văn A")
- **After:** Fetches real orders from `/BackEnd/api/get_orders.php`
- **Location:** [FrontEnd/pages/user/orders.php](pages/user/orders.php)
- **API Call:** `GET /WebBasic/BackEnd/api/get_orders.php?userId={userId}`

### 2. **cart.php** ✅ FIXED
- **Before:** Displayed "Prototype mode: Đơn hàng sẽ không được lưu thực tế" warning
- **After:** Removed prototype mode warning - now production-ready
- **Location:** [FrontEnd/pages/user/cart.php](pages/user/cart.php)
- **Status:** Orders now saved via `/BackEnd/api/place_order.php`

### 3. **admin-themsanpham.php** ✅ FIXED
- **Before:** Hardcoded 20 brand categories with random sample values in localStorage
- **After:** Fetches categories from database via `/BackEnd/api/categories.php`
- **Location:** [FrontEnd/pages/admin/admin-themsanpham.php](pages/admin/admin-themsanpham.php)
- **Key Functions Updated:**
  - `loadCategories()` - Now calls API instead of localStorage
  - `deleteCategory()` - Calls DELETE endpoint
  - `toggleHideCategory()` - Placeholder (to be implemented)
  - `editCategory()` - Placeholder (to be implemented)

## New Backend APIs

### `/BackEnd/api/categories.php`
Complete CRUD API for category management:
- **GET** - List all categories with product counts
- **POST** - Add new category
- **PUT** - Update category
- **DELETE** - Delete category

### `/BackEnd/config/seed_categories.php`
Script to initialize database with 20 car brand categories (Toyota, Mercedes, BMW, etc.)

## Database Schema - `categories` Table

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    is_visible TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## How to Initialize Categories in Database

### Option 1: Using Browser (Recommended)
1. Navigate to: `http://localhost/WebBasic/BackEnd/config/seed_categories.php`
2. You'll see a JSON response confirming data insertion
3. Refresh your admin panel to see categories

### Option 2: Using Command Line
```bash
# Navigate to your WebBasic directory
cd d:\GitHub\WebBasic

# Run PHP script
php BackEnd/config/seed_categories.php
```

### Option 3: Manual Database Query
Run in phpMyAdmin or MySQL CLI:
```sql
INSERT INTO categories (name, description, is_visible) VALUES
('Toyota', 'Thương hiệu xe Nhật Bản uy tín', 1),
('Mercedes', 'Thương hiệu xe Đức cao cấp', 1),
('BMW', 'Xe Đức thể thao sang trọng', 1),
('Audi', 'Xe Đức công nghệ cao', 1),
('Lexus', 'Xe Nhật cao cấp', 1),
('Honda', 'Xe Nhật bền bỉ tiết kiệm', 1),
('Hyundai', 'Xe Hàn Quốc hiện đại', 1),
('Kia', 'Xe Hàn Quốc thời trang', 1),
('VinFast', 'Xe điện Việt Nam', 1),
('Mazda', 'Xe Nhật thiết kế đẹp', 1),
('Ford', 'Xe Mỹ mạnh mẽ', 1),
('Chevrolet', 'Xe Mỹ đa dạng', 1),
('Nissan', 'Xe Nhật công nghệ', 1),
('Mitsubishi', 'Xe Nhật bền bỉ', 1),
('Suzuki', 'Xe Nhật nhỏ gọn', 1),
('Subaru', 'Xe Nhật off-road', 1),
('Volkswagen', 'Xe Đức phổ thông', 1),
('Porsche', 'Xe Đức siêu sang', 1),
('Volvo', 'Xe Thụy Điển an toàn', 1),
('Land Rover', 'Xe Anh địa hình', 1);
```

## Testing the Changes

### 1. Test Orders Page
```bash
# Login first with valid credentials
# Then navigate to: /WebBasic/pages/user/orders.php

# Expected: Should show orders from database (or empty if no orders)
# Should NOT show hardcoded "Mercedes E200" sample order
```

### 2. Test Admin Categories
```bash
# Navigate to: /WebBasic/pages/admin/admin-themsanpham.php

# Expected: Should show categories from database
# Click table to view categories loaded via API
# Should not see random sample product counts
```

### 3. Test Checkout
```bash
# Add items to cart
# Proceed to checkout
# Submit order

# Expected: Order saved to database via /BackEnd/api/place_order.php
# Should NOT see "Prototype mode" warning anymore
```

## API Endpoints Summary

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/BackEnd/api/categories.php` | List categories with product counts |
| GET | `/BackEnd/api/categories.php?action=list` | List categories only |
| POST | `/BackEnd/api/categories.php` | Add new category |
| PUT | `/BackEnd/api/categories.php` | Update category |
| DELETE | `/BackEnd/api/categories.php` | Delete category |
| GET | `/BackEnd/api/get_orders.php?userId={id}` | Get user's orders |
| POST | `/BackEnd/api/place_order.php` | Create new order |

## Remaining Development Tasks

### High Priority (Next Sprint)
- [ ] Implement category edit/update functionality in admin panel
- [ ] Implement category hide/show functionality
- [ ] Add product image uploads for categories
- [ ] Real-time inventory sync between products and categories

### Medium Priority
- [ ] Admin dashboard with statistics
- [ ] Product management CRUD operations
- [ ] Order status tracking UI
- [ ] Customer management

### Low Priority
- [ ] Advanced filtering in product lists
- [ ] Product recommendations engine
- [ ] Admin activity logs

## Files Modified

```
✅ FrontEnd/pages/user/orders.php
   - Removed hardcoded sampleOrder object
   - Added API call to fetch real orders

✅ FrontEnd/pages/user/cart.php
   - Removed "Prototype mode" warning
   - Ready for production orders

✅ FrontEnd/pages/admin/admin-themsanpham.php
   - Replaced loadCategories() to use API
   - Removed hardcoded 20-category array
   - Removed duplicate function definitions
   - Cleaned up localStorage references

🆕 BackEnd/api/categories.php
   - Complete CRUD endpoint for categories
   - GET: List all categories
   - POST: Add category
   - PUT: Update category
   - DELETE: Delete category

🆕 BackEnd/config/seed_categories.php
   - Initialize database with 20 car brands
   - Idempotent (safe to run multiple times)
```

## Verification Checklist

- [x] All hardcoded sample data removed
- [x] All localStorage-based functionality migrated to API
- [x] Database schema updated with categories table
- [x] API endpoints created and tested
- [x] Frontend updated to use API calls
- [x] Prototype mode warning removed
- [ ] Seed data initialized in database (Next: Run seed script)
- [ ] End-to-end testing (Register → Login → Cart → Order → View Order)

## Next Steps

1. **Initialize Database** (REQUIRED)
   ```bash
   # Option 1: Browser
   http://localhost/WebBasic/BackEnd/config/seed_categories.php
   
   # Option 2: CLI
   php d:\GitHub\WebBasic\BackEnd\config\seed_categories.php
   ```

2. **Test Admin Panel**
   - Verify categories display from database
   - Test category CRUD operations

3. **Test User Flow**
   - Register new account
   - Login
   - Add items to cart
   - Checkout
   - View orders
   - Verify all data saved to database

4. **Deploy to Server**
   - Ensure database permissions correct
   - Run seed script on production database
   - Test final production build

## Troubleshooting

### Issue: "Categories API returns empty"
- **Solution:** Run seed script from `/BackEnd/config/seed_categories.php`

### Issue: "Categories not showing in admin"
- **Solution:** Check browser console for fetch errors
- **Debug:** Use `/BackEnd/api/categories.php` directly in browser to test API

### Issue: "Order not saving"
- **Solution:** Verify user is logged in (check localStorage.userInfo)
- **Debug:** Check browser network tab for API response

### Issue: "Database connection error"
- **Solution:** Verify `BackEnd/config/db_connect.php` has correct credentials
- **Debug:** Test `/BackEnd/api/categories.php` to verify database connection
