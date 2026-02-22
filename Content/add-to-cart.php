<?php
session_start();
include("../Database/config.php");

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect simply to login if not logged in
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// 1.5 Handle Add to Cart (from Product Page)
// Supports legacy GET or POST
if (isset($_GET['add_to_cart']) || isset($_POST['add_to_cart'])) {
    $p_id = intval(isset($_GET['product_id']) ? $_GET['product_id'] : (isset($_POST['product_id']) ? $_POST['product_id'] : 0));
    $p_qty = intval(isset($_GET['quantity']) ? $_GET['quantity'] : (isset($_POST['quantity']) ? $_POST['quantity'] : 1));

    // Try to fetch product details from DB if product_id provided. This is safer than trusting client inputs.
    $p_name = '';
    $p_price = 0.0;
    $p_image = '';
    $shop_name = '';

    if ($p_id > 0) {
        $safe_pid = intval($p_id);
        // Products table uses image_url, not image. Check if shop_name column exists first
        $shop_col_check = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'shop_name'");
        $has_shop_name = ($shop_col_check && mysqli_num_rows($shop_col_check) > 0);
        
        // Build query based on whether shop_name column exists
        if ($has_shop_name) {
            $prod_sql = "SELECT name, price, image_url, 
                         COALESCE(shop_name, 'IMarket Official Store') as shop_name 
                         FROM products WHERE id='$safe_pid' LIMIT 1";
        } else {
            $prod_sql = "SELECT name, price, image_url FROM products WHERE id='$safe_pid' LIMIT 1";
        }
        
        $prod_res = mysqli_query($conn, $prod_sql);
        if ($prod_res && mysqli_num_rows($prod_res) > 0) {
            $prod = mysqli_fetch_assoc($prod_res);
            // Support both possible column names (legacy compatibility)
            $p_name = isset($prod['name']) ? $prod['name'] : (isset($prod['product_name']) ? $prod['product_name'] : 'Product');
            $p_price = isset($prod['price']) ? floatval($prod['price']) : 0.0;
            $p_image = isset($prod['image_url']) && !empty($prod['image_url']) ? $prod['image_url'] : '';
            $shop_name = ($has_shop_name && isset($prod['shop_name']) && !empty($prod['shop_name'])) 
                        ? $prod['shop_name'] 
                        : 'IMarket Official Store';
        }
    }

    // Fall back to client-provided fields if DB lookup did not return values (legacy behavior)
    if (empty($p_name)) {
        $p_name = mysqli_real_escape_string($conn, isset($_GET['product_name']) ? $_GET['product_name'] : (isset($_POST['product_name']) ? $_POST['product_name'] : ''));
    }
    if (empty($p_image)) {
        $p_image = mysqli_real_escape_string($conn, isset($_GET['image']) ? $_GET['image'] : (isset($_POST['image']) ? $_POST['image'] : ''));
    }
    if (empty($p_price)) {
        $p_price = floatval(isset($_GET['price']) ? $_GET['price'] : (isset($_POST['price']) ? $_POST['price'] : 0));
    }
    if (empty($shop_name)) {
        $shop_name = isset($_GET['store']) ? mysqli_real_escape_string($conn, $_GET['store']) : (isset($_POST['store']) ? mysqli_real_escape_string($conn, $_POST['store']) : '');
    }

    // Check if item already exists in cart, update quantity? Or just insert new row (Shopee usually groups, but simple insert for now or unique constraint)
    $check_sql = "SELECT * FROM cart WHERE user_id='$user_id' AND product_name='$p_name'";
    $check_res = mysqli_query($conn, $check_sql);

    // If shop name is not provided, try to infer it from product name or defaults
    if (empty($shop_name)) {
        // Fallback or default
        // Could implement logic here to detect if "UrbanWear" etc
    }

    if (mysqli_num_rows($check_res) > 0) {
        // Update quantity
        $existing = mysqli_fetch_assoc($check_res);
        $new_qty = $existing['quantity'] + $p_qty;
        $update_sql = "UPDATE cart SET quantity='$new_qty' WHERE id='" . $existing['id'] . "'";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: add-to-cart.php"); // Refresh to clean URL
            exit();
        }
    } else {
        // Insert new
        $insert_sql = "INSERT INTO cart (user_id, product_id, product_name, price, quantity, image, shop_name) VALUES ('$user_id', '$p_id', '" . mysqli_real_escape_string($conn, $p_name) . "', '$p_price', '$p_qty', '" . mysqli_real_escape_string($conn, $p_image) . "', '" . mysqli_real_escape_string($conn, $shop_name) . "')";
        if (mysqli_query($conn, $insert_sql)) {
            header("Location: add-to-cart.php");
            exit();
        } else {
            $msg = "<div class='alert-error'>Error adding to cart: " . mysqli_error($conn) . "</div>";
        }
    }
}

