<?php
// Include database connection
if (!isset($conn)) {
    include '../../Database/config.php';
}

// Include the best sellers query function
include 'get_best_sellers.php';

// Fetch best-selling products from database
$products = getBestSellingProducts($conn, 15);
?>


<div class="product-grid" id="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card" 
            data-name="<?php echo htmlspecialchars($product['name']); ?>"
            data-image="<?php echo $product['image']; ?>"
            data-variants='<?php echo htmlspecialchars(json_encode($product['variants'] ?? [])); ?>'
            onclick="window.location.href='<?php echo $product['link']; ?>'">
            <div class="image-wrapper">
                <?php if (isset($product['discount'])): ?>
                    <span class="discount-badge"><?php echo $product['discount']; ?></span>
                <?php endif; ?>
                <img src="<?php echo str_replace(' ', '%20', $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                <div class="variant-swatches" aria-hidden="true" onclick="event.stopPropagation();"></div>
            </div>
            <div class="product-details">
                <h3 class="product-title">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h3>
                <?php if (isset($product['description'])): ?>
                    <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                <?php endif; ?>
                <?php if (isset($product['reviews']) && !empty($product['reviews'])): ?>
                    <div class="product-rating">
                        <div class="stars">
                            <?php 
                                $avgRating = array_sum(array_map(function($r) { return $r['rating']; }, $product['reviews'])) / count($product['reviews']);
                                $fullStars = floor($avgRating);
                                for ($i = 0; $i < 5; $i++) {
                                    echo $i < $fullStars ? '⭐' : '☆';
                                }
                            ?>
                        </div>
                        <span class="review-count">(<?php echo count($product['reviews']); ?> reviews)</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-action-area">
                <div class="product-price"><?php echo $product['price']; ?></div>
                <a href="#" class="add-to-cart-btn" onclick="event.stopPropagation(); return false;">Find Similar</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

