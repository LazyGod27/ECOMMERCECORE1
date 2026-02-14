<?php
/**
 * Sample script to add detailed product information to existing products
 * This demonstrates how to populate the new product detail columns
 */

session_start();
include '../Database/config.php';
include '../Database/upgrade_product_details.php';

// Sample product data with complete details
$sampleProducts = [
    1 => [ // Laptop Pro 2025
        'material' => 'Aluminum Alloy, Glass',
        'origin' => 'Taiwan',
        'warranty' => '2 Years Global Warranty',
        'weight' => '1.8kg',
        'dimensions' => '35cm × 24cm × 2cm',
        'specifications' => [
            ['key' => 'Processor', 'value' => 'Intel Core i7 13th Gen'],
            ['key' => 'RAM', 'value' => '16GB DDR5'],
            ['key' => 'Storage', 'value' => '512GB SSD NVMe'],
            ['key' => 'Display', 'value' => '15.6" 4K IPS'],
            ['key' => 'Battery', 'value' => '100Wh (12 hours)'],
            ['key' => 'Ports', 'value' => '2x Thunderbolt 4, 1x USB-C, HDMI 2.1'],
            ['key' => 'Graphics', 'value' => 'NVIDIA RTX 4070']
        ],
        'care_instructions' => "Keep in cool, dry environment\nClean screen with microfiber cloth only\nUse original charger\nAvoid liquid exposure\nEject storage devices properly\nStore in padded case when not in use"
    ],
    2 => [ // Organic Coffee Beans
        'material' => 'Arabica Coffee Beans, Natural',
        'origin' => 'Ethiopia, Single-Origin',
        'warranty' => 'Satisfaction Guaranteed',
        'weight' => '1000g (1kg)',
        'specifications' => [
            ['key' => 'Type', 'value' => 'Specialty Arabica Coffee'],
            ['key' => 'Origin', 'value' => 'Yirgacheffe Region, Ethiopia'],
            ['key' => 'Altitude', 'value' => '1900-2200 meters'],
            ['key' => 'Processing', 'value' => 'Washed Method'],
            ['key' => 'Roast Level', 'value' => 'Medium Roast'],
            ['key' => 'Flavor Profile', 'value' => 'Floral, Fruity with Blueberry notes'],
            ['key' => 'Acidity', 'value' => 'High'],
            ['key' => 'Certification', 'value' => 'Organic & Fair Trade']
        ],
        'care_instructions' => "Store in airtight container away from light\nKeep away from heat and moisture\nBest consumed within 3 weeks of roast date\nGrind just before brewing\nStore in cool, dark place\nDo not refrigerate or freeze"
    ],
    3 => [ // Noise-Cancelling Headphones
        'material' => 'ABS Plastic, Silicone Ear Cups',
        'origin' => 'South Korea',
        'warranty' => '2 Years Manufacturer',
        'weight' => '245g',
        'dimensions' => '19cm × 18cm × 8.5cm',
        'specifications' => [
            ['key' => 'Driver Size', 'value' => '40mm Dynamic'],
            ['key' => 'Frequency Response', 'value' => '20Hz - 20kHz'],
            ['key' => 'Impedance', 'value' => '32Ω'],
            ['key' => 'Connectivity', 'value' => 'Bluetooth 5.2 + 3.5mm'],
            ['key' => 'Noise Cancellation', 'value' => 'Active (ANC) up to -30dB'],
            ['key' => 'Battery Life', 'value' => '40 hours (Passive mode)'],
            ['key' => 'Charging Time', 'value' => '2 hours via USB-C'],
            ['key' => 'Microphone', 'value' => 'Dual Mic Array with AI Noise Reduction']
        ],
        'care_instructions' => "Clean ear cups with soft, dry cloth\nAvoid water and moisture\nStore in protective case\nGently twist ear cups to remove\nKeep away from heat and direct sunlight\nCharge every 2-3 weeks if not in use"
    ],
    4 => [ // Ergonomic Desk Mat
        'material' => '100% Natural Rubber, Fabric Top',
        'origin' => 'Vietnam',
        'warranty' => '1 Year Limited',
        'weight' => '680g',
        'dimensions' => '90cm × 40cm × 0.3cm',
        'size_chart' => [
            ['Size' => 'Small', 'Width' => '60cm', 'Length' => '30cm', 'Area' => '1,800 cm²'],
            ['Size' => 'Medium', 'Width' => '80cm', 'Length' => '40cm', 'Area' => '3,200 cm²'],
            ['Size' => 'Large', 'Width' => '90cm', 'Length' => '40cm', 'Area' => '3,600 cm²'],
            ['Size' => 'XL', 'Width' => '120cm', 'Length' => '60cm', 'Area' => '7,200 cm²']
        ],
        'specifications' => [
            ['key' => 'Material', 'value' => 'Natural Rubber + Fabric'],
            ['key' => 'Thickness', 'value' => '3mm'],
            ['key' => 'Surface', 'value' => 'Non-slip Textured Fabric'],
            ['key' => 'Base', 'value' => 'Rubber with grip dots'],
            ['key' => 'Color Options', 'value' => 'Grey, Black, Navy Blue'],
            ['key' => 'Weight Capacity', 'value' => 'Supports heavy equipment']
        ],
        'care_instructions' => "Wipe with damp cloth and dry immediately\nDo not soak or submerge\nAvoid exposure to sunlight\nClean spills immediately\nDo not use harsh chemicals\nStore rolled in cool place"
    ],
    5 => [ // Wireless Mouse
        'material' => 'ABS Plastic, Rubber Grip',
        'origin' => 'China',
        'warranty' => '3 Years Limited',
        'weight' => '95g',
        'dimensions' => '12.5cm × 6.5cm × 3.5cm',
        'specifications' => [
            ['key' => 'Connectivity', 'value' => 'Wireless 2.4GHz + Bluetooth'],
            ['key' => 'DPI Range', 'value' => '800 - 3200 DPI (Adjustable)'],
            ['key' => 'Buttons', 'value' => '6 Programmable Buttons'],
            ['key' => 'Polling Rate', 'value' => '125Hz / 250Hz / 500Hz / 1000Hz'],
            ['key' => 'Battery Type', 'value' => '2x AA Batteries'],
            ['key' => 'Battery Life', 'value' => '18 months typical'],
            ['key' => 'Working Range', 'value' => '10 meters']
        ],
        'care_instructions' => "Keep sensor clean with soft cloth\nAvoid liquids and moisture\nUse on appropriate mouse pad\nStore away from heat\nReplace batteries when low\nClean scroll wheel periodically"
    ]
];

// Add detailed information to sample products
echo "<h2>Adding Detailed Product Information</h2>";
echo "<ul>";

foreach ($sampleProducts as $productId => $details) {
    if (updateProductDetails($conn, $productId, $details)) {
        echo "<li>✓ Product ID {$productId} updated successfully</li>";
    } else {
        echo "<li>✗ Error updating Product ID {$productId}</li>";
    }
}

echo "</ul>";

// Verify the data was saved
echo "<h2>Verification</h2>";
echo "<p>Fetching updated product details...</p>";
echo "<ul>";

$sampleIds = [1, 2, 3, 4, 5];
foreach ($sampleIds as $id) {
    $product = getProductDetails($conn, $id);
    if ($product) {
        echo "<li>✓ Product ID {$id}: {$product['name']}<br>";
        echo "  Material: {$product['material']}<br>";
        echo "  Origin: {$product['origin']}<br>";
        echo "  Warranty: {$product['warranty']}</li>";
    }
}

echo "</ul>";

echo "<h2>✓ Product details have been added!</h2>";
echo "<p>You can now view the complete product information in the modal when viewing products in the shop.</p>";
?>
