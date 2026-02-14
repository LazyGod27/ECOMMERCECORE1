<?php
/**
 * Database Migration: Add detailed product information columns
 * This script updates the products table to support enhanced product descriptions
 */

if (!isset($conn)) {
    include 'config.php';
}

// List of columns to add to products table
$columnsToAdd = [
    'material' => "VARCHAR(255) DEFAULT NULL COMMENT 'Material composition (cotton, steel, wood, etc.)'",
    'origin' => "VARCHAR(100) DEFAULT NULL COMMENT 'Country or place of origin'",
    'size_chart' => "JSON DEFAULT NULL COMMENT 'Size chart data in JSON format'",
    'specifications' => "JSON DEFAULT NULL COMMENT 'Product specifications in JSON format'",
    'care_instructions' => "TEXT DEFAULT NULL COMMENT 'Care and maintenance instructions'",
    'warranty' => "VARCHAR(255) DEFAULT NULL COMMENT 'Warranty information'",
    'dimensions' => "VARCHAR(255) DEFAULT NULL COMMENT 'Product dimensions (height x width x depth)'",
    'weight' => "VARCHAR(100) DEFAULT NULL COMMENT 'Product weight with unit'",
];

echo "Starting database upgrade for product details...\n";

foreach ($columnsToAdd as $columnName => $columnDef) {
    // Check if column already exists
    $checkQuery = "SHOW COLUMNS FROM products LIKE '$columnName'";
    $result = $conn->query($checkQuery);
    
    if ($result && $result->num_rows == 0) {
        // Column doesn't exist, add it
        $addQuery = "ALTER TABLE products ADD COLUMN $columnName $columnDef";
        if ($conn->query($addQuery)) {
            echo "✓ Added column: $columnName\n";
        } else {
            echo "✗ Error adding column $columnName: " . $conn->error . "\n";
        }
    } else {
        echo "• Column already exists: $columnName\n";
    }
}

echo "\nDatabase upgrade complete!\n";

/**
 * Function to add product details
 * Usage: updateProductDetails($conn, $productId, [
 *     'material' => 'Cotton, Polyester',
 *     'origin' => 'Bangladesh',
 *     'size_chart' => ['S' => ..., 'M' => ..., 'L' => ..., 'XL' => ...],
 *     'specifications' => [...],
 *     'care_instructions' => '...',
 *     'warranty' => '1 Year',
 *     'weight' => '250g',
 *     'dimensions' => '30cm x 20cm x 10cm'
 * ]);
 */
function updateProductDetails($conn, $productId, $details) {
    $settings = [];
    $values = [];
    
    if (isset($details['material'])) {
        $settings[] = "material = ?";
        $values[] = $details['material'];
    }
    if (isset($details['origin'])) {
        $settings[] = "origin = ?";
        $values[] = $details['origin'];
    }
    if (isset($details['size_chart'])) {
        $settings[] = "size_chart = ?";
        $values[] = is_array($details['size_chart']) ? json_encode($details['size_chart']) : $details['size_chart'];
    }
    if (isset($details['specifications'])) {
        $settings[] = "specifications = ?";
        $values[] = is_array($details['specifications']) ? json_encode($details['specifications']) : $details['specifications'];
    }
    if (isset($details['care_instructions'])) {
        $settings[] = "care_instructions = ?";
        $values[] = $details['care_instructions'];
    }
    if (isset($details['warranty'])) {
        $settings[] = "warranty = ?";
        $values[] = $details['warranty'];
    }
    if (isset($details['weight'])) {
        $settings[] = "weight = ?";
        $values[] = $details['weight'];
    }
    if (isset($details['dimensions'])) {
        $settings[] = "dimensions = ?";
        $values[] = $details['dimensions'];
    }
    
    if (empty($settings)) return false;
    
    $values[] = $productId;
    $query = "UPDATE products SET " . implode(", ", $settings) . " WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    // Build type string
    $types = str_repeat('s', count($settings)) . 'i';
    
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Function to get complete product details
 */
function getProductDetails($conn, $productId) {
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return null;
    }
    
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if ($product) {
        // Decode JSON fields
        if ($product['size_chart']) {
            $product['size_chart'] = json_decode($product['size_chart'], true);
        }
        if ($product['specifications']) {
            $product['specifications'] = json_decode($product['specifications'], true);
        }
    }
    
    return $product;
}

?>
