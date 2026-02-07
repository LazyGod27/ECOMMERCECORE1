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
                        $name = $adjectives[array_rand($adjectives)] . ' ' . $productNames[array_rand($productNames)];
                        $image = 'https://via.placeholder.com/300x400/f5f5f5/999999?text=' . urlencode($name);

                        $sourceProducts[] = [
                            'name' => $name,
                            'price' => '₱' . number_format($price),
                            'raw_price' => $price,
                            'original_price' => '₱' . number_format($origPrice),
                            'discount' => $discount,
                            'image' => $image,
                            'rating' => 4.0 + (rand(0, 9) / 10),
                            'sold' => rand(100, 5000)
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
                    .best-selling-container {
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
                        <div class="best-selling-container"
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
                                        data-rating="<?php echo $product['rating']; ?>" data-sold="<?php echo $soldDisp; ?>"
                                        data-store="<?php echo htmlspecialchars($selectedStore); ?>"
                                        data-category="<?php echo htmlspecialchars($currentShop['category'] ?? 'General'); ?>"
                                        onclick="openProductModal(this)">

                                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"
                                            class="product-img">
                                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
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
                                                onclick="openProductModal(this)">
                                                <img src="<?php echo $tp['image']; ?>" class="mini-product-img">
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
                                    $soldDisp = ($ap['sold'] > 1000) ? number_format($ap['sold'] / 1000, 1) . 'k' : $ap['sold'];
                                    ?>
                                    <div class="result-card" data-name="<?php echo htmlspecialchars($ap['name']); ?>"
                                        data-price="<?php echo $ap['price']; ?>" data-raw-price="<?php echo $ap['raw_price']; ?>"
                                        data-original-price="<?php echo $ap['original_price'] ?? ''; ?>"
                                        data-discount="<?php echo $ap['discount'] ?? ''; ?>"
                                        data-image="<?php echo $ap['image']; ?>" data-rating="<?php echo $ap['rating']; ?>"
                                        data-sold="<?php echo $soldDisp; ?>"
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
                                            <img src="<?php echo $ap['image']; ?>" class="result-img">
                                            <?php if (!empty($ap['discount'])): ?>
                                                <div style="position: absolute; top: 0; right: 0; background: #ffe910; color: #ee4d2d; padding: 2px 5px; font-size: 10px; font-weight: 700;">
                                                    <?php echo $ap['discount']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="result-info">
                                            <div class="result-title"><?php echo htmlspecialchars($ap['name']); ?></div>
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
                <style>
                    .shop-hero {
                        height: 450px;
                        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #172554 100%);
                        border-radius: 24px;
                        position: relative;
                        overflow: hidden;
                        margin-top: 20px;
                        display: flex;
                        align-items: center;
                        padding: 0 60px;
                        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                    }

                    .hero-glass-card {
                        background: rgba(255, 255, 255, 0.05);
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        padding: 40px;
                        border-radius: 30px;
                        z-index: 2;
                        max-width: 550px;
                        animation: fadeInUp 0.8s ease-out;
                    }

                    @keyframes fadeInUp {
                        from { opacity: 0; transform: translateY(30px); }
                        to { opacity: 1; transform: translateY(0); }
                    }

                    .hero-badge {
                        display: inline-block;
                        padding: 6px 16px;
                        background: linear-gradient(90deg, #3b82f6, #60a5fa);
                        color: white;
                        border-radius: 50px;
                        font-size: 0.85rem;
                        font-weight: 700;
                        margin-bottom: 20px;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
                    }

                    .floating-img-wrapper {
                        flex: 1;
                        display: flex;
                        justify-content: flex-end;
                        z-index: 2;
                        animation: float 6s ease-in-out infinite;
                    }

                    @keyframes float {
                        0%, 100% { transform: translateY(0) rotate(0deg); }
                        50% { transform: translateY(-20px) rotate(2deg); }
                    }

                    .hero-glow {
                        position: absolute;
                        width: 400px;
                        height: 400px;
                        background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
                        border-radius: 50%;
                        filter: blur(40px);
                        pointer-events: none;
                    }

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

                <div class="shop-hero">
                    <div class="hero-glow" style="top: -100px; left: -100px;"></div>
                    <div class="hero-glow" style="bottom: -100px; right: 20%;"></div>
                    
                    <div class="hero-glass-card">
                        <span class="hero-badge">Verified Official Mall</span>
                        <h1 style="font-size: 3.5rem; font-weight: 800; color: white; margin: 0 0 15px; line-height: 1.1; letter-spacing: -1px;">
                            Elevate Your <span style="background: linear-gradient(90deg, #60a5fa, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Shopping</span>
                        </h1>
                        <p style="font-size: 1.1rem; color: rgba(255,255,255,0.8); line-height: 1.6; margin-bottom: 30px;">
                            Discover a curated world of premium brands and exclusive collections. Experience the future of e-commerce today.
                        </p>
                        <div style="display: flex; gap: 15px;">
                            <a href="?search=" class="btn-seller-primary" style="padding: 14px 40px; border-radius: 12px; font-size: 1rem; text-decoration: none; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                                Explore All Stores
                            </a>
                            <a href="#premium-collections" style="padding: 14px 25px; color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; text-decoration: none; font-weight: 600; backdrop-filter: blur(5px);">
                                <i class="fas fa-play" style="margin-right: 8px; font-size: 0.8em;"></i> Highlights
                            </a>
                        </div>
                    </div>

                    <div class="floating-img-wrapper" style="display: flex; align-items: center; justify-content: center; height: 100%;">
                        <div style="position: relative;">
                            <img src="../image/Dashboard/brand%20new%20bag.jpeg" alt="Premium Bag" 
                                 style="height: 350px; width: 320px; object-fit: cover; border-radius: 30px; box-shadow: 0 30px 60px rgba(0,0,0,0.5); border: 8px solid rgba(255,255,255,0.1);">
                            <!-- Floating decorative tags -->
                            <div style="position: absolute; top: 15%; left: -25px; background: white; padding: 10px 15px; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 10px; transform: rotate(-8deg); z-index: 3;">
                                <i class="fas fa-tag" style="color: #3b82f6;"></i>
                                <span style="font-size: 0.8rem; font-weight: 700; color: #1e293b;">SALE -40%</span>
                            </div>
                            <div style="position: absolute; bottom: 20%; right: -20px; background: #1e293b; color: white; padding: 10px 15px; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 10px; transform: rotate(8deg); z-index: 3;">
                                <i class="fas fa-crown" style="color: #f59e0b;"></i>
                                <span style="font-size: 0.8rem; font-weight: 600;">LUXURY</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium Store Collections Section -->
                <div id="premium-collections" class="featured-section" style="margin-top: 60px; margin-bottom: 70px;">
                    <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                <div style="width: 25px; height: 3px; background: #3b82f6; border-radius: 10px;"></div>
                                <span style="font-weight: 800; color: #3b82f6; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;">Curated For You</span>
                            </div>
                            <h2 style="font-size: 2.2rem; color: #0f172a; margin: 0; font-weight: 800;">Premium Store <span style="color: #3b82f6;">Collections</span></h2>
                        </div>
                        <a href="?search=" style="color: #64748b; text-decoration: none; font-weight: 600; font-size: 0.95rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 4px; transition: all 0.3s;" onmouseover="this.style.borderColor='#3b82f6'; this.style.color='#3b82f6'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'">
                            Explore All Mall Brands <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 0.8em;"></i>
                        </a>
                    </div>

                    <div class="featured-row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
                        <!-- Item 1: UrbanWear -->
                        <div class="premium-card" onclick="window.location.href='?store=UrbanWear+PH'">
                            <div class="premium-shop-badge">Official Store</div>
                            <img src="../image/Shop/UrbanWear PH/H&M Loose Fit Hoodie.jpeg" alt="UrbanWear">
                            <div class="premium-overlay">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                    <i class="fas fa-tshirt" style="color: #60a5fa;"></i>
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #60a5fa;">Lifestyle</span>
                                </div>
                                <h3 style="margin: 0; font-size: 1.6rem; font-weight: 800;">UrbanWear PH</h3>
                                <p style="margin: 8px 0 0; font-size: 0.95rem; opacity: 0.8; font-weight: 400;">Premium Streetwear & Style Experts</p>
                                <div style="margin-top: 20px; width: 0; height: 3px; background: #3b82f6; transition: width 0.4s ease;" class="hover-line"></div>
                            </div>
                        </div>

                        <!-- Item 2: TechZone -->
                        <div class="premium-card" onclick="window.location.href='?store=TechZone+PH'">
                            <div class="premium-shop-badge">Tech Partner</div>
                            <img src="../image/electronics/Noise Cancelling Headphones.jpeg" alt="TechZone">
                            <div class="premium-overlay" style="background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 40%, transparent 100%);">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                    <i class="fas fa-bolt" style="color: #a855f7;"></i>
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #a855f7;">Electronics</span>
                                </div>
                                <h3 style="margin: 0; font-size: 1.6rem; font-weight: 800;">TechZone PH</h3>
                                <p style="margin: 8px 0 0; font-size: 0.95rem; opacity: 0.8; font-weight: 400;">Innovation & Future Gadgets</p>
                            </div>
                        </div>

                        <!-- Item 3: GlowUp -->
                        <div class="premium-card" onclick="window.location.href='?store=GlowUp+Beauty'">
                            <div class="premium-shop-badge">Beauty Expert</div>
                            <img src="../image/beauty-health/Charcoal Face Mask.jpeg" alt="GlowUp">
                            <div class="premium-overlay">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                    <i class="fas fa-magic" style="color: #f472b6;"></i>
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #f472b6;">Beauty</span>
                                </div>
                                <h3 style="margin: 0; font-size: 1.6rem; font-weight: 800;">GlowUp Beauty</h3>
                                <p style="margin: 8px 0 0; font-size: 0.95rem; opacity: 0.8; font-weight: 400;">Certified Skincare & Cosmetics</p>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .premium-card:hover .hover-line {
                        width: 50% !important;
                    }
                </style>

                <!-- Featured / Shop Grid (Premium Highlights) -->
                <div class="content-card" style="margin-top: 50px; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;">
                    <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                        <h2 style="font-size: 2.2rem; color: #0f172a; margin-bottom: 12px; font-weight: 800;">Mall <span style="color: #3b82f6;">Highlights</span></h2>
                        <div style="width: 60px; height: 4px; background: #3b82f6; margin: 0 auto 15px; border-radius: 10px;"></div>
                        <p style="color: #64748b; font-size: 1.1rem;">Handpicked essentials from our top-rated official stores</p>
                    </div>

                    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px;">
                        <?php 
                        foreach ($landing_products as $ap):
                            $soldDisp = ($ap['sold'] > 1000) ? number_format($ap['sold'] / 1000, 1) . 'k' : $ap['sold'];
                        ?>
                            <div class="product-card" 
                                style="border: 1px solid #f1f5f9; border-radius: 16px; overflow: hidden; background: #fff; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;"
                                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 25px -5px rgba(0, 0, 0, 0.1)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                                data-name="<?php echo htmlspecialchars($ap['name']); ?>"
                                data-price="<?php echo $ap['price']; ?>" 
                                data-raw-price="<?php echo $ap['raw_price']; ?>"
                                data-original-price="<?php echo $ap['original_price'] ?? ''; ?>"
                                data-discount="<?php echo $ap['discount'] ?? ''; ?>"
                                data-image="<?php echo $ap['image']; ?>" 
                                data-rating="<?php echo $ap['rating'] ?? 4.5; ?>" 
                                data-sold="<?php echo $soldDisp; ?>"
                                data-store="<?php echo htmlspecialchars($ap['shop_name']); ?>" 
                                onclick="openProductModal(this)">
                                
                                <div class="result-img-wrapper" style="aspect-ratio: 1; overflow: hidden; position: relative; background: #f8fafc;">
                                    <img src="<?php echo $ap['image']; ?>" class="result-img" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                    <?php if (!empty($ap['discount'])): ?>
                                        <div style="position: absolute; top: 12px; left: 12px; background: #ef4444; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);">
                                            <?php echo $ap['discount']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="result-info" style="padding: 20px;">
                                    <div style="font-size: 11px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-check-circle" style="font-size: 10px;"></i> Mall Verified
                                    </div>
                                    <div class="result-title" style="font-weight: 600; font-size: 15px; color: #1e293b; margin-bottom: 12px; height: 2.8em; overflow: hidden; line-height: 1.4;"><?php echo htmlspecialchars($ap['name']); ?></div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="result-price" style="color: #0f172a; font-weight: 800; font-size: 18px;"><?php echo $ap['price']; ?></div>
                                        <div style="font-size: 12px; color: #64748b; font-weight: 500; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;"><?php echo $soldDisp; ?> sold</div>
                                    </div>
                                    
                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 24px; height: 24px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #64748b;">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <span style="font-size: 12px; color: #64748b; font-weight: 600;"><?php echo htmlspecialchars($ap['shop_name']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
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
                <img id="modalImg" src="" alt="Product" class="pv-product-img">
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
                    <a id="modalAddToCartBtn" href="#" class="pv-btn pv-btn-cart">
                        <i class="fas fa-cart-plus" style="margin-right: 8px;"></i> Add to Cart
                    </a>
                    <a id="modalBuyNowBtn" href="#" class="pv-btn pv-btn-buy">Buy Now</a>
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

            currentProduct = { name, price, rawPrice, originalPrice, discount, image, store, category };

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
            const baseAdd = `../Content/add-to-cart.php?add_to_cart=1&product_name=${encodeURIComponent(currentProduct.name)}&price=${currentProduct.rawPrice}&store=${encodeURIComponent(currentProduct.store)}&image=${encodeURIComponent(currentProduct.image)}&quantity=${qty}`;

            // Construct URL for Buy Now (Direct Checkout)
            const buyNowUrl = `../Content/Payment.php?product_name=${encodeURIComponent(currentProduct.name)}&price=${currentProduct.rawPrice}&quantity=${qty}&image=${encodeURIComponent(currentProduct.image)}`;

            document.getElementById('modalAddToCartBtn').href = baseAdd;
            document.getElementById('modalBuyNowBtn').href = buyNowUrl;

            // Rate Product Link
            document.getElementById('modalRateLink').href = `Rate-Reviews.php?product_name=${encodeURIComponent(currentProduct.name)}`;
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
        <?php if (isset($selectedStore)): ?>
            checkFollowStatus('<?php echo htmlspecialchars($selectedStore); ?>');
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