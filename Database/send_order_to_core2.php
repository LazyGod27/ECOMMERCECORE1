<?php
/**
 * Send Order to Core 2 Seller Side API (api_order.php)
 *
 * Matches EXACTLY the working HTML test format:
 * {
 *   "customer": { "first_name", "last_name", "email", "phone" },
 *   "seller": "SHOP NAME",
 *   "product": { "name" },
 *   "quantity": 1,
 *   "payment_method": "cod",
 *   "shipping_address": "...",
 *   "shipping_city": "...",
 *   "shipping_postal_code": "..."
 * }
 *
 * NOTE: api_order.php handles ONE product per request, so for
 * multi-item orders we send one request per item.
 *
 * @param mysqli $conn
 * @param array  $orderData
 * @param string $core2ApiUrl
 * @return array
 */
function sendOrderToCore2($conn, $orderData, $core2ApiUrl = null) {
    if ($core2ApiUrl === null) {
        $core2ApiUrl = defined('CORE2_API_URL')
            ? CORE2_API_URL
            : 'https://sellercenter.imarketph.com/api_order.php';
    }

    // ── Customer info ──────────────────────────────────────────────────────────
    $customerFullName = trim($orderData['customer']['full_name']    ?? '');
    $customerEmail    =      $orderData['customer']['email']        ?? '';
    $customerPhone    =      $orderData['customer']['phone_number'] ?? '';
    $shippingAddress  =      $orderData['customer']['address']      ?? '';
    $shippingCity     =      $orderData['customer']['city']         ?? '';
    $shippingPostal   =      $orderData['customer']['postal_code']  ?? '';
    $paymentMethod    = trim($orderData['payment_method']           ?? 'cod');

    // Split full name into first / last
    $nameParts = preg_split('/\s+/', $customerFullName, -1, PREG_SPLIT_NO_EMPTY);
    $firstName = $nameParts[0] ?? '';
    $lastName  = count($nameParts) > 1
                 ? implode(' ', array_slice($nameParts, 1))
                 : '';

    // Core 2 requires BOTH first_name and last_name.
    // If user has only one word (e.g. username), reuse it as last_name.
    if ($lastName === '') {
        $lastName = $firstName !== '' ? $firstName : 'Customer';
    }

    $responses = [];
    $debug     = !empty($orderData['debug']);
    $core2ProductsIndex = null; // lazy-built map: product_name_lower => seller_name

    // Default seller when Core 1 uses generic names that don't exist in Core 2
    $defaultSeller = defined('CORE2_DEFAULT_SELLER') ? CORE2_DEFAULT_SELLER : 'Balnce';
    $genericSellers = ['IMarket Official Store', 'IMarket Best Selling', 'Unknown Seller', 'Imarket', ''];

    foreach ($orderData['items'] as $item) {
        $productName = $item['product_name'] ?? 'Unknown Product';
        $quantity    = max(1, (int)($item['quantity'] ?? 1));
        $sellerName  = trim($item['seller_name'] ?? $item['shop_name'] ?? '');

        // If seller is missing/generic, try to infer the correct Core 2 seller from product name.
        // This prevents mismatches like ordering JUNJIEs products under Balnce.
        if ($sellerName === '' || in_array($sellerName, $genericSellers, true)) {
            if ($core2ProductsIndex === null) {
                $core2ProductsIndex = [];
                $core2Helper = __DIR__ . '/core2_products.php';
                if (file_exists($core2Helper)) {
                    include_once $core2Helper;
                    if (function_exists('fetchCore2Products')) {
                        $all = fetchCore2Products(['approved_only' => false, 'include_pending' => true, 'timeout' => 10]);
                        foreach ($all as $p) {
                            $nm = strtolower(trim((string)($p['name'] ?? '')));
                            $sn = trim((string)($p['seller_name'] ?? ''));
                            if ($nm !== '' && $sn !== '' && !isset($core2ProductsIndex[$nm])) {
                                $core2ProductsIndex[$nm] = $sn;
                            }
                        }
                    }
                }
            }

            $lookupKey = strtolower(trim((string)$productName));
            if ($lookupKey !== '' && is_array($core2ProductsIndex) && isset($core2ProductsIndex[$lookupKey])) {
                $sellerName = $core2ProductsIndex[$lookupKey];
            } else {
                $sellerName = $defaultSeller;
            }
        }

        // ── Build payload exactly like the working test_core2_direct.php ─────────
        $payload = [
            'customer' => [
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $customerEmail,
                'phone'      => $customerPhone,
            ],
            'seller'  => $sellerName,
            'product' => [
                'name' => $productName,   // NO price — matches working HTML exactly
            ],
            'quantity'             => $quantity,
            'payment_method'       => $paymentMethod,
            'shipping_address'     => $shippingAddress,
            'shipping_city'        => $shippingCity,
            'shipping_postal_code' => $shippingPostal,
        ];

        // ── Send via cURL ──────────────────────────────────────────────────────
        $ch = curl_init($core2ApiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'User-Agent: Core1-Storefront/1.0',
            ],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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
            'http_code'        => $httpCode,
            'curl_error'       => $curlError,
            'raw_response'     => $responseBody,
            'decoded_response' => $decoded,
            'payload_sent'     => $payload,
        ];

        if ($debug) {
            error_log('Core2 DEBUG item for order ' . ($orderData['order_id'] ?? 'N/A') . ': ' . json_encode(end($responses)));
        }

        // ── Error handling ─────────────────────────────────────────────────────
        if ($curlError) {
            error_log("Core2 cURL error for order {$orderData['order_id']}: {$curlError}");
            return [
                'success'   => false,
                'message'   => 'Failed to connect to Core 2 API: ' . $curlError,
                'responses' => $responses,
            ];
        }

        if ($httpCode !== 200 && $httpCode !== 201) {
            error_log("Core2 returned HTTP {$httpCode} for order {$orderData['order_id']}. Body: {$responseBody}");
            return [
                'success'   => false,
                'message'   => "Core 2 API returned HTTP {$httpCode}",
                'responses' => $responses,
            ];
        }

        if (!is_array($decoded) || empty($decoded['success'])) {
            error_log("Core2 unsuccessful for order {$orderData['order_id']}: {$responseBody}");
            return [
                'success'   => false,
                'message'   => 'Core 2 API indicated failure: '
                               . ($decoded['message'] ?? 'Unknown error'),
                'responses' => $responses,
            ];
        }
    }

    return [
        'success'   => true,
        'message'   => 'Order(s) sent to Core 2 successfully',
        'responses' => $responses,
    ];
}
?>
