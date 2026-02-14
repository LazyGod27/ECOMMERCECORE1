<style>
    .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
    .modal.show { display: flex; }
    .modal-content { background: #fff; padding: 40px; border-radius: 20px; max-width: 380px; width: 90%; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
    .modal-icon { font-size: 50px; color: #ef4444; margin-bottom: 20px; }
    .modal-btn { background: #0f172a; color: #fff; border: none; padding: 12px 30px; border-radius: 10px; cursor: pointer; font-weight: 600; width: 100%; margin-top: 10px; }

    .btn-rate-now { 
        display: inline-block; 
        background: #2c4c7c; 
        color: #fff !important; 
        padding: 10px 20px; 
        border-radius: 8px; 
        text-decoration: none !important; 
        font-weight: 600; 
        font-size: 14px; 
        transition: 0.3s; 
        margin-top: 15px;
    }
    .btn-rate-now:hover { background: #1a355e; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
</style>

<div class="reviews-container">
    <div class="reviews-header">
        Product Ratings
    </div>

    <?php
    // Connect to DB if not already connected
    if (!isset($conn)) {
        $config_path = __DIR__ . '/../../Database/config.php';
        if (file_exists($config_path)) {
            include($config_path);
        } else {
            include('../../Database/config.php');
        }
    }

    // Check if user has purchased the item
    // We expect $name (product name) and user session to be available
    $can_rate = false;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    // We try to use $name from product_template scope. If not available, we can't check by name.
    // Fallback: if $name is not set, we assume FALSE to be safe (show modal)
    $product_name_check = isset($name) ? $name : "";

    if ($user_id > 0 && !empty($product_name_check)) {
        $product_name_esc = mysqli_real_escape_string($conn, $product_name_check);
        // Check orders table
        $order_query = "SELECT id FROM orders WHERE user_id = '$user_id' AND product_name = '$product_name_esc' LIMIT 1";
        $order_result = mysqli_query($conn, $order_query);
        if ($order_result && mysqli_num_rows($order_result) > 0) {
            $can_rate = true;
            $order_row = mysqli_fetch_assoc($order_result);
            $existing_order_id = $order_row['id'];
        }
    }

    // For debugging/demo purposes, if you want to bypass check:
    // $can_rate = true; 
    
    $product_id = isset($product_id) ? $product_id : 1;
    if (isset($conn)) {
        // Fetch from 'reviews' table
        $sql = "SELECT r.*, u.fullname AS user_name 
                FROM reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = '$product_id' 
                ORDER BY r.created_at DESC";

        $result = mysqli_query($conn, $sql);

        if ($result === false) {
            echo "Error: " . mysqli_error($conn);
        } elseif (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $display_name = !empty($row['user_name']) ? $row['user_name'] : "User";
                $user_initial = strtoupper(substr($display_name, 0, 1));
                ?>
                <div class="review-item">
                    <div class="review-avatar">
                        <?php echo $user_initial; ?>
                    </div>
                    <div class="review-author"><?php echo htmlspecialchars($display_name); ?></div>
                    <div class="review-stars">
                        <?php
                        // rating is stored as 'rating' in reviews table
                        $stars = isset($row['rating']) ? intval($row['rating']) : 5;
                        for ($i = 0; $i < $stars; $i++) {
                            echo '<i class="fas fa-star"></i>';
                        }
                        ?>
                    </div>
                    <!-- created_at is the column -->
                    <div class="review-meta"><?php echo date("F j, Y", strtotime($row['created_at'])); ?></div>
                    <div class="review-content"><?php echo nl2br(htmlspecialchars($row['comment'])); ?></div>

                    <?php if (!empty($row['media_url'])):
                        $img_path = '../../' . $row['media_url'];
                        $ext = strtolower(pathinfo($img_path, PATHINFO_EXTENSION));
                        $video_exts = ['mp4', 'mov', 'avi', 'webm', 'mkv'];
                        ?>
                        <div class="review-images">
                            <?php if (in_array($ext, $video_exts)): ?>
                                <video src="<?php echo htmlspecialchars($img_path); ?>" class="review-img" controls
                                    style="object-fit:cover;"></video>
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($img_path); ?>" class="review-img" alt="review image"
                                    onclick="window.open(this.src, '_blank')">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="review-item" style="justify-content: center; color: #888;">
                <div style="text-align:center; padding: 20px 0; color: #94a3b8;"><i class="fas fa-star" style="display:block; font-size: 30px; margin-bottom: 10px; opacity: 0.3;"></i>No ratings yet. Be the first to rate!</div>
            </div>
            <?php
        }
    } else {
        echo "Database connection error.";
    }
    ?>

</div>

<script>
    function showModal() {
        document.getElementById("buyFirstModal").classList.add("show");
    }

    function closeModal() {
        document.getElementById("buyFirstModal").classList.remove("show");
    }

    // Close modal if clicked outside
    window.onclick = function (event) {
        const modal = document.getElementById("buyFirstModal");
        if (event.target == modal) {
            modal.classList.remove("show");
        }
    }
</script>












