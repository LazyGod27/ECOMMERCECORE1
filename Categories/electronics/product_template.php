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
        'has_variants' => true,
        'variant_images' => [
            'Black Model' => '../../image/electronics/Wireless Bluetooth Earbuds.jpeg',
            'Grey Model' => '../../image/electronics/Wireless Bluetooth Earbuds.jpeg',
            'Blue Model' => '../../image/electronics/Wireless Bluetooth Earbuds.jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black Model' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg',
            'Pink Model' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg',
            'White Model' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black Model' => '../../image/electronics/Portable Power Bank 20,000mAh.jpeg',
            'Pink Model' => '../../image/electronics/Portable Power Bank 20,000mAh.jpeg',
            'White Model' => '../../image/electronics/Portable Power Bank 20,000mAh.jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black Model' => '../../image/electronics/Bluetooth Speaker (Waterproof).jpeg',
            'Pink Model' => '../../image/electronics/Bluetooth Speaker (Waterproof).jpeg',
            'White Model' => '../../image/electronics/Bluetooth Speaker (Waterproof).jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black Cable' => '../../image/electronics/USB-C Fast Charging Cable.jpeg',
            'White Cable' => '../../image/electronics/USB-C Fast Charging Cable.jpeg',
            'Grey Cable' => '../../image/electronics/USB-C Fast Charging Cable.jpeg'
        ],
        'colors' => ['Black'],
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
        'has_variants' => true,
        'variant_images' => [
            'Black RGB' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg',
            'White RGB' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black Edition' => '../../image/electronics/Noise_Cancelling_Headphones.jpeg',
            'Silver Edition' => '../../image/electronics/Noise_Cancelling_Headphones.jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'White Model' => '../../image/electronics/Mini WiFi Router Pocket WiFi.jpeg',
            'Black Model' => '../../image/electronics/Mini WiFi Router Pocket WiFi.jpeg'
        ],
        'colors' => ['White'],
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
        'has_variants' => true,
        'variant_images' => [
            'Warm White' => '../../image/electronics/Smart LED Light Bulb (WiFi Controlled).jpeg',
            'Cool White' => '../../image/electronics/Smart LED Light Bulb (WiFi Controlled).jpeg',
            'RGB Color' => '../../image/electronics/Smart LED Light Bulb (WiFi Controlled).jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black RGB' => '../../image/electronics/Laptop Cooling Pad RGB Fan.jpeg',
            'Blue RGB' => '../../image/electronics/Laptop Cooling Pad RGB Fan.jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Full HD 1080p' => '../../image/electronics/1080p HD Web Camera.jpeg',
            'Standard Package' => '../../image/electronics/1080p HD Web Camera.jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'White Plug' => '../../image/electronics/Smart Plug (App Controlled).jpeg',
            'Black Plug' => '../../image/electronics/Smart Plug (App Controlled).jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black Model' => '../../image/electronics/Portable SSD 500GB.jpeg',
            'Blue Model' => '../../image/electronics/Portable SSD 500GB.jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'White Design' => '../../image/electronics/Digital Alarm Clock with LED Display.jpeg',
            'Black Design' => '../../image/electronics/Digital Alarm Clock with LED Display.jpeg',
            'Wood Design' => '../../image/electronics/Digital Alarm Clock with LED Display.jpeg'
        ],
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
        'has_variants' => true,
        'variant_images' => [
            'Black Holder' => '../../image/electronics/Car Phone Holder (Magnetic).jpeg',
            'Silver Holder' => '../../image/electronics/Car Phone Holder (Magnetic).jpeg'
        ],
        'colors' => ['Black'],
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

<style>
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

    /* Variant Image Styles */
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

    <!-- Variant Options -->
    <div class="pv-option-group">
        <span class="pv-option-label">Choose Variant</span>
        <div class="pv-variant-images" id="color-options">
            <?php foreach ($product['variant_images'] as $variant_label => $variant_img): ?>
                <div class="pv-variant-img-wrapper">
                    <button class="pv-variant-img <?php echo array_key_first($product['variant_images']) === $variant_label ? 'selected' : ''; ?>" 
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

    <!-- Size/Quantity -->
    <div class="pv-option-group">
        <span class="pv-option-label">Size</span>
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="padding: 8px 16px; background: #f0f0f0; border-radius: 4px; font-weight: 500;">Standard</span>
        </div>
        <input type="hidden" id="size-options" value="Standard">
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
            const colorInput = document.querySelector('#color-options');
            
            // Get variant value - check if it's a hidden input (STANDARD) or from button
            let color;
            if (colorInput.tagName === 'INPUT') {
                color = colorInput.value;
            } else {
                const colorBtn = document.querySelector('#color-options .pv-color-swatch.selected');
                const variantBtn = document.querySelector('#color-options .pv-variant-img.selected');
                color = colorBtn ? colorBtn.getAttribute('data-val') : (variantBtn ? variantBtn.getAttribute('data-val') : 'Default');
            }
            
            const sizeBtn = document.querySelector('#size-options .pv-option-btn.selected');
            const size = sizeBtn ? sizeBtn.getAttribute('data-val') : 'Standard';
            const qty = document.getElementById('qty').value;
            const fullName = `<?php echo addslashes($name); ?> (${color}, ${size})`;
            const price = <?php echo floatval(preg_replace('/[^0-9.]/', '', $price)); ?>;
            const img = '<?php echo $img; ?>';

            window.location.href = `../../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(fullName)}&price=${price}&image=${img}&quantity=${qty}&store=IMarket%20Electronics`;
        }

        function selectColor(button) {
            document.querySelectorAll('.pv-color-swatch').forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
        }

        function selectVariantImage(button) {
            document.querySelectorAll('.pv-variant-img').forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
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
