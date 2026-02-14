<?php
/**
 * PRICE ALIGNMENT EXAMPLE IMPLEMENTATION
 * 
 * Shows how to use the price helper system across different pages
 * Copy patterns from here to update your product pages
 */

// ============================================================
// EXAMPLE 1: Product Card Display (Categories/category/card.php)
// ============================================================

echo "<!-- EXAMPLE 1: Product Card -->\n";

// 1. Include price helper at TOP of file
include("../../../php/price_helper.php");

// 2. Your product array
$products = [
    [
        'name' => 'Beautiful T-Shirt',
        'price' => 299,       // Can be numeric or ₱299.00 or "299"
        'image' => 'image.jpg',
        'link' => 'view.php?id=1'
    ],
    [
        'name' => 'Cool Pants',
        'price' => '₱499.00', // Different format
        'image' => 'image.jpg',
        'link' => 'view.php?id=2'
    ]
];

// 3. NORMALIZE all prices (does this once)
normalizeProductPrices($products);

// Now all products have:
// - $product['price'] = "₱299.00" (formatted for display)
// - $product['raw_price'] = 299 (numeric for hidden fields)

// 4. In your HTML loop:
foreach ($products as $product) {
    echo "
    <div class='product-card' 
         data-name='" . htmlspecialchars($product['name']) . "'
         data-price='" . $product['price'] . "'
         data-raw-price='" . $product['raw_price'] . "'>
        <img src='" . $product['image'] . "'>
        <h3>" . $product['name'] . "</h3>
        <span class='price'>" . $product['price'] . "</span>
    </div>
    ";
}

echo "\n<!-- Each product displayed as: ₱299.00 -->\n";

// ============================================================
// EXAMPLE 2: Add to Cart (Content/add-to-cart.php or similar)
// ============================================================

echo "\n<!-- EXAMPLE 2: Add to Cart -->\n";

include("../php/price_helper.php");

// Simulate form submission
$_POST = [
    'product_id' => 1,
    'product_name' => 'T-Shirt',
    'price' => '₱299.00',  // Might come formatted from JS
    'quantity' => 2,
    'user_id' => 5
];

// Get the price (handles any format)
$price = getPriceNumeric($_POST['price']); // Returns: 299
$quantity = intval($_POST['quantity']);
$total = calculateTotalPrice($price, $quantity); // 299 × 2 = 598

// Store in cart (database)
$sql = "INSERT INTO cart (user_id, product_id, product_name, price, quantity, total_price, created_at)
        VALUES (
            '{$_POST['user_id']}',
            '{$_POST['product_id']}',
            '" . htmlspecialchars($_POST['product_name']) . "',
            '$price',
            '$quantity',
            '$total',
            NOW()
        )";

echo "<!-- Stored in DB: price=$price, total=$total (both numeric) -->\n";

// ============================================================
// EXAMPLE 3: Display Cart Items (Content/Check-out.php)
// ============================================================

echo "\n<!-- EXAMPLE 3: Checkout Cart Display -->\n";

include("../php/price_helper.php");

// Simulate getting cart from DB
$cart_items = [
    ['product_name' => 'T-Shirt', 'price' => 299, 'quantity' => 2],
    ['product_name' => 'Pants', 'price' => 499, 'quantity' => 1],
    ['product_name' => 'Shoes', 'price' => 799, 'quantity' => 1]
];

echo "<table class='cart-table'>\n";
echo "  <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr>\n";

$subtotal = 0;

foreach ($cart_items as $item) {
    // Get numeric price for calculations
    $price = getCalculationPrice($item['price']);
    
    // Calculate item total
    $item_total = calculateTotalPrice($price, $item['quantity']);
    
    // Add to subtotal
    $subtotal += $item_total;
    
    // Format for display
    $price_display = formatPrice($price);
    $total_display = formatPrice($item_total);
    
    echo "  <tr>\n";
    echo "    <td>" . $item['product_name'] . "</td>\n";
    echo "    <td>$price_display</td>\n";
    echo "    <td>" . $item['quantity'] . "</td>\n";
    echo "    <td>$total_display</td>\n";
    echo "  </tr>\n";
}

echo "</table>\n";
echo "<p>Subtotal: " . formatPrice($subtotal) . "</p>\n";

// ============================================================
// EXAMPLE 4: Payment Page (Content/Payment.php)
// ============================================================

echo "\n<!-- EXAMPLE 4: Payment Page -->\n";

include("../php/price_helper.php");

// Get cart total from previous example
$subtotal = 1596;  // (299×2) + 499 + 799
$shipping_fee = 50;

