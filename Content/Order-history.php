<?php
session_start();
include("../Database/config.php");

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Delete Request
if (isset($_POST['delete_order_id'])) {
    $delete_id = intval($_POST['delete_order_id']);
    $del_sql = "DELETE FROM orders WHERE id = '$delete_id' AND user_id = '$user_id'";
    if (mysqli_query($conn, $del_sql)) {
        // Success
        header("Location: Order-history.php");
        exit();
    }
}

// Check if Order Table Exists (it should, but good practice)
// Fetch Orders
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- Use CSS from shop/css but corrected path -->
    <!-- Assuming Content is sibling to Shop, so ../Sh../css/order-history.css -->
    <link rel="stylesheet" href="../css/shop/order-history.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <nav>
        <?php
        $path_prefix = '../';
        include $path_prefix . 'Components/header.php';
        ?>
    </nav>

    <div class="order-history-container">

        <div class="history-header">
            <h1>Order History</h1>
            <p>Track your past orders and their current status</p>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="orders-list">
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <?php
                    // 1. Resolve Image Path First to determine source
                    $img_file = isset($order['image_url']) ? $order['image_url'] : '';
                    $final_img_src = '';
                    $is_best_selling = false;

                    if (!empty($img_file)) {
                        // 1. Clean up paths and generate variants
                        $clean_path = str_replace('../../', '../', $img_file);
                        $basename = basename($clean_path);
                        $basename_dashed = str_replace(' ', '-', $basename);

                        $candidates = [
                            $clean_path,
                            '../image/Best-seller/' . $basename,
                            '../image/Best-seller/' . $basename_dashed,
                            '../image/best_selling/' . $basename,
                            '../image/' . $basename,
                            '../image/Shop/' . $basename,
                            '../image/Shop/UrbanWear PH/' . $basename,
                            $img_file,
                            $basename
                        ];

                        foreach ($candidates as $candidate) {
                            if (!empty($candidate) && file_exists($candidate) && !is_dir($candidate)) {
                                $final_img_src = $candidate;
                                if (strpos(strtolower($candidate), 'best-seller') !== false || strpos(strtolower($candidate), 'best_selling') !== false) {
                                    $is_best_selling = true;
                                }
                                break;
                            }
                        }

                        if (empty($final_img_src)) {
                            $check_str = strtolower($img_file);
                            $is_best_selling = (strpos($check_str, 'best') !== false);
                        }
                    }
                    ?>
                    <div class="order-card">
                        <!-- Dynamic Header -->
                        <div
                            style="display: flex; align-items: center; padding-bottom: 10px; border-bottom: 1px solid #f0f0f0; margin-bottom: 10px;">
                            <img src="../image/logo.png" alt="Logo"
                                style="width: 40px; height: 40px; object-fit: contain; margin-right: 10px;">
                            <div>
                                <?php if ($is_best_selling): ?>
                                    <h4 style="margin: 0; font-size: 15px; color: #222; font-weight: 500;">
                                        iMarket Best Selling</h4>
                                    <span style="font-size: 12px; color: #757575; display: block;">Top Rated Products</span>
                                <?php else: ?>
                                    <h4 style="margin: 0; font-size: 15px; color: #222; font-weight: 500;">UrbanWear PH</h4>
                                    <span style="font-size: 12px; color: #757575;">Streetwear & Casual Outfits</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Make the whole top part clickable to go to product page if ID exists -->
                        <?php
                        // If product_id is 0 (old orders) or 1, link to Sale.php
                        // Otherwise link to Sale-X.php
                        // Also adjust links for view-product.php
                        $pid = isset($order['product_id']) ? intval($order['product_id']) : 0;
                        if ($pid <= 0) {
                            $pid = 1;
                        }

                        // FIX LINK: linking to the known view-product.php
                        $product_link = "../Categories/best_selling/view-product.php?id=" . $pid;
                        ?>
                        <div class="order-top">
                            <div class="order-id">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            <div class="order-status"><?php echo htmlspecialchars($order['status']); ?></div>
                        </div>

                        <div style="display: flex; gap: 20px; align-items: flex-start; margin-top: 15px;">
                            <!-- Order Image -->
                            <a href="<?php echo $product_link; ?>" class="order-image"
                                style="width: 80px; height: 80px; flex-shrink: 0; background-color: #f9f9f9; border: 1px solid #eee; border-radius: 2px; overflow: hidden; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                <?php
                                if (!empty($final_img_src)) {
                                    echo '<img src="' . htmlspecialchars($final_img_src) . '" alt="Product" style="width: 100%; height: 100%; object-fit: contain;">';
                                } elseif (!empty($img_file) && filter_var($img_file, FILTER_VALIDATE_URL)) {
                                    echo '<img src="' . htmlspecialchars($img_file) . '" alt="Product" style="width: 100%; height: 100%; object-fit: contain;">';
                                } else {
                                    // Fallback icon
                                    echo '<i class="fas fa-box" style="font-size: 1.5rem; color: #ddd;"></i>';
                                }
                                ?>
                            </a>

                            <div style="flex-grow: 1;">
                                <div class="order-details-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Order Date</span>
                                        <span
                                            class="detail-value"><?php echo date("M d, Y", strtotime($order['created_at'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Total Amount</span>
                                        <span class="detail-value"
                                            style="color: #ee4d2d; font-weight: 500;">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Payment Method</span>
                                        <span
                                            class="detail-value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Payment Status</span>
                                        <span class="detail-value"
                                            style="text-transform:uppercase; font-weight: 500; color: #28a745;"><?php echo htmlspecialchars($order['status'] == 'Shipped' || $order['status'] == 'Delivered' ? 'PAID' : 'PENDING'); ?></span>
                                    </div>
                                </div>

                                <div class="track-btn-container">
                                    <a href="Tracking.php?order_id=<?php echo $order['id']; ?>" class="btn-track primary"><i
                                            class="fas fa-shipping-fast"></i> Track Order</a>

                                    <a href="Rate.php?order_id=<?php echo $order['id']; ?>&product_id=<?php echo $pid; ?>"
                                        class="btn-track" style="background:#fff; color:#ee4d2d; border:1px solid #ee4d2d;"><i
                                            class="fas fa-star"></i> Rate</a>

                                    <button type="button" class="btn-track"
                                        onclick="openDeleteModal(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
                <h2>No orders yet</h2>
                <p>Looks like you haven't placed any orders yet.</p>
                <a href="../Shop/index.php"
                    style="display:inline-block; margin-top:1rem; color:white; background-color:#2A3B7E; font-weight:600; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Start
                    Shopping</a>
            </div>
        <?php endif; ?>

    </div>

    <!-- Custom Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Order</h3>
                <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this order history?</p>
                <p style="font-size: 0.9em; color: #666;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="delete_order_id" id="modal_delete_id" value="">
                    <button type="submit" class="btn-confirm-delete">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.2s ease-out;
            font-family: 'Roboto', sans-serif;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.2rem;
        }

        .close-modal {
            font-size: 24px;
            cursor: pointer;
            color: #888;
            font-weight: 500;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-body p {
            margin: 5px 0;
            color: #444;
        }

        .modal-footer {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel {
            padding: 8px 16px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            color: #444;
            font-family: inherit;
        }

        .btn-cancel:hover {
            background-color: #e2e6ea;
        }

        .btn-confirm-delete {
            padding: 8px 16px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-confirm-delete:hover {
            background-color: #c82333;
        }

        /* Ensure buttons are "pantay" (equal alignment) */
        .track-btn-container .btn-track {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            /* Fixed height for consistency */
            padding: 0 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            white-space: nowrap;
        }
    </style>

    <script>
        function openDeleteModal(orderId) {
            document.getElementById('modal_delete_id').value = orderId;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal if clicking outside
        window.onclick = function (event) {
            var modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <footer>
        <?php include $path_prefix . 'Components/footer.php'; ?>
    </footer>

</body>

</html>
