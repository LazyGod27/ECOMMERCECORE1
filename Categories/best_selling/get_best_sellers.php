<?php
/**
 * Get Best-Selling Products
 * Query the most frequently purchased products from the database
 */

if (!isset($conn)) {
    require_once __DIR__ . '/../../Database/config.php';
}

/**
 * Fetch best-selling products from database
 * @param int $limit Number of products to return (default: 15)
 * @return array Array of best-selling products
 */
function getBestSellingProducts($conn, $limit = 15) {
    $products = [];
    
    // Query to get products sorted by total quantity sold
    $query = "
        SELECT 
            p.id,
            p.name,
            p.price,
            p.image_url,
            p.description,
            SUM(oi.quantity) as total_sold,
            COUNT(DISTINCT oi.order_id) as num_orders
        FROM products p
        LEFT JOIN order_items oi ON p.id = oi.product_id
        WHERE p.status = 'Active'
        GROUP BY p.id, p.name, p.price, p.image_url, p.description
        ORDER BY total_sold DESC, p.id DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        // Fallback if prepare fails
        error_log("Prepare failed: " . $conn->error);
        return getStaticBestSellingProducts();
    }
    
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Convert results to product array format
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => '₱' . number_format($row['price'], 2),
            'image' => !empty($row['image_url']) ? $row['image_url'] : '../../image/Best-seller/default.jpg',
            'link' => 'view-product.php?id=' . $row['id'],
            'discount' => '-20%',
            'variants' => [],
            'total_sold' => $row['total_sold'],
            'num_orders' => $row['num_orders'],
            'description' => !empty($row['description']) ? $row['description'] : 'High-quality product that customers love. Great value for money.',
            'reviews' => [
                [
                    'reviewer_name' => 'Customer 1',
                    'rating' => 5,
                    'comment' => 'Excellent product! Very satisfied with my purchase.',
                    'date' => '1 week ago'
                ],
                [
                    'reviewer_name' => 'Customer 2',
                    'rating' => 5,
                    'comment' => 'Great quality and fast shipping. Highly recommended!',
                    'date' => '2 weeks ago'
                ],
                [
                    'reviewer_name' => 'Customer 3',
                    'rating' => 4,
                    'comment' => 'Good product, meets expectations perfectly.',
                    'date' => '3 weeks ago'
                ]
            ]
        ];
    }
    
    $stmt->close();
    
    // If no products found in database, return static products as fallback
    if (count($products) === 0) {
        return getStaticBestSellingProducts();
    }
    
    return $products;
}

/**
 * Fallback static products (for when database is empty)
 * @return array Static best-selling products
 */
function getStaticBestSellingProducts() {
    return [
        [
            'id' => 101,
            'name' => 'bag Sholder Men',
            'price' => '₱100.00',
            'image' => '../../image/Best-seller/bag-men.jpeg',
            'link' => 'view-product.php?id=101',
            'discount' => '-20%',
            'variants' => [
                'Brown' => 'https://via.placeholder.com/400x400/8b6f47/996633?text=Brown',
                'Black' => 'https://via.placeholder.com/400x400/1a1a1a/333333?text=Black'
            ],
            'total_sold' => 0,
            'num_orders' => 0,
            'description' => 'Stylish and durable mens shoulder bag perfect for daily use. Premium leather construction with comfortable strap.',
            'reviews' => [
                [
                    'reviewer_name' => 'Carlos',
                    'rating' => 5,
                    'comment' => 'Excellent quality! Very comfortable to wear and looks great. Customer service was amazing.',
                    'date' => '1 week ago'
                ],
                [
                    'reviewer_name' => 'Miguel',
                    'rating' => 5,
                    'comment' => 'Best purchase ever! The leather is top-notch and spacious enough for my daily items.',
                    'date' => '2 weeks ago'
                ],
                [
                    'reviewer_name' => 'Roberto',
                    'rating' => 4,
                    'comment' => 'Great bag, slightly smaller than expected but still very functional.',
                    'date' => '3 weeks ago'
                ]
            ]
        ],
        [
            'id' => 102,
            'name' => 'bag women',
            'price' => '₱340.00',
            'image' => '../../image/Best-seller/bag-women.jpeg',
            'link' => 'view-product.php?id=102',
            'discount' => '-20%',
            'variants' => [
                'Burgundy' => 'https://via.placeholder.com/400x400/800020/cc3333?text=Burgundy',
                'Black' => 'https://via.placeholder.com/400x400/1a1a1a/333333?text=Black',
                'Beige' => 'https://via.placeholder.com/400x400/f5f5dc/cccccc?text=Beige'
            ],
            'total_sold' => 0,
            'num_orders' => 0,
            'description' => 'Elegant womens handbag with sophisticated design. Multiple compartments and premium finish make it perfect for any occasion.',
            'reviews' => [
                [
                    'reviewer_name' => 'Sofia',
                    'rating' => 5,
                    'comment' => 'Absolutely love this bag! The color is perfect and it pairs well with everything in my wardrobe.',
                    'date' => '5 days ago'
                ],
                [
                    'reviewer_name' => 'Maria',
                    'rating' => 5,
                    'comment' => 'High quality material and beautiful design. Worth every peso spent!',
                    'date' => '1 week ago'
                ],
                [
                    'reviewer_name' => 'Angela',
                    'rating' => 4,
                    'comment' => 'Nice bag, exactly as described. Arrived quickly and packaging was excellent.',
                    'date' => '2 weeks ago'
                ]
            ]
        ],
        [
            'id' => 103,
            'name' => 'Notebook',
            'price' => '₱50.00',
            'image' => '../../image/Best-seller/Notebooks.jpeg',
            'link' => 'view-product.php?id=103',
            'discount' => '-20%',
            'variants' => [
                'Lined' => 'https://via.placeholder.com/400x400/ffffff/cccccc?text=Lined',
                'Blank' => 'https://via.placeholder.com/400x400/f0f0f0/999999?text=Blank',
                'Dotted' => 'https://via.placeholder.com/400x400/fafafa/aaaaaa?text=Dotted'
            ],
            'total_sold' => 0,
            'num_orders' => 0,
            'description' => 'Premium quality notebook with 100 pages. Smooth paper perfect for writing, sketching, and journaling.',
            'reviews' => [
                [
                    'reviewer_name' => 'Emma',
                    'rating' => 5,
                    'comment' => 'Perfect for daily journaling! The paper quality is exceptional.',
                    'date' => '3 days ago'
                ],
                [
                    'reviewer_name' => 'David',
                    'rating' => 5,
                    'comment' => 'Great notebook! Pen doesnt bleed through and the cover is durable.',
                    'date' => '10 days ago'
                ],
                [
                    'reviewer_name' => 'Isabella',
                    'rating' => 5,
                    'comment' => 'Best notebook Ive bought. Amazing paper quality and perfect size!',
                    'date' => '2 weeks ago'
                ]
            ]
        ],
        [
            'id' => 104,
            'name' => 'Earphone Bluetooth',
            'price' => '₱1,500.00',
            'image' => '../../image/Best-seller/Earphone-bluetooth.jpeg',
            'link' => 'view-product.php?id=104',
            'discount' => '-20%',
            'variants' => [
                'Black' => 'https://via.placeholder.com/400x400/1a1a1a/333333?text=Black',
                'Silver' => 'https://via.placeholder.com/400x400/c0c0c0/999999?text=Silver',
                'White' => 'https://via.placeholder.com/400x400/ffffff/cccccc?text=White'
            ],
            'total_sold' => 0,
            'num_orders' => 0,
            'description' => 'High-quality Bluetooth earphones with crystal clear sound. Long battery life and comfortable fit for extended wear.',
            'reviews' => [
                [
                    'reviewer_name' => 'James',
                    'rating' => 5,
                    'comment' => 'Excellent sound quality! Battery lasts all day and they fit perfectly.',
                    'date' => '1 week ago'
                ],
                [
                    'reviewer_name' => 'Lisa',
                    'rating' => 5,
                    'comment' => 'Best earphones for the price! Amazing bass and clear treble. Very happy with my purchase.',
                    'date' => '2 weeks ago'
                ],
                [
                    'reviewer_name' => 'Alex',
                    'rating' => 4,
                    'comment' => 'Really good quality. Sound is clear and charging case is convenient.',
                    'date' => '1 month ago'
                ]
            ]
        ],
        [
            'id' => 105,
            'name' => 'Snikers Shoes',
            'price' => '₱2,500.00',
            'image' => '../../image/Best-seller/snikers%20shoes.avif',
            'link' => 'view-product.php?id=105',
            'discount' => '-20%',
            'variants' => [
                'White' => 'https://via.placeholder.com/400x400/ffffff/cccccc?text=White',
                'Black' => 'https://via.placeholder.com/400x400/1a1a1a/333333?text=Black',
                'Gray' => 'https://via.placeholder.com/400x400/808080/999999?text=Gray'
            ],
            'total_sold' => 0,
            'num_orders' => 0,
            'description' => 'Comfortable and stylish sneakers for everyday wear. Durable sole with breathable mesh upper for all-day comfort.',
            'reviews' => [
                [
                    'reviewer_name' => 'Juan',
                    'rating' => 5,
                    'comment' => 'Super comfortable shoes! Perfect for walking around the city all day.',
                    'date' => '5 days ago'
                ],
                [
                    'reviewer_name' => 'Patricia',
                    'rating' => 5,
                    'comment' => 'Great quality and very durable. My feet feel great wearing them!',
                    'date' => '2 weeks ago'
                ],
                [
                    'reviewer_name' => 'Vincent',
                    'rating' => 4,
                    'comment' => 'Good shoes, fit is true to size. Slightly stiff at first but broken in nicely.',
                    'date' => '1 month ago'
                ]
            ]
        ],
        [
            'id' => 106,
            'name' => 'swatch watch',
            'price' => '₱10,200.00',
            'image' => '../../image/Best-seller/Snart%20watch.jpeg',
            'link' => 'view-product.php?id=106',
            'discount' => '-20%',
            'variants' => [
                'Black' => 'https://via.placeholder.com/400x400/1a1a1a/333333?text=Black',
                'Silver' => 'https://via.placeholder.com/400x400/c0c0c0/999999?text=Silver',
                'Rose Gold' => 'https://via.placeholder.com/400x400/b76d6d/cc9999?text=Rose+Gold'
            ],
            'total_sold' => 0,
            'num_orders' => 0,
            'description' => 'Premium quality smartwatch with advanced features. Heart rate monitor, fitness tracking, and elegant design.',
            'reviews' => [
                [
                    'reviewer_name' => 'Richard',
                    'rating' => 5,
                    'comment' => 'Outstanding smartwatch! Features are incredible and battery lasts for days.',
                    'date' => '1 week ago'
                ],
                [
                    'reviewer_name' => 'Catherine',
                    'rating' => 5,
                    'comment' => 'Love the design and functionality! Tracks my workouts perfectly and looks elegant on my wrist.',
                    'date' => '2 weeks ago'
                ],
                [
                    'reviewer_name' => 'Marcus',
                    'rating' => 4,
                    'comment' => 'Great watch with good features. Initial setup took time but works smoothly now.',
                    'date' => '3 weeks ago'
                ]
            ]
        ]
    ];
}

?>
