<?php
session_start();
include("../Database/config.php");

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$msg = "";

// 2. Handle Actions (Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete Single Item
    if (isset($_POST['remove_item_id'])) {
        $remove_id = intval($_POST['remove_item_id']);
        $del_sql = "DELETE FROM cart WHERE id = '$remove_id' AND user_id = '$user_id'";
        if (mysqli_query($conn, $del_sql)) {
            $msg = "<div class='alert-success'>Item removed from cart.</div>";
        }
    }
    // Add Selected to Cart (replace checkout with add-to-cart)
    if (isset($_POST['add_selected_to_cart']) && !empty($_POST['selected_items'])) {
        $selected_cart_ids = array_map('intval', $_POST['selected_items']);
        foreach ($selected_cart_ids as $cid) {
            // fetch the cart row (ensure it's a valid row)
            $cres = mysqli_query($conn, "SELECT * FROM cart WHERE id='$cid' AND user_id='$user_id' LIMIT 1");
            if (!$cres || mysqli_num_rows($cres) == 0) continue;
            $crow = mysqli_fetch_assoc($cres);
            $pid = intval($crow['product_id']);
            $pq = intval($crow['quantity']);

            // check if another cart row already exists for this user and product
            $exist = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id='$user_id' AND product_id='$pid' LIMIT 1");
            if ($exist && mysqli_num_rows($exist) > 0) {
                $erow = mysqli_fetch_assoc($exist);
                // if the existing row is the same row we selected, skip
                if (intval($erow['id']) === intval($cid)) continue;
                $newq = intval($erow['quantity']) + $pq;
                mysqli_query($conn, "UPDATE cart SET quantity='$newq' WHERE id='" . intval($erow['id']) . "'");
            } else {
                // insert a new cart row using values from selected row
                $pname = mysqli_real_escape_string($conn, $crow['product_name']);
                $pprice = floatval($crow['price']);
                $pimg = mysqli_real_escape_string($conn, $crow['image']);
                $pshop = mysqli_real_escape_string($conn, $crow['shop_name']);
                mysqli_query($conn, "INSERT INTO cart (user_id, product_id, product_name, price, quantity, image, shop_name) VALUES ('$user_id', '$pid', '" . $pname . "', '$pprice', '$pq', '" . $pimg . "', '" . $pshop . "')");
            }
        }
        $msg = "<div class='alert-success'>Selected items added to cart.</div>";
    }
    // Update Quantity would go here
}

