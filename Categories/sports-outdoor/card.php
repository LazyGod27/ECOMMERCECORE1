<?php
$products = [
    //Product 1
    [
        'name' => 'Yoga Mat (Non-Slip)',
        'price' => '₱100.00',
        'image' => '../../image/sports-outdoor/Yoga Mat (Non-Slip).jpeg',
        'link' => 'view-product.php?id=701',
        'discount' => '-20%'
    ],

    //Product 2
    [
        'name' => 'Dumbbell Set (Adjustable)',
        'price' => '₱340.00',
        'image' => '../../image/sports-outdoor/Dumbbell Set (Adjustable).jpeg',
        'link' => 'view-product.php?id=702',
        'discount' => '-20%',
    ],

    //Product 3
    [
        'name' => 'Resistance Bands Set',
        'price' => '₱50.00',
        'image' => '../../image/sports-outdoor/Resistance Bands Set.jpeg',
        'link' => 'view-product.php?id=703',
        'discount' => '-20%',
    ],

    //Product 4
    [
        'name' => 'Jump Rope (Speed Type)',
        'price' => '₱1,500.00',
        'image' => '../../image/sports-outdoor/Jump Rope (Speed Type).jpeg',
        'link' => 'view-product.php?id=704',
        'discount' => '-20%',
    ],

    //Product 5
    [
        'name' => 'Sports Water Bottle (1L)',
        'price' => '₱2,500.00',
        'image' => '../../image/sports-outdoor/Sports Water Bottle (1L).jpeg',
        'link' => 'view-product.php?id=705',
        'discount' => '-20%',
    ],

    //Product 6
    [
        'name' => 'Running Shoes (Men-Women)',
        'price' => '₱10,200.00',
        'image' => '../../image/sports-outdoor/Running Shoes (Men-Women).jpeg',
        'link' => 'view-product.php?id=706',
        'discount' => '-20%',
    ],

    //Product 7
    [
        'name' => 'Fitness Tracker Watch',
        'price' => '₱15,000.00',
        'image' => '../../image/sports-outdoor/Fitness Tracker Watch.jpeg',
        'link' => 'view-product.php?id=707',
        'discount' => '-20%',
    ],

    //Product 8
    [
        'name' => 'Camping Tent (2–3 Person)',
        'price' => '₱20,000.00',
        'image' => '../../image/sports-outdoor/Camping Tent (2–3 Person).jpeg',
        'link' => 'view-product.php?id=708',
        'discount' => '-20%',
    ],

    //Product 9
    [
        'name' => 'Portable Folding Chair',
        'price' => '₱59,000.00',
        'image' => '../../image/sports-outdoor/Portable Folding Chair.jpeg',
        'link' => 'view-product.php?id=709',
        'discount' => '-20%',
    ],

    //Product 10
    [
        'name' => 'Outdoor Backpack (Waterproof)',
        'price' => '₱100.00',
        'image' => '../../image/sports-outdoor/Outdoor Backpack (Waterproof).jpeg',
        'link' => 'view-product.php?id=710',
        'discount' => '-20%',
    ],

    //Product 11
    [
        'name' => 'Yoga Ball (Anti-Burst)',
        'price' => '₱1,000.00',
        'image' => '../../image/sports-outdoor/Yoga Ball (Anti-Burst).jpeg',
        'link' => 'view-product.php?id=711',
        'discount' => '-20%',
    ],

    //Product 12
    [
        'name' => 'Cycling Helmet',
        'price' => '₱100.00',
        'image' => '../../image/sports-outdoor/Cycling Helmet.jpeg',
        'link' => 'view-product.php?id=712',
        'discount' => '-20%',
    ],

    //Product 13
    [
        'name' => 'Trekking Poles (Pair)',
        'price' => '₱299.00',
        'image' => '../../image/sports-outdoor/Trekking Poles (Pair).jpeg',
        'link' => 'view-product.php?id=713',
        'discount' => '-20%',
    ],

    //Product 14
    [
        'name' => 'Boxing Gloves (Training Type)',
        'price' => '₱299',
        'image' => '../../image/sports-outdoor/Boxing Gloves (Training Type).jpeg',
        'link' => 'view-product.php?id=714',
        'discount' => '-20%',
    ],

    //Product 15
    [
        'name' => 'Portable BBQ Grill',
        'price' => '₱299',
        'image' => '../../image/sports-outdoor/Portable BBQ Grill.jpeg',
        'link' => 'view-product.php?id=715',
        'discount' => '-20%',
    ]
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



