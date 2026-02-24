# 📦 ORDER SYSTEM FLOW EXPLANATION

## 🎯 Overview
This document explains how the order system works in ECOMMERCECORE1, from adding products to cart through order confirmation and sending to Core 2 API.

---

## 🔄 Complete Order Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    ORDER FLOW DIAGRAM                           │
└─────────────────────────────────────────────────────────────────┘

1. PRODUCT PAGE
   ↓
2. ADD TO CART (add-to-cart.php)
   ↓
3. VIEW CART (add-to-cart.php)
   ↓
4. CHECKOUT (Check-out.php) → Select Items
   ↓
5. PAYMENT PAGE (Payment.php) → Enter Address & Payment Method
   ↓
6. CONFIRMATION (Confirmation.php) → Create Order & Send to Core 2
   ↓
7. CORE 2 API (send_order_to_core2.php) → Notify Seller
```

---

## 📁 FILES INVOLVED IN ORDER PROCESS

### 1. **Content/add-to-cart.php**
**Purpose**: Manages shopping cart operations

**What it does**:
- ✅ Handles "Add to Cart" requests from product pages
- ✅ Fetches product details from `products` table
- ✅ Stores items in `cart` table
- ✅ Updates quantity if item already exists
- ✅ Displays cart items with checkboxes
- ✅ Allows deleting items
- ✅ Calculates cart totals

**Key Functions**:
- Adds products to cart (lines 17-98)
- Updates/Deletes cart items (lines 87-123)
- Fetches cart items for display (lines 126-140)

**Database Tables Used**:
- `cart` - Stores cart items
- `products` - Fetches product details (name, price, image_url, shop_name)

---

### 2. **Content/Check-out.php**
**Purpose**: Review cart items before payment

**What it does**:
- ✅ Displays selected cart items
- ✅ Shows item details (name, price, quantity, image)
- ✅ Calculates subtotal
- ✅ Allows selecting items to checkout
- ✅ Redirects to Payment.php with selected item IDs

**Key Functions**:
- Fetches cart items (lines 59-71)
- JavaScript function `proceedToCheckout()` (lines 390-410)
- Redirects to Payment.php with `selected_ids` parameter

**Database Tables Used**:
- `cart` - Reads cart items

---

### 3. **Content/Payment.php**
**Purpose**: Payment and shipping information page

**What it does**:
- ✅ Handles both cart checkout AND single product "Buy Now"
- ✅ Fetches selected cart items OR single product data
- ✅ Loads user's saved address (from `user_addresses` table)
- ✅ Displays payment method options (COD, Online, GCash, etc.)
- ✅ Calculates shipping fee (₱50.00)
- ✅ Shows order summary
- ✅ Submits order data to Confirmation.php

**Key Functions**:
- Detects cart checkout vs single product (lines 38-80)
- Fetches user address (lines 94-120)
- Calculates totals (lines 87-89)
- Form submission to Confirmation.php (lines 790-813)

**Database Tables Used**:
- `cart` - Reads selected cart items
- `user_addresses` - Fetches saved shipping address
- `users` - Gets user information

**URL Parameters**:
- `from_cart=1&selected_ids=1,2,3` - Cart checkout
- `product_name=...&price=...&quantity=...` - Single product checkout

---

### 4. **Content/Confirmation.php** ⭐ **MOST IMPORTANT**
**Purpose**: Creates order and sends to Core 2 API

**What it does**:
- ✅ Receives order data from Payment.php
- ✅ Creates order records in `orders` table
- ✅ Handles multiple items (cart checkout) OR single item (Buy Now)
- ✅ Fetches shop_name from products table or cart table
- ✅ Generates tracking number (TRK-XXXXXX)
- ✅ Generates order reference (ORD-XXXXXX)
- ✅ **Sends order to Core 2 API** via `sendOrderToCore2()`
- ✅ Sends confirmation email to customer
- ✅ Deletes items from cart after successful order
- ✅ Displays order confirmation page

**Key Functions**:
- **Multiple Items Processing** (lines 55-140):
  - Loops through each cart item
  - Creates one order record per item
  - Groups items with same tracking number
  - Fetches shop_name for each item
  
- **Single Product Processing** (lines 142-191):
  - Creates one order record
  - Fetches shop_name from products table
  
- **Send to Core 2** (lines 196-220):
  - Prepares order data structure
  - Calls `sendOrderToCore2()` function
  - Handles errors gracefully (doesn't fail order if Core 2 is down)

**Database Tables Used**:
- `orders` - Creates order records
- `products` - Fetches shop_name
- `cart` - Reads shop_name, deletes ordered items
- `users` - Gets customer email for confirmation

**POST Data Received**:
```php
$_POST['action'] = 'complete_purchase'
$_POST['items'] = [
    ['product_id', 'product_name', 'price', 'quantity', 'image', 'cart_id'],
    ...
]
$_POST['full_name']
$_POST['phone_number']
$_POST['address']
$_POST['city']
$_POST['postal_code']
$_POST['payment_method']
$_POST['total_amount']
```

---

### 5. **Database/send_order_to_core2.php** ⭐ **CORE 2 INTEGRATION**
**Purpose**: Sends order data to Core 2 Seller Side API

**What it does**:
- ✅ Transforms Core 1 order format to Core 2 API format
- ✅ Sends ONE API request per product (Core 2 handles one product per request)
- ✅ Splits customer name into first_name and last_name
- ✅ Extracts seller_name/shop_name from order items
- ✅ Makes HTTP POST request to Core 2 API
- ✅ Returns success/failure status

**Function**: `sendOrderToCore2($conn, $orderData, $core2ApiUrl)`

**Parameters**:
- `$conn` - Database connection (can be null)
- `$orderData` - Order data array with items and customer info
- `$core2ApiUrl` - API endpoint URL (defaults to sellercenter.imarketph.com/api_order.php)

**Input Format** (from Confirmation.php):
```php
$orderData = [
    'order_id' => 123,
    'order_reference' => 'ORD-000123',
    'tracking_number' => 'TRK-ABC123',
    'items' => [
        [
            'product_id' => 456,
            'product_name' => 'Product Name',
            'quantity' => 2,
            'price' => 100.00,
            'shop_name' => 'Shop Name',
            'seller_name' => 'Shop Name'
        ],
        ...
    ],
    'customer' => [
        'full_name' => 'Juan Dela Cruz',
        'phone_number' => '09123456789',
        'address' => '123 Main St',
        'city' => 'Manila',
        'postal_code' => '1000'
    ],
    'payment_method' => 'cod'
]
```

**Output Format** (sent to Core 2 API):
```json
{
    "customer": {
        "id": null,
        "first_name": "Juan",
        "last_name": "Dela Cruz",
        "email": "customer@email.com",
        "phone": "09123456789"
    },
    "seller": "Shop Name",
    "product": {
        "name": "Product Name",
        "price": 100.00
    },
    "quantity": 2,
    "payment_method": "cod",
    "shipping_address": "123 Main St",
    "shipping_city": "Manila",
    "shipping_postal_code": "1000"
}
```

**API Endpoint**: `https://sellercenter.imarketph.com/api_order.php`

