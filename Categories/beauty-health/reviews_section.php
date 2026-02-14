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

    if (!isset($conn)) {
        $config_path = __DIR__ . '/../../Database/config.php';
        if (file_exists($config_path)) {
            include($config_path);
        } else {
            include('../../Database/config.php');
        }
    }


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
        // Filter by Product ID AND Product Name to prevent ID collision across categories
        // Since product_id resets to 1 for each category file, we must fallback to name.
        $product_name_filter = "";
        if (!empty($product_name_check)) {
            $esc_name = mysqli_real_escape_string($conn, $product_name_check);
            // Use LIKE or exact match. Exact match is safer if names are consistent.
            // We'll try to join with orders to verify or just assume the reviews table has product_name if we added it?
            // Actually, the reviews table usually relies on product_id. 
            // IF the reviews table schema doesn't have product_name, we can't filter by it directly unless we saved it.
            // Looking at previous context, we might not have saved product_name in reviews.
            // However, to fix the user's issue, we MUST filter better.
            // Let's assume for now we only rely on product_id but maybe the user is viewing ID=1 from Beauty but seeing ID=1 from Best Selling.
            // The only way to differentiate is if the IDs were globally unique or if we saved category/store info.
    
            // WORKAROUND: If we can't change the DB schema right now, we can filter in PHP if necessary, 
            // but let's try to see if we can use the order's product name if the review is linked to an order?
            // The reviews table schema is: id, user_id, product_id, rating, comment, media_url, created_at.
            // It does NOT seem to have product_name. This is a flaw in the design if product_ids are not unique.
    
            // TEMPORARY FIX: We will rely on the fact that maybe the user hasn't reviewed this specific item yet? 
            // but the user says "nakapansin nito yung kaiba yung picture".
            // This means a review EXISTS with product_id=1.
    
            // CRITICAL FIX: We need to filter by `product_name` if it exists in the reviews table. 
            // If not, we are stuck. Let's assume we can filter by exact name if we JOIN orders or if we just added it.
            // Wait, if the review doesn't store product name, we can't separate ID=1 (Bag) from ID=1 (Cleanser).
    
            // PROPOSAL: Filter by `product_id` BUT exclude reviews where the comment keywords clearly mismatch? No that's AI.
            // REAL FIX: The review system needs `product_name` or a global ID.
            // Let's try to add `AND (product_name = '$esc_name')` if the column exists? 
            // User didn't give me the schema.
    
            // Let's try filtering by just `product_id` for now but add a check. 
            // Actually, if I look at `Content/Payment.php`, I see it saves `product_name` in `orders` table. 
            // Does `reviews` table have a link to `orders`? Likely not.
            // Does `reviews` table have `product_name`? I should assume NO unless I added it.
    
            // HACK: I will filter by `product_id`. If the user sees wrong image, it's 100% ID collision.
            // I will try to add `AND product_name = '$esc_name'` blindly? 
            // If it fails, it errors.
    
            // Safer approach: The current `product_id` in THIS file comes from `$product_id = isset($_GET['id'])`.
            // If I change the IDs in `Beauty & Health/product_template.php` to be unique (e.g. 101, 102...), it fixes it for NEW reviews.
            // But existing reviews are already "1".
    
            // Let's try to see if I can simply filter reviews by checking if the review's content matches? No.
    
            // BEST PATH FORWARD: Update the SQL to TRY to filter by product name if possible.
            // If I can't, I should warn the user. 
            // BUT, wait. `Categories/Beauty & Health/index.php` -> `card.php` -> links to `view-product.php?id=1`.
            // If I change `card.php` links to `id=201`, `id=202`, and `product_template.php` keys to `201`, `202`, then collision is solved.
            // This is the cleanest fix without touching DB schema blindly.
            // I will start IDs in Beauty & Health from 200.
        }

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












