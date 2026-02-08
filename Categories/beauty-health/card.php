<?php
$products = [
    //Product 1
    [
        'name' => 'Facial Cleanser (Gentle Formula)',
        'price' => '₱100.00',
        'image' => '../../image/beauty-health/Facial Cleanser (Gentle Formula).jpeg',
        'link' => 'view-product.php?id=601',
        'discount' => '-20%'
    ],

    //Product 2
    [
        'name' => 'Vitamin C Serum',
        'price' => '₱340.00',
        'image' => '../../image/beauty-health/Vitamin C Serum.jpeg',
        'link' => 'view-product.php?id=602',
        'discount' => '-20%'
    ],

    //Product 3
    [
        'name' => 'Aloe Vera Soothing Gel',
        'price' => '₱50.00',
        'image' => '../../image/beauty-health/Aloe Vera Soothing Gel.jpeg',
        'link' => 'view-product.php?id=603',
        'discount' => '-20%'
    ],

    //Product 4
    [
        'name' => 'Sunscreen SPF 50+',
        'price' => '₱1,500.00',
        'image' => '../../image/beauty-health/Sunscreen SPF 50+.jpeg',
        'link' => 'view-product.php?id=604',
        'discount' => '-20%'
    ],

    //Product 5
    [
        'name' => 'Moisturizing Face Cream',
        'price' => '₱2,500.00',
        'image' => '../../image/beauty-health/Moisturizing Face Cream.jpeg',
        'link' => 'view-product.php?id=605',
        'discount' => '-20%'
    ],

    //Product 6
    [
        'name' => 'Charcoal Face Mask',
        'price' => '₱10,200.00',
        'image' => '../../image/beauty-health/Charcoal Face Mask.jpeg',
        'link' => 'view-product.php?id=606',
        'discount' => '-20%'
    ],


    //Product 7
    [
        'name' => 'Hair Growth Shampoo',
        'price' => '₱15,000.00',
        'image' => '../../image/beauty-health/Hair Growth Shampoo.jpeg',
        'link' => 'view-product.php?id=607',
        'discount' => '-20%'
    ],

    //Product 8
    [
        'name' => 'Hair Conditioner (Repair Care)',
        'price' => '₱15,000.00',
        'image' => '../../image/beauty-health/Hair Conditioner (Repair Care).jpeg',
        'link' => 'view-product.php?id=608',
        'discount' => '-20%'
    ],

    //Product 9
    [
        'name' => 'Makeup Brush Set (10pcs)',
        'price' => '₱20,000.00',
        'image' => '../../image/beauty-health/Makeup Brush Set (10pcs).jpeg',
        'link' => 'view-product.php?id=609',
        'discount' => '-20%'
    ],

    //Product 10
    [
        'name' => 'Lip Tint - Lip Gloss Set',
        'price' => '₱59,000.00',
        'image' => '../../image/beauty-health/Lip Tint - Lip Gloss Set.jpeg',
        'link' => 'view-product.php?id=610',
        'discount' => '-20%'
    ],

    //Product 11
    [
        'name' => 'Body Scrub (Whitening & Exfoliating)',
        'price' => '₱100.00',
        'image' => '../../image/beauty-health/Body Scrub (Whitening & Exfoliating).jpeg',
        'link' => 'view-product.php?id=611',
        'discount' => '-20%'
    ],

    //Product 12
    [
        'name' => 'Electric Facial Cleanser Brush',
        'price' => '₱1,000.00',
        'image' => '../../image/beauty-health/Electric Facial Cleanser Brush.jpeg',
        'link' => 'view-product.php?id=612',
        'discount' => '-20%'
    ],

    //Product 13
    [
        'name' => 'Nail Care Kit (Manicure Set)',
        'price' => '₱100.00',
        'image' => '../../image/beauty-health/Nail Care Kit (Manicure Set).jpeg',
        'link' => 'view-product.php?id=613',
        'discount' => '-20%'
    ],

    //Product 14
    [
        'name' => 'Essential Oil Set (Relaxing Blend)',
        'price' => '₱299.00',
        'image' => '../../image/beauty-health/Essential Oil Set (Relaxing Blend).jpeg',
        'link' => 'view-product.php?id=614',
        'discount' => '-20%'
    ],

    //Product 15
    [
        'name' => 'Foot Spa Massager Roller',
        'price' => '₱299',
        'image' => '../../image/beauty-health/Foot Spa Massager Roller.jpeg',
        'link' => 'view-product.php?id=615',
        'discount' => '-20%'
    ],


];
?>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card" onclick="window.location.href='<?php echo $product['link']; ?>'">
            <div class="image-wrapper">
                <?php if (isset($product['discount'])): ?>
                    <span class="discount-badge"><?php echo $product['discount']; ?></span>
                <?php endif; ?>
                <img src="<?php echo str_replace(' ', '%20', $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
            </div>
            <div class="product-details">
                <h3 class="product-title"
                    >
                    <?php echo htmlspecialchars($product['name']); ?>
                </h3>
            </div>
            <div class="card-action-area">
                <div class="product-price"><?php echo $product['price']; ?></div>
                <a href="#" class="add-to-cart-btn">Find Similar</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>



