<?php
session_start();
include("../Database/config.php");

// Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch User Data
$sql_user = "SELECT * FROM users WHERE id='$user_id'";
$res_user = mysqli_query($conn, $sql_user);
$u = mysqli_fetch_assoc($res_user);

$fname = $u['fullname'] ?? 'User';
$uphone = $u['phone'] ?? '';
$uaddr = $u['address'] ?? '';
$ucity = $u['city'] ?? '';
$uzip = $u['zip'] ?? '';

// Fetch Order Data if ID is provided
$order_id_param = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_data = null;
$shop_name = "ImarketPH"; // Default
$is_best_selling = false;

if ($order_id_param > 0) {
    // Try to fetch order
    $sql_o = "SELECT * FROM orders WHERE id='$order_id_param' AND user_id='$user_id'";
    $res_o = mysqli_query($conn, $sql_o);
    if ($res_o && mysqli_num_rows($res_o) > 0) {
        $order_data = mysqli_fetch_assoc($res_o);
        $order_id = str_pad($order_data['id'], 6, '0', STR_PAD_LEFT);
        $order_status = strtoupper($order_data['status']);

        // Determine Shop Logic (Same as Order-history)
        $img_file = isset($order_data['image_url']) ? $order_data['image_url'] : '';
        if (!empty($img_file)) {
            // Check for Best Selling Keywords
            if (
                strpos($img_file, 'best_selling') !== false ||
                strpos(strtolower($img_file), 'bag-women') !== false ||
                strpos(strtolower($img_file), 'notebooks') !== false ||
                strpos(strtolower($img_file), 'earphone') !== false ||
                strpos(strtolower($img_file), 'shoes') !== false
            ) {
                $is_best_selling = true;
                $shop_name = "iMarket Best Selling";
            } else {
                // Try to check folder in path
                if (preg_match('/Shop\/image\/([^\/]+)\//', $img_file, $matches)) {
                    $shop_name = $matches[1];
                } else {
                    $shop_name = "UrbanWear PH"; // Fallback or detect others
                }
            }
        }
    }
}

// Restore Mock Data Arrays (Fallback or if no real tracking table yet)
// We need these defined to avoid Undefined Variable errors in the View.
// In a real scenario, these would be populated from the DB or an API based on $order_id.
if (!isset($order_statuses)) {
    $order_statuses = [
        ['label' => 'Order Placed', 'time' => $order_data ? date('m/d/Y H:i', strtotime($order_data['created_at'])) : '11/30/2022 10:18', 'icon' => 'fa-file-invoice', 'completed' => true],
        ['label' => 'Payment Info Confirmed', 'time' => $order_data ? date('m/d/Y H:i', strtotime($order_data['created_at'] . ' +10 min')) : '11/30/2022 10:28', 'icon' => 'fa-file-invoice-dollar', 'completed' => true],
        ['label' => 'Order Shipped Out', 'time' => 'Pending', 'icon' => 'fa-truck', 'completed' => ($order_status == 'DELIVERED' || $order_status == 'SHIPPED')], // Dynamic check
        ['label' => 'Order Received', 'time' => 'Pending', 'icon' => 'fa-box-open', 'completed' => ($order_status == 'DELIVERED')],
        ['label' => 'Order Completed', 'time' => 'Pending', 'icon' => 'fa-star', 'completed' => ($order_status == 'DELIVERED' || $order_status == 'COMPLETED')],
    ];
}

