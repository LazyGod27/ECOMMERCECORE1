<?php
$products = [
    //Product 1
    [
        'name' => 'Decorative Throw Pillow Set',
        'price' => '₱100.00',
        'image' => '../../image/Home & living/Decorative Throw Pillow Set.jpeg',
        'link' => 'view-product.php?id=501',
        'discount' => '-20%'
    ],

    //Product 2
    [
        'name' => 'Aromatherapy Scented Candles',
        'price' => '₱340.00',
        'image' => '../../image/Home & living/Aromatherapy Scented Candles.jpeg',
        'link' => 'view-product.php?id=502',
        'discount' => '-20%',
    ],

    //Product 3
    [
        'name' => 'Non-Slip Floor Mat - Rug',
        'price' => '₱50.00',
        'image' => '../../image/Home & living/Non-Slip Floor Mat - Rug.jpeg',
        'link' => 'view-product.php?id=503',
        'discount' => '-20%',
    ],

    //Product 4
    [
        'name' => 'Wall Art Canvas Frame',
        'price' => '₱1,500.00',
        'image' => '../../image/Home & living/Wall Art Canvas Frame.jpeg',
        'link' => 'view-product.php?id=504',
        'discount' => '-20%',
    ],

    //Product 5
    [
        'name' => 'LED Table Lamp (Touch Control)',
        'price' => '₱2,500.00',
        'image' => '../../image/Home & living/LED Table Lamp (Touch Control).jpeg',
        'link' => 'view-product.php?id=505',
        'discount' => '-20%',
    ],

    //Product 6
    [
        'name' => 'Storage Basket Organizer',
        'price' => '₱10,200.00',
        'image' => '../../image/Home & living/Storage Basket Organizer.jpeg',
        'link' => 'view-product.php?id=506',
        'discount' => '-20%',
    ],

    //Product 7
    [
        'name' => 'Ceramic Vase (Modern Design)',
        'price' => '₱15,000.00',
        'image' => '../../image/Home & living/Ceramic Vase (Modern Design).jpeg',
        'link' => 'view-product.php?id=507',
        'discount' => '-20%',
    ],

    //Product 8
    [
        'name' => 'Kitchen Spice Rack Organizer',
        'price' => '₱20,000.00',
        'image' => '../../image/Home & living/Kitchen Spice Rack Organizer.jpeg',
        'link' => 'view-product.php?id=508',
        'discount' => '-20%',
    ],

    //Product 9
    [
        'name' => 'Foldable Laundry Basket',
        'price' => '₱59,000.00',
        'image' => '../../image/Home & living/Foldable Laundry Basket.jpeg',
        'link' => 'view-product.php?id=509',
        'discount' => '-20%',
    ],

    //Product 10
    [
        'name' => 'Electric Kettle (1.7L)',
        'price' => '₱100.00',
        'image' => '../../image/Home & living/Electric Kettle (1.7L).jpeg',
        'link' => 'view-product.php?id=510',
        'discount' => '-20%',
    ],

    //Product 11
    [
        'name' => 'Bedside Alarm Clock (Digital)',
        'price' => '₱1,000.00',
        'image' => '../../image/Home & living/Bedside Alarm Clock (Digital).jpeg',
        'link' => 'view-product.php?id=511',
        'discount' => '-20%',
    ],

    //Product 12
    [
        'name' => 'Multi-Purpose Storage Box',
        'price' => '₱100.00',
        'image' => '../../image/Home & living/Multi-Purpose Storage Box.jpeg',
        'link' => 'view-product.php?id=512',
        'discount' => '-20%',
    ],

    //Product 13
    [
        'name' => 'Wall Mounted Floating Shelf',
        'price' => '₱299.00',
        'image' => '../../image/Home & living/Wall Mounted Floating Shelf.jpeg',
        'link' => 'view-product.php?id=513',
        'discount' => '-20%',
    ],

    //Product 14
    [
        'name' => 'Bathroom Shower Caddy Organizer',
        'price' => '₱299',
        'image' => '../../image/Home & living/Bathroom Shower Caddy Organizer.jpeg',
        'link' => 'view-product.php?id=514',
        'discount' => '-20%',
    ],

    //Product 15
    [
        'name' => 'Tabletop Indoor Plant (Artificial)',
        'price' => '₱299',
        'image' => '../../image/Home & living/Tabletop Indoor Plant (Artificial).jpeg',
        'link' => 'view-product.php?id=515',
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



