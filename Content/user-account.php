<?php
session_start();
include("../Database/config.php");

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$view = isset($_GET['view']) ? $_GET['view'] : 'profile'; // profile, orders, tracking

// ---------------------------------------------------------
// SELF-HEALING DB: Ensure 'users' table has profile columns
// ---------------------------------------------------------
if (isset($conn)) {
    // Check for 'profile_pic' column
    $pic_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'profile_pic'");
    if (mysqli_num_rows($pic_check) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL");
    }

    // Check for 'address' column as a proxy for the update
    $cols_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'address'");
    if (mysqli_num_rows($cols_check) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN fullname VARCHAR(255) AFTER email");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN phone VARCHAR(50) AFTER fullname");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN address TEXT AFTER phone");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN city VARCHAR(100) AFTER address");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN zip VARCHAR(20) AFTER city");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN gender VARCHAR(20) DEFAULT 'Not Specified'");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN birthdate DATE NULL");
    }

    // Check for 'gender' specifically if added later
    $gender_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'gender'");
    if (mysqli_num_rows($gender_check) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN gender VARCHAR(20) DEFAULT 'Not Specified'");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN birthdate DATE NULL");
    }

    // Ensure 'user_addresses' table exists
    $create_addr_table = "CREATE TABLE IF NOT EXISTS user_addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        fullname VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address TEXT NOT NULL,
        city VARCHAR(100) NOT NULL,
        zip VARCHAR(20) NOT NULL,
        is_default TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $create_addr_table);
}

// ---------------------------------------------------------
// HANDLE ADDRESS ACTIONS
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'save_address') {
        $addr_id = isset($_POST['address_id']) ? intval($_POST['address_id']) : 0;
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $zip = mysqli_real_escape_string($conn, $_POST['zip']);
        $is_default = isset($_POST['is_default']) ? 1 : 0;

        if ($is_default) {
            mysqli_query($conn, "UPDATE user_addresses SET is_default = 0 WHERE user_id = '$user_id'");
        }

        if ($addr_id > 0) {
            // Update
            $sql = "UPDATE user_addresses SET fullname='$fullname', phone='$phone', address='$address', city='$city', zip='$zip', is_default='$is_default' WHERE id='$addr_id' AND user_id='$user_id'";
        } else {
            // Insert
            $sql = "INSERT INTO user_addresses (user_id, fullname, phone, address, city, zip, is_default) VALUES ('$user_id', '$fullname', '$phone', '$address', '$city', '$zip', '$is_default')";
        }

        if (mysqli_query($conn, $sql)) {
            $msg = "<div class='alert-success'>Address saved successfully!</div>";
        } else {
            $msg = "<div class='alert-error'>Error saving address: " . mysqli_error($conn) . "</div>";
        }
    }

    if ($action == 'delete_address') {
        $addr_id = intval($_POST['address_id']);
        if (mysqli_query($conn, "DELETE FROM user_addresses WHERE id='$addr_id' AND user_id='$user_id'")) {
            $msg = "<div class='alert-success'>Address deleted successfully!</div>";
        }
    }

    if ($action == 'set_default') {
        $addr_id = intval($_POST['address_id']);
        mysqli_query($conn, "UPDATE user_addresses SET is_default = 0 WHERE user_id = '$user_id'");
        mysqli_query($conn, "UPDATE user_addresses SET is_default = 1 WHERE id = '$addr_id' AND user_id = '$user_id'");
        $msg = "<div class='alert-success'>Default address updated!</div>";
    }
}

// ---------------------------------------------------------
// HANDLE FORM SUBMISSIONS (Profile Update)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    // Optional fields
    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
    $birthdate = isset($_POST['birthdate']) ? mysqli_real_escape_string($conn, $_POST['birthdate']) : NULL;
    $bd_sql_part = $birthdate ? ", birthdate='$birthdate'" : "";

    $update_sql = "UPDATE users SET fullname='$fullname', phone='$phone', address='$address', city='$city', zip='$zip', gender='$gender' $bd_sql_part WHERE id='$user_id'";

    if (mysqli_query($conn, $update_sql)) {
        // Handle Profile Picture Upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $target_dir = "../uploads/profile/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $new_filename = "user_" . $user_id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                mysqli_query($conn, "UPDATE users SET profile_pic='$new_filename' WHERE id='$user_id'");
            }
        }
        $msg = "<div class='alert-success'><i class='fas fa-check-circle'></i> Profile updated successfully!</div>";
    } else {
        $msg = "<div class='alert-error'>Error updating profile: " . mysqli_error($conn) . "</div>";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Verify current password
    $verify_sql = "SELECT password FROM users WHERE id='$user_id'";
    $verify_res = mysqli_query($conn, $verify_sql);
    $user_data = mysqli_fetch_assoc($verify_res);

    if (password_verify($current_pass, $user_data['password'])) {
        if ($new_pass === $confirm_pass) {
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_pass_sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";
            if (mysqli_query($conn, $update_pass_sql)) {
                $msg = "<div class='alert-success'>Password updated successfully!</div>";
            } else {
                $msg = "<div class='alert-error'>Error updating password.</div>";
            }
        } else {
            $msg = "<div class='alert-error'>New passwords do not match.</div>";
        }
    } else {
        $msg = "<div class='alert-error'>Incorrect current password.</div>";
    }
}