**Response Handling**:
- ✅ Checks HTTP status code (200/201 = success)
- ✅ Checks for cURL errors
- ✅ Validates JSON response
- ✅ Returns array with success status and responses

**Return Format**:
```php
[
    'success' => true/false,
    'message' => 'Order sent to Core 2 successfully',
    'responses' => [
        [
            'http_code' => 200,
            'curl_error' => null,
            'raw_response' => '...',
            'decoded_response' => [...],
            'payload' => [...]
        ],
        ...
    ]
]
```

---

## 🗄️ DATABASE TABLES

### **cart** Table
Stores shopping cart items
```sql
- id (PK)
- user_id
- product_id
- product_name
- price
- image
- quantity
- shop_name
- created_at
```

### **orders** Table
Stores completed orders
```sql
- id (PK)
- user_id
- tracking_number
- product_id
- product_name
- quantity
- price
- total_amount
- full_name
- phone_number
- address
- city
- postal_code
- payment_method
- status (Pending, Processing, Shipped, etc.)
- image_url
- created_at
```

### **products** Table
Stores product information
```sql
- id (PK)
- name
- price
- image_url
- shop_name (NEW - for Core 3 products)
- status
- external_core3_id (for Core 3 products)
- is_core3 (flag)
```

### **user_addresses** Table
Stores saved shipping addresses
```sql
- id (PK)
- user_id
- fullname
- phone
- address
- city
- zip
- is_default
```

---

## 🔀 ORDER FLOW SCENARIOS

### Scenario 1: Cart Checkout (Multiple Items)
```
1. User adds Product A to cart → add-to-cart.php
2. User adds Product B to cart → add-to-cart.php
3. User views cart → add-to-cart.php (displays both items)
4. User selects items and clicks "Checkout" → Check-out.php
5. User reviews items → Check-out.php
6. User clicks "Proceed to Payment" → Payment.php?from_cart=1&selected_ids=1,2
7. Payment.php fetches cart items with IDs 1 and 2
8. User enters address and selects payment method
9. User clicks "Place Order" → POST to Confirmation.php
10. Confirmation.php:
    - Creates Order 1 for Product A
    - Creates Order 2 for Product B
    - Both orders share same tracking number
    - Sends Order 1 to Core 2 API
    - Sends Order 2 to Core 2 API
    - Deletes cart items 1 and 2
    - Sends confirmation email
    - Shows confirmation page
```

