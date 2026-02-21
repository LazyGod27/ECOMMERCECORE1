<?php
/**
 * API: Get Orders from Core 1 (for Core 2 Seller Side)
 *
 * Core 2 can GET orders instead of POSTing them. Use this API to retrieve
 * orders placed by customers on the Core 1 storefront.
 *
 * URL (example):
 *   http://localhost/ECOMMERCECORE1/api_get_orders.php
 *   http://localhost/ECOMMERCECORE1/api_get_orders.php?status=Pending
 *   http://localhost/ECOMMERCECORE1/api_get_orders.php?limit=50&offset=0
 *   http://localhost/ECOMMERCECORE1/api_get_orders.php?tracking_number=TRK-ABC123
 *
 * Method: GET
 *
 * Query Parameters (all optional):
 *   - status      Filter by status: Pending, Processing, Shipped, Delivered, Cancelled
 *   - limit       Max records to return (default: 50, max: 200)
 *   - offset      Skip N records for pagination (default: 0)
 *   - tracking_number  Filter by tracking number
 *   - from_date   Orders from this date (Y-m-d, e.g. 2025-01-01)
 *   - to_date     Orders until this date (Y-m-d)
 *
 * Response (success):
 * {
 *   "success": true,
 *   "count": 5,
 *   "total": 42,
 *   "data": [
 *     {
 *       "id": 1,
 *       "order_reference": "ORD-000001",
 *       "tracking_number": "TRK-ABC12345",
 *       "user_id": 2,
 *       "product_id": 15,
 *       "product_name": "Nike Heritage Backpack",
 *       "quantity": 1,
 *       "price": 2344.00,
 *       "total_amount": 2344.00,
 *       "full_name": "Juan Dela Cruz",
 *       "phone_number": "09171234567",
 *       "address": "123 Sample St, Barangay 1",
 *       "city": "Manila",
 *       "postal_code": "1000",
 *       "payment_method": "cod",
 *       "status": "Pending",
 *       "image_url": "https://...",
 *       "created_at": "2025-02-17 10:30:00"
 *     }
 *   ]
 * }
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use GET.'
    ]);
    exit;
}

include __DIR__ . '/Database/config.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed.'
    ]);
    exit;
}

// Parse query params
$status = isset($_GET['status']) ? trim($_GET['status']) : null;
$limit = isset($_GET['limit']) ? min(200, max(1, (int)$_GET['limit'])) : 50;
$offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
$tracking_number = isset($_GET['tracking_number']) ? trim($_GET['tracking_number']) : null;
$from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : null;
$to_date = isset($_GET['to_date']) ? trim($_GET['to_date']) : null;

// Build query
$where = ['1=1'];
$types = '';
$params = [];

if ($status !== null && $status !== '') {
    $where[] = 'o.status = ?';
    $types .= 's';
    $params[] = $status;
}
if ($tracking_number !== null && $tracking_number !== '') {
    $where[] = 'o.tracking_number = ?';
    $types .= 's';
    $params[] = $tracking_number;
}
if ($from_date !== null && $from_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date)) {
    $where[] = 'DATE(o.created_at) >= ?';
    $types .= 's';
    $params[] = $from_date;
}
if ($to_date !== null && $to_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
    $where[] = 'DATE(o.created_at) <= ?';
    $types .= 's';
    $params[] = $to_date;
}

$where_sql = implode(' AND ', $where);

// Count total matching orders
$count_sql = "SELECT COUNT(*) as total FROM orders o WHERE {$where_sql}";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
} else {
    $count_result = $conn->query($count_sql);
}
$total = (int)($count_result->fetch_assoc()['total'] ?? 0);
if (isset($count_stmt)) {
    $count_stmt->close();
}

// Fetch orders
$select_sql = "
    SELECT 
        o.id,
        o.tracking_number,
        o.user_id,
        o.product_id,
        o.product_name,
        o.quantity,
        o.price,
        o.total_amount,
        o.full_name,
        o.phone_number,
        o.address,
        o.city,
        o.postal_code,
        o.payment_method,
        o.status,
        o.image_url,
        o.created_at
    FROM orders o
    WHERE {$where_sql}
    ORDER BY o.created_at DESC
    LIMIT ? OFFSET ?
";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($select_sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to prepare query.'
    ]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => (int)$row['id'],
        'order_reference' => 'ORD-' . str_pad($row['id'], 6, '0', STR_PAD_LEFT),
        'tracking_number' => $row['tracking_number'],
        'user_id' => (int)$row['user_id'],
        'product_id' => (int)$row['product_id'],
        'product_name' => $row['product_name'],
        'quantity' => (int)$row['quantity'],
        'price' => (float)$row['price'],
        'total_amount' => (float)$row['total_amount'],
        'full_name' => $row['full_name'],
        'phone_number' => $row['phone_number'],
        'address' => $row['address'],
        'city' => $row['city'],
        'postal_code' => $row['postal_code'],
        'payment_method' => $row['payment_method'],
        'status' => $row['status'],
        'image_url' => $row['image_url'],
        'created_at' => $row['created_at'],
    ];
}

$stmt->close();

echo json_encode([
    'success' => true,
    'count' => count($data),
    'total' => $total,
    'data' => $data,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
