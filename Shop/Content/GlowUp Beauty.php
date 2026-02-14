<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=GB&background=8e44ad&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>GlowUp Beauty</h1>
                <p>Latest Beauty Products</p>
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
// Custom Products List for GlowUp Beauty
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'Glow Up Facial Serum (30ml)',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/GlowUp Beauty/Glow Up Facial Serum (30ml).jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Brightening facial serum formulated to hydrate and improve skin radiance with lightweight absorption.'
        ],
        // Product 2
        [
            'name' => 'Secret Glow Tone Up Cream ',
            'price' => '₱899.00',
            'raw_price' => 899.00,
            'image' => '../image/Shop/GlowUp Beauty/Secret Glow Tone Up Cream.jpeg',
            'rating' => 4.5,
            'sold' => 3400
            ,'description' => 'Tone-up cream that smooths complexion and provides a natural, dewy finish with SPF protection.'
        ],
        // Product 3
        [
            'name' => 'SevenGlow Brightening Soap',
            'price' => '₱1,250.00',
            'raw_price' => 1250.00,
            'image' => '../image/Shop/GlowUp Beauty/SevenGlow Brightening Soap.jpeg',
            'rating' => 4.7,
            'sold' => 850
            ,'description' => 'Brightening soap bar enriched with natural extracts to help even skin tone and cleanse gently.'
        ],
        // Product 4
        [
            'name' => 'Shantahl Ultimate Glow Up Set',
            'price' => '₱2,800.00',
            'raw_price' => 2800.00,
            'image' => '../image/Shop/GlowUp Beauty/Shantahl Ultimate Glow Up Set.jpeg',
            'rating' => 4.9,
            'sold' => 540
            ,'description' => 'Complete glow-up skincare set containing cleanser, serum, and moisturizer for a refreshed routine.'
        ],
        // Product 5
        [
            'name' => 'I White Korea Glow-Up Whip',
            'price' => '₱750.00',
            'raw_price' => 750.00,
            'image' => '../image/Shop/GlowUp Beauty/I White Korea Glow-Up Whip.jpeg',
            'rating' => 4.3,
            'sold' => 2100
            ,'description' => 'Whipping cream formula for skin brightening with silky texture and quick absorption.'
        ],
        // Product 6
        [
            'name' => 'Hey Skin Glow Up Serum',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/GlowUp Beauty/Hey Skin Glow Up Serum.jpeg',
            'rating' => 4.6,
            'sold' => 3000
            ,'description' => 'Lightweight serum designed to boost radiance and deliver targeted hydration for dull skin.'
        ],
        // Product 7
        [
            'name' => 'Glow Up Night Cream (15ml)',
            'price' => '₱1,500.00',
            'raw_price' => 1500.00,
            'image' => '../image/Shop/GlowUp Beauty/Glow Up Night Cream (15ml).jpeg',
            'rating' => 4.8,
            'sold' => 1500
            ,'description' => 'Night cream formulated to repair and restore skin overnight with nourishing ingredients.'
        ],
        // Product 8
        [
            'name' => 'SkinTouch Magic Set A',
            'price' => '₱350.00',
            'raw_price' => 350.00,
            'image' => '../image/Shop/GlowUp Beauty/SkinTouch Magic Set A.jpeg', // Using stylized image
            'rating' => 4.2,
            'sold' => 1100
            ,'description' => 'Value skincare set providing curated essentials for a simple but effective routine.'
        ],
        // Product 9
        [
            'name' => 'Avocado Papaya Collagen Drink',
            'price' => '₱1,100.00',
            'raw_price' => 1100.00,
            'image' => '../image/Shop/GlowUp Beauty/Avocado Papaya Collagen Drink.jpeg',
            'rating' => 4.5,
            'sold' => 980
            ,'description' => 'Collagen drink with avocado and papaya extracts to support skin elasticity and hydration.'
        ],
        // Product 10
        [
            'name' => 'Mango Melon Creamy Collagen',
            'price' => '₱850.00',
            'raw_price' => 850.00,
            'image' => '../image/Shop/GlowUp Beauty/Mango Melon Creamy Collagen.jpeg',
            'rating' => 4.4,
            'sold' => 1300
            ,'description' => 'Creamy collagen supplement with mango and melon flavors for daily skin support.'
        ],
        // Product 11
        [
            'name' => 'Strawberry Banana Collagen',
            'price' => '₱1,800.00',
            'raw_price' => 1800.00,
            'image' => '../image/Shop/GlowUp Beauty/Strawberry Banana Collagen.jpeg',
            'rating' => 4.7,
            'sold' => 670
            ,'description' => 'Strawberry-banana flavored collagen supplement for skin nourishment and delightful taste.'
        ],
        // Product 12
        [
            'name' => 'AiBeauty Glow Up Iced Tea',
            'price' => '₱1,350.00',
            'raw_price' => 1350.00,
            'image' => '../image/Shop/GlowUp Beauty/AiBeauty Glow Up Iced Tea.jpeg',
            'rating' => 4.3,
            'sold' => 2500
            ,'description' => 'Iced tea beverage formulation with beauty-supporting ingredients for hydration and glow.'
        ],
        // Product 13
        [
            'name' => 'Glow Up Magic Blusher (30ml)',
            'price' => '₱450.00',
            'raw_price' => 450.00,
            'image' => '../image/Shop/GlowUp Beauty/Glow Up Magic Blusher (30ml).jpeg',
            'rating' => 4.1,
            'sold' => 4200
            ,'description' => 'Liquid blusher with blendable pigment to add a natural flushed glow to the cheeks.'
        ],
        // Product 14
        [
            'name' => 'Yeoubi Glow Up Whitening Soap',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/GlowUp Beauty/Yeoubi Glow Up Whitening Soap.jpeg',
            'rating' => 4.9,
            'sold' => 3100
            ,'description' => 'Whitening soap bar with gentle cleansers and nourishing oils for brighter-looking skin.'
        ],
        // Product 15
        [
            'name' => 'Sevendays Sun Protect (Pouch)',
            'price' => '₱299.00',
            'raw_price' => 299.00,
            'image' => '../image/Shop/GlowUp Beauty/Sevendays Sun Protect (Pouch).jpeg',
            'rating' => 4.0,
            'sold' => 1800
            ,'description' => 'Sun protect pouch with essential sunscreen formulations for portable sun protection.'
        ]
    ];
    return; // Stop processing
}
?>
