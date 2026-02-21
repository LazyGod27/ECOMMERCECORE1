<?php
/**
 * Core 3 Products Integration Helper
 *
 * Fetches products from Core 3 API and returns only APPROVED, non-flagged products
 * in a normalized PHP array format that Core 1 can easily use.
 *
 * Usage:
 *   include __DIR__ . '/core3_products.php';
 *   $products = fetchCore3ApprovedProducts();
 */

if (!function_exists('fetchCore3ApprovedProducts')) {
    /**
     * Call Core 3 products API and return approved products
     *
     * @param array $options [
     *   'timeout' => int   // timeout in seconds (default 10)
     * ]
     * @return array List of products. Each product shape:
     *   [
     *     'external_id'   => string,
     *     'name'          => string,
     *     'category'      => string,
     *     'description'   => string,
     *     'key_specs'     => array,   // as given by API
     *     'seller_name'   => ?string,
     *     'price'         => float,
     *     'image'         => string,
     *     'approval_status' => string,
     *     'flagged'       => int,
     *     'raw'           => array    // full original item from API
     *   ]
     */
    function fetchCore3ApprovedProducts(array $options = []): array
    {
        $url = 'https://core3.imarketph.com/api_products_local.php';
        $timeout = isset($options['timeout']) ? (int)$options['timeout'] : 10;

        // Prefer cURL for better error handling; fall back to file_get_contents
        $responseBody = null;
        $httpStatus = null;
        $errMsg = null;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_TIMEOUT        => $timeout,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT      => 'Core1-Storefront/1.0',
            ]);

            $responseBody = curl_exec($ch);
            if ($responseBody === false) {
                $errMsg = 'Core3 API cURL error: ' . curl_error($ch);
            }
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            // Fallback: allow_url_fopen must be enabled
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => $timeout,
                        'header'  => "User-Agent: Core1-Storefront/1.0\r\n",
                    ]
                ]);
                $responseBody = @file_get_contents($url, false, $context);
                if ($responseBody === false) {
                    $errMsg = 'Core3 API file_get_contents failed.';
                }
            } catch (Throwable $e) {
                $errMsg = 'Core3 API exception: ' . $e->getMessage();
            }
        }

        if ($errMsg !== null) {
            error_log($errMsg);
            return [];
        }

        // Basic HTTP status check if we have it
        if ($httpStatus !== null && $httpStatus !== 200) {
            error_log("Core3 API returned HTTP status {$httpStatus}");
            return [];
        }

        if ($responseBody === null || $responseBody === '') {
            error_log('Core3 API returned empty response.');
            return [];
        }

        $decoded = json_decode($responseBody, true);
        if (!is_array($decoded)) {
            error_log('Core3 API response is not valid JSON.');
            return [];
        }

        if (empty($decoded['success']) || !isset($decoded['data']) || !is_array($decoded['data'])) {
            // Unexpected structure, but log and fail gracefully
            error_log('Core3 API response missing expected keys (success/data).');
            return [];
        }

        $normalized = [];

        foreach ($decoded['data'] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $approval = isset($item['approval_status']) ? (string)$item['approval_status'] : '';
            $flagged  = isset($item['flagged']) ? (int)$item['flagged'] : 0;

            // Only keep approved and non-flagged products
            if (strtoupper($approval) !== 'APPROVE') {
                continue;
            }
            if ($flagged === 1) {
                continue;
            }

            $priceRaw = isset($item['price']) ? $item['price'] : 0;
            // Make sure price is numeric float
            $price = is_numeric($priceRaw) ? (float)$priceRaw : 0.0;

            $imgRaw = isset($item['image']) ? (string)$item['image'] : '';
            // Ensure image URL is absolute (Core 3 may return relative paths)
            $image = $imgRaw;
            if (!empty($image) && strpos($image, 'http') !== 0) {
                $base = rtrim('https://core3.imarketph.com', '/');
                $image = $base . (strpos($image, '/') === 0 ? '' : '/') . $image;
            }

            $normalized[] = [
                'external_id'     => isset($item['id']) ? (string)$item['id'] : '',
                'name'            => isset($item['name']) ? (string)$item['name'] : '',
                'category'        => isset($item['category']) ? (string)$item['category'] : '',
                'description'     => isset($item['description']) ? (string)$item['description'] : '',
                'key_specs'       => isset($item['key_specs']) && is_array($item['key_specs']) ? $item['key_specs'] : [],
                'seller_name'     => isset($item['seller_name']) ? $item['seller_name'] : null,
                'seller_email'    => isset($item['seller_email']) ? $item['seller_email'] : null,
                'seller_phone'    => isset($item['seller_phone']) ? $item['seller_phone'] : null,
                'seller_rating'   => isset($item['seller_rating']) ? $item['seller_rating'] : null,
                'price'           => $price,
                'image'           => $image,
                'approval_status' => $approval,
                'flagged'         => $flagged,
                'created_at'      => isset($item['created_at']) ? $item['created_at'] : null,
                'updated_at'      => isset($item['updated_at']) ? $item['updated_at'] : null,
                'raw'             => $item, // keep full original for advanced use
            ];
        }

        return $normalized;
    }
}

?>