// 2. Handle POST Actions (Delete, Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Delete Selected Items
    if (isset($_POST['delete_selected'])) {
        if (!empty($_POST['selected_items'])) {
            // Sanitize IDs
            $ids_to_delete = array_map('intval', $_POST['selected_items']);
            $ids_string = implode(",", $ids_to_delete);

            $delete_sql = "DELETE FROM cart WHERE id IN ($ids_string) AND user_id = '$user_id'";
            if (mysqli_query($conn, $delete_sql)) {
                $msg = "<div class='alert-success'>Selected items deleted successfully.</div>";
            } else {
                $msg = "<div class='alert-error'>Error deleting items: " . mysqli_error($conn) . "</div>";
            }
        } else {
            // Only show warning if not a direct single delete (which is handled slightly differently in UI usually)
            $msg = "<div class='alert-warning'>Please select items to delete.</div>";
        }
    }

    // Future: Handle Update Quantity
    if (isset($_POST['update_cart'])) {
        if (!empty($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $cart_id => $qty) {
                $cid = intval($cart_id);
                $new_qty = max(1, intval($qty));
                $upd = "UPDATE cart SET quantity='$new_qty' WHERE id='$cid' AND user_id='$user_id'";
                mysqli_query($conn, $upd);
            }
            $msg = "<div class='alert-success'>Cart updated.</div>";
            // Refresh to reflect changes
            header("Location: add-to-cart.php");
            exit();
        }
    }
}

