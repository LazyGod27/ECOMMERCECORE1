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
            --text-dark: #1a1a1a;
            --text-muted: #888;
            --bg-light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .tracking-container {
            max-width: 1200px;
            margin: 30px auto;
            background: white;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
        }

        .tracking-header {
            padding: 25px 35px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #f8f9fa, #fff);
        }

        .back-link {
            text-decoration: none;
            color: var(--primary-blue);
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .back-link:hover {
            background-color: rgba(42, 59, 126, 0.08);
            transform: translateX(-3px);
        }

        .order-meta {
            text-align: right;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-badge {
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            padding: 8px 16px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--success-green) 0%, #1f9183 100%);
            box-shadow: 0 4px 12px rgba(38, 170, 153, 0.3);
        }

        /* Progress Steps */
        .progress-section {
            padding: 60px 30px;
            border-bottom: 2px solid #f0f0f0;
            background: linear-gradient(to bottom, #fafbfc, #fff);
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
            background: #e8e8e8;
            z-index: 1;
            border-radius: 2px;
        }

        .stepper-progress {
            position: absolute;
            top: 25px;
            left: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--success-green), var(--primary-blue));
            z-index: 2;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 2px;
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
            border: 3px solid #e8e8e8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #ccc;
            font-size: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .step-item.active .icon-box {
            border-color: var(--success-green);
            color: var(--success-green);
            background: rgba(38, 170, 153, 0.08);
        }

        .step-item.current .icon-box {
            background: white;
            border-color: var(--primary-blue);
            color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(42, 59, 126, 0.1);
        }

        .step-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-dark);
        }

        .step-time {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Info Section */
        .info-section {
            padding: 25px 35px;
            background: #fff;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }

        .product-info-section {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .product-info-section img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #f0f0f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .product-info-section img:hover {
            transform: scale(1.05);
        }

        .product-details h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .product-details p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }

        .buy-again-btn,
        .contact-btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .buy-again-btn {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #1a2657 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .buy-again-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.4);
        }

        .contact-btn {
            background: white;
            border: 2px solid #e8e8e8;
            color: var(--text-dark);
        }

        .contact-btn:hover {
            border-color: var(--primary-blue);
            background-color: rgba(42, 59, 126, 0.05);
        }

        /* Details Grid */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            border-top: 2px solid #f0f0f0;
            position: relative;
            background: #fff;
        }

        .address-pane {
            padding: 35px;
            border-right: 2px solid #f0f0f0;
        }

        .pane-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .address-info {
            font-size: 14px;
            line-height: 1.8;
            color: #555;
        }

        .address-info b {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
            color: var(--text-dark);
            font-weight: 700;
        }

        .timeline-pane {
            padding: 35px;
        }

        .store-header {
            text-align: right;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 25px;
            font-weight: 600;
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
            top: 18px;
            bottom: -15px;
            width: 2px;
            background: #e8e8e8;
            transition: all 0.4s ease;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-item.active::before {
            background: linear-gradient(180deg, var(--success-green), transparent);
        }

        .tm-time {
            width: 120px;
            text-align: right;
            font-size: 13px;
            color: var(--text-muted);
            padding-top: 2px;
            font-weight: 600;
        }

        .tm-dot {
            width: 14px;
            height: 14px;
            background: #e8e8e8;
            border-radius: 50%;
            margin-top: 4px;
            z-index: 2;
            flex-shrink: 0;
            transition: all 0.4s ease;
            border: 3px solid white;
        }

        .timeline-item.active .tm-dot {
            background: var(--success-green);
            box-shadow: 0 0 0 4px rgba(38, 170, 153, 0.15), 0 2px 8px rgba(38, 170, 153, 0.2);
        }

        .tm-content {
            flex: 1;
        }

        .tm-status {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .timeline-item.active .tm-status {
            color: var(--success-green);
        }

        .tm-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .tm-action {
            color: var(--success-green);
            text-decoration: none;
            font-size: 13px;
            margin-top: 6px;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tm-action:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .tracking-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
                padding: 20px 20px;
            }

            .order-meta {
                width: 100%;
                justify-content: space-between;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .address-pane {
                border-right: none;
                border-bottom: 2px solid #f0f0f0;
            }

            .stepper {
                flex-wrap: wrap;
                gap: 20px;
            }

            .stepper::before,
            .stepper-progress {
                display: none;
            }

            .icon-box {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .info-section {
                flex-direction: column;
                gap: 15px;
            }

            .product-info-section {
                width: 100%;
            }

            .action-buttons {
                width: 100%;
                flex-wrap: wrap;
            }

            .buy-again-btn,
            .contact-btn {
                flex: 1;
                min-width: 120px;
            }
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tracking-container {
            animation: slideInUp 0.5s ease-out;
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
                <i class="fas fa-chevron-left"></i> Back to Orders
            </a>
            <div class="order-meta">
                <span style="color: #666;">ORDER ID: <strong><?php echo $tracking_number; ?></strong></span>
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
            <div class="product-info-section">
                <?php if (!empty($order_data['image_url'])): ?>
                    <img src="../../<?php echo htmlspecialchars($order_data['image_url']); ?>" alt="Product">
                <?php else: ?>
                    <div style="width: 90px; height: 90px; background: linear-gradient(135deg, #f0f0f0, #e8e8e8); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #ccc;">
                        <i class="fas fa-image" style="font-size: 2.5rem;"></i>
                    </div>
                <?php endif; ?>
                <div class="product-details">
                    <h3><?php echo htmlspecialchars($order_data['product_name'] ?? 'Product'); ?></h3>
                    <p>Thank you for shopping with iMarket! Track your delivery status below.</p>
                </div>
            </div>
            <div class="action-buttons">
                <a href="index.php" class="buy-again-btn">
                    <i class="fas fa-shopping-bag" style="margin-right: 6px;"></i> Buy Again
                </a>
                <a href="../../Services/Customer_Service.php?tab=chat" class="contact-btn">
                    <i class="fas fa-headset" style="margin-right: 6px;"></i> Contact Support
                </a>
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
