<?php
/**
 * API: Create Order in Core 1 (for Core 2 Seller Side)
 *
 * URL (example, local):
 *   http://localhost/ECOMMERCECORE1-main/api_create_order.php
 *
 * Method: POST
 * Content-Type: application/json
 *
 * Request body example:
 * {
 *   "source": "core2",
 *   "external_order_id": "CORE2-ORD-12345",
 *   "customer": {
 *     "full_name": "Juan Dela Cruz",
 *     "phone_number": "09171234567",
 *     "address": "123 Sample St",
 *     "city": "Manila",
 *     "postal_code": "1000"
 *   },
 *   "payment_method": "cod",
 *   "items": [
 *     {
 *       "product_id": 123,                 // Core 1 product ID (preferred)
 *       "external_product_id": "15",       // Or Core 3 external id (fallback)
 *       "name": "Nike Backpack",
 *       "image_url": "https://...",
 *       "quantity": 2,
 *       "unit_price": 2344.00
 *     }
 *   ]
 * }
 *
 * Response (success):
 * {
 *   "success": true,
 *   "order_reference": "ORD-000123",
 *   "tracking_number": "EXT-TRK-ABC12345",
 *   "created_orders": [
 *     { "order_id": 10, "product_id": 123, "quantity": 2, "subtotal": 4688 }
 *   ]
 * }
 */

// Basic CORS support (for demo / development)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use POST with JSON body.'
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

// Ensure extra columns exist on `orders` table for external integration
$check_orders = $conn->query("SHOW TABLES LIKE 'orders'");
if ($check_orders && $check_orders->num_rows > 0) {
    $cols = [
        'external_source'    => "VARCHAR(50) DEFAULT NULL",
        'external_order_id'  => "VARCHAR(100) DEFAULT NULL"
    ];

    foreach ($cols as $col => $def) {
        $res = $conn->query("SHOW COLUMNS FROM orders LIKE '{$col}'");
        if ($res && $res->num_rows === 0) {
            $conn->query("ALTER TABLE orders ADD COLUMN {$col} {$def}");
        }
    }

    // Optional: unique key for (external_source, external_order_id)
    $idxRes = $conn->query("SHOW INDEX FROM orders WHERE Key_name = 'uniq_external_order'");
    if ($idxRes && $idxRes->num_rows === 0) {
        $conn->query("ALTER TABLE orders ADD UNIQUE KEY `uniq_external_order` (`external_source`, `external_order_id`)");
    }
}

