# 🚀 Setup Guide for ECOMMERCECORE1 (Core 1 - Customer Side)

This guide will help you set up the project on a new device using XAMPP.

---

## 📋 Prerequisites

1. **XAMPP** (Windows/Mac/Linux)
   - Download from: https://www.apachefriends.org/
   - Install with Apache and MySQL enabled

2. **PHP 7.4+** (usually included with XAMPP)

3. **MySQL/MariaDB** (included with XAMPP)

---

## 🔧 Step-by-Step Setup

### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Run the installer
3. Select components:
   - ✅ **Apache**
   - ✅ **MySQL**
   - ✅ **PHP**
   - ✅ **phpMyAdmin** (optional but recommended)
4. Choose installation directory (default: `C:\xampp` on Windows)
5. Complete the installation

### Step 2: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Click **Start** next to:
   - ✅ **Apache**
   - ✅ **MySQL**
3. Both should show green "Running" status

### Step 3: Copy Project Files

1. Copy the entire `ECOMMERCECORE1-main` folder to:
   ```
   C:\xampp\htdocs\ECOMMERCECORE1-main
   ```
   (or wherever your XAMPP `htdocs` folder is located)

2. Make sure the folder structure looks like:
   ```
   C:\xampp\htdocs\ECOMMERCECORE1-main\
   ├── Database\
   ├── Content\
   ├── Categories\
   ├── Shop\
   ├── Components\
   └── ... (other folders)
   ```

### Step 4: Create Database

1. Open your browser and go to:
   ```
   http://localhost/phpmyadmin
   ```

2. Click **"New"** (or **"Databases"** tab) on the left sidebar

3. Create a new database:
   - **Database name**: `core1_marketph`
   - **Collation**: `utf8mb4_general_ci` (or `utf8mb4_unicode_ci`)
   - Click **"Create"**

4. The database is now created (tables will be auto-created by the app)

### Step 5: Configure Database Connection

1. Open the file:
   ```
   Database\config.php
   ```

2. Verify these settings match your XAMPP setup:
   ```php
   $host = "localhost:3306";  // Default XAMPP MySQL port
   $user = "root";             // Default XAMPP MySQL user
   $password = "";              // Default XAMPP MySQL password (empty)
   $db = "core1_marketph";     // Database name you created
   ```

3. If your MySQL uses a different port (like 3307), change it:
   ```php
   $host = "localhost:3307";
   ```

### Step 6: Test the Setup

1. Open your browser and go to:
   ```
   http://localhost/ECOMMERCECORE1-main/
   ```

2. You should see the homepage of your e-commerce site

3. If you see errors:
   - **"Database connection failed"**: Check Step 5 (config.php)
   - **"404 Not Found"**: Check Step 3 (folder location)
   - **"Access Denied"**: Make sure Apache is running in XAMPP

### Step 7: Initialize Core 3 Products (Optional)

To sync products from Core 3:

1. Visit in your browser:
   ```
   http://localhost/ECOMMERCECORE1-main/Database/sync_core3_products.php
   ```

2. You should see output like:
   ```
   Starting Core 3 → Core 1 product sync...
   ✓ Ensured `products` table exists.
   ...
   Sync complete. Total products synced/updated: X.
   ```

---

## 🔗 Important URLs

After setup, you can access:

- **Homepage**: `http://localhost/ECOMMERCECORE1-main/`
- **Shop**: `http://localhost/ECOMMERCECORE1-main/Shop/`
- **Categories**: `http://localhost/ECOMMERCECORE1-main/Categories/`
- **phpMyAdmin**: `http://localhost/phpmyadmin`

---

## ⚙️ Configuration Files

### Database Configuration
- **File**: `Database/config.php`
- **Purpose**: Database connection settings

### Core 2 API URL (for order sending)
- **File**: `Database/send_order_to_core2.php`
- **Default URL**: `https://core2.imarketph.com/api_receive_order.php`
- **To change**: Edit line ~20 in `send_order_to_core2.php`:
  ```php
  $core2ApiUrl = 'YOUR_CORE2_API_URL_HERE';
  ```

---

## 🐛 Troubleshooting

### Problem: "Access Denied" or "Forbidden"
**Solution**: 
- Make sure Apache is running in XAMPP Control Panel
- Check folder permissions (Windows: right-click folder → Properties → Security)

### Problem: "Database connection failed"
**Solution**:
1. Make sure MySQL is running in XAMPP
2. Check `Database/config.php` settings
3. Verify database `core1_marketph` exists in phpMyAdmin

### Problem: "Port 80 already in use"
**Solution**:
1. Open XAMPP Control Panel
2. Click **Config** → **Apache (httpd.conf)**
3. Change `Listen 80` to `Listen 8080`
4. Restart Apache
5. Access site at: `http://localhost:8080/ECOMMERCECORE1-main/`

### Problem: "Port 3306 already in use"
**Solution**:
1. Change MySQL port in XAMPP Control Panel → Config → MySQL (my.ini)
2. Change `port=3306` to `port=3307`
3. Update `Database/config.php` to use port 3307
4. Restart MySQL

### Problem: Products not showing
**Solution**:
1. Run the sync script: `http://localhost/ECOMMERCECORE1-main/Database/sync_core3_products.php`
2. Check if `products` table exists in phpMyAdmin
3. Verify Core 3 API is accessible: `https://core3.imarketph.com/api_products_local.php`

---

## 📝 Next Steps

1. **Test Order Placement**:
   - Add products to cart
   - Go through checkout
   - Orders should be saved locally AND sent to Core 2

2. **Customize Core 2 API URL**:
   - Edit `Database/send_order_to_core2.php` if your Core 2 URL is different

3. **Set up Email (Optional)**:
   - Configure PHPMailer settings in `Content/Confirmation.php` (lines ~286-289)
   - For Gmail, you'll need an App Password

---

## ✅ Setup Checklist

- [ ] XAMPP installed and running
- [ ] Apache started
- [ ] MySQL started
- [ ] Project files copied to `htdocs`
- [ ] Database `core1_marketph` created
- [ ] `Database/config.php` configured correctly
- [ ] Homepage loads: `http://localhost/ECOMMERCECORE1-main/`
- [ ] Core 3 products synced (optional)
- [ ] Test order placement works

---

## 🆘 Need Help?

If you encounter issues:
1. Check XAMPP error logs: `C:\xampp\apache\logs\error.log`
2. Check PHP error logs: `C:\xampp\php\logs\php_error_log`
3. Enable PHP error display in `php.ini`:
   ```ini
   display_errors = On
   error_reporting = E_ALL
   ```

---

**Happy Coding! 🎉**
