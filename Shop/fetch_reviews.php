<?php
include("../Database/config.php");
$product_name = $_GET['product_name'] ?? '';
$product_id = abs(crc32($product_name)) % 2147483647;

// Table is auto-created in config.php; self-healing for sentiment columns
$res = mysqli_query($conn, "SHOW COLUMNS FROM reviews LIKE 'sentiment'");
if (mysqli_num_rows($res) == 0) {
    mysqli_query($conn, "ALTER TABLE reviews ADD COLUMN sentiment VARCHAR(20) DEFAULT 'Neutral'");
    mysqli_query($conn, "ALTER TABLE reviews ADD COLUMN confidence FLOAT DEFAULT 0.0");
}

$sql = "SELECT r.*, u.fullname FROM reviews r 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.product_id = '$product_id' 
        ORDER BY r.created_at DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $name = htmlspecialchars($row['fullname'] ?? 'User');
        $initial = strtoupper(substr($name, 0, 1));
        $comment = htmlspecialchars($row['comment']);
        $rating = intval($row['rating']);
        $sentiment = $row['sentiment'] ?? 'Neutral';
        $confidence = $row['confidence'] ?? 0;
        $date = date('M d, Y', strtotime($row['created_at']));

        $sentiment_label = '';
        if ($sentiment == 'Positive') $sentiment_label = '<span style="color:#10b981; font-size:11px; font-weight:600; background:#ecfdf5; padding:2px 8px; border-radius:10px; margin-left:10px;">AI: Positive</span>';
        if ($sentiment == 'Negative') $sentiment_label = '<span style="color:#ef4444; font-size:11px; font-weight:600; background:#fef2f2; padding:2px 8px; border-radius:10px; margin-left:10px;">AI: Negative</span>';

        $stars = '';
        for ($i = 0; $i < 5; $i++) {
            if ($i < $rating)
                $stars .= '<i class="fas fa-star"></i>';
            else
                $stars .= '<i class="far fa-star"></i>';
        }

        echo '
        <div class="review-item" style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">
            <div class="review-header" style="display:flex; align-items:center;">
                <span class="user-name" style="font-weight:600;">' . $name . '</span>
                <span class="review-rating" style="color:#f59e0b; margin-left:10px;">' . $stars . '</span>
                ' . $sentiment_label . '
            </div>
            <div class="review-text" style="margin-top:5px; font-size:14px; color:#444;">' . $comment . '</div>
            <div class="review-date" style="font-size:11px; color:#999; margin-top:5px;">' . $date . '</div>
        </div>';
    }
} else {
    echo '<div style="text-align:center; padding:20px; color:#888;">No reviews yet. Be the first to rate!</div>';
}
?>