// Ensure order_items table exists (minimal structure)
$check_items = $conn->query("SHOW TABLES LIKE 'order_items'");
if ($check_items && $check_items->num_rows === 0) {
    $createItemsSql = "
        CREATE TABLE IF NOT EXISTS `order_items` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `order_id` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          `quantity` int(11) NOT NULL,
          `price` decimal(10,2) NOT NULL,
          `subtotal` decimal(10,2) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          KEY `order_id` (`order_id`),
          KEY `product_id` (`product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $conn->query($createItemsSql);
}

// Read JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON body.'
    ]);
    exit;
}

// Validate required fields
$source = isset($data['source']) ? trim($data['source']) : 'core2';
$externalOrderId = isset($data['external_order_id']) ? trim($data['external_order_id']) : null;
$customer = $data['customer'] ?? null;
$paymentMethod = isset($data['payment_method']) ? trim($data['payment_method']) : null;
$items = $data['items'] ?? null;

if (empty($externalOrderId) || !is_array($customer) || !is_array($items) || count($items) === 0 || empty($paymentMethod)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing required fields. Required: external_order_id, customer, payment_method, items[].'
    ]);
    exit;
}

// Customer fields
$fullName    = $customer['full_name']    ?? '';
$phoneNumber = $customer['phone_number'] ?? '';
$address     = $customer['address']      ?? '';
$city        = $customer['city']         ?? '';
$postalCode  = $customer['postal_code']  ?? '';

if ($fullName === '' || $phoneNumber === '' || $address === '' || $city === '' || $postalCode === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Incomplete customer information.'
    ]);
    exit;
}

// Generate tracking number similar style but marked as external
$trackingNumber = 'EXT-TRK-' . strtoupper(bin2hex(random_bytes(4)));

// Try to prevent duplicate creation if same external_order_id already exists
$dupCheckStmt = $conn->prepare("SELECT id FROM orders WHERE external_source = ? AND external_order_id = ? LIMIT 1");
if ($dupCheckStmt) {
    $dupCheckStmt->bind_param('ss', $source, $externalOrderId);
    $dupCheckStmt->execute();
    $dupRes = $dupCheckStmt->get_result();
    if ($dupRes && $dupRes->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'error' => 'Order with this external_order_id already exists.',
        ]);
        $dupCheckStmt->close();
        exit;
    }
    $dupCheckStmt->close();
}

// Prepare INSERT for orders table (one row per item, like Confirmation.php)
$orderSql = "
INSERT INTO orders (
  user_id,
  tracking_number,
  product_id,
  product_name,
  quantity,
  price,
  total_amount,
  full_name,
  phone_number,
  address,
  city,
  postal_code,
  payment_method,
  status,
  image_url,
  created_at,
  external_source,
  external_order_id
) VALUES (
  0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?, NOW(), ?, ?
)";

$orderStmt = $conn->prepare($orderSql);
if (!$orderStmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to prepare order insert statement.'
    ]);
    exit;
}

// Prepare INSERT for order_items
$itemSql = "
INSERT INTO order_items (order_id, product_id, quantity, price, subtotal, created_at)
VALUES (?, ?, ?, ?, ?, NOW())
";
$itemStmt = $conn->prepare($itemSql);

$createdOrders = [];
$totalAmount = 0.0;

$conn->begin_transaction();

try {
    foreach ($items as $item) {
        if (!is_array($item)) continue;

        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $unitPrice = isset($item['unit_price']) ? (float)$item['unit_price'] : 0.0;
        if ($qty <= 0 || $unitPrice < 0) {
            continue;
        }

        $productId = isset($item['product_id']) ? (int)$item['product_id'] : 0;

        // If no internal product_id given, try to map using external_product_id (Core 3 id)
        if ($productId === 0 && !empty($item['external_product_id'])) {
            $externalPid = (string)$item['external_product_id'];
            $mapStmt = $conn->prepare("SELECT id FROM products WHERE external_core3_id = ? LIMIT 1");
            if ($mapStmt) {
                $mapStmt->bind_param('s', $externalPid);
                $mapStmt->execute();
                $mapRes = $mapStmt->get_result();
                if ($mapRes && $mapRes->num_rows > 0) {
                    $row = $mapRes->fetch_assoc();
                    $productId = (int)$row['id'];
                }
                $mapStmt->close();
            }
        }

        $productName = isset($item['name']) ? trim($item['name']) : 'External Product';
        $imageUrl = isset($item['image_url']) ? trim($item['image_url']) : null;

        $itemTotal = $qty * $unitPrice;
        $totalAmount += $itemTotal;

        $orderStmt->bind_param(
            'sisdsssssssss',
            $trackingNumber,
            $productId,
            $productName,
            $qty,
            $unitPrice,
            $itemTotal,
            $fullName,
            $phoneNumber,
            $address,
            $city,
            $postalCode,
            $paymentMethod,
            $imageUrl,
            $source,
            $externalOrderId
        );

        if (!$orderStmt->execute()) {
            throw new Exception('Failed to insert order row: ' . $orderStmt->error);
        }

        $orderId = $conn->insert_id;

        if ($itemStmt) {
            $itemStmt->bind_param('iiidd', $orderId, $productId, $qty, $unitPrice, $itemTotal);
            if (!$itemStmt->execute()) {
                throw new Exception('Failed to insert order_items row: ' . $itemStmt->error);
            }
        }

        $createdOrders[] = [
            'order_id'   => $orderId,
            'product_id' => $productId,
            'quantity'   => $qty,
            'subtotal'   => $itemTotal
        ];
    }

    if (count($createdOrders) === 0) {
        throw new Exception('No valid items were processed.');
    }

    $conn->commit();

    // Use first order id to build a reference similar to Confirmation.php
    $firstOrderId = $createdOrders[0]['order_id'];
    $orderRef = 'ORD-' . str_pad($firstOrderId, 6, '0', STR_PAD_LEFT);

    echo json_encode([
        'success' => true,
        'order_reference' => $orderRef,
        'tracking_number' => $trackingNumber,
        'total_amount' => $totalAmount,
        'created_orders' => $createdOrders
    ]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

if ($orderStmt) {
    $orderStmt->close();
}
if ($itemStmt) {
    $itemStmt->close();
}

?>

