<?php
/**
 * Fetch sellers from iMarket Seller Center API
 * https://sellercenter.imarketph.com/api_seller_info.php
 *
 * @return array List of sellers with shop_name (filters out incomplete records)
 */
function fetchSellersFromApi() {
    $url = 'https://sellercenter.imarketph.com/api_seller_info.php';
    $sellers = [];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $body = @file_get_contents($url, false, $ctx);
    }

    if (empty($body)) {
        return [];
    }

    $json = json_decode($body, true);
    if (empty($json['success']) || empty($json['data']) || !is_array($json['data'])) {
        return [];
    }

    foreach ($json['data'] as $s) {
        $shopName = $s['shop_name'] ?? null;
        if (!empty($shopName) && trim($shopName) !== '') {
            $sellers[] = [
                'id' => $s['id'] ?? '',
                'shop_name' => trim($shopName),
                'business_type' => $s['business_type'] ?? null,
                'seller_type' => $s['seller_type'] ?? null,
                'logo' => $s['logo'] ?? null,
                'contact_info' => $s['contact_info'] ?? null,
                'warehouse_address' => $s['warehouse_address'] ?? null,
                'policies' => $s['policies'] ?? null,
            ];
        }
    }

    return $sellers;
}
