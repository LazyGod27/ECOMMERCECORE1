<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=GL&background=000000&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>GadgetLab PH</h1>
                <p>Budget Electronics</p>
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
// Custom Products List for GadgetLab PH
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'Pro-Bass TWS Earbuds (V5.3)',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/GadgetLab PH/Pro-Bass TWS Earbuds (V5.3).jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Pro-bass true wireless earbuds with punchy bass and stable Bluetooth connectivity.'
        ],
        // Product 2
        [
            'name' => 'Ultra-Slim 10,000mAh Powerbank',
            'price' => '₱899.00',
            'raw_price' => 899.00,
            'image' => '../image/Shop/GadgetLab PH/Ultra-Slim 10,000mAh Powerbank.jpeg',
            'rating' => 4.5,
            'sold' => 3400
            ,'description' => 'Ultra-slim 10,000mAh powerbank offering high-capacity charging in a compact form.'
        ],
        // Product 3
        [
            'name' => 'Smart LED Sunset Lamp',
            'price' => '₱1,250.00',
            'raw_price' => 1250.00,
            'image' => '../image/Shop/GadgetLab PH/Smart LED Sunset Lamp.jpeg',
            'rating' => 4.7,
            'sold' => 850
            ,'description' => 'Smart LED lamp that creates a warm sunset ambiance with adjustable colors and brightness.'
        ],
        // Product 4
        [
            'name' => 'RGB Mechanical Gaming Keyboard',
            'price' => '₱2,800.00',
            'raw_price' => 2800.00,
            'image' => '../image/Shop/GadgetLab PH/RGB Mechanical Gaming Keyboard.jpeg',
            'rating' => 4.9,
            'sold' => 540
            ,'description' => 'Mechanical gaming keyboard with RGB lighting and tactile switches for responsive typing.'
        ],
        // Product 5
        [
            'name' => 'HD 1080p Web Camera (Built-in Mic)',
            'price' => '₱750.00',
            'raw_price' => 750.00,
            'image' => '../image/Shop/GadgetLab PH/HD 1080p Web Camera (Built-in Mic).jpeg',
            'rating' => 4.3,
            'sold' => 2100
            ,'description' => '1080p webcam with built-in mic for clear video calls and simple plug-and-play setup.'
        ],
        // Product 6
        [
            'name' => 'Magnetic Wireless Car Charger',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/GadgetLab PH/Magnetic Wireless Car Charger.jpeg',
            'rating' => 4.6,
            'sold' => 3000
            ,'description' => 'Magnetic wireless car charger that snaps onto compatible phones for secure charging while driving.'
        ],
        // Product 7
        [
            'name' => 'Portable Mini Humidifier (USB)',
            'price' => '₱1,500.00',
            'raw_price' => 1500.00,
            'image' => '../image/Shop/GadgetLab PH/Portable Mini Humidifier (USB).jpeg',
            'rating' => 4.8,
            'sold' => 1500
            ,'description' => 'USB-powered mini humidifier for desks with quiet operation and easy refilling.'
        ],
        // Product 8
        [
            'name' => '65W GaN Fast Charger Adaptor',
            'price' => '₱350.00',
            'raw_price' => 350.00,
            'image' => '../image/Shop/GadgetLab PH/65W GaN Fast Charger Adaptor.jpeg', // Using stylized image
            'rating' => 4.2,
            'sold' => 1100
            ,'description' => '65W GaN charger offering fast, efficient charging for laptops and mobile devices.'
        ],
        // Product 9
        [
            'name' => 'Vertical Ergonomic Wireless Mouse',
            'price' => '₱1,100.00',
            'raw_price' => 1100.00,
            'image' => '../image/Shop/GadgetLab PH/Vertical Ergonomic Wireless Mouse.jpeg',
            'rating' => 4.5,
            'sold' => 980
            ,'description' => 'Vertical ergonomic mouse designed to reduce wrist strain during extended computer sessions.'
        ],
        // Product 10
        [
            'name' => 'Clip-on Reading Ring Light',
            'price' => '₱850.00',
            'raw_price' => 850.00,
            'image' => '../image/Shop/GadgetLab PH/Clip-on Reading Ring Light.jpeg',
            'rating' => 4.4,
            'sold' => 1300
            ,'description' => 'Clip-on ring light for reading or video calls with adjustable brightness settings.'
        ],
        // Product 11
        [
            'name' => 'Bluetooth Smart Fitness Tracker',
            'price' => '₱1,800.00',
            'raw_price' => 1800.00,
            'image' => '../image/Shop/GadgetLab PH/Bluetooth Smart Fitness Tracker.jpeg',
            'rating' => 4.7,
            'sold' => 670
            ,'description' => 'Waterproof fitness tracker with activity and sleep tracking plus Bluetooth syncing.'
        ],
        // Product 12
        [
            'name' => 'Noise-Cancelling Wired Headset',
            'price' => '₱1,350.00',
            'raw_price' => 1350.00,
            'image' => '../image/Shop/GadgetLab PH/Noise-Cancelling Wired Headset.jpeg',
            'rating' => 4.3,
            'sold' => 2500
            ,'description' => 'Noise-cancelling wired headset with comfortable padding and clear audio for calls.'
        ],
        // Product 13
        [
            'name' => 'Multi-Port USB-C Hub (5-in-1)',
            'price' => '₱450.00',
            'raw_price' => 450.00,
            'image' => '../image/Shop/GadgetLab PH/Multi-Port USB-C Hub (5-in-1).jpeg',
            'rating' => 4.1,
            'sold' => 4200
            ,'description' => '5-in-1 USB-C hub providing extra ports for storage, HDMI and peripherals.'
        ],
        // Product 14
        [
            'name' => 'Foldable Laptop Desk Stand',
            'price' => '₱1,200.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/GadgetLab PH/Foldable Laptop Desk Stand.jpeg',
            'rating' => 4.9,
            'sold' => 3100
            ,'description' => 'Foldable laptop desk stand with adjustable height for improved ergonomics.'
        ],
        // Product 15
        [
            'name' => 'Universal Stylus Pen',
            'price' => '₱299.00',
            'raw_price' => 299.00,
            'image' => '../image/Shop/GadgetLab PH/Universal Stylus Pen.jpeg',
            'rating' => 4.0,
            'sold' => 1800
            ,'description' => 'Universal stylus pen compatible with capacitive touchscreens for precise input.'
        ]
    ];
    return; // Stop processing
}
?>
