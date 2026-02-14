<?php
/**
 * Real-Time Transaction Alert System
 * Monitors transactions and customer support issues
 * Provides integrated notifications for admin dashboard
 */

header('Content-Type: application/json; charset=UTF-8');
session_start();

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication required'
    ]);
    exit();
}

require_once 'connection.php';

try {
    $pdo = get_db_connection();
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit();
}

// Helper function to format time
function format_time_ago($timestamp) {
    $now = new DateTime();
    $past = new DateTime($timestamp);
    $interval = $now->diff($past);
    
    if ($interval->y > 0) return $interval->y . 'y ago';
    if ($interval->m > 0) return $interval->m . 'mo ago';
    if ($interval->d > 0) return $interval->d . 'd ago';
    if ($interval->h > 0) return $interval->h . 'h ago';
    if ($interval->i > 0) return $interval->i . 'm ago';
    return 'just now';
}

// Helper function to identify alert priority
function get_alert_priority($type, $data) {
    switch ($type) {
        case 'high_value_order':
            if ($data['amount'] >= 50000) return 'critical';
            if ($data['amount'] >= 20000) return 'high';
            return 'normal';
        case 'support_urgent':
            return 'critical';
        case 'payment_failed':
            return 'high';
        case 'low_stock':
            return 'warning';
        default:
            return 'normal';
    }
}

// Fetch recent transaction alerts
$alerts = [];

// 1. High-value orders (last 24 hours)
try {
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            'high_value_order' as alert_type,
            o.order_number,
            u.username as customer_name,
            u.email,
            o.total_amount as amount,
            o.created_at,
            o.status
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        AND o.total_amount >= 20000
        ORDER BY o.total_amount DESC
        LIMIT 15
    ");
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $alerts[] = [
            'id' => $row['id'],
            'type' => $row['alert_type'],
            'title' => 'High Value Order',
            'message' => $row['customer_name'] . ' placed a ₱' . number_format($row['amount'], 2) . ' order',
            'icon' => 'trending-up',
            'priority' => get_alert_priority('high_value_order', $row),
            'timestamp' => $row['created_at'],
            'time_ago' => format_time_ago($row['created_at']),
            'related_data' => [
                'order_number' => $row['order_number'],
                'customer' => $row['customer_name'],
                'email' => $row['email'],
                'amount' => $row['amount'],
                'status' => $row['status']
            ]
        ];
    }
} catch (PDOException $e) {
    error_log('Error fetching high-value orders: ' . $e->getMessage());
}

// 2. Pending payments (orders not yet confirmed)
try {
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            'pending_payment' as alert_type,
            o.order_number,
            u.username as customer_name,
            u.email,
            o.total_amount as amount,
            o.created_at,
            o.status
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Pending'
        AND o.created_at >= DATE_SUB(NOW(), INTERVAL 3 HOUR)
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $alerts[] = [
            'id' => $row['id'],
            'type' => $row['alert_type'],
            'title' => 'Pending Payment',
            'message' => $row['customer_name'] . ' has an order awaiting confirmation',
            'icon' => 'clock',
            'priority' => 'high',
            'timestamp' => $row['created_at'],
            'time_ago' => format_time_ago($row['created_at']),
            'related_data' => [
                'order_number' => $row['order_number'],
                'customer' => $row['customer_name'],
                'email' => $row['email'],
                'amount' => $row['amount'],
                'status' => $row['status'],
                'action' => 'View Order'
            ]
        ];
    }
} catch (PDOException $e) {
    error_log('Error fetching pending payments: ' . $e->getMessage());
}

// 3. Completed orders for delivery confirmation
try {
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            'delivery_ready' as alert_type,
            o.order_number,
            u.username as customer_name,
            u.email,
            o.total_amount as amount,
            o.created_at,
            o.status
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Processing'
        AND o.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY o.created_at ASC
        LIMIT 8
    ");
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $alerts[] = [
            'id' => $row['id'],
            'type' => $row['alert_type'],
            'title' => 'Ready for Dispatch',
            'message' => 'Order ' . $row['order_number'] . ' is ready to ship',
            'icon' => 'package',
            'priority' => 'normal',
            'timestamp' => $row['created_at'],
            'time_ago' => format_time_ago($row['created_at']),
            'related_data' => [
                'order_number' => $row['order_number'],
                'customer' => $row['customer_name'],
                'email' => $row['email'],
                'amount' => $row['amount'],
                'status' => $row['status']
            ]
        ];
    }
} catch (PDOException $e) {
    error_log('Error fetching processing orders: ' . $e->getMessage());
}

// 4. Check for unread support conversations
try {
    $supportCheckQuery = "
        SELECT COUNT(*) as unread_tickets
        FROM support_tickets
        WHERE status = 'Open'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ";
    
    $stmt = $pdo->prepare($supportCheckQuery);
    $stmt->execute();
    $supportData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($supportData && $supportData['unread_tickets'] > 0) {
        $alerts[] = [
            'id' => 'support-' . time(),
            'type' => 'support_tickets',
            'title' => 'Support Tickets',
            'message' => $supportData['unread_tickets'] . ' open support tickets require attention',
            'icon' => 'message-circle',
            'priority' => 'high',
            'timestamp' => date('Y-m-d H:i:s'),
            'time_ago' => 'now',
            'related_data' => [
                'unread_count' => $supportData['unread_tickets'],
                'action' => 'View Tickets'
            ]
        ];
    }
} catch (PDOException $e) {
    error_log('Error fetching support tickets: ' . $e->getMessage());
}

// Sort alerts by priority and timestamp
usort($alerts, function($a, $b) {
    $priorityOrder = ['critical' => 0, 'high' => 1, 'warning' => 2, 'normal' => 3];
    $priorityA = $priorityOrder[$a['priority']] ?? 3;
    $priorityB = $priorityOrder[$b['priority']] ?? 3;
    
    if ($priorityA !== $priorityB) return $priorityA - $priorityB;
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

// Limit alerts
$alerts = array_slice($alerts, 0, 15);

// Return response
echo json_encode([
    'success' => true,
    'alerts' => $alerts,
    'total_count' => count($alerts),
    'timestamp' => date('Y-m-d H:i:s'),
    'last_update' => format_time_ago(date('Y-m-d H:i:s'))
]);
