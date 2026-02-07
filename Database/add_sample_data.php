<?php
include('../Database/config.php');

// 1. Create Sample Users if they don't exist
$sample_users = [
    ['fullname' => 'John Doe', 'email' => 'john@example.com', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'username' => 'johndoe'],
    ['fullname' => 'Maria Clara', 'email' => 'maria@example.com', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'username' => 'mariac'],
    ['fullname' => 'Pedro Penduko', 'email' => 'pedro@example.com', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'username' => 'pedrop'],
    ['fullname' => 'Juana Change', 'email' => 'juana@example.com', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'username' => 'juanac'],
    ['fullname' => 'Boy Bawang', 'email' => 'boy@example.com', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'username' => 'boyb']
];

$user_ids = [];
foreach ($sample_users as $user) {
    $email = $user['email'];
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $user_ids[] = $row['id'];
    } else {
        $fullname = $user['fullname'];
        $pass = $user['password'];
        $username = $user['username'];
        mysqli_query($conn, "INSERT INTO users (fullname, email, password, username) VALUES ('$fullname', '$email', '$pass', '$username')");
        $user_ids[] = mysqli_insert_id($conn);
    }
}

// 2. Add Sample Reviews for all Best Selling Products (101-115)
$product_ids = range(101, 115);

$sample_pool = [
    // Positive
    ['rating' => 5, 'comment' => 'Sulit na sulit! The quality is way better than expected.'],
    ['rating' => 5, 'comment' => 'Ganda ng item, legit na legit. Mabilis din ang delivery.'],
    ['rating' => 5, 'comment' => 'Amazing design and very durable. 5 stars for this!'],
    ['rating' => 4, 'comment' => 'Good quality product. Worth the price. Will buy again.'],
    ['rating' => 5, 'comment' => 'Best purchase so far! Highly recommended to everyone.'],
    ['rating' => 5, 'comment' => 'Satisfied customer here. Items were well-packaged.'],
    
    // Negative
    ['rating' => 1, 'comment' => 'Pangit ng quality, wag niyo na bilhin. Sayang lang pera.'],
    ['rating' => 2, 'comment' => 'Disappointed. The item looks different from the pictures.'],
    ['rating' => 1, 'comment' => 'Sira agad after a few uses. Terrible experience.'],
    ['rating' => 1, 'comment' => 'Bad customer service and very slow shipping.'],
    
    // Neutral
    ['rating' => 3, 'comment' => 'Sakto lang, pwede na for its price.'],
    ['rating' => 3, 'comment' => 'Okay naman, normal standard quality.'],
    ['rating' => 3, 'comment' => 'Fine product. Nothing special but works as intended.'],
    ['rating' => 3, 'comment' => 'Average quality. You get what you pay for.']
];

// Clear all existing reviews for these products first
$id_list = implode(',', $product_ids);
mysqli_query($conn, "DELETE FROM reviews WHERE product_id IN ($id_list)");

foreach ($product_ids as $pid) {
    // Add 4-6 random reviews for each product
    $num_reviews = rand(4, 7);
    shuffle($sample_pool);
    
    for ($i = 0; $i < $num_reviews; $i++) {
        $review = $sample_pool[$i];
        $uid = $user_ids[array_rand($user_ids)]; // Random user
        $rating = $review['rating'];
        $comment = mysqli_real_escape_string($conn, $review['comment']);
        
        $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment, created_at) 
                VALUES ('$uid', '$pid', 0, '$rating', '$comment', DATE_SUB(NOW(), INTERVAL ".rand(1, 30)." DAY))";
        mysqli_query($conn, $sql);
    }
}

echo "SUCCESS_REVIEWS_ALL_101_TO_115_UPLOADED_V2";
?>
