<?php
/**
 * Individual Seller Storefront
 * Displays products from Core 3 that belong to this seller (matched by seller_name = shop_name)
 */
$path_prefix = '../';
include $path_prefix . 'Database/config.php';
include $path_prefix . 'Components/security.php';
require_once __DIR__ . '/fetch_sellers.php';
require_once $path_prefix . 'Database/core3_products.php';

$seller_id = $_GET['id'] ?? '';
$seller_name = $_GET['name'] ?? '';

// Fetch seller details from API
$sellers = fetchSellersFromApi();
$seller = null;
foreach ($sellers as $s) {
    if ($s['id'] == $seller_id || $s['shop_name'] === $seller_name) {
        $seller = $s;
        break;
    }
}

// If no match by id, try by name
if (!$seller && !empty($seller_name)) {
    foreach ($sellers as $s) {
        if (strcasecmp(trim($s['shop_name']), trim($seller_name)) === 0) {
            $seller = $s;
            break;
        }
    }
}

// Fetch Core 3 products and filter by this seller's shop_name
$allCore3Products = fetchCore3ApprovedProducts();
$products = [];
if ($seller) {
    $shopName = $seller['shop_name'];
    foreach ($allCore3Products as $p) {
        $sn = $p['seller_name'] ?? '';
        if (!empty($sn) && strcasecmp(trim($sn), trim($shopName)) === 0) {
            $products[] = $p;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $seller ? htmlspecialchars($seller['shop_name']) : 'Seller Store'; ?> | iMarket PH</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $path_prefix; ?>image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.5; }
        nav { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }
        .store-header {
            background: linear-gradient(135deg, #2A3B7E 0%, #3b82f6 100%); color: #fff; border-radius: 16px;
            padding: 32px; margin-bottom: 32px; display: flex; align-items: center; gap: 24px; flex-wrap: wrap;
        }
        .store-logo { width: 100px; height: 100px; border-radius: 12px; background: rgba(255,255,255,0.2); overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .store-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .store-logo .placeholder { font-size: 40px; opacity: 0.8; }
        .store-info h1 { font-size: 1.75rem; margin-bottom: 8px; }
        .store-info .meta { opacity: 0.9; font-size: 0.95rem; margin-bottom: 4px; }
        .store-info .contact { font-size: 0.9rem; opacity: 0.85; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; margin-bottom: 24px; font-size: 0.9rem; }
        .back-link:hover { color: #2A3B7E; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .product-card {
            background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s; text-decoration: none; color: inherit; display: block;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .product-img { aspect-ratio: 1; background: #f1f5f9; overflow: hidden; }
        .product-img img { width: 100%; height: 100%; object-fit: cover; }
        .product-body { padding: 16px; }
        .product-body .title { font-weight: 600; font-size: 0.95rem; color: #0f172a; margin-bottom: 8px; height: 2.4em; overflow: hidden; line-height: 1.2; }
        .product-body .price { font-size: 1.15rem; font-weight: 700; color: #2A3B7E; }
        .empty-products { text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; color: #64748b; }
        .empty-products i { font-size: 48px; color: #cbd5e1; margin-bottom: 16px; }
        .not-found { text-align: center; padding: 60px 20px; }
    </style>
</head>
<body>
    <nav>
        <?php $path_prefix = '../'; include '../Components/header.php'; ?>
    </nav>

    <div class="container">
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> All Sellers</a>

        <?php if (!$seller): ?>
            <div class="not-found">
                <h2>Seller not found</h2>
                <p style="margin-top: 8px; color: #64748b;">The seller you're looking for doesn't exist or has been removed.</p>
                <a href="index.php" style="display: inline-block; margin-top: 16px; color: #2A3B7E; font-weight: 600;">Back to Sellers</a>
            </div>
        <?php else: ?>
            <div class="store-header">
                <div class="store-logo">
                    <?php if (!empty($seller['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($seller['logo']); ?>" alt="<?php echo htmlspecialchars($seller['shop_name']); ?>">
                    <?php else: ?>
                        <span class="placeholder"><i class="fas fa-store"></i></span>
                    <?php endif; ?>
                </div>
                <div class="store-info">
                    <h1><?php echo htmlspecialchars($seller['shop_name']); ?></h1>
                    <?php if (!empty($seller['business_type'])): ?>
                        <div class="meta"><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars(ucfirst($seller['business_type'])); ?> • <?php echo htmlspecialchars($seller['seller_type'] ?? 'Seller'); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($seller['warehouse_address'])): ?>
                        <div class="meta"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($seller['warehouse_address']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($seller['contact_info'])): ?>
                        <div class="contact"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($seller['contact_info']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="empty-products">
                    <i class="fas fa-box-open"></i>
                    <p>This seller has no approved products listed yet.</p>
                    <p style="margin-top: 8px; font-size: 0.9rem;">Products are synced from the Core 3 marketplace. Check back later.</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <a href="<?php echo $path_prefix; ?>Shop/index.php?search=<?php echo urlencode($p['name']); ?>" class="product-card">
                            <div class="product-img">
                                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.onerror=null; this.src='<?php echo $path_prefix; ?>image/logo.png';">
                            </div>
                            <div class="product-body">
                                <div class="title"><?php echo htmlspecialchars($p['name']); ?></div>
                                <div class="price">₱<?php echo number_format($p['price'], 2); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include '../Components/footer.php'; ?>
</body>
</html>
