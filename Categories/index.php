<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories | IMARKETPH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/components/category-base.css?v=<?php echo time(); ?>">
    <style>
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .category-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        .category-img {
            height: 180px;
            width: 100%;
            object-fit: cover;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #ccc;
        }
        .category-info {
            padding: 15px;
            text-align: center;
        }
        .category-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2c3e50;
            margin: 0;
        }
        .category-count {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <nav>
        <?php $path_prefix = '../'; include '../Components/header.php'; ?>
    </nav>

    <div class="content">
        <div class="section-header" style="text-align: center; margin-top: 40px; margin-bottom: 20px;">
            <h2 style="font-size: 2rem; color: #2c3e50;">Browse Categories</h2>
            <p style="color: #7f8c8d;">Find everything you need across our wide range of categories</p>
        </div>

        <div class="categories-grid">
            <!-- Best Selling -->
            <a href="best_selling/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/best_selling/Foldable%20Laptop%20Stand%20(Aluminum).jpeg'); background-size: cover; background-position: center;">
                    <!-- Fallback icon if image fails to load -->
                </div>
                <div class="category-info">
                    <h3 class="category-title">Best Selling</h3>
                    <p class="category-count">Top Rated Items</p>
                </div>
            </a>

            <!-- New Arrivals -->
            <a href="new-arrivals/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/new-arrivals/Mechanical%20Keyboard%20(RGB).jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">New Arrivals</h3>
                    <p class="category-count">Fresh Drops</p>
                </div>
            </a>

            <!-- Electronics -->
            <a href="electronics/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/electronics/Wireless%20Bluetooth%20Earbuds.jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">Electronics</h3>
                    <p class="category-count">Gadgets & Tech</p>
                </div>
            </a>

            <!-- Fashion & Apparel -->
            <a href="fashion-apparel/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/fashion-apparel/Oversized%20Graphic%20Tee.jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">Fashion & Apparel</h3>
                    <p class="category-count">Clothing & Style</p>
                </div>
            </a>

            <!-- Home & Living -->
            <a href="home-living/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/home-living/Multipurpose%20Blender%20(Portable).jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">Home & Living</h3>
                    <p class="category-count">Decor & Essentials</p>
                </div>
            </a>

            <!-- Beauty & Health -->
            <a href="beauty-health/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/beauty-health/vitamin%20c%20serum.jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">Beauty & Health</h3>
                    <p class="category-count">Care & Wellness</p>
                </div>
            </a>

            <!-- Sports & Outdoor -->
            <a href="sports-outdoor/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/sports-outdoor/Yoga%20Mat%20(Non-Slip).jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">Sports & Outdoor</h3>
                    <p class="category-count">Active Gear</p>
                </div>
            </a>

            <!-- Toys & Games -->
            <a href="toys-games/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/toys-games/Building%20Blocks%20Set%20(500%20pcs).jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">Toys & Games</h3>
                    <p class="category-count">Fun & Play</p>
                </div>
            </a>

            <!-- Groceries -->
            <a href="groceries/index.php" class="category-card">
                <div class="category-img" style="background-image: url('../image/groceries/Organic%20Rolled%20Oats%20(1kg).jpeg'); background-size: cover; background-position: center;"></div>
                <div class="category-info">
                    <h3 class="category-title">Groceries</h3>
                    <p class="category-count">Daily Essentials</p>
                </div>
            </a>
        </div>
    </div>

    <footer>
        <?php include '../Components/footer.php'; ?>
    </footer>
</body>
</html>
