<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=TB&background=8e44ad&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>TrendyBags PH</h1>
                <p>Stylish Bags</p>
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
// Custom Products List for TrendyBags PH
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'Olive Utility Messenger',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/TrendyBags PH/Olive Utility Messenger.jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Durable messenger bag with multiple compartments and an adjustable strap for daily carry.'
        ],
        // Product 2
        [
            'name' => 'Suede Shoulder Bag (Dark Moss)',
            'price' => '₱899.00',
            'raw_price' => 899.00,
            'image' => '../image/Shop/TrendyBags PH/Suede Shoulder Bag (Dark Moss).jpeg',
            'rating' => 4.5,
            'sold' => 3400
            ,'description' => 'Soft suede shoulder bag with elegant silhouette, perfect for everyday or dressy occasions.'
        ],
        // Product 3
        [
            'name' => 'Half Moon Leather Purse',
            'price' => '₱1,250.00',
            'raw_price' => 1250.00,
            'image' => '../image/Shop/TrendyBags PH/Half Moon Leather Purse.jpeg',
            'rating' => 4.7,
            'sold' => 850
            ,'description' => 'Premium half-moon leather purse with secure closure and compact organization for essentials.'
        ],
        // Product 4
        [
            'name' => 'Canvas Tote with Scarf',
            'price' => '₱2,800.00',
            'raw_price' => 2800.00,
            'image' => '../image/Shop/TrendyBags PH/Canvas Tote with Scarf.jpeg',
            'rating' => 4.9,
            'sold' => 540
            ,'description' => 'Sturdy canvas tote paired with a decorative scarf — roomy and stylish for errands or beach days.'
        ],
        // Product 5
        [
            'name' => 'Mother of Pearl Clutch',
            'price' => '₱750.00',
            'raw_price' => 750.00,
            'image' => '../image/Shop/TrendyBags PH/Mother of Pearl Clutch.jpeg',
            'rating' => 4.3,
            'sold' => 2100
            ,'description' => 'Elegant mother-of-pearl clutch ideal for evening events, with a secure snap closure.'
        ],
        // Product 6
        [
            'name' => 'Nylon Dumpling Bag',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/TrendyBags PH/Nylon Dumpling Bag.jpeg',
            'rating' => 4.6,
            'sold' => 3000
            ,'description' => 'Lightweight nylon dumpling bag with a modern, curved shape and water-resistant finish.'
        ],
        // Product 7
        [
            'name' => 'Anderson Top Handle Bag',
            'price' => '₱1,500.00',
            'raw_price' => 1500.00,
            'image' => '../image/Shop/TrendyBags PH/Anderson Top Handle Bag.jpeg',
            'rating' => 4.8,
            'sold' => 1500
            ,'description' => 'Structured top-handle bag with clean lines and internal pockets for organized carrying.'
        ],
        // Product 8
        [
            'name' => 'Crocodile Vanity Bag',
            'price' => '₱350.00',
            'raw_price' => 350.00,
            'image' => '../image/Shop/TrendyBags PH/Crocodile Vanity Bag.jpeg', // Using stylized image
            'rating' => 4.2,
            'sold' => 1100
            ,'description' => 'Small vanity bag with crocodile-texture finish — compact, stylish and great for cosmetics.'
        ],
        // Product 9
        [
            'name' => 'Pop Toft Tote Bag',
            'price' => '₱1,100.00',
            'raw_price' => 1100.00,
            'image' => '../image/Shop/TrendyBags PH/Pop Toft Tote Bag.jpeg',
            'rating' => 4.5,
            'sold' => 980
            ,'description' => 'Durable tote with a playful printed pattern and spacious interior for daily essentials.'
        ],
        // Product 10
        [
            'name' => 'Woven Suede Crossbody',
            'price' => '₱850.00',
            'raw_price' => 850.00,
            'image' => '../image/Shop/TrendyBags PH/Woven Suede Crossbody.jpeg',
            'rating' => 4.4,
            'sold' => 1300
            ,'description' => 'Woven suede crossbody with artisanal texture and adjustable strap for hands-free convenience.'
        ],
        // Product 11
        [
            'name' => 'Buntal Shoulder Bag',
            'price' => '₱1,800.00',
            'raw_price' => 1800.00,
            'image' => '../image/Shop/TrendyBags PH/Buntal Shoulder Bag.jpeg',
            'rating' => 4.7,
            'sold' => 670
            ,'description' => 'Handwoven buntal shoulder bag offering natural texture and breathable build for summer looks.'
        ],
        // Product 12
        [
            'name' => 'Metallic Ruched Hobo Bag',
            'price' => '₱1,350.00',
            'raw_price' => 1350.00,
            'image' => '../image/Shop/TrendyBags PH/Metallic Ruched Hobo Bag.jpeg',
            'rating' => 4.3,
            'sold' => 2500
            ,'description' => 'Metallic ruched hobo bag with a soft interior and stylish sheen for day-to-night use.'
        ],
        // Product 13
        [
            'name' => 'Snoopy Peanuts Shopper',
            'price' => '₱450.00',
            'raw_price' => 450.00,
            'image' => '../image/Shop/TrendyBags PH/Snoopy Peanuts Shopper.jpeg',
            'rating' => 4.1,
            'sold' => 4200
            ,'description' => 'Playful printed shopper bag featuring character artwork and lightweight canvas construction.'
        ],
        // Product 14
        [
            'name' => 'Landscape Satchel',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/TrendyBags PH/Landscape Satchel.jpeg',
            'rating' => 4.9,
            'sold' => 3100
            ,'description' => 'Textured satchel with landscape print, roomy compartments and secure zip closure.'
        ],
        // Product 15
        [
            'name' => 'Faux Shearling Shoulder Bag',
            'price' => '₱299.00',
            'raw_price' => 299.00,
            'image' => '../image/Shop/TrendyBags PH/Faux Shearling Shoulder Bag.jpeg',
            'rating' => 4.0,
            'sold' => 1800
            ,'description' => 'Cozy faux-shearling shoulder bag ideal for cold-weather styling and soft comfort.'
        ]
    ];
    return; // Stop processing
}
?>
