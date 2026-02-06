<?php
$products = [

    //Product 1
    [
        'name' => 'Classic Leather Wallet (Men)',
        'price' => '₱180',
        'raw_price' => 180.00,
        'image' => '../../image/New-arrivals/Classic Leather Wallet (Men).jpeg',
        'rating' => 4.7,
        'sold' => 670,
        'discount' => '-21%',
        'link' => 'view-product.php?id=201'
    ],

    //Product 2
    [
        'name' => 'Trendy Crossbody Bag (Women)',
        'price' => '₱1,495',
        'raw_price' => 1495.00,
        'image' => '../../image/New-arrivals/Trendy Crossbody Bag (Women).jpeg',
        'rating' => 4.3,
        'sold' => 2500,
        'discount' => '-20%',
        'link' => 'view-product.php?id=202'
    ],

    //Product 3
    [
        'name' => 'Wireless Bluetooth Earbuds',
        'price' => '₱219',
        'raw_price' => 219.00,
        'image' => '../../image/New-arrivals/Wireless Bluetooth Earbuds.jpeg',
        'rating' => 4.1,
        'sold' => 4200,
        'discount' => '-20%',
        'link' => 'view-product.php?id=203'
    ],

    //Product 4
    [
        'name' => 'Oversized Graphic T-Shirt',
        'price' => '₱149',
        'raw_price' => 149.00,
        'image' => '../../image/New-arrivals/Oversized Graphic T-Shirt.jpeg',
        'rating' => 4.9,
        'sold' => 3100,
        'discount' => '-20%',
        'link' => 'view-product.php?id=204'
    ],

    //Product 5
    [
        'name' => 'Smart LED Desk Lamp',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Smart LED Desk Lamp.jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=205'
    ],

    //Product 6
    [
        'name' => 'Minimalist Wrist Watch',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Minimalist Wrist Watch.jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=206'
    ],

    //Product 7
    [
        'name' => 'Canvas Tote Bag',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Canvas Tote Bag.jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=207'
    ],

    //Product 8
    [
        'name' => 'Portable Power Bank 10,000mAh',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Portable Power Bank 10,000mAh.jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=208'
    ],

    //Product 9
    [
        'name' => 'Fashion Sunglasses (UV Protected)',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Fashion Sunglasses (UV Protected).jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=209'
    ],

    //Product 10
    [
        'name' => 'Bluetooth Mini Speaker',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Bluetooth Mini Speaker.jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=210'
    ],

    //Product 11
    [
        'name' => 'Stainless Steel Water Bottle',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Stainless Steel Water Bottle.jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=211'
    ],

    //Product 12
    [
        'name' => 'Casual Sneakers (Unisex)',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Casual Sneakers (Unisex).jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=212'
    ],

    //Product 13
    [
        'name' => 'Phone Stand Holder (Adjustable)',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Phone Stand Holder (Adjustable).jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=213'
    ],

    //Product 14
    [
        'name' => 'Wireless Mouse (Silent Click)',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Wireless Mouse (Silent Click).jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=214'
    ],

    //Product 15
    [
        'name' => 'Aesthetic Desk Organizer Set',
        'price' => '₱299',
        'raw_price' => 299.00,
        'image' => '../../image/New-arrivals/Aesthetic Desk Organizer Set.jpeg',
        'rating' => 4.0,
        'sold' => 1800,
        'discount' => '-20%',
        'link' => 'view-product.php?id=215'
    ]




];
?>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <div class="image-wrapper">
                <?php if (isset($product['discount'])): ?>
                    <span class="discount-badge"><?php echo $product['discount']; ?></span>
                <?php endif; ?>
                <img src="<?php echo str_replace(' ', '%20', $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
            </div>
            <div class="product-details">
                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="product-price"><?php echo $product['price']; ?></div>
                <div class="product-meta">
                    <div class="rating">
                        <?php
                        $rating = $product['rating'];
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < floor($rating))
                                echo '<i class="fas fa-star"></i>';
                            else
                                echo '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>
                    <span class="sold"><?php echo number_format($product['sold']); ?> Sold</span>
                </div>
            </div>
            <a href="<?php echo $product['link']; ?>" class="add-to-cart-btn">Find Similar</a>
        </div>
    <?php endforeach; ?>
</div>



