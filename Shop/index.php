<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <title>
        <?php echo isset($_GET['store']) ? htmlspecialchars(urldecode($_GET['store'])) . ' - IMARKET PH' : 'SHOP NOW - IMARKET PH'; ?>
    </title>
</head>

<body>
    <nav>
        <?php $path_prefix = '../';
        include '../Components/header.php'; ?>
    </nav>

    <!-- Link Shop CSS after header to ensure it takes precedence or cascades correctly -->
    <link rel="stylesheet" href="../css/shop/shop.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/shop/shop_landing.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/components/product-details.css?v=<?php echo time(); ?>">

    <style>
        .variant-swatches{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap}
        .variant-swatch{width:40px;height:40px;border-radius:6px;border:2px solid transparent;padding:0;cursor:pointer;background:#f0f0f0;overflow:hidden;background-size:cover;background-position:center;position:relative;transition:all 0.2s}
        .variant-swatch:hover{transform:scale(1.08);border-color:#999}
        .variant-swatch.selected{border-color:#3b82f6;box-shadow:0 0 0 2px #3b82f6;transform:scale(1.15)}
        .variant-swatch img{width:100%;height:100%;object-fit:cover;display:block}
        .pv-option-btn.selected{box-shadow:inset 0 -3px 0 rgba(0,0,0,0.06)}
    </style>

    <div class="content">
        <div class="shop-container">
            <?php
            // Define Shops Array
            $shops = [
                [
                    "name" => "UrbanWear PH",
                    "category" => "Streetwear & Casual Outfits",
                    "rating" => "4.8",
                    "sold" => "25k",
                    "initials" => "UW",
                    "bg" => "000000"
                ],
                [
                    "name" => "StyleHub Manila",
                    "category" => "Trendy Men & Women Fashion",
                    "rating" => "4.9",
                    "sold" => "18k",
                    "initials" => "SH",
                    "bg" => "d68910"
                ],
                [
                    "name" => "DailyFits Co.",
                    "category" => "Pang-daily na damit",
                    "rating" => "4.7",
                    "sold" => "30k",
                    "initials" => "DF",
                    "bg" => "27ae60"
                ],
                [
                    "name" => "LuxeBasics",
                    "category" => "Minimalist & Basic Wear",
                    "rating" => "4.8",
                    "sold" => "22k",
                    "initials" => "LB",
                    "bg" => "333333"
                ],
                [
                    "name" => "TechZone PH",
                    "category" => "Gadgets & Accessories",
                    "rating" => "4.9",
                    "sold" => "40k",
                    "initials" => "TZ",
                    "bg" => "2980b9"
                ],
                [
                    "name" => "SmartGear Store",
                    "category" => "Phone Accessories",
                    "rating" => "4.8",
                    "sold" => "28k",
                    "initials" => "SG",
                    "bg" => "2c3e50"
                ],
                [
                    "name" => "GadgetLab PH",
                    "category" => "Budget Electronics",
                    "rating" => "4.6",
                    "sold" => "15k",
                    "initials" => "GL",
                    "bg" => "e67e22"
                ],
                [
                    "name" => "CozyLiving Store",
                    "category" => "Home Decor",
                    "rating" => "4.7",
                    "sold" => "14k",
                    "initials" => "CL",
                    "bg" => "8e44ad"
                ],
                [
                    "name" => "GlowUp Beauty",
                    "category" => "Skincare & Makeup",
                    "rating" => "4.9",
                    "sold" => "20k",
                    "initials" => "GU",
                    "bg" => "e91e63"
                ],
                [
                    "name" => "FreshLook PH",
                    "category" => "Personal Care Products",
                    "rating" => "4.8",
                    "sold" => "17k",
                    "initials" => "FL",
                    "bg" => "1abc9c"
                ],
                [
                    "name" => "HomeEssentials PH",
                    "category" => "Kitchen & Home Items",
                    "rating" => "4.8",
                    "sold" => "35k",
                    "initials" => "HE",
                    "bg" => "c0392b"
                ],
                [
                    "name" => "TrendyBags PH",
                    "category" => "Stylish Bags",
                    "rating" => "4.7",
                    "sold" => "14k",
                    "initials" => "TB",
                    "bg" => "795548"
                ]
            ];

            // Mock Products Data
            // Mock Products Data with Search Support
            function getMockProducts($storeName, $searchQuery = '')
            {
                // Generate some deterministic mock products based on store name
                $seed = crc32($storeName);
                srand($seed);

                $products = [];
                $productNames = ['T-Shirt', 'Jeans', 'Sneakers', 'Watch', 'Headphones', 'Bag', 'Lamp', 'Phone Case', 'Lipstick', 'Coffee Maker', 'Hoodie', 'Socks', 'Cap', 'Shorts'];
                $adjectives = ['Classic', 'Premium', 'Basic', 'Stylish', 'Modern', 'Urban', 'Cozy'];
                
                // Product descriptions (base) and adjective modifiers
                $descriptions = [
                    'T-Shirt' => 'Comfortable and breathable cotton t-shirt perfect for everyday wear. Features a classic fit and durable stitching.',
                    'Jeans' => 'Premium denim jeans with a reliable fit. Made from high-quality cotton blend for comfort and durability.',
                    'Sneakers' => 'Comfortable sneakers engineered for daily wear with cushioned soles and breathable materials.',
                    'Watch' => 'Reliable wristwatch with accurate timekeeping, water resistance and a sleek design.',
                    'Headphones' => 'High-fidelity headphones with noise isolation and comfortable ear cups for long listening sessions.',
                    'Bag' => 'Durable bag with smart compartments and reinforced straps for everyday carry.',
                    'Lamp' => 'Energy-efficient LED lamp with adjustable brightness and a modern design.',
                    'Phone Case' => 'Protective slim phone case with shock absorption and scratch resistance.',
                    'Lipstick' => 'Long-lasting lipstick with rich pigmentation and moisturizing formula.',
                    'Coffee Maker' => 'Automatic coffee maker that brews fresh coffee quickly and is easy to clean.',
                    'Hoodie' => 'Cozy hoodie made from soft fabric with a roomy pocket and adjustable hood.',
                    'Socks' => 'Breathable socks designed for comfort and durability during daily use or sports.',
                    'Cap' => 'Adjustable cap offering sun protection and effortless style.',
                    'Shorts' => 'Lightweight shorts made for comfort in warm weather, with practical pockets.'
                ];

                // Adjective-based modifiers to make descriptions vary by product variant
                $adj_modifiers = [
                    'Classic' => 'A timeless design that emphasizes comfort and reliability.',
                    'Premium' => 'Crafted with higher-grade materials for superior comfort and longevity.',
                    'Basic' => 'An affordable, no-frills option that covers essential needs.',
                    'Stylish' => 'Designed with current trends in mind to give a fashionable edge.',
                    'Modern' => 'Contemporary styling with functional improvements and sleek finishes.',
                    'Urban' => 'Street-ready design built for everyday city life and commuter comfort.',
                    'Cozy' => 'Extra soft and warm, perfect for relaxed, comfortable wear.'
                ];

                // 1. Determine Correct Content File (Once)
                $safeStoreName = rtrim($storeName, '.');
                $exactFile = 'Content/' . $safeStoreName . '.php';
                $dashedFile = 'Content/' . str_replace(' ', '-', $safeStoreName) . '.php';
                $fileToLoad = 'Content/UrbanWear-PH.php';

                if (file_exists($exactFile) && filesize($exactFile) > 0) {
                    $fileToLoad = $exactFile;
                } elseif (file_exists($dashedFile) && filesize($dashedFile) > 0) {
                    $fileToLoad = $dashedFile;
                }

                // 2. Check for Manual Product List (No Loop Mode)
                $manualProducts = [];
                $definingProducts = true; // Signal to included file
                if (file_exists($fileToLoad)) {
                    include $fileToLoad;
                }

                if (!empty($manualProducts)) {
                    $sourceProducts = $manualProducts;
                } else {
                    // 3. Fallback: Generate Mock Products Loop
                    $sourceProducts = [];
                    for ($i = 0; $i < 20; $i++) {
                        $price = rand(150, 2500);
                        $origPrice = floor($price * 1.35);
                        $discount = "35% OFF";
                        $productName = $productNames[array_rand($productNames)];
                        $adjective = $adjectives[array_rand($adjectives)];
                        $name = $adjective . ' ' . $productName;
                        $image = 'https://via.placeholder.com/300x400/f5f5f5/999999?text=' . urlencode($name);
                        
                        // Get base description based on product type
                        $baseDesc = $descriptions[$productName] ?? 'High-quality product designed for everyday use. Features superior craftsmanship and durability.';
                        // Pick adjective modifier when available
                        $modifier = $adj_modifiers[$adjective] ?? '';
                        // Compose final description (ensure readability)
                        if (!empty($modifier)) {
                            $description = $baseDesc . ' ' . $modifier;
                        } else {
                            $description = $baseDesc;
                        }

                        $sourceProducts[] = [
                            'name' => $name,
                            'price' => '₱' . number_format($price),
                            'raw_price' => $price,
                            'original_price' => '₱' . number_format($origPrice),
                            'discount' => $discount,
                            'image' => $image,
                            'rating' => 4.0 + (rand(0, 9) / 10),
                            'sold' => rand(100, 5000),
                            'description' => $description,
                            'variants' => [
                                'Black' => ['image' => 'https://via.placeholder.com/600x800/111111/ffffff?text=' . urlencode($name . ' Black'), 'color' => '#111111'],
                                'White' => ['image' => 'https://via.placeholder.com/600x800/ffffff/111111?text=' . urlencode($name . ' White'), 'color' => '#ffffff'],
                                'Blue' => ['image' => 'https://via.placeholder.com/600x800/3b82f6/ffffff?text=' . urlencode($name . ' Blue'), 'color' => '#3b82f6']
                            ]
                        ];
                    }
                }

                // Ensure every product has a `variants` map so front-end swatches work
                foreach ($sourceProducts as $idx => $sp) {
                    if (!isset($sp['variants']) || empty($sp['variants'])) {
                        $prodName = $sp['name'] ?? ('Product' . ($idx + 1));
                        $sourceProducts[$idx]['variants'] = [
                            'Black' => [
                                'image' => 'H&M ' . urlencode($prodName . ' Black'),
                                'color' => '#111111'
                            ],
                            'White' => [
                                'image' => 'https://via.placeholder.com/600x800/ffffff/111111?text=' . urlencode($prodName . ' White'),
                                'color' => '#ffffff'
                            ],
                            'Blue' => [
                                'image' => 'https://via.placeholder.com/600x800/3b82f6/ffffff?text=' . urlencode($prodName . ' Blue'),
                                'color' => '#3b82f6'
                            ]
                        ];
                    }
                }

                // Apply Filtering if search query exists
                if (!empty($searchQuery)) {
                    $filtered = [];
                    foreach ($sourceProducts as $p) {
                        if (stripos($p['name'], $searchQuery) !== false) {
                            $filtered[] = $p;
                        }
                    }
                    return $filtered;
                }

                return $sourceProducts;
            }


            // CHECK: Is a store selected or searching?
            $searchQuery = $_GET['search'] ?? '';
            $selectedStore = $_GET['store'] ?? '';
            $currentShop = $shops[0]; // Default fallback
            
            if (!empty($selectedStore)) {
                $selectedStore = urldecode($selectedStore);
                // Find selected shop details
                foreach ($shops as $s) {
                    if ($s['name'] === $selectedStore) {
                        $currentShop = $s;
                        break;
                    }
                }

                $products = getMockProducts($selectedStore, $searchQuery);

                // --- Sorting Logic ---
                $sort = $_GET['sort'] ?? 'best';
                if ($sort === 'price_asc') {
                    usort($products, fn($a, $b) => $a['raw_price'] <=> $b['raw_price']);
                } elseif ($sort === 'price_desc') {
                    usort($products, fn($a, $b) => $b['raw_price'] <=> $a['raw_price']);
                } elseif ($sort === 'sales') {
                    usort($products, fn($a, $b) => $b['sold'] <=> $a['sold']);
                } elseif ($sort === 'latest') {
                    shuffle($products);
                }
                ?>

                <!-- Mimic Category UI: Link CSS -->
                <link rel="stylesheet" href="../css/components/category-base.css?v=<?php echo time(); ?>">
                <style>
                    /* Specific Overrides for Shop View */
                    .best_selling-container {
                        margin-top: 20px;
                        margin-bottom: 40px;
                        padding: 0 !important;
                        background: transparent;
                        overflow: hidden;
                        border-radius: 20px;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                    }

                    .shop-seller-profile {
                        color: #fff !important;
                    }

                    .shop-seller-profile h2 {
                        color: #fff !important;
                        font-size: 2.5em;
                        text-transform: uppercase;
                        font-weight: 800;
                        line-height: 1.1;
                        margin-top: 5px;
                    }

                    .shop-seller-profile p {
                        color: rgba(255, 255, 255, 0.8) !important;
                        margin-bottom: 0;
                    }

                    .shop-seller-stats {
                        display: flex;
                        gap: 25px;
                        margin: 25px 0;
                        font-size: 1em;
                        color: #fff;
                    }

                    .stat-item {
                        display: flex;
                        flex-direction: column;
                        align-items: flex-start;
                    }

                    .stat-val {
                        font-weight: bold;
                        font-size: 1.25em;
                    }

                    .stat-label {
                        font-size: 0.85em;
                        opacity: 0.7;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }

                    .seller-actions {
                        display: flex;
                        gap: 12px;
                        margin-top: 25px;
                    }

                    .btn-seller-action {
                        padding: 10px 22px;
                        border-radius: 8px;
                        text-decoration: none;
                        font-size: 0.9em;
                        border: 1px solid rgba(255, 255, 255, 0.3);
                        color: white;
                        transition: all 0.2s;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        font-weight: 500;
                    }

                    .btn-seller-action:hover {
                        background: rgba(255, 255, 255, 0.15);
                        border-color: white;
                    }

                    .btn-seller-primary {
                        background: white;
                        color: #111 !important;
                        border: none;
                        font-weight: 700;
                    }

                    .btn-seller-primary:hover {
                        background: #f8f9fa;
                        color: #000 !important;
                        transform: translateY(-2px);
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                    }

                    /* Sort Controls Override */
                    .sort-controls {
                        display: flex;
                        gap: 10px;
                        align-items: center;
                        background: #fff;
                        padding: 8px 16px;
                        border-radius: 50px;
                        border: 1px solid #e2e8f0;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
                    }

                    .sort-btn {
                        padding: 6px 14px;
                        font-size: 0.9em;
                        color: #64748b;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: 600;
                        transition: all 0.2s;
                        border: 1px solid transparent;
                    }

                    .sort-btn:hover {
                        color: #2c4c7c;
                        background: #f1f5f9;
                    }

                    .sort-btn.active {
                        background: #2c4c7c;
                        color: white;
                        box-shadow: 0 4px 6px -1px rgba(44, 76, 124, 0.2);
                    }

                    .sort-label {
                        color: #64748b;
                        font-size: 0.85em;
                        font-weight: 700;
                        text-transform: uppercase;
                        margin-right: 5px;
                        letter-spacing: 0.5px;
                    }
                </style>

                <!-- NEW LAYOUT: Sidebar + Main Content -->
                <div class="store-layout"
                    style="display: flex; gap: 30px; align-items: flex-start; margin-top: 30px; position: relative;">

                    <!-- Sidebar -->
                    <div class="shop-sidebar"
                        style="width: 260px; flex-shrink: 0; background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #f1f5f9; position: sticky; top: 100px; height: fit-content; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <h3 class="sidebar-title"
                            style="margin-top: 0; padding-bottom: 15px; border-bottom: 1px solid #eee; color: #1e293b; font-size: 0.9rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                            All Shops</h3>
                        <ul class="sidebar-list"
                            style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                            <?php foreach ($shops as $shop):
                                $isActive = ($shop['name'] === $selectedStore);
                                $shopInitial = $shop['initials'];
                                $shopColor = $shop['bg'];
                                ?>
                                <li class="sidebar-item" style="margin-bottom: 5px;">
                                    <a href="?store=<?php echo urlencode($shop['name']); ?>"
                                        class="sidebar-link <?php echo $isActive ? 'active' : ''; ?>"
                                        style="display: flex; align-items: center; padding: 10px; text-decoration: none; color: #555; border-radius: 6px; transition: all 0.2s; <?php echo $isActive ? 'background-color: #f0f7ff; color: #2A3B7E; font-weight: 600;' : ''; ?>">

                                        <div class="sidebar-checkbox"
                                            style="margin-right: 10px; width: 30px; height: 30px; background: #<?php echo $shopColor; ?>; color: #fff; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                            <?php echo $shopInitial; ?>
                                        </div>

                                        <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($shop['name']); ?>
                                        </span>

                                        <?php if ($isActive): ?>
                                            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Main Content (Hero + Products) -->
                    <div class="store-main" style="flex: 1; min-width: 0;">

                        <!-- Full Width Hero/Banner -->
                        <div class="best_selling-container"
                            style="display: block; width: 100%; position: relative; padding: 0 !important; margin: 0 !important; border-radius: 0; box-shadow: none; border: none; height: 350px;">

                            <!-- Banner/Slider (Now Full Width) -->
                            <div class="slider-section"
                                style="width: 100%; height: 100%; position: relative; overflow: hidden; background: #f8f8f8; margin: 0;">

                                <!-- Dynamic Dark Background with Shop Color Tint -->
                                <div
                                    style="position: absolute; inset: 0; background: linear-gradient(to right, #<?php echo $currentShop['bg']; ?> 0%, #1a1a1a 100%); opacity: 0.8;">
                                </div>
                                <div style="position: absolute; inset: 0; background: #1a1a1a; opacity: 0.4;"></div>

                                <!-- Geometric Pattern Overlay -->
                                <div
                                    style="position: absolute; inset: 0; background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px; opacity: 0.03;">
                                </div>
                                
                                <!-- Store Name as Subtle Watermark -->
                                <div style="position: absolute; bottom: 30px; left: 40px; color: rgba(255,255,255,0.2); font-size: 4rem; font-weight: 900; pointer-events: none; text-transform: uppercase; letter-spacing: -2px;">
                                    <?php echo htmlspecialchars($currentShop['name']); ?>
                                </div>

                            </div>
                        </div>

                        <div class="content-card" style="margin-top: 40px;">
                            <div class="section-header"
                                style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
                                <div>
                                    <h2 style="font-size: 1.8rem; color: #1e293b; margin-bottom: 5px;">Store Products</h2>
                                    <p style="color: #64748b; margin: 0;">Browse our latest collection</p>
                                </div>

                                <!-- Sort Controls -->
                                <div class="sort-controls">
                                    <span class="sort-label">Sort By:</span>
                                    <a href="?store=<?php echo urlencode($selectedStore); ?>&sort=best"
                                        class="sort-btn <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'best') ? 'active' : ''; ?>">Best
                                        Match</a>
                                    <a href="?store=<?php echo urlencode($selectedStore); ?>&sort=latest"
                                        class="sort-btn <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'latest') ? 'active' : ''; ?>">Latest</a>
                                    <a href="?store=<?php echo urlencode($selectedStore); ?>&sort=sales"
                                        class="sort-btn <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'sales') ? 'active' : ''; ?>">Top
                                        Sales</a>
                                </div>
                            </div>

                            <!-- Product Grid -->
                            <div class="product-grid"
                                style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                                <?php
                                foreach ($products as $index => $product):
                                    $rating = $product['rating'];
                                    $soldVal = $product['sold'];
                                    if ($soldVal > 1000) {
                                        $soldDisp = number_format($soldVal / 1000, 1) . 'k';
                                    } else {
                                        $soldDisp = $soldVal;
                                    }
                                    ?>
                                    <div class="product-card" data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-price="<?php echo $product['price']; ?>"
                                        data-raw-price="<?php echo $product['raw_price']; ?>"
                                        data-image="<?php echo $product['image']; ?>"
                                        data-variants='<?php echo htmlspecialchars(json_encode($product['variants'] ?? [])); ?>'
                                        data-rating="<?php echo $product['rating']; ?>" data-sold="<?php echo $soldDisp; ?>"
                                        data-store="<?php echo htmlspecialchars($selectedStore); ?>"
                                        data-category="<?php echo htmlspecialchars($currentShop['category'] ?? 'General'); ?>"
                                        data-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
                                        onclick="openProductModal(this)">

                                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"
                                            class="product-img">
                                        <div class="variant-swatches" aria-hidden="true"></div>
                                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="product-description"><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 60)) . '...'; ?></p>
                                        <div class="product-price"><?php echo $product['price']; ?></div>
                                        <div class="product-meta-row">
                                            <div class="product-rating">
                                                <?php
                                                for ($i = 0; $i < 5; $i++) {
                                                    if ($i < floor($rating))
                                                        echo '<i class="fas fa-star"></i>';
                                                    else
                                                        echo '<i class="far fa-star"></i>';
                                                }
                                                ?>
                                            </div>
                                            <span class="product-sold"><?php echo $soldDisp; ?> Sold</span>
                                        </div>
                                        <button class="add-to-cart-btn">View Details</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div> <!-- End .store-main -->
                </div> <!-- End .store-layout -->

                <?php
            } elseif (!empty($searchQuery)) {
                // --- SHOPEE STYLE SEARCH RESULTS VIEW ---
            
                // 1. Get Related Shops
                $relatedShops = [];
                foreach ($shops as $shop) {
                    if (stripos($shop['name'], $searchQuery) !== false || stripos($shop['category'], $searchQuery) !== false) {
                        $relatedShops[] = $shop;
                    }
                }
                $relatedShops = array_slice($relatedShops, 0, 3);

                // 2. Get All Products matching Search
                $allProducts = [];
                foreach ($shops as $shop) {
                    $shopProducts = getMockProducts($shop['name'], $searchQuery);
                    foreach ($shopProducts as $p) {
                        $p['shop_name'] = $shop['name'];
                        $p['shop_initials'] = $shop['initials'];
                        $p['shop_bg'] = $shop['bg'];
                        $allProducts[] = $p;
                    }
                }
                // Include Core 3 approved products in search results
                if (!empty($searchQuery)) {
                    include_once __DIR__ . '/../Database/core3_products.php';
                    $core3Search = fetchCore3ApprovedProducts();
                    foreach ($core3Search as $c3) {
                        if (stripos($c3['name'], $searchQuery) !== false || stripos($c3['description'] ?? '', $searchQuery) !== false || stripos($c3['category'] ?? '', $searchQuery) !== false) {
                            $allProducts[] = [
                                'name' => $c3['name'],
                                'price' => '₱' . number_format($c3['price'], 2),
                                'raw_price' => $c3['price'],
                                'original_price' => '',
                                'discount' => '',
                                'image' => $c3['image'],
                                'rating' => 4.5,
                                'sold' => 'New',
                                'description' => $c3['description'] ?? '',
                                'shop_name' => $c3['seller_name'] ?? 'Marketplace Seller',
                                'shop_initials' => 'C3',
                                'shop_bg' => '1d4ed8',
                            ];
                        }
                    }
                }

                // 3. Sorting
                $sort = $_GET['sort'] ?? 'best';
                if ($sort === 'price_asc')
                    usort($allProducts, fn($a, $b) => $a['raw_price'] <=> $b['raw_price']);
                elseif ($sort === 'price_desc')
                    usort($allProducts, fn($a, $b) => $b['raw_price'] <=> $a['raw_price']);
                elseif ($sort === 'sales')
                    usort($allProducts, fn($a, $b) => $b['sold'] <=> $a['sold']);
                elseif ($sort === 'latest')
                    shuffle($allProducts);

                // Set a default currentShop for the global search view
                $currentShop = !empty($relatedShops) ? $relatedShops[0] : $shops[0];
                ?>

                <style>
                    .search-results-page {
                        display: flex;
                        gap: 25px;
                        margin-top: 30px;
                        font-family: 'Helvetica Neue', Helvetica, Arial, 文泉驛正黑, "WenQuanYi Zen Hei", "Hiragino Sans GB", "Microsoft YaHei", sans-serif;
                    }

                    .search-sidebar {
                        width: 200px;
                        flex-shrink: 0;
                    }

                    .filter-group {
                        margin-bottom: 25px;
                        padding-bottom: 15px;
                        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                    }

                    .filter-title {
                        font-size: 14px;
                        font-weight: 700;
                        color: #333;
                        margin-bottom: 12px;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    }

                    .filter-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }

                    .filter-item {
                        margin-bottom: 8px;
                        color: #555;
                        font-size: 13px;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        cursor: pointer;
                    }

                    .filter-item:hover {
                        color: #2A3B7E;
                    }

                    .filter-checkbox {
                        width: 14px;
                        height: 14px;
                        border: 1px solid #ccc;
                        border-radius: 2px;
                    }

                    .search-main {
                        flex: 1;
                        min-width: 0;
                    }

                    .related-shops-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 15px;
                    }

                    .related-shops-header h3 {
                        color: #777;
                        font-size: 14px;
                        font-weight: 400;
                        text-transform: uppercase;
                    }

                    .more-shops-link {
                        color: #2A3B7E;
                        text-decoration: none;
                        font-size: 14px;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                    }

                    .related-shop-card {
                        background: #fff;
                        border: 1px solid rgba(0, 0, 0, 0.05);
                        border-radius: 4px;
                        padding: 20px;
                        display: flex;
                        gap: 30px;
                        margin-bottom: 30px;
                        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
                    }

                    .shop-info-side {
                        width: 250px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        border-right: 1px solid #eee;
                        padding-right: 30px;
                        text-align: center;
                    }

                    .shop-logo-large {
                        width: 80px;
                        height: 80px;
                        border-radius: 50%;
                        margin-bottom: 12px;
                        overflow: hidden;
                        border: 1px solid #eee;
                    }

                    .shop-name-bold {
                        font-weight: 700;
                        font-size: 16px;
                        color: #333;
                        margin-bottom: 5px;
                    }

                    .shop-badge {
                        background: #ee4d2d;
                        color: #fff;
                        font-size: 10px;
                        padding: 1px 3px;
                        border-radius: 2px;
                        text-transform: uppercase;
                        margin-bottom: 8px;
                    }

                    .shop-meta-row {
                        display: flex;
                        gap: 15px;
                        font-size: 12px;
                        color: #777;
                        margin-bottom: 15px;
                    }

                    .visit-shop-btn {
                        padding: 6px 20px;
                        border: 1px solid #2A3B7E;
                        color: #2A3B7E;
                        text-decoration: none;
                        border-radius: 2px;
                        font-size: 14px;
                        font-weight: 500;
                    }

                    .visit-shop-btn:hover {
                        background: rgba(42, 59, 126, 0.05);
                    }

                    .shop-top-products {
                        flex: 1;
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 15px;
                    }

                    .mini-product {
                        cursor: pointer;
                        transition: transform 0.2s;
                    }

                    .mini-product:hover {
                        transform: translateY(-2px);
                    }

                    .mini-product-img {
                        width: 100%;
                        aspect-ratio: 1;
                        object-fit: cover;
                        border-radius: 2px;
                        margin-bottom: 8px;
                    }

                    .mini-product-price {
                        color: #2A3B7E;
                        font-weight: 700;
                        font-size: 14px;
                    }

                    .results-summary {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        margin-bottom: 20px;
                        color: #333;
                    }

                    .results-grid {
                        display: grid;
                        grid-template-columns: repeat(5, 1fr);
                        gap: 12px;
                    }

                    .result-card {
                        background: #fff;
                        border: 1px solid transparent;
                        border-radius: 4px;
                        overflow: hidden;
                        cursor: pointer;
                        transition: all 0.2s;
                    }

                    .result-card:hover {
                        border-color: #2A3B7E;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                        transform: translateY(-2px);
                    }

                    .result-img-wrapper {
                        width: 100%;
                        aspect-ratio: 1;
                        position: relative;
                    }

                    .result-img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }

                    .result-info {
                        padding: 10px;
                    }

                    .result-title {
                        font-size: 12px;
                        line-height: 1.4;
                        height: 2.8em;
                        overflow: hidden;
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        color: #333;
                        margin-bottom: 8px;
                    }

                    .result-price-row {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }

                    .result-price {
                        color: #2A3B7E;
                        font-weight: 700;
                        font-size: 16px;
                    }

                    .result-sold {
                        font-size: 10px;
                        color: #999;
                    }
                </style>

                <div class="search-results-page">
                    <!-- Sidebar Filters -->
                    <div class="search-sidebar">
                        <div class="filter-group">
                            <div class="filter-title"><i class="fas fa-filter"></i> SEARCH FILTER</div>
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Shipped From</div>
                            <ul class="filter-list">
                                <li class="filter-item">
                                    <div class="filter-checkbox"></div> Domestic
                                </li>
                                <li class="filter-item">
                                    <div class="filter-checkbox"></div> Overseas
                                </li>
                                <li class="filter-item">
                                    <div class="filter-checkbox"></div> Metro Manila
                                </li>
                                <li class="filter-item">
                                    <div class="filter-checkbox"></div> North Luzon
                                </li>
                            </ul>
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Shops & Promos</div>
                            <ul class="filter-list">
                                <li class="filter-item">
                                    <div class="filter-checkbox"></div> Official Shop
                                </li>
                                <li class="filter-item">
                                    <div class="filter-checkbox"></div> Shop Vouchers
                                </li>
                                <li class="filter-item">
                                    <div class="filter-checkbox"></div> Cash on Delivery
                                </li>
                            </ul>
                        </div>

                        <div class="filter-group">
                            <div class="filter-title">Price Range</div>
                            <div style="display:flex; gap:5px; align-items:center;">
                                <input type="text" placeholder="₱ MIN"
                                    style="width: 50%; padding: 5px; border: 1px solid #ccc; font-size: 12px;">
                                <span>-</span>
                                <input type="text" placeholder="₱ MAX"
                                    style="width: 50%; padding: 5px; border: 1px solid #ccc; font-size: 12px;">
                            </div>
                            <button
                                style="width: 100%; padding: 8px; background: #2A3B7E; color: #fff; border: none; margin-top: 10px; border-radius: 2px; cursor: pointer; font-size: 12px;">APPLY</button>
                        </div>
                    </div>

                    <!-- Main Search Content -->
                    <div class="search-main">

                        <!-- Related Shops Section -->
                        <?php if (!empty($relatedShops)): ?>
                            <div class="related-shops-header">
                                <h3>SHOPS RELATED TO "<?php echo htmlspecialchars($searchQuery); ?>"</h3>
                                <a href="#" class="more-shops-link">More Shops <i class="fas fa-chevron-right"></i></a>
                            </div>

                            <?php foreach ($relatedShops as $rShop):
                                $topProducts = getMockProducts($rShop['name'], '');
                                $topProducts = array_slice($topProducts, 0, 3);
                                ?>
                                <div class="related-shop-card">
                                    <div class="shop-info-side">
                                        <div class="shop-logo-large">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo $rShop['initials']; ?>&background=<?php echo $rShop['bg']; ?>&color=fff&size=128"
                                                style="width:100%; height:100%;">
                                        </div>
                                        <div class="shop-name-bold"><?php echo htmlspecialchars($rShop['name']); ?></div>
                                        <div class="shop-badge">Mall</div>
                                        <div class="shop-meta-row">
                                            <span><i class="fas fa-star" style="color:#ee4d2d"></i> 4.9</span>
                                            <span>|</span>
                                            <span>1.2M Followers</span>
                                        </div>
                                        <a href="?store=<?php echo urlencode($rShop['name']); ?>" class="visit-shop-btn">Visit
                                            Shop</a>
                                    </div>
                                    <div class="shop-top-products">
                                        <?php foreach ($topProducts as $tp): ?>
                                            <div class="mini-product" data-name="<?php echo htmlspecialchars($tp['name']); ?>"
                                                data-price="<?php echo $tp['price']; ?>"
                                                data-raw-price="<?php echo $tp['raw_price']; ?>"
                                                data-original-price="<?php echo $tp['original_price'] ?? ''; ?>"
                                                data-discount="<?php echo $tp['discount'] ?? ''; ?>"
                                                data-image="<?php echo $tp['image']; ?>" data-rating="<?php echo $tp['rating']; ?>"
                                                data-sold="<?php echo $tp['sold']; ?>"
                                                data-store="<?php echo htmlspecialchars($rShop['name']); ?>"
                                                data-variants='<?php echo htmlspecialchars(json_encode($tp['variants'] ?? [])); ?>'
                                                onclick="openProductModal(this)">
                                                <?php 
                                                    $tp_img = $tp['image'];
                                                    if(strpos($tp_img, '../../') === 0) $tp_img = str_replace('../../', '../', $tp_img);
                                                    $tp_img = str_replace(' ', '%20', $tp_img);
                                                ?>
                                                <img src="<?php echo $tp_img; ?>" class="mini-product-img">
                                                <div class="variant-swatches" aria-hidden="true" style="position: absolute; bottom:6px; right:6px;"></div>
                                                <div class="mini-product-price"><?php echo $tp['price']; ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Standard Search Results -->
                        <div class="results-summary">
                            <i class="fas fa-lightbulb" style="color: #2A3B7E;"></i>
                            <span>Search result for '<strong><?php echo htmlspecialchars($searchQuery); ?></strong>'</span>

                            <div
                                style="margin-left: auto; display: flex; gap: 10px; align-items: center; background: #fafafa; padding: 5px 15px; border-radius: 2px;">
                                <span style="font-size: 13px; color: #555;">Sort By:</span>
                                <a href="?search=<?php echo urlencode($searchQuery); ?>&sort=best"
                                    class="sort-btn-mini <?php echo ($sort == 'best') ? 'active' : ''; ?>"
                                    style="font-size:12px; text-decoration:none; color:<?php echo ($sort == 'best') ? '#2A3B7E' : '#555'; ?>; font-weight:<?php echo ($sort == 'best') ? '700' : '400'; ?>;">Relavance</a>
                                <a href="?search=<?php echo urlencode($searchQuery); ?>&sort=latest"
                                    class="sort-btn-mini <?php echo ($sort == 'latest') ? 'active' : ''; ?>"
                                    style="font-size:12px; text-decoration:none; color:<?php echo ($sort == 'latest') ? '#2A3B7E' : '#555'; ?>; font-weight:<?php echo ($sort == 'latest') ? '700' : '400'; ?>;">Latest</a>
                                <a href="?search=<?php echo urlencode($searchQuery); ?>&sort=sales"
                                    class="sort-btn-mini <?php echo ($sort == 'sales') ? 'active' : ''; ?>"
                                    style="font-size:12px; text-decoration:none; color:<?php echo ($sort == 'sales') ? '#2A3B7E' : '#555'; ?>; font-weight:<?php echo ($sort == 'sales') ? '700' : '400'; ?>;">Top
                                    Sales</a>
                            </div>
                        </div>

                        <?php if (empty($allProducts)): ?>
                            <div style="text-align: center; padding: 50px; background: #fff; border-radius: 4px;">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-product-found-8290610-6632128.png"
                                    style="width: 200px; opacity: 0.5;">
                                <p style="color: #999; margin-top: 20px;">No results found for
                                    "<?php echo htmlspecialchars($searchQuery); ?>"</p>
                            </div>
                        <?php else: ?>
                            <div class="results-grid">
                                <?php foreach ($allProducts as $ap):
                                    $soldVal = $ap['sold'] ?? 0;
                                    $soldDisp = (is_numeric($soldVal) && $soldVal > 1000) ? number_format($soldVal / 1000, 1) . 'k' : $soldVal;
                                    ?>
                                    <div class="result-card" data-name="<?php echo htmlspecialchars($ap['name']); ?>"
                                        data-price="<?php echo $ap['price']; ?>" data-raw-price="<?php echo $ap['raw_price']; ?>"
                                        data-original-price="<?php echo $ap['original_price'] ?? ''; ?>"
                                        data-discount="<?php echo $ap['discount'] ?? ''; ?>"
                                        data-image="<?php echo $ap['image']; ?>" data-rating="<?php echo $ap['rating']; ?>"
                                        data-variants='<?php echo htmlspecialchars(json_encode($ap['variants'] ?? [])); ?>'
                                        data-sold="<?php echo $soldDisp; ?>"
                                        data-description="<?php echo htmlspecialchars($ap['description'] ?? ''); ?>"
                                        data-store="<?php echo htmlspecialchars($ap['shop_name']); ?>" data-category="<?php
                                           $prodCat = 'General';
                                           foreach ($shops as $sh)
                                               if ($sh['name'] == $ap['shop_name']) {
                                                   $prodCat = $sh['category'];
                                                   break;
                                               }
                                           echo htmlspecialchars($prodCat);
                                           ?>" onclick="openProductModal(this)">
                                        <div class="result-img-wrapper">
                                            <?php 
                                                $img_path = $ap['image'];
                                                if(strpos($img_path, '../../') === 0) $img_path = str_replace('../../', '../', $img_path);
                                                $img_path = str_replace(' ', '%20', $img_path);
                                            ?>
                                            <img src="<?php echo $img_path; ?>" class="result-img">
                                            <div class="variant-swatches" aria-hidden="true" style="position: absolute; bottom:8px; left:8px;"></div>
                                            <?php if (!empty($ap['discount'])): ?>
                                                <div style="position: absolute; top: 0; right: 0; background: #ffe910; color: #ee4d2d; padding: 2px 5px; font-size: 10px; font-weight: 700;">
                                                    <?php echo $ap['discount']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="result-info">
                                            <div class="result-title"><?php echo htmlspecialchars($ap['name']); ?></div>
                                            <div class="result-description"><?php echo htmlspecialchars(substr($ap['description'] ?? '', 0, 50)) . '...'; ?></div>
                                            <div class="result-price-row">
                                                <div class="result-price"><?php echo $ap['price']; ?></div>
                                                <?php if (!empty($ap['original_price'])): ?>
                                                    <div style="font-size: 11px; text-decoration: line-through; color: #999; margin-left: 5px;"><?php echo $ap['original_price']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                                <div class="result-sold"><?php echo $soldDisp; ?> sold</div>
                                            </div>
                                            <div style="font-size: 10px; color: #999; margin-top: 5px;"><i class="fas fa-store"></i>
                                                <?php echo htmlspecialchars($ap['shop_name']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
            } else {
                // DEFAULT VIEW: Shop List
                $currentShop = $shops[0];
                $selectedStore = $shops[0]['name'];
                ?>
                <!-- LANDING VIEW (Premium Enhanced Hero) -->
                <!-- Old inline hero styles removed - using professional mall_hero.php component instead -->

                <style>
                    .premium-card {
                        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                        cursor: pointer;
                        overflow: hidden;
                        position: relative;
                        height: 400px;
                        border-radius: 20px;
                        border: 1px solid rgba(0,0,0,0.05);
                    }

                    .premium-card:hover {
                        transform: translateY(-10px) scale(1.02);
                        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25);
                    }

                    .premium-card img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        transition: transform 0.6s ease;
                    }

                    .premium-card:hover img {
                        transform: scale(1.1);
                    }

                    .premium-overlay {
                        position: absolute;
                        inset: 0;
                        background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 40%, transparent 100%);
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-end;
                        padding: 30px;
                        color: white;
                        opacity: 1;
                        transition: all 0.3s;
                    }

                    .premium-shop-badge {
                        position: absolute;
                        top: 20px;
                        right: 20px;
                        background: rgba(255, 255, 255, 0.2);
                        backdrop-filter: blur(5px);
                        padding: 5px 12px;
                        border-radius: 8px;
                        font-size: 0.75rem;
                        font-weight: 600;
                        border: 1px solid rgba(255, 255, 255, 0.3);
                    }
                </style>

                <!-- LANDING VIEW: Professional Mall Hero Component -->
                <?php include '../Components/mall_hero.php'; ?>

                <!-- Helper function for hex to RGB conversion -->
                <?php
                function hexToRgb($hex) {
                    $hex = str_replace('#', '', $hex);
                    if(strlen($hex) == 6) {
                        list($r, $g, $b) = sscanf($hex, '%02x%02x%02x');
                        return "$r,$g,$b";
                    }
                    return "99,102,241"; // Default indigo fallback
                }
                ?>

                <!-- Premium Store Collections Section -->
                <div id="premium-collections" class="featured-section" style="margin-top: 60px; margin-bottom: 70px;">
                    <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                <div style="width: 25px; height: 3px; background: #3b82f6; border-radius: 10px;"></div>
                                <span style="font-weight: 800; color: #3b82f6; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">All Official Stores</span>
                            </div>
                            <h2 style="font-size: 2.2rem; color: #0f172a; margin: 0; font-weight: 800;">Premium Store <span style="color: #3b82f6;">Partners</span></h2>
                        </div>
                        <a href="?search=" style="color: #64748b; text-decoration: none; font-weight: 600; font-size: 0.95rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 4px; transition: all 0.3s;" onmouseover="this.style.borderColor='#3b82f6'; this.style.color='#3b82f6'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'">
                            Browse Categories <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 0.8em;"></i>
                        </a>
                    </div>

                    <div class="featured-row" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                        <?php 
                        $categoryIcons = [
                            'UrbanWear PH' => ['icon' => 'fa-tshirt', 'color' => '#60a5fa', 'label' => 'Fashion'],
                            'StyleHub Manila' => ['icon' => 'fa-crown', 'color' => '#f59e0b', 'label' => 'Trending'],
                            'DailyFits Co.' => ['icon' => 'fa-heart', 'color' => '#ef4444', 'label' => 'Everyday'],
                            'LuxeBasics' => ['icon' => 'fa-gem', 'color' => '#8b5cf6', 'label' => 'Premium'],
                            'TechZone PH' => ['icon' => 'fa-bolt', 'color' => '#a855f7', 'label' => 'Tech'],
                            'SmartGear Store' => ['icon' => 'fa-mobile', 'color' => '#06b6d4', 'label' => 'Gadgets'],
                            'GadgetLab PH' => ['icon' => 'fa-laptop', 'color' => '#3b82f6', 'label' => 'Electronics'],
                            'CozyLiving Store' => ['icon' => 'fa-home', 'color' => '#ec4899', 'label' => 'Home'],
                            'GlowUp Beauty' => ['icon' => 'fa-magic', 'color' => '#f472b6', 'label' => 'Beauty'],
                            'FreshLook PH' => ['icon' => 'fa-spa', 'color' => '#10b981', 'label' => 'Care'],
                            'HomeEssentials PH' => ['icon' => 'fa-utensils', 'color' => '#f97316', 'label' => 'Kitchen'],
                            'TrendyBags PH' => ['icon' => 'fa-bag-shopping', 'color' => '#6366f1', 'label' => 'Bags']
                        ];
                        
                        foreach($shops as $shop): 
                            $storeName = $shop['name'];
                            $storeEnc = urlencode($storeName);
                            $iconInfo = $categoryIcons[$storeName] ?? ['icon' => 'fa-store', 'color' => '#64748b', 'label' => 'Shop'];
                            $initials = $shop['initials'];
                            $bgColor = $shop['bg'];
                        ?>
                        <div class="premium-card" onclick="window.location.href='?store=<?php echo $storeEnc; ?>'" style="cursor: pointer;">
                            <div class="premium-shop-badge" style="background: linear-gradient(135deg, #<?php echo $bgColor; ?>, rgba(<?php echo hexToRgb($bgColor); ?>, 0.7)); font-size: 11px;"><?php echo $shop['rating']; ?> ★</div>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #<?php echo $bgColor; ?>, rgba(<?php echo hexToRgb($bgColor); ?>, 0.6)); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                                <div style="position: absolute; width: 100%; height: 100%; background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2220%22 cy=%2220%22 r=%225%22 fill=%22white%22 opacity=%220.05%22/><circle cx=%2270%22 cy=%2280%22 r=%228%22 fill=%22white%22 opacity=%220.05%22/></svg>'); opacity: 0.5;"></div>
                                <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 32px; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.2); z-index: 2;"><?php echo $initials; ?></div>
                            </div>
                            <div class="premium-overlay" style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                    <i class="fas <?php echo $iconInfo['icon']; ?>" style="color: <?php echo $iconInfo['color']; ?>;font-size:14px;"></i>
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: <?php echo $iconInfo['color']; ?>;"><?php echo $iconInfo['label']; ?></span>
                                </div>
                                <h3 style="margin: 0; font-size: 1rem; font-weight: 800; line-height: 1.2;"><?php echo htmlspecialchars($storeName); ?></h3>
                                <p style="margin: 6px 0 0; font-size: 0.85rem; opacity: 0.75; font-weight: 400; line-height: 1.3;"><?php echo htmlspecialchars($shop['category']); ?></p>
                                <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1);">
                                    <span style="font-size: 11px; font-weight: 600; opacity: 0.8;"><?php echo $shop['sold']; ?> sold</span>
                                    <div style="width: 0; height: 2px; background: rgba(255,255,255,0.6); transition: width 0.3s ease;" class="hover-line"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <style>
                    .premium-card {
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        transform-origin: center;
                    }
                    .premium-card:hover {
                        transform: translateY(-8px) scale(1.02);
                        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15), 0 0 1px rgba(99, 102, 241, 0.1);
                    }
                    .premium-card:hover .hover-line {
                        width: 100% !important;
                    }
                    .premium-card .premium-overlay {
                        background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.85) 50%, transparent 100%);
                        transition: all 0.3s ease;
                    }
                    .premium-card:hover .premium-overlay {
                        background: linear-gradient(to top, rgba(99, 102, 241, 0.2) 0%, rgba(99, 102, 241, 0.1) 50%, transparent 100%);
                    }
                </style>

                <!-- Best Selling Products Section -->
                <div class="content-card" style="margin-top: 50px; background: linear-gradient(135deg, #e0e7ff 0%, #f0f4ff 100%); padding: 40px; border-radius: 24px; box-shadow: 0 8px 32px rgba(99, 102, 241, 0.1); border: 2px solid #c7d2fe;">
                    <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 15px;">
                            <i class="fas fa-star" style="color: #6366f1; font-size: 22px;"></i>
                            <span style="font-weight: 800; color: #6366f1; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">Most Popular</span>
                            <i class="fas fa-star" style="color: #6366f1; font-size: 22px;"></i>
                        </div>
                        <h2 style="font-size: 2.2rem; color: #0f172a; margin-bottom: 12px; font-weight: 800;">Customer Favorites & <span style="color: #6366f1;">Best Sellers</span></h2>
                        <div style="width: 80px; height: 4px; background: linear-gradient(90deg, #6366f1, #a855f7); margin: 0 auto 15px; border-radius: 10px;"></div>
                        <p style="color: #64748b; font-size: 1.1rem;">Top-rated products loved by thousands of customers across the mall</p>
                    </div>

                    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px;">
                        <?php 
                        // Ensure database connection is available
                        if (!isset($conn)) {
                            require_once __DIR__ . '/../Database/config.php';
                        }
                        // Fetch best-selling products from database
                        include '../Categories/best_selling/get_best_sellers.php';
                        $bestSellers = getBestSellingProducts($conn, 4);
                        
                        foreach ($bestSellers as $bs):
                            $soldDisp = $bs['total_sold'] ?? 0;
                            if ($soldDisp > 1000) {
                                $soldDisp = number_format($soldDisp / 1000, 1) . 'k';
                            }
                        ?>
                            <div class="product-card" 
                                style="border: 1px solid #c7d2fe; border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; position: relative; box-shadow: 0 4px 6px rgba(99, 102, 241, 0.05);"
                                onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 25px 50px rgba(99, 102, 241, 0.15); this.style.borderColor='#a855f7';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(99, 102, 241, 0.05)'; this.style.borderColor='#c7d2fe';"
                                data-name="<?php echo htmlspecialchars($bs['name']); ?>"
                                data-price="<?php echo $bs['price']; ?>" 
                                data-raw-price="<?php echo $bs['price']; ?>"
                                data-image="<?php echo $bs['image']; ?>" 
                                data-rating="4.5" 
                                data-sold="<?php echo $soldDisp; ?>"
                                data-description="<?php echo htmlspecialchars($bs['description'] ?? ''); ?>"
                                data-store="Best Sellers" 
                                onclick="openProductModal(this)">
                                
                                <!-- Popular Badge -->
                                <div style="position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #6366f1, #a855f7); color: white; padding: 8px 14px; border-radius: 8px; font-size: 11px; font-weight: 700; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); z-index: 10; display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-fire"></i> Bestseller
                                </div>
                                
                                <div class="result-img-wrapper" style="aspect-ratio: 1; overflow: hidden; position: relative; background: #f8fafc;">
                                    <img src="<?php echo str_replace(' ', '%20', $bs['image']); ?>" class="result-img" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" onerror="this.onerror=null; this.src='../image/logo.png';">
                                </div>
                                
                                <div class="result-info" style="padding: 20px;">
                                    <div style="font-size: 11px; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-chart-line" style="font-size: 10px;"></i> Customer Choice
                                    </div>
                                    <div class="result-title" style="font-weight: 600; font-size: 15px; color: #1e293b; margin-bottom: 8px; height: 2.8em; overflow: hidden; line-height: 1.4;"><?php echo htmlspecialchars($bs['name']); ?></div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                        <div class="result-price" style="color: #0f172a; font-weight: 800; font-size: 18px;"><?php echo $bs['price']; ?></div>
                                        <div style="font-size: 12px; color: #6366f1; font-weight: 700; background: #e0e7ff; padding: 4px 12px; border-radius: 20px;"><?php echo $soldDisp; ?> sold</div>
                                    </div>
                                    
                                    <button style="width: 100%; padding: 12px; background: linear-gradient(135deg, #6366f1, #a855f7); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;" 
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(99, 102, 241, 0.3)'" 
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                                        onclick="event.stopPropagation(); openProductModal(this.closest('.product-card'))">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Core 3 Marketplace Products Section -->
                <div class="content-card" style="margin-top: 40px; background: linear-gradient(135deg, #fef3c7 0%, #e0f2fe 100%); padding: 40px; border-radius: 24px; box-shadow: 0 8px 32px rgba(30, 64, 175, 0.12); border: 2px solid #bfdbfe;">
                    <div class="section-header" style="text-align: center; margin-bottom: 32px;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 12px;">
                            <i class="fas fa-globe-asia" style="color: #2563eb; font-size: 20px;"></i>
                            <span style="font-weight: 800; color: #1d4ed8; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">Live Marketplace</span>
                            <i class="fas fa-store" style="color: #2563eb; font-size: 20px;"></i>
                        </div>
                        <h2 style="font-size: 2.0rem; color: #0f172a; margin-bottom: 10px; font-weight: 800;">Approved Products from <span style="color: #1d4ed8;">Core 3</span></h2>
                        <p style="color: #64748b; font-size: 1rem;">Real items uploaded by sellers in Core 2 and approved by the Core 3 admin system</p>
                    </div>

                    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                        <?php
                        include_once '../Database/core3_products.php';
                        $core3Products = fetchCore3ApprovedProducts();

                        if (!empty($core3Products)):
                            // Limit number of products to avoid overloading the page
                            $limitCore3 = 8;
                            $countCore3 = 0;
                            foreach ($core3Products as $c3) :
                                if ($countCore3 >= $limitCore3) break;
                                $countCore3++;

                                $priceDisplay = '₱' . number_format($c3['price'], 2);
                                $shortDesc = !empty($c3['description']) ? substr($c3['description'], 0, 80) . '...' : 'Approved marketplace product from our Core 3 system.';
                                $storeLabel = !empty($c3['seller_name']) ? $c3['seller_name'] : 'Marketplace Seller';
                        ?>
                            <div class="product-card"
                                style="border: 1px solid #bfdbfe; border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; position: relative; box-shadow: 0 4px 6px rgba(30, 64, 175, 0.08);"
                                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(30, 64, 175, 0.18)'; this.style.borderColor='#60a5fa';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(30, 64, 175, 0.08)'; this.style.borderColor='#bfdbfe';"
                                data-name="<?php echo htmlspecialchars($c3['name']); ?>"
                                data-price="<?php echo $priceDisplay; ?>"
                                data-raw-price="<?php echo $c3['price']; ?>"
                                data-image="<?php echo htmlspecialchars($c3['image']); ?>"
                                data-rating="4.5"
                                data-sold="New"
                                data-description="<?php echo htmlspecialchars($c3['description'] ?? ''); ?>"
                                data-store="<?php echo htmlspecialchars($storeLabel); ?>"
                                onclick="openProductModal(this)">

                                <!-- Live badge -->
                                <div style="position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #ef4444, #f97316); color: white; padding: 6px 12px; border-radius: 999px; font-size: 10px; font-weight: 800; box-shadow: 0 4px 12px rgba(248, 113, 113, 0.4); display: flex; align-items: center; gap: 5px; z-index: 10;">
                                    <span style="width: 8px; height: 8px; border-radius: 999px; background: #fbbf24; box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.4);"></span>
                                    LIVE
                                </div>

                                <div class="result-img-wrapper" style="aspect-ratio: 1; overflow: hidden; position: relative; background: #eff6ff;">
                                    <img src="<?php echo htmlspecialchars($c3['image']); ?>" class="result-img" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" onerror="this.onerror=null; this.src='../image/logo.png';">
                                </div>

                                <div class="result-info" style="padding: 18px;">
                                    <div style="font-size: 11px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-shield-alt" style="font-size: 10px;"></i> Approved by Core 3
                                    </div>
                                    <div class="result-title" style="font-weight: 600; font-size: 15px; color: #0f172a; margin-bottom: 8px; height: 2.8em; overflow: hidden; line-height: 1.4;"><?php echo htmlspecialchars($c3['name']); ?></div>
                                    <div class="result-description" style="font-size: 12px; color: #64748b; margin-bottom: 10px; height: 2.4em; overflow: hidden; line-height: 1.2;"><?php echo htmlspecialchars($shortDesc); ?></div>

                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <div class="result-price" style="color: #0f172a; font-weight: 800; font-size: 18px;"><?php echo $priceDisplay; ?></div>
                                        <div style="font-size: 11px; color: #1d4ed8; font-weight: 600; background: #dbeafe; padding: 4px 10px; border-radius: 999px; display: flex; align-items: center; gap: 5px;">
                                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($storeLabel); ?>
                                        </div>
                                    </div>

                                    <button style="width: 100%; padding: 10px; background: linear-gradient(135deg, #1d4ed8, #2563eb); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-size: 14px;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(37, 99, 235, 0.4)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                                        onclick="event.stopPropagation(); openProductModal(this.closest('.product-card'))">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <p style="grid-column: 1 / -1; text-align: center; color: #64748b; font-size: 0.95rem;">
                                No live Core 3 products are available right now. Please check again later.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Featured / Shop Grid (Premium Highlights) -->
                <div class="content-card" style="margin-top: 50px; background: linear-gradient(135deg, #dcfce7 0%, #ccfbf1 100%); padding: 40px; border-radius: 24px; box-shadow: 0 8px 32px rgba(16, 185, 129, 0.1); border: 2px solid #a7f3d0;">
                    <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 15px;">
                            <i class="fas fa-gem" style="color: #059669; font-size: 22px;"></i>
                            <span style="font-weight: 800; color: #059669; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">Premium Selection</span>
                            <i class="fas fa-gem" style="color: #059669; font-size: 22px;"></i>
                        </div>
                        <h2 style="font-size: 2.2rem; color: #0f172a; margin-bottom: 12px; font-weight: 800;">Mall <span style="color: #059669;">Highlights</span></h2>
                        <div style="width: 80px; height: 4px; background: linear-gradient(90deg, #059669, #14b8a6); margin: 0 auto 15px; border-radius: 10px;"></div>
                        <p style="color: #64748b; font-size: 1.1rem;">Curated collection from our most trusted and best-reviewed stores</p>
                    </div>

                    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                        <?php 
                        // Fetch products from multiple top shops for the landing page
                        $landing_products = [];
                        $shops_to_sample = ['UrbanWear PH', 'TechZone PH', 'TrendyBags PH', 'GlowUp Beauty', 'DailyFits Co.', 'SmartGear Store', 'CozyLiving Store', 'StyleHub Manila'];
                        foreach($shops_to_sample as $sname) {
                            $sprod = getMockProducts($sname);
                            if(!empty($sprod)) {
                                $p = $sprod[0];
                                $p['shop_name'] = $sname;
                                $landing_products[] = $p;
                            }
                        }

                        foreach ($landing_products as $ap):
                            $soldDisp = ($ap['sold'] > 1000) ? number_format($ap['sold'] / 1000, 1) . 'k' : $ap['sold'];
                        ?>
                            <div class="product-card" 
                                style="border: 1px solid #a7f3d0; border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.05);"
                                onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 25px 50px rgba(16, 185, 129, 0.15)'; this.style.borderColor='#14b8a6';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(16, 185, 129, 0.05)'; this.style.borderColor='#a7f3d0';"
                                data-name="<?php echo htmlspecialchars($ap['name']); ?>"
                                data-price="<?php echo $ap['price']; ?>" 
                                data-raw-price="<?php echo $ap['raw_price']; ?>"
                                data-original-price="<?php echo $ap['original_price'] ?? ''; ?>"
                                data-discount="<?php echo $ap['discount'] ?? ''; ?>"
                                data-image="<?php echo $ap['image']; ?>" 
                                data-rating="<?php echo $ap['rating'] ?? 4.5; ?>" 
                                data-sold="<?php echo $soldDisp; ?>"
                                data-description="<?php echo htmlspecialchars($ap['description'] ?? ''); ?>"
                                data-store="<?php echo htmlspecialchars($ap['shop_name']); ?>" 
                                onclick="openProductModal(this)">
                                
                                <div style="position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #059669, #14b8a6); color: white; padding: 8px 14px; border-radius: 8px; font-size: 11px; font-weight: 700; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); z-index: 10; display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-crown"></i> Featured
                                </div>
                                
                                <div class="result-img-wrapper" style="aspect-ratio: 1; overflow: hidden; position: relative; background: #f0fdf4;">
                                    <img src="<?php echo str_replace(' ', '%20', $ap['image']); ?>" class="result-img" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                    <?php if (!empty($ap['discount'])): ?>
                                        <div style="position: absolute; bottom: 10px; left: 10px; background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 800; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); display: flex; align-items: center; gap: 4px;">
                                            <i class="fas fa-tag"></i> <?php echo $ap['discount']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="result-info" style="padding: 18px;">
                                    <div style="font-size: 10px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-certificate" style="font-size: 10px;"></i> Official Certified
                                    </div>
                                    <div class="result-title" style="font-weight: 600; font-size: 15px; color: #1e293b; margin-bottom: 8px; height: 2.8em; overflow: hidden; line-height: 1.4;"><?php echo htmlspecialchars($ap['name']); ?></div>
                                    
                                    <div class="result-description" style="font-size: 12px; color: #64748b; margin-bottom: 12px; height: 2.4em; overflow: hidden; line-height: 1.2;"><?php echo htmlspecialchars(substr($ap['description'] ?? '', 0, 80)); ?></div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                        <div class="result-price" style="color: #0f172a; font-weight: 800; font-size: 18px;"><?php echo $ap['price']; ?></div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 700; background: #dcfce7; padding: 4px 12px; border-radius: 20px;"><?php echo $soldDisp; ?> sold</div>
                                    </div>
                                    
                                    <button style="width: 100%; padding: 10px; background: linear-gradient(135deg, #059669, #14b8a6); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-size: 14px;" 
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(16, 185, 129, 0.3)'" 
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                                        onclick="event.stopPropagation(); openProductModal(this.closest('.product-card'))">
                                        <i class="fas fa-shopping-bag" style="margin-right: 6px;"></i> View Details
                                    </button>
                                    
                                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #a7f3d0; display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 20px; height: 20px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #059669; border: 1px solid #a7f3d0;">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <span style="font-size: 11px; color: #64748b; font-weight: 600;"><?php echo htmlspecialchars($ap['shop_name']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <style>
                    .mall-highlights-card:hover {
                        transform: translateY(-10px) scale(1.02);
                        box-shadow: 0 25px 50px rgba(16, 185, 129, 0.15);
                    }
                </style>
                <!-- You can add featured categories or other content here later -->
                <?php
            }
            ?>
        </div>
    </div>
    <footer>
        <?php include '../Components/footer.php'; ?>
    </footer>

    <!-- Product Detail Modal -->
    <link rel="stylesheet" href="../css/components/shared-product-view.css?v=<?php echo time(); ?>">
    <div id="productModal" class="modal-overlay">
        <div class="modal-content" style="width: 1000px; max-width: 95%;">
            <span class="modal-close" onclick="closeProductModal()">&times;</span>
            
            <div class="pv-left">
                <img id="modalImg" src="" alt="Product" class="pv-product-img" onerror="this.onerror=null; this.src='../image/logo.png';">
            </div>
            
            <div class="pv-right">
                <div class="pv-header">
                    <div class="pv-header-title">
                        <img src="../image/logo.png" alt="IMarket" class="pv-header-logo"> |
                        <span id="modalStoreSpan"><?php echo htmlspecialchars($selectedStore); ?></span>
                    </div>
                    <p id="modalCategoryP" class="pv-category">
                        <?php echo htmlspecialchars($currentShop['category'] ?? 'General'); ?>
                    </p>
                </div>

                <h2 id="modalTitle" class="pv-title">Product Name</h2>
                
                <div class="pv-meta">
                    <div id="modalRating" class="pv-rating"></div>
                    <span id="modalSold"></span>
                </div>

                <div class="pv-price-container">
                    <span id="modalOriginalPrice" class="pv-original-price"></span>
                    <span id="modalPrice" class="pv-price">₱0.00</span>
                    <span id="modalDiscountBadge" class="pv-discount-badge"></span>
                </div>

                <!-- Options -->
                <div class="pv-options-container">
                    <div class="pv-option-group">
                        <span class="pv-option-label">Color</span>
                        <div class="pv-options" id="modal-color-options">
                            <div class="pv-option-btn selected" onclick="selectOption(this)">Black</div>
                            <div class="pv-option-btn" onclick="selectOption(this)">White</div>
                            <div class="pv-option-btn" onclick="selectOption(this)">Blue</div>
                        </div>
                    </div>
                    <div class="pv-option-group">
                        <span class="pv-option-label">Size</span>
                        <div class="pv-options" id="modal-size-options">
                            <div class="pv-option-btn selected" onclick="selectOption(this)">M</div>
                            <div class="pv-option-btn" onclick="selectOption(this)">L</div>
                            <div class="pv-option-btn" onclick="selectOption(this)">XL</div>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="pv-option-group">
                    <span class="pv-option-label">Quantity</span>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div class="pv-quantity-control">
                            <button class="pv-qty-btn" onclick="updateModalQty(-1)">-</button>
                            <input type="text" id="modalQty" class="pv-qty-input" value="1" readonly>
                            <button class="pv-qty-btn" onclick="updateModalQty(1)">+</button>
                        </div>
                    </div>
                </div>

                <div class="pv-actions">
                    <a id="modalAddToCartBtn" href="#" onclick="event.preventDefault(); window.location.href = document.getElementById('modalAddToCartBtn').href;" class="pv-btn pv-btn-cart">
                        <i class="fas fa-cart-plus" style="margin-right: 8px;"></i> Add to Cart
                    </a>
                    <a id="modalBuyNowBtn" href="#" onclick="event.preventDefault(); window.location.href = document.getElementById('modalBuyNowBtn').href;" class="pv-btn pv-btn-buy">Buy Now</a>
                </div>

                <!-- Product Details Section -->
                <div class="product-details-section">
                    <div class="details-grid">
                        <div class="detail-item material" id="materialDetail" style="display:none;">
                            <div class="detail-label"><i class="fas fa-shirt"></i> Material Quality</div>
                            <div class="detail-value" id="materialValue"></div>
                        </div>
                        <div class="detail-item origin" id="originDetail" style="display:none;">
                            <div class="detail-label"><i class="fas fa-globe"></i> Origin</div>
                            <div class="detail-value" id="originValue"></div>
                        </div>
                        <div class="detail-item warranty" id="warrantyDetail" style="display:none;">
                            <div class="detail-label"><i class="fas fa-shield-alt"></i> Warranty</div>
                            <div class="detail-value" id="warrantyValue"></div>
                        </div>
                        <div class="detail-item weight" id="weightDetail" style="display:none;">
                            <div class="detail-label"><i class="fas fa-weight"></i> Weight</div>
                            <div class="detail-value" id="weightValue"></div>
                        </div>
                    </div>

                    <!-- Size Chart -->
                    <div class="size-chart-section" id="sizeChartSection" style="display:none;">
                        <h4><i class="fas fa-ruler"></i> Size Chart</h4>
                        <table class="size-table" id="sizeTable">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Specifications -->
                    <div class="specifications-section" id="specsSection" style="display:none;">
                        <h4><i class="fas fa-cogs"></i> Specifications</h4>
                        <ul class="specs-list" id="specsList"></ul>
                    </div>

                    <!-- Dimensions -->
                    <div class="physical-specs" id="physicalSpecsSection" style="display:none;">
                        <div class="spec-card" id="dimensionsCard" style="display:none;">
                            <div class="spec-card-icon"><i class="fas fa-cube"></i></div>
                            <div class="spec-card-label">Dimensions</div>
                            <div class="spec-card-value" id="dimensionsValue"></div>
                        </div>
                    </div>

                    <!-- Care Instructions -->
                    <div class="care-section" id="careSection" style="display:none;">
                        <h4><i class="fas fa-hand-holding-water"></i> Care Instructions</h4>
                        <ul class="care-list" id="careList"></ul>
                    </div>

                    <!-- Trust Badges -->
                    <div class="trust-badges" id="trustBadges" style="display:none;margin-top:20px;"></div>
                </div>

                <!-- Reviews Section -->
                <div class="pv-reviews">
                    <h3>Reviews</h3>
                    <div id="modalReviewsList" class="reviews-list">
                        <div class="review-item" style="text-align:center; color:#999; border:none;">
                            No reviews yet.
                        </div>
                    </div>
                    <a id="modalRateLink" href="#" class="rate-product-link">Rate Product <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentProduct = {};

        function populateProductDetails(product) {
            // Material
            const materialEl = document.getElementById('materialDetail');
            if (product.material) {
                materialEl.style.display = 'block';
                document.getElementById('materialValue').innerText = product.material;
            } else {
                materialEl.style.display = 'none';
            }

            // Origin
            const originEl = document.getElementById('originDetail');
            if (product.origin) {
                originEl.style.display = 'block';
                document.getElementById('originValue').innerText = product.origin;
            } else {
                originEl.style.display = 'none';
            }

            // Warranty
            const warrantyEl = document.getElementById('warrantyDetail');
            if (product.warranty) {
                warrantyEl.style.display = 'block';
                document.getElementById('warrantyValue').innerText = product.warranty;
            } else {
                warrantyEl.style.display = 'none';
            }

            // Weight
            const weightEl = document.getElementById('weightDetail');
            if (product.weight) {
                weightEl.style.display = 'block';
                document.getElementById('weightValue').innerText = product.weight;
            } else {
                weightEl.style.display = 'none';
            }

            // Size Chart
            const sizeChartEl = document.getElementById('sizeChartSection');
            if (product.sizeChart && typeof product.sizeChart === 'object') {
                sizeChartEl.style.display = 'block';
                const thead = sizeChartEl.querySelector('thead');
                const tbody = sizeChartEl.querySelector('tbody');
                thead.innerHTML = '';
                tbody.innerHTML = '';

                const headers = Object.keys(product.sizeChart[0] || {});
                const headerRow = thead.insertRow();
                headers.forEach(h => {
                    const th = document.createElement('th');
                    th.innerText = h.charAt(0).toUpperCase() + h.slice(1);
                    headerRow.appendChild(th);
                });

                product.sizeChart.forEach(row => {
                    const tr = tbody.insertRow();
                    headers.forEach(h => {
                        const td = tr.insertCell();
                        td.innerText = row[h] || '-';
                    });
                });
            } else {
                sizeChartEl.style.display = 'none';
            }

            // Specifications
            const specsEl = document.getElementById('specsSection');
            if (product.specs && Array.isArray(product.specs)) {
                specsEl.style.display = 'block';
                const specsList = document.getElementById('specsList');
                specsList.innerHTML = '';
                product.specs.forEach(spec => {
                    const li = document.createElement('li');
                    if (typeof spec === 'object') {
                        li.innerHTML = `<strong>${spec.key || spec.name || 'Feature'}:</strong> ${spec.value || ''}`;
                    } else {
                        li.innerText = spec;
                    }
                    specsList.appendChild(li);
                });
            } else {
                specsEl.style.display = 'none';
            }

            // Dimensions
            const dimensionsCard = document.getElementById('dimensionsCard');
            if (product.dimensions) {
                const physicalSection = document.getElementById('physicalSpecsSection');
                physicalSection.style.display = 'block';
                dimensionsCard.style.display = 'block';
                document.getElementById('dimensionsValue').innerText = product.dimensions;
            } else {
                dimensionsCard.style.display = 'none';
            }

            // Care Instructions
            const careEl = document.getElementById('careSection');
            if (product.careInstructions && Array.isArray(product.careInstructions) && product.careInstructions.length > 0) {
                careEl.style.display = 'block';
                const careList = document.getElementById('careList');
                careList.innerHTML = '';
                product.careInstructions.forEach(instruction => {
                    const li = document.createElement('li');
                    li.innerText = instruction.trim();
                    careList.appendChild(li);
                });
            } else {
                careEl.style.display = 'none';
            }

            // Trust Badges
            const trustBadges = document.getElementById('trustBadges');
            trustBadges.innerHTML = '';
            let hasBadges = false;

            if (product.warranty) {
                const badge = document.createElement('div');
                badge.className = 'trust-badge warranty';
                badge.innerHTML = '<i class="fas fa-check-circle"></i> ' + product.warranty + ' Warranty';
                trustBadges.appendChild(badge);
                hasBadges = true;
            }

            if (product.origin) {
                const badge = document.createElement('div');
                badge.className = 'trust-badge verified';
                badge.innerHTML = '<i class="fas fa-globe"></i> Made in ' + product.origin;
                trustBadges.appendChild(badge);
                hasBadges = true;
            }

            if (product.material) {
                const badge = document.createElement('div');
                badge.className = 'trust-badge';
                badge.innerHTML = '<i class="fas fa-leaf"></i> ' + product.material;
                trustBadges.appendChild(badge);
                hasBadges = true;
            }

            if (hasBadges) {
                trustBadges.style.display = 'flex';
            } else {
                trustBadges.style.display = 'none';
            }
        }

        function selectOption(btn) {
            // Remove selected from siblings
            let group = btn.parentNode;
            let options = group.getElementsByClassName('pv-option-btn');
            for (let i = 0; i < options.length; i++) options[i].classList.remove('selected');
            btn.classList.add('selected');
        }

        function openProductModal(element) {
            const name = element.getAttribute('data-name');
            const price = element.getAttribute('data-price');
            const rawPrice = element.getAttribute('data-raw-price');
            const originalPrice = element.getAttribute('data-original-price');
            const discount = element.getAttribute('data-discount');
            const image = element.getAttribute('data-image');
            const rating = element.getAttribute('data-rating');
            const sold = element.getAttribute('data-sold');
            const store = element.getAttribute('data-store');
            const category = element.getAttribute('data-category') || 'General';
            const description = element.getAttribute('data-description') || 'Product description not available.';
            
            // Detailed product attributes
            const material = element.getAttribute('data-material');
            const origin = element.getAttribute('data-origin');
            const warranty = element.getAttribute('data-warranty');
            const weight = element.getAttribute('data-weight');
            const dimensions = element.getAttribute('data-dimensions');
            let sizeChart = element.getAttribute('data-size-chart');
            let specs = element.getAttribute('data-specifications');
            let careInstructions = element.getAttribute('data-care-instructions');

            // Parse JSON if needed
            if (sizeChart) {
                try { sizeChart = JSON.parse(sizeChart); } catch(e) { sizeChart = null; }
            }
            if (specs) {
                try { specs = JSON.parse(specs); } catch(e) { specs = null; }
            }
            if (careInstructions && typeof careInstructions === 'string') {
                careInstructions = careInstructions.split('\n').filter(i => i.trim());
            }

            currentProduct = { name, price, rawPrice, originalPrice, discount, image, store, category, description, 
                            material, origin, warranty, weight, dimensions, sizeChart, specs, careInstructions };

            document.getElementById('modalTitle').innerText = name;
            document.getElementById('modalPrice').innerText = price;
            
            const origPriceEl = document.getElementById('modalOriginalPrice');
            if (originalPrice) {
                origPriceEl.innerText = originalPrice;
                origPriceEl.style.display = 'inline';
            } else {
                origPriceEl.style.display = 'none';
            }

            const discountEl = document.getElementById('modalDiscountBadge');
            if (discount) {
                discountEl.innerText = discount;
                discountEl.style.display = 'inline-block';
            } else {
                discountEl.style.display = 'none';
            }
            document.getElementById('modalImg').src = image;
            // Populate modal color variants (if any) and select default
            populateModalVariantsFromElement(element);
            
            // Add description to modal
            let descriptionEl = document.getElementById('modalDescription');
            if (!descriptionEl) {
                // Create the description element if it doesn't exist
                const metaEl = document.querySelector('.pv-meta');
                descriptionEl = document.createElement('div');
                descriptionEl.id = 'modalDescription';
                descriptionEl.className = 'pv-description';
                descriptionEl.style.cssText = 'margin: 15px 0; padding: 15px; background: #f8fafc; border-left: 4px solid #3b82f6; border-radius: 4px; font-size: 14px; line-height: 1.6; color: #475569;';
                metaEl.parentNode.insertBefore(descriptionEl, metaEl.nextSibling);
            }
            descriptionEl.innerHTML = '<strong style="color: #1e293b; display: block; margin-bottom: 8px;">Product Description</strong>' + description;

            // Populate detailed product information
            populateProductDetails(currentProduct);

            document.getElementById('modalSold').innerText = sold + ' Sold';

            // Set Modal Store Name and Category
            document.getElementById('modalStoreSpan').innerText = store;
            const categoryEl = document.getElementById('modalCategoryP');
            if (categoryEl) categoryEl.innerText = category;

            document.getElementById('modalQty').value = 1;

            updateModalLinks();

            // Fetch Reviews
            const reviewsContainer = document.getElementById('modalReviewsList');
            reviewsContainer.innerHTML = '<div style="text-align:center; padding:20px;">Loading...</div>';

            fetch(`fetch_reviews.php?product_name=${encodeURIComponent(name)}`)
                .then(response => response.text())
                .then(html => {
                    reviewsContainer.innerHTML = html;
                })
                .catch(err => {
                    reviewsContainer.innerHTML = '<div style="text-align:center; color:red;">Failed to load reviews.</div>';
                });

            const ratingVal = parseFloat(rating);
            let starsHtml = '';
            for (let i = 0; i < 5; i++) {
                if (i < Math.floor(ratingVal)) starsHtml += '<i class="fas fa-star"></i>';
                else starsHtml += '<i class="far fa-star"></i>';
            }
            document.getElementById('modalRating').innerHTML = starsHtml;

            const modal = document.getElementById('productModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }

        function closeProductModal() {
            const modal = document.getElementById('productModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        function updateModalQty(change) {
            const input = document.getElementById('modalQty');
            let val = parseInt(input.value);
            val += change;
            if (val < 1) val = 1;
            input.value = val;
            updateModalLinks();
        }

        function updateModalLinks() {
            const qty = document.getElementById('modalQty').value;
            // Construct URL for Add to Cart
            const selectedImage = currentProduct.selectedImage || currentProduct.image || '';
            const baseAdd = `../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(currentProduct.name)}&price=${currentProduct.rawPrice}&store=${encodeURIComponent(currentProduct.store)}&image=${encodeURIComponent(selectedImage)}&quantity=${qty}`;

            // Construct URL for Buy Now (Direct Checkout)
            const buyNowUrl = `../Content/Payment.php?product_name=${encodeURIComponent(currentProduct.name)}&price=${currentProduct.rawPrice}&quantity=${qty}&image=${encodeURIComponent(selectedImage)}`;

            document.getElementById('modalAddToCartBtn').href = baseAdd;
            document.getElementById('modalBuyNowBtn').href = buyNowUrl;

            // Rate Product Link
            document.getElementById('modalRateLink').href = `Rate-Reviews.php?product_name=${encodeURIComponent(currentProduct.name)}`;
        }

        // initialize swatches once DOM ready
        window.addEventListener('DOMContentLoaded', function () { initVariantSwatches(); });

        /* Variant / Color Swatch Helpers */
        function generatePlaceholderVariants(name, baseImage) {
            // simple set of demo colors
            const cols = [
                { key: 'Black', hex: '111111' },
                { key: 'White', hex: 'ffffff' },
                { key: 'Blue', hex: '3b82f6' }
            ];
            const variants = {};
            cols.forEach(c => {
                // Placeholder image with background color and the product name
                const text = encodeURIComponent(name + ' ' + c.key);
                const url = `https://via.placeholder.com/600x800/${c.hex}/ffffff?text=${text}`;
                variants[c.key] = { image: url, color: '#' + c.hex, name: c.key };
            });
            return variants;
        }

        function buildSwatchesForElement(el) {
            try {
                const variantsAttr = el.getAttribute('data-variants');
                let variants = [];
                if (variantsAttr && variantsAttr.trim() !== '') {
                    try { variants = JSON.parse(variantsAttr); } catch(e) { variants = variantsAttr; }
                }

                // If variants is an object/associative map (PHP style), normalize to array of {key,image,color}
                let normalized = [];
                if (variants && typeof variants === 'object' && !Array.isArray(variants)) {
                    for (const k in variants) {
                        if (variants.hasOwnProperty(k)) {
                            const v = variants[k];
                            if (typeof v === 'string') normalized.push({ key: k, image: v, color: '' });
                            else normalized.push({ key: k, image: v.image || '', color: v.color || '' });
                        }
                    }
                } else if (Array.isArray(variants) && variants.length > 0) {
                    normalized = variants.map(v => ({ key: v.key || v.color || v.name || 'Variant', image: v.image || '', color: v.color || '' }));
                }

                // If still empty, generate placeholder variants
                const name = el.getAttribute('data-name') || 'Product';
                const baseImg = el.getAttribute('data-image') || '';
                if (normalized.length === 0) {
                    const gen = generatePlaceholderVariants(name, baseImg);
                    for (const k in gen) normalized.push({ key: k, image: gen[k].image, color: gen[k].color });
                }

                // Find the image element in this card/result
                const imgEl = el.querySelector('img');
                const swatchContainer = el.querySelector('.variant-swatches');
                if (!swatchContainer) return;
                swatchContainer.innerHTML = '';

                normalized.forEach((v, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'variant-swatch';
                    btn.title = v.key;
                    btn.dataset.image = v.image || '';
                    btn.dataset.key = v.key;
                    
                    // Create image thumbnail instead of color swatch
                    const img = document.createElement('img');
                    img.src = v.image || '';
                    img.alt = v.key;
                    img.onerror = function() { this.style.display = 'none'; btn.style.background = '#e0e0e0'; };
                    btn.appendChild(img);
                    
                    if (i === 0) btn.classList.add('selected');
                    btn.onclick = function (ev) {
                        ev.stopPropagation();
                        // swap main product image
                        if (imgEl && btn.dataset.image) imgEl.src = btn.dataset.image;
                        // mark selected
                        const sibs = swatchContainer.querySelectorAll('.variant-swatch');
                        sibs.forEach(s => s.classList.remove('selected'));
                        btn.classList.add('selected');
                    };
                    swatchContainer.appendChild(btn);
                });
            } catch (err) {
                console.error('buildSwatchesForElement error', err);
            }
        }

        function initVariantSwatches() {
            // Build swatches for product cards, mini-products, results, etc.
            const nodes = document.querySelectorAll('[data-variants]');
            nodes.forEach(n => buildSwatchesForElement(n));
        }

        // Modal-specific variant selection (image thumbnails)
        function populateModalVariantsFromElement(el) {
            const variantsAttr = el.getAttribute('data-variants') || '';
            let variants = {};
            try { variants = JSON.parse(variantsAttr); } catch(e){ variants = {}; }
            const name = el.getAttribute('data-name') || 'Product';
            const baseImg = el.getAttribute('data-image') || '';
            if (!variants || Object.keys(variants).length === 0) variants = generatePlaceholderVariants(name, baseImg);

            // Clear existing variant options and replace with image thumbnails
            const container = document.getElementById('modal-color-options');
            container.innerHTML = '';
            container.style.display = 'flex';
            container.style.gap = '8px';
            container.style.flexWrap = 'wrap';
            container.style.alignItems = 'center';
            
            let firstImage = '';
            let firstKey = '';
            for (const key in variants) {
                if (!variants.hasOwnProperty(key)) continue;
                const v = variants[key];
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'variant-swatch';
                btn.title = key;
                btn.style.width = '50px';
                btn.style.height = '50px';
                btn.dataset.image = (typeof v === 'string') ? v : (v.image || '');
                
                // Add image thumbnail to button
                const img = document.createElement('img');
                img.src = btn.dataset.image;
                img.alt = key;
                img.onerror = function() { this.style.display = 'none'; btn.style.background = '#e0e0e0'; };
                btn.appendChild(img);
                
                btn.onclick = function () {
                    // mark selected
                    const sibs = container.querySelectorAll('.variant-swatch');
                    sibs.forEach(s => s.classList.remove('selected'));
                    btn.classList.add('selected');
                    const modalImg = document.getElementById('modalImg');
                    if (btn.dataset.image) modalImg.src = btn.dataset.image;
                    currentProduct.selectedImage = btn.dataset.image || currentProduct.image;
                    updateModalLinks();
                };
                container.appendChild(btn);
                if (!firstImage) {
                    firstImage = btn.dataset.image;
                    firstKey = key;
                }
            }
            // auto-select first
            if (firstImage) {
                const img = document.getElementById('modalImg');
                img.src = firstImage;
                currentProduct.selectedImage = firstImage;
                // mark first button selected
                const firstBtn = container.querySelector('.variant-swatch');
                if (firstBtn) firstBtn.classList.add('selected');
            }
        }


        window.onclick = function (event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeProductModal();
            }
            const chatModal = document.getElementById('chatModal');
            if (event.target == chatModal) {
                closeChatModal();
            }
        }

        // Follow/Unfollow Functionality
        let isFollowing = false;

        async function checkFollowStatus(storeName) {
            try {
                const response = await fetch(`check_follow.php?store_name=${encodeURIComponent(storeName)}`);
                const data = await response.json();
                if (data.success && data.following) {
                    isFollowing = true;
                    updateFollowButton(true);
                }
            } catch (error) {
                console.error('Error checking follow status:', error);
            }
        }

        async function toggleFollow(event, storeName) {
            event.preventDefault();

            const action = isFollowing ? 'unfollow' : 'follow';
            const formData = new FormData();
            formData.append('store_name', storeName);
            formData.append('action', action);

            try {
                const response = await fetch('follow_store.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    isFollowing = !isFollowing;
                    updateFollowButton(isFollowing);
                    showNotification(data.message);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            }
        }

        function updateFollowButton(following) {
            const btn = document.getElementById('followBtn');
            const icon = document.getElementById('followIcon');
            const text = document.getElementById('followText');

            if (!btn || !icon || !text) return;

            if (following) {
                btn.classList.remove('btn-seller-primary');
                btn.style.background = 'rgba(255,255,255,0.2)';
                icon.className = 'fas fa-check';
                text.textContent = 'Following';
            } else {
                btn.classList.add('btn-seller-primary');
                btn.style.background = '';
                icon.className = 'fas fa-plus';
                text.textContent = 'Follow';
            }
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                font-weight: 600;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Chat Modal Functionality
        let chatPollInterval = null;
        let currentStoreName = '';

        function openChatModal(event, storeName) {
            event.preventDefault();
            currentStoreName = storeName;
            document.getElementById('chatStoreName').textContent = storeName;
            document.getElementById('chatModal').style.display = 'flex';
            setTimeout(() => {
                document.getElementById('chatModal').classList.add('show');
            }, 10);
            loadChatHistory(storeName);

            if (chatPollInterval) clearInterval(chatPollInterval);
            chatPollInterval = setInterval(() => loadChatHistory(storeName), 3000);
        }

        function closeChatModal() {
            if (chatPollInterval) clearInterval(chatPollInterval);
            const modal = document.getElementById('chatModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        let lastMessageCount = 0;
        async function loadChatHistory(storeName) {
            try {
                const response = await fetch(`get_chat_messages.php?store_name=${encodeURIComponent(storeName)}`);
                const data = await response.json();

                if (data.success) {
                    const messagesContainer = document.getElementById('chatMessages');

                    // Only update if count changed
                    if (data.messages.length !== lastMessageCount) {
                        const isAtBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop <= messagesContainer.clientHeight + 50;

                        messagesContainer.innerHTML = '';
                        data.messages.forEach(msg => {
                            const messageDiv = document.createElement('div');
                            const isCustomer = msg.sender_type === 'customer';
                            messageDiv.style.cssText = `
                                background: ${isCustomer ? 'linear-gradient(135deg, #2c4c7c 0%, #1e3a5f 100%)' : 'linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%)'}; 
                                color: ${isCustomer ? 'white' : '#1e293b'}; 
                                padding: 12px 18px; 
                                border-radius: 18px; 
                                margin-bottom: 12px; 
                                max-width: 70%; 
                                align-self: ${isCustomer ? 'flex-end' : 'flex-start'}; 
                                ${isCustomer ? 'margin-left: auto;' : ''}
                                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                                word-wrap: break-word;
                            `;
                            messageDiv.textContent = msg.message;
                            messagesContainer.appendChild(messageDiv);
                        });

                        if (isAtBottom || lastMessageCount === 0) {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
                        lastMessageCount = data.messages.length;
                    }
                }
            } catch (error) {
                console.error('Error loading chat history:', error);
            }
        }

        async function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();

            if (message && currentStoreName) {
                const formData = new FormData();
                formData.append('store_name', currentStoreName);
                formData.append('message', message);
                formData.append('sender_type', 'customer');

                try {
                    const response = await fetch('send_chat_message.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        const messagesContainer = document.getElementById('chatMessages');
                        const messageDiv = document.createElement('div');
                        messageDiv.style.cssText = 'background: linear-gradient(135deg, #2c4c7c 0%, #1e3a5f 100%); color: white; padding: 12px 18px; border-radius: 18px; margin-bottom: 12px; max-width: 70%; align-self: flex-end; margin-left: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.08);';
                        messageDiv.textContent = message;
                        messagesContainer.appendChild(messageDiv);
                        input.value = '';
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    } else {
                        showNotification(data.message || 'Failed to send message', 'error');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                }
            }
        }

        // Check follow status on page load
        <?php if (!empty($_GET['store'])): ?>
            checkFollowStatus('<?php echo htmlspecialchars(urldecode($_GET['store'])); ?>');
        <?php endif; ?>
    </script>

    <!-- Chat Modal -->
    <div id="chatModal" class="modal-overlay" style="display: none;">
        <div class="modal-content"
            style="max-width: 600px; max-height: 700px; display: flex; flex-direction: column; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <!-- Header -->
            <div
                style="background: linear-gradient(135deg, #2c4c7c 0%, #1e3a5f 100%); color: white; padding: 20px 25px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div
                        style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold;">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 700;" id="chatStoreName"></h3>
                        <p style="margin: 0; font-size: 12px; opacity: 0.9;">
                            <i class="fas fa-circle" style="font-size: 8px; color: #10b981;"></i> Online
                        </p>
                    </div>
                </div>
                <span class="modal-close" onclick="closeChatModal()"
                    style="cursor: pointer; font-size: 32px; opacity: 0.8; transition: opacity 0.2s;"
                    onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">&times;</span>
            </div>

            <!-- Messages Area -->
            <div id="chatMessages"
                style="flex: 1; overflow-y: auto; padding: 25px; display: flex; flex-direction: column; gap: 12px; background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);">
                <div
                    style="background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%); color: #3730a3; padding: 15px 20px; border-radius: 15px; max-width: 80%; text-align: center; margin: 0 auto; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <i class="fas fa-comments"></i> Start a conversation with the seller
                </div>
            </div>

            <!-- Input Area -->
            <div
                style="padding: 20px 25px; background: #ffffff; border-top: 2px solid #e2e8f0; display: flex; gap: 12px; align-items: center;">
                <button onclick="document.getElementById('chatFileInput').click()"
                    style="background: #f1f5f9; border: none; width: 45px; height: 45px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all 0.2s;"
                    onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" id="chatFileInput" style="display: none;">

                <input type="text" id="chatInput" placeholder="Type your message..."
                    style="flex: 1; padding: 14px 20px; border: 2px solid #e2e8f0; border-radius: 25px; outline: none; font-size: 15px; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#2c4c7c'" onblur="this.style.borderColor='#e2e8f0'"
                    onkeypress="if(event.key === 'Enter') sendMessage()">

                <button onclick="sendMessage()"
                    style="background: linear-gradient(135deg, #2c4c7c 0%, #1e3a5f 100%); color: white; border: none; padding: 14px 28px; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.2s; box-shadow: 0 4px 12px rgba(44,76,124,0.3); display: flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(44,76,124,0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(44,76,124,0.3)'">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        #chatMessages::-webkit-scrollbar {
            width: 8px;
        }

        #chatMessages::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        #chatMessages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        #chatMessages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    </script>
</body>

</html>