// Calculate final total
$total_payment = calculateTotalPrice($subtotal) + $shipping_fee;

// BUT: Display using formatted prices
echo "
<div class='payment-summary'>
    <div class='summary-row'>
        <span>Subtotal (VAT Included)</span>
        <span>" . formatPrice($subtotal) . "</span>
    </div>
    <div class='summary-row'>
        <span>Shipping Fee</span>
        <span>" . formatPrice($shipping_fee) . "</span>
    </div>
    <div class='summary-row total'>
        <span>Total Payment</span>
        <span>" . formatPrice($total_payment) . "</span>
    </div>
</div>
";

// Store total as numeric in hidden field
echo "
<input type='hidden' name='total_amount' value='$total_payment'>
";

// ============================================================
// EXAMPLE 5: Normalize and Display Prices (Mixed sources)
// ============================================================

echo "\n<!-- EXAMPLE 5: Normalize Mixed Price Sources -->\n";

include("../php/price_helper.php");

// Products from different sources - different formats
$products_mixed = [
    // From database (numeric)
    ['id' => 1, 'price' => 299, 'source' => 'DB'],
    
    // From API (formatted)
    ['id' => 2, 'price' => '₱399.00', 'source' => 'API'],
    
    // From array (string)
    ['id' => 3, 'price' => '499', 'source' => 'Array']
];

// Normalize ALL to consistent format
normalizeProductPrices($products_mixed);

echo "After normalization:\n";
foreach ($products_mixed as $p) {
    echo "  ID {$p['id']}: price='" . $p['price'] . "' raw_price='" . $p['raw_price'] . "'\n";
}

// All now show the same:
// ID 1: price='₱299.00' raw_price='299'
// ID 2: price='₱399.00' raw_price='399'
// ID 3: price='₱499.00' raw_price='499'

// ============================================================
// EXAMPLE 6: Different Scenarios
// ============================================================

echo "\n<!-- EXAMPLE 6: Different Scenarios -->\n";

include("../php/price_helper.php");

// Scenario 1: Single item purchase
$price = getPriceNumeric('₱299.50');
$qty = 1;
$total = calculateTotalPrice($price, $qty);
echo "Scenario 1: $total items @ formatPrice = " . formatPrice($total) . "\n";

// Scenario 2: Multiple items in cart
$prices = ['₱299', '₱499', '₱199.99'];
$cart_total = sumPrices($prices);
echo "Scenario 2: Cart total = " . formatPrice($cart_total) . "\n";

// Scenario 3: With discount
$original = 499;
$discount_calc = applyDiscountPercent($original, 20);
echo "Scenario 3: " . $discount_calc['original_formatted'] . " - 20% = " . $discount_calc['final_formatted'] . "\n";

// Scenario 4: Price validation before storing
$user_input = '₱299.99';
if (isValidPrice($user_input)) {
    $clean_price = getPriceNumeric($user_input);
    echo "Scenario 4: Valid price - storing as $clean_price\n";
} else {
    echo "Scenario 4: Invalid price - rejecting\n";
}

// ============================================================
// EXAMPLE 7: Building HTML Data Attributes
// ============================================================

echo "\n<!-- EXAMPLE 7: HTML Data Attributes -->\n";

include("../php/price_helper.php");

$product = ['name' => 'T-Shirt', 'price' => 299.50];

// Old way (inconsistent):
echo "<div data-price='₱299.50' data-raw='299'> <!-- Inconsistent formatting -->\n";

// New way (consistent):
echo "<div " . buildPriceDataAttributes(299.50) . ">\n";
// Outputs: data-price="₱299.50" data-raw-price="299.50" data-price-numeric="299.50"

?>

<!-- END OF EXAMPLES -->

<hr>

<h2>How to Use These Examples</h2>
<ol>
    <li>Include price_helper.php at the top of your file</li>
    <li>Copy the pattern that matches your use case</li>
    <li>Replace sample data with your actual data</li>
    <li>Test to ensure prices match at each step (display → cart → checkout → payment)</li>
</ol>

<h2>Key Patterns to Remember</h2>
<ul>
    <li><strong>For Display:</strong> Use formatPrice() → ₱299.00</li>
    <li><strong>For Calculations:</strong> Use getPriceNumeric() → 299</li>
    <li><strong>For Storage:</strong> Use getPriceNumeric() → 299</li>
    <li><strong>For Normalization:</strong> Use normalizeProductPrice() → both formats</li>
    <li><strong>For Validation:</strong> Use isValidPrice() → true/false</li>
</ul>
