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
    <title>Track Order - <?php echo $order_id; ?> | iMarket</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #2A3B7E;
            --primary-dark: #1a2657;
            --accent-blue: #3b82f6;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --danger-red: #ef4444;
            --soft-gray: #f8fafc;
            --light-border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--soft-gray) 0%, #f0f4f8 100%);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .tracking-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }

        /* Header Navigation */
        .tracking-header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 14px;
            padding: 10px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .back-btn:hover {
            background: var(--soft-gray);
            color: var(--primary-navy);
        }

        .order-info-header {
            text-align: right;
        }

        .order-id {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .order-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-navy);
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--success-green), #059669);
            color: white;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        /* Shop Branding */
        .shop-branding {
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-navy);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .shop-branding img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .shop-branding-info h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 3px;
        }

        .shop-branding-info p {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Progress Stepper */
        .progress-section {
            background: white;
            border-radius: 12px;
            padding: 40px 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
            gap: 0;
        }

        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 8%;
            right: 8%;
            height: 3px;
            background: var(--light-border);
            z-index: 0;
        }

        .stepper-item {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .step-icon {
            width: 60px;
            height: 60px;
            background: white;
            border: 3px solid var(--light-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: var(--text-secondary);
            font-size: 24px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .stepper-item.completed .step-icon {
            background: linear-gradient(135deg, var(--success-green), #059669);
            border-color: var(--success-green);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .stepper-item.current .step-icon {
            background: linear-gradient(135deg, var(--accent-blue), #1d4ed8);
            border-color: var(--accent-blue);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 4px 20px rgba(59, 130, 246, 0.5); }
        }

        .step-label {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-primary);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .step-time {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Product & Actions Card */
        .product-action-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid var(--light-border);
            flex-shrink: 0;
        }

        .product-image-placeholder {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--soft-gray), #e2e8f0);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            font-size: 32px;
            flex-shrink: 0;
        }

        .product-info {
            flex: 1;
            min-width: 200px;
        }

        .product-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .product-shop {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 12px;
        }

        .action-buttons-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-navy), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.3);
        }

        .btn-secondary {
            background: white;
            border: 2px solid var(--light-border);
            color: var(--primary-navy);
        }

        .btn-secondary:hover {
            border-color: var(--accent-blue);
            color: var(--accent-blue);
            background: var(--soft-gray);
        }

        /* Details Section */
        .details-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .detail-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-card h3 i {
            color: var(--accent-blue);
        }

        .address-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .address-name {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-primary);
        }

        .address-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .address-item i {
            color: var(--accent-blue);
            width: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Timeline */
        .timeline-list {
            list-style: none;
            position: relative;
            padding: 0;
        }

        .timeline-list::before {
            content: '';
            position: absolute;
            left: 21px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, var(--accent-blue), var(--light-border));
        }

        .timeline-item {
            margin-bottom: 25px;
            position: relative;
            padding-left: 70px;
        }

        .timeline-dot {
            position: absolute;
            left: 11px;
            top: 5px;
            width: 22px;
            height: 22px;
            background: white;
            border: 3px solid var(--light-border);
            border-radius: 50%;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .timeline-item.active .timeline-dot {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.1);
            width: 26px;
            height: 26px;
            left: 9px;
        }

        .timeline-time {
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-blue);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .timeline-status {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-primary);
            margin-bottom: 3px;
        }

        .timeline-desc {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .timeline-action {
            display: inline-block;
            margin-top: 8px;
            color: var(--accent-blue);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .timeline-action:hover {
            text-decoration: underline;
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

        <!-- Header Navigation -->
        <div class="tracking-header-nav">
            <a href="Order-history.php" class="back-btn">
                <i class="fas fa-chevron-left"></i> Back to Orders
            </a>
            <div class="order-info-header">
                <div class="order-id">Order #</div>
                <div class="order-number"><?php echo $order_id; ?></div>
                <div class="status-badge">
                    <i class="fas fa-check-circle" style="margin-right: 5px;"></i> <?php echo $order_status; ?>
                </div>
            </div>
        </div>

        <!-- Shop Branding -->
        <div class="shop-branding">
            <img src="../image/logo.png" alt="iMarket">
            <div class="shop-branding-info">
                <?php if ($is_best_selling): ?>
                    <h3>iMarket Best Selling</h3>
                    <p>★★★★★ Top Rated Products</p>
                <?php else: ?>
                    <h3><?php echo htmlspecialchars($shop_name); ?></h3>
                    <p>Official Store • Trusted Seller</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Stepper -->
        <div class="progress-section">
            <div class="stepper-wrapper">
                <?php foreach ($order_statuses as $index => $step): ?>
                    <div class="stepper-item <?php echo $step['completed'] ? 'completed' : ''; ?> <?php echo ($index == 2) ? 'current' : ''; ?>">
                        <div class="step-icon">
                            <i class="fas <?php echo $step['icon']; ?>"></i>
                        </div>
                        <div class="step-label"><?php echo $step['label']; ?></div>
                        <div class="step-time"><?php echo $step['time']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product & Action Buttons -->
        <div class="product-action-card">
            <div>
                <?php if (!empty($order_data['image_url'])): ?>
                    <img src="../<?php echo htmlspecialchars($order_data['image_url']); ?>" alt="Product" class="product-image">
                <?php else: ?>
                    <div class="product-image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <div class="product-name"><?php echo htmlspecialchars($order_data['product_name'] ?? 'Product'); ?></div>
                <div class="product-shop">from <?php echo htmlspecialchars($shop_name); ?></div>
                <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 15px;">Thank you for shopping with us! Your order is being carefully prepared.</p>
                <div class="action-buttons-group">
                    <?php
                    $buy_again_pid = isset($order_data['product_id']) ? $order_data['product_id'] : 1;
                    ?>
                    <a href="../Categories/best_selling/index.php?id=<?php echo $buy_again_pid; ?>" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Buy Again
                    </a>
                    <a href="../Services/Customer_Service.php?tab=chat" class="btn btn-secondary">
                        <i class="fas fa-comments"></i> Contact Seller
                    </a>
                </div>
            </div>
        </div>

        <!-- Details Section: Address & Timeline -->
        <div class="details-section">
            <!-- Delivery Address -->
            <div class="detail-card">
                <h3>
                    <i class="fas fa-map-marker-alt"></i> Delivery Address
                </h3>
                <div class="address-info">
                    <div class="address-name"><?php echo htmlspecialchars($fname); ?></div>
                    <div class="address-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo htmlspecialchars($uphone); ?></span>
                    </div>
                    <div class="address-item">
                        <i class="fas fa-map-pin"></i>
                        <span><?php echo htmlspecialchars($uaddr); ?></span>
                    </div>
                    <?php if ($ucity || $uzip): ?>
                        <div class="address-item">
                            <i class="fas fa-city"></i>
                            <span><?php echo htmlspecialchars($ucity); ?>, <?php echo htmlspecialchars($uzip); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Timeline Events -->
            <div class="detail-card">
                <h3>
                    <i class="fas fa-history"></i> Tracking Timeline
                </h3>
                <ul class="timeline-list">
                    <?php foreach ($timeline_events as $event): ?>
                        <li class="timeline-item <?php echo $event['active'] ? 'active' : ''; ?>">
                            <div class="timeline-dot"></div>
                            <div class="timeline-time"><?php echo $event['time']; ?></div>
                            <div class="timeline-status"><?php echo $event['status']; ?></div>
                            <div class="timeline-desc"><?php echo $event['desc']; ?></div>
                            <?php if (isset($event['action'])): ?>
                                <a href="#" class="timeline-action"><?php echo $event['action']; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>

    <footer>
        <?php include $path_prefix . 'Components/footer.php'; ?>
    </footer>
</body>

</html>



