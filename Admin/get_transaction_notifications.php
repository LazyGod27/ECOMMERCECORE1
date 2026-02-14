<?php
/**
 * Get Recent Transaction Notifications
 * This API returns recent orders/transactions from the system
 * for real-time notification display in admin dashboard
 */

require_once 'connection.php';
require_once 'functions.php';

header('Content-Type: application/json');

try {
    $pdo = get_db_connection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Start session to check authentication
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get recent transactions (last 10)
try {
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            CONCAT('#ORD-', o.id) as order_number,
            o.full_name as customer_name,
            o.product_name,
            o.quantity,
            o.total_amount as amount,
            o.payment_method,
            o.status,
            o.created_at as transaction_date,
            u.email as customer_email,
            CASE 
                WHEN o.status = 'Pending' THEN 'warning'
                WHEN o.status = 'Processing' THEN 'info'
                WHEN o.status = 'Shipped' THEN 'success'
                WHEN o.status = 'Delivered' THEN 'success'
                WHEN o.status = 'Cancelled' THEN 'danger'
                ELSE 'secondary'
            END as status_color,
            TIME_TO_SEC(TIMEDIFF(NOW(), o.created_at)) as seconds_ago
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response with notifications
    $notifications = [];
    foreach ($transactions as $tx) {
        $notifications[] = [
            'id' => $tx['id'],
            'title' => 'New Order: ' . $tx['order_number'],
            'message' => $tx['customer_name'] . ' ordered "' . $tx['product_name'] . '" (x' . $tx['quantity'] . ')',
            'amount' => '₱' . number_format($tx['amount'], 2),
            'status' => $tx['status'],
            'status_color' => $tx['status_color'],
            'customer' => $tx['customer_name'],
            'email' => $tx['customer_email'],
            'timestamp' => $tx['transaction_date'],
            'time_ago' => format_time_ago($tx['seconds_ago'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'count' => count($notifications),
        'total_transactions' => get_total_transactions_count($pdo)
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching transactions: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to fetch transactions']);
}

// Helper function to format time ago
function format_time_ago($seconds) {
    if ($seconds < 60) {
        return 'just now';
    } elseif ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        return $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($seconds < 86400) {
        $hours = floor($seconds / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } else {
        $days = floor($seconds / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
}

// Helper function to get total transaction count
function get_total_transactions_count($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}
?>