### Scenario 2: Buy Now (Single Product)
```
1. User clicks "Buy Now" on Product Page
2. Redirects to Payment.php?product_name=...&price=...&quantity=...
3. Payment.php creates single item array
4. User enters address and selects payment method
5. User clicks "Place Order" → POST to Confirmation.php
6. Confirmation.php:
    - Creates one order record
    - Sends order to Core 2 API
    - Sends confirmation email
    - Shows confirmation page
```

---

## 🔧 KEY FEATURES

### 1. **Shop Name Handling**
- Fetches `shop_name` from `products` table (for Core 3 products)
- Falls back to `cart` table if not in products
- Defaults to "IMarket Official Store" if not found
- Required for Core 2 API to know which seller to notify

### 2. **Multiple Items Support**
- Each cart item becomes a separate order record
- All items share the same tracking number (grouped order)
- Each item is sent separately to Core 2 API (one request per product)

### 3. **Error Handling**
- Order creation doesn't fail if Core 2 API is down
- Errors are logged but order is still saved locally
- Customer still gets confirmation even if Core 2 fails

### 4. **Cart Cleanup**
- Cart items are deleted after successful order creation
- Only selected items are deleted (for cart checkout)
- Cart persists if order creation fails

---

## 📊 DATA FLOW EXAMPLE

### Example: Ordering 2 Products

**Step 1: Cart State**
```php
cart table:
- id: 1, product_id: 10, product_name: "Laptop", shop_name: "TechStore"
- id: 2, product_id: 20, product_name: "Mouse", shop_name: "TechStore"
```

**Step 2: Payment.php Receives**
```
GET: Payment.php?from_cart=1&selected_ids=1,2
```

**Step 3: Confirmation.php Creates Orders**
```php
orders table:
- id: 100, tracking_number: "TRK-ABC123", product_id: 10, product_name: "Laptop"
- id: 101, tracking_number: "TRK-ABC123", product_id: 20, product_name: "Mouse"
```

**Step 4: Send to Core 2**
```php
API Request 1:
POST to Core 2 API
{
    "seller": "TechStore",
    "product": {"name": "Laptop", "price": 1000},
    "quantity": 1,
    ...
}

API Request 2:
POST to Core 2 API
{
    "seller": "TechStore",
    "product": {"name": "Mouse", "price": 50},
    "quantity": 1,
    ...
}
```

**Step 5: Cart Cleanup**
```php
DELETE FROM cart WHERE id IN (1, 2) AND user_id = 123
```

---

## 🚨 IMPORTANT NOTES

1. **Core 2 API Requirement**: Each product requires a separate API call (Core 2 limitation)

2. **Shop Name is Critical**: Without `shop_name`, Core 2 API will receive "Unknown Seller"

3. **Order Status**: All orders start with status "Pending"

4. **Tracking Number**: Generated randomly (TRK-XXXXXX format)

5. **Order Reference**: Format is ORD-XXXXXX (padded with zeros)

6. **Email Confirmation**: Sent after order creation (uses PHPMailer)

---

## 🔍 DEBUGGING ORDER ISSUES

### Check if order was created:
```sql
SELECT * FROM orders WHERE user_id = YOUR_USER_ID ORDER BY created_at DESC;
```

### Check if order was sent to Core 2:
- Check error logs for "Core2 api_order.php" messages
- Check `sendOrderToCore2()` return value in Confirmation.php

### Check cart items:
```sql
SELECT * FROM cart WHERE user_id = YOUR_USER_ID;
```

### Check shop_name:
```sql
SELECT id, name, shop_name FROM products WHERE id = PRODUCT_ID;
```

---

## 📝 SUMMARY

**Order Flow**: Product → Cart → Checkout → Payment → Confirmation → Core 2 API

**Key Files**:
1. `add-to-cart.php` - Cart management
2. `Check-out.php` - Cart review
3. `Payment.php` - Payment & address entry
4. `Confirmation.php` - Order creation & Core 2 integration ⭐
5. `send_order_to_core2.php` - Core 2 API communication ⭐

**Database Tables**:
- `cart` - Shopping cart
- `orders` - Completed orders
- `products` - Product catalog
- `user_addresses` - Shipping addresses

**External Integration**:
- Core 2 API: `https://sellercenter.imarketph.com/api_order.php`

---

*Last Updated: Based on current codebase analysis*
