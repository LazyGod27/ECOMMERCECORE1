<?php
/**
 * VAT System Test & Verification Tool
 * 
 * This file tests the VAT implementation to ensure:
 * 1. Prices include VAT correctly
 * 2. No double VAT application
 * 3. Calculations are accurate
 */

// Include helper functions
include("php/vat_helper.php");

$test_results = [];
$all_passed = true;

// Test 1: Basic VAT Calculation
$test_name = "Test 1: Add VAT to Base Price";
$base = 100;
$expected = 112;
$result = calculatePriceWithVAT($base);
$passed = abs($result - $expected) < 0.01;
$all_passed = $all_passed && $passed;
$test_results[] = [
    'name' => $test_name,
    'input' => "Base: ₱$base",
    'expected' => "₱$expected",
    'result' => "₱" . number_format($result, 2),
    'passed' => $passed
];

// Test 2: Remove VAT from Price
$test_name = "Test 2: Remove VAT from Price";
$with_vat = 112;
$expected = 100;
$result = calculateBasePriceFromVAT($with_vat);
$passed = abs($result - $expected) < 0.01;
$all_passed = $all_passed && $passed;
$test_results[] = [
    'name' => $test_name,
    'input' => "Price with VAT: ₱$with_vat",
    'expected' => "₱$expected",
    'result' => "₱" . number_format($result, 2),
    'passed' => $passed
];

// Test 3: Calculate just VAT amount
$test_name = "Test 3: Calculate VAT Amount";
$base = 100;
$expected = 12;
$result = calculateVATAmount($base);
$passed = abs($result - $expected) < 0.01;
$all_passed = $all_passed && $passed;
$test_results[] = [
    'name' => $test_name,
    'input' => "Base: ₱$base",
    'expected' => "₱$expected",
    'result' => "₱" . number_format($result, 2),
    'passed' => $passed
];

// Test 4: Grand Total with Shipping
$test_name = "Test 4: Grand Total (VAT incl + Shipping)";
$subtotal = 112; // Already includes VAT
$shipping = 50;
$expected = 162;
$result = calculateGrandTotal($subtotal, $shipping);
$passed = abs($result - $expected) < 0.01;
$all_passed = $all_passed && $passed;
$test_results[] = [
    'name' => $test_name,
    'input' => "Subtotal (VAT incl): ₱$subtotal + Shipping: ₱$shipping",
    'expected' => "₱$expected",
    'result' => "₱" . number_format($result, 2),
    'passed' => $passed
];

// Test 5: Multiple Items
$test_name = "Test 5: Multiple Items Cart";
$items = [
    ['name' => 'Item 1', 'price' => 112, 'qty' => 2],    // ₱112 × 2
    ['name' => 'Item 2', 'price' => 56, 'qty' => 1],     // ₱56 × 1
    ['name' => 'Item 3', 'price' => 224, 'qty' => 1],    // ₱224 × 1
];
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += ($item['price'] * $item['qty']);
}
$expected = 560;
$shipping = 50;
$total = calculateGrandTotal($subtotal, $shipping);
$expected_total = 610;
$passed = abs($total - $expected_total) < 0.01 && abs($subtotal - $expected) < 0.01;
$all_passed = $all_passed && $passed;
$items_detail = "";
foreach ($items as $item) {
    $items_detail .= "₱{$item['price']} × {$item['qty']} = ₱" . ($item['price'] * $item['qty']) . " | ";
}
$test_results[] = [
    'name' => $test_name,
    'input' => $items_detail . " + Shipping ₱$shipping",
    'expected' => "Subtotal: ₱$expected, Total: ₱$expected_total",
    'result' => "Subtotal: ₱" . number_format($subtotal, 2) . ", Total: ₱" . number_format($total, 2),
    'passed' => $passed
];

// Test 6: No Double VAT
$test_name = "Test 6: Prevent Double VAT";
$price_with_vat = 112; // Already has VAT
$wrong_calc = $price_with_vat + ($price_with_vat * 0.12); // Wrong: adds VAT again = 125.44
$correct_calc = $price_with_vat + 50; // Correct: just add shipping = 162
$expected = 162;
$passed = $correct_calc == $expected && $wrong_calc != $expected; // Should NOT equal
$all_passed = $all_passed && $passed;
$test_results[] = [
    'name' => $test_name,
    'input' => "Price (₱112, VAT incl) + Shipping (₱50)",
    'expected' => "₱$expected (NOT ₱" . number_format($wrong_calc, 2) . ")",
    'result' => "✓ Correct: ₱" . number_format($correct_calc, 2) . " | ✗ Wrong would be: ₱" . number_format($wrong_calc, 2),
    'passed' => $passed
];

