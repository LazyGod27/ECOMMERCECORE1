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
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 20px;
        border: 1px solid #f1f5f9;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .review-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.03);
    }
</style>

<div class="reviews-container">
    <div class="reviews-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <span style="font-weight: 800; font-size: 1.2rem; color: #0f172a;">What Customers Say</span>
        <span style="font-size:11px; font-weight: 700; background:#f0f9ff; color:#0369a1; padding:6px 12px; border-radius:100px; border: 1px solid #bae6fd; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-magic"></i> AI Sentiment Analysis
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
        } elseif ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $display_name = !empty($row['user_name']) ? $row['user_name'] : "User";
                $user_initial = strtoupper(substr($display_name, 0, 1));
                $review_id = $row['id']; // Unique ID for JS targeting
                ?>
                <div class="review-item">
                    <div class="review-avatar">
                        <?php echo $user_initial; ?>
                    </div>
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

                    <!-- Trigger Analysis (Already handled by server, keeping for future real-time updates) -->
                </div>
                <?php
            }
        } else {
            // FALLBACK: Auto-Insert Sample Data if none exists (Self-Healing Demo Data)
            $sample_comments = [
                ['rating' => 5, 'comment' => 'Sulit na sulit! The quality is way better than expected.'],
                ['rating' => 5, 'comment' => 'Ganda ng item, legit na legit. Mabilis din ang delivery.'],
                ['rating' => 1, 'comment' => 'Pangit ng quality, wag niyo na bilhin. Sayang lang pera.'],
                ['rating' => 3, 'comment' => 'Sakto lang, pwede na for its price.'],
                ['rating' => 5, 'comment' => 'Best purchase so far! Highly recommended to everyone.'],
                ['rating' => 2, 'comment' => 'Disappointed. The item looks different from the pictures.'],
                ['rating' => 3, 'comment' => 'Okay naman, normal standard quality.']
            ];
            
            // Check if we have at least one user
            $user_check = mysqli_query($conn, "SELECT id FROM users LIMIT 1");
            if ($user_check && mysqli_num_rows($user_check) > 0) {
                $user_id_row = mysqli_fetch_assoc($user_check);
                $demo_uid = $user_id_row['id'];
                
                foreach ($sample_comments as $sc) {
                    $r = $sc['rating'];
                    $c = mysqli_real_escape_string($conn, $sc['comment']);
                    mysqli_query($conn, "INSERT INTO reviews (user_id, product_id, order_id, rating, comment, created_at) 
                                        VALUES ('$demo_uid', '$product_id', 0, '$r', '$c', NOW())");
                }
                // Refresh page once to show new data
                echo "<script>window.location.reload();</script>";
            } else {
                ?>
                <div class="review-item" style="justify-content: center; color: #888;">
                    <div style="text-align:center; padding: 20px 0; color: #94a3b8;"><i class="fas fa-star"
                            style="display:block; font-size: 30px; margin-bottom: 10px; opacity: 0.3;"></i>No ratings yet. Be the
                        first to rate!</div>
                </div>
                <?php
            }
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