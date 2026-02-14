<?php
/**
 * Product Price Management & Normalization System
 * 
 * Ensures consistent pricing across the entire platform
 * - Display prices, raw prices, and formatted prices all use the same logic
 * - Prevents price discrepancies between product view and checkout
 */

/**
 * Extract numeric price from any format
 * Handles: ₱299.00, "₱299.00", 299, 299.00, "299"
 * 
 * @param mixed $price - Price in any format
 * @return float - Numeric price value
 */
function getPriceNumeric($price) {
    if (is_numeric($price)) {
        return floatval($price);
    }
    
    // Remove peso sign and any whitespace
    $price = str_replace(['₱', ' ', ','], '', $price);
    
    // Extract just the number
    if (preg_match('/[\d.]+/', $price, $matches)) {
        return floatval($matches[0]);
    }
    
    return 0.0;
}

/**
 * Format price for display with peso sign
 * Returns: ₱299.00
 * 
 * @param mixed $price - Raw numeric price
 * @param bool $with_decimals - Include .00 decimals
 * @return string - Formatted price string
 */
function formatPrice($price, $with_decimals = true) {
    $numeric = getPriceNumeric($price);
    if ($with_decimals) {
        return '₱' . number_format($numeric, 2);
    }
    return '₱' . number_format($numeric, 0);
}

/**
 * Get standardized price object
 * Returns both formatted and raw prices for use anywhere
 * 
 * @param mixed $price - Price in any format
 * @return array - Array with 'raw' and 'formatted' keys
 * 
 * Example:
 * $p = normalizePrice('₱299.50');
 * echo $p['raw'];       // 299.5
 * echo $p['formatted']; // ₱299.50
 */
function normalizePrice($price) {
    $numeric = getPriceNumeric($price);
    return [
        'raw' => $numeric,
        'numeric' => $numeric,
        'formatted' => formatPrice($numeric),
        'raw_var' => $numeric  // For JS data attributes
    ];
}

/**
 * Format product data with consistent pricing
 * 
 * @param array $product - Product array
 * @return array - Product with normalized prices
 */
function normalizeProductPrice(&$product) {
    if (isset($product['price'])) {
        $normalized = normalizePrice($product['price']);
        $product['price'] = $normalized['formatted'];       // Display format ₱299.00
        $product['raw_price'] = $normalized['numeric'];    // Raw number for calculations
        $product['price_numeric'] = $normalized['numeric']; // Explicit numeric
    }
    return $product;
}

/**
 * Ensure all products in array have consistent, normalized prices
 * 
 * @param array $products - Array of products
 * @return array - Products with normalized prices
 */
function normalizeProductPrices(&$products) {
    if (is_array($products)) {
        foreach ($products as &$product) {
            normalizeProductPrice($product);
        }
    }
    return $products;
}

/**
 * Get price for display on product card
 * 
 * @param mixed $price - Any price format
 * @return string - Formatted price: ₱299.00
 */
function getDisplayPrice($price) {
    return formatPrice($price);
}

/**
 * Get price as numeric value for calculations
 * 
 * @param mixed $price - Any price format
 * @return float - Numeric price
 */
function getCalculationPrice($price) {
    return getPriceNumeric($price);
}

/**
 * Add multiple prices together (cart total)
 * 
 * @param array $prices - Array of prices in any format
 * @return float - Sum of all prices
 */
function sumPrices($prices) {
    $total = 0;
    foreach ($prices as $price) {
        $total += getPriceNumeric($price);
    }
    return $total;
}

/**
 * Calculate price with quantity
 * 
 * @param mixed $price - Any price format
 * @param int $quantity - Number of items
 * @return float - Total price
 */
function calculateTotalPrice($price, $quantity = 1) {
    return getPriceNumeric($price) * intval($quantity);
}

/**
 * Format multiple prices and return their sum
 * For displaying cart/checkout subtotal
 * 
 * @param array $prices - Array of prices in any format
 * @return string - Formatted total: ₱5,000.00
 */
function formatTotalPrice($prices) {
    $total = sumPrices($prices);
    return formatPrice($total);
}

/**
 * Apply discount percentage to price
 * 
 * @param mixed $price - Original price
 * @param int $discount_percent - Discount percentage (e.g., 20 for 20%)
 * @return array - Array with original, discount_amount, final_price
 */
function applyDiscountPercent($price, $discount_percent) {
    $numeric = getPriceNumeric($price);
    $discount_amount = $numeric * ($discount_percent / 100);
    $final = $numeric - $discount_amount;
    
    return [
        'original' => $numeric,
        'original_formatted' => formatPrice($numeric),
        'discount_percent' => $discount_percent,
        'discount_amount' => round($discount_amount, 2),
        'discount_formatted' => formatPrice($discount_amount),
        'final' => round($final, 2),
        'final_formatted' => formatPrice($final)
    ];
}

/**
 * Validate that a price is reasonable (>0 and <1000000)
 * 
 * @param mixed $price - Price to validate
 * @return bool - True if valid
 */
function isValidPrice($price) {
    $numeric = getPriceNumeric($price);
    return $numeric > 0 && $numeric < 1000000;
}

/**
 * Round price to 2 decimals
 * 
 * @param float $price - Price to round
 * @return float - Rounded price
 */
function roundPrice($price) {
    return round(getPriceNumeric($price), 2);
}

/**
 * Compare two prices (accounting for formatting differences)
 * 
 * @param mixed $price1 - First price
 * @param mixed $price2 - Second price
 * @return bool - True if prices are equal
 */
function pricesEqual($price1, $price2) {
    return abs(getPriceNumeric($price1) - getPriceNumeric($price2)) < 0.01;
}

/**
 * Get price range from array of products
 * Returns min and max prices
 * 
 * @param array $products - Array of products with 'price' key
 * @return array - ['min' => 100, 'max' => 500, 'min_formatted' => '₱100.00', 'max_formatted' => '₱500.00']
 */
function getPriceRange($products) {
    if (empty($products)) {
        return ['min' => 0, 'max' => 0, 'min_formatted' => '₱0.00', 'max_formatted' => '₱0.00'];
    }
    
    $prices = array_map(function($p) {
        return getPriceNumeric($p['price'] ?? 0);
    }, $products);
    
    $min = min($prices);
    $max = max($prices);
    
    return [
        'min' => $min,
        'max' => $max,
        'min_formatted' => formatPrice($min),
        'max_formatted' => formatPrice($max)
    ];
}

/**
 * Build data attributes for HTML with consistent pricing
 * For use in product cards with JavaScript
 * 
 * @param mixed $price - Price in any format
 * @return string - HTML data attributes
 * 
 * Example output:
 * data-price="₱299.00" data-raw-price="299" data-price-numeric="299"
 */
function buildPriceDataAttributes($price) {
    $normalized = normalizePrice($price);
    return sprintf(
        'data-price="%s" data-raw-price="%.2f" data-price-numeric="%.2f"',
        htmlspecialchars($normalized['formatted']),
        $normalized['numeric'],
        $normalized['numeric']
    );
}

/**
 * Sanitize price from user input (form submission)
 * Removes all non-numeric characters except decimal point
 * 
 * @param string $price - User-submitted price
 * @return float - Numeric price
 */
function sanitizePrice($price) {
    // Remove everything except numbers and decimal point
    $numeric = preg_replace('/[^0-9.]/', '', $price);
    return floatval($numeric);
}

?>
