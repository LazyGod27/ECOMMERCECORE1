<?php
// CustomerSupport/get_ticket_replies.php
session_start();
require_once('../Database/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['support_logged_in']) || $_SESSION['support_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$ticket_id = $_GET['ticket_id'] ?? 0;
if (!$ticket_id) {
    echo json_encode(['success' => false, 'replies' => []]);
    exit();
}

try {
    // Re-use $pdo from config.php if it exists, otherwise use $conn
    // Dashboard.php uses PDO, config.php uses mysqli.
    // I should check how dashboard.php initializes PDO.
    $stmt = $conn->prepare("SELECT * FROM ticket_replies WHERE ticket_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $replies = [];
    while ($row = $result->fetch_assoc()) {
        $replies[] = $row;
    }
    echo json_encode(['success' => true, 'replies' => $replies]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
