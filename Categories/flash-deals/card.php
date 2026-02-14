<?php
// Flash Deals Product Cards
// This file displays flash deal products in a grid

// Sample flash deals data
$flash_deals = [
    [
        'id' => 1,
        'name' => 'Premium Wireless Headphones',
        'original_price' => 2999,
        'sale_price' => 1499,
        'discount' => 50,
        'image' => '../../image/Dashboard/jbl.webp',
        'time_left' => 3600, // seconds
        'shop' => 'AudioTech PH'
    ],
    [
        'id' => 2,
        'name' => 'Sports Running Shoes',
        'original_price' => 3500,
        'sale_price' => 2275,
        'discount' => 35,
        'image' => '../../image/Dashboard/kobe.jpg',
        'time_left' => 5400,
        'shop' => 'SportGear Store'
    ],
    [
        'id' => 3,
        'name' => 'JBL Audio System',
        'original_price' => 5000,
        'sale_price' => 2000,
        'discount' => 60,
        'image' => '../../image/Dashboard/jbl.webp',
        'time_left' => 2700,
        'shop' => 'Electronics Hub'
    ],
    [
        'id' => 4,
        'name' => 'Smart Watch Pro',
        'original_price' => 4500,
        'sale_price' => 2475,
        'discount' => 45,
        'image' => '../../image/Dashboard/watch.jpg',
        'time_left' => 7200,
        'shop' => 'TechGear Store'
    ],
    [
        'id' => 5,
        'name' => 'Premium Laptop Bag',
        'original_price' => 2500,
        'sale_price' => 1250,
        'discount' => 50,
        'image' => '../../image/Dashboard/f1.avif',
        'time_left' => 4500,
        'shop' => 'UrbanWear PH'
    ],
    [
        'id' => 6,
        'name' => 'Mechanical Keyboard',
        'original_price' => 3000,
        'sale_price' => 1500,
        'discount' => 50,
        'image' => '../../image/Best-seller/Keyboard-maagas.jpeg',
        'time_left' => 6300,
        'shop' => 'Gaming Gear'
    ],
    [
        'id' => 7,
        'name' => 'Wireless Mouse Pro',
        'original_price' => 1500,
        'sale_price' => 750,
        'discount' => 50,
        'image' => '../../image/Best-seller/pc%20computer.avif',
        'time_left' => 3900,
        'shop' => 'Tech Essentials'
    ],
    [
        'id' => 8,
        'name' => 'Premium School Backpack',
        'original_price' => 2200,
        'sale_price' => 880,
        'discount' => 60,
        'image' => '../../image/Best-seller/School-bag.jpg',
        'time_left' => 5500,
        'shop' => 'BackpackHub'
    ]
];
?>

<div class="flash-products-grid">
    <?php foreach ($flash_deals as $deal): ?>
    <div class="flash-product-card">
        <!-- Badges -->
        <div class="flash-badges">
            <div class="discount-badge">-<?php echo $deal['discount']; ?>%</div>
            <div class="flash-badge">
                <i class="fas fa-zap"></i> FLASH
            </div>
        </div>

        <!-- Product Image -->
        <div class="product-image-container">
            <img src="<?php echo $deal['image']; ?>" alt="<?php echo htmlspecialchars($deal['name']); ?>" class="product-img" onerror="this.src='../../image/placeholder.png'">
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <!-- Shop Name -->
            <div class="shop-name">
                <?php echo htmlspecialchars($deal['shop']); ?>
            </div>

            <!-- Product Name -->
            <h3 class="product-name"><?php echo htmlspecialchars($deal['name']); ?></h3>

            <!-- Price Section -->
            <div class="price-section">
                <span class="original-price">₱<?php echo number_format($deal['original_price']); ?></span>
                <span class="sale-price">₱<?php echo number_format($deal['sale_price']); ?></span>
            </div>

            <!-- Countdown -->
            <div class="product-countdown">
                <i class="fas fa-hourglass-end"></i>
                <span class="countdown-timer" data-time="<?php echo $deal['time_left']; ?>">
                    <?php
                    $hours = floor($deal['time_left'] / 3600);
                    $minutes = floor(($deal['time_left'] % 3600) / 60);
                    $seconds = $deal['time_left'] % 60;
                    echo sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                    ?>
                </span>
            </div>

            <!-- Variant Swatches -->
            <div class="variant-swatches" data-name="<?php echo htmlspecialchars($deal['name']); ?>" data-image="<?php echo $deal['image']; ?>"></div>

            <!-- Buttons -->
            <button class="add-to-cart-btn" onclick="addToCart(this)">
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>

            <button class="quick-checkout-btn" onclick="quickCheckout(this)">
                <i class="fas fa-bolt"></i> Buy Now
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
