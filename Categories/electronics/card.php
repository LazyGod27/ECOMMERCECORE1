<?php
$products = [
    //Product 1
    [
        'name' => 'Wireless Bluetooth Earbuds',
        'price' => '₱100.00',
        'image' => '../../image/electronics/Wireless Bluetooth Earbuds.jpeg',
        'link' => 'view-product.php?id=301',
        'discount' => '-20%'
    ],

    //Product 2
    [
        'name' => 'Smart Watch (Fitness Tracker)',
        'price' => '₱340.00',
        'image' => '../../image/electronics/Smart Watch (Fitness Tracker).jpeg',
        'link' => 'view-product.php?id=302',
        'discount' => '-20%'
    ],

    //Product 3
    [
        'name' => 'Portable Power Bank 20,000mAh',
        'price' => '₱50.00',
        'image' => '../../image/electronics/Portable Power Bank 20,000mAh.jpeg',
        'link' => 'view-product.php?id=303',
        'discount' => '-20%'
    ],

    //Product 4
    [
        'name' => 'Bluetooth Speaker (Waterproof)',
        'price' => '₱1,500.00',
        'image' => '../../image/electronics/Bluetooth Speaker (Waterproof).jpeg',
        'link' => 'view-product.php?id=304',
        'discount' => '-20%'
    ],

    //Product 5
    [
        'name' => 'USB-C Fast Charging Cable',
        'price' => '₱2,500.00',
        'image' => '../../image/electronics/USB-C Fast Charging Cable.jpeg',
        'link' => 'view-product.php?id=305',
        'discount' => '-20%'
    ],

    //Product 6
    [
        'name' => 'Wireless Charging Pad',
        'price' => '₱10,200.00',
        'image' => '../../image/electronics/Wireless Charging Pad.jpeg',
        'link' => 'view-product.php?id=306',
        'discount' => '-20%'
    ],


    //Product 7
    [
        'name' => 'Noise Cancelling Headphones',
        'price' => '₱15,000.00',
        'image' => '../../image/electronics/Noise Cancelling Headphones.jpeg',
        'link' => 'view-product.php?id=307',
        'discount' => '-20%'
    ],

    //Product 8
    [
        'name' => 'Mini WiFi Router / Pocket WiFi',
        'price' => '₱15,000.00',
        'image' => '../../image/electronics/Mini WiFi Router Pocket WiFi.jpeg',
        'link' => 'view-product.php?id=308',
        'discount' => '-20%'
    ],

    //Product 9
    [
        'name' => 'Smart LED Light Bulb (WiFi Controlled)',
        'price' => '₱20,000.00',
        'image' => '../../image/electronics/Smart LED Light Bulb (WiFi Controlled).jpeg',
        'link' => 'view-product.php?id=309',
        'discount' => '-20%'
    ],

    //Product 10
    [
        'name' => 'Laptop Cooling Pad (RGB Fan)',
        'price' => '₱59,000.00',
        'image' => '../../image/electronics/Laptop Cooling Pad (RGB Fan).jpeg',
        'link' => 'view-product.php?id=310',
        'discount' => '-20%'
    ],

    //Product 11
    [
        'name' => '1080p HD Web Camera',
        'price' => '₱100.00',
        'image' => '../../image/electronics/1080p HD Web Camera.jpeg',
        'link' => 'view-product.php?id=311',
        'discount' => '-20%'
    ],

    //Product 12
    [
        'name' => 'Smart Plug (App Controlled)',
        'price' => '₱1,000.00',
        'image' => '../../image/electronics/Smart Plug (App Controlled).jpeg',
        'link' => 'view-product.php?id=312',
        'discount' => '-20%'
    ],

    //Product 13
    [
        'name' => 'Portable SSD 500GB',
        'price' => '₱100.00',
        'image' => '../../image/electronics/Portable SSD 500GB.jpeg',
        'link' => 'view-product.php?id=313',
        'discount' => '-20%'
    ],

    //Product 14
    [
        'name' => 'Digital Alarm Clock with LED Display',
        'price' => '₱299.00',
        'image' => '../../image/electronics/Digital Alarm Clock with LED Display.jpeg',
        'link' => 'view-product.php?id=314',
        'discount' => '-20%'
    ],

    //Product 15
    [
        'name' => 'Car Phone Holder (Magnetic)',
        'price' => '₱299',
        'image' => '../../image/electronics/Car Phone Holder (Magnetic).jpeg',
        'link' => 'view-product.php?id=315',
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



