<?php
/**
 * Sync products from Core 2 (Seller Center) into Core 1 `products` table.
 *
 * Fetches from: https://sellercenter.imarketph.com/api_public_products.php
 * Uses external_core2_id as unique key for upsert.
 *
 * By default syncs only APPROVED products. Use ?include_pending=1 to also sync pending.
 *
 * Run:
 *   Browser: http://localhost/ECOMMERCECORE1/Database/sync_core2_products.php
 *   Browser: http://localhost/ECOMMERCECORE1/Database/sync_core2_products.php?include_pending=1
 *   CLI:     php Database/sync_core2_products.php
 *   CLI:     php Database/sync_core2_products.php --include-pending
 */

include __DIR__ . '/config.php';
include __DIR__ . '/core2_products.php';

header('Content-Type: text/plain; charset=utf-8');

$includePending = isset($_GET['include_pending']) || in_array('--include-pending', $argv ?? [], true);

echo "Starting Core 2 → Core 1 product sync...\n\n";

// 1) Ensure products table and Core 2 columns exist
$checkCols = [
    'external_core2_id' => "VARCHAR(50) DEFAULT NULL",
    'is_core2'          => "TINYINT(1) NOT NULL DEFAULT 0",
];

foreach ($checkCols as $col => $def) {
    $res = $conn->query("SHOW COLUMNS FROM products LIKE '{$col}'");
    if (!$res || $res->num_rows === 0) {
        if ($conn->query("ALTER TABLE products ADD COLUMN {$col} {$def}")) {
            echo "✓ Added column: {$col}\n";
        } else {
            echo "✗ Error adding column {$col}: " . $conn->error . "\n";
        }
    }
}

// 2) Unique index for upsert
$idxCheck = $conn->query("SHOW INDEX FROM products WHERE Key_name = 'uniq_external_core2_id'");
if ($idxCheck && $idxCheck->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD UNIQUE KEY `uniq_external_core2_id` (`external_core2_id`)");
    echo "✓ Added unique index: uniq_external_core2_id\n";
}

// 3) Ensure shop_name exists
$shopCheck = $conn->query("SHOW COLUMNS FROM products LIKE 'shop_name'");
if (!$shopCheck || $shopCheck->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN shop_name VARCHAR(255) DEFAULT NULL");
    echo "✓ Added column: shop_name\n";
}

// 4) Fetch Core 2 products
echo "\nFetching products from Core 2 api_public_products.php...\n";

$options = [
    'approved_only'  => !$includePending,
    'include_pending'=> $includePending,
];
$core2Products = fetchCore2Products($options);

$totalFromApi = count($core2Products);
echo "Found {$totalFromApi} products to sync.\n";

if ($totalFromApi === 0) {
    echo "Nothing to sync. Finished.\n";
    exit;
}

// 5) Category mapping (optional)
$categoryMap = [
    'Electronics'   => 'Electronics & Gadgets',
    'Clothing'      => 'Fashion & Apparel',
    'Accessories'   => 'Accessories',
    'Toys'          => 'Toys & Games',
    'Home & Living' => 'Home & Living',
    'Groceries'     => 'Groceries',
    'Sports'        => 'Sports & Outdoor',
];

// 6) Upsert
$insertSql = "
INSERT INTO products (name, price, image_url, description, status, category, external_core2_id, is_core2, shop_name)
VALUES (?, ?, ?, ?, 'Active', ?, ?, 1, ?)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  price = VALUES(price),
  image_url = VALUES(image_url),
  description = VALUES(description),
  status = 'Active',
  category = VALUES(category),
  shop_name = VALUES(shop_name),
  updated_at = NOW()
";

$stmt = $conn->prepare($insertSql);
if (!$stmt) {
    echo "✗ Failed to prepare upsert: " . $conn->error . "\n";
    exit;
}

$synced = 0;
foreach ($core2Products as $p) {
    $externalId = $p['external_id'] ?? '';
    if ($externalId === '') continue;

    $name       = $p['name'] ?? '';
    $price      = $p['price'] ?? 0;
    $image      = $p['image'] ?? null;
    $desc       = $p['description'] ?? null;
    $cat        = $p['category'] ?? null;
    $sellerName = $p['seller_name'] ?? null;
    $mappedCat  = $categoryMap[$cat] ?? $cat;

    $stmt->bind_param('sdsssss', $name, $price, $image, $desc, $mappedCat, $externalId, $sellerName);

    if ($stmt->execute()) {
        $synced++;
    } else {
        echo "✗ Failed product id={$externalId}: " . $stmt->error . "\n";
    }
}

$stmt->close();

echo "\nSync complete. Products synced/updated: {$synced}.\n";
?>
