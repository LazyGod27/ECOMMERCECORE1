<?php
/**
 * Sync APPROVED products from Core 3 into Core 1 `products` table.
 *
 * Flow:
 *  - Fetch products via Core 3 API (see core3_products.php)
 *  - Ensure `products` table and required columns exist
 *  - Upsert each Core 3 product using `external_core3_id` as unique key
 *
 * Run this script manually in browser or CLI when you want to refresh:
 *   - Browser: http://localhost/ECOMMERCECORE1-main/Database/sync_core3_products.php
 *   - CLI: php Database/sync_core3_products.php
 */

include __DIR__ . '/config.php';
include __DIR__ . '/core3_products.php';

header('Content-Type: text/plain; charset=utf-8');

echo "Starting Core 3 → Core 1 product sync...\n\n";

// 1) Ensure `products` table exists (safe if already created)
$createProductsSql = "
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'Active',
  `category` VARCHAR(100) DEFAULT NULL,
  `external_core3_id` VARCHAR(50) DEFAULT NULL,
  `is_core3` TINYINT(1) NOT NULL DEFAULT 0,
  `core3_category` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($conn->query($createProductsSql)) {
    echo "✓ Ensured `products` table exists.\n";
} else {
    echo "✗ Error ensuring `products` table: " . $conn->error . "\n";
}

// 2) Ensure extra columns exist (for Core 3 integration)
$extraCols = [
    'external_core3_id' => "VARCHAR(50) DEFAULT NULL",
    'is_core3'          => "TINYINT(1) NOT NULL DEFAULT 0",
    'core3_category'    => "VARCHAR(100) DEFAULT NULL",
];

foreach ($extraCols as $col => $def) {
    $res = $conn->query("SHOW COLUMNS FROM products LIKE '{$col}'");
    if ($res && $res->num_rows === 0) {
        $alter = "ALTER TABLE products ADD COLUMN {$col} {$def}";
        if ($conn->query($alter)) {
            echo "✓ Added column: {$col}\n";
        } else {
            echo "✗ Error adding column {$col}: " . $conn->error . "\n";
        }
    } else {
        echo "• Column already exists: {$col}\n";
    }
}

// 3) Ensure unique index on external_core3_id for upsert
$idxCheck = $conn->query("SHOW INDEX FROM products WHERE Key_name = 'uniq_external_core3_id'");
if ($idxCheck && $idxCheck->num_rows === 0) {
    $addIdx = "ALTER TABLE products ADD UNIQUE KEY `uniq_external_core3_id` (`external_core3_id`)";
    if ($conn->query($addIdx)) {
        echo "✓ Added unique index: uniq_external_core3_id\n";
    } else {
        echo "✗ Error adding unique index uniq_external_core3_id: " . $conn->error . "\n";
    }
} else {
    echo "• Unique index already exists: uniq_external_core3_id\n";
}

echo "\nFetching approved products from Core 3 API...\n";

// 4) Fetch Core 3 products
$core3Products = fetchCore3ApprovedProducts();
$totalFromApi = count($core3Products);
echo "Found {$totalFromApi} approved, non-flagged products from Core 3.\n";

if ($totalFromApi === 0) {
    echo "Nothing to sync. Finished.\n";
    exit;
}

// Simple category mapping (Core 3 → generic category text, optional)
$categoryMap = [
    'Home & Living' => 'Home & Living',
    'Clothing'      => 'Fashion & Apparel',
    'Electronics'   => 'Electronics & Gadgets',
    'Groceries'     => 'Groceries',
    'Sports'        => 'Sports & Outdoor',
];

// 5) Prepare upsert statement using external_core3_id unique key
$insertSql = "
INSERT INTO products (name, price, image_url, description, status, category, external_core3_id, is_core3, core3_category)
VALUES (?, ?, ?, ?, 'Active', ?, ?, 1, ?)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  price = VALUES(price),
  image_url = VALUES(image_url),
  description = VALUES(description),
  status = 'Active',
  category = VALUES(category),
  core3_category = VALUES(core3_category),
  updated_at = NOW()
";

$stmt = $conn->prepare($insertSql);
if (!$stmt) {
    echo "✗ Failed to prepare upsert statement: " . $conn->error . "\n";
    exit;
}

$synced = 0;

foreach ($core3Products as $p) {
    $name        = $p['name'] ?? '';
    $price       = $p['price'] ?? 0;
    $image       = $p['image'] ?? null;
    $description = $p['description'] ?? null;
    $core3Cat    = $p['category'] ?? null;

    // Map Core 3 category to a generic display category (optional)
    $mappedCat = null;
    if ($core3Cat !== null && $core3Cat !== '') {
        $mappedCat = $categoryMap[$core3Cat] ?? $core3Cat;
    }

    $externalId = $p['external_id'] ?? null;

    if (empty($externalId)) {
        // Skip items without stable id
        continue;
    }

    $stmt->bind_param(
        'sdsssss',
        $name,
        $price,
        $image,
        $description,
        $mappedCat,
        $externalId,
        $core3Cat
    );

    if ($stmt->execute()) {
        $synced++;
    } else {
        echo "✗ Failed to upsert product external_core3_id={$externalId}: " . $stmt->error . "\n";
    }
}

$stmt->close();

echo "\nSync complete. Total products synced/updated: {$synced}.\n";
echo "You can now use these records from the `products` table (e.g., in best sellers, category listings, or product details).\n";

?>

