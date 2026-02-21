# ⚡ Quick Start Guide

## 🎯 Fast Setup (5 minutes)

### 1. Start XAMPP
- Open **XAMPP Control Panel**
- Click **Start** for **Apache** and **MySQL**

### 2. Create Database
- Go to: `http://localhost/phpmyadmin`
- Click **"New"** → Database name: `core1_marketph` → **Create**

### 3. Access Your Site
- Open browser: `http://localhost/ECOMMERCECORE1-main/`
- ✅ Done!

---

## 🔧 If Something Doesn't Work

### Database Error?
1. Check `Database/config.php`:
   ```php
   $host = "localhost:3306";
   $user = "root";
   $password = "";
   $db = "core1_marketph";
   ```

### Port Already in Use?
- **Port 80**: Change Apache port to 8080 in XAMPP Config
- **Port 3306**: Change MySQL port to 3307 in XAMPP Config

### Can't Access Site?
- Make sure folder is in: `C:\xampp\htdocs\ECOMMERCECORE1-main\`
- Check Apache is running (green in XAMPP)

---

## 📦 Sync Core 3 Products

Visit: `http://localhost/ECOMMERCECORE1-main/Database/sync_core3_products.php`

---

## 🛒 Test Order Flow

1. Add product to cart
2. Go to checkout
3. Place order
4. Order is saved locally AND sent to Core 2 automatically

---

## 🔗 Core 2 API URL

Default: `https://core2.imarketph.com/api_receive_order.php`

To change: Edit `Database/send_order_to_core2.php` (line ~20)

---

**For detailed setup, see `SETUP_GUIDE.md`**
