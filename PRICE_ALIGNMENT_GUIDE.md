# Price Consistency & Alignment System Guide

## 📋 Problem Statement

Currently, product prices are inconsistent across the platform:

| Location | Format | Issue |
|----------|--------|-------|
| Product Cards | `'₱299.00'` | Formatted string |
| Shop Grid | `'₱' . number_format($price)` or `raw_price` | Mixed formats |
| Checkout | Raw numeric from cart | Stripped of formatting |
| Cart Display | Database value | May differ from display |
| Payment | May not match cart | Price mismatch at checkout |

**Result:** Prices change between viewing and checkout ❌

---

## ✅ Solution: Unified Price System

All prices now use **PHP Price Helper** (`php/price_helper.php`)

### Core Principle
- **Display Price:** Always formatted as `₱299.00`  
- **Calculation Price:** Always numeric `299.00`  
- **Data Attributes:** Both formats available in HTML
- **Checkout:** Uses same price function as display

---

## 🔧 How to Use

### 1. Include Price Helper (wherever prices are used)
```php
include("../php/price_helper.php");

// Or in nested paths:
include(__DIR__ . "/../../php/price_helper.php");
```

### 2. Display Price on Product Card
```php
<?php
$product = ['price' => 299, 'name' => 'Product'];
normalizeProductPrice($product); // Ensures consistent format
?>

<!-- Display price -->
<div class="product-price"><?php echo $product['price']; ?></div>
<!-- Displays: ₱299.00 -->

<!-- Use raw price in data attribute -->
<div data-price="<?php echo $product['raw_price']; ?>">
<!-- Ensures calculations work correctly -->
```

### 3. Add to Cart (with consistent pricing)
```php
// When adding item to cart, use raw price for storage
$price = getPriceNumeric($_POST['price']); // 299

// Store in database
mysqli_query($conn, "INSERT INTO cart (user_id, product_id, price, ...) 
                     VALUES ('$user_id', '$pid', '$price', ...)");
```

### 4. Display Cart Items (with correct prices)
```php
<?php
while ($item = mysqli_fetch_assoc($cart_result)) {
    // Normalize the price from database
    $price = formatPrice($item['price']); // Converts to ₱299.00
    $numeric = getPriceNumeric($item['price']); // Gets 299.00
?>
    <div class="cart-item">
        <span>Price: <?php echo $price; ?></span>
        <input type="hidden" name="price" value="<?php echo $numeric; ?>">
    </div>
<?php }
```

### 5. Calculate Totals  
```php
// Get cart items
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate subtotal
$subtotal = sumPrices(array_column($items, 'price'));

// Display
echo "Subtotal: " . formatPrice($subtotal);
```

---

## 📝 Available Functions

### Display & Formatting
```php
formatPrice($price)              // "₱299.00"
formatPrice($price, false)       // "₱299" (no decimals)
getDisplayPrice($price)          // Same as formatPrice()
```

### Calculations
```php
getPriceNumeric($price)          // 299.00 (pure number)
getCalculationPrice($price)      // 299.00 (same)
getPriceNumeric('₱299.50')       // 299.50 (strips peso sign)
```

### Multiple Prices
```php
sumPrices([100, 200, 150])       // 450
sumPrices(['₱100', '₱200'])      // 300
formatTotalPrice([100, 200])     // "₱300.00"
```

### With Quantity
```php
calculateTotalPrice(299, 2)      // 598.00 (299 × 2)
calculateTotalPrice('₱299', 3)   // 897.00
```

### Normalization
```php
$norm = normalizePrice('₱299.50');
// Returns:
// [
//   'raw' => 299.50,
//   'formatted' => '₱299.50',
//   'raw_var' => 299.50
// ]

// Or entire product:
normalizeProductPrice($product_array);
// Ensures product has: $product['price'] (formatted) 
//                      $product['raw_price'] (numeric)
```

### Discounts
```php
$discount = applyDiscountPercent(299, 20);
// Returns: [
//   'original' => 299,
//   'original_formatted' => '₱299.00',
//   'discount_percent' => 20,
//   'discount_amount' => 59.8,
//   'final' => 239.2,
//   'final_formatted' => '₱239.20'
// ]
```

### Validation
```php
isValidPrice('₱299')             // true
isValidPrice(0)                  // false
isValidPrice(1000000)            // false
pricesEqual('₱299', 299.00)      // true
```

---

## 🎯 Implementation Checklist

### Files That Need Updates

#### 1. **Product Card Files** (Category cards)
- [ ] `Categories/beauty-health/card.php`
- [ ] `Categories/fashion-apparel/card.php`
- [ ] `Categories/electronics/card.php`
- [ ] `Categories/groceries/card.php`
- [ ] `Categories/home-living/card.php`
- [ ] `Categories/sports-outdoor/card.php`
- [ ] `Categories/toys-games/card.php`
- [ ] `Categories/new-arrivals/card.php`
- [ ] `Categories/best_selling/card.php`

**Required Change:**
```php
<?php
include("../../../php/price_helper.php");

$products = [ /* ... */ ];
normalizeProductPrices($products);
?>

<!-- Now use: -->
<div class="product-price"><?php echo $product['price']; ?></div>
<div data-raw-price="<?php echo $product['raw_price']; ?>">
```

#### 2. **Checkout/Cart Files**
- [ ] `Content/Check-out.php` - Ensure prices from database are normalized
- [ ] `Content/Payment.php` - Use price_helper for all calculations
- [ ] `Content/Buy-now.php` - Normalize passed prices
- [ ] `Content/Confirmation.php` - Use same pricing logic

**Required Change:**
```php
<?php
include("../php/price_helper.php");

// Get items
while ($row = mysqli_fetch_assoc($result)) {
    $row['price_display'] = formatPrice($row['price']);
    $row['price_numeric'] = getPriceNumeric($row['price']);
    // Use these normalized values
}
```

#### 3. **Shop & Browse Pages**
- [ ] `Shop/index.php` - Normalize mock products
- [ ] `Categories/best_selling/get_best_sellers.php` - Ensure consistent pricing

**Required Change:**
```php
<?php
include("../../php/price_helper.php");

// Before returning products:
normalizeProductPrices($products);

// Or during creation:
$product['price'] = formatPrice($calculated_price);
$product['raw_price'] = getPriceNumeric($calculated_price);
```

---

## 🔄 Migration Strategy

### Step 1: Update Product Card Templates
All category card.php files should normalize prices ONCE:

```php
<?php
// At TOP of file
include("../../../php/price_helper.php");

$products = [
    ['name' => 'Item', 'price' => 299],
    // ...
];

// Normalize ALL prices
normalizeProductPrices($products);
?>

<!-- Then in HTML: -->
<div data-price="<?php echo $product['price']; ?>"
     data-raw-price="<?php echo $product['raw_price']; ?>"
>
```

### Step 2: Update JavaScript Add-to-Cart
Ensure add-to-cart button passes correct price:

```javascript
// OLD: 
const price = this.dataset.price; // "₱299.00" ❌ String with peso

// NEW:
const price = this.dataset.rawPrice; // 299 ✅ Clean numeric

// Or if price is formatted:
const price = getPriceNumeric(this.dataset.price); // Use helper
```

### Step 3: Update Cart Submission
When adding to cart, always send numeric price:

```php
// In cart handler
$price = getPriceNumeric($_POST['price']);  // Ensures numeric
$quantity = intval($_POST['quantity']);
$total = calculateTotalPrice($price, $quantity);

// Store normalized values
mysqli_query($conn, "INSERT INTO cart (...) VALUES ('$price', ...)");
```

### Step 4: Update Checkout Display
Display prices using consistent function:

```php
<?php
include("../php/price_helper.php");

// Get cart items
$items = mysqli_fetch_all($cart_query, MYSQLI_ASSOC);

$subtotal = 0;
foreach ($items as $item) {
    $price = formatPrice($item['price']); // Always formatted for display
    $subtotal += getPriceNumeric($item['price']); // Accumulate numeric
?>
    <div class="cart-row">
        <td><?php echo $price; ?></td>
    </div>
<?php } 

// Display total
echo "Total: " . formatPrice($subtotal);
?>
```

---

## 🧪 Testing Price Consistency

### Test 1: Product Display → Checkout
1. Browse product: Shows **₱299.00**
2. Add to cart: Price passes as `299`
3. View cart: Shows **₱299.00**
4. Checkout: Still **₱299.00**
5. Payment: **₱299.00**
✅ Should match throughout

### Test 2: Multiple Items
1. Item 1: ₱299 × 2 = ₱598
2. Item 2: ₱499 × 1 = ₱499
3. Total: ₱598 + ₱499 = **₱1,097.00**
✅ Should match at every step

