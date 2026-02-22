<?php
// Centralized Product Data
// This replaces the need for 12+ separate files.
// Ideally, this should come from a database.

$products_data = [
    101 => [
        'name' => 'Shoulder Bag Men',
        'price_range' => '₱149 - ₱170',
        'original_price' => '₱198',
        'discount' => '35% OFF',
        'image' => '../../image/Best-seller/bag-men.jpeg',
        'stock' => 1209,
        'has_variants' => true,
        'variant_images' => [
            'Black Model' => '../../image/Best-seller/bag-men.jpeg',
            'Grey Model' => '../../image/Best-seller/bag-men.jpeg',
            'Blue Model' => '../../image/Best-seller/bag-men.jpeg'
        ],
        'colors' => ['Black', 'Grey', 'Blue'],
        'sizes' => ['S', 'M', 'L']
    ],
    102 => [
        'name' => 'Bag Women',
        'price_range' => '₱340',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/bag-women.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],
    103 => [
        'name' => 'Notebook',
        'price_range' => '₱340',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/Notebooks.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    104 => [
        'name' => 'Earphone Bluetooth',
        'price_range' => '₱1,500',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/Earphone-bluetooth.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    105 => [
        'name' => 'Snikers Shoes',
        'price_range' => '₱2,500',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/snikers%20shoes.avif',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    106 => [
        'name' => 'Swatch Watch',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/Snart%20watch.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    107 => [
        'name' => 'Brand New SEALED HP Laptop i3',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/laptop.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    108 => [
        'name' => 'Desktop Computers & 2-in-1 PCs | Dell Philippines',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/pc%20computer.avif',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    109 => [
        'name' => 'vivo pro max',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/vivo%20pro%20max.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    110 => [
        'name' => 'iphone 15 pro max na may kagat',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/iphone.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    111 => [
        'name' => 'Keyboard mechanical',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/Keyboard-maagas.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    112 => [
        'name' => 'Ben10 brief',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/brief.jpg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    113 => [
        'name' => 'USB C Fast Charging Cable (2-Pack)',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/USB%20C%20Fast%20Charging%20Cable%20(2-Pack).jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    114 => [
        'name' => 'Mini Bluetooth Speaker',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/Mini%20Bluetooth%20Speaker.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],

    115 => [
        'name' => 'Phone Ring Holder    ',
        'price_range' => '₱10,200',
        'original_price' => '₱500',
        'discount' => '32% OFF',
        'image' => '../../image/Best-seller/Phone%20Ring%20Holder.jpeg',
        'stock' => 500,
        'colors' => ['Black', 'Pink', 'White'],
        'sizes' => ['Standard']
    ],
];

    // Fix pricing data for consistency
    foreach ($products_data as $id => &$p) {
        if (!$p) continue;
        if ($id == 101) continue; // Skip already correct one
        // Default logic: original price should be higher than current price
        if (isset($p['price_range']) && strpos($p['price_range'], '10,200') !== false) {
            $p['original_price'] = '₱15,000';
            $p['price_range'] = '₱10,200';
        } elseif ($id == 104) {
            $p['original_price'] = '₱2,200';
            $p['price_range'] = '₱1,500';
        } elseif ($id == 105) {
            $p['original_price'] = '₱3,800';
            $p['price_range'] = '₱2,500';
        } else {
            $p['original_price'] = '₱500';
            $p['price_range'] = '₱340';
        }
    }
    unset($p);

    // Get ID correctly
    $p_id = isset($product_id) ? intval($product_id) : (isset($_GET['id']) ? intval($_GET['id']) : 101);

    // Refresh current product after data fix - only use default if product doesn't exist
    if (isset($products_data[$p_id])) {
        $product = $products_data[$p_id];
    } else {
        // Product not found - try to fetch from database or show error
        if (!isset($conn)) {
            require_once __DIR__ . '/../../Database/config.php';
        }
        
        // Try to fetch product from database
        $db_product = null;
        if (isset($conn) && $p_id > 0) {
            $prod_query = "SELECT name, price, image_url, description FROM products WHERE id = " . intval($p_id) . " AND status = 'Active' LIMIT 1";
            $prod_result = mysqli_query($conn, $prod_query);
            if ($prod_result && mysqli_num_rows($prod_result) > 0) {
                $db_product = mysqli_fetch_assoc($prod_result);
            }
        }
        
        if ($db_product) {
            // Use database product
            $product = [
                'name' => $db_product['name'],
                'price_range' => '₱' . number_format($db_product['price'], 2),
                'original_price' => '₱' . number_format($db_product['price'] * 1.2, 2),
                'discount' => '20% OFF',
                'image' => $db_product['image_url'] ?? '../../image/Best-seller/default.jpg',
                'stock' => 100,
                'colors' => ['Default'],
                'sizes' => ['Standard']
            ];
        } else {
            // Fallback to default product 101 only if database fetch also failed
            $product = $products_data[101];
        }
    }
    $price = $product['price_range'];
    $name = isset($product['name']) ? $product['name'] : 'Product';
    $img = isset($product['image']) ? str_replace(' ', '%20', $product['image']) : ''; 
?>

<link rel="stylesheet" href="../../css/components/shared-product-view.css?v=<?php echo time(); ?>">

<style>
    .pv-variant-images {
        display: flex;
        flex-wrap: nowrap;
        gap: 15px;
        margin-top: 10px;
        overflow-x: auto;
        padding-bottom: 10px;
    }

    .pv-variant-img-wrapper {
        position: relative;
    }

    .pv-variant-img {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 80px;
    }

    .pv-variant-img:hover {
        border-color: #999;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .pv-variant-img.selected {
        border-color: #0f172a;
        border-width: 2px;
        box-shadow: 0 0 0 2px white, 0 0 0 3px #0f172a;
        background: #f9fafb;
    }

    .pv-color-swatches {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 10px;
    }

    .pv-color-swatch-wrapper {
        position: relative;
    }

    .pv-color-swatch {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 3px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .pv-color-swatch:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-color: #999;
    }

    .pv-color-swatch.selected {
        border-color: #0f172a;
        border-width: 3px;
        box-shadow: 0 0 0 2px white, 0 0 0 4px #0f172a;
    }

    .pv-color-name {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        background: #0f172a;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap;
        font-size: 12px;
        font-weight: 600;
        opacity: 0;
        transition: opacity 0.2s, bottom 0.2s;
        pointer-events: none;
    }

    .pv-color-swatch:hover .pv-color-name {
        opacity: 1;
        bottom: -30px;
    }

    /* Light color borders for visibility */
    .pv-color-swatch[style*="#f5f5f5"],
    .pv-color-swatch[style*="#ffffff"],
    .pv-color-swatch[style*="#fff"] {
        border-color: #d1d5db !important;
    }
</style>

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
            Best Selling
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

    <!-- Variant Options -->
    <div class="pv-option-group">
        <span class="pv-option-label">Choose Variant</span>
        <div class="pv-variant-images" id="color-options">
            <?php 
            $variant_images = isset($product['variant_images']) ? $product['variant_images'] : [];
            if (empty($variant_images) && isset($product['colors'])) {
                // Auto-generate variant images if not defined
                foreach ($product['colors'] as $color) {
                    $variant_images[$color] = $product['image'];
                }
            }
            ?>
            <?php foreach ($variant_images as $variant_label => $variant_img): ?>
                <div class="pv-variant-img-wrapper">
                    <button class="pv-variant-img <?php echo array_key_first($variant_images) === $variant_label ? 'selected' : ''; ?>" 
                            data-val="<?php echo htmlspecialchars($variant_label); ?>" 
                            onclick="selectVariantImage(this)"
                            title="<?php echo htmlspecialchars($variant_label); ?>">
                        <img src="<?php echo str_replace(' ', '%20', $variant_img); ?>" alt="<?php echo htmlspecialchars($variant_label); ?>" style="width: 60px; height: 60px; border-radius: 6px; object-fit: cover;">
                        <span style="display: block; font-size: 11px; margin-top: 6px; text-align: center; font-weight: 500;"><?php echo htmlspecialchars($variant_label); ?></span>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Size -->
    <div class="pv-option-group">
        <span class="pv-option-label">Size</span>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <?php foreach ($product['sizes'] as $index => $size): ?>
                <span style="padding: 8px 16px; background: #f0f0f0; border-radius: 4px; font-weight: 500;"><?php echo htmlspecialchars($size); ?></span>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="size-options" value="<?php echo htmlspecialchars($product['sizes'][0] ?? 'Standard'); ?>">
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

    <!-- Rate Product Button -->
    <div class="pv-rate-section" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f4f8;">
        <a href="../../Shop/Rate-Reviews.php?product_name=<?php echo urlencode($name); ?>" 
           class="pv-btn pv-btn-rate" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fas fa-star"></i> Share Your Review
        </a>
        <p style="font-size: 12px; color: #999; text-align: center; margin-top: 10px;">Have you used this product? Share your experience!</p>
    </div>

    <script>
        function addToCart() {
            const variantBtn = document.querySelector('#color-options .pv-variant-img.selected');
            const variant = variantBtn ? variantBtn.getAttribute('data-val') : 'Default';
            const sizeInput = document.querySelector('#size-options');
            const size = sizeInput ? sizeInput.value : 'Standard';
            const qty = document.getElementById('qty').value;
            const fullName = `<?php echo addslashes($name); ?> (${variant}, ${size})`;
            const price = <?php echo floatval(preg_replace('/[^0-9.]/', '', $price)); ?>;
            const img = '<?php echo $img; ?>';

            window.location.href = `../../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(fullName)}&price=${price}&image=${img}&quantity=${qty}&store=IMarket%20Best%20Selling`;
        }

        function selectVariantImage(button) {
            document.querySelectorAll('.pv-variant-img').forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
        }

        function selectColor(button) {
            document.querySelectorAll('.pv-color-swatch').forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
        }

        // Initialize first variant as selected if it exists
        const firstVariant = document.querySelector('#color-options .pv-variant-img');
        if (firstVariant && !firstVariant.classList.contains('selected')) {
            firstVariant.classList.add('selected');
        }
    </script>
</div>
