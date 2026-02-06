<?php
$products = [
    //Product 1
    [
        'name' => 'Organic Rolled Oats (1kg)',
        'price' => '₱250.00',
        'image' => '../../image/groceries/Organic Rolled Oats (1kg).jpeg',
        'link' => 'view-product.php?id=901',
        'discount' => '-17%'
    ],

    //Product 2
    [
        'name' => 'Unsweetened Almond Milk (1L)',
        'price' => '₱180.00',
        'image' => '../../image/groceries/Unsweetened Almond Milk (1L).jpeg',
        'link' => 'view-product.php?id=902',
        'discount' => '-18%',
    ],

    //Product 3
    [
        'name' => 'Extra Virgin Olive Oil (500ml)',
        'price' => '₱450.00',
        'image' => '../../image/groceries/Extra Virgin Olive Oil (500ml).jpeg',
        'link' => 'view-product.php?id=903',
        'discount' => '-18%',
    ],

    //Product 4
    [
        'name' => 'Premium Brown Rice (2kg)',
        'price' => '₱320.00',
        'image' => '../../image/groceries/Premium Brown Rice (2kg).jpeg',
        'link' => 'view-product.php?id=904',
        'discount' => '-20%',
    ],

    //Product 5
    [
        'name' => 'Japanese Green Tea Bags (50pcs)',
        'price' => '₱280.00',
        'image' => '../../image/groceries/Japanese Green Tea Bags (50pcs).jpeg',
        'link' => 'view-product.php?id=905',
        'discount' => '-20%',
    ],

    //Product 6
    [
        'name' => 'Jasmine Rice',
        'price' => '₱150.00',
        'image' => '../../image/groceries/Jasmine Rice.jpeg',
        'link' => 'view-product.php?id=906',
        'discount' => '-16%',
    ],

    //Product 7
    [
        'name' => 'Pure Raw Honey (500g)',
        'price' => '₱380.00',
        'image' => '../../image/groceries/Pure Raw Honey (500g).jpeg',
        'link' => 'view-product.php?id=907',
        'discount' => '-15%',
    ],

    //Product 8
    [
        'name' => 'Creamy Peanut Butter (No Added Sugar)',
        'price' => '₱220.00',
        'image' => '../../image/groceries/Creamy Peanut Butter (No Added Sugar).jpeg',
        'link' => 'view-product.php?id=908',
        'discount' => '-21%',
    ],

    //Product 9
    [
        'name' => 'Organic Chia Seeds (250g)',
        'price' => '₱180.00',
        'image' => '../../image/groceries/Organic Chia Seeds (250g).jpeg',
        'link' => 'view-product.php?id=909',
        'discount' => '-28%',
    ],

    //Product 10
    [
        'name' => 'White Quinoa (500g)',
        'price' => '₱350.00',
        'image' => '../../image/groceries/White Quinoa (500g).jpeg',
        'link' => 'view-product.php?id=910',
        'discount' => '-22%',
    ],

    //Product 11
    [
        'name' => 'Pure Coconut Water (1L)',
        'price' => '₱120.00',
        'image' => '../../image/groceries/Pure Coconut Water (1L).jpeg',
        'link' => 'view-product.php?id=911',
        'discount' => '-20%',
    ],

    //Product 12
    [
        'name' => 'Crunchy Granola (Honey Almond)',
        'price' => '₱280.00',
        'image' => '../../image/groceries/Crunchy Granola (Honey Almond).jpeg',
        'link' => 'view-product.php?id=912',
        'discount' => '-20%',
    ],

    //Product 13
    [
        'name' => 'High Protein Energy Bar (Box of 12)',
        'price' => '₱600.00',
        'image' => '../../image/groceries/High Protein Energy Bar (Box of 12).jpeg',
        'link' => 'view-product.php?id=913',
        'discount' => '-25%',
    ],

    //Product 14
    [
        'name' => 'Apple Cider Vinegar with Mother',
        'price' => '₱250.00',
        'image' => '../../image/groceries/Apple Cider Vinegar with Mother.jpeg',
        'link' => 'view-product.php?id=914',
        'discount' => '-17%',
    ],

    //Product 15
    [
        'name' => 'Mixed Nuts (Roasted & Salted)',
        'price' => '₱320.00',
        'image' => '../../image/groceries/Mixed Nuts (Roasted & Salted).jpeg',
        'link' => 'view-product.php?id=915',
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



