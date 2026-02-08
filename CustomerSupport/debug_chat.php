<?php
// CustomerSupport/debug_chat.php
require_once __DIR__ . '/connection.php';

echo "<h1>Chat Debug Info</h1>";

try {
    $pdo = get_db_connection();
    
    // Check table structure
    echo "<h2>Table Structure: store_chat_messages</h2>";
    $stmt = $pdo->query("DESCRIBE store_chat_messages");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        foreach ($col as $val) echo "<td>" . htmlspecialchars($val ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check Content
    echo "<h2>Recent Messages</h2>";
    $stmt = $pdo->query("SELECT * FROM store_chat_messages ORDER BY created_at DESC LIMIT 10");
    $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'><tr><th>ID</th><th>User ID</th><th>Store Name</th><th>Sender Type</th><th>Message</th><th>Created At</th></tr>";
    foreach ($msgs as $msg) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($msg['id']) . "</td>";
        echo "<td>" . htmlspecialchars($msg['user_id']) . "</td>";
        echo "<td>'" . htmlspecialchars($msg['store_name']) . "'</td>"; // Surrounded by quotes to see spaces
        echo "<td>" . htmlspecialchars($msg['sender_type']) . "</td>";
        echo "<td>" . htmlspecialchars($msg['message']) . "</td>";
        echo "<td>" . htmlspecialchars($msg['created_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
