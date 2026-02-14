<?php
// Centralized Product Data for New Arrivals
$products_data = [

    //Product 1
    201 => [
        'name' => 'Classic Leather Wallet (Men)',
        'price_range' => '₱180',
        'original_price' => '₱225',
        'discount' => '20% OFF',
        'image' => '../../image/New-arrivals/Classic Leather Wallet (Men).jpeg',
        'stock' => 50,
        'colors' => ['Brown', 'Black', 'Tan'],
        'sizes' => ['Standard']
    ],

    //Product 2
    202 => [
        'name' => 'Trendy Crossbody Bag (Women)',
        'price_range' => '₱1,495',
        'original_price' => '₱1,899',
        'discount' => '20% OFF',
        'image' => '../../image/New-arrivals/Trendy Crossbody Bag (Women).jpeg',
        'stock' => 30,
        'colors' => ['Beige', 'Black', 'Pink'],
        'sizes' => ['Standard']
    ],

    //Product 3
    203 => [
        'name' => 'Wireless Bluetooth Earbuds',
        'price_range' => '₱219',
        'original_price' => '₱300',
        'discount' => '27% OFF',
        'image' => '../../image/New-arrivals/Wireless Bluetooth Earbuds.jpeg',
        'stock' => 100,
        'colors' => ['Black', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 4
    204 => [
        'name' => 'Oversized Graphic T-Shirt',
        'price_range' => '₱149',
        'original_price' => '₱299',
        'discount' => '50% OFF',
        'image' => '../../image/New-arrivals/Oversized Graphic T-Shirt.jpeg',
        'stock' => 200,
        'colors' => ['Black', 'White', 'Navy'],
        'sizes' => ['S', 'M', 'L', 'XL']
    ],

    //Product 5
    205 => [
        'name' => 'Smart LED Desk Lamp',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Smart LED Desk Lamp.jpeg',
        'stock' => 45,
        'colors' => ['White'],
        'sizes' => ['Standard']
    ],

    //Product 6
    206 => [
        'name' => 'Minimalist Wrist Watch',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Minimalist Wrist Watch.jpeg',
        'stock' => 45,
        'colors' => ['Black', 'Silver'],
        'sizes' => ['Standard']
    ],

    //Product 7
    207 => [
        'name' => 'Canvas Tote Bag',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Canvas Tote Bag.jpeg',
        'stock' => 45,
        'colors' => ['Beige', 'Black'],
        'sizes' => ['Standard']
    ],

    //Product 8
    208 => [
        'name' => 'Portable Power Bank 10,000mAh',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Portable Power Bank 10,000mAh.jpeg',
        'stock' => 45,
        'colors' => ['Black', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 9
    209 => [
        'name' => 'Fashion Sunglasses (UV Protected)',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Fashion Sunglasses (UV Protected).jpeg',
        'stock' => 45,
        'colors' => ['Black', 'Brown'],
        'sizes' => ['Standard']
    ],

    //Product 10
    210 => [
        'name' => 'Bluetooth Mini Speaker',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Bluetooth Mini Speaker.jpeg',
        'stock' => 45,
        'colors' => ['White', 'Black'],
        'sizes' => ['Standard']
    ],

    //Product 11
    211 => [
        'name' => 'Stainless Steel Water Bottle',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Stainless Steel Water Bottle.jpeg',
        'stock' => 45,
        'colors' => ['Silver', 'Black'],
        'sizes' => ['500ml']
    ],

    //Product 12
    212 => [
        'name' => 'Casual Sneakers (Unisex)',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Casual Sneakers (Unisex).jpeg',
        'stock' => 45,
        'colors' => ['White', 'Black'],
        'sizes' => ['38', '39', '40', '41', '42']
    ],

    //Product 13
    213 => [
        'name' => 'Phone Stand Holder (Adjustable)',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Phone Stand Holder (Adjustable).jpeg',
        'stock' => 45,
        'colors' => ['Black', 'White'],
        'sizes' => ['Standard']
    ],

    //Product 14
    214 => [
        'name' => 'Wireless Mouse (Silent Click)',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Wireless Mouse (Silent Click).jpeg',
        'stock' => 45,
        'colors' => ['Black', 'Grey'],
        'sizes' => ['Standard']
    ],

    //Product 15
    215 => [
        'name' => 'Aesthetic Desk Organizer Set',
        'price_range' => '₱299',
        'original_price' => '₱499',
        'discount' => '40% OFF',
        'image' => '../../image/New-arrivals/Aesthetic Desk Organizer Set.jpeg',
        'stock' => 45,
        'colors' => ['White', 'Pink'],
        'sizes' => ['Standard']
    ],

];

    // Fix pricing data for consistency
    foreach ($products_data as $id => &$p) {
        if ($id == 201) continue; // Skip already correct one
        // Default logic: original price should be higher than current price
        $raw_price = floatval(preg_replace('/[^0-9.]/', '', $p['price_range']));
        $p['original_price'] = '₱' . number_format($raw_price * 1.5);
    }
    unset($p);

    // Get ID correctly
    $p_id = isset($product_id) ? $product_id : 201;

    // Refresh current product after data fix
    $product = isset($products_data[$p_id]) ? $products_data[$p_id] : $products_data[201];
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
    
    /* Picture Variant Styles */
    .pv-variant-images {
        display: flex;
        flex-wrap: nowrap;
        gap: 15px;
        margin-top: 10px;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .pv-variant-img-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
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
        font-size: 12px;
        font-weight: 500;
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .pv-variant-img:hover {
        border-color: #999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .pv-variant-img.selected {
        border-color: #0f172a;
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
            New Arrivals
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

    <!-- Size/Quantity -->
    <div class="pv-option-group">
        <span class="pv-option-label">Size</span>
        <div style="display: flex; gap: 10px; align-items: center;">
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

            window.location.href = `../../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(fullName)}&price=${price}&image=${img}&quantity=${qty}&store=IMarket%20New%20Arrivals`;
        }

        function selectVariantImage(button) {
            document.querySelectorAll('.pv-variant-img').forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
        }

        const firstVariant = document.querySelector('#color-options .pv-variant-img');
        if (firstVariant && !firstVariant.classList.contains('selected')) {
            firstVariant.classList.add('selected');
        }
    </script>
</div>
</div>
