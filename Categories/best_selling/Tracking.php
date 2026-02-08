<?php
session_start();
include("../../Database/config.php");

// Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get Order ID from URL
$order_id_param = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_data = null;

if ($order_id_param > 0) {
    // Fetch specific order
    $sql_o = "SELECT o.*, u.fullname, u.phone, u.address, u.city, u.zip 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              WHERE o.id='$order_id_param' AND o.user_id='$user_id'";
    $res_o = mysqli_query($conn, $sql_o);
    if ($res_o && mysqli_num_rows($res_o) > 0) {
        $order_data = mysqli_fetch_assoc($res_o);
    }
}

if (!$order_data) {
    die("Order not found or access denied.");
}

$st = $order_data['status'];
$tracking_number = "IMARKET" . str_pad($order_data['id'], 12, '0', STR_PAD_LEFT);
$order_ref = "ORD-" . str_pad($order_data['id'], 6, '0', STR_PAD_LEFT);

// Determine steps
$steps = [
    ['label' => 'Order Placed', 'icon' => 'fa-file-alt', 'time' => date('m/d/Y H:i', strtotime($order_data['created_at']))],
    ['label' => 'Payment Info Confirmed', 'icon' => 'fa-file-invoice-dollar', 'time' => date('m/d/Y H:i', strtotime($order_data['created_at'] . ' +10 minutes'))],
    ['label' => 'Order Shipped Out', 'icon' => 'fa-truck', 'time' => date('m/d/Y H:i', strtotime($order_data['created_at'] . ' +1 day'))],
    ['label' => 'Order Received', 'icon' => 'fa-box', 'time' => date('m/d/Y H:i', strtotime($order_data['created_at'] . ' +3 days'))],
    ['label' => 'Order Completed', 'icon' => 'fa-star', 'time' => date('m/d/Y H:i', strtotime($order_data['created_at'] . ' +4 days'))],
];

