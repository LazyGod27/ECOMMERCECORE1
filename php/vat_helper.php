<?php
/**
 * VAT Helper Functions
 * 
 * Handles VAT (Value Added Tax) calculations and formatting
 * Philippines VAT Rate: 12%
 */

// VAT Configuration
define('VAT_RATE', 0.12); // 12% VAT for Philippines

/**
 * Calculate price WITH VAT included
 * 
 * @param float $base_price - Price without VAT
 * @return float - Price with VAT included
 * 
 * Example: calculatePriceWithVAT(100) returns 112
 */
function calculatePriceWithVAT($base_price) {
    return $base_price * (1 + VAT_RATE);
}

/**
 * Calculate base price FROM price with VAT
 * (Reverse calculation)
 * 
 * @param float $price_with_vat - Price that includes VAT
 * @return float - Base price without VAT
 * 
 * Example: calculateBasePriceFromVAT(112) returns 100
 */
function calculateBasePriceFromVAT($price_with_vat) {
    return $price_with_vat / (1 + VAT_RATE);
}

/**
 * Calculate just the VAT amount from a base price
 * 
 * @param float $base_price - Price without VAT
 * @return float - VAT amount only
 * 
 * Example: calculateVATAmount(100) returns 12
 */
function calculateVATAmount($base_price) {
    return $base_price * VAT_RATE;
}

/**
 * Format price with VAT label
 * 
 * @param float $price - Price (assumed to include VAT)
 * @param bool $show_label - Add "(VAT Incl.)" text
 * @return string - Formatted price string
 */
function formatPriceWithVAT($price, $show_label = false) {
    $formatted = '₱' . number_format($price, 2);
    if ($show_label) {
        $formatted .= ' <span style="font-size: 0.8rem; color: #777;">(VAT incl.)</span>';
    }
    return $formatted;
}

/**
 * Get VAT rate as percentage string
 * 
 * @return string - "12%"
 */
function getVATRatePercent() {
    return (VAT_RATE * 100) . '%';
}

/**
 * Display price with VAT breakdown (for informational purposes)
 * 
 * @param float $base_price - Price without VAT
 * @return array - Array with 'base', 'vat', 'total' keys
 * 
 * Example:
 * $breakdown = getVATBreakdown(100);
 * Array (
 *   'base' => 100,
 *   'vat' => 12,
 *   'total' => 112
 * )
 */
function getVATBreakdown($base_price) {
    $vat_amount = calculateVATAmount($base_price);
    $total = $base_price + $vat_amount;
    
    return [
        'base' => $base_price,
        'vat_amount' => $vat_amount,
        'vat_rate' => VAT_RATE,
        'total' => $total
    ];
}

/**
 * Calculate cart total with VAT and shipping
 * 
 * @param float $subtotal - Subtotal of items (already VAT-inclusive)
 * @param float $shipping_fee - Shipping cost (fixed or calculated)
 * @return float - Grand total
 */
function calculateGrandTotal($subtotal, $shipping_fee = 50.00) {
    // Prices already include VAT, so just add shipping
    return $subtotal + $shipping_fee;
}

?>
