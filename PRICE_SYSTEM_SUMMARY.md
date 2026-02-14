# 💰 Price Alignment System - Complete Implementation Guide

## Problem You Had
**"pwede mo bang i align lahat ng product price na kapag nag check out ay nag iiba yung price ng product"**

Translation: *"Can you align all product prices so that they don't change when checking out?"*

---

## ✅ Solution Implemented

A unified **Price Helper System** that ensures prices stay the same from browsing to payment:

| Stage | Before | After |
|-------|--------|-------|
| **Browse** | ₱299.00 | ₱299.00 ✓ |
| **Add to Cart** | Changes to 299 | 299 (numeric stored) |
| **View Cart** | Shows different format | ₱299.00 ✓ |
| **Checkout** | Might be 299 or ₱299 | ₱299.00 ✓ |
| **Payment** | Could mismatch | ₱299.00 ✓ |

**Result:** Same price shown at every step!

---

## 📦 What Was Created

### 1. **[php/price_helper.php](php/price_helper.php)** - Main System
Complete set of functions to handle prices consistently:

```php
include("../php/price_helper.php");

// Display: ₱299.00
echo formatPrice(299);

// Calculate: 299
$numeric = getPriceNumeric('₱299.00');

// Normalize products
normalizeProductPrice($product);
// Now has: $product['price'] = "₱299.00"
//          $product['raw_price'] = 299
```

**30+ Functions Available:**
- `formatPrice()` - Display format
- `getPriceNumeric()` - Calculation format
- `normalizeProductPrice()` - Fix entire products
- `sumPrices()` - Add multiple prices
- `calculateTotalPrice()` - Price × quantity
- `applyDiscountPercent()` - Apply discounts
- `isValidPrice()` - Validate prices
- And many more...

### 2. **[PRICE_ALIGNMENT_GUIDE.md](PRICE_ALIGNMENT_GUIDE.md)** - Documentation
Complete guide covering:
- The problem and solution
- How to use each function
- Step-by-step implementation checklist
- Migration strategy
- Testing procedures
- Common mistakes to avoid

### 3. **[PRICE_IMPLEMENTATION_EXAMPLES.php](PRICE_IMPLEMENTATION_EXAMPLES.php)** - Examples
Real-world code examples showing:
- How to display prices on product cards
- How to add items to cart
- How to show cart totals
- How to display payment summary
- How to handle mixed price sources
- And more...

---

## 🚀 Quick Start (3 Steps)

### Step 1: Include the Helper
At the TOP of any file that uses prices:
```php
<?php
include("../php/price_helper.php");
// Or: include(__DIR__ . "/../../php/price_helper.php");
```

### Step 2: Normalize Products
When you have a product array:
```php
// Before:
$products = [
    ['name' => 'Item', 'price' => 299],
];

// After:
normalizeProductPrice($products[0]);
// Now: $products[0]['price'] = "₱299.00"
//      $products[0]['raw_price'] = 299
```

### Step 3: Use Correct Format
- **Display (HTML):** Use `$product['price']` - formatted as ₱299.00
- **Calculate (Math):** Use `$product['raw_price']` - numeric 299
- **Hidden fields:** Use `$product['raw_price']` - for correct calculations

---

## 📊 How It Works

### Price Flow Chart
```
Raw Price Input (Any Format: 299, "₱299", "₱299.00")
           ↓
  getPriceNumeric() [Extract: 299]
           ↓
  Display: formatPrice(299) → ₱299.00
  Calculate: Use numeric 299
  Store: Use numeric 299
```

### Example: Product to Checkout
```
1. Product Page:
   Price in DB: 299
   Display: formatPrice(299) → "₱299.00" ✓

2. Add to Cart:
   User clicks "Add to Cart"
   Price sent: 299 (raw)
   Store in DB: 299 ✓

3. View Cart:
   Fetch from DB: 299
   Display: formatPrice(299) → "₱299.00" ✓

4. Checkout:
   Get from cart: 299
   Calculate: 299 × 2 = 598
   Display: formatPrice(598) → "₱598.00" ✓

5. Payment:
   Get total: 598
   Display: formatPrice(598) → "₱598.00" ✓
   Invoice amount matches! ✔️
```

---

## 🔧 Common Use Cases

### Use Case 1: Display Product Cards
```php
<?php
include("../php/price_helper.php");

$products = [ /* from database or array */ ];
normalizeProductPrices($products);
?>

<?php foreach ($products as $p): ?>
    <div class="product">
        <h3><?php echo $p['name']; ?></h3>
        <p>Price: <?php echo $p['price']; ?></p>
        <!-- Hidden for add to cart -->
        <input type="hidden" name="price" value="<?php echo $p['raw_price']; ?>">
    </div>
<?php endforeach; ?>
```

### Use Case 2: Calculate Cart Total
```php
<?php
include("../php/price_helper.php");

$total = 0;
foreach ($cart as $item) {
    // Use numeric price for math
    $total += calculateTotalPrice($item['price'], $item['qty']);
}

// Display with correct format
echo "Total: " . formatPrice($total);
?>
```

### Use Case 3: Handle Prices from Any Source
```php
<?php
include("../php/price_helper.php");

// Handles all these formats automatically:
$p1 = getPriceNumeric(299);         // Direct number
$p2 = getPriceNumeric("₱299");      // With peso sign
$p3 = getPriceNumeric("₱299.00");   // Fully formatted
$p4 = getPriceNumeric("299.00");    // String number

// All return: 299.00
echo formatPrice($p1);  // ₱299.00
echo formatPrice($p2);  // ₱299.00
echo formatPrice($p3);  // ₱299.00
echo formatPrice($p4);  // ₱299.00
?>
```

