<?php
// Centralized Product Data
// This replaces the need for 12+ separate files.
// Ideally, this should come from a database.

$products_data = [

    //Product 1 -> 601
    601 => [
        'name' => 'Facial Cleanser (Gentle Formula)',
        'price_range' => '₱240',
        'original_price' => '₱350',
        'discount' => '31% OFF',
        'image' => '../../image/beauty-health/Facial Cleanser (Gentle Formula).jpeg',
        'stock' => 1209,
        'colors' => ['Black', 'Grey', 'Blue'],
        'sizes' => ['S', 'M', 'L']
    ],

    //Product 2 -> 602
    602 => [
        'name' => 'Vitamin C Serum',
        'price_range' => '₱340',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/beauty-health/Vitamin C Serum.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 3 -> 603
    603 => [
        'name' => 'Aloe Vera Soothing Gel',
        'price_range' => '₱180',
        'original_price' => '₱250',
        'discount' => '28% OFF',
        'image' => '../../image/beauty-health/Aloe Vera Soothing Gel.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 4 -> 604
    604 => [
        'name' => 'Sunscreen SPF 50+',
        'price_range' => '₱450',
        'original_price' => '₱650',
        'discount' => '30% OFF',
        'image' => '../../image/beauty-health/Sunscreen SPF 50+.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 5 -> 605
    605 => [
        'name' => 'Moisturizing Face Cream',
        'price_range' => '₱320',
        'original_price' => '₱480',
        'discount' => '33% OFF',
        'image' => '../../image/beauty-health/Moisturizing Face Cream.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 6 -> 606 (As seen in User Image)
    606 => [
        'name' => 'Charcoal Face Mask',
        'price_range' => '₱10,200',
        'original_price' => '₱15,000',
        'discount' => '32% OFF',
        'image' => '../../image/beauty-health/Charcoal Face Mask.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 7 -> 607
    607 => [
        'name' => 'Hair Growth Shampoo',
        'price_range' => '₱290',
        'original_price' => '₱420',
        'discount' => '31% OFF',
        'image' => '../../image/beauty-health/Hair Growth Shampoo.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 8 -> 608
    608 => [
        'name' => 'Hair Conditioner (Repair Care)',
        'price_range' => '₱270',
        'original_price' => '₱380',
        'discount' => '29% OFF',
        'image' => '../../image/beauty-health/Hair Conditioner (Repair Care).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 9 -> 609
    609 => [
        'name' => 'Makeup Brush Set (10pcs)',
        'price_range' => '₱350',
        'original_price' => '₱550',
        'discount' => '36% OFF',
        'image' => '../../image/beauty-health/Makeup Brush Set (10pcs).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 10 -> 610
    610 => [
        'name' => 'Lip Tint - Lip Gloss Set',
        'price_range' => '₱190',
        'original_price' => '₱280',
        'discount' => '32% OFF',
        'image' => '../../image/beauty-health/Lip Tint - Lip Gloss Set.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],


    //Product 11 -> 611
    611 => [
        'name' => 'Body Scrub (Whitening & Exfoliating)',
        'price_range' => '₱220',
        'original_price' => '₱320',
        'discount' => '31% OFF',
        'image' => '../../image/beauty-health/Body Scrub (Whitening & Exfoliating).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 12 -> 612
    612 => [
        'name' => 'Electric Facial Cleanser Brush',
        'price_range' => '₱950',
        'original_price' => '₱1,500',
        'discount' => '37% OFF',
        'image' => '../../image/beauty-health/Electric Facial Cleanser Brush.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 13 -> 613
    613 => [
        'name' => 'Nail Care Kit (Manicure Set)',
        'price_range' => '₱120',
        'original_price' => '₱180',
        'discount' => '33% OFF',
        'image' => '../../image/beauty-health/Nail Care Kit (Manicure Set).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 14 -> 614
    614 => [
        'name' => 'Essential Oil Set (Relaxing Blend)',
        'price_range' => '₱380',
        'original_price' => '₱550',
        'discount' => '31% OFF',
        'image' => '../../image/beauty-health/Essential Oil Set (Relaxing Blend).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    615 => [
        'name' => 'Foot Spa Massager Roller',
        'price_range' => '₱850',
        'original_price' => '₱1,200',
        'discount' => '29% OFF',
        'image' => '../../image/beauty-health/Foot Spa Massager Roller.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],
];

    // Fix pricing data for consistency
    foreach ($products_data as $id => &$p) {
        if ($id == 601) continue; // Skip already correct one
        // Default logic: original price should be higher than current price
        if (isset($p['price_range']) && strpos($p['price_range'], '10,200') !== false) {
            $p['original_price'] = '₱15,000';
            $p['price_range'] = '₱10,200';
        } elseif ($id == 610) {
            $p['original_price'] = '₱75,000';
            $p['price_range'] = '₱59,000';
        } else {
            // Standard beauty product logic
            $raw_price = floatval(preg_replace('/[^0-9.]/', '', $p['price_range'] ?? '340'));
            if ($raw_price > 0 && !isset($p['original_price'])) {
                $p['original_price'] = '₱' . number_format($raw_price * 1.4);
            }
        }
    }
    unset($p);

// Get ID correctly
$p_id = isset($product_id) ? $product_id : 601;

// Refresh current product after data fix
$product = isset($products_data[$p_id]) ? $products_data[$p_id] : $products_data[601];
$price = $product['price_range'];
$name = isset($product['name']) ? $product['name'] : 'Product';
$img = isset($product['image']) ? str_replace(' ', '%20', $product['image']) : ''; 
?>

<link rel="stylesheet" href="../../css/components/shared-product-view.css?v=<?php echo time(); ?>">

<div class="pv-left">
    <img class="pv-product-img" src="<?php echo $img; ?>" alt="Product">
</div>
<div class="pv-right">
    <div class="pv-header">
        <div class="pv-header-title">
            <img src="../../image/logo.png" alt="IMarket" class="pv-header-logo"> |
            <span>IMarket Official Store</span>
        </div>
        <p class="pv-category">
            <?php echo isset($category_name) ? $category_name : "Beauty & Health"; ?>
        </p>
    </div>

    <h2 class="pv-title"><?php echo htmlspecialchars($name); ?></h2>
    
    <div class="pv-meta">
        <div class="pv-rating">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <span>1.5k Ratings</span>
        <span>|</span>
        <span>4.2k Sold</span>
    </div>

    <div class="pv-price-container">
        <?php if (isset($product['original_price'])): ?>
            <span class="pv-original-price"><?php echo $product['original_price']; ?></span>
        <?php endif; ?>
        <span class="pv-price"><?php echo $price; ?></span>
        <?php if (isset($product['discount'])): ?>
            <span class="pv-discount-badge"><?php echo $product['discount']; ?></span>
        <?php endif; ?>
    </div>

    <!-- Color Options -->
    <div class="pv-option-group">
        <span class="pv-option-label">Color</span>
        <div class="pv-options" id="color-options">
            <?php foreach ($product['colors'] as $index => $color): ?>
                <button class="pv-option-btn <?php echo $index === 0 ? 'selected' : ''; ?>" data-val="<?php echo $color; ?>">
                    <?php echo $color; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Size Options -->
    <div class="pv-option-group">
        <span class="pv-option-label">Size</span>
        <div class="pv-options" id="size-options">
            <?php foreach ($product['sizes'] as $index => $size): ?>
                <button class="pv-option-btn <?php echo $index === 0 ? 'selected' : ''; ?>" data-val="<?php echo $size; ?>"><?php echo $size; ?></button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Quantity -->
    <div class="pv-option-group">
        <span class="pv-option-label">Quantity</span>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div class="pv-quantity-control">
                <button class="pv-qty-btn" onclick="let q=document.getElementById('qty'); if(q.value>1)q.value--;">-</button>
                <input type="text" id="qty" class="pv-qty-input" value="1">
                <button class="pv-qty-btn" onclick="document.getElementById('qty').value++;">+</button>
            </div>
            <span style="font-size: 14px; color: #757575;"><?php echo number_format($product['stock']); ?> pieces available</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="pv-actions">
        <a class="pv-btn pv-btn-cart" href="#" onclick="addToCart()">
            <i class="fas fa-cart-plus" style="margin-right: 8px;"></i> Add To Cart
        </a>
        <a href="#"
            onclick="const qty = document.getElementById('qty').value; window.location.href='../../Content/Payment.php?product_name=<?php echo urlencode($name); ?>&price=<?php echo floatval(preg_replace('/[^0-9.]/', '', $price)); ?>&image=<?php echo urlencode($img); ?>&quantity=' + qty + '&product_id=<?php echo $p_id; ?>'; return false;"
            class="pv-btn pv-btn-buy">Buy Now</a>
    </div>

    <script>
        function addToCart() {
            const colorBtn = document.querySelector('#color-options .pv-option-btn.selected');
            const sizeBtn = document.querySelector('#size-options .pv-option-btn.selected');
            const color = colorBtn ? colorBtn.getAttribute('data-val') : 'Default';
            const size = sizeBtn ? sizeBtn.getAttribute('data-val') : 'Default';
            const qty = document.getElementById('qty').value;
            const fullName = `<?php echo addslashes($name); ?> (${color}, ${size})`;
            const price = <?php echo floatval(preg_replace('/[^0-9.]/', '', $price)); ?>;
            const img = '<?php echo $img; ?>';

            window.location.href = `../../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(fullName)}&price=${price}&image=${img}&quantity=${qty}&store=IMarket%20Beauty%20%26%20Health`;
        }

        document.querySelectorAll('.pv-options').forEach(container => {
            container.querySelectorAll('.pv-option-btn').forEach(button => {
                button.addEventListener('click', function () {
                    container.querySelectorAll('.pv-option-btn').forEach(btn => btn.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
        });
    </script>
</div>