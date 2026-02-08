<?php
$products = [
    //Product 1
    [
        'name' => 'Men’s Plain T-Shirt (Cotton)',
        'price' => '₱100.00',
        'image' => '../../image/fashion-apparel/Men’s Plain T-Shirt (Cotton).jpeg',
        'link' => 'view-product.php?id=401',
        'discount' => '-20%'
    ],

    //Product 2
    [
        'name' => 'Women’s Oversized Blouse',
        'price' => '₱340.00',
        'image' => '../../image/fashion-apparel/Women’s Oversized Blouse.jpeg',
        'link' => 'view-product.php?id=402',
        'discount' => '-20%',
    ],

    //Product 3
    [
        'name' => 'Slim Fit Denim Jeans (Men)',
        'price' => '₱50.00',
        'image' => '../../image/fashion-apparel/Slim Fit Denim Jeans (Men).jpeg',
        'link' => 'view-product.php?id=403',
        'discount' => '-20%',
    ],

    //Product 4
    [
        'name' => 'High-Waist Skinny Jeans (Women)',
        'price' => '₱1,500.00',
        'image' => '../../image/fashion-apparel/High-Waist Skinny Jeans (Women).jpeg',
        'link' => 'view-product.php?id=404',
        'discount' => '-20%',
    ],

    //Product 5
    [
        'name' => 'Unisex Hoodie (Pullover Style)',
        'price' => '₱2,500.00',
        'image' => '../../image/fashion-apparel/Unisex Hoodie (Pullover Style).jpeg',
        'link' => 'view-product.php?id=405',
        'discount' => '-20%',
    ],

    //Product 6
    [
        'name' => 'Casual Polo Shirt',
        'price' => '₱10,200.00',
        'image' => '../../image/fashion-apparel/Casual Polo Shirt.jpeg',
        'link' => 'view-product.php?id=406',
        'discount' => '-20%',
    ],

    //Product 7
    [
        'name' => 'Summer Floral Dress',
        'price' => '₱15,000.00',
        'image' => '../../image/fashion-apparel/Summer Floral Dress.jpeg',
        'link' => 'view-product.php?id=407',
        'discount' => '-20%',
    ],

    //Product 8
    [
        'name' => 'Jogger Pants (Unisex)',
        'price' => '₱20,000.00',
        'image' => '../../image/fashion-apparel/Jogger Pants (Unisex).jpeg',
        'link' => 'view-product.php?id=408',
        'discount' => '-20%',
    ],

    //Product 9
    [
        'name' => 'Bomber Jacket (Lightweight)',
        'price' => '₱59,000.00',
        'image' => '../../image/fashion-apparel/Bomber Jacket (Lightweight).jpeg',
        'link' => 'view-product.php?id=409',
        'discount' => '-20%',
    ],

    //Product 10
    [
        'name' => 'Crop Top (Trendy Style)',
        'price' => '₱100.00',
        'image' => '../../image/fashion-apparel/Crop Top (Trendy Style).jpeg',
        'link' => 'view-product.php?id=410',
        'discount' => '-20%',
    ],

    //Product 11
    [
        'name' => 'Long Sleeve Polo Shirt',
        'price' => '₱1,000.00',
        'image' => '../../image/fashion-apparel/Long Sleeve Polo Shirt.jpeg',
        'link' => 'view-product.php?id=411',
        'discount' => '-20%',
    ],

    //Product 12
    [
        'name' => 'Denim Jacket (Classic Fit)',
        'price' => '₱100.00',
        'image' => '../../image/fashion-apparel/Denim Jacket (Classic Fit).jpeg',
        'link' => 'view-product.php?id=412',
        'discount' => '-20%',
    ],

    //Product 13
    [
        'name' => 'Cotton Shorts (Men Women)',
        'price' => '₱299.00',
        'image' => '../../image/fashion-apparel/Cotton Shorts (Men Women).jpeg',
        'link' => 'view-product.php?id=413',
        'discount' => '-20%',
    ],

    //Product 14
    [
        'name' => 'Cardigan Sweater (Women)',
        'price' => '₱299',
        'image' => '../../image/fashion-apparel/Cardigan Sweater (Women).jpeg',
        'link' => 'view-product.php?id=414',
        'discount' => '-20%',
    ],

    //Product 15
    [
        'name' => 'Athletic Leggings (High Waist)',
        'price' => '₱299',
        'image' => '../../image/fashion-apparel/Athletic Leggings (High Waist).jpeg',
        'link' => 'view-product.php?id=415',
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