// 3. Fetch Cart Items
$sql = "SELECT * FROM cart WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$cart_items = [];
$total_price = 0;
$total_items = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
        // Don't calculate total here for display if we want dynamic JS selection totals, 
        // but for initial load or full cart value:
        $total_price += ($row['price'] * $row['quantity']);
        $total_items += $row['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="../css/best_selling/cart.css">
    <!-- Using Shop's CSS as base, can override -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <title>Shopping Cart</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif !important;
            background-color: #f8f9fa;
            color: #1a1a1a;
        }

        /* Shared Styles (can be moved to a css file later) */
        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            padding: 14px 16px;
            margin: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(15, 81, 50, 0.1);
            animation: slideDown 0.3s ease;
        }

        .alert-error {
            color: #842029;
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            padding: 14px 16px;
            margin: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(132, 32, 41, 0.1);
            animation: slideDown 0.3s ease;
        }

        .alert-warning {
            color: #664d03;
            background-color: #fff3cd;
            border: 1px solid #ffecb5;
            padding: 14px 16px;
            margin: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(102, 77, 3, 0.1);
        }

        .cart-main-container {
            min-height: 400px;
            padding: 30px 20px 120px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Custom Modal Styles */
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease-out;
            backdrop-filter: blur(2px);
        }

        .custom-modal-content {
            background-color: #ffffff;
            margin: 20% auto;
            padding: 30px;
            border: none;
            width: 90%;
            max-width: 420px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        .close-modal {
            color: #999;
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 28px;
            font-weight: 300;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: #2A3B7E;
        }

        #modalMessage {
            margin: 15px 0;
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }

        .btn-ok {
            background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%);
            color: white;
            padding: 12px 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .btn-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.4);
        }

        .btn-ok:active {
            transform: translateY(0);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Unified Cart Branding Overrides */
        .cart-header-bar {
            background: linear-gradient(to bottom, #ffffff, #f8f9fa);
            border-bottom: 1px solid #e5e5e5;
            padding: 18px 0;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .cart-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            gap: 30px;
        }

        .cart-branding {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-shrink: 0;
        }

        .cart-logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #2A3B7E;
            gap: 10px;
            transition: opacity 0.3s ease;
        }

        .cart-logo-link:hover {
            opacity: 0.8;
        }

        .cart-logo-link img {
            height: 45px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .cart-logo-text {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1.5px;
            background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cart-divider {
            height: 35px;
            width: 1.5px;
            background: linear-gradient(to bottom, transparent, #ddd, transparent);
        }

        .cart-page-title {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a1a;
            letter-spacing: 0.3px;
        }

        .cart-search-container input {
            transition: all 0.3s ease;
        }

        .cart-search-container input:focus {
            box-shadow: 0 0 0 3px rgba(42, 59, 126, 0.1);
        }

        /* Cart Table Styles */
        .cart-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .cart-table thead {
            background: linear-gradient(135deg, #f5f7fb 0%, #eef2f8 100%);
            border-bottom: 2px solid #e5e5e5;
        }

        .cart-table thead th {
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
        }

        .cart-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .cart-table tbody tr:hover {
            background-color: #fafafa;
        }

        .cart-table tbody tr:last-child {
            border-bottom: none;
        }

        .cart-table tbody td {
            padding: 16px 12px;
            vertical-align: middle;
        }

        .cart-table input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #2A3B7E;
        }

        /* Product Column */
        .cart-table .th-product,
        .cart-table .th-price,
        .cart-table .th-qty,
        .cart-table .th-total,
        .cart-table .th-actions {
            padding: 16px 12px;
        }

        /* Quantity Input Styling */
        .cart-table input[type="number"] {
            width: 70px;
            padding: 8px 10px;
            border: 2px solid #e5e5e5;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .cart-table input[type="number"]:focus {
            outline: none;
            border-color: #2A3B7E;
            box-shadow: 0 0 0 3px rgba(42, 59, 126, 0.1);
        }

        .cart-table input[type="number"]::-webkit-outer-spin-button,
        .cart-table input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Delete Button */
        .cart-table button[name="delete_selected"] {
            color: #ff6b6b;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            padding: 6px 10px;
            border-radius: 4px;
        }

        .cart-table button[name="delete_selected"]:hover {
            background-color: rgba(255, 107, 107, 0.1);
            color: #ff5252;
        }

        /* Suggested Products Section */
        .suggested-card {
            background: white;
            border: 1px solid #e8e8e8;
            padding: 14px;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .suggested-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(42, 59, 126, 0.15);
            border-color: #2A3B7E;
        }

        .suggested-card .suggested-image {
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .suggested-card:hover .suggested-image {
            transform: scale(1.05);
        }

        .suggested-card button {
            background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%);
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(42, 59, 126, 0.2);
        }

        .suggested-card button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 16px rgba(42, 59, 126, 0.3);
        }

        .suggested-card button:active {
            transform: translateY(0);
        }

        .suggested-card a {
            border: 2px solid #e5e5e5;
            transition: all 0.3s ease;
        }

        .suggested-card a:hover {
            border-color: #2A3B7E;
            background-color: #f8f9fa;
        }

        /* Empty Cart State */
        .empty-cart-section {
            background: white;
            border-radius: 10px;
            padding: 60px 30px !important;
            text-align: center;
            margin-top: 30px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .empty-cart-icon {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-cart-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .empty-cart-text {
            color: #999;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .btn-continue-shopping {
            display: inline-block;
            padding: 12px 40px !important;
            background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%) !important;
            color: white !important;
            text-decoration: none !important;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .btn-continue-shopping:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(42, 59, 126, 0.4);
        }

        /* Bottom Actions Bar */
        .cart-bottom-bar {
            background: white;
            border-top: 1px solid #e5e5e5;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
        }

        .cart-bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .bottom-left-actions {
            display: flex;
            gap: 25px;
            align-items: center;
            flex: 1;
        }

        .bottom-left-actions label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
            color: #333;
            transition: color 0.3s ease;
            user-select: none;
        }

        .bottom-left-actions label:hover {
            color: #2A3B7E;
        }

        .bottom-left-actions input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
            accent-color: #2A3B7E;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            padding: 6px 12px;
            border-radius: 4px;
        }

        .action-btn:hover {
            color: #2A3B7E;
            background-color: rgba(42, 59, 126, 0.05);
        }

        .action-btn[name="update_cart"] {
            color: #2A3B7E;
        }

        .action-btn[name="delete_selected"] {
            color: #ff6b6b;
        }

        .action-btn[name="delete_selected"]:hover {
            background-color: rgba(255, 107, 107, 0.1);
            color: #ff5252;
        }

        .bottom-right-actions {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-shrink: 0;
        }

        .total-label {
            font-weight: 500;
            color: #666;
            font-size: 14px;
        }

        .total-price {
            font-size: 26px;
            color: #2A3B7E;
            font-weight: 700;
            min-width: 140px;
            text-align: right;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
            white-space: nowrap;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(42, 59, 126, 0.4);
        }

        .btn-checkout:active {
            transform: translateY(0);
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 300ms ease-out both;
        }

        .fly-image {
            position: fixed;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            pointer-events: none;
            transition: transform 600ms cubic-bezier(0.25, 0.46, 0.45, 0.94), top 600ms cubic-bezier(0.25, 0.46, 0.45, 0.94), left 600ms cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 400ms ease;
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .pulse {
            animation: pulse 500ms cubic-bezier(0.4, 0, 0.6, 1);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-header-content {
                flex-direction: column;
                gap: 15px;
            }

            .cart-search-container {
                width: 100% !important;
                max-width: none !important;
                margin-left: 0 !important;
            }

            .cart-table thead {
                font-size: 12px;
            }

            .cart-table tbody td {
                padding: 12px 8px;
                font-size: 13px;
            }

            .cart-bottom-content {
                flex-direction: column;
                gap: 15px;
            }

            .bottom-left-actions {
                width: 100%;
                flex-wrap: wrap;
            }

            .bottom-right-actions {
                width: 100%;
                gap: 10px;
            }

            .btn-checkout {
                width: 100%;
                justify-content: center;
            }

            .cart-header-content > div:last-child {
                width: 100%;
                flex-direction: column;
            }

            .cart-header-content > div:last-child a {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <nav>
        <?php
        $path_prefix = '../';
        include '../Components/header.php';
        ?>
    </nav>

    <div class="cart-header-bar">
        <div class="cart-header-content">
            <div class="cart-branding">
                <a href="../Categories/best_selling/index.php" class="cart-logo-link">
                    <img src="../image/logo.png" alt="Imarket Logo">
                    <span class="cart-logo-text">IMARKET</span>
                </a>
                <div class="cart-divider"></div>
                <span class="cart-page-title">Shopping Cart</span>
            </div>

            <div class="cart-search-container" style="flex: 1; max-width: 500px; margin-left: 50px;">
                <div style="display: flex; border: 2px solid #2A3B7E; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(42, 59, 126, 0.1);">
                    <input type="text"
                        style="flex: 1; border: none; padding: 12px 16px; outline: none; font-family: inherit; font-size: 14px;"
                        placeholder="Search for products, brands and shops">
                    <button style="background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%); border: none; padding: 0 20px; color: white; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(42, 59, 126, 0.3)'" onmouseout="this.style.boxShadow='none'">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div style="display: flex; gap: 12px; flex-shrink: 0;">
                <a href="user-account.php?view=orders" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: rgba(42, 59, 126, 0.08); color: #2A3B7E; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; border: 1px solid rgba(42, 59, 126, 0.15);" onmouseover="this.style.backgroundColor='rgba(42, 59, 126, 0.12)'" onmouseout="this.style.backgroundColor='rgba(42, 59, 126, 0.08)'">
                    <i class="fas fa-history"></i> My Orders
                </a>
                <a href="user-account.php" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(42, 59, 126, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 16px rgba(42, 59, 126, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(42, 59, 126, 0.2)'">
                    <i class="fas fa-user-circle"></i> Account
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Wrapped in Form -->
    <form action="" method="POST" id="cartForm">
        <div class="cart-main-container">

            <?php echo $msg; ?>

            <!-- Table Header -->
            <table class="cart-table" <?php if (count($cart_items) === 0)
                echo 'style="display:none;"';
            else
                echo ''; ?> width="100%" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center; padding: 16px 12px;">
                            <input type="checkbox" id="selectAllHeader" onclick="toggleSelectAll(this)">
                        </th>
                        <th class="th-product">Product</th>
                        <th class="th-price">Unit Price</th>
                        <th class="th-qty">Quantity</th>
                        <th class="th-total">Total Price</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($cart_items) > 0): ?>
                        <?php
                        // Group items? For now just list them
                        foreach ($cart_items as $item):
                            $item_total = $item['price'] * $item['quantity'];

                            // Determine if this item is Best Selling or Shop based
                            $is_best_selling_item = false;
                            $img_check_cart = strtolower($item['image']);
                            $best_selling_kws = ['bag-women', 'bag-men', 'notebooks', 'earphone', 'shoes', 'watch', 'best_selling'];
                            foreach ($best_selling_kws as $kw) {
                                if (strpos($img_check_cart, $kw) !== false) {
                                    $is_best_selling_item = true;
                                    break;
                                }
                            }
                            ?>

                            <!-- Dynamic Shop Header Per Item (Simplified: Display above the item if needed, or inline) -->
                            <!-- User requested: "Pwede pag isahin lang din sila... sa baba yung title" 
                                 Implies they want the "shop/Branding" header visible per item block or similar.
                                 Let's add a small header row or badge for the item's store context.
                            -->
                            <!-- Shop Header Row REMOVED -->

                            <tr style="background: white;" data-unit-price="<?php echo htmlspecialchars($item['price']); ?>">
                                <td style="text-align: center; padding: 16px 12px;">
                                    <input type="checkbox" name="selected_items[]" class="item-checkbox"
                                        value="<?php echo $item['id']; ?>" onchange="updateSummary()">
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <?php
                                        $raw_img = $item['image'];
                                        $display_img = $raw_img; // Default to raw path logic
                                
                                        // Image path resolution logic (check raw path)
                                        if (!file_exists($display_img) && strpos($display_img, 'http') === false) {
                                            $possible_paths = [
                                                '../image/Shop/' . basename($display_img),
                                                '../image/Best-seller/' . basename($display_img),
                                                '../image/Best/' . basename($display_img),
                                                '../image/' . basename($display_img),
                                                '../Categories/best_selling/image/' . basename($display_img)
                                            ];

                                            // Add Shop-specific path if shop_name exists
                                            if (!empty($item['shop_name'])) {
                                                array_unshift($possible_paths, '../image/Shop/' . $item['shop_name'] . '/' . basename($display_img));
                                            } else {
                                                // Fallback for UrbanWear
                                                $possible_paths[] = '../image/Shop/UrbanWear PH/' . basename($display_img);
                                            }

                                            foreach ($possible_paths as $path) {
                                                if (file_exists($path)) {
                                                    $display_img = $path;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($display_img); ?>" alt="Product"
                                            style="width: 85px; height: 85px; object-fit: cover; border: 1px solid #e8e8e8; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">

                                        <div style="display:flex; flex-direction:column; gap: 6px;">
                                            <span
                                                style="font-weight: 600; font-size: 15px; color: #1a1a1a; line-height: 1.3;"><?php echo htmlspecialchars($item['product_name']); ?></span>

                                            <!-- shop/Branding Subtitle -->
                                            <?php if ($is_best_selling_item): ?>
                                                <span style="font-size: 12px; color: #2A3B7E; font-weight: 600;"><i
                                                        class="fas fa-certificate" style="margin-right:4px;"></i> IMarket Best
                                                    Selling</span>
                                            <?php else: ?>
                                                <?php $disp_shop = !empty($item['shop_name']) ? $item['shop_name'] : "UrbanWear PH"; ?>
                                                <span style="font-size: 12px; color: #888;"><i class="fas fa-store"
                                                        style="margin-right:4px;"></i>
                                                    <?php echo htmlspecialchars($disp_shop); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; color: #2A3B7E; font-weight: 600; font-size: 15px;">₱<?php echo number_format($item['price'], 2); ?></td>
                                <td style="padding: 16px 12px;">
                                    <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo intval($item['quantity']); ?>" min="1" style="width: 70px; padding: 8px 10px; border: 2px solid #e5e5e5; border-radius: 6px; font-family: inherit; transition: all 0.3s ease;" onchange="onQtyChange(this)" onfocus="this.style.borderColor='#2A3B7E'; this.style.boxShadow='0 0 0 3px rgba(42, 59, 126, 0.1)'" onblur="this.style.borderColor='#e5e5e5'; this.style.boxShadow='none'">
                                </td>
                                <td class="item-total-price" style="color: #2A3B7E; font-weight: 700; font-size: 15px; padding: 16px 12px;">
                                    ₱<?php echo number_format($item_total, 2); ?></td>
                                <td style="padding: 16px 12px;">
                                    <button type="submit" name="delete_selected"
                                        onclick="selectSingleItem(<?php echo $item['id']; ?>)"
                                        style="color: #ff6b6b; background: none; border: none; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s ease; padding: 6px 10px; border-radius: 4px;" onmouseover="this.style.backgroundColor='rgba(255, 107, 107, 0.1)'; this.style.color='#ff5252';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ff6b6b';">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Suggested Products -->
            <?php
            // Load helper for best-selling products
            include_once('../Categories/best_selling/get_best_sellers.php');
            $suggested = [];
            if (function_exists('getBestSellingProducts')) {
                $suggested = getBestSellingProducts($conn, 4);
            }
            ?>

            <?php if (!empty($suggested)): ?>
                <div style="margin-top: 40px;">
                    <h3 style="margin: 0 0 20px 0; color: #1a1a1a; font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Suggested for you</h3>
                    <div style="display: flex; gap: 18px; flex-wrap: wrap;">
                        <?php foreach ($suggested as $s): ?>
                            <div class="suggested-card fade-in" data-product-id="<?php echo intval($s['id']); ?>" style="width: 220px;">
                                <img class="suggested-image" src="<?php echo htmlspecialchars($s['image']); ?>" alt="<?php echo htmlspecialchars($s['name']); ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                                <div>
                                    <div style="font-weight: 600; color: #1a1a1a; margin-bottom: 8px; font-size: 14px; line-height: 1.4;"><?php echo htmlspecialchars($s['name']); ?></div>
                                    <div style="color: #2A3B7E; font-weight: 700; margin-bottom: 12px; font-size: 16px;"><?php echo htmlspecialchars($s['price']); ?></div>
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" onclick="addSuggestedToCart(<?php echo intval($s['id']); ?>,1,this)" style="flex: 1; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 16px rgba(42, 59, 126, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(42, 59, 126, 0.2)'">Add to cart</button>
                                        <a href="../Categories/best_selling/<?php echo htmlspecialchars(basename($s['link'])); ?>" style="display: inline-block; padding: 10px; border-radius: 6px; border: 2px solid #e5e5e5; text-decoration: none; color: #333; font-weight: 600; font-size: 13px; transition: all 0.3s ease; flex: 1; text-align: center;" onmouseover="this.style.borderColor='#2A3B7E'; this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.borderColor='#e5e5e5'; this.style.backgroundColor='transparent'">View</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Empty Cart State -->
            <?php if (count($cart_items) === 0): ?>
                <div class="empty-cart-section">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="empty-cart-title">Your cart is empty</div>
                    <div class="empty-cart-text">Looks like you haven't added any items to your cart yet.</div>
                    <a href="../Shop/index.php" class="btn-continue-shopping">Continue Shopping</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bottom Actions Bar -->
        <div class="cart-bottom-bar" <?php if (count($cart_items) === 0)
            echo 'style="display:none;"'; ?> style="position: fixed; bottom: 0; left: 0; width: 100%; padding: 15px 0; z-index: 100;">
            <div class="cart-bottom-content" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div class="bottom-left-actions">
                    <label style="margin-right: 5px;">
                        <input type="checkbox" class="select-all-checkbox" id="selectAllFooter"
                            onclick="toggleSelectAll(this)">
                        Select All (<?php echo count($cart_items); ?>)
                    </label>
                    <button type="submit" name="delete_selected" class="action-btn"
                        style="color: #ff6b6b;">Delete</button>
                    <button type="submit" name="update_cart" class="action-btn"
                        style="">Update Cart</button>
                </div>
                <div class="bottom-right-actions">
                    <div class="total-label">Total (<?php echo $total_items; ?> items):</div>
                    <div class="total-price">
                        ₱<?php echo number_format($total_price, 2); ?></div>
                    <button type="button" class="btn-checkout" onclick="proceedToCheckout()"><i class="fas fa-lock"></i> Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </form>

    <footer>
        <?php include '../Components/footer.php'; ?>
    </footer>

    <!-- Custom Modal Structure -->
    <div id="customModal" class="custom-modal">
        <div class="custom-modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <p id="modalMessage">Message goes here</p>
            <button onclick="closeModal()" class="btn-ok">OK</button>
        </div>
    </div>

    <script>
        // Modal functions
        function showModal(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('customModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('customModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function (event) {
            const modal = document.getElementById('customModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Sync Select All checkboxes
        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const footerCheckbox = document.getElementById('selectAllFooter');
            const headerCheckbox = document.getElementById('selectAllHeader');

                checkboxes.forEach(cb => cb.checked = source.checked);

            if (headerCheckbox) headerCheckbox.checked = source.checked;
            if (footerCheckbox) footerCheckbox.checked = source.checked;

            updateSummary();
        }

        // Helper to select a single item before submitting form (for the per-row Delete button)
        function selectSingleItem(id) {
            // Uncheck all first
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = false);

            // Check only the target one
            const target = document.querySelector(`input[value="${id}"]`);
            if (target) {
                target.checked = true;
            }
            updateSummary();
            }

        function onQtyChange(el) {
            try {
                const row = el.closest('tr');
                if (!row) return;
                const unit = parseFloat(row.getAttribute('data-unit-price')) || 0;
                const qty = parseInt(el.value) || 0;
                const totalCell = row.querySelector('.item-total-price');
                if (totalCell) {
                    const newTotal = unit * qty;
                    totalCell.innerText = '₱' + newTotal.toFixed(2);
                }
                updateSummary();
            } catch (e) {
                // ignore
            }
        }

        // Proceed to Checkout - validate selection and redirect to Payment page
        function proceedToCheckout() {
            const selected = [];
            let total = 0;
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                selected.push(cb.value);
                const row = cb.closest('tr');
                const priceCell = row.querySelector('.item-total-price');
                const text = priceCell.innerText || priceCell.textContent;
                const num = parseFloat(text.replace(/[^0-9\\.]/g, '')) || 0;
                total += num;
            });

            if (selected.length === 0) {
                showModal('Please select at least one item to proceed to checkout.');
                return;
            }

            const ids = selected.join(',');
            window.location.href = 'Payment.php?from_cart=1&selected_ids=' + ids + '&amount=' + total.toFixed(2);
        }

        // Recalculate selected totals and update bottom bar display
        function updateSummary() {
            try {
                const checked = document.querySelectorAll('.item-checkbox:checked');
                let total = 0;
                let items = 0;
                checked.forEach(cb => {
                    const row = cb.closest('tr');
                    if (!row) return;
                    const priceCell = row.querySelector('.item-total-price');
                    if (priceCell) {
                        const text = priceCell.innerText || priceCell.textContent;
                        // extract digits and decimal
                        const num = parseFloat(text.replace(/[^0-9\.]/g, '')) || 0;
                        total += num;
                    }
                    const qtyInput = row.querySelector('input[name^="quantities"]');
                    const qty = qtyInput ? parseInt(qtyInput.value) || 0 : 1;
                    items += qty;
                });

                const totalPriceEl = document.querySelector('.total-price');
                const totalLabelEl = document.querySelector('.total-label');
                if (totalPriceEl) totalPriceEl.innerText = '₱' + total.toFixed(2);
                if (totalLabelEl) totalLabelEl.innerText = 'Total (' + items + ' items):';
            } catch (e) {
                // ignore
            }
        }

        // Add suggested product to cart by product id (creates form and submits)
        function addSuggestedToCart(productId, qty, btn) {
            // animate image flying to bottom-right (cart area)
            try {
                const card = btn.closest('.suggested-card');
                const img = card.querySelector('.suggested-image');
                const rect = img.getBoundingClientRect();

                const fly = img.cloneNode(true);
                fly.className = 'fly-image';
                fly.style.top = rect.top + 'px';
                fly.style.left = rect.left + 'px';
                fly.style.width = rect.width + 'px';
                fly.style.height = rect.height + 'px';
                fly.style.opacity = '1';
                document.body.appendChild(fly);

                // target: total-price element position (checkout button removed)
                let target = document.querySelector('.total-price');
                if (!target) target = document.querySelector('.cart-bottom-content');
                const tRect = target.getBoundingClientRect();

                // force reflow then animate
                requestAnimationFrame(() => {
                    fly.style.top = (tRect.top + 8) + 'px';
                    fly.style.left = (tRect.left + 8) + 'px';
                    fly.style.transform = 'scale(0.28)';
                    fly.style.opacity = '0.6';
                });

                setTimeout(() => {
                    fly.remove();
                    // small pulse on the total-price area
                    if (target) {
                        target.classList.add('pulse');
                        setTimeout(() => target.classList.remove('pulse'), 500);
                    }
                }, 650);
            } catch (e) {
                // ignore animation errors
            }

            // submit add-to-cart via POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            const inputFlag = document.createElement('input');
            inputFlag.type = 'hidden';
            inputFlag.name = 'add_to_cart';
            inputFlag.value = '1';
            form.appendChild(inputFlag);

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'product_id';
            inputId.value = String(productId);
            form.appendChild(inputId);

            const inputQty = document.createElement('input');
            inputQty.type = 'hidden';
            inputQty.name = 'quantity';
            inputQty.value = String(qty);
            form.appendChild(inputQty);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>
