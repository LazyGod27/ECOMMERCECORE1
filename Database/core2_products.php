<?php
/**
 * Core 2 Products Integration Helper
 *
 * Fetches products from Core 2 Seller Center API (api_public_products.php)
 * Returns products in a normalized format for Core 1 sync.
 *
 * API: https://sellercenter.imarketph.com/api_public_products.php
 *
 * Usage:
 *   include __DIR__ . '/core2_products.php';
 *   $products = fetchCore2Products();
 *   $products = fetchCore2Products(['approved_only' => true]);
 */

if (!function_exists('fetchCore2Products')) {
    /**
     * Fetch products from Core 2 api_public_products.php
     *
     * @param array $options [
     *   'approved_only' => bool  // if true, only return approval_status=approved (default: true)
     *   'include_pending' => bool // if true, include pending products (default: false)
     *   'timeout' => int         // timeout in seconds (default: 10)
     * ]
     * @return array List of products. Each product:
     *   [
     *     'external_id'   => string,  // Core 2 product id
     *     'seller_id'     => string,
     *     'name'          => string,
     *     'price'         => float,
     *     'stock'         => int,
     *     'category'      => string,
     *     'description'   => string,
     *     'seller_name'   => string,
     *     'image'         => string,  // first image URL
     *     'images'        => array,   // all image URLs
     *     'approval_status' => string,
     *     'status'        => string,
     *     'weight','width','length' => mixed,
     *     'created_at'    => string,
     *     'raw'           => array
     *   ]
     */
    function fetchCore2Products(array $options = []): array
    {
        $url = defined('CORE2_PRODUCTS_API_URL')
            ? CORE2_PRODUCTS_API_URL
            : 'https://sellercenter.imarketph.com/api_public_products.php';

        $approvedOnly   = $options['approved_only'] ?? true;
        $includePending = $options['include_pending'] ?? false;
        $timeout        = isset($options['timeout']) ? (int)$options['timeout'] : 10;

        $responseBody = null;
        $httpStatus   = null;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_CONNECTTIMEOUT  => $timeout,
                CURLOPT_TIMEOUT         => $timeout,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_SSL_VERIFYHOST  => false,
                CURLOPT_USERAGENT       => 'Core1-Storefront/1.0',
            ]);
            $responseBody = curl_exec($ch);
            $httpStatus   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($responseBody === false) {
                error_log('Core2 Products API cURL error: ' . curl_error($ch));
                return [];
            }
            curl_close($ch);
        } else {
            $ctx = stream_context_create([
                'http' => ['timeout' => $timeout, 'header' => "User-Agent: Core1-Storefront/1.0\r\n"]
            ]);
            $responseBody = @file_get_contents($url, false, $ctx);
            if ($responseBody === false) {
                error_log('Core2 Products API file_get_contents failed.');
                return [];
            }
        }

        if ($httpStatus !== null && $httpStatus !== 200) {
            error_log("Core2 Products API returned HTTP {$httpStatus}");
            return [];
        }

        $decoded = json_decode($responseBody, true);
        if (!is_array($decoded) || empty($decoded['success']) || !isset($decoded['data']['products'])) {
            error_log('Core2 Products API: invalid or empty response.');
            return [];
        }

        $normalized = [];
        foreach ($decoded['data']['products'] as $item) {
            if (!is_array($item)) continue;

            $approval = isset($item['approval_status']) ? strtolower((string)$item['approval_status']) : '';

            // approvedOnly=true: only approved
            // includePending=true: approved + pending
            // otherwise: exclude rejected
            if ($approvedOnly) {
                if ($approval !== 'approved') continue;
            } elseif ($includePending) {
                if ($approval !== 'approved' && $approval !== 'pending') continue;
            } else {
                if ($approval === 'rejected') continue;
            }

            $priceRaw = $item['price'] ?? 0;
            $price    = is_numeric($priceRaw) ? (float)$priceRaw : 0.0;

            $images = $item['images'] ?? [];
            $image  = '';
            if (!empty($images) && is_array($images)) {
                usort($images, function ($a, $b) {
                    return ($a['position'] ?? 0) <=> ($b['position'] ?? 0);
                });
                $first = $images[0];
                $image = isset($first['url']) ? (string)$first['url'] : '';
            }

            $normalized[] = [
                'external_id'     => isset($item['id']) ? (string)$item['id'] : '',
                'seller_id'       => isset($item['seller_id']) ? (string)$item['seller_id'] : '',
                'name'            => isset($item['name']) ? trim((string)$item['name']) : '',
                'price'           => $price,
                'stock'           => isset($item['stock']) ? (int)$item['stock'] : 0,
                'category'        => isset($item['category']) ? (string)$item['category'] : '',
                'description'     => isset($item['description']) ? (string)$item['description'] : '',
                'seller_name'     => isset($item['seller_name']) ? (string)$item['seller_name'] : '',
                'image'           => $image,
                'images'          => array_map(function ($img) {
                    return $img['url'] ?? '';
                }, is_array($images) ? $images : []),
                'approval_status' => $approval,
                'status'          => isset($item['status']) ? (string)$item['status'] : '',
                'weight'          => $item['weight'] ?? null,
                'width'           => $item['width'] ?? null,
                'length'          => $item['length'] ?? null,
                'created_at'      => $item['created_at'] ?? null,
                'raw'             => $item,
            ];
        }

        return $normalized;
    }
}
?>
