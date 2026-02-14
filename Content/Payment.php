<?php
session_start();
include '../Database/config.php';

// Import PHPMailer for checkout confirmation email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("../PHPMailer/src/PHPMailer.php");
require_once("../PHPMailer/src/Exception.php");
require_once("../PHPMailer/src/SMTP.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// =====================================================
// HANDLE BOTH CART CHECKOUT AND SINGLE PRODUCT CHECKOUT
// =====================================================

$cart_items = [];
$product_name = isset($_GET['product_name']) ? $_GET['product_name'] : 'Multiple Items';
$item_price = isset($_GET['price']) ? floatval($_GET['price']) : 0;
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
$image = isset($_GET['image']) ? $_GET['image'] : '../image/logo.png';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Normalize path
if (strpos($image, '../../') === 0) {
    $image = str_replace('../../', '../', $image);
}

// Check if this is a cart checkout (from_cart parameter + selected_ids)
$is_cart_checkout = isset($_GET['from_cart']) && isset($_GET['selected_ids']);

if ($is_cart_checkout) {
    // Fetch cart items from database
    $selected_ids = array_map('intval', explode(',', $_GET['selected_ids']));
    $ids_placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
    
    $cart_sql = "SELECT * FROM cart WHERE id IN ($ids_placeholders) AND user_id=?";
    $stmt = $conn->prepare($cart_sql);
    $params = array_merge($selected_ids, [$user_id]);
    $types = str_repeat('i', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $cart_res = $stmt->get_result();
    
    if ($cart_res) {
        while ($item = mysqli_fetch_assoc($cart_res)) {
            $cart_items[] = $item;
        }
    }
    
    // Calculate totals from cart items
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += ($item['price'] * $item['quantity']);
    }
    
    $product_name = 'Multiple Items (' . count($cart_items) . ' items)';
} else {
    // Single product checkout (Buy Now)
    $subtotal = $item_price * $quantity;
    
    // Create a single item array for consistency
    $cart_items[] = [
        'id' => null,
        'product_id' => $product_id,
        'product_name' => $product_name,
        'price' => $item_price,
        'quantity' => $quantity,
        'image' => $image,
        'shop_name' => ''
    ];
}

// If amount is passed directly (from cart total)
if (isset($_GET['amount'])) {
    $subtotal = floatval($_GET['amount']);
}

// Calculate final total with shipping
$shipping_fee = 50.00;
$total_payment = $subtotal + $shipping_fee;

// Fetch Address logic
$full_addr_details = null;

// 1. Try user_addresses table (Default)
$stmt_addr = $conn->prepare("SELECT * FROM user_addresses WHERE user_id=? AND is_default=1 LIMIT 1");
$stmt_addr->bind_param("i", $user_id);
$stmt_addr->execute();
$check_addr = $stmt_addr->get_result();

if ($check_addr->num_rows > 0) {
    $full_addr_details = $check_addr->fetch_assoc();
    $stmt_addr->close();
} else {
    $stmt_addr->close();
    // 2. Try any address
    $stmt_addr2 = $conn->prepare("SELECT * FROM user_addresses WHERE user_id=? LIMIT 1");
    $stmt_addr2->bind_param("i", $user_id);
    $stmt_addr2->execute();
    $check_addr_any = $stmt_addr2->get_result();
    
    if ($check_addr_any->num_rows > 0) {
        $full_addr_details = $check_addr_any->fetch_assoc();
    } else {
        // 3. Try users table (Legacy)
        $stmt_user = $conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result();
        $u_row = $user_result->fetch_assoc();
        if (!empty($u_row['address'])) {
            $full_addr_details = [
                'fullname' => $u_row['fullname'],
                'phone' => $u_row['phone'],
                'address' => $u_row['address'],
                'city' => $u_row['city'],
                'zip' => $u_row['zip']
            ];
        }
        $stmt_user->close();
    }
    $stmt_addr2->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Selection | IMarket</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <link rel="stylesheet" href="../css/shop/payment.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            color: var(--text-primary);
        }

        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px 20px 80px;
        }

        /* Stepper Styles */
        .stepper-wrapper {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 50px;
            position: relative;
        }

        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 50%;
            width: 60%;
            height: 3px;
            background: linear-gradient(to right, var(--primary-navy), #ddd);
            transform: translateX(-50%);
            z-index: -1;
        }

        .stepper-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            z-index: 1;
        }

        .step-counter {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--light-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .stepper-item.completed .step-counter {
            background: var(--success-green);
            border-color: var(--success-green);
            color: white;
        }

        .stepper-item.active .step-counter {
            background: linear-gradient(135deg, var(--primary-navy), var(--accent-blue));
            border-color: var(--primary-navy);
            color: white;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .step-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stepper-item.active .step-name {
            color: var(--primary-navy);
        }

        /* Main Layout */
        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 40px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            border: 1px solid var(--light-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Delivery Address Section */
        .content-header {
            margin-bottom: 20px;
        }

        .content-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .content-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-top: 5px;
        }

        /* Payment Methods Grid */
        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .payment-card-label {
            cursor: pointer;
            position: relative;
        }

        .payment-card-content {
            border: 2px solid var(--light-border);
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            height: 100%;
            position: relative;
        }

        .payment-card-label:hover .payment-card-content {
            border-color: var(--accent-blue);
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.15);
        }

        .payment-card-label.active .payment-card-content {
            border: 2px solid var(--primary-navy);
            background: linear-gradient(135deg, rgba(42, 59, 126, 0.05), rgba(59, 130, 246, 0.05));
            box-shadow: 0 8px 24px rgba(42, 59, 126, 0.15);
        }

        .payment-card-label.active .payment-card-content::after {
            content: 'âœ“';
            font-weight: 900;
            position: absolute;
            top: 8px;
            right: 8px;
            color: var(--primary-navy);
            font-size: 1.4rem;
            background: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--primary-navy);
        }

        .payment-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }

        .payment-card-label:hover .payment-icon {
            transform: scale(1.1);
        }

        .payment-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .payment-subtitle {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .payment-option-details {
            display: none;
            padding: 20px;
            background: linear-gradient(135deg, #fef3c7 0%, #fee2e2 100%);
            margin-top: 20px;
            border-radius: 12px;
            border: 2px solid var(--warning-orange);
            animation: slideDown 0.4s ease-out;
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

        /* Summary Card */
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            border: 1px solid var(--light-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 120px;
            height: fit-content;
        }

        .summary-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        .cart-items-preview {
            background: var(--soft-gray);
            border: 1px solid var(--light-border);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
            border-radius: 8px;
        }

        .cart-items-preview::-webkit-scrollbar {
            width: 6px;
        }

        .cart-items-preview::-webkit-scrollbar-track {
            background: transparent;
        }

        .cart-items-preview::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .cart-item-preview {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--light-border);
            align-items: center;
            font-size: 13px;
        }

        .cart-item-preview:last-child {
            border-bottom: none;
        }

        .cart-item-preview img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--light-border);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--light-border);
            font-size: 14px;
            color: var(--text-secondary);
        }

        .summary-row.total {
            border-bottom: none;
            border-top: 2px solid var(--light-border);
            padding: 16px 0;
            font-weight: 700;
            color: var(--text-primary);
        }

        .btn-place-order {
            width: 100%;
            padding: 16px;
            margin-top: 25px;
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);
        }

        .btn-place-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(42, 59, 126, 0.4);
        }

        .btn-place-order:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .terms-text {
            font-size: 11px;
            color: var(--text-secondary);
            text-align: center;
            margin-top: 12px;
        }

        .terms-text a {
            color: var(--primary-navy);
            text-decoration: none;
            font-weight: 600;
        }

        .terms-text a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .summary-card {
                position: relative;
                top: 0;
            }
        }

        @media (max-width: 600px) {
            .checkout-container {
                padding: 20px 15px;
            }

            .stepper-wrapper {
                gap: 10px;
                margin-bottom: 30px;
            }

            .stepper-wrapper::before {
                width: 50%;
            }

            .step-counter {
                width: 40px;
                height: 40px;
                font-size: 12px;
            }

            .step-name {
                font-size: 11px;
            }

            .payment-methods-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php 
    $path_prefix = '../';
    include '../Components/header.php'; 
    ?>

    <div class="checkout-container">
        <!-- Stepper -->
        <div class="stepper-wrapper">
            <div class="stepper-item completed">
                <div class="step-counter"><i class="fas fa-check"></i></div>
                <div class="step-name">Cart</div>
            </div>
            <div class="stepper-item active">
                <div class="step-counter">2</div>
                <div class="step-name">Payment</div>
            </div>
            <div class="stepper-item">
                <div class="step-counter">3</div>
                <div class="step-name">Confirm</div>
            </div>
        </div>

        <div class="checkout-content">
            <!-- Left Side: Payment Methods -->
            <div class="checkout-details">
                <!-- Delivery Address Section -->
                <div class="card" style="margin-bottom: 20px; padding: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <!-- Decorative Border -->
                    <div style="height: 4px; background: repeating-linear-gradient(45deg, #6fa6d6, #6fa6d6 33px, transparent 0, transparent 41px, #f18d9b 0, #f18d9b 74px, transparent 0, transparent 82px); width: 100%;"></div>
                    
                    <div style="padding: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h2 class="section-title" style="margin:0; font-size: 1.1rem; color: #2A3B7E; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-map-marker-alt" style="color: #ef4444;"></i> Delivery Address
                            </h2>
                            <a href="user-account.php?view=address" style="color: #2A3B7E; font-size: 0.85rem; font-weight: 600; text-decoration: none; text-transform: uppercase; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">Change</a>
                        </div>

                        <?php if ($full_addr_details): ?>
                            <div id="addressDisplay" style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; color: #1e293b; font-size: 1rem; margin-bottom: 8px;">
                                        <span id="addr_fullname"><?php echo htmlspecialchars($full_addr_details['fullname']); ?></span>
                                    </div>
                                    <div style="color: #334155; font-size: 0.95rem; line-height: 1.6;">
                                        <div><i class="fas fa-phone" style="color: #2A3B7E; width: 20px;"></i> <span id="addr_phone"><?php echo htmlspecialchars($full_addr_details['phone']); ?></span></div>
                                        <div><i class="fas fa-map-marker-alt" style="color: #2A3B7E; width: 20px;"></i> <span id="addr_address"><?php echo htmlspecialchars($full_addr_details['address']); ?></span></div>
                                        <div><i class="fas fa-city" style="color: #2A3B7E; width: 20px;"></i> <span id="addr_city"><?php echo htmlspecialchars($full_addr_details['city']); ?></span>, <span id="addr_zip"><?php echo htmlspecialchars($full_addr_details['zip']); ?></span></div>
                                    </div>
                                </div>
                                <button type="button" class="btn-detect-location" onclick="detectCurrentLocation()" style="padding: 10px 18px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.3s; white-space: nowrap; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                                    <i class="fas fa-location-crosshairs" id="detectIcon"></i> <span id="detectText">Detect Location</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <div style="padding: 30px; border: 2px dashed #e2e8f0; text-align: center; border-radius: 8px; background: #f8fafc;">
                                <div style="color: #94a3b8; margin-bottom: 12px; font-size: 0.95rem;">No delivery address found for your account.</div>
                                <a href="user-account.php?view=address" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 0.9rem; text-decoration: none; color: white; background-color: #2A3B7E; border-radius: 6px; font-weight: 500; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(42, 59, 126, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <i class="fas fa-plus"></i> Add Address
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <h2 class="section-title"><i class="fas fa-wallet" style="margin-right: 10px; color: #2A3B7E;"></i> Select Payment Method</h2>
                    
                    <div class="payment-methods-grid">
                        <!-- GCash -->
                        <label class="payment-card-label" onclick="selectMethod('gcash')">
                            <input type="radio" name="payment_method" value="gcash" id="radio-gcash">
                            <div class="payment-card-content">
                                <div class="payment-icon">
                                    <i class="fas fa-mobile-alt" style="font-size: 2rem; color: #4169E1;"></i>
                                </div>
                                <div class="payment-title">GCash</div>
                                <div class="payment-subtitle">E-wallet</div>
                            </div>
                        </label>

                        <!-- PayMaya -->
                        <label class="payment-card-label" onclick="selectMethod('paymaya')">
                            <input type="radio" name="payment_method" value="paymaya" id="radio-paymaya">
                            <div class="payment-card-content">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card" style="font-size: 2rem; color: #FF6B35;"></i>
                                </div>
                                <div class="payment-title">PayMaya</div>
                                <div class="payment-subtitle">E-wallet</div>
                            </div>
                        </label>

                        <!-- Mastercard -->
                        <label class="payment-card-label" onclick="selectMethod('card')">
                            <input type="radio" name="payment_method" value="card" id="radio-card">
                            <div class="payment-card-content">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card" style="font-size: 2rem; color: #FF5F00;"></i>
                                </div>
                                <div class="payment-title">Credit/Debit Card</div>
                                <div class="payment-subtitle">Visa, Mastercard</div>
                            </div>
                        </label>

                        <!-- Maya (Alternative) -->
                        <label class="payment-card-label" onclick="selectMethod('maya')">
                            <input type="radio" name="payment_method" value="maya" id="radio-maya">
                            <div class="payment-card-content">
                                <div class="payment-icon">
                                    <i class="fas fa-wallet" style="font-size: 2rem; color: #009FDF;"></i>
                                </div>
                                <div class="payment-title">Maya</div>
                                <div class="payment-subtitle">E-wallet</div>
                            </div>
                        </label>

                        <!-- BDO -->
                        <label class="payment-card-label" onclick="selectMethod('bdo')">
                            <input type="radio" name="payment_method" value="bdo" id="radio-bdo" style="display:none;">
                            <div class="payment-card-content">
                                <div class="payment-icon">
                                    <i class="fas fa-building" style="font-size: 2rem; color: #003D82;"></i>
                                </div>
                                <div class="payment-title">BDO</div>
                                <div class="payment-subtitle">Bank Transfer</div>
                            </div>
                        </label>

                        <!-- Cash on Delivery -->
                        <label class="payment-card-label" onclick="selectMethod('cod')">
                            <input type="radio" name="payment_method" value="cod" id="radio-cod" style="display:none;">
                            <div class="payment-card-content">
                                <div class="payment-icon">
                                    <i class="fas fa-hand-holding-usd" style="font-size: 2rem; color: #10B981;"></i>
                                </div>
                                <div class="payment-title">Cash on Delivery</div>
                                <div class="payment-subtitle">Pay at Delivery</div>
                            </div>
                        </label>
                    </div>

                    <!-- Method Specific Details -->
                    <div id="method-details-box" class="payment-option-details">
                        <div id="gcash-info" style="display:none;">
                            <strong>GCash Payment:</strong> You will be redirected to the GCash portal to authorize payment.
                        </div>
                        <div id="card-info" style="display:none;">
                            <div class="form-row">
                                <input type="text" placeholder="Card Number" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; width: 100%; margin-bottom: 10px;">
                                <div style="display: flex; gap: 10px;">
                                    <input type="text" placeholder="MM/YY" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; flex: 1;">
                                    <input type="text" placeholder="CVV" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; flex: 1;">
                                </div>
                            </div>
                        </div>
                        <div id="cod-info" style="display:none;">
                            <div style="display:flex; gap:12px; align-items:center;">
                                <i class="fas fa-info-circle" style="color: #2A3B7E; font-size: 1.2rem;"></i>
                                <span style="color: #334155;">You can pay the driver when the order arrives at your location.</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

                <div class="summary-card">
                    <h3 style="margin-bottom: 20px;">Order Summary</h3>
                    
                    <!-- Cart Items Preview -->
                    <?php if ($is_cart_checkout && count($cart_items) > 1): ?>
                        <div class="cart-items-preview">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item-preview">
                                    <img src="<?php 
                                        $img = $item['image'];
                                        $possible_paths = [
                                            '../image/Shop/' . basename($img),
                                            '../image/Best-seller/' . basename($img),
                                            '../image/' . basename($img),
                                            $img
                                        ];
                                        $final = '../image/logo.png';
                                        foreach ($possible_paths as $p) {
                                            if (file_exists($p)) { $final = $p; break; }
                                        }
                                        echo htmlspecialchars($final);
                                    ?>" alt="Product">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <div style="color: #64748b; font-size: 0.85rem;">x<?php echo $item['quantity']; ?></div>
                                    </div>
                                    <div style="font-weight: 700; color: #2A3B7E;">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Single Product Preview -->
                        <div style="display: flex; gap: 15px; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="Product" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="flex: 1;">
                                <h4 style="font-size: 0.95rem; font-weight: 600; color: #1e293b; margin: 0 0 5px 0;"><?php echo htmlspecialchars($product_name); ?></h4>
                                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Quantity: <?php echo $quantity; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-row">
                        <span>Subtotal <span style="font-size: 0.8rem; color: #777;">(VAT Included)</span></span>
                        <span>₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping Fee</span>
                        <span>₱<?php echo number_format($shipping_fee, 2); ?></span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total Payment</span>
                        <span style="color: #2A3B7E; font-size: 1.4rem;">₱<?php echo number_format($total_payment, 2); ?></span>
                    </div>

                    <!-- Hidden Form with Cart Items -->
                    <form id="paymentForm" action="Confirmation.php" method="POST" style="display:none;">
                        <input type="hidden" name="action" value="complete_purchase">
                        <input type="hidden" name="is_cart_checkout" value="<?php echo $is_cart_checkout ? '1' : '0'; ?>">
                        <input type="hidden" name="total_amount" value="<?php echo $total_payment; ?>">
                        <input type="hidden" name="payment_method" id="form-payment-method" value="">
                        <input type="hidden" name="full_name" value="<?php echo isset($full_addr_details['fullname']) ? htmlspecialchars($full_addr_details['fullname']) : ''; ?>">
                        <input type="hidden" name="phone_number" value="<?php echo isset($full_addr_details['phone']) ? htmlspecialchars($full_addr_details['phone']) : ''; ?>">
                        <input type="hidden" name="address" value="<?php echo isset($full_addr_details['address']) ? htmlspecialchars($full_addr_details['address']) : ''; ?>">
                        <input type="hidden" name="city" value="<?php echo isset($full_addr_details['city']) ? htmlspecialchars($full_addr_details['city']) : ''; ?>">
                        <input type="hidden" name="postal_code" value="<?php echo isset($full_addr_details['zip']) ? htmlspecialchars($full_addr_details['zip']) : ''; ?>">
                        
                        <!-- Add cart items as hidden fields -->
                        <?php foreach ($cart_items as $idx => $item): ?>
                            <input type="hidden" name="items[<?php echo $idx; ?>][product_id]" value="<?php echo intval($item['product_id']); ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][product_name]" value="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][price]" value="<?php echo floatval($item['price']); ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][quantity]" value="<?php echo intval($item['quantity']); ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][image]" value="<?php echo htmlspecialchars($item['image']); ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][cart_id]" value="<?php echo isset($item['id']) ? intval($item['id']) : 0; ?>">
                        <?php endforeach; ?>
                    </form>

                    <button class="btn-place-order" onclick="processPayment()">Place Order Now</button>
                    
                    <p style="font-size: 0.75rem; color: #888; text-align: center; margin-top: 15px;">
                        By placing your order, you agree to our <a href="#">Terms & Conditions</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMethod = '';

        function selectMethod(method) {
            selectedMethod = method;
            
            document.querySelectorAll('.payment-card-label').forEach(label => {
                label.classList.remove('active');
            });
            
            const radio = document.getElementById('radio-' + method);
            if (radio) {
                radio.checked = true;
                radio.closest('.payment-card-label').classList.add('active');
            }

            const detailsBox = document.getElementById('method-details-box');
            detailsBox.style.display = 'block';
            
            document.getElementById('gcash-info').style.display = 'none';
            document.getElementById('card-info').style.display = 'none';
            document.getElementById('cod-info').style.display = 'none';

            if (method === 'gcash') document.getElementById('gcash-info').style.display = 'block';
            if (method === 'card') document.getElementById('card-info').style.display = 'block';
            if (method === 'cod') document.getElementById('cod-info').style.display = 'block';
        }

        function processPayment() {
            if (!selectedMethod) {
                alert('Please select a payment method before placing order.');
                return;
            }
            
            document.getElementById('form-payment-method').value = selectedMethod;
            const btn = document.querySelector('.btn-place-order');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            setTimeout(() => {
                document.getElementById('paymentForm').submit();
            }, 1500);
        }

        // Geolocation Address Detection
        function detectCurrentLocation() {
            const btn = document.querySelector('.btn-detect-location');
            const detectIcon = document.getElementById('detectIcon');
            const detectText = document.getElementById('detectText');

            if (!navigator.geolocation) {
                showNotification('Geolocation not supported by your browser.', 'warning');
                return;
            }

            btn.disabled = true;
            detectIcon.classList.add('fa-spin');
            detectText.innerHTML = 'Detecting...';

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    try {
                        const { latitude, longitude } = position.coords;
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`,
                            { headers: { 'Accept-Language': 'en' } }
                        );
                        const data = await response.json();
                        
                        if (data.address) {
                            const address = data.address;
                            const street = address.road || address.street || '';
                            const city = address.city || address.town || address.municipality || '';
                            const province = address.state || '';
                            const zip = address.postcode || '';
                            
                            const fullAddress = [street, city, province].filter(Boolean).join(', ');
                            const cityProvince = [city, province].filter(Boolean).join(', ');
                            
                            if (document.getElementById('addr_address')) {
                                document.getElementById('addr_address').textContent = fullAddress || 'Detected location';
                                document.getElementById('addr_city').textContent = cityProvince || 'Unknown';
                                document.getElementById('addr_zip').textContent = zip || 'N/A';
                            }

                            document.querySelector('input[name="address"]').value = fullAddress;
                            document.querySelector('input[name="city"]').value = cityProvince;
                            document.querySelector('input[name="postal_code"]').value = zip;

                            showNotification('📍 Location detected successfully!', 'success');
                        }
                    } catch (error) {
                        showNotification('Could not detect address. Using default address.', 'warning');
                    } finally {
                        btn.disabled = false;
                        detectIcon.classList.remove('fa-spin');
                        detectText.innerHTML = 'Detect Location';
                    }
                },
                (error) => {
                    showNotification('Location access denied. Using default address.', 'info');
                    btn.disabled = false;
                    detectIcon.classList.remove('fa-spin');
                    detectText.innerHTML = 'Detect Location';
                }
            );
        }

        function showNotification(message, type = 'info') {
            const notif = document.createElement('div');
            notif.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                border-radius: 12px;
                font-weight: 500;
                z-index: 9999;
                animation: slideInRight 0.3s ease-out;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            `;

            if (type === 'success') {
                notif.style.background = '#10b981';
                notif.style.color = 'white';
            } else if (type === 'warning') {
                notif.style.background = '#f59e0b';
                notif.style.color = 'white';
            } else {
                notif.style.background = '#3b82f6';
                notif.style.color = 'white';
            }

            notif.textContent = message;
            document.body.appendChild(notif);

            setTimeout(() => {
                notif.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => notif.remove(), 300);
            }, 3000);
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(30px); }
                to { opacity: 1; transform: translateX(0); }
            }
            @keyframes slideOutRight {
                from { opacity: 1; transform: translateX(0); }
                to { opacity: 0; transform: translateX(30px); }
            }
        `;
        document.head.appendChild(style);
    </script>
    <div style="margin-top: 50px;">
        <?php
        $path_prefix = '../';
        include '../Components/footer.php';
        ?>
    </div>
</body>
</html>
