<?php
$path_prefix = '../../';

// Get product ID from URL, default to 301 if missing or invalid
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 301;

// Capture product output to get access to variables like $name
ob_start();
include 'product_template.php';
$product_html = ob_get_clean();

// Fallback if name is not set
$page_title = isset($name) ? $name : "Product Details";
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../../image/logo.png">
    <title><?php echo htmlspecialchars($page_title); ?> - IMarket</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>css/components/product-view.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>css/dashboard/reviews.css?v=<?php echo time(); ?>">
</head>

<body>
    <nav>
        <?php

        $path_prefix = '../../';
        include '../../Components/header.php';
        ?>
    </nav>

    <div class="product-view-container">
            <?php echo $product_html; ?>
    </div>

    <div class="reviews">
        <?php

        // Ensure product_id is set for the reviews section to use
        // $product_id is already set above
        include 'reviews_section.php';
        ?>
    </div>

    <footer>
        <?php

        $path_prefix = '../../';
        include '../../Components/footer.php';
        ?>
    </footer>
</body>

</html>













