<?php
/**
 * Product Reviews Section Component
 * 
 * Displays customer reviews with ratings, comments, and reviewer information
 * Usage: include this file and call displayProductReviews($product_reviews)
 * 
 * @param array $reviews - Array of review objects with: reviewer_name, rating, comment, date
 */

function displayProductReviews($reviews = []) {
    if (empty($reviews)) {
        echo '<div class="reviews-section"><p>No reviews yet. Be the first to review!</p></div>';
        return;
    }
    
    // Calculate average rating
    $avgRating = array_reduce($reviews, function($sum, $review) {
        return $sum + $review['rating'];
    }, 0) / count($reviews);
    
    ?>
    <div class="reviews-section">
        <div class="reviews-header">
            <h3>Customer Reviews</h3>
            <div class="average-rating">
                <div class="rating-display">
                    <?php
                    $fullStars = floor($avgRating);
                    $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                    
                    for ($i = 0; $i < 5; $i++) {
                        if ($i < $fullStars) {
                            echo '⭐';
                        } elseif ($i === $fullStars && $hasHalfStar) {
                            echo '⭐';
                        } else {
                            echo '☆';
                        }
                    }
                    ?>
                </div>
                <span class="avg-rating-text"><?php echo round($avgRating, 1); ?> out of 5</span>
                <span class="review-count-text">Based on <?php echo count($reviews); ?> reviews</span>
            </div>
        </div>

        <div class="reviews-list">
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <h4 class="reviewer-name"><?php echo htmlspecialchars($review['reviewer_name']); ?></h4>
                            <span class="review-date"><?php echo htmlspecialchars($review['date']); ?></span>
                        </div>
                        <div class="review-rating">
                            <?php
                            $rating = $review['rating'];
                            for ($i = 0; $i < 5; $i++) {
                                echo ($i < $rating) ? '⭐' : '☆';
                            }
                            ?>
                        </div>
                    </div>
                    <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <div class="review-actions">
                        <button class="helpful-btn" onclick="markHelpful(event)">👍 Helpful</button>
                        <button class="report-btn" onclick="reportReview(event)">⚠️ Report</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="reviews-cta">
            <p>Have you purchased this product?</p>
            <button class="btn-write-review" onclick="openWriteReviewModal()">Write a Review</button>
        </div>
    </div>

    <style>
        .reviews-section {
            margin: 40px 0;
            padding: 24px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid #e2e8f0;
        }

        .reviews-header h3 {
            font-size: 1.5rem;
            color: #1e293b;
            margin: 0;
        }

        .average-rating {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .rating-display {
            font-size: 1.5rem;
            letter-spacing: 3px;
        }

        .avg-rating-text {
            font-weight: 600;
            color: #2a3b7e;
            font-size: 1rem;
        }

        .review-count-text {
            color: #64748b;
            font-size: 0.85rem;
        }

        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-bottom: 32px;
        }

        .review-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: box-shadow 0.3s ease;
        }

        .review-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .reviewer-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .reviewer-name {
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            font-size: 0.95rem;
        }

        .review-date {
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .review-rating {
            font-size: 0.95rem;
            letter-spacing: 2px;
        }

        .review-comment {
            color: #475569;
            line-height: 1.6;
            margin: 12px 0;
            font-size: 0.95rem;
        }

        .review-actions {
            display: flex;
            gap: 16px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .helpful-btn,
        .report-btn {
            background: none;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            color: #475569;
            transition: all 0.2s ease;
        }

        .helpful-btn:hover {
            background: #f0f9ff;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .report-btn:hover {
            background: #fef2f2;
            border-color: #ef4444;
            color: #ef4444;
        }

        .reviews-cta {
            text-align: center;
            padding: 24px;
            background: white;
            border-radius: 8px;
            border: 2px dashed #cbd5e1;
        }

        .reviews-cta p {
            color: #64748b;
            margin-bottom: 12px;
        }

        .btn-write-review {
            background: #2a3b7e;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-write-review:hover {
            background: #1e2d5c;
        }

        @media (max-width: 768px) {
            .reviews-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .review-header {
                flex-direction: column;
                gap: 8px;
            }

            .reviews-cta {
                padding: 16px;
            }
        }
    </style>

    <script>
        function markHelpful(e) {
            e.preventDefault();
            const btn = e.target;
            btn.style.background = '#f0f9ff';
            btn.style.color = '#3b82f6';
            btn.textContent = '👍 Marked as helpful';
            btn.disabled = true;
        }

        function reportReview(e) {
            e.preventDefault();
            alert('Thank you for reporting this review. Our team will review it shortly.');
        }

        function openWriteReviewModal() {
            alert('Write your review dialog would appear here. This is a placeholder for the review submission form.');
            // TODO: Implement write review modal
        }
    </script>

    <?php
}

/**
 * Display a single review item (used in modals or standalone)
 */
function displaySingleReview($review) {
    ?>
    <div class="review-card">
        <div class="review-header-mini">
            <strong><?php echo htmlspecialchars($review['reviewer_name']); ?></strong>
            <span class="review-stars">
                <?php
                for ($i = 0; $i < 5; $i++) {
                    echo ($i < $review['rating']) ? '⭐' : '☆';
                }
                ?>
            </span>
        </div>
        <p><?php echo htmlspecialchars($review['comment']); ?></p>
        <small><?php echo htmlspecialchars($review['date']); ?></small>
    </div>
    <?php
}

?>
