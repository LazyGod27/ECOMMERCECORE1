<?php
// Simple direct test without the form
require_once 'order_helper.php';

$testOrder = [
    'order_id' => 'TEST_' . time(),
    'customer' => [
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone_number' => '09123456789',
        'address' => '123 Test St',
        'city' => 'Manila',
        'postal_code' => '1000'
    ],
    'items' => [
        [
            'product_name' => 'LABUBU',
            'price' => 899.00,
            'quantity' => 1,
            'seller_name' => 'Balnce'
        ]
    ],
    'payment_method' => 'cod'
];

echo "Testing Core 2 API...\n\n";
$result = sendOrderToCore2(null, $testOrder);

echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
echo "Message: " . $result['message'] . "\n\n";

if (isset($result['responses'][0])) {
    $resp = $result['responses'][0];
    echo "HTTP Code: " . $resp['http_code'] . "\n";
    
    if ($resp['curl_error']) {
        echo "cURL Error: " . $resp['curl_error'] . "\n";
    }
    
    if ($resp['decoded_response']) {
        echo "API Response:\n";
        print_r($resp['decoded_response']);
    }
}
?>