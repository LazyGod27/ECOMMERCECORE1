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
    $p_name = mysqli_real_escape_string($conn, isset($_GET['product_name']) ? $_GET['product_name'] : $_POST['product_name']);
    $p_price = floatval(isset($_GET['price']) ? $_GET['price'] : $_POST['price']);
    $p_qty = intval(isset($_GET['quantity']) ? $_GET['quantity'] : $_POST['quantity']);
    $p_image = mysqli_real_escape_string($conn, isset($_GET['image']) ? $_GET['image'] : $_POST['image']);
    $shop_name = isset($_GET['store']) ? mysqli_real_escape_string($conn, $_GET['store']) : (isset($_POST['store']) ? mysqli_real_escape_string($conn, $_POST['store']) : '');

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
        $insert_sql = "INSERT INTO cart (user_id, product_id, product_name, price, quantity, image, shop_name) VALUES ('$user_id', '$p_id', '$p_name', '$p_price', '$p_qty', '$p_image', '$shop_name')";
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
        // Logic to update quantities could go here
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
        body {
            font-family: 'Montserrat', sans-serif !important;
        }

        /* Shared Styles (can be moved to a css file later) */
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .cart-main-container {
            min-height: 400px;
            padding-bottom: 100px;
            /* Space for bottom bar */
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
            background-color: rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.3s;
        }

        .custom-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px 25px;
            border: 1px solid #888;
            width: 90%;
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: slideIn 0.3s;
        }

        .close-modal {
            color: #aaa;
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }

        #modalMessage {
            margin: 20px 0;
            font-size: 16px;
            color: #333;
        }

        .btn-ok {
            background-color: #2A3B7E;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            transition: background-color 0.2s;
        }

        .btn-ok:hover {
            background-color: #1a2657;
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
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Unified Cart Branding Overrides */
        .cart-header-bar {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            margin-bottom: 20px;
        }

        .cart-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .cart-branding {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #2A3B7E;
            gap: 10px;
        }

        .cart-logo-link img {
            height: 40px;
            width: auto;
        }

        .cart-logo-text {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .cart-divider {
            height: 30px;
            width: 1px;
            background-color: #ddd;
        }

        .cart-page-title {
            font-size: 20px;
            color: #333;
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
                    <img src="../image/Logo/logo.png" alt="Imarket Logo">
                    <span class="cart-logo-text">IMARKET</span>
                </a>
                <div class="cart-divider"></div>
                <span class="cart-page-title">Shopping Cart</span>
            </div>

            <div class="cart-search-container" style="flex: 1; max-width: 500px; margin-left: 50px;">
                <div style="display: flex; border: 2px solid #2A3B7E; border-radius: 4px; overflow: hidden;">
                    <input type="text"
                        style="flex: 1; border: none; padding: 10px; outline: none; font-family: inherit;"
                        placeholder="Search for products, brands and shops">
                    <button style="background: #2A3B7E; border: none; padding: 0 20px; color: white; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Wrapped in Form -->
    <form action="" method="POST" id="cartForm">
        <div class="cart-main-container">

            <?php echo $msg; ?>

            <!-- Table Header -->
            <table class="cart-table" <?php if (count($cart_items) === 0)
                echo 'style="display:none;" width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin-top: 20px;"';
            else
                echo 'width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); background: white;"'; ?>>
                <thead>
                    <tr style="background: #fff; text-align: left; border-bottom: 1px solid #eee; color: #888;">
                        <th style="width: 50px; text-align: center;">
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

                            <tr style="background: #fff; border-bottom: 1px solid #eee;">
                                <td style="text-align: center;">
                                    <input type="checkbox" name="selected_items[]" class="item-checkbox"
                                        value="<?php echo $item['id']; ?>" onclick="updateSummary()">
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
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
                                            style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #eee; border-radius: 4px;">

                                        <div style="display:flex; flex-direction:column;">
                                            <span
                                                style="font-weight: 500; font-size: 16px; margin-bottom: 5px; color: #333;"><?php echo htmlspecialchars($item['product_name']); ?></span>

                                            <!-- shop/Branding Subtitle -->
                                            <?php if ($is_best_selling_item): ?>
                                                <span style="font-size: 13px; color: #2A3B7E; font-weight: 600;"><i
                                                        class="fas fa-certificate" style="margin-right:4px;"></i> IMarket Best
                                                    Selling</span>
                                            <?php else: ?>
                                                <?php $disp_shop = !empty($item['shop_name']) ? $item['shop_name'] : "UrbanWear PH"; ?>
                                                <span style="font-size: 13px; color: #777;"><i class="fas fa-store"
                                                        style="margin-right:4px;"></i>
                                                    <?php echo htmlspecialchars($disp_shop); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <!-- Simple quantity display for now -->
                                    <?php echo $item['quantity']; ?>
                                </td>
                                <td class="item-total-price" style="color: #2A3B7E; font-weight: bold;">
                                    ₱<?php echo number_format($item_total, 2); ?></td>
                                <td>
                                    <button type="submit" name="delete_selected"
                                        onclick="selectSingleItem(<?php echo $item['id']; ?>)"
                                        style="color: #ff4d4f; background: none; border: none; cursor: pointer; font-weight: 500;">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Empty Cart State -->
            <?php if (count($cart_items) === 0): ?>
                <div class="empty-cart-section"
                    style="text-align: center; padding: 50px; background: #fff; margin-top: 20px;">
                    <div class="empty-cart-icon" style="font-size: 50px; color: #ddd; margin-bottom: 20px;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="empty-cart-title" style="font-size: 18px; color: #333; margin-bottom: 10px;">Your cart is
                        empty</div>
                    <div class="empty-cart-text" style="color: #888; margin-bottom: 20px;">Looks like you haven't added any
                        items to your cart yet.</div>
                    <a href="../Shop/index.php" class="btn-continue-shopping"
                        style="display: inline-block; padding: 10px 30px; background: #2A3B7E; color: #fff; text-decoration: none; border-radius: 4px;">Continue
                        Shopping</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bottom Actions Bar -->
        <div class="cart-bottom-bar" <?php if (count($cart_items) === 0)
            echo 'style="display:none;"'; ?>
            style="position: fixed; bottom: 0; left: 0; width: 100%; background: #fff; border-top: 1px solid #eee; padding: 15px 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 100;">
            <div class="cart-bottom-content"
                style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
                <div class="bottom-left-actions" style="display: flex; gap: 20px; align-items: center;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" class="select-all-checkbox" id="selectAllFooter"
                            onclick="toggleSelectAll(this)" style="margin-right: 8px;">
                        Select All (<?php echo count($cart_items); ?>)
                    </label>
                    <button type="submit" name="delete_selected" class="action-btn"
                        style="background: none; border: none; cursor: pointer; color: #333;">Delete</button>
                </div>
                <div class="bottom-right-actions" style="display: flex; gap: 20px; align-items: center;">
                    <div class="total-label">Total (<?php echo $total_items; ?> items):</div>
                    <div class="total-price" style="font-size: 20px; color: #2A3B7E; font-weight: bold;">
                        ₱<?php echo number_format($total_price, 2); ?></div>
                    <button type="button" class="btn-checkout" onclick="proceedToCheckout()"
                        style="background: #2A3B7E; color: #fff; border: none; padding: 12px 40px; border-radius: 4px; font-weight: bold; cursor: pointer;">Check
                        Out</button>
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
        }

        function proceedToCheckout() {
            const selected = [];
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                selected.push(cb.value);
            });

            if (selected.length === 0) {
                showModal('Please select at least one item to checkout.');
                return;
            }

            const ids = selected.join(',');
            // Redirect to the UNIFIED PAYMENT page
            window.location.href = 'Payment.php?from_cart=1&selected_ids=' + ids;
        }
    </script>
</body>

</html>
