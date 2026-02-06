<?php
$products = [
    //Product 1
    [
        'name' => 'Board Game (Strategy)',
        'price' => '₱100.00',
        'image' => '../../image/Toys & Games/Board Game (Strategy).jpeg',
        'link' => 'view-product.php?id=801',
        'discount' => '-20%'
    ],

    //Product 2
    [
        'name' => 'Action Figure (Superhero)',
        'price' => '₱340.00',
        'image' => '../../image/Toys & Games/Action Figure (Superhero).jpeg',
        'link' => 'view-product.php?id=802',
        'discount' => '-20%',
    ],

    //Product 3
    [
        'name' => 'Puzzle Set (1000 Pieces)',
        'price' => '₱50.00',
        'image' => '../../image/Toys & Games/Puzzle Set (1000 Pieces).jpeg',
        'link' => 'view-product.php?id=803',
        'discount' => '-20%',
    ],

    //Product 4
    [
        'name' => 'Remote Control Car',
        'price' => '₱1,500.00',
        'image' => '../../image/Toys & Games/Remote Control Car.jpeg',
        'link' => 'view-product.php?id=804',
        'discount' => '-20%',
    ],

    //Product 5
    [
        'name' => 'Doll House (Wooden)',
        'price' => '₱2,500.00',
        'image' => '../../image/Toys & Games/Doll House (Wooden).jpeg',
        'link' => 'view-product.php?id=805',
        'discount' => '-20%',
    ],

    //Product 6
    [
        'name' => 'Building Blocks Set',
        'price' => '₱10,200.00',
        'image' => '../../image/Toys & Games/Building Blocks Set.jpeg',
        'link' => 'view-product.php?id=806',
        'discount' => '-20%',
    ],

    //Product 7
    [
        'name' => 'Plush Teddy Bear',
        'price' => '₱15,000.00',
        'image' => '../../image/Toys & Games/Plush Teddy Bear.jpeg',
        'link' => 'view-product.php?id=807',
        'discount' => '-20%',
    ],

    //Product 8
    [
        'name' => 'Educational Toy Tablet',
        'price' => '₱20,000.00',
        'image' => '../../image/Toys & Games/Educational Toy Tablet.jpeg',
        'link' => 'view-product.php?id=808',
        'discount' => '-20%',
    ],

    //Product 9
    [
        'name' => 'Drone with Camera',
        'price' => '₱59,000.00',
        'image' => '../../image/Toys & Games/Drone with Camera.jpeg',
        'link' => 'view-product.php?id=809',
        'discount' => '-20%',
    ],

    //Product 10
    [
        'name' => 'Chess Set (Magnetic)',
        'price' => '₱100.00',
        'image' => '../../image/Toys & Games/Chess Set (Magnetic).jpeg',
        'link' => 'view-product.php?id=810',
        'discount' => '-20%',
    ],

    //Product 11
    [
        'name' => 'Card Game (Family Fun)',
        'price' => '₱1,000.00',
        'image' => '../../image/Toys & Games/Card Game (Family Fun).jpeg',
        'link' => 'view-product.php?id=811',
        'discount' => '-20%',
    ],

    //Product 12
    [
        'name' => 'Toy Kitchen Set',
        'price' => '₱100.00',
        'image' => '../../image/Toys & Games/Toy Kitchen Set.jpeg',
        'link' => 'view-product.php?id=812',
        'discount' => '-20%',
    ],

    //Product 13
    [
        'name' => 'Inflatable Pool for Kids',
        'price' => '₱299.00',
        'image' => '../../image/Toys & Games/Inflatable Pool for Kids.jpeg',
        'link' => 'view-product.php?id=813',
        'discount' => '-20%',
    ],

    //Product 14
    [
        'name' => 'Musical Keyboard Toy',
        'price' => '₱299',
        'image' => '../../image/Toys & Games/Musical Keyboard Toy.jpeg',
        'link' => 'view-product.php?id=814',
        'discount' => '-20%',
    ],

    //Product 15
    [
        'name' => 'Yo-Yo (Pro Level)',
        'price' => '₱299',
        'image' => '../../image/Toys & Games/Yo-Yo (Pro Level).jpeg',
        'link' => 'view-product.php?id=815',
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