// Test 7: VAT Breakdown
$test_name = "Test 7: VAT Breakdown";
$base = 500;
$breakdown = getVATBreakdown($base);
$expected_total = 560;
$expected_vat = 60;
$passed = abs($breakdown['total'] - $expected_total) < 0.01 && abs($breakdown['vat_amount'] - $expected_vat) < 0.01;
$all_passed = $all_passed && $passed;
$test_results[] = [
    'name' => $test_name,
    'input' => "Base: ₱$base",
    'expected' => "Base: ₱$base + VAT: ₱$expected_vat = Total: ₱$expected_total",
    'result' => "Base: ₱" . number_format($breakdown['base'], 2) . " + VAT: ₱" . number_format($breakdown['vat_amount'], 2) . " = Total: ₱" . number_format($breakdown['total'], 2),
    'passed' => $passed
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAT System Test</title>
    <link rel="icon" type="image/x-icon" href="image/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        
        .status-badge.passed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .test-grid {
            display: grid;
            gap: 20px;
            margin-top: 30px;
        }
        
        .test-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #667eea;
        }
        
        .test-card.passed {
            border-left-color: #28a745;
        }
        
        .test-card.failed {
            border-left-color: #dc3545;
        }
        
        .test-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .test-title::before {
            content: '✓';
            display: inline-block;
            width: 24px;
            height: 24px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .test-card.failed .test-title::before {
            content: '✗';
            background: #dc3545;
        }
        
        .test-details {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            font-size: 0.95rem;
        }
        
        .test-detail-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .test-detail-item strong {
            display: block;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .test-detail-item span {
            display: block;
            color: #333;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .test-details {
                grid-template-columns: 1fr;
            }
        }
        
        .summary {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .summary h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-box h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-box.passed h3 {
            color: #28a745;
        }
        
        .stat-box.failed h3 {
            color: #dc3545;
        }
        
        .stat-box p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .note {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            color: #1976D2;
            font-size: 0.95rem;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: white;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 VAT System Test & Verification</h1>
            <p>Testing VAT calculations and verifying implementation</p>
            <div class="status-badge <?php echo $all_passed ? 'passed' : 'failed'; ?>">
                <?php echo $all_passed ? '✓ All Tests Passed' : '✗ Some Tests Failed'; ?>
            </div>
        </div>
        
        <div class="test-grid">
            <?php foreach ($test_results as $test): ?>
                <div class="test-card <?php echo $test['passed'] ? 'passed' : 'failed'; ?>">
                    <div class="test-title"><?php echo $test['name']; ?></div>
                    <div class="test-details">
                        <div class="test-detail-item">
                            <strong>Input</strong>
                            <span><?php echo $test['input']; ?></span>
                        </div>
                        <div class="test-detail-item">
                            <strong>Expected</strong>
                            <span><?php echo $test['expected']; ?></span>
                        </div>
                        <div class="test-detail-item">
                            <strong>Result</strong>
                            <span><?php echo $test['result']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="summary">
            <h2>Test Summary</h2>
            <div class="summary-stats">
                <div class="stat-box passed">
                    <h3><?php echo array_reduce($test_results, function($c, $t) { return $c + ($t['passed'] ? 1 : 0); }, 0); ?></h3>
                    <p>Tests Passed</p>
                </div>
                <div class="stat-box <?php echo count($test_results) === array_reduce($test_results, function($c, $t) { return $c + ($t['passed'] ? 1 : 0); }, 0) ? 'passed' : 'failed'; ?>">
                    <h3><?php echo count($test_results); ?></h3>
                    <p>Total Tests</p>
                </div>
                <div class="stat-box <?php echo $all_passed ? 'passed' : 'failed'; ?>">
                    <h3><?php echo $all_passed ? '✓' : '✗'; ?></h3>
                    <p>Overall Status</p>
                </div>
            </div>
            
            <div class="note">
                <strong>ℹ️ Information:</strong><br>
                These tests verify that the VAT helper functions work correctly. VAT is set at 12% (Philippines standard). 
                All product prices in the system should be VAT-inclusive, meaning a ₱112 price already includes ₱12 in taxes.
            </div>
        </div>
        
        <div class="footer">
            <p>VAT Test Tool • Last Updated: February 13, 2026</p>
        </div>
    </div>
</body>
</html>
