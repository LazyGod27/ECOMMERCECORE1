<?php
/**
 * Helper: Send Order to Core 2 Seller Side API (api_order.php)
 *
 * This maps Core 1 order data into the JSON format that Core 2's
 * api_order.php expects:
 *
 * {
 *   "customer": { "id", "first_name", "last_name", "email", "phone" },
 *   "seller": "SHOP NAME",
 *   "product": { "name", "price" },
 *   "quantity": 1,
 *   "payment_method": "string",
 *   "shipping_address": "string",
 *   "shipping_city": "string",
 *   "shipping_postal_code": "string"
 * }
 *
 * NOTE: api_order.php handles ONE product per request, so for
 * multi‑item orders we send one request per item.
 *
 * @param mysqli $conn
 * @param array  $orderData
 * @param string $core2ApiUrl
 * @return array
 */
function sendOrderToCore2($conn, $orderData, $core2ApiUrl = null) {
    if ($core2ApiUrl === null) {
        $core2ApiUrl = defined('CORE2_API_URL') ? CORE2_API_URL : 'https://sellercenter.imarketph.com/api_order.php';
    }

    $customerFullName = trim($orderData['customer']['full_name'] ?? '');
    $customerEmail    = $orderData['customer']['email'] ?? '';
    $customerPhone    = $orderData['customer']['phone_number'] ?? '';
    $shippingAddress  = $orderData['customer']['address'] ?? '';
    $shippingCity     = $orderData['customer']['city'] ?? '';
    $shippingPostal   = $orderData['customer']['postal_code'] ?? '';
    $paymentMethod    = $orderData['payment_method'] ?? 'cod';

    $nameParts   = preg_split('/\s+/', $customerFullName, -1, PREG_SPLIT_NO_EMPTY);
    $firstName   = $nameParts[0] ?? '';
    $lastName    = '';
    if (count($nameParts) > 1) {
        $lastName = implode(' ', array_slice($nameParts, 1));
    }

    $responses = [];

    foreach ($orderData['items'] as $item) {
        $productName  = $item['product_name'] ?? 'Unknown Product';
        $productPrice = isset($item['price']) ? (float)$item['price'] : 0.0;
        $quantity     = isset($item['quantity']) ? (int)$item['quantity'] : 1;

        if ($quantity <= 0) {
            $quantity = 1;
        }

        $sellerName = $item['seller_name'] ?? ($item['shop_name'] ?? '');

        if ($sellerName === '') {
            $sellerName = 'Unknown Seller';
        }

        $payload = [
            'customer' => [
                'id'         => $orderData['customer']['id'] ?? null,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $customerEmail,
                'phone'      => $customerPhone,
            ],
            'seller' => $sellerName,
            'product' => [
                'name'  => $productName,
                'price' => $productPrice,
            ],
            'quantity'             => $quantity,
            'payment_method'       => $paymentMethod,
            'shipping_address'     => $shippingAddress,
            'shipping_city'        => $shippingCity,
            'shipping_postal_code' => $shippingPostal,
        ];

        $ch = curl_init($core2ApiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: Core1-Storefront/1.0'
            ],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $responseBody = curl_exec($ch);
        $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        $decoded = null;
        if ($responseBody !== false && $responseBody !== '') {
            $decoded = json_decode($responseBody, true);
        }

        $responses[] = [
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'raw_response' => $responseBody,
            'decoded_response' => $decoded,
            'payload' => $payload,
        ];

        if ($curlError) {
            error_log("Core2 api_order.php cURL error for order {$orderData['order_id']}: {$curlError}");
            return [
                'success' => false,
                'message' => 'Failed to connect to Core 2 API: ' . $curlError,
                'responses' => $responses,
            ];
        }

        if ($httpCode !== 200 && $httpCode !== 201) {
            error_log("Core2 api_order.php returned HTTP {$httpCode} for order {$orderData['order_id']}. Response: {$responseBody}");
            return [
                'success' => false,
                'message' => "Core 2 API returned HTTP {$httpCode}",
                'responses' => $responses,
            ];
        }

        if (!is_array($decoded) || !isset($decoded['success']) || !$decoded['success']) {
            error_log("Core2 api_order.php returned unsuccessful response for order {$orderData['order_id']}: {$responseBody}");
            return [
                'success' => false,
                'message' => 'Core 2 API indicated failure for one of the order items',
                'responses' => $responses,
            ];
        }
    }

    return [
        'success' => true,
        'message' => 'Order sent to Core 2 successfully',
        'responses' => $responses,
    ];
}

?>