// Active step based on status
$active_step = 1;
if ($st == 'Paid') $active_step = 2;
if ($st == 'Shipped') $active_step = 3;
if ($st == 'Delivered') $active_step = 4;
if ($st == 'Completed') $active_step = 5;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Order - IMarket</title>
    <link rel="icon" type="image/x-icon" href="../../image/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2A3B7E;
            --success-green: #26aa99;
            --text-dark: #333;
            --text-muted: #888;
            --bg-light: #f5f5f5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            color: var(--text-dark);
        }

        .tracking-container {
            max-width: 1100px;
            margin: 30px auto;
            background: white;
            box-shadow: 0 1px 1px rgba(0,0,0,0.05);
            border-radius: 4px;
            overflow: hidden;
        }

        .tracking-header {
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-meta {
            text-align: right;
            font-size: 14px;
        }

        .status-badge {
            color: var(--success-green);
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 15px;
            padding-left: 15px;
            border-left: 1px solid #ddd;
        }

        /* Progress Steps */
        .progress-section {
            padding: 50px 20px;
            border-bottom: 1px solid #eee;
        }

        .stepper {
            display: flex;
            justify-content: space-between;
            position: relative;
            max-width: 900px;
            margin: 0 auto;
        }

        .stepper::before {
            content: "";
            position: absolute;
            top: 25px;
            left: 50px;
            right: 50px;
            height: 3px;
            background: #e0e0e0;
            z-index: 1;
        }

        .stepper-progress {
            position: absolute;
            top: 25px;
            left: 50px;
            height: 3px;
            background: var(--success-green);
            z-index: 2;
            transition: width 0.5s ease;
        }

        .step-item {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 3;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: white;
            border: 3px solid #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #ccc;
            font-size: 20px;
            transition: all 0.3s;
        }

        .step-item.active .icon-box {
            border-color: var(--success-green);
            color: var(--success-green);
        }

        .step-item.current .icon-box {
            background: white;
            border-color: var(--success-green);
            color: var(--success-green);
        }

        .step-label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
        }

        .step-time {
            font-size: 12px;
            color: #999;
        }

        /* Info Section */
        .info-section {
            padding: 25px 40px;
            background: #fff;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .buy-again-btn {
            background: #2b3b7e;
            color: white;
            padding: 12px 40px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            margin-right: 20px;
        }

        .contact-btn {
            background: white;
            border: 1px solid #ddd;
            color: #555;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 400;
            font-size: 15px;
        }

        /* Details Grid */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            border-top: 5px solid transparent;
            position: relative;
        }

        .details-grid::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background-image: repeating-linear-gradient(45deg, #6fa6d6, #6fa6d6 30px, transparent 30px, transparent 40px, #f18d9b 40px, #f18d9b 70px, transparent 70px, transparent 80px);
        }

        .address-pane {
            padding: 30px;
            border-right: 1px solid #eee;
        }

        .pane-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .address-info {
            font-size: 14px;
            line-height: 1.8;
            color: #444;
        }

        .address-info b {
            display: block;
            margin-bottom: 5px;
            font-size: 15px;
        }

        .timeline-pane {
            padding: 30px;
        }

        .store-header {
            text-align: right;
            font-size: 12px;
            color: #999;
            margin-bottom: 25px;
        }

        .timeline {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .timeline-item {
            display: flex;
            gap: 20px;
            padding-bottom: 25px;
            position: relative;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: 136px;
            top: 15px;
            bottom: -15px;
            width: 1px;
            background: #eee;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .tm-time {
            width: 120px;
            text-align: right;
            font-size: 13px;
            color: #777;
            padding-top: 2px;
        }

        .tm-dot {
            width: 12px;
            height: 12px;
            background: #ddd;
            border-radius: 50%;
            margin-top: 6px;
            z-index: 2;
            flex-shrink: 0;
        }

        .timeline-item.active .tm-dot {
            background: var(--success-green);
            box-shadow: 0 0 0 4px rgba(38, 170, 153, 0.15);
        }

        .tm-content {
            flex: 1;
        }

        .tm-status {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .timeline-item.active .tm-status {
            color: var(--success-green);
        }

        .tm-desc {
            font-size: 13px;
            color: #777;
            line-height: 1.5;
        }

        .tm-action {
            color: var(--success-green);
            text-decoration: none;
            font-size: 13px;
            margin-top: 5px;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .details-grid { grid-template-columns: 1fr; }
            .address-pane { border-right: none; border-bottom: 1px solid #eee; }
            .stepper { flex-wrap: wrap; gap: 20px; }
            .stepper::before, .stepper-progress { display: none; }
        }
    </style>
</head>
<body>

    <nav>
        <?php 
        $path_prefix = '../../';
        include '../../Components/header.php'; 
        ?>
    </nav>

    <div class="tracking-container">
        <!-- Header -->
        <div class="tracking-header">
            <a href="../../Content/user-account.php?view=orders" class="back-link">
                <i class="fas fa-chevron-left"></i> BACK
            </a>
            <div class="order-meta">
                ORDER ID. <?php echo $tracking_number; ?>
                <span class="status-badge"><?php echo str_replace('_', ' ', strtoupper($st)); ?></span>
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-section">
            <div class="stepper">
                <div class="stepper-progress" style="width: <?php echo ($active_step - 1) * 25; ?>%;"></div>
                <?php foreach ($steps as $i => $step): 
                    $is_active = ($i + 1 <= $active_step);
                    $is_current = ($i + 1 == $active_step);
                ?>
                <div class="step-item <?php echo $is_active ? 'active' : ''; ?> <?php echo $is_current ? 'current' : ''; ?>">
                    <div class="icon-box">
                        <i class="fas <?php echo $step['icon']; ?>"></i>
                    </div>
                    <div class="step-label"><?php echo $step['label']; ?></div>
                    <div class="step-time"><?php echo $is_active ? $step['time'] : ''; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Info/Actions -->
        <div class="info-section">
            <div style="display: flex; align-items: center; gap: 20px;">
                <?php if (!empty($order_data['image_url'])): ?>
                    <img src="../../<?php echo htmlspecialchars($order_data['image_url']); ?>" alt="Product" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                <?php else: ?>
                    <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ccc;">
                        <i class="fas fa-image" style="font-size: 2rem;"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 5px;"><?php echo htmlspecialchars($order_data['product_name'] ?? 'Product'); ?></div>
                    <div style="font-size: 14px; color: #555;">Thank you for shopping with iMarket Best Selling!</div>
                </div>
            </div>
            <div class="action-buttons">
                <a href="index.php" class="buy-again-btn">Buy Again</a>
                <a href="../../Services/Customer_Service.php?tab=chat" class="contact-btn">Contact Seller</a>
            </div>
        </div>

        <!-- Details -->
        <div class="details-grid">
            <div class="address-pane">
                <div class="pane-title">Delivery Address</div>
                <div class="address-info">
                    <b><?php echo htmlspecialchars($order_data['fullname']); ?></b>
                    <?php echo htmlspecialchars($order_data['phone']); ?><br>
                    <?php echo htmlspecialchars($order_data['address']); ?><br>
                    <?php echo htmlspecialchars($order_data['city']); ?>, <?php echo htmlspecialchars($order_data['zip']); ?>
                </div>
            </div>
            
            <div class="timeline-pane">
                <div class="store-header">
                    iMarket Best Selling<br>
                    <?php echo $tracking_number; ?>
                </div>
                
                <ul class="timeline">
                    <!-- Delivered -->
                    <?php if ($active_step >= 4): ?>
                    <li class="timeline-item active">
                        <div class="tm-time"><?php echo date('m/d/Y H:i', strtotime($order_data['created_at'] . ' +3 days')); ?></div>
                        <div class="tm-dot"></div>
                        <div class="tm-content">
                            <div class="tm-status">Delivered</div>
                            <div class="tm-desc">Parcel has been delivered. Recipient: [<?php echo $order_data['fullname']; ?>]</div>
                            <a href="#" class="tm-action">View Proof of Delivery</a>
                        </div>
                    </li>
                    <?php endif; ?>

                    <!-- Shipped -->
                    <?php if ($active_step >= 3): ?>
                    <li class="timeline-item <?php echo ($active_step == 3) ? 'active' : ''; ?>">
                        <div class="tm-time"><?php echo date('m/d/Y H:i', strtotime($order_data['created_at'] . ' +1 day')); ?></div>
                        <div class="tm-dot"></div>
                        <div class="tm-content">
                            <div class="tm-status">In transit</div>
                            <div class="tm-desc">Parcel is out for delivery.</div>
                        </div>
                    </li>
                    <?php endif; ?>

                    <!-- Placed -->
                    <li class="timeline-item <?php echo ($active_step == 1 || $active_step == 2) ? 'active' : ''; ?>">
                        <div class="tm-time"><?php echo date('m/d/Y H:i', strtotime($order_data['created_at'])); ?></div>
                        <div class="tm-dot"></div>
                        <div class="tm-content">
                            <div class="tm-status">Order Placed</div>
                            <div class="tm-desc">Your order is being processed by the seller.</div>
                        </div>
                    </li>

                    <li class="timeline-item">
                        <div class="tm-time"></div>
                        <div class="tm-dot" style="visibility: hidden;"></div>
                        <div class="tm-content">
                            <a href="#" class="tm-action">See More</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <footer>
        <?php include '../../Components/footer.php'; ?>
    </footer>

</body>
</html>
