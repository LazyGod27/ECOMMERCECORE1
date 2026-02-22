<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/order_helper.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON input',
        'received' => file_get_contents('php://input')
    ]);
    exit;
}

$orderData = [
    'order_id' => 'TEST_' . time(),
    'customer' => [
        'full_name' => ($input['customer']['first_name'] ?? '') . ' ' . ($input['customer']['last_name'] ?? ''),
        'email' => $input['customer']['email'] ?? '',
        'phone_number' => $input['customer']['phone'] ?? '',
        'address' => $input['shipping_address'] ?? '',
        'city' => $input['shipping_city'] ?? '',
        'postal_code' => $input['shipping_postal_code'] ?? ''
    ],
    'items' => [
        [
            'product_name' => $input['product']['name'] ?? 'Unknown Product',
            'price' => $input['product']['price'] ?? 0,
            'quantity' => $input['quantity'] ?? 1,
            'seller_name' => $input['seller'] ?? 'Unknown Seller'
        ]
    ],
    'payment_method' => $input['payment_method'] ?? 'cod'
];

$result = sendOrderToCore2(null, $orderData);

echo json_encode($result);