// Handle Order Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel_order') {
    $order_id_to_cancel = intval($_POST['order_id']);
    $cancel_sql = "UPDATE orders SET status='Cancelled' WHERE id='$order_id_to_cancel' AND user_id='$user_id'";
    if (mysqli_query($conn, $cancel_sql)) {
        $msg = "<div class='alert-success'>Order cancelled successfully.</div>";
    } else {
        $msg = "<div class='alert-error'>Error cancelling order.</div>";
    }
}

// ---------------------------------------------------------
// FETCH USER DATA
// ---------------------------------------------------------
$sql = "SELECT * FROM users WHERE id='$user_id'";
$res = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($res);

// ---------------------------------------------------------
// FETCH ADDRESSES
// ---------------------------------------------------------
$user_addresses = [];
if ($view == 'address') {
    $addr_res = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id='$user_id' ORDER BY is_default DESC, id DESC");
    while ($row = mysqli_fetch_assoc($addr_res)) {
        $user_addresses[] = $row;
    }
}

// ---------------------------------------------------------
// FETCH ORDERS (If view is orders or tracking)
// ---------------------------------------------------------
$my_orders = [];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'All';
if ($view == 'orders' || $view == 'tracking') {
    $check_orders = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
    if (mysqli_num_rows($check_orders) > 0) {
        $filter_sql = "";
        if ($tab == 'To Pay') $filter_sql = " AND status='Pending'";
        if ($tab == 'To Ship') $filter_sql = " AND status='Paid'";
        if ($tab == 'To Receive') $filter_sql = " AND status='Shipped'";
        if ($tab == 'Completed') $filter_sql = " AND status IN ('Delivered', 'Completed')";
        if ($tab == 'Cancelled') $filter_sql = " AND status='Cancelled'";

        $order_sql = "SELECT * FROM orders WHERE user_id='$user_id' $filter_sql ORDER BY created_at DESC";
        $order_res = mysqli_query($conn, $order_sql);
        while ($r = mysqli_fetch_assoc($order_res)) {
            $my_orders[] = $r;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | IMARKET PH</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/components/user-account.css?v=<?php echo time(); ?>">

    <style>
        /* Inline overrides if needed */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <nav>
        <?php
        $path_prefix = '../';
        include('../Components/header.php');
        ?>
    </nav>

    <div class="user-account-wrapper">

        <!-- SIDEBAR -->
        <aside class="account-sidebar">
            <div class="sidebar-profile">
                <div class="sidebar-avatar" style="background-image: url('<?php echo !empty($user['profile_pic']) ? '../uploads/profile/'.$user['profile_pic'] : '../image/logo.png'; ?>'); background-size: cover; background-position: center;">
                    <?php if(empty($user['profile_pic'])): ?><i class="far fa-user"></i><?php endif; ?>
                </div>
                <div>
                    <div class="sidebar-username">
                        <?php echo htmlspecialchars(!empty($user['username']) ? $user['username'] : (!empty($user['fullname']) ? $user['fullname'] : $user['email'])); ?>
                    </div>
                    <a href="?view=profile" class="sidebar-edit-link"><i class="fas fa-pen"></i> Edit Profile</a>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="?view=profile" class="sidebar-menu-title">
                        <i class="fas fa-user"></i> My Account
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="?view=profile"
                                class="<?php echo $view == 'profile' ? 'active' : ''; ?>">Profile</a></li>
                        <li><a href="?view=banks" class="<?php echo $view == 'banks' ? 'active' : ''; ?>">Banks &
                                Cards</a></li>
                        <li><a href="?view=address"
                                class="<?php echo $view == 'address' ? 'active' : ''; ?>">Addresses</a></li>
                        <li><a href="?view=password" class="<?php echo $view == 'password' ? 'active' : ''; ?>">Change
                                Password</a></li>
                    </ul>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?view=orders" class="sidebar-menu-title">
                        <i class="fas fa-clipboard-list"></i> My Purchase
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-title">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </li>
            </ul>

            <!-- SELLER CENTRE BUTTON (Integration Point) -->
            <!-- Assuming Seller/Login.php or similar exists, or just a placeholder for Team 2 -->
            <a href="../Seller/index.php" class="seller-btn">
                <i class="fas fa-store"></i> Seller Centre
            </a>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="account-content">

            <!-- VIEW: PROFILE -->
            <?php if ($view == 'profile'): ?>
                <div class="content-header">
                    <div class="content-title">My Profile</div>
                    <div class="content-subtitle">Manage and protect your account</div>
                </div>

                <?php echo $msg; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">

                    <div style="display: flex;">
                        <div style="flex: 1; padding-right: 40px;">

                            <div class="profile-input-group">
                                <div class="profile-input-label">Username</div>
                                <div class="profile-input-field" style="padding-top:10px;">
                                    <?php echo htmlspecialchars(!empty($user['username']) ? $user['username'] : (!empty($user['fullname']) ? $user['fullname'] : $user['email'])); ?>
                                </div>
                            </div>

                            <div class="profile-input-group">
                                <div class="profile-input-label">Name</div>
                                <div class="profile-input-field">
                                    <input type="text" name="fullname"
                                        value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="profile-input-group">
                                <div class="profile-input-label">Phone Number</div>
                                <div class="profile-input-field">
                                    <input type="text" name="phone"
                                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="profile-input-group">
                                <div class="profile-input-label">Address</div>
                                <div class="profile-input-field">
                                    <textarea name="address"
                                        rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="profile-input-group">
                                <div class="profile-input-label">City</div>
                                <div class="profile-input-field">
                                    <input type="text" name="city"
                                        value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="profile-input-group">
                                <div class="profile-input-label">Postal Code</div>
                                <div class="profile-input-field">
                                    <input type="text" name="zip"
                                        value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="profile-input-group">
                                <div class="profile-input-label">Gender</div>
                                <div class="profile-input-field"> <!-- Simple Radio or Select -->
                                    <?php $g = $user['gender'] ?? ''; ?>
                                    <select name="gender">
                                        <option value="" disabled <?php echo empty($g) ? 'selected' : ''; ?>>Select Gender
                                        </option>
                                        <option value="Male" <?php echo ($g == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($g == 'Female') ? 'selected' : ''; ?>>Female
                                        </option>
                                        <option value="Other" <?php echo ($g == 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="save-btn-container">
                                <button type="submit" class="btn-primary">Save</button>
                            </div>

                        </div>

                        <!-- Avatar Section -->
                        <div
                            style="width: 280px; border-left: 1px solid #efefef; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-left: 40px;">
                            <div id="avatarPreview"
                                style="width: 100px; height: 100px; background: #eee; border-radius: 50%; margin-bottom: 20px; display:flex; align-items:center; justify-content:center; color:#ccc; font-size:40px; overflow:hidden; border:1px solid #ddd; background-image: url('<?php echo !empty($user['profile_pic']) ? '../uploads/profile/'.$user['profile_pic'] : ''; ?>'); background-size: cover; background-position: center;">
                                <?php if(empty($user['profile_pic'])): ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="profile_pic" id="profile_pic_input" style="display:none;" onchange="previewImage(this)">
                            <button type="button" class="btn-outline" onclick="document.getElementById('profile_pic_input').click()">Select Image</button>
                            <div style="margin-top: 15px; font-size: 12px; color: #999; text-align: center;">
                                File size: maximum 1 MB<br>
                                File extension: .JPEG, .PNG
                            </div>
                        </div>

                        <script>
                        function previewImage(input) {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    const preview = document.getElementById('avatarPreview');
                                    preview.style.backgroundImage = 'url(' + e.target.result + ')';
                                    preview.innerHTML = '';
                                }
                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                        </script>
                    </div>
                </form>

                <!-- VIEW: ORDERS -->
            <?php elseif ($view == 'orders'): ?>

                <div class="order-tabs">
                    <a href="?view=orders&tab=All" class="order-tab <?php echo $tab == 'All' ? 'active' : ''; ?>">All</a>
                    <a href="?view=orders&tab=To Pay" class="order-tab <?php echo $tab == 'To Pay' ? 'active' : ''; ?>">To Pay</a>
                    <a href="?view=orders&tab=To Ship" class="order-tab <?php echo $tab == 'To Ship' ? 'active' : ''; ?>">To Ship</a>
                    <a href="?view=orders&tab=To Receive" class="order-tab <?php echo $tab == 'To Receive' ? 'active' : ''; ?>">To Receive</a>
                    <a href="?view=orders&tab=Completed" class="order-tab <?php echo $tab == 'Completed' ? 'active' : ''; ?>">Completed</a>
                    <a href="?view=orders&tab=Cancelled" class="order-tab <?php echo $tab == 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
                </div>

                <!-- Search Bar for Orders -->
                <div style="background:#eaeaea; padding:10px; margin-bottom:20px; border-radius:2px;">
                    <i class="fas fa-search" style="color:#666; margin-left:10px;"></i>
                    <input type="text" placeholder="Search orders by Order ID or Product Name"
                        style="background:transparent; border:none; outline:none; padding:5px; width:90%;">
                </div>

                <?php if (empty($my_orders)): ?>
                    <div class="empty-state">
                        <img src="../image/no-orders.png" alt="" style="width:100px; opacity:0.5; margin-bottom:15px;">
                        <div>No orders yet</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($my_orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="shop-name">
                                    <i class="fas fa-store"></i> IMarket Shop <button class="btn-outline"
                                        style="padding:2px 8px; font-size:11px; margin-left:10px;">Chat</button>
                                </div>
                                <div class="order-status">
                                    <?php
                                    // Status Mapping
                                    echo htmlspecialchars($order['status']);
                                    if ($order['status'] == 'Pending')
                                        echo " | To Pay";
                                    ?>
                                </div>
                            </div>

                            <a href="?view=tracking&order_id=<?php echo $order['id']; ?>" style="text-decoration:none;">
                                <div class="order-items">
                                    <div class="order-item-row">
                                        <?php
                                        // Resolve Image Path
                                        $img_file = isset($order['image_url']) ? $order['image_url'] : '';
                                        $final_img_src = '../image/imarket.png'; // Default fallback

                                        if (!empty($img_file)) {
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
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($final_img_src); ?>"
                                            class="item-img" alt="Product">
                                        <div class="item-info">
                                            <div class="item-name"><?php echo htmlspecialchars($order['product_name']); ?></div>
                                            <div class="item-meta">Quantity: x<?php echo $order['quantity']; ?></div>
                                        </div>
                                        <div class="item-price">₱<?php echo number_format($order['price'], 2); ?></div>
                                    </div>
                                </div>
                            </a>

                            <div class="order-footer" style="padding-top: 15px; border-top: 1px solid #f1f5f9; display: flex; flex-direction: column; align-items: flex-end;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                    <span style="font-size: 0.9rem; color: #64748b;">Order Total:</span>
                                    <span style="font-size: 1.4rem; font-weight: 700; color: #2A3B7E;">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                                </div>

                                <div class="order-actions" style="display:flex; gap:12px; justify-content: flex-end; width: 100%; margin-top: 5px;">
                                    <!-- TRACK ORDER -->
                                    <a href="../Categories/best_selling/Tracking.php?order_id=<?php echo $order['id']; ?>" 
                                       style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px; font-weight: 500; border-radius: 4px; background: #fff; border: 1px solid #e0e0e0; color: #333; text-decoration: none; transition: all 0.2s;">
                                        <i class="fas fa-truck-fast"></i> Track Order
                                    </a>
                                    
                                    <?php if (in_array($order['status'], ['Delivered', 'Completed'])): 
                                        $oid = $order['id'];
                                        $rev_check = mysqli_query($conn, "SELECT id FROM reviews WHERE order_id='$oid' LIMIT 1");
                                        $is_reviewed = (mysqli_num_rows($rev_check) > 0);
                                    ?>
                                        <!-- RATE BUTTON -->
                                        <?php if (!$is_reviewed): ?>
                                            <a href="Rate.php?product_id=<?php echo urlencode($order['product_id'] ?? 0); ?>&order_id=<?php echo $order['id']; ?>" 
                                               style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px; font-weight: 500; border-radius: 4px; background: #2A3B7E; color: #fff; border: none; text-decoration: none;">
                                                <i class="fas fa-star"></i> Rate
                                            </a>
                                        <?php else: ?>
                                            <span style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px; color: #10b981; font-weight: 600;">
                                                <i class="fas fa-check-circle"></i> Reviewed
                                            </span>
                                        <?php endif; ?>

                                        <!-- DELETE BUTTON (MOCK) -->
                                        <button style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px; font-weight: 500; border-radius: 4px; background: #ef4444; color: #fff; border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>

                                    <?php endif; ?>

                                    <?php if ($order['status'] == 'Pending'): ?>
                                        <button onclick="cancelOrder(<?php echo $order['id']; ?>)" 
                                                style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px; font-weight: 500; border-radius: 4px; background: #fff; border: 1px solid #fca5a5; color: #ef4444; cursor: pointer;">
                                            <i class="fas fa-times"></i> Cancel Order
                                        </button>
                                    <?php endif; ?>

                                    <button style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px; font-weight: 500; border-radius: 4px; background: #fff; border: 1px solid #ddd; color: #555; cursor: pointer;">
                                        Buy Again
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- VIEW: TRACKING -->
            <?php elseif ($view == 'tracking'):
                $track_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
                // Fetch specific order
                $curr_order = null;
                foreach ($my_orders as $o) {
                    if ($o['id'] == $track_id) {
                        $curr_order = $o;
                        break;
                    }
                }

                if (!$curr_order) {
                    echo "<div class='empty-state'>Order not found. <a href='?view=orders'>Back to orders</a></div>";
                } else {
                    $st = $curr_order['status']; // Pending, Paid, Shipped, Delivered
                    // Determine step active index
                    $step_idx = 1;
                    if (strcasecmp($st, 'Pending') == 0)
                        $step_idx = 1;
                    if (strcasecmp($st, 'Paid') == 0)
                        $step_idx = 2;
                    if (strcasecmp($st, 'Shipped') == 0)
                        $step_idx = 3;
                    if (strcasecmp($st, 'Delivered') == 0)
                        $step_idx = 4;
                    if (strcasecmp($st, 'Completed') == 0)
                        $step_idx = 5;
                    ?>
                    <div class="content-header">
                        <a href="?view=orders" style="text-decoration:none; color:#555; font-size:14px; margin-right:10px;"><i
                                class="fas fa-arrow-left"></i> Back</a>
                        <span class="content-title" style="font-size:18px;">Order Details |
                            <?php echo htmlspecialchars($curr_order['tracking_number']); ?></span>
                    </div>

                    <div class="tracking-container">

                        <div
                            style="background:#fffcf5; border:1px solid #ffeedb; padding:15px; margin-bottom:30px; border-radius:4px; display:flex; justify-content:space-between;">
                            <div>
                                <div style="font-size:16px; color:#2A3B7E; font-weight:500;">Order Status:
                                    <?php echo htmlspecialchars($st); ?>
                                </div>
                                <div style="font-size:13px; color:#777; margin-top:5px;">Estimated Delivery: 3-5 days</div>
                            </div>
                        </div>

                        <!-- STEPPER -->
                        <div class="track-stepper">
                            <!-- Step 1 -->
                            <div class="track-step <?php echo ($step_idx >= 1) ? 'active' : ''; ?>">
                                <div class="step-icon"><i class="fas fa-file-invoice"></i></div>
                                <div class="step-label">Order Placed</div>
                                <div class="track-time"><?php echo date('M d H:i', strtotime($curr_order['created_at'])); ?>
                                </div>
                            </div>
                            <!-- Step 2 -->
                            <div class="track-step <?php echo ($step_idx >= 2) ? 'active' : ''; ?>">
                                <div class="step-icon"><i class="fas fa-money-bill-wave"></i></div>
                                <div class="step-label">Paid</div>
                                <?php if ($step_idx >= 2): ?>
                                    <div class="track-time">
                                        <?php echo date('M d H:i', strtotime($curr_order['created_at'] . ' + 1 hour')); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Step 3 -->
                            <div class="track-step <?php echo ($step_idx >= 3) ? 'active' : ''; ?>">
                                <div class="step-icon"><i class="fas fa-box-open"></i></div>
                                <div class="step-label">Shipped Out</div>
                                <?php if ($step_idx >= 3): ?>
                                    <div class="track-time">
                                        <?php echo date('M d H:i', strtotime($curr_order['created_at'] . ' + 1 day')); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Step 4 -->
                            <div class="track-step <?php echo ($step_idx >= 4) ? 'active' : ''; ?>">
                                <div class="step-icon"><i class="fas fa-shipping-fast"></i></div>
                                <div class="step-label">To Receive</div>
                            </div>
                            <!-- Step 5 -->
                            <div class="track-step <?php echo ($step_idx >= 5) ? 'active' : ''; ?>">
                                <div class="step-icon"><i class="fas fa-star"></i></div>
                                <div class="step-label">Completed</div>
                            </div>
                        </div>

                        <!-- Detailed Timeline -->
                        <div style="margin-top:40px;">
                            <div style="font-weight:500; color:#333; margin-bottom:15px;">Tracking History</div>
                            <div
                                style="border-left:2px solid #e0e0e0; margin-left:15px; padding-left:25px; padding-bottom:20px; position:relative;">
                                <div
                                    style="position:absolute; left:-6px; top:0; width:10px; height:10px; background:#2A3B7E; border-radius:50%;">
                                </div>
                                <div style="font-size:14px; color:#333;">Your order is being processed by the seller.</div>
                                <div style="font-size:12px; color:#999; margin-top:2px;">
                                    <?php echo date('Y-m-d H:i', strtotime($curr_order['created_at'])); ?>
                                </div>
                            </div>
                            <?php if ($step_idx >= 2): ?>
                                <div
                                    style="border-left:2px solid #e0e0e0; margin-left:15px; padding-left:25px; padding-bottom:20px; position:relative;">
                                    <div
                                        style="position:absolute; left:-6px; top:0; width:10px; height:10px; background:#ccc; border-radius:50%;">
                                    </div>
                                    <div style="font-size:14px; color:#333;">Payment confirmed.</div>
                                    <div style="font-size:12px; color:#999; margin-top:2px;">...</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Order Items Small View -->
                        <div style="margin-top:30px; border-top:1px solid #eee; padding-top:20px;">
                            <div style="font-weight:500; margin-bottom:10px;">Product Info</div>
                            <div style="display:flex; gap:15px;">
                                <?php
                                // Resolve Image Path for Tracking
                                $track_img = isset($curr_order['image_url']) ? $curr_order['image_url'] : '';
                                $final_track_img = '../image/imarket.png';

                                if (!empty($track_img)) {
                                    $clean_t = str_replace('../../', '../', $track_img);
                                    $t_base = basename($clean_t);
                                    $t_base_d = str_replace(' ', '-', $t_base);

                                    $t_candidates = [
                                        $clean_t,
                                        '../image/Best-seller/' . $t_base,
                                        '../image/Best-seller/' . $t_base_d,
                                        '../image/best_selling/' . $t_base,
                                        '../image/' . $t_base,
                                        '../image/Shop/' . $t_base,
                                        '../image/Shop/UrbanWear PH/' . $t_base,
                                        $track_img,
                                        $t_base
                                    ];

                                    foreach ($t_candidates as $c) {
                                        if (!empty($c) && file_exists($c) && !is_dir($c)) {
                                            $final_track_img = $c;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($final_track_img); ?>"
                                    style="width:60px; height:60px; border:1px solid #eee; object-fit: contain;">
                                <div>
                                    <div style="font-size:14px; font-weight:600;">
                                        <?php echo htmlspecialchars($curr_order['product_name']); ?>
                                    </div>
                                    <div style="font-size:13px; color:#777;">Quantity: x<?php echo $curr_order['quantity']; ?>
                                    </div>
                                    <div style="font-size:14px; color:#2A3B7E;">
                                        ₱<?php echo number_format($curr_order['total_amount'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($curr_order['status'] == 'Pending'): ?>
                            <div style="margin-top: 30px; display: flex; justify-content: center; border-top: 1px solid #eee; padding-top: 25px;">
                                <button class="btn-outline" style="padding: 12px 30px; border-radius: 8px; color: #ef4444; border: 2px solid #fca5a5; font-weight: 700; background: #fffcfc; cursor: pointer; transition: all 0.2s;" 
                                        onmouseover="this.style.background='#ef4444'; this.style.color='white';" 
                                        onmouseout="this.style.background='#fffcfc'; this.style.color='#ef4444';"
                                        onclick="cancelOrder(<?php echo $curr_order['id']; ?>)">
                                    <i class="fas fa-times-circle" style="margin-right: 8px;"></i> Cancel Order
                                </button>
                            </div>
                        <?php endif; ?>

                    </div>

                    <?php
                } // End if/else for curr_order
            endif;
            ?>

            <!-- VIEW: BANKS & CARDS -->
            <?php if ($view == 'banks'): ?>
                <div class="content-header">
                    <div class="content-title">Credit / Debit Cards</div>
                    <div class="content-subtitle">Manage your payment methods</div>
                </div>

                <div class="empty-state">
                    <i class="fas fa-credit-card" style="font-size: 40px; margin-bottom: 20px; color: #ccc;"></i>
                    <p>You have not added any cards yet.</p>
                    <button class="btn-primary" style="margin-top: 10px;">+ Add New Card</button>
                </div>

                <!-- VIEW: ADDRESSES -->
            <?php elseif ($view == 'address'): ?>
                <div class="content-header">
                    <div class="content-title">My Addresses</div>
                    <div class="content-subtitle">Manage your shipping addresses</div>
                </div>

                <?php echo $msg; ?>

                <div class="address-list">
                    <?php if (empty($user_addresses)): ?>
                        <div class="empty-state">
                            <i class="fas fa-map-marker-alt" style="font-size: 40px; color: #ccc; margin-bottom: 20px;"></i>
                            <p>You have no saved addresses.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($user_addresses as $addr): ?>
                            <div class="address-card"
                                style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; position: relative; margin-bottom: 15px; background: #fff;">
                                <span style="position: absolute; right: 20px; top: 20px;">
                                    <a href="javascript:void(0)"
                                        onclick="editAddress(<?php echo htmlspecialchars(json_encode($addr)); ?>)"
                                        style="color: #0f8392; margin-right: 15px; font-size: 14px; font-weight: 500; text-decoration: none;">Edit</a>
                                    <a href="javascript:void(0)" onclick="deleteAddress(<?php echo $addr['id']; ?>)"
                                        style="color: #d9534f; font-size: 14px; font-weight: 500; text-decoration: none;">Delete</a>
                                </span>

                                <div style="font-weight: 600; font-size: 16px; margin-bottom: 8px;">
                                    <?php echo htmlspecialchars($addr['fullname']); ?>
                                    <span
                                        style="font-weight: normal; color: #777; margin-left: 10px; border-left: 1px solid #eee; padding-left: 10px;">
                                        <?php echo htmlspecialchars($addr['phone']); ?>
                                    </span>
                                </div>

                                <div style="color: #555; font-size: 14px; line-height: 1.6;">
                                    <?php echo htmlspecialchars($addr['address']); ?><br>
                                    <?php echo htmlspecialchars($addr['city']); ?>, <?php echo htmlspecialchars($addr['zip']); ?>
                                </div>

                                <div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
                                    <?php if ($addr['is_default']): ?>
                                        <span
                                            style="border: 1px solid #ee4d2d; color: #ee4d2d; font-size: 11px; padding: 2px 8px; border-radius: 2px; text-transform: uppercase; font-weight: 600;">Default</span>
                                    <?php else: ?>
                                        <button onclick="setDefault(<?php echo $addr['id']; ?>)"
                                            style="background: none; border: 1px solid #ccc; color: #666; font-size: 12px; padding: 4px 10px; border-radius: 2px; cursor: pointer;">Set
                                            as Default</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button class="btn-primary" onclick="openAddressModal()"
                    style="margin-top: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus"></i> Add New Address
                </button>

                <!-- VIEW: CHANGE PASSWORD -->
            <?php elseif ($view == 'password'): ?>
                <div class="content-header">
                    <div class="content-title">Change Password</div>
                    <div class="content-subtitle">For your account's security, do not share your password with anyone.</div>
                </div>

                <?php echo $msg; ?>

                <form action="" method="POST" style="max-width: 500px;">
                    <input type="hidden" name="action" value="change_password">

                    <div class="profile-input-group">
                        <div class="profile-input-label" style="width: 180px;">Current Password</div>
                        <div class="profile-input-field">
                            <input type="password" name="current_password" required>
                        </div>
                    </div>

                    <div class="profile-input-group">
                        <div class="profile-input-label" style="width: 180px;">New Password</div>
                        <div class="profile-input-field">
                            <input type="password" name="new_password" required>
                        </div>
                    </div>

                    <div class="profile-input-group">
                        <div class="profile-input-label" style="width: 180px;">Confirm Password</div>
                        <div class="profile-input-field">
                            <input type="password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="save-btn-container" style="margin-left: 210px;">
                        <button type="submit" class="btn-primary">Confirm</button>
                    </div>
                </form>

            <?php endif; ?>

        </main>
    </div>

    <!-- ADDRESS MODAL -->
    <div id="addressModal" class="modal-overlay"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div
            style="background: #fff; width: 500px; padding: 30px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); max-width: 95%;">
            <div style="font-size: 20px; font-weight: 500; margin-bottom: 25px; color: #333;" id="modalTitle">New
                Address</div>

            <form id="addressForm" action="" method="POST">
                <input type="hidden" name="action" value="save_address">
                <input type="hidden" name="address_id" id="addr_id" value="">

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <input type="text" name="fullname" id="addr_fullname" placeholder="Full Name"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                    </div>
                    <div style="flex: 1;">
                        <input type="text" name="phone" id="addr_phone" placeholder="Phone Number"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <input type="text" name="city" id="addr_city" placeholder="Region, Province, City"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <textarea name="address" id="addr_text" rows="3" placeholder="Street Name, Building, House No."
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px; font-family: inherit;"
                        required></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <input type="text" name="zip" id="addr_zip" placeholder="Postal Code"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px;" required>
                </div>

                <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                    <label
                        style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #666; cursor: pointer;">
                        <input type="checkbox" name="is_default" id="addr_default"> Set as Default Address
                    </label>
                    <button type="button" id="btn-detect-account"
                        style="background:none; border:1px solid #2A3B7E; color:#2A3B7E; padding:5px 10px; border-radius:4px; font-size:12px; cursor:pointer;">
                        <i class="fas fa-location-arrow"></i> Detect Location
                    </button>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-outline" onclick="closeAddressModal()"
                        style="padding: 10px 25px; border: 1px solid #ddd; background: #fff; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-primary"
                        style="padding: 10px 35px; background: #2A3B7E; color: #fff; border: none; cursor: pointer;">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden form for actions -->
    <form id="actionForm" method="POST" style="display:none;">
        <input type="hidden" name="action" id="formAction">
        <input type="hidden" name="address_id" id="formAddrId">
        <input type="hidden" name="order_id" id="formOrderId">
    </form>

    <script>
        function openAddressModal() {
            document.getElementById('modalTitle').innerText = 'New Address';
            document.getElementById('addressForm').reset();
            document.getElementById('addr_id').value = '';
            document.getElementById('addressModal').style.display = 'flex';
        }

        function closeAddressModal() {
            document.getElementById('addressModal').style.display = 'none';
        }

        function editAddress(addr) {
            document.getElementById('modalTitle').innerText = 'Edit Address';
            document.getElementById('addr_id').value = addr.id;
            document.getElementById('addr_fullname').value = addr.fullname;
            document.getElementById('addr_phone').value = addr.phone;
            document.getElementById('addr_city').value = addr.city;
            document.getElementById('addr_text').value = addr.address;
            document.getElementById('addr_zip').value = addr.zip;
            document.getElementById('addr_default').checked = addr.is_default == 1;
            document.getElementById('addressModal').style.display = 'flex';
        }

        function deleteAddress(id) {
            if (confirm('Are you sure you want to delete this address?')) {
                document.getElementById('formAction').value = 'delete_address';
                document.getElementById('formAddrId').value = id;
                document.getElementById('actionForm').submit();
            }
        }

        function setDefault(id) {
            document.getElementById('formAction').value = 'set_default';
            document.getElementById('formAddrId').value = id;
            document.getElementById('actionForm').submit();
        }

        function cancelOrder(id) {
            if (confirm('Are you sure you want to cancel this order?')) {
                document.getElementById('formAction').value = 'cancel_order';
                document.getElementById('formOrderId').value = id;
                document.getElementById('actionForm').submit();
            }
        }

        // Detect Location in Account Modal
        const detectBtnAcc = document.getElementById('btn-detect-account');
        if (detectBtnAcc) {
            detectBtnAcc.onclick = function () {
                if (!navigator.geolocation) {
                    alert("Geolocation not supported.");
                    return;
                }
                detectBtnAcc.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
                navigator.geolocation.getCurrentPosition(async (pos) => {
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`);
                        const data = await res.json();
                        if (data && data.address) {
                            document.getElementById('addr_text').value = data.display_name;
                            document.getElementById('addr_city').value = data.address.city || data.address.town || data.address.province || "";
                            document.getElementById('addr_zip').value = data.address.postcode || "";
                        }
                    } catch (e) { alert("Detection failed."); }
                    finally { detectBtnAcc.innerHTML = '<i class="fas fa-location-arrow"></i> Detect Location'; }
                }, () => {
                    alert("Location access denied.");
                    detectBtnAcc.innerHTML = '<i class="fas fa-location-arrow"></i> Detect Location';
                });
            };
        }

        // Close modal if clicked outside
        window.onclick = function (event) {
            const modal = document.getElementById('addressModal');
            if (event.target == modal) {
                closeAddressModal();
            }
        }
    </script>
    <div class="footer-account-wrapper" style="margin-top: 50px;">
        <?php
        $path_prefix = '../';
        include('../Components/footer.php');
        ?>
    </div>
</body>

</html>
