<!-- Enhanced Reviews Display Section with Professional Styling -->
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
        background: linear-gradient(135deg, #2A3B7E 0%, #1e2b5e 100%);
        color: #fff !important;
        padding: 12px 28px;
        border-radius: 8px;
        text-decoration: none !important;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        margin-top: 15px;
        box-shadow: 0 2px 8px rgba(42, 59, 126, 0.2);
    }

    .btn-rate-now:hover {
        background: linear-gradient(135deg, #1e2b5e 0%, #151e3f 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(42, 59, 126, 0.3);
    }

    /* Sentiment Badge Styles */
    .sentiment-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .sentiment-positive {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #059669;
        border: 1px solid #10b981;
    }

    .sentiment-negative {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        border: 1px solid #ef4444;
    }

    .sentiment-neutral {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .sentiment-icon {
        font-size: 12px;
    }

    /* Enhanced Review Item */
    .review-item {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 25px;
        border: 1px solid #f0f4f8;
        display: flex;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .review-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #dbeafe;
    }

    .review-avatar {
        width: 50px;
        height: 50px;
        border-radius: 12px;
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

    .review-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .review-author {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        background: #f0fdf4;
        color: #166534;
        padding: 3px 10px;
        border-radius: 50px;
        font-weight: 700;
        border: 1px solid #dcfce7;
    }

    .recommendation-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
    }

    .recommendation-yes {
        background: #ecfdf5;
        color: #10b981;
        border: 1px solid #10b981;
    }

    .recommendation-no {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .review-stars {
        color: #fbbf24;
        font-size: 12px;
        letter-spacing: 2px;
        display: inline-flex;
        gap: 2px;
    }

    .review-meta {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
        margin: 8px 0;
    }

    .review-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #1e293b;
        margin: 12px 0 10px 0;
        word-break: break-word;
    }

    .review-content {
        line-height: 1.8;
        color: #475569;
        font-size: 0.95rem;
        word-break: break-word;
        white-space: pre-wrap;
        margin: 15px 0;
    }

    /* Media Gallery */
    .review-media-gallery {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }

    .review-media-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #f5f5f5;
        aspect-ratio: 1;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .review-media-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .review-media-item img,
    .review-media-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .media-type-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
    }

    .video-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 30px;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    /* Criteria Ratings Display */
    .review-criteria {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
    }

    .criteria-rating {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        text-align: center;
    }

    .criteria-label {
        font-size: 11px;
        font-weight: 700;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }

    .criteria-stars {
        font-size: 13px;
        color: #fbbf24;
        letter-spacing: 1px;
    }

    /* Reviews Container */
    .reviews-container {
        width: 100%;
    }

    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 0 10px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .reviews-header-title {
        font-weight: 900;
        font-size: 1.5rem;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .ai-badge {
        font-size: 11px;
        font-weight: 800;
        background: #f0f9ff;
        color: #0369a1;
        padding: 8px 16px;
        border-radius: 50px;
        border: 1px solid #bae6fd;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 4px rgba(3, 105, 161, 0.1);
    }

    .ai-badge i {
        color: #0ea5e9;
    }

    .rate-btn-container {
        margin-top: 35px;
        text-align: center;
    }

    .close-modal {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #999;
    }

    .close-modal:hover {
        color: #333;
    }

    /* Empty State */
    .no-reviews {
        text-align: center;
        padding: 40px;
        color: #998;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .review-item {
            flex-direction: column;
            gap: 15px;
        }

        .review-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .reviews-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .review-media-gallery {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }

        .review-criteria {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="reviews-container">
    <div class="reviews-header">
        <span class="reviews-header-title">Customer Reviews</span>
        <span class="ai-badge">
            <i class="fas fa-magic"></i> AI INSIGHTS ACTIVE
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
    $can_rate = false;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    $product_name_check = isset($name) ? $name : "";

    if ($user_id > 0) {
        $product_name_esc = mysqli_real_escape_string($conn, $product_name_check);
        $p_id_check = intval($product_id);
        $order_query = "SELECT id FROM orders WHERE user_id = '$user_id' AND (product_id = '$p_id_check' OR product_name = '$product_name_esc') LIMIT 1";
        $order_result = mysqli_query($conn, $order_query);
        if ($order_result && mysqli_num_rows($order_result) > 0) {
            $can_rate = true;
            $order_row = mysqli_fetch_assoc($order_result);
            $existing_order_id = $order_row['id'];
        }
    }

    $product_id = isset($product_id) ? $product_id : 1;
    if (isset($conn)) {
        $sql = "SELECT r.*, u.fullname AS user_name 
                FROM reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = '$product_id' 
                ORDER BY r.created_at DESC LIMIT 20";

        $result = mysqli_query($conn, $sql);

        if ($result === false) {
            echo "<div class='no-reviews'>Error fetching reviews</div>";
        } elseif ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $display_name = !empty($row['user_name']) ? $row['user_name'] : "User";
                $is_sample = (isset($row['order_id']) && $row['order_id'] == 0);
                
                if ($is_sample) {
                    $random_names = ['Maria Clara', 'Juan Dela Cruz', 'Sophia Reyes', 'Kevin Lee', 'Elena Gilbert', 'James Bondoc', 'Rico Blanco', 'Sarah Chen', 'David Kim', 'Jessica Lim'];
                    $name_index = $row['id'] % count($random_names);
                    $display_name = $random_names[$name_index];
                }

                $user_initial = strtoupper(substr($display_name, 0, 1));
                $review_id = $row['id'];
                $avatar_colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'];
                $color_idx = $review_id % count($avatar_colors);
                $bg_color = $avatar_colors[$color_idx];
                
                // Parse media URLs (handle both JSON array and single file for backward compatibility)
                $media_files = [];
                if (!empty($row['media_url'])) {
                    if (strpos($row['media_url'], '[') === 0) {
                        // JSON array
                        $media_files = json_decode($row['media_url'], true) ?: [];
                    } else {
                        // Single file (backward compatibility)
                        $media_files = [$row['media_url']];
                    }
                }

                // Get review title if available
                $review_title = isset($row['review_title']) && !empty($row['review_title']) ? $row['review_title'] : '';
                $recommend = isset($row['recommended']) ? $row['recommended'] : null;
                ?>
                <div class="review-item">
                    <div class="review-avatar" style="background-color: <?php echo $bg_color; ?>;">
                        <?php echo $user_initial; ?>
                    </div>
                    <div class="review-details">
                        <!-- Header with author and badges -->
                        <div class="review-header">
                            <div style="flex: 1;">
                                <div class="review-author">
                                    <?php echo htmlspecialchars($display_name); ?>
                                    <span class="verified-badge">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                </div>
                            </div>
                            <?php if ($recommend !== null): ?>
                                <span class="recommendation-badge <?php echo $recommend ? 'recommendation-yes' : 'recommendation-no'; ?>">
                                    <i class="fas fa-<?php echo $recommend ? 'thumbs-up' : 'thumbs-down'; ?>"></i>
                                    <?php echo $recommend ? 'Recommended' : 'Not Recommended'; ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Star Rating -->
                        <div class="review-stars">
                            <?php
                            $stars = isset($row['rating']) ? intval($row['rating']) : 5;
                            for ($i = 0; $i < $stars; $i++) {
                                echo '<i class="fas fa-star"></i>';
                            }
                            for ($i = $stars; $i < 5; $i++) {
                                echo '<i class="far fa-star" style="color: #ddd;"></i>';
                            }
                            ?>
                        </div>

                        <!-- Meta Info -->
                        <div class="review-meta">
                            <?php echo date("F j, Y", strtotime($row['created_at'])); ?>
                        </div>

                        <!-- AI Sentiment Badge -->
                        <div id="sentiment-container-<?php echo $review_id; ?>" style="min-height: 24px; margin: 10px 0;">
                            <?php 
                            $analysis = analyzeSentimentAI($row['comment']);
                            $sentiment = $analysis['result']['sentiment'];
                            $badgeClass = 'sentiment-neutral';
                            $icon = '<i class="fas fa-minus"></i>';

                            if ($sentiment === 'Positive') {
                                $badgeClass = 'sentiment-positive';
                                $icon = '<i class="fas fa-thumbs-up"></i>';
                            } elseif ($sentiment === 'Negative') {
                                $badgeClass = 'sentiment-negative';
                                $icon = '<i class="fas fa-thumbs-down"></i>';
                            }
                            ?>
                            <div class="sentiment-badge <?php echo $badgeClass; ?>">
                                <span class="sentiment-icon"><?php echo $icon; ?></span>
                                <?php echo $sentiment; ?>
                            </div>
                        </div>

                        <!-- Review Title -->
                        <?php if (!empty($review_title)): ?>
                            <div class="review-title"><?php echo htmlspecialchars($review_title); ?></div>
                        <?php endif; ?>

                        <!-- Review Content -->
                        <div class="review-content" id="review-text-<?php echo $review_id; ?>">
                            <?php echo nl2br(htmlspecialchars($row['comment'])); ?>
                        </div>

                        <!-- Media Gallery -->
                        <?php if (!empty($media_files) && count($media_files) > 0): ?>
                            <div class="review-media-gallery">
                                <?php foreach ($media_files as $media_file): ?>
                                    <?php
                                    $img_path = '../../' . $media_file;
                                    $ext = strtolower(pathinfo($img_path, PATHINFO_EXTENSION));
                                    $video_exts = ['mp4', 'mov', 'avi', 'webm', 'mkv'];
                                    $is_video = in_array($ext, $video_exts);
                                    ?>
                                    <div class="review-media-item" 
                                         onclick="window.open('<?php echo htmlspecialchars($img_path); ?>', '_blank')"
                                         title="Click to view full size">
                                        <?php if ($is_video): ?>
                                            <video src="<?php echo htmlspecialchars($img_path); ?>" 
                                                   style="width:100%; height:100%; object-fit:cover;"></video>
                                            <span class="media-type-badge">VIDEO</span>
                                            <div class="video-icon">
                                                <i class="fas fa-play-circle"></i>
                                            </div>
                                        <?php else: ?>
                                            <img src="<?php echo htmlspecialchars($img_path); ?>" 
                                                 alt="review image"
                                                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22><rect fill=%22%23ddd%22 width=%22150%22 height=%22150%22/></svg>'">
                                            <span class="media-type-badge">PHOTO</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="no-reviews"><i class="fas fa-star" style="font-size: 24px; color: #ddd; margin-bottom: 10px; display: block;"></i>No reviews yet. Be the first to share your experience!</div>';
        }
    } else {
        echo '<div class="no-reviews">Database connection error.</div>';
    }
    ?>

    <!-- Rate Button -->
    <div class="rate-btn-container">
        <a href="#" class="btn-rate-now" onclick="handleRateClick(event)">
            <i class="fas fa-star"></i> Write a Review <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</div>

<!-- Buy First Modal -->
<div id="buyFirstModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div class="modal-icon"><i class="fas fa-shopping-cart"></i></div>
        <h3>Purchase Required</h3>
        <p>You need to buy this product first before you can leave a review.</p>
        <button class="modal-btn" onclick="closeModal()">OK</button>
    </div>
</div>

<script>
    function handleRateClick(e) {
        e.preventDefault();
        const canRate = <?php echo $can_rate ? 'true' : 'false'; ?>;
        const productId = <?php echo $product_id; ?>;
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

    window.onclick = function (event) {
        const modal = document.getElementById("buyFirstModal");
        if (event.target == modal) {
            modal.classList.remove("show");
        }
    }
</script>