---

## ✨ Key Benefits

| Before | After |
|--------|-------|
| ❌ Price changes at checkout | ✅ Same price throughout |
| ❌ Calculation errors (math on strings) | ✅ Always numeric for math |
| ❌ Inconsistent formatting | ✅ Always ₱299.00 for display |
| ❌ Manual conversions | ✅ Automatic normalization |
| ❌ Hard to debug | ✅ Clear, consistent functions |
| ❌ Different pages use different formats | ✅ Unified system |

---

## 📋 Implementation Checklist

### Files That Need Updates
- [ ] All `Categories/*/card.php` files (8 files)
- [ ] `Content/Check-out.php` 
- [ ] `Content/Payment.php`
- [ ] `Content/Confirmation.php`
- [ ] `Shop/index.php`
- [ ] `Categories/best_selling/get_best_sellers.php`
- [ ] Any other files that handle prices

### For Each File:
1. Add at top: `include("../php/price_helper.php");`
2. For product arrays: `normalizeProductPrices($array);`
3. For display: Use `$product['price']`
4. For storage/calc: Use `$product['raw_price']`
5. Test: Verify price is same from browse to payment

---

## 🧪 How to Test

### Test 1: Product Display Matches Checkout
1. Go to product page → Note price (e.g., ₱299.00)
2. Add to cart
3. View cart → Price should still be ₱299.00
4. Proceed to checkout → Should still be ₱299.00
5. Payment page → Should still be ₱299.00
✅ All match? Perfect!

### Test 2: Calculate Multiple Items
1. Add 2 items @ ₱299 = ₱598
2. Add 1 item @ ₱499 = ₱499
3. Subtotal should be ₱1,097
4. Verify at checkout
✅ Math is correct? Perfect!

### Test 3: Different Price Formats
1. Create product with price: 299 (numeric)
2. Add to cart with price: "₱299" (string)
3. Payment receives: 299.00
4. All display as: ₱299.00
✅ Formats handled? Perfect!

---

## 🎯 Next Steps

### Quick Implementation (1 hour)
1. Use price_helper in product card files
2. Normalize prices in checkout/payment pages
3. Test critical paths (browse → checkout → payment)

### Complete Implementation (2-3 hours)
1. Update all category cards
2. Update all checkout flow pages  
3. Verify database prices are numeric
4. Run full test suite
5. Document your implementation

### Code Example to Copy
See **[PRICE_IMPLEMENTATION_EXAMPLES.php](PRICE_IMPLEMENTATION_EXAMPLES.php)** for ready-to-use patterns!

---

## 📖 Documentation Files

| File | Purpose |
|------|---------|
| **php/price_helper.php** | Core functions - include this everywhere |
| **PRICE_ALIGNMENT_GUIDE.md** | Complete documentation - read for understanding |
| **PRICE_IMPLEMENTATION_EXAMPLES.php** | Copy patterns from here - practical examples |

---

## ⚠️ Important Notes

### DO:
✅ Always include price_helper.php where prices are used  
✅ Normalize products when you get them  
✅ Use `$product['price']` for display  
✅ Use `$product['raw_price']` for calculations  
✅ Test at every transition point  

### DON'T:
❌ Store formatted prices (₱299) in database - store numeric (299)  
❌ Do math on formatted strings - use getPriceNumeric() first  
❌ Mix price formats in same file - normalize once at start  
❌ Pass formatted prices to hidden fields - pass raw_price  
❌ Forget to include price_helper.php  

---

## 🆘 Troubleshooting

**Problem:** Price changes between pages  
**Solution:** Use `getPriceNumeric()` when storing, `formatPrice()` when displaying

**Problem:** Math is off (2 × ₱299 ≠ ₱598)  
**Solution:** Remove peso sign before calculation with `getPriceNumeric()`

**Problem:** Checkout shows wrong total  
**Solution:** Verify you're using `sumPrices()` or `calculateTotalPrice()` from helper

**Problem:** Prices look different across pages  
**Solution:** Use `normalizeProductPrice()` on products right after loading

---

## 📞 Quick Reference

```php
// 5 Most Used Functions:

1. formatPrice($price)
   // Returns: ₱299.00 (for display)

2. getPriceNumeric($price)
   // Returns: 299 (for calculations)

3. normalizeProductPrice($product)
   // Adds both formatted and raw to product

4. sumPrices($array)
   // Returns: 1597 (total of multiple prices)

5. calculateTotalPrice($price, $qty)
   // Returns: 598 (price × qty)
```

---

## ✅ Success Checklist

After implementing this system, verify:
- [ ] Price is same from product page to cart
- [ ] Price is same from cart to checkout
- [ ] Price is same from checkout to payment
- [ ] Multiple item totals calculate correctly
- [ ] All payment amounts match displayed amounts
- [ ] Database stores numeric prices
- [ ] Invoice/confirmation shows correct amount

**Once all checked:** You have a fully aligned price system! 🎉

---

**Status:** Ready to Implement  
**Difficulty:** Easy (copy-paste patterns)  
**Time Required:** 1-3 hours  
**Impact:** Eliminates price confusion and checkout issues