if (!isset($timeline_events)) {
    $timeline_events = [
        [
            'status' => 'Processing',
            'desc' => 'Your order is being processed.',
            'time' => $order_data ? date('m/d/Y H:i', strtotime($order_data['created_at'])) : 'Just now',
            'active' => true,
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Order - <?php echo $order_id; ?></title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/best_selling/tracking.css"> <!-- Updated path -->
    <style>
        /* Embedding crucial styles to ensure look */
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f5f5f5;
            margin: 0;
        }

        .tracking-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .shop-header-branding {
            background: white;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 3px solid #eee;
        }

        .shop-header-branding img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .shop-header-branding h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #2A3B7E;
            font-weight: 700;
        }

        .shop-header-branding p {
            margin: 0;
            font-size: 0.8rem;
            color: #777;
        }

        .tracking-header {
            background: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .back-btn {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .order-info {
            text-align: right;
        }

        .status-text {
            color: #26aa99;
            font-weight: 600;
            margin-left: 10px;
            text-transform: uppercase;
        }

        .status-progress-card {
            background: white;
            padding: 30px 20px;
            margin-bottom: 20px;
        }

        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .stepper-item {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .stepper-item .step-icon {
            width: 50px;
            height: 50px;
            background: white;
            border: 4px solid #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px auto;
            color: #ddd;
            font-size: 1.2rem;
        }

        .stepper-item.completed .step-icon {
            border-color: #26aa99;
            color: #26aa99;
        }

        .stepper-item.current .step-icon {
            background: #26aa99;
            border-color: #26aa99;
            color: white;
        }

        .stepper-item::after {
            content: '';
            position: absolute;
            top: 25px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #ddd;
            z-index: -1;
        }

        .stepper-item:last-child::after {
            content: none;
        }

        .stepper-item.completed::after {
            background: #26aa99;
        }

        .title-sub {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .date-sub {
            font-size: 0.75rem;
            color: #888;
        }

        .actions-card,
        .details-container {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .thank-you-msg {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .btn {
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-right: 10px;
        }

        .btn-primary {
            background: #2A3B7E;
            color: white;
        }

        .btn-secondary {
            background: white;
            border: 1px solid #ddd;
            color: #333;
        }
    </style>
</head>

<body>
    <nav>
        <?php
        $path_prefix = '../';
        include $path_prefix . 'Components/header.php';
        ?>
    </nav>

    <div class="tracking-container">

        <!-- DYNAMIC SHOP BRANDING Header -->
        <div class="shop-header-branding">
            <img src="../image/logo.png" alt="Logo">
            <div>
                <?php if ($is_best_selling): ?>
                    <h2>| iMarket Best Selling</h2>
                    <p>Top Rated Products</p>
                <?php else: ?>
                    <h2>| <?php echo htmlspecialchars($shop_name); ?></h2>
                    <p>Official Store</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Header: Back | Order ID | Status -->
        <div class="tracking-header">
            <a href="Order-history.php" class="back-btn">
                <i class="fas fa-chevron-left"></i> BACK
            </a>
            <div class="order-info">
                ORDER ID. <?php echo $order_id; ?>
                <span class="status-text"><?php echo $order_status; ?></span>
            </div>
        </div>

        <!-- Progress Stepper -->
        <div class="status-progress-card">
            <div class="stepper-wrapper">
                <?php foreach ($order_statuses as $index => $step): ?>
                    <div
                        class="stepper-item <?php echo $step['completed'] ? 'completed' : ''; ?> <?php echo ($index == count($order_statuses) - 1) ? 'current' : ''; ?>">
                        <div class="step-icon">
                            <i class="fas <?php echo $step['icon']; ?>"></i>
                        </div>
                        <div class="step-content">
                            <div class="title-sub"><?php echo $step['label']; ?></div>
                            <div class="date-sub"><?php echo $step['time']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="actions-card" style="display: flex; align-items: center; gap: 20px; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <?php if (!empty($order_data['image_url'])): ?>
                    <img src="../<?php echo htmlspecialchars($order_data['image_url']); ?>" alt="Product" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                <?php else: ?>
                    <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ccc;">
                        <i class="fas fa-image" style="font-size: 2rem;"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: #333; margin-bottom: 5px;"><?php echo htmlspecialchars($order_data['product_name'] ?? 'Product'); ?></div>
                    <div class="thank-you-msg" style="margin-bottom: 0;">Thank you for Shopping with <?php echo htmlspecialchars($shop_name); ?>!</div>
                </div>
            </div>
            <div class="action-buttons-group">
                <?php
                $buy_again_pid = isset($order_data['product_id']) ? $order_data['product_id'] : 1;
                ?>
                <a href="../Categories/best_selling/index.php?id=<?php echo $buy_again_pid; ?>" class="btn btn-primary" style="margin-bottom: 5px;">Buy Again</a>
                <a href="../Services/Customer_Service.php?tab=chat" class="btn btn-secondary">Contact Seller</a>
            </div>
        </div>
    </div>

    <!-- Details: Address & Tracking History -->
    <div class="details-container">
        <div class="envelope-line"></div> <!-- Decorative Border -->

        <div class="details-content">
            <!-- Delivery Address -->
            <div class="address-section">
                <h3>Delivery Address</h3>
                <div class="address-box">
                    <div class="name"><?php echo htmlspecialchars($fname); ?></div>
                    <div class="phone"><?php echo htmlspecialchars($uphone); ?></div>
                    <div class="text">
                        <?php echo htmlspecialchars($uaddr); ?><br>
                        <?php if ($ucity || $uzip): ?>
                            <?php echo htmlspecialchars($ucity); ?>, <?php echo htmlspecialchars($uzip); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Vertical Timeline -->
            <div class="timeline-section">
                <div style="font-size:0.8rem; text-align:right; margin-bottom:10px; color:#777;">
                    ImarketPH<br>IMarketPH02393613507C
                </div>
                <ul class="timeline-list">
                    <?php foreach ($timeline_events as $event): ?>
                        <li class="timeline-item <?php echo $event['active'] ? 'active' : ''; ?>">
                            <div class="timeline-time">
                                <?php echo $event['time']; ?>
                            </div>
                            <div class="timeline-dot"></div>
                            <div class="timeline-info">
                                <div class="timeline-status"><?php echo $event['status']; ?></div>
                                <div class="timeline-desc"><?php echo $event['desc']; ?></div>
                                <?php if (isset($event['action'])): ?>
                                    <a href="#" class="timeline-action"><?php echo $event['action']; ?></a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <li class="timeline-item">
                        <div class="timeline-time">...</div>
                        <div class="timeline-info">
                            <a href="#" style="color:#26aa99; font-size:0.9rem; text-decoration:none;">See More</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    </div>

    <footer>
        <?php include $path_prefix . 'Components/footer.php'; ?>
    </footer>
</body>

</html>



