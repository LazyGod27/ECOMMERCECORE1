<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=LB&background=27ae60&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>Luxe Basics</h1>
                <p>Minimalist & Basic Wear</p>
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
// Custom Products List for Luxe Basics
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'Luxe Ribbed Bodycon Midi',
            'price' => '₱249',
            'raw_price' => 249.00,
            'image' => '../image/Shop/Luxe Basics/Luxe Ribbed Bodycon Midi.jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Ribbed bodycon midi dress made from stretch fabric for a flattering silhouette and comfortable wear.'
        ],
        // Product 2
        [
            'name' => 'Premium Heavyweight Boxy Tee',
            'price' => '₱225',
            'raw_price' => 225.00,
            'image' => '../image/Shop/Luxe Basics/Premium Heavyweight Boxy Tee.jpeg',
            'rating' => 4.5,
            'sold' => 3400
            ,'description' => 'Heavyweight boxy tee crafted from premium cotton for a structured feel and lasting durability.'
        ],
        // Product 3
        [
            'name' => 'Seamless Sculpting Tank Top',
            'price' => '₱145',
            'raw_price' => 145.00,
            'image' => '../image/Shop/Luxe Basics/Seamless Sculpting Tank Top.jpeg',
            'rating' => 4.7,
            'sold' => 850
            ,'description' => 'Seamless sculpting tank top designed for smooth shaping and comfortable everyday layering.'
        ],
        // Product 4
        [
            'name' => 'Soft-Touch Halter Neck Top',
            'price' => '₱299',
            'raw_price' => 299.00,
            'image' => '../image/Shop/Luxe Basics/Soft-Touch Halter Neck Top.jpeg',
            'rating' => 4.9,
            'sold' => 540
            ,'description' => 'Halter neck top with soft-touch fabric and supportive fit ideal for evening wear.'
        ],
        // Product 5
        [
            'name' => 'Tailored Trouser Pants (Cream)',
            'price' => '₱135',
            'raw_price' => 135.00,
            'image' => '../image/Shop/Luxe Basics/Tailored Trouser Pants (Cream).jpeg',
            'rating' => 4.3,
            'sold' => 2100
            ,'description' => 'Tailored trousers with a clean cream finish; made for a smart, minimalist wardrobe.'
        ],
        // Product 6
        [
            'name' => 'Classic Button-Down Linen Shirt',
            'price' => '₱115',
            'raw_price' => 115.00,
            'image' => '../image/Shop/Luxe Basics/Classic Button-Down Linen Shirt.jpeg', // Fallback
            'rating' => 4.6,
            'sold' => 3000
            ,'description' => 'Classic linen shirt with breathable fibres for effortless smart-casual styling.'
        ],
        // Product 7
        [
            'name' => 'Double-Lined Tube Top',
            'price' => '₱280',
            'raw_price' => 280.00,
            'image' => '../image/Shop/Luxe Basics/Double-Lined Tube Top.jpeg',
            'rating' => 4.8,
            'sold' => 1500
            ,'description' => 'Double-lined tube top offering smoother coverage and a secure fit.'
        ],
        // Product 8
        [
            'name' => 'Premium Knit Cardigan',
            'price' => '₱320',
            'raw_price' => 320.00,
            'image' => '../image/Shop/Luxe Basics/Premium Knit Cardigan.jpeg', // Using stylized image
            'rating' => 4.2,
            'sold' => 1100
            ,'description' => 'Premium knit cardigan made from soft yarns to keep you warm without bulk.'
        ],
        // Product 9
        [
            'name' => 'High-Waist Wide Leg Trousers',
            'price' => '₱275',
            'raw_price' => 275.00,
            'image' => '../image/Shop/Luxe Basics/High-Waist Wide Leg Trousers.jpeg',
            'rating' => 4.5,
            'sold' => 980
            ,'description' => 'High-waist wide leg trousers with a relaxed fit and flattering drape.'
        ],
        // Product 10
        [
            'name' => 'Minimalist Mock Neck Top',
            'price' => '₱155',
            'raw_price' => 155.00,
            'image' => '../image/Shop/Luxe Basics/Minimalist Mock Neck Top.jpeg',
            'rating' => 4.4,
            'sold' => 1300
            ,'description' => 'Minimalist mock neck top with clean lines and a soft touch fabric for layering.'
        ],
        // Product 11
        [
            'name' => 'Luxe Cotton Biker Shorts',
            'price' => '₱120',
            'raw_price' => 185.00,
            'image' => '../image/Shop/Luxe Basics/Luxe Cotton Biker Shorts.jpeg',
            'rating' => 4.7,
            'sold' => 670
            ,'description' => 'Cotton biker shorts with luxe finish; comfortable and form-flattering for workouts or casual wear.'
        ],
        // Product 12
        [
            'name' => 'Ribbed Square Neck Long Sleeve',
            'price' => '₱265',
            'raw_price' => 265.00,
            'image' => '../image/Shop/Luxe Basics/Ribbed Square Neck Long Sleeve.jpeg',
            'rating' => 4.3,
            'sold' => 2500
            ,'description' => 'Ribbed long sleeve with square neckline providing elegant shape and comfortable stretch.'
        ],
        // Product 13
        [
            'name' => 'Satin Slip Maxi Skirt',
            'price' => '₱350',
            'raw_price' => 350.00,
            'image' => '../image/Shop/Luxe Basics/Satin Slip Maxi Skirt.jpeg',
            'rating' => 4.1,
            'sold' => 4200
            ,'description' => 'Satin slip maxi skirt with smooth finish and graceful movement for dressy occasions.'
        ],
        // Product 14
        [
            'name' => 'Premium Oversized Sweatshirt',
            'price' => '₱165',
            'raw_price' => 165.00,
            'image' => '../image/Shop/Luxe Basics/Premium Oversized Sweatshirt.jpeg',
            'rating' => 4.9,
            'sold' => 3100
            ,'description' => 'Oversized sweatshirt in a premium fabric blend with a cozy interior and relaxed fit.'
        ],
        // Product 15
        [
            'name' => 'Old Money Knitted Vest',
            'price' => '₱299.00',
            'raw_price' => 299.00,
            'image' => '../image/Shop/Luxe Basics/Old Money Knitted Vest.jpeg',
            'rating' => 4.0,
            'sold' => 1800
            ,'description' => 'Knitted vest with retro-inspired design delivering texture and versatile layering options.'
        ]
    ];
    return; // Stop processing
}
?>