// 3. Fetch Cart
$sql = "SELECT * FROM cart WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$total_price = 0;
$total_count = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
        // Calculation handled by JS mostly for dynamic selection, but initial here:
        $total_price += ($row['price'] * $row['quantity']);
        $total_count += $row['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- External CSS - We should unify this, but for now using the request's style source -->
    <link rel="stylesheet" href="../css/best_selling/checkout_cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- User requested Montserrat font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif !important;
            background-color: #f5f7f9;
        }

        /* Alert Styles */
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
        }

        /* Ensure container fits Content/ structure */
        .checkout-page-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            padding-bottom: 100px;
        }

        /* Nav adjustment */
        nav {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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

    <div class="checkout-page-container">

        <h2 class="page-title" style="margin-bottom: 20px; font-weight: 600; color: #333;">Shopping Cart</h2>
        <?php echo $msg; ?>

        <?php if (count($cart_items) > 0): ?>

            <!-- Select All Header -->
            <div class="cart-card select-all-header"
                style="background: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <input type="checkbox" id="selectAllItems" onclick="toggleAll(this)" checked
                    style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                <label for="selectAllItems" style="font-weight: 500; cursor: pointer;">Select All Items</label>
            </div>

            <form action="" method="POST" id="cartForm">
                <!-- Items List -->
                <?php foreach ($cart_items as $item):
                    // Determine shop/Brand Display
                    $is_best_selling = false;
                    $img_check = strtolower($item['image']);
                    $bs_keywords = ['bag-women', 'bag-men', 'notebooks', 'earphone', 'shoes', 'watch', 'best_selling'];
                    foreach ($bs_keywords as $kw) {
                        if (strpos($img_check, $kw) !== false) {
                            $is_best_selling = true;
                            break;
                        }
                    }
                    ?>
                    <div class="cart-card cart-item-row"
                        style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: flex-start; gap: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <!-- Checkbox -->
                        <div class="item-checkbox-container" style="padding-top: 35px;">
                            <!-- Align with image center somewhat -->
                            <input type="checkbox" name="selected_items[]" class="item-checkbox"
                                value="<?php echo $item['id']; ?>" checked onchange="updateSummary()"
                                style="width: 18px; height: 18px; cursor: pointer;">
                        </div>

                        <!-- Image -->
                        <?php
                        // Image resolution
                        $display_img = $item['image'];

                        // Basic fix for malformed paths stored in DB like "Product/Categories/..."
                        $clean_img_name = basename($display_img);

                        // Define potential directories relative to Content/Check-out.php
                        $possible_paths = [
                            '../image/Best-seller/' . $clean_img_name, // Corrected Best-seller path
                            '../image/New-arrivals/' . $clean_img_name, // New Arrivals
                            '../image/electronics/' . $clean_img_name,
                            '../image/Fashion & Apparel/' . $clean_img_name,
                            '../image/Beauty & Health/' . $clean_img_name,
                            '../image/Home & Living/' . $clean_img_name,
                            '../image/Sports & Outdoor/' . $clean_img_name,
                            '../image/Toys & Games/' . $clean_img_name,
                            '../image/groceries/' . $clean_img_name,
                            // Specific Shop Directories in image/Shop
                            '../image/Shop/CozyLiving Store/' . $clean_img_name,
                            '../image/Shop/DailyFits Co/' . $clean_img_name,
                            '../image/Shop/FreshLook PH/' . $clean_img_name,
                            '../image/Shop/GadgetLab PH/' . $clean_img_name,
                            '../image/Shop/GlowUp Beauty/' . $clean_img_name,
                            '../image/Shop/HomeEssentials PH/' . $clean_img_name,
                            '../image/Shop/Luxe Basics/' . $clean_img_name,
                            '../image/Shop/SmartGear Store/' . $clean_img_name,
                            '../image/Shop/StyleHub Manila/' . $clean_img_name,
                            '../image/Shop/TechZone PH/' . $clean_img_name,
                            '../image/Shop/TrendyBags PH/' . $clean_img_name,
                            '../image/Shop/UrbanWear PH/' . $clean_img_name,
                            // Legacy/Generic
                            '../image/Best/' . $clean_img_name,
                            '../image/Shop/' . $clean_img_name,
                            '../image/' . $clean_img_name,
                            '../Categories/best_selling/image/' . $clean_img_name,
                            '../shop/image/' . $clean_img_name,
                            '../shop/image/UrbanWear PH/' . $clean_img_name, // Legacy specific
                            '../shop/image/' . $clean_img_name,
                            $display_img
                        ];

                        $final_img = '../image/imarket.png'; // Default fallback
                
                        foreach ($possible_paths as $path) {
                            if (file_exists($path)) {
                                $final_img = $path;
                                break;
                            }
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($final_img); ?>" alt="Product" class="item-image"
                            style="width: 100px; height: 100px; object-fit: contain; border-radius: 6px; border: 1px solid #eee;">

                        <!-- Details -->
                        <div class="item-details" style="flex: 1;">
                            <div class="item-name" style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </div>

                            <!-- Shop Subtitle -->
                            <div style="margin-bottom: 10px;">
                                <?php if ($is_best_selling): ?>
                                    <span style="font-size: 13px; color: #2A3B7E; font-weight: 600;"><i
                                            class="fas fa-certificate"></i> IMarket Best Selling</span>
                                <?php else: ?>
                                    <?php $shopName = !empty($item['shop_name']) ? $item['shop_name'] : "UrbanWear PH"; ?>
                                    <span style="font-size: 13px; color: #777;"><i class="fas fa-store"></i>
                                        <?php echo htmlspecialchars($shopName); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="item-price"
                                style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 15px;">
                                ₱<?php echo number_format($item['price'], 2); ?></div>

                            <div class="item-controls" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                                <!-- Quantity -->
                                <div class="qty-stepper"
                                    style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="qty-btn"
                                        style="width: 30px; height: 30px; background: #fff; border: none; cursor: pointer; color: #555;"
                                        onclick="updateQty(<?php echo $item['id']; ?>, -1)">-</button>
                                    <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly
                                        id="qty-<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>"
                                        style="width: 40px; text-align: center; border: none; border-left: 1px solid #ddd; border-right: 1px solid #ddd; height: 30px; outline: none;">
                                    <button type="button" class="qty-btn"
                                        style="width: 30px; height: 30px; background: #fff; border: none; cursor: pointer; color: #555;"
                                        onclick="updateQty(<?php echo $item['id']; ?>, 1)">+</button>
                                </div>

                                <!-- Buttons -->
                                <a href="#" class="action-btn btn-wishlist"
                                    style="color: #fff; background: #ff4081; padding: 6px 12px; border-radius: 4px; font-size: 13px; text-decoration: none;"><i
                                        class="fas fa-heart"></i> Wishlist</a>
                                <a href="#" class="action-btn btn-save-later"
                                    style="color: #fff; background: #6c757d; padding: 6px 12px; border-radius: 4px; font-size: 13px; text-decoration: none;"><i
                                        class="fas fa-bookmark"></i> Save for Later</a>

                                <!-- Remove Button -->
                                <button type="button" class="action-btn btn-remove"
                                    onclick="removeItem(<?php echo $item['id']; ?>)"
                                    style="color: #fff; background: #dc3545; border: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; cursor: pointer;">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="item-note-container" style="width: 250px;">
                            <textarea class="note-input" placeholder="Add a note (e.g. 'no box')"
                                style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; resize: none; font-family: inherit; font-size: 13px;"></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>

                <input type="hidden" name="remove_item_id" id="remove_item_id_input">
                <input type="hidden" name="add_selected_to_cart" id="add_selected_to_cart_input" value="0">
            </form>

        <?php else: ?>
            <div class="cart-card empty-cart-message"
                style="background: white; padding: 50px; text-align: center; border-radius: 8px;">
                <i class="fas fa-shopping-cart" style="font-size:3rem; margin-bottom:1rem; color:#ddd;"></i>
                <h3 style="color: #333;">Your cart is empty</h3>
                <p style="color: #777;">Looks like you haven't added any items to your cart yet.</p>
                <a href="../Categories/best_selling/index.php"
                    style="color:#2A3B7E; text-decoration:none; font-weight:600; margin-top: 10px; display: inline-block;">Start
                    Shopping</a>
            </div>
        <?php endif; ?>

    </div>

    <!-- Fixed Footer Bar -->
    <?php if (count($cart_items) > 0): ?>
        <div class="checkout-footer-bar"
            style="position: fixed; bottom: 0; left: 0; width: 100%; background: #fff; padding: 15px 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); z-index: 100;">
            <div class="footer-content"
                style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
                <div class="footer-left">
                    <div class="selected-count" style="font-weight: 600; color: #333;">Selected Items (<span
                            id="selectedCountDisplay"><?php echo count($cart_items); ?></span>)</div>
                    <div class="total-items-label" style="font-size: 13px; color: #777;">Total Items in Cart:
                        <?php echo $total_count; ?>
                    </div>
                </div>
                <div class="footer-right" style="display: flex; align-items: center; gap: 20px;">
                    <div class="footer-total-price" style="font-size: 20px; font-weight: 700; color: #333;">₱<span
                            id="grandTotalDisplay"><?php echo number_format($total_price, 2); ?></span></div>

                    <button type="button" class="btn-proceed-checkout" onclick="addSelectedToCart(); return false;"
                        style="background: #2A3B7E; color: white; padding: 12px 30px; border: none; border-radius: 4px; font-weight: 600; display: flex; align-items: center; cursor: pointer;">
                        <i class="fas fa-shopping-cart" style="margin-right:8px; font-size:0.9em;"></i> Add Selected to Cart (<span
                            id="checkoutCountDisplay"><?php echo count($cart_items); ?></span>)
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function updateQty(id, change) {
            const input = document.getElementById('qty-' + id);
            let val = parseInt(input.value);
            val += change;
            if (val < 1) val = 1;
            input.value = val;
            updateSummary();
        }

        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateSummary();
        }

        function addSelectedToCart() {
            // ensure at least one selected
            const selected = document.querySelectorAll('.item-checkbox:checked');
            if (!selected || selected.length === 0) {
                alert('Please select at least one item to add to cart.');
                return;
            }
            document.getElementById('add_selected_to_cart_input').value = '1';
            document.getElementById('cartForm').submit();
        }

        function updateSummary() {
            let count = 0;
            let total = 0;

            document.querySelectorAll('.item-checkbox').forEach(cb => {
                if (cb.checked) {
                    count++;
                    const id = cb.value;
                    const qtyInput = document.getElementById('qty-' + id);
                    const price = parseFloat(qtyInput.getAttribute('data-price'));
                    const qty = parseInt(qtyInput.value);
                    total += (price * qty);
                }
            });

            document.getElementById('selectedCountDisplay').innerText = count;
            document.getElementById('checkoutCountDisplay').innerText = count;

            // Format Currency
            document.getElementById('grandTotalDisplay').innerText = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function removeItem(id) {
            if (confirm('Are you sure you want to remove this item?')) {
                document.getElementById('remove_item_id_input').value = id;
                document.getElementById('cartForm').submit();
            }
        }

        function proceedToCheckout() {
            const selected = [];
            let total = 0;
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                selected.push(cb.value);
                const id = cb.value;
                const qtyInput = document.getElementById('qty-' + id);
                const price = parseFloat(qtyInput.getAttribute('data-price'));
                const qty = parseInt(qtyInput.value);
                total += (price * qty);
            });

            if (selected.length === 0) {
                alert('Please select at least one item to checkout.');
                return;
            }

            const ids = selected.join(',');
            // Redirect to the UNIFIED PAYMENT PAGE with total amount
            window.location.href = 'Payment.php?from_cart=1&selected_ids=' + ids + '&amount=' + total;
        }
    </script>

    <div style="margin-top: 50px;">
        <?php
        $path_prefix = '../';
        include '../Components/footer.php';
        ?>
    </div>
</body>

</html>
