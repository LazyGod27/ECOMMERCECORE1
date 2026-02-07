<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: #fff;
        padding: 40px;
        border-radius: 20px;
        max-width: 380px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-icon {
        font-size: 50px;
        color: #ef4444;
        margin-bottom: 20px;
    }

    .modal-btn {
        background: #0f172a;
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        margin-top: 10px;
    }

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

    .btn-rate-now:hover {
        background: #1a355e;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Sentiment Badge Styles */
    .sentiment-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: fadeInScale 0.4s ease-out;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    .sentiment-positive {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #10b981;
    }

    .sentiment-negative {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #ef4444;
    }

    .sentiment-neutral {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .sentiment-icon {
        font-size: 11px;
    }

    .review-item {
        background: #fff;
        padding: 30px;
        border-radius: 20px;
        margin-bottom: 25px;
        border: 1px solid #f1f5f9;
        display: flex;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .review-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #3b82f6;
    }

    .review-avatar {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .review-details {
        flex: 1;
    }

    .review-author {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1e293b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .review-author::after {
        content: 'Verified Purchase';
        font-size: 10px;
        background: #f0fdf4;
        color: #166534;
        padding: 2px 8px;
        border-radius: 100px;
        font-weight: 600;
        border: 1px solid #dcfce7;
    }

    .review-stars {
        color: #fbbf24;
        font-size: 13px;
        margin-bottom: 8px;
        letter-spacing: 1px;
    }

    .review-meta {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 15px;
        font-weight: 500;
    }

    .review-content {
        line-height: 1.7;
        color: #334155;
        font-size: 1rem;
        font-weight: 450;
    }

</style>

<div class="reviews-container">
    <div class="reviews-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 30px; padding: 0 10px;">
        <span style="font-weight: 900; font-size: 1.5rem; color: #0f172a; letter-spacing: -0.5px;">Customer Reviews</span>
        <span style="font-size:11px; font-weight: 800; background:#f0f9ff; color:#0369a1; padding:8px 16px; border-radius:100px; border: 1px solid #bae6fd; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(3, 105, 161, 0.1);">
            <i class="fas fa-magic" style="color: #0ea5e9;"></i> AI INSIGHTS ACTIVE
        </span>
    </div>

    <?php include_once __DIR__ . '/../nlp_core.php'; ?>

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

    if ($user_id > 0) {
        $product_name_esc = mysqli_real_escape_string($conn, $product_name_check);
        $p_id_check = intval($product_id);
        // Check orders table - either by name or by specific product_id
        $order_query = "SELECT id FROM orders WHERE user_id = '$user_id' AND (product_id = '$p_id_check' OR product_name = '$product_name_esc') LIMIT 1";
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
        } elseif ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $display_name = !empty($row['user_name']) ? $row['user_name'] : "User";
                $is_sample = (isset($row['order_id']) && $row['order_id'] == 0);
                
                if ($is_sample) {
                    $random_names = ['Maria Clara', 'Juan Dela Cruz', 'Sophia Reyes', 'Kevin Lee', 'Elena Gilbert', 'James Bondoc', 'Rico Blanco', 'Sarah Chen', 'David Kim', 'Jessica Lim', 'Bong Go', 'Robin Padilla', 'Liza Soberano', 'Enrique Gil'];
                    // Use the review ID as a seed so the name stays the same for that specific review on refresh
                    $name_index = $row['id'] % count($random_names);
                    $display_name = $random_names[$name_index];
                }

                $user_initial = strtoupper(substr($display_name, 0, 1));
                $review_id = $row['id']; // Unique ID for JS targeting
                ?>
                <?php
                $avatar_colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'];
                $color_idx = $review_id % count($avatar_colors);
                $bg_color = $avatar_colors[$color_idx];
                ?>
                <div class="review-item">
                    <div class="review-avatar" style="background-color: <?php echo $bg_color; ?>;">
                        <?php echo $user_initial; ?>
                    </div>
                    <div class="review-details">
                        <div class="review-author"><?php echo htmlspecialchars($display_name); ?></div>
                        <div class="review-stars">
                            <?php
                            $stars = isset($row['rating']) ? intval($row['rating']) : 5;
                            for ($i = 0; $i < $stars; $i++) {
                                echo '<i class="fas fa-star"></i>';
                            }
                            ?>
                        </div>
                        <div class="review-meta"><?php echo date("F j, Y", strtotime($row['created_at'])); ?></div>

                        <!-- AI Sentiment Container (Server-Side Pre-rendered) -->
                        <div id="sentiment-container-<?php echo $review_id; ?>" style="min-height: 20px;">
                            <?php 
                            $analysis = analyzeSentimentAI($row['comment']);
                            $sentiment = $analysis['result']['sentiment'];
                            $badgeClass = 'sentiment-neutral';
                            $icon = '<i class="fas fa-minus"></i>';

                            if ($sentiment === 'Positive') {
                                $badgeClass = 'sentiment-positive';
                                $icon = '<i class="fas fa-thumbs-up"></i>';
                            } else if ($sentiment === 'Negative') {
                                $badgeClass = 'sentiment-negative';
                                $icon = '<i class="fas fa-thumbs-down"></i>';
                            }
                            ?>
                            <div class="sentiment-badge <?php echo $badgeClass; ?>">
                                <span class="sentiment-icon"><?php echo $icon; ?></span>
                                <?php echo $sentiment; ?>
                            </div>
                        </div>

                        <div class="review-content" id="review-text-<?php echo $review_id; ?>">
                            <?php echo nl2br(htmlspecialchars($row['comment'])); ?>
                        </div>

                        <?php if (!empty($row['media_url'])):
                            $img_path = '../../' . $row['media_url'];
                            $ext = strtolower(pathinfo($img_path, PATHINFO_EXTENSION));
                            $video_exts = ['mp4', 'mov', 'avi', 'webm', 'mkv'];
                            ?>
                            <div class="review-images" style="margin-top: 15px;">
                                <?php if (in_array($ext, $video_exts)): ?>
                                    <video src="<?php echo htmlspecialchars($img_path); ?>" class="review-img" controls
                                        style="object-fit:cover; border-radius: 12px; max-width: 200px;"></video>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($img_path); ?>" class="review-img" alt="review image"
                                        style="border-radius: 12px; max-width: 200px;"
                                        onclick="window.open(this.src, '_blank')">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            // FALLBACK: Auto-Insert Sample Data if none exists (Self-Healing Demo Data)
            $sample_users = [
                ['fullname' => 'John Mark', 'email_prefix' => 'john.mark'],
                ['fullname' => 'Maria Santos', 'email_prefix' => 'maria.santos'],
                ['fullname' => 'Kevin Lee', 'email_prefix' => 'kevin.lee'],
                ['fullname' => 'Sophia Reyes', 'email_prefix' => 'sophia.reyes'],
                ['fullname' => 'James Bondoc', 'email_prefix' => 'james.bondoc'],
                ['fullname' => 'Elena Gilbert', 'email_prefix' => 'elena.gilbert'],
                ['fullname' => 'Rico Blanco', 'email_prefix' => 'rico.blanco'],
                ['fullname' => 'Sarah Chen', 'email_prefix' => 'sarah.chen'],
                ['fullname' => 'David Kim', 'email_prefix' => 'david.kim'],
                ['fullname' => 'Jessica Lim', 'email_prefix' => 'jessica.lim']
            ];

            $sample_comments = [
                ['name' => 'John Mark', 'rating' => 5, 'comment' => 'Sulit na sulit! The quality is way better than expected.'],
                ['name' => 'Maria Santos', 'rating' => 5, 'comment' => 'Ganda ng item, legit na legit. Mabilis din ang delivery.'],
                ['name' => 'Kevin Lee', 'rating' => 1, 'comment' => 'Pangit ng quality, wag niyo na bilhin. Sayang lang pera.'],
                ['name' => 'Sophia Reyes', 'rating' => 3, 'comment' => 'Sakto lang, pwede na for its price.'],
                ['name' => 'James Bondoc', 'rating' => 5, 'comment' => 'Best purchase so far! Highly recommended to everyone.'],
                ['name' => 'Elena Gilbert', 'rating' => 2, 'comment' => 'Disappointed. The item looks different from the pictures.'],
                ['name' => 'Rico Blanco', 'rating' => 3, 'comment' => 'Okay naman, normal standard quality.']
            ];
            
            foreach ($sample_comments as $sc) {
                $full_name = $sc['name'];
                $r = $sc['rating'];
                $c = mysqli_real_escape_string($conn, $sc['comment']);
                $email = strtolower(str_replace(' ', '.', $full_name)) . rand(100, 999) . "@imarket.ph";
                
                // Create unique user for each review
                mysqli_query($conn, "INSERT INTO users (fullname, email, password) VALUES ('$full_name', '$email', 'demo123')");
                $new_uid = mysqli_insert_id($conn);
                
                if ($new_uid > 0) {
                    // Random date in the last 30 days
                    $random_days = rand(0, 30);
                    mysqli_query($conn, "INSERT INTO reviews (user_id, product_id, order_id, rating, comment, created_at) 
                                        VALUES ('$new_uid', '$product_id', 0, '$r', '$c', DATE_SUB(NOW(), INTERVAL $random_days DAY))");
                }
            }
            // Refresh page once to show new data
            echo "<script>window.location.reload();</script>";
        }
    } else {
        echo "Database connection error.";
    }
    ?>

    <!-- Rate Button -->
    <div class="rate-btn-container">
        <a href="#" class="btn-rate-now" onclick="handleRateClick(event)">Rate Product <i
                class="fas fa-chevron-right"></i></a>
    </div>
</div>

<!-- Buy First Modal -->
<div id="buyFirstModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div class="modal-icon"><i class="fas fa-shopping-cart"></i></div>
        <h3>Purchase Required</h3>
        <p>You need to buy this product first before you can leave a rating.</p>
        <button class="modal-btn" onclick="closeModal()">OK</button>
    </div>
</div>

<script>
    // NLP Analysis Logic
    function analyzeReviewBase(reviewId, text) {
        const container = document.getElementById(`sentiment-container-${reviewId}`);
        if (!container) return;

        // Simulate loading or just wait for response
        // container.innerHTML = '<span style="font-size:10px; color:#999;">Analyzing...</span>';

        fetch('../nlp_processor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: text })
        })
            .then(response => response.json())
            .then(data => {
                if (data.result) {
                    const sentiment = data.result.sentiment; // Positive, Negative, Neutral
                    let badgeClass = 'sentiment-neutral';
                    let icon = '<i class="fas fa-minus"></i>';

                    if (sentiment === 'Positive') {
                        badgeClass = 'sentiment-positive';
                        icon = '<i class="fas fa-thumbs-up"></i>';
                    } else if (sentiment === 'Negative') {
                        badgeClass = 'sentiment-negative';
                        icon = '<i class="fas fa-thumbs-down"></i>';
                    }

                    container.innerHTML = `
                    <div class="sentiment-badge ${badgeClass}">
                        <span class="sentiment-icon">${icon}</span>
                        ${sentiment}
                    </div>
                `;
                }
            })
            .catch(err => console.error("NLP Error:", err));
    }

    // Helper for queuing if scripts load async
    function queueReviewAnalysis(id, text) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            analyzeReviewBase(id, text);
        } else {
            document.addEventListener("DOMContentLoaded", () => analyzeReviewBase(id, text));
        }
    }

    function handleRateClick(e) {
        e.preventDefault();
        // PHP sets this variable based on check
        const canRate = <?php echo $can_rate ? 'true' : 'false'; ?>;
        const productId = <?php echo $product_id; ?>;
        // Ideally pass order_id if available, though Rate.php might need it logic refinement fallback
        const orderId = <?php echo isset($existing_order_id) ? $existing_order_id : 0; ?>;

        if (canRate) {
            window.location.href = `../../Content/Rate.php?product_id=${productId}&order_id=${orderId}`;
        } else {
            showModal();
        }
    }

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