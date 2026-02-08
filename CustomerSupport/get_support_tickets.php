<?php
// CustomerSupport/get_support_tickets.php
session_start();
require_once('../Database/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['support_logged_in']) || $_SESSION['support_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once('functions.php');

// We'll reuse the existing function from functions.php
try {
    // Assuming $pdo is available in connection.php
    require_once('connection.php');
    $pdo = get_db_connection();
    $tickets = get_support_tickets_list($pdo);
    echo json_encode(['success' => true, 'tickets' => $tickets]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
