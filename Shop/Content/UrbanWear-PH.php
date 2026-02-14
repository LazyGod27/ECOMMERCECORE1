<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=UW&background=000000&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>UrbanWear PH</h1>
                <p>Streetwear & Casual Outfits</p>
            </div>
        </div>
        <!-- Sort Controls -->
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
// Option to Define Custom Products List (Overrides Random Loop)
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'H&M Loose Fit Hoodie',
            'price' => '₱999',
            'raw_price' => 999,
            'image' => '../image/Shop/UrbanWear PH/hoodie.avif', // Path to your image
            'rating' => 4.5,
            'sold' => 433
            ,'description' => 'Loose-fit hoodie in soft cotton-blend fabric. Features a roomy kangaroo pocket and adjustable drawstrings for casual comfort.'
            ,'variants' => [
             'Black' => ['image' => '../image/Shop/UrbanWear PH/hoodie.avif', 'color' => '#111111'],
            'Grey' => ['image' => '../image/Shop/UrbanWear PH/blue.avif', 'color' => '#6b7280'],
            'Navy' => ['image' => '../image/Shop/UrbanWear PH/gray.avif', 'color' => '#1e293b'],
            'brown' => ['image' => '../image/Shop/UrbanWear PH/brown.avif', 'color' => '#8b4513'],
            'green' => ['image' => '../image/Shop/UrbanWear PH/choco.avif', 'color' => '#228b22'],
]
        ],
        // Product 2
        [
            'name' => 'Denim Pants',
            'price' => '₱4,108.06',
            'raw_price' => 4108.06,
            'image' => '../image/Shop/UrbanWear PH/denim1.avif', // Path to your image
            'rating' => 4.2,
            'sold' => 2400
            ,'description' => 'Mom Slim-Fit High-Waist Ankle Jeans.'
            ,'variants' => [
             'Black' => ['image' => '../image/Shop/UrbanWear PH/denim1.avif', 'color' => '#111111'],
            'Grey' => ['image' => '../image/Shop/UrbanWear PH/black.avif', 'color' => '#6b7280'],
            'Navy' => ['image' => '../image/Shop/UrbanWear PH/white.avif', 'color' => '#1e293b'],
            'brown' => ['image' => '../image/Shop/UrbanWear PH/light.avif', 'color' => '#8b4513'],
            'green' => ['image' => '../image/Shop/UrbanWear PH/light1.avif', 'color' => '#228b22'],
]
        ],
        // Product 3
        [
            'name' => 'Team SKOOP Denim Jacket',
            'price' => '₱7,999.00',
            'raw_price' => 7999.00,
            'image' => '../image/Shop/UrbanWear PH/Team SKOOP Denim Jacket.jpeg',
            'rating' => 4.8,
            'sold' => 4700
            ,'description' => 'Classic denim jacket with reinforced stitching and multiple pockets. Durable, structured fit that layers well over tees and hoodies.'
        ],
        // Product 4
        [
            'name' => 'Adidas MU Tracksuit Jacket',
            'price' => '₱3,800.00',
            'raw_price' => 3800.00,
            'image' => '../image/Shop/UrbanWear PH/Adidas MU Tracksuit Jacket.jpeg',
            'rating' => 4.1,
            'sold' => 1100
            ,'description' => 'Sporty tracksuit jacket with breathable fabric and zip front. Designed for warm-ups and casual streetwear with moisture-wicking performance.'
        ],
        // Product 5
        [
            'name' => 'Baggy Denim Jeans',
            'price' => '₱288.00',
            'raw_price' => 288.00,
            'image' => '../image/Shop/UrbanWear PH/Baggy Denim Jeans.jpeg', // Using fallback
            'rating' => 4.5,
            'sold' => 4500
            ,'description' => 'Relaxed-fit baggy denim made from sturdy cotton. Roomy leg silhouette and durable seams for everyday wear and street style looks.'
        ],
        // Product 6
        [
            'name' => 'GentEssential Korean Cargo Jogger Pants',
            'price' => '₱187.00',
            'raw_price' => 187.00,
            'image' => '../image/Shop/UrbanWear PH/GentEssential Korean Cargo Jogger Pants.jpeg',
            'rating' => 4.0,
            'sold' => 5000
            ,'description' => 'Lightweight cargo joggers with tapered ankle and multiple utility pockets. Comfortable elastic waistband suitable for active, casual wear.'
        ],
        // Product 7
        [
            'name' => 'Branded Mens Twill Cargo Jogger Pants',
            'price' => '₱699.00',
            'raw_price' => 699.00,
            'image' => '../image/Shop/UrbanWear PH/Branded Men\'s Twill Cargo Jogger Pants.jpeg',
            'rating' => 4.3,
            'sold' => 3200
            ,'description' => 'Twill cargo joggers offering durability and structure with comfortable stretch. Reinforced pockets and a modern tapered fit.'
        ],
        // Product 8
        [
            'name' => 'Hot Big Pockets Cargo Pants',
            'price' => '₱1,299.00',
            'raw_price' => 1299.00,
            'image' => '../image/Shop/UrbanWear PH/Hot Big Pockets Cargo Pants.jpeg',
            'rating' => 4.6,
            'sold' => 1200
            ,'description' => 'Utility cargo pants with oversized pockets for a bold functional look. Made from mid-weight fabric for structure and longevity.'
        ],
        // Product 9
        [
            'name' => 'Pants',
            'price' => '₱2,597.00',
            'raw_price' => 2597.00,
            'image' => '../image/Shop/UrbanWear PH/Pants.jpeg',
            'rating' => 4.9,
            'sold' => 890
            ,'description' => 'Versatile mid-weight pants suitable for daily wear. Clean lines and neutral styling make these easy to pair with multiple looks.'
        ],
        // Product 10
        [
            'name' => 'Harajuku Fashion Techwear Cargo Pants',
            'price' => '₱890.00',
            'raw_price' => 890.00,
            'image' => '../image/Shop/UrbanWear PH/Pant.jpeg',
            'rating' => 4.7,
            'sold' => 500
            ,'description' => 'Techwear-inspired cargo pants with water-resistant finish and ergonomic pocket placement. Ideal for urban exploration and layered outfits.'
        ],
        // Product 11
        [
            'name' => 'Men H&M Loose Fit Sweatshirt',
            'price' => '₱899.00',
            'raw_price' => 899.00,
            'image' => '../image/Shop/UrbanWear PH/Men H&M Loose Fit Sweatshirt.jpeg',
            'rating' => 4.4,
            'sold' => 6000
            ,'description' => 'Soft loose-fit sweatshirt with ribbed cuffs and hem. Brushed interior for warmth and a relaxed silhouette for everyday comfort.'
        ],
        // Product 12
        [
            'name' => 'Philippines Baybayin Hoodie',
            'price' => '₱348.00',
            'raw_price' => 348.00,
            'image' => '../image/Shop/UrbanWear PH/Philippines Baybayin Hoodie.jpeg',
            'rating' => 4.5,
            'sold' => 1500
            ,'description' => 'Unique hoodie featuring Baybayin-inspired artwork. Lightweight fleece with printed graphics that showcase local culture.'
        ],
        // Product 13
        [
            'name' => 'Pilipinas AOP Hoodie',
            'price' => '₱3,500',
            'raw_price' => 3500,
            'image' => '../image/Shop/UrbanWear PH/Pilipinas AOP Hoodie.jpeg',
            'rating' => 4.8,
            'sold' => 900
            ,'description' => 'All-over print (AOP) hoodie with vibrant, fade-resistant colors. Comfortable fit and eye-catching design for statement looks.'
        ],
        // Product 14
        [
            'name' => 'solid street drip',
            'price' => '₱1,399.00',
            'raw_price' => 1399.00,
            'image' => '../image/Shop/UrbanWear PH/solid street drip.avif',
            'rating' => 4.1,
            'sold' => 2100
            ,'description' => 'Minimalist streetwear piece with clean construction and premium finishing. Designed for layered urban outfits.'
        ],
        // Product 15
        [
            'name' => 'Graphic Street Tee',
            'price' => '₱499.00',
            'raw_price' => 499.00,
            'image' => '../image/Shop/UrbanWear PH/Graphic Street Tee.jpeg', // Fallback for cap
            'rating' => 4.2,
            'sold' => 800
            ,'description' => 'Printed graphic tee made from preshrunk cotton. Bold front artwork and a comfortable regular fit.'
            ,'variants' => [
             'Black' => ['image' => '../image/Shop/UrbanWear PH/H&M Loose Fit Hoodie.jpg', 'color' => '#111111'],
            'Grey' => ['image' => '../image/Shop/UrbanWear PH/H&M Loose Fit Hoodie-grey.jpg', 'color' => '#6b7280'],
            'Navy' => ['image' => '../image/Shop/UrbanWear PH/H&M Loose Fit Hoodie-navy.jpg', 'color' => '#1e293b']
]
        ],
    ];
    return; // Stop processing
}
// ------------------------------------

if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        [
            'name' => 'H&M Loose Fit Hoodie',
            'price' => '₱976.50',
            'raw_price' => 976.50,
            'image' => '../image/Shop/UrbanWear PH/bllue.av', // Path to your image
            'rating' => 4.5, // 0 to 5
            'sold' => 433
            ,'description' => 'Slim-fit long-sleeved shirt in lightweight, breathable fabric. Tailored silhouette suitable for layering or smart-casual wear.'
            
        ],
        // You can copy the block above to add more unique products!
    ];
    return; // Stop processing
}
// 



