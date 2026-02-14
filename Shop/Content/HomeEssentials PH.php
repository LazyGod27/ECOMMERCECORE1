<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=HE&background=8e44ad&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>HomeEssentials PH</h1>
                <p>Latest Home Products</p>
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
// Custom Products List for HomeEssentials PH
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'Quikfab 5-Tier Steel Storage Rack',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/HomeEssentials PH/Quikfab 5-Tier Steel Storage Rack.jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Sturdy 5-tier steel rack for home storage with easy assembly and high load capacity.'
        ],
        // Product 2
        [
            'name' => 'Megabox 5-Layer Drawer (Beige)',
            'price' => '₱899.00',
            'raw_price' => 899.00,
            'image' => '../image/Shop/HomeEssentials PH/Megabox 5-Layer Drawer (Beige).jpeg',
            'rating' => 4.5,
            'sold' => 3400
            ,'description' => 'Versatile 5-layer plastic drawer unit for organized storage and space-saving convenience.'
        ],
        // Product 3
        [
            'name' => 'Our Home Copenhagen Sectional Sofa',
            'price' => '₱1,250.00',
            'raw_price' => 1250.00,
            'image' => '../image/Shop/HomeEssentials PH/Our Home Copenhagen Sectional Sofa.jpeg',
            'rating' => 4.7,
            'sold' => 850
            ,'description' => 'Comfortable sectional sofa with modular pieces and durable upholstery for family living rooms.'
        ],
        // Product 4
        [
            'name' => 'IKEA RÖDFLIK Desk Lamp',
            'price' => '₱2,800.00',
            'raw_price' => 2800.00,
            'image' => '../image/Shop/HomeEssentials PH/IKEA RÖDFLIK Desk Lamp.jpeg',
            'rating' => 4.9,
            'sold' => 540
            ,'description' => 'Minimal desk lamp with adjustable neck and warm LED light for reading or workspace tasks.'
        ],
        // Product 5
        [
            'name' => 'Nordic Wood Legs Coffee Table',
            'price' => '₱750.00',
            'raw_price' => 750.00,
            'image' => '../image/Shop/HomeEssentials PH/Nordic Wood Legs Coffee Table.jpeg',
            'rating' => 4.3,
            'sold' => 2100
            ,'description' => 'Nordic-inspired coffee table with wooden legs and a smooth tabletop for a clean aesthetic.'
        ],
        // Product 6
        [
            'name' => '13-Piece Non-Stick Cookware Set',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/HomeEssentials PH/13-Piece Non-Stick Cookware Set.jpeg',
            'rating' => 4.6,
            'sold' => 3000
            ,'description' => '13-piece non-stick cookware set with even heat distribution and easy cleaning surfaces.'
        ],
        // Product 7
        [
            'name' => 'HODEKT Electric Water Kettle (2.3L)',
            'price' => '₱1,500.00',
            'raw_price' => 1500.00,
            'image' => '../image/Shop/HomeEssentials PH/HODEKT Electric Water Kettle (2.3L).jpeg',
            'rating' => 4.8,
            'sold' => 1500
            ,'description' => 'Fast-boiling electric kettle with safety auto-shutoff and 2.3L capacity.'
        ],
        // Product 8
        [
            'name' => 'Home Essentials Silver Metal Coffee Press',
            'price' => '₱350.00',
            'raw_price' => 350.00,
            'image' => '../image/Shop/HomeEssentials PH/Home Essentials Silver Metal Coffee Press.jpeg', // Using stylized image
            'rating' => 4.2,
            'sold' => 1100
            ,'description' => 'Stainless metal coffee press delivering rich, full-flavored brews with easy cleanup.'
        ],
        // Product 9
        [
            'name' => 'Orocan 45L Ice Box (Insulated)',
            'price' => '₱1,100.00',
            'raw_price' => 1100.00,
            'image' => '../image/Shop/HomeEssentials PH/Orocan 45L Ice Box (Insulated).jpeg',
            'rating' => 4.5,
            'sold' => 980
            ,'description' => 'Insulated 45L ice box to keep food and drinks cold for outdoor gatherings and trips.'
        ],
        // Product 10
        [
            'name' => 'Westinghouse Electronic Bathroom Scale',
            'price' => '₱850.00',
            'raw_price' => 850.00,
            'image' => '../image/Shop/HomeEssentials PH/Westinghouse Electronic Bathroom Scale.jpeg',
            'rating' => 4.4,
            'sold' => 1300
            ,'description' => 'Accurate digital bathroom scale with sleek design and easy-to-read display.'
        ],
        // Product 11
        [
            'name' => 'Wall-Mounted Vegetable & Spice Organizer',
            'price' => '₱1,800.00',
            'raw_price' => 1800.00,
            'image' => '../image/Shop/HomeEssentials PH/Wall-Mounted Vegetable & Spice Organizer.jpeg',
            'rating' => 4.7,
            'sold' => 670
            ,'description' => 'Wall-mounted organizer for spices and veggies that saves counter space and keeps things tidy.'
        ],
        // Product 12
        [
            'name' => 'Eko 12L Stella Step Bin',
            'price' => '₱1,350.00',
            'raw_price' => 1350.00,
            'image' => '../image/Shop/HomeEssentials PH/Eko 12L Stella Step Bin.jpeg',
            'rating' => 4.3,
            'sold' => 2500
            ,'description' => 'Durable step bin with hands-free pedal operation and a compact 12L capacity.'
        ],
        // Product 13
        [
            'name' => 'Scrub Daddy (The Original Sponge)',
            'price' => '₱450.00',
            'raw_price' => 450.00,
            'image' => '../image/Shop/HomeEssentials PH/Scrub Daddy (The Original Sponge).jpeg',
            'rating' => 4.1,
            'sold' => 4200
            ,'description' => 'Original Scrub Daddy sponge — versatile, long-lasting and gentle on cookware.'
        ],
        // Product 14
        [
            'name' => 'ACE Mop Bucket & Wringer (19L)',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/HomeEssentials PH/ACE Mop Bucket & Wringer (19L).jpeg',
            'rating' => 4.9,
            'sold' => 3100
            ,'description' => 'Heavy-duty mop bucket with wringer for efficient mopping and quick water removal.'
        ],
        // Product 15
        [
            'name' => 'Hava Asia Stainless Steel Towel Shelf',
            'price' => '₱299.00',
            'raw_price' => 299.00,
            'image' => '../image/Shop/HomeEssentials PH/Hava Asia Stainless Steel Towel Shelf.jpeg',
            'rating' => 4.0,
            'sold' => 1800
            ,'description' => 'Stainless steel towel shelf offering corrosion resistance and sleek bathroom storage.'
        ]
    ];
    return; // Stop processing
}
?>
