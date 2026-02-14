<?php
/**
 * Batch update all category card.php files to add variant swatches
 * This script adds 'variants' => [] to each product and updates card markup
 */

$categories = [
    'electronics',
    'fashion-apparel',
    'home-living',
    'beauty-health',
    'groceries',
    'new-arrivals',
    'sports-outdoor',
    'toys-games'
];

$base_path = __DIR__ . '/Categories/';

foreach ($categories as $cat) {
    $card_file = $base_path . $cat . '/card.php';
    
    if (!file_exists($card_file)) {
        echo "⚠️  Skipping $cat - card.php not found\n";
        continue;
    }

    $content = file_get_contents($card_file);
    
    // Add 'variants' => [] to each product array
    $updated = preg_replace_callback(
        "/('discount'\s*=>\s*'[^']*')\s*\]/",
        function($matches) {
            return $matches[1] . ",\n\t\t'variants' => []\n\t]";
        },
        $content
    );

    // Update product card markup to include variants
    $updated = str_replace(
        "onclick=\"window.location.href='<?php echo \$product['link']; ?>'\"",
        "data-name=\"<?php echo htmlspecialchars(\$product['name']); ?>\"\n            data-image=\"<?php echo \$product['image']; ?>\"\n            data-variants='<?php echo htmlspecialchars(json_encode(\$product['variants'] ?? [])); ?>'\n            onclick=\"window.location.href='<?php echo \$product['link']; ?>'\"",
        $updated
    );

    // Add variant-swatches container after product image
    $updated = str_replace(
        "<img src=\"<?php echo str_replace(' ', '%20', \$product['image']); ?>\" alt=\"<?php echo htmlspecialchars(\$product['name']); ?>\" class=\"product-img\">\n            </div>",
        "<img src=\"<?php echo str_replace(' ', '%20', \$product['image']); ?>\" alt=\"<?php echo htmlspecialchars(\$product['name']); ?>\" class=\"product-img\">\n                <div class=\"variant-swatches\" aria-hidden=\"true\"></div>\n            </div>",
        $updated
    );

    file_put_contents($card_file, $updated);
    echo "✅ Updated $cat/card.php\n";
}

echo "\nDone! All category card.php files updated.\n";
?>
