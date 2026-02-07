<?php
// Centralized Product Data
// This replaces the need for 12+ separate files.
// Ideally, this should come from a database.

$products_data = [

    //Product 1
    301 => [
        'name' => 'Wireless Bluetooth Earbuds',
        'price_range' => '₱1,250',
        'original_price' => '₱1,850',
        'discount' => '32% OFF',
        'image' => '../../image/electronics/Wireless Bluetooth Earbuds.jpeg',
        'stock' => 1209,
        'colors' => ['Black', 'Grey', 'Blue'],
        'sizes' => ['S', 'M', 'L']
    ],

    //Product 2
    302 => [
        'name' => 'Smart Watch (Fitness Tracker)',
        'price_range' => '₱2,450',
        'original_price' => '₱3,600',
        'discount' => '32% OFF',
        'image' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 3
    303 => [
        'name' => 'Portable Power Bank 20,000mAh',
        'price_range' => '₱1,150',
        'original_price' => '₱1,650',
        'discount' => '30% OFF',
        'image' => '../../image/electronics/Portable Power Bank 20,000mAh.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 4
    304 => [
        'name' => 'Bluetooth Speaker (Waterproof)',
        'price_range' => '₱1,500',
        'original_price' => '₱2,100',
        'discount' => '28% OFF',
        'image' => '../../image/electronics/Bluetooth Speaker (Waterproof).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 5
    305 => [
        'name' => 'USB-C Fast Charging Cable',
        'price_range' => '₱250',
        'original_price' => '₱450',
        'discount' => '44% OFF',
        'image' => '../../image/electronics/USB-C Fast Charging Cable.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 6
    306 => [
        'name' => 'High-End Gaming Mouse',
        'price_range' => '₱3,200',
        'original_price' => '₱4,500',
        'discount' => '29% OFF',
        'image' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'RGB'],
        'sizes' => ['Standard']
    ],

    //Product 7
    307 => [
        'name' => 'Noise Cancelling Headphones',
        'price_range' => '₱8,990',
        'original_price' => '₱12,500',
        'discount' => '28% OFF',
        'image' => '../../image/electronics/Noise_Cancelling_Headphones.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Silver'],
        'sizes' => ['Standard']
    ],

    //Product 8
    308 => [
        'name' => 'Mini WiFi Router / Pocket WiFi',
        'price_range' => '₱1,850',
        'original_price' => '₱2,600',
        'discount' => '29% OFF',
        'image' => '../../image/electronics/Mini WiFi Router Pocket WiFi.jpeg',
        'stock' => 500,
        'colors' => ['White', 'Black'],
        'sizes' => ['Standard']
    ],

    //Product 9
    309 => [
        'name' => 'Smart LED Light Bulb (WiFi Controlled)',
        'price_range' => '₱450',
        'original_price' => '₱750',
        'discount' => '40% OFF',
        'image' => '../../image/electronics/Smart LED Light Bulb (WiFi Controlled).jpeg',
        'stock' => 500,
        'colors' => ['RGB'],
        'sizes' => ['Standard']
    ],

    //Product 10
    310 => [
        'name' => 'Laptop Cooling Pad (RGB Fan)',
        'price_range' => '₱1,200',
        'original_price' => '₱1,800',
        'discount' => '33% OFF',
        'image' => '../../image/electronics/Laptop Cooling Pad RGB Fan.jpeg',
        'stock' => 500,
        'colors' => ['Black'],
        'sizes' => ['Standard']
    ],


    //Product 11
    311 => [
        'name' => '1080p HD Web Camera',
        'price_range' => '₱1,450',
        'original_price' => '₱2,200',
        'discount' => '34% OFF',
        'image' => '../../image/electronics/1080p HD Web Camera.jpeg',
        'stock' => 500,
        'colors' => ['Black'],
        'sizes' => ['Standard']
    ],

    //Product 12
    312 => [
        'name' => 'Smart Plug (App Controlled)',
        'price_range' => '₱550',
        'original_price' => '₱850',
        'discount' => '35% OFF',
        'image' => '../../image/electronics/Smart Plug (App Controlled).jpeg',
        'stock' => 500,
        'colors' => ['White'],
        'sizes' => ['Standard']
    ],

    //Product 13
    313 => [
        'name' => 'Portable SSD 500GB',
        'price_range' => '₱4,500',
        'original_price' => '₱6,200',
        'discount' => '27% OFF',
        'image' => '../../image/electronics/Portable SSD 500GB.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Blue'],
        'sizes' => ['Standard']
    ],

    //Product 14
    314 => [
        'name' => 'Digital Alarm Clock with LED Display',
        'price_range' => '₱380',
        'original_price' => '₱600',
        'discount' => '37% OFF',
        'image' => '../../image/electronics/Digital Alarm Clock with LED Display.jpeg',
        'stock' => 500,
        'colors' => ['White', 'Black', 'Wood'],
        'sizes' => ['Standard']
    ],

    315 => [
        'name' => 'Car Phone Holder (Magnetic)',
        'price_range' => '₱250',
        'original_price' => '₱450',
        'discount' => '44% OFF',
        'image' => '../../image/electronics/Car Phone Holder (Magnetic).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Silver'],
        'sizes' => ['Standard']
    ],
];

    // Get ID correctly
    $p_id = isset($product_id) ? $product_id : 301;

    // Refresh current product after data fix
    $product = isset($products_data[$p_id]) ? $products_data[$p_id] : $products_data[301];
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
            Electronics
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

            window.location.href = `../../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(fullName)}&price=${price}&image=${img}&quantity=${qty}&store=IMarket%20Electronics`;
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