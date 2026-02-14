<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=FL&background=8e44ad&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>FreshLook PH</h1>
                <p>Personal Care Products</p>
            </div>
        </div>
        <!-- Sort Controls -->
        <div class="sort-controls">
            <span class="sort-label">Sort By</span>
            <a href="?store=<?php echo urlencode($selectedStore); ?>&sort=best"
                class="sort-btn <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'best') ? 'active' : ''; ?>">Best
                Match</a>
            <a href="?store=<?php echo urlencode($selectedStore); ?>&sort=latest"
                class="sort-btn <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'latest') ? 'active' : ''; ?>">Latest</a>
            <a href="?store=<?php echo urlencode($selectedStore); ?>&sort=sales"
                class="sort-btn <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'sales') ? 'active' : ''; ?>">Top
                Sales</a>
            <a href="?store=<?php echo urlencode($selectedStore); ?>&sort=<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'price_desc' : 'price_asc'; ?>"
                class="sort-btn <?php echo (isset($_GET['sort']) && strpos($_GET['sort'], 'price') !== false) ? 'active' : ''; ?>">
                Price <i
                    class="fas fa-chevron-<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'up' : 'down'; ?>"></i>
            </a>
        </div>
    </div>
    <?php
    return; // Exit to prevent running product logic when rendering header
}

// ------------------------------------
// Custom Products List for FreshLook PH
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'FreshLook Colorblends (Monthly)',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/FreshLook PH/FreshLook Colorblends (Monthly).jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Monthly colored contact lenses designed for comfort and natural-looking color enhancement.'
        ],
        // Product 2
        [
            'name' => 'FreshLook One-Day Color (10 Lenses)',
            'price' => '₱899.00',
            'raw_price' => 899.00,
            'image' => '../image/Shop/FreshLook PH/FreshLook One-Day Color (10 Lenses).jpeg',
            'rating' => 4.5,
            'sold' => 3400
            ,'description' => 'Daily disposable colored lenses in a 10-lens pack for convenience and hygiene.'
        ],
        // Product 3
        [
            'name' => 'SevenGlow Brightening Soap',
            'price' => '₱1,250.00',
            'raw_price' => 1250.00,
            'image' => '../image/Shop/GlowUp Beauty/SevenGlow Brightening Soap.jpeg',
            'rating' => 4.7,
            'sold' => 850
            ,'description' => 'Brightening soap formulated to gently cleanse and enhance skin clarity with natural extracts.'
        ],
        // Product 4
        [
            'name' => 'FreshLook Illuminate (Starburst)',
            'price' => '₱2,800.00',
            'raw_price' => 2800.00,
            'image' => '../image/Shop/FreshLook PH/FreshLook Illuminate (Starburst).jpeg',
            'rating' => 4.9,
            'sold' => 540
            ,'description' => 'Premium skincare product with targeted brightening ingredients to improve skin tone.'
        ],
        // Product 5
        [
            'name' => 'Fresh Tomato Glass Skin Gel',
            'price' => '₱750.00',
            'raw_price' => 750.00,
            'image' => '../image/Shop/FreshLook PH/Fresh Tomato Glass Skin Gel.jpeg',
            'rating' => 4.3,
            'sold' => 2100
            ,'description' => 'Glass skin gel with tomato extracts to hydrate and promote a smooth, dewy finish.'
        ],
        // Product 6
        [
            'name' => 'Opti-Free PureMoist (300ml)',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/FreshLook PH/Opti-Free PureMoist (300ml).jpeg',
            'rating' => 4.6,
            'sold' => 3000
            ,'description' => 'Multipurpose contact lens solution for cleaning and storage with moisturizing formula.'
        ],
        // Product 7
        [
            'name' => 'Fresh Skinlab Milk White Lotion',
            'price' => '₱1,500.00',
            'raw_price' => 1500.00,
            'image' => '../image/Shop/FreshLook PH/Fresh Skinlab Milk White Lotion.jpeg',
            'rating' => 4.8,
            'sold' => 1500
            ,'description' => 'Nourishing body lotion enriched with milk proteins for soft, hydrated skin.'
        ],
        // Product 8
        [
            'name' => 'FreshLook CC Lens (Jewel Collection)',
            'price' => '₱350.00',
            'raw_price' => 350.00,
            'image' => '../image/Shop/FreshLook PH/FreshLook CC Lens (Jewel Collection).jpeg', // Using stylized image
            'rating' => 4.2,
            'sold' => 1100
            ,'description' => 'Colored contact lenses from the jewel collection offering vibrant tints and comfort.'
        ],
        // Product 9
        [
            'name' => 'Fresh Tomato Glass Skin Vitamin C',
            'price' => '₱1,100.00',
            'raw_price' => 1100.00,
            'image' => '../image/Shop/FreshLook PH/Fresh Tomato Glass Skin Vitamin C.jpeg',
            'rating' => 4.5,
            'sold' => 980
            ,'description' => 'Vitamin C serum formulated to brighten and support skin health with antioxidant benefits.'
        ],
        // Product 10
        [
            'name' => 'Systane Ultra Lubricant Eye Drops',
            'price' => '₱850.00',
            'raw_price' => 850.00,
            'image' => '../image/Shop/FreshLook PH/Systane Ultra Lubricant Eye Drops.jpeg',
            'rating' => 4.4,
            'sold' => 1300
            ,'description' => 'Lubricant eye drops for long-lasting relief and comfort during screen use.'
        ],
        // Product 11
        [
            'name' => 'Fresh Jeju Aloe Ice Facial Mist',
            'price' => '₱1,800.00',
            'raw_price' => 1800.00,
            'image' => '../image/Shop/FreshLook PH/Fresh Jeju Aloe Ice Facial Mist.jpeg',
            'rating' => 4.7,
            'sold' => 670
            ,'description' => 'Aloe-infused facial mist for quick hydration and cooling on the go.'
        ],
        // Product 12
        [
            'name' => 'FreshLook Colorblends (Graded)',
            'price' => '₱1,350.00',
            'raw_price' => 1350.00,
            'image' => '../image/Shop/FreshLook PH/FreshLook Colorblends (Graded).jpeg',
            'rating' => 4.3,
            'sold' => 2500
            ,'description' => 'Graded colorblend contact lenses with consistent tint and comfort for regular wear.'
        ],
        // Product 13
        [
            'name' => 'Fresh Skinlab 98% Tomato Toner',
            'price' => '₱450.00',
            'raw_price' => 450.00,
            'image' => '../image/Shop/FreshLook PH/Fresh Skinlab 98 Percent Tomato Toner.jpeg',
            'rating' => 4.1,
            'sold' => 4200
            ,'description' => 'Tomato-based toner with 98% natural content to soothe and tone the skin.'
        ],
        // Product 14
        [
            'name' => 'Opti-Free Replenish Kit (Travel Size)',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/FreshLook PH/Opti-Free Replenish Kit (Travel Size).jpeg',
            'rating' => 4.9,
            'sold' => 3100
            ,'description' => 'Travel-sized replenishing kit ideal for maintaining contact lens hygiene while traveling.'
        ],
        // Product 15
        [
            'name' => 'Fresh Jeju Aloe Ice Sun Protection',
            'price' => '₱299.00',
            'raw_price' => 299.00,
            'image' => '../image/Shop/FreshLook PH/Fresh Jeju Aloe Ice Sun Protection.jpeg',
            'rating' => 4.0,
            'sold' => 1800
            ,'description' => 'Sun protection mist with aloe for lightweight, refreshing UV defense.'
        ]
    ];
    return; // Stop processing
}
?>