### Test 3: Format Handling
```php
// These should all equal the same:
getPriceNumeric('₱299.50')     // 299.5
getPriceNumeric(299.50)        // 299.5
getPriceNumeric('299.50')      // 299.5
getPriceNumeric('299')         // 299

// All display as:
formatPrice(299.50)            // ₱299.50
```

---

## ⚠️ Common Mistakes to Avoid

### ❌ DON'T: Store formatted prices in calculations
```php
$total = 299.00 + "₱150.00"; // Wrong! Mixed types
```

### ✅ DO: Always convert to numeric
```php
$total = 299.00 + getPriceNumeric("₱150.00"); // Correct
```

### ❌ DON'T: Pass formatted price to cart
```php
$_POST['price'] = '₱299.00'; // Will cause issues
```

### ✅ DO: Pass numeric price
```php
$_POST['price'] = 299; // Or normalize it
$price = getPriceNumeric($_POST['price']);
```

### ❌ DON'T: Mix price sources
```php
// Cart from database price, Product from static array
// May have different formats
```

### ✅ DO: Normalize all prices consistently
```php
normalizeProductPrice($product_from_any_source);
// Now price is always: formatted in $product['price']
//                     numeric in $product['raw_price']
```

---

## 🚀 Quick Start Template

Use this template in files that handle prices:

```php
<?php
// 1. INCLUDE PRICE HELPER
include("../php/price_helper.php");

// 2. NORMALIZE ALL PRICES (product arrays)
$products = get_products_from_db();
normalizeProductPrices($products);

// 3. FOR CALCULATIONS, USE NUMERIC
$total = sumPrices(array_column($products, 'raw_price'));

// 4. FOR DISPLAY, USE FORMATTED
foreach ($products as $p) {
    echo "Display: " . $p['price'];           // ₱299.00
    echo "Hidden: " . $p['raw_price'];       // 299
}

// 5. IN HTML
?>
<div data-price="<?php echo $product['raw_price']; ?>">
    <span class="price"><?php echo $product['price']; ?></span>
</div>
```

---

## 📊 Price Consistency Matrix

| Action | Input | Process | Output | Usage |
|--------|-------|---------|--------|-------|
| Display | Any | `formatPrice()` | ₱299.00 | HTML rendering |
| Calculate | Any | `getPriceNumeric()` | 299 | Math operations |
| Store | Any | `getPriceNumeric()` | 299 | Database, hidden fields |
| Verify | Any | `isValidPrice()` | bool | Input validation |
| Normalize | Product array | `normalizeProductPrice()` | Consistent pair | All data structures |

---

## 💾 Database Considerations

### Storing Prices
```sql
-- Store raw numeric values, NOT formatted strings
CREATE TABLE products (
  id INT,
  name VARCHAR(255),
  price DECIMAL(10, 2),  -- Store as: 299.00 (numeric)
  -- NOT as: '₱299.00' (string)
);

-- When storing prices from forms:
$price = getPriceNumeric($_POST['price']); // Always gets numeric
INSERT INTO products (price) VALUES ($price);
```

### Retrieving Prices
```php
// Get from DB (always numeric)
$product = mysqli_fetch_assoc($result);
// $product['price'] is numeric like: 299.00

// Format for display
$display_price = formatPrice($product['price']); // ₱299.00

// Use numeric for calculations
$total = $product['price'] * $quantity; // 299 * 2 = 598
```

---

## ✨ Benefits

After implementation:
- ✅ **No Price Mismatches** - Same price from browse to checkout
- ✅ **No Math Errors** - All calculations use numeric values
- ✅ **Consistent Display** - Always formatted as ₱299.00
- ✅ **Easier Maintenance** - Change format in one place
- ✅ **Flexible Input** - Accept prices in any format
- ✅ **Fewer Bugs** - Validation ensures valid prices
- ✅ **Better Performance** - No string conversions in loops

---

## 📞 Support & Questions

For issues with price consistency:
1. Verify file includes `php/price_helper.php`
2. Check that products are normalized via `normalizeProductPrice()`
3. Use `getPriceNumeric()` for all calculations
4. Use `formatPrice()` for all display
5. Validate with `isValidPrice()` before storing

---

**Last Updated:** February 13, 2026  
**Status:** Ready for Implementation  
**Files Affected:** 15+ files across product, cart, and checkout modules
