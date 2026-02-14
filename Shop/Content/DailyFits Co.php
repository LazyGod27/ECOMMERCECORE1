<?php
if (isset($rendering_header) && $rendering_header) {
    ?>
    <div class="store-header-panel">
        <div class="store-info">
            <img src="https://ui-avatars.com/api/?name=DF&background=27ae60&color=fff&size=64" alt="Logo"
                class="store-logo-small">
            <div class="store-details">
                <h1>DailyFits Co</h1>
                <p>Streetwear & Casual Outfits</p>
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
// Custom Products List for DailyFits Co
// ------------------------------------
if (isset($definingProducts) && $definingProducts) {
    $manualProducts = [
        // Product 1
        [
            'name' => 'Black Everyday Joggers',
            'price' => '₱290.00',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/DailyFits Co/Black Everyday Joggers.jpeg',
            'rating' => 4.8,
            'sold' => 1020
            ,'description' => 'Classic black joggers with a comfortable stretch waistband and tapered ankle for daily wear.'
        ],
        // Product 2
        [
            'name' => 'Studio Mist Loose Fit Joggers',
            'price' => '₱1,195',
            'raw_price' => 1099.00,
            'image' => '../image/Shop/DailyFits Co/Studio Mist Loose Fit Joggers.jpeg',
            'rating' => 4.5,
            'sold' => 3400
                ,'description' => 'Loose-fit studio joggers made from soft fabric for relaxed comfort and easy movement.'
        ],
        // Product 3
        [
            'name' => 'Midnight Miles Loose Fit Joggers',
            'price' => '₱990.00',
            'raw_price' => 2299.00,
            'image' => '../image/Shop/DailyFits Co/Midnight Miles Loose Fit Joggers.jpeg',
            'rating' => 4.7,
            'sold' => 850
                ,'description' => 'Midnight-toned loose-fit joggers with durable stitching and breathable material for everyday use.'
        ],
        // Product 4
        [
            'name' => 'Pencil Skirt with Slit',
            'price' => '₱229.00',
            'raw_price' => 95.00,
            'image' => '../image/Shop/DailyFits Co/Pencil Skirt with Slit.jpeg',
            'rating' => 4.9,
            'sold' => 540
                ,'description' => 'Elegant pencil skirt featuring a slit for mobility and a flattering, office-ready silhouette.'
        ],
        // Product 5
        [
            'name' => 'Heavyweight American Print Tee',
            'price' => '₱199.00',
            'raw_price' => 95.00,
            'image' => '../image/Shop/DailyFits Co/Heavyweight American Print Tee.jpeg',
            'rating' => 4.3,
            'sold' => 2100
                ,'description' => 'Heavyweight printed tee with a vintage-inspired American graphic and comfortable cotton feel.'
        ],
        // Product 6
        [
            'name' => 'Skinny Jeans',
            'price' => '₱450',
            'raw_price' => 2295.00,
            'image' => '../image/Shop/DailyFits Co/Skinny Jeans.jpeg',
            'rating' => 4.6,
            'sold' => 3000
                ,'description' => 'Stretchy skinny jeans with a modern fit and durable wash for everyday styling.'
        ],
        // Product 7
        [
            'name' => 'Hylie Jane Lettuce Blouse',
            'price' => '₱249',
            'raw_price' => 1500.00,
            'image' => '../image/Shop/DailyFits Co/Hylie Jane Lettuce Blouse.jpeg',
            'rating' => 4.8,
            'sold' => 1500
                ,'description' => 'Delicate lettuce-edge blouse with a feminine fit that pairs well with skirts and jeans.'
        ],
        // Product 8
        [
            'name' => 'Candy Plus-Size Thick Pants',
            'price' => '₱1,250',
            'raw_price' => 350.00,
            'image' => '../image/Shop/DailyFits Co/Candy Plus-Size Thick Pants.jpeg',
            'rating' => 4.2,
            'sold' => 1100
                ,'description' => 'Plus-size thick pants with supportive fabric and comfortable fit designed for extended wear.'
        ],
        // Product 9
        [
            'name' => 'Square Neck Classy Top',
            'price' => '₱349',
            'raw_price' => 1100.00,
            'image' => '../image/Shop/DailyFits Co/Square Neck Classy Top.jpeg',
            'rating' => 4.5,
            'sold' => 980
                ,'description' => 'Square-neck top crafted for a classy finish and flattering neckline suitable for many occasions.'
        ],
        // Product 10
        [
            'name' => 'Vintage Embroidery Racing Polo',
            'price' => '₱580',
            'raw_price' => 850.00,
            'image' => '../image/Shop/DailyFits Co/Vintage Embroidery Racing Polo.jpeg',
            'rating' => 4.4,
            'sold' => 1300
                ,'description' => 'Vintage embroidered polo with racing-inspired details and premium stitching.'
        ],
        // Product 11
        [
            'name' => 'Hip-Hop Sweatpants (Straight Leg)',
            'price' => '₱180',
            'raw_price' => 1800.00,
            'image' => '../image/Shop/DailyFits Co/Hip-Hop Sweatpants (Straight Leg).jpeg',
            'rating' => 4.7,
            'sold' => 670
                ,'description' => 'Straight-leg hip-hop sweatpants with comfy fabric and street-style detailing.'
        ],
        // Product 12
        [
            'name' => 'Stripe American Style Polo',
            'price' => '₱1,495',
            'raw_price' => 1350.00,
            'image' => '../image/Shop/DailyFits Co/Stripe American Style Polo.jpeg',
            'rating' => 4.3,
            'sold' => 2500
                ,'description' => 'Striped American-style polo with a classic fit and soft cotton blend.'
        ],
        // Product 13
        [
            'name' => 'Corset Puff Sleeve Crop Top',
            'price' => '₱219',
            'raw_price' => 450.00,
            'image' => '../image/Shop/DailyFits Co/Corset Puff Sleeve Crop Top.jpeg',
            'rating' => 4.1,
            'sold' => 4200
                ,'description' => 'Corset-inspired crop top with puff sleeves and structured fit for fashion-forward outfits.'
        ],
        // Product 14
        [
            'name' => 'Scoop Back Plain Blouse',
            'price' => '₱149',
            'raw_price' => 1200.00,
            'image' => '../image/Shop/DailyFits Co/Scoop Back Plain Blouse.jpeg',
            'rating' => 4.9,
            'sold' => 3100
                ,'description' => 'Scoop-back blouse with lightweight fabric and clean silhouette for comfortable wear.'
        ],
        // Product 15
        [
            'name' => 'Hylie Yassi Combi Dress',
            'price' => '₱299',
            'raw_price' => 299.00,
            'image' => '../image/Shop/DailyFits Co/Hylie Yassi Combi Dress.jpeg',
            'rating' => 4.0,
            'sold' => 1800
                ,'description' => 'Versatile combi dress combining comfort and style, suitable for casual and semi-formal events.'
        ]
    ];
    return; // Stop processing
}
?>
