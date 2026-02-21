<?php
/**
 * Sellers Directory - Browse all iMarket sellers and their storefronts
 * Fetches sellers from https://sellercenter.imarketph.com/api_seller_info.php
 */
$path_prefix = '../';
include $path_prefix . 'Database/config.php';
include $path_prefix . 'Components/security.php';
require_once __DIR__ . '/fetch_sellers.php';

$sellers = fetchSellersFromApi();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Sellers | iMarket PH</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $path_prefix; ?>image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.5; }
        nav { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }
        .page-header { text-align: center; padding: 40px 0 30px; }
        .page-header h1 { font-size: 2rem; color: #0f172a; margin-bottom: 8px; }
        .page-header p { color: #64748b; font-size: 1.05rem; }
        .sellers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; margin-top: 32px; }
        .seller-card {
            background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s; text-decoration: none; color: inherit; display: block;
        }
        .seller-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
        .seller-card-logo {
            height: 140px; background: linear-gradient(135deg, #e0e7ff 0%, #f0f4ff 100%);
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .seller-card-logo img { max-width: 100%; max-height: 100%; object-fit: contain; padding: 16px; }
        .seller-card-logo .placeholder { font-size: 48px; color: #94a3b8; }
        .seller-card-body { padding: 20px; }
        .seller-card-body h3 { font-size: 1.15rem; color: #0f172a; margin-bottom: 8px; }
        .seller-card-body .meta { font-size: 0.85rem; color: #64748b; margin-bottom: 12px; }
        .seller-card-body .btn { display: inline-flex; align-items: center; gap: 6px; color: #2A3B7E; font-weight: 600; font-size: 0.9rem; }
        .seller-card-body .btn i { font-size: 0.8em; }
        .empty-state { text-align: center; padding: 60px 20px; color: #64748b; }
        .empty-state i { font-size: 64px; color: #cbd5e1; margin-bottom: 16px; }
    </style>
</head>
<body>
    <nav>
        <?php $path_prefix = '../'; include '../Components/header.php'; ?>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-store"></i> Our Sellers</h1>
            <p>Browse trusted sellers and their products on iMarket</p>
        </div>

        <?php if (empty($sellers)): ?>
            <div class="empty-state">
                <i class="fas fa-store-slash"></i>
                <p>No sellers with complete profiles are available at the moment.</p>
                <p style="margin-top: 8px; font-size: 0.9rem;">Sellers are fetched from the iMarket Seller Center.</p>
            </div>
        <?php else: ?>
            <div class="sellers-grid">
                <?php foreach ($sellers as $s): ?>
                    <a href="shop.php?id=<?php echo urlencode($s['id']); ?>&name=<?php echo urlencode($s['shop_name']); ?>" class="seller-card">
                        <div class="seller-card-logo">
                            <?php if (!empty($s['logo'])): ?>
                                <img src="<?php echo htmlspecialchars($s['logo']); ?>" alt="<?php echo htmlspecialchars($s['shop_name']); ?>">
                            <?php else: ?>
                                <span class="placeholder"><i class="fas fa-store"></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="seller-card-body">
                            <h3><?php echo htmlspecialchars($s['shop_name']); ?></h3>
                            <?php if (!empty($s['warehouse_address'])): ?>
                                <div class="meta"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(substr($s['warehouse_address'], 0, 50)); ?>...</div>
                            <?php else: ?>
                                <div class="meta">iMarket Verified Seller</div>
                            <?php endif; ?>
                            <span class="btn">Visit Store <i class="fas fa-chevron-right"></i></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../Components/footer.php'; ?>
</body>
</html>
