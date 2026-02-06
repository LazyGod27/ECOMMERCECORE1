<?php
$products = [
    //Product 1
    [
        'name' => 'bag Sholder Men',
        'price' => '₱100.00',
        'image' => '../../image/Best-seller/bag-men.jpeg',
        'link' => 'view-product.php?id=101',
        'discount' => '-20%'
    ],

    //Product 2
    [
        'name' => 'bag women',
        'price' => '₱340.00',
        'image' => '../../image/Best-seller/bag-women.jpeg',
        'link' => 'view-product.php?id=102',
        'discount' => '-20%',
    ],

    //Product 3
    [
        'name' => 'Notebook',
        'price' => '₱50.00',
        'image' => '../../image/Best-seller/Notebooks.jpeg',
        'link' => 'view-product.php?id=103',
        'discount' => '-20%',
    ],

    //Product 4
    [
        'name' => 'Earphone Bluetooth',
        'price' => '₱1,500.00',
        'image' => '../../image/Best-seller/Earphone-bluetooth.jpeg',
        'link' => 'view-product.php?id=104',
        'discount' => '-20%',
    ],

    //Product 5
    [
        'name' => 'Snikers Shoes',
        'price' => '₱2,500.00',
        'image' => '../../image/Best-seller/snikers%20shoes.avif',
        'link' => 'view-product.php?id=105',
        'discount' => '-20%',
    ],

    //Product 6
    [
        'name' => 'swatch watch',
        'price' => '₱10,200.00',
        'image' => '../../image/Best-seller/Snart%20watch.jpeg',
        'link' => 'view-product.php?id=106',
        'discount' => '-20%',
    ],

    //Product 7
    [
        'name' => 'Brand New SEALED HP Laptop i3',
        'price' => '₱15,000.00',
        'image' => '../../image/Best-seller/laptop.jpeg',
        'link' => 'view-product.php?id=107',
        'discount' => '-20%',
    ],

    //Product 8
    [
        'name' => 'Desktop Computers & 2-in-1 PCs | Dell Philippines',
        'price' => '₱20,000.00',
        'image' => '../../image/Best-seller/pc%20computer.avif',
        'link' => 'view-product.php?id=108',
        'discount' => '-20%',
    ],

    //Product 9
    [
        'name' => 'vivo pro max',
        'price' => '₱59,000.00',
        'image' => '../../image/Best-seller/vivo%20pro%20max.jpeg',
        'link' => 'view-product.php?id=109',
        'discount' => '-20%',
    ],

    //Product 10
    [
        'name' => 'iphone 15 pro max na may kagat',
        'price' => '₱100.00',
        'image' => '../../image/Best-seller/iphone.jpeg',
        'link' => 'view-product.php?id=110',
        'discount' => '-20%',
    ],

    //Product 11
    [
        'name' => 'Keyboard mechanical',
        'price' => '₱1,000.00',
        'image' => '../../image/Best-seller/Keyboard-maagas.jpeg',
        'link' => 'view-product.php?id=111',
        'discount' => '-20%',
    ],

    //Product 12
    [
        'name' => 'Ben10 brief',
        'price' => '₱100.00',
        'image' => '../../image/Best-seller/brief.jpg',
        'link' => 'view-product.php?id=112',
        'discount' => '-20%',
    ],

    //Product 13
    [
        'name' => 'USB C Fast Charging Cable (2-Pack)',
        'price' => '₱299.00',
        'image' => '../../image/Best-seller/USB%20C%20Fast%20Charging%20Cable%20(2-Pack).jpeg',
        'link' => 'view-product.php?id=113',
        'discount' => '-20%',
    ],

    //Product 14
    [
        'name' => 'Mini Bluetooth Speaker',
        'price' => '₱299',
        'image' => '../../image/Best-seller/Mini%20Bluetooth%20Speaker.jpeg',
        'link' => 'view-product.php?id=114',
        'discount' => '-20%',
    ],

    //Product 15
    [
        'name' => 'Phone Ring Holder',
        'price' => '₱299',
        'image' => '../../image/Best-seller/Phone%20Ring%20Holder.jpeg',
        'link' => 'view-product.php?id=115',
        'discount' => '-20%',
    ]
];
?>

<div class="product-grid" id="product-grid">
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

