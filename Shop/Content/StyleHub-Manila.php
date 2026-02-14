<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=SH&background=d68910&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>StyleHub Manila</h1>
                <p>Trendy Men & Women Fashion</p>
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
// Custom Products List for StyleHub Manila
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'Adjustable Vest Top',
            'price' => '₱480.00',
            'raw_price' => 480.00,
            'image' => '../image/Shop/StyleHub Manila/Adjustable Vest Top.jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Adjustable vest top with a fitted shape and soft fabric — ideal for layered summer looks.'
        ],
        // Product 2
        [
            'name' => 'Korean Style Oversized Shirt',
            'price' => '₱650.00',
            'raw_price' => 650.00,
            'image' => '../image/Shop/StyleHub Manila/Korean Style Oversized Shirt.jpeg',
            'rating' => 4.5,
            'sold' => 3400
            ,'description' => 'Oversized Korean-style shirt in breathable fabric for relaxed everyday or streetwear looks.'
        ],
        // Product 3
        [
            'name' => 'Summer Floral Dress',
            'price' => '₱1,250.00',
            'raw_price' => 1250.00,
            'image' => '../image/Shop/StyleHub Manila/Summer Floral Dress.jpeg', // Fallback as I don't have a dress img, reusing generic or similar
            'rating' => 4.7,
            'sold' => 850
            ,'description' => 'Lightweight floral dress with breathable material and a flattering silhouette for warm days.'
        ],
        // Product 4
        [
            'name' => 'Ivana Puff Sleeve Formal Dress',
            'price' => '₱3,999.00',
            'raw_price' => 3999.00,
            'image' => '../image/Shop/StyleHub Manila/Ivana Puff Sleeve Formal Dress.jpeg',
            'rating' => 4.9,
            'sold' => 540
            ,'description' => 'Elegant puff-sleeve formal dress with delicate details and structured fit for special occasions.'
        ],
        // Product 5
        [
            'name' => 'Tyla Linen Dress',
            'price' => '₱1,250.00',
            'raw_price' => 1250.00,
            'image' => '../image/Shop/StyleHub Manila/Tyla Linen Dress.jpeg',
            'rating' => 4.3,
            'sold' => 2100
            ,'description' => 'Linen dress with a breezy fit and natural texture, perfect for summer or resort wear.'
        ],
        // Product 6
        [
            'name' => 'HUILISHI Korean Plain Men’s Long Sleeve Shirt',
            'price' => '₱2,250.00',
            'raw_price' => 2250.00,
            'image' => '../image/Shop/StyleHub Manila/HUILISHI Korean Plain Men’s Long Sleeve Shirt.jpeg', // Fallback
            'rating' => 4.6,
            'sold' => 3000
            ,'description' => 'Classic long-sleeve men\'s shirt with clean lines and versatile styling for smart-casual wear.'
        ],
        // Product 7
        [
            'name' => 'INCERUN Korean Style Men Casual Shirt',
            'price' => '₱4,601.00',
            'raw_price' => 4601.00,
            'image' => '../image/Shop/StyleHub Manila/INCERUN Korean Style Men Casual Shirt.jpeg',
            'rating' => 4.8,
            'sold' => 1500
            ,'description' => 'Casual men\'s shirt crafted in lightweight fabric for comfortable all-day wear.'
        ],
        // Product 8
        [
            'name' => 'Giordano Korean Style Summer Men’s Polo',
            'price' => '₱2,980.00',
            'raw_price' => 2980.00,
            'image' => '../image/Shop/StyleHub Manila/Giordano Korean Style Summer Men’s Polo.jpeg', // Using stylized image
            'rating' => 4.2,
            'sold' => 1100
            ,'description' => 'Summer polo with soft knit and breathable construction — great for warm-weather layering.'
        ],
        // Product 9
        [
            'name' => 'Chicly Korean Men’s Casual Long Sleeve Polo',
            'price' => '₱2,290.00',
            'raw_price' => 1100.00,
            'image' => '../image/Shop/StyleHub Manila/Chicly Korean Men’s Casual Long Sleeve Polo.jpeg',
            'rating' => 4.5,
            'sold' => 980
            ,'description' => 'Casual long sleeve polo with refined stitching and versatile style for day-to-day wear.'
        ],
        // Product 10
        [
            'name' => 'Korea Floral Polo For Men',
            'price' => '₱3,490.00',
            'raw_price' => 2290.00,
            'image' => '../image/Shop/StyleHub Manila/Korea Floral Polo For Men.jpeg',
            'rating' => 4.4,
            'sold' => 1300
            ,'description' => 'Floral polo designed for a modern fit; soft fabric and subtle patterning for a polished casual look.'
        ],
        // Product 11
        [
            'name' => 'Denim Streetwear Jacket',
            'price' => '₱6,400.00',
            'raw_price' => 6400.00,
            'image' => '../image/Shop/StyleHub Manila/Denim Streetwear Jacket.jpeg',
            'rating' => 4.7,
            'sold' => 670
            ,'description' => 'Durable denim jacket with streetwear-inspired details and a comfortable cut for layering.'
        ],
        // Product 12
        [
            'name' => 'Graphic Tee (Astig Design)',
            'price' => '₱7,550.00',
            'raw_price' => 7550.00,
            'image' => '../image/Shop/StyleHub Manila/Graphic Tee (Astig Design).jpeg',
            'rating' => 4.3,
            'sold' => 2500
            ,'description' => 'Graphic tee featuring bold artwork printed on premium cotton for long-lasting wear.'
        ],
        // Product 13
        [
            'name' => 'Bucket Hat (Streetwear)',
            'price' => '₱8,450.00',
            'raw_price' => 1450.00,
            'image' => '../image/Shop/StyleHub Manila/Bucket Hat (Streetwear).jpeg',
            'rating' => 4.1,
            'sold' => 4200
            ,'description' => 'Street-style bucket hat made from lightweight fabric — a versatile accessory for sun protection.'
        ],
        // Product 14
        [
            'name' => 'Ripped Jogger Pants',
            'price' => '₱11,302.00',
            'raw_price' => 11302.00,
            'image' => '../image/Shop/StyleHub Manila/Ripped Jogger Pants.jpeg',
            'rating' => 4.9,
            'sold' => 3100
            ,'description' => 'Ripped jogger pants with a modern cut and comfortable stretch for an edgy street look.'
        ],
        // Product 15
        [
            'name' => 'Button‑Up Street Shirt',
            'price' => '₱24,999.00',
            'raw_price' => 12999.00,
            'image' => '../image/Shop/StyleHub Manila/Button‑Up Street Shirt.jpeg',
            'rating' => 4.0,
            'sold' => 1800
            ,'description' => 'Button-up street shirt with clean tailoring and contemporary detailing for smart-casual outfits.'
        ]
    ];
    return; // Stop processing
}
?>
