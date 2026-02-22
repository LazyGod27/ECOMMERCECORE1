<?php
session_start();
include("../Database/config.php");
include("../Components/security.php");

// Import PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("../PHPMailer/src/PHPMailer.php");
require_once("../PHPMailer/src/Exception.php");
require_once("../PHPMailer/src/SMTP.php");

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

// Self-healing DB: Ensure 'orders' table has 'product_id' column
$pid_check = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'product_id'");
if (mysqli_num_rows($pid_check) == 0) {
    mysqli_query($conn, "ALTER TABLE orders ADD COLUMN product_id INT DEFAULT 0 AFTER tracking_number");
}

$user_id = $_SESSION['user_id'];
$ref_id = "ORD-" . rand(100000, 999999);
$tracking_num = "TRK-" . strtoupper(bin2hex(random_bytes(4)));

// Variables for display
$pname = '';
$total = 0;
$order_id = null;
$method = '';
$fname = '';
$img = '';
$items_ordered = [];

// 2. Handle POST from Payment.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_purchase') {
    $total = floatval($_POST['total_amount']);
    $method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    $fname = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $addr = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $zip = mysqli_real_escape_string($conn, $_POST['postal_code']);
    
    $is_cart_checkout = isset($_POST['is_cart_checkout']) && $_POST['is_cart_checkout'] == '1';
    
    // =====================================================
    // HANDLE MULTIPLE ITEMS FROM CART
    // =====================================================
    if (isset($_POST['items']) && is_array($_POST['items']) && count($_POST['items']) > 0) {
        // Generate a master order ID for grouping
        $master_tracking_num = "TRK-" . strtoupper(bin2hex(random_bytes(4)));
        
        // Create orders for each item in the cart
        $cart_ids_to_delete = [];
        $items_count = 0;
        
        foreach ($_POST['items'] as $item_data) {
            $pid = intval($item_data['product_id']);
            $pname_item = mysqli_real_escape_string($conn, $item_data['product_name']);
            $price_item = floatval($item_data['price']);
            $qty_item = intval($item_data['quantity']);
            $img_item = mysqli_real_escape_string($conn, $item_data['image']);
            $cart_id = isset($item_data['cart_id']) && $item_data['cart_id'] > 0 ? intval($item_data['cart_id']) : null;
            
            // Fetch shop_name from products table or cart table
            $shop_name_item = 'IMarket Official Store'; // Default
            if ($pid > 0) {
                // Try to get shop_name from products table
                $shop_check = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'shop_name'");
                if ($shop_check && mysqli_num_rows($shop_check) > 0) {
                    $shop_query = "SELECT COALESCE(shop_name, 'IMarket Official Store') as shop_name FROM products WHERE id = '$pid' LIMIT 1";
                    $shop_result = mysqli_query($conn, $shop_query);
                    if ($shop_result && mysqli_num_rows($shop_result) > 0) {
                        $shop_row = mysqli_fetch_assoc($shop_result);
                        $shop_name_item = !empty($shop_row['shop_name']) ? $shop_row['shop_name'] : 'IMarket Official Store';
                    }
                }
            }
            // If not found in products, try cart table
            if ($shop_name_item === 'IMarket Official Store' && $cart_id) {
                $cart_shop_query = "SELECT shop_name FROM cart WHERE id = '$cart_id' AND user_id = '$user_id' LIMIT 1";
                $cart_shop_result = mysqli_query($conn, $cart_shop_query);
                if ($cart_shop_result && mysqli_num_rows($cart_shop_result) > 0) {
                    $cart_shop_row = mysqli_fetch_assoc($cart_shop_result);
                    $shop_name_item = !empty($cart_shop_row['shop_name']) ? $cart_shop_row['shop_name'] : 'IMarket Official Store';
                }
            }
            
            // Calculate item total
            $item_total = $price_item * $qty_item;
            
            // Insert Order for this item
            $sql = "INSERT INTO orders (user_id, tracking_number, product_id, product_name, quantity, price, total_amount, full_name, phone_number, address, city, postal_code, payment_method, status, image_url, created_at) 
                    VALUES ('$user_id', '$master_tracking_num', '$pid', '$pname_item', '$qty_item', '$price_item', '$item_total', '$fname', '$phone', '$addr', '$city', '$zip', '$method', 'Pending', '$img_item', NOW())";
            
            if (mysqli_query($conn, $sql)) {
                $order_id = mysqli_insert_id($conn);
                $items_ordered[] = [
                    'order_id' => $order_id,
                    'product_id' => $pid,
                    'product_name' => $pname_item,
                    'quantity' => $qty_item,
                    'price' => $price_item,
                    'image' => $img_item,
                    'image_url' => $img_item,
                    'item_total' => $item_total,
                    'shop_name' => $shop_name_item,
                    'seller_name' => $shop_name_item
                ];
                
                // Track cart IDs to delete
                if ($cart_id) {
                    $cart_ids_to_delete[] = $cart_id;
                }
                
                $items_count++;
                
                // Set the first item's name as display product name
                if ($items_count === 1) {
                    $pname = $pname_item;
                    $img = $img_item;
                } else if ($items_count === 2) {
                    $pname = "Multiple Items (" . count($_POST['items']) . " items)";
                }
            }
        }
        
        // Delete specific cart items that were ordered
        if (count($cart_ids_to_delete) > 0) {
            $ids_str = implode(',', $cart_ids_to_delete);
            mysqli_query($conn, "DELETE FROM cart WHERE id IN ($ids_str) AND user_id = '$user_id'");
        }
        
        $ref_id = "ORD-" . str_pad($order_id ?? rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
    } else {
        // =====================================================
        // HANDLE SINGLE PRODUCT (BUY NOW)
        // =====================================================
        $pname = mysqli_real_escape_string($conn, $_POST['product_name'] ?? 'Product');
        $price = floatval($_POST['price'] ?? 0);
        $qty = intval($_POST['quantity'] ?? 1);
        $pid = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $img = mysqli_real_escape_string($conn, $_POST['image_url'] ?? '../image/logo.png');
        
        // Fetch shop_name from products table
        $shop_name_single = 'IMarket Official Store'; // Default
        if ($pid > 0) {
            $shop_check = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'shop_name'");
            if ($shop_check && mysqli_num_rows($shop_check) > 0) {
                $shop_query = "SELECT COALESCE(shop_name, 'IMarket Official Store') as shop_name FROM products WHERE id = '$pid' LIMIT 1";
                $shop_result = mysqli_query($conn, $shop_query);
                if ($shop_result && mysqli_num_rows($shop_result) > 0) {
                    $shop_row = mysqli_fetch_assoc($shop_result);
                    $shop_name_single = !empty($shop_row['shop_name']) ? $shop_row['shop_name'] : 'IMarket Official Store';
                }
            }
        }
        // Also check POST data for shop/store name
        if (isset($_POST['store']) && !empty($_POST['store'])) {
            $shop_name_single = mysqli_real_escape_string($conn, $_POST['store']);
        }
        
        // Insert Order
        $sql = "INSERT INTO orders (user_id, tracking_number, product_id, product_name, quantity, price, total_amount, full_name, phone_number, address, city, postal_code, payment_method, status, image_url, created_at) 
                VALUES ('$user_id', '$tracking_num', '$pid', '$pname', '$qty', '$price', '$total', '$fname', '$phone', '$addr', '$city', '$zip', '$method', 'Pending', '$img', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            $order_id = mysqli_insert_id($conn);
            $ref_id = "ORD-" . str_pad($order_id, 6, '0', STR_PAD_LEFT);
            
            $items_ordered[] = [
                'order_id' => $order_id,
                'product_id' => $pid,
                'product_name' => $pname,
                'quantity' => $qty,
                'price' => $price,
                'image' => $img,
                'image_url' => $img,
                'item_total' => $total,
                'shop_name' => $shop_name_single,
                'seller_name' => $shop_name_single
            ];
        }
    }
    
    // =====================================================
    // SEND ORDER TO CORE 2 SELLER SIDE API
    // =====================================================
    if ($order_id && !empty($items_ordered)) {
        include_once '../Database/send_order_to_core2.php';
        
        // Prepare order data for Core 2
        $core2OrderData = [
            'order_id' => $order_id,
            'order_reference' => $ref_id,
            'tracking_number' => $master_tracking_num ?? $tracking_num,
            'items' => array_map(function($item) {
                return [
                    'product_id' => $item['product_id'] ?? 0,
                    'product_name' => $item['product_name'] ?? '',
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                    'image_url' => $item['image_url'] ?? $item['image'] ?? ''
                ];
            }, $items_ordered),
            'customer' => [
                'full_name' => $fname,
                'phone_number' => $phone,
                'address' => $addr,
                'city' => $city,
                'postal_code' => $zip
            ],
            'payment_method' => $method,
            'total_amount' => $total
        ];
        
        // Send to Core 2 (non-blocking - don't fail order if Core 2 is down)
        try {
            $core2Result = sendOrderToCore2($conn, $core2OrderData);
            if (!$core2Result['success']) {
                error_log("Failed to send order {$order_id} to Core 2: " . $core2Result['message']);
                // Continue anyway - order is saved locally
            }
        } catch (Exception $e) {
            error_log("Exception sending order {$order_id} to Core 2: " . $e->getMessage());
            // Continue anyway - order is saved locally
        }
    }
    
    // =====================================================
    // SEND ORDER CONFIRMATION EMAIL
    // =====================================================
    if ($order_id && !empty($items_ordered)) {
        // Get user email from database
        $user_stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_row = $user_result->fetch_assoc();
        $user_email = $user_row['email'] ?? '';
        $user_stmt->close();
        
        if (!empty($user_email)) {
            // Create email content
            $items_html = '';
            foreach ($items_ordered as $item) {
                $item_total = $item['quantity'] * $item['price'];
                $items_html .= "
                    <tr style='border-bottom: 1px solid #e2e8f0;'>
                        <td style='padding: 12px; text-align: left;'>{$item['product_name']}</td>
                        <td style='padding: 12px; text-align: center;'>x{$item['quantity']}</td>
                        <td style='padding: 12px; text-align: right;'>₱" . number_format($item['price'], 2) . "</td>
                        <td style='padding: 12px; text-align: right; font-weight: bold; color: #2A3B7E;'>₱" . number_format($item_total, 2) . "</td>
                    </tr>";
            }
            
            $email_body = "
            <html>
            <head>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f8fafc; }
                    .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 20px; }
                    .header { background: linear-gradient(135deg, #2A3B7E 0%, #3b82f6 100%); color: white; padding: 30px; border-radius: 12px 12px 0 0; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 700; }
                    .order-info { background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0; }
                    .info-row { display: flex; justify-content: space-between; padding: 8px 0; }
                    .label { font-weight: 600; color: #2A3B7E; }
                    .value { color: #64748b; }
                    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    th { background: #f0f4f8; padding: 12px; text-align: left; font-weight: 600; color: #2A3B7E; border-bottom: 2px solid #e2e8f0; }
                    .total-row { background: #f0f4f8; font-weight: 700; color: #2A3B7E; }
                    .footer { text-align: center; color: #64748b; font-size: 12px; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
                    .btn { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #2A3B7E 0%, #3b82f6 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 20px; }
                    .success { color: #10b981; font-size: 48px; text-align: center; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='card'>
                        <div class='header'>
                            <div class='success'>✓</div>
                            <h1>Order Confirmed!</h1>
                            <p style='margin: 10px 0 0 0; opacity: 0.9;'>Your order has been successfully placed</p>
                        </div>
                        
                        <div class='order-info'>
                            <div class='info-row'>
                                <span class='label'>Order Reference:</span>
                                <span class='value' style='font-weight: 600; color: #2A3B7E;'>{$ref_id}</span>
                            </div>
                            <div class='info-row'>
                                <span class='label'>Tracking Number:</span>
                                <span class='value'>" . ($master_tracking_num ?? $tracking_num) . "</span>
                            </div>
                            <div class='info-row'>
                                <span class='label'>Order Date:</span>
                                <span class='value'>" . date('F d, Y g:i A') . "</span>
                            </div>
                            <div class='info-row'>
                                <span class='label'>Payment Method:</span>
                                <span class='value'>" . ucfirst(str_replace('_', ' ', $method)) . "</span>
                            </div>
                        </div>
                        
                        <h3 style='color: #2A3B7E; margin: 25px 0 15px 0;'>Order Items</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th style='text-align: center;'>Qty</th>
                                    <th style='text-align: right;'>Unit Price</th>
                                    <th style='text-align: right;'>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$items_html}
                                <tr class='total-row'>
                                    <td colspan='3' style='text-align: right; padding: 12px;'>Subtotal:</td>
                                    <td style='text-align: right; padding: 12px;'>₱" . number_format($total - 50, 2) . "</td>
                                </tr>
                                <tr class='total-row'>
                                    <td colspan='3' style='text-align: right; padding: 12px;'>Shipping Fee:</td>
                                    <td style='text-align: right; padding: 12px;'>₱50.00</td>
                                </tr>
                                <tr class='total-row' style='font-size: 18px;'>
                                    <td colspan='3' style='text-align: right; padding: 12px;'>TOTAL:</td>
                                    <td style='text-align: right; padding: 12px;'>₱" . number_format($total, 2) . "</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <h3 style='color: #2A3B7E; margin: 25px 0 15px 0;'>Delivery Address</h3>
                        <div style='background: #f0f4f8; padding: 15px; border-radius: 8px;'>
                            <p style='margin: 0; font-weight: 600;'>{$fname}</p>
                            <p style='margin: 5px 0; color: #64748b;'>{$addr}</p>
                            <p style='margin: 5px 0; color: #64748b;'>{$city}, {$zip}</p>
                            <p style='margin: 5px 0; color: #64748b;'>Phone: {$phone}</p>
                        </div>
                        
                        <p style='margin: 25px 0; line-height: 1.8; color: #64748b;'>
                            Thank you for your order! Your items will be carefully prepared and shipped to your address. 
                            You can track your order using the tracking number: <strong style='color: #2A3B7E;'>" . ($master_tracking_num ?? $tracking_num) . "</strong>
                        </p>
                        
                        <div style='background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <p style='margin: 0; color: #2A3B7E; font-weight: 600;'>📦 What's Next?</p>
                            <ul style='margin: 10px 0 0 20px; color: #64748b;'>
                                <li>Your order is being processed</li>
                                <li>We'll notify you when it ships</li>
                                <li>Track your package anytime</li>
                            </ul>
                        </div>
                        
                        <div class='footer'>
                            <p>If you have any questions, please don't hesitate to contact our support team.</p>
                            <p><strong>iMarket Support</strong> | support@imarket.com</p>
                            <p style='margin-top: 15px; font-size: 11px; color: #cbd5e1;'>© 2025 iMarket. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>";
            
            // Send email using PHPMailer
            try {
                $mail = new PHPMailer(true);
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'longkinog@gmail.com';
                $mail->Password = 'ssau zscp bbzr vrkh';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Recipients
                $mail->setFrom('support@imarket.com', 'iMarket Support');
                $mail->addAddress($user_email);
                $mail->addReplyTo('support@imarket.com', 'iMarket Support');
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Order Confirmation - ' . $ref_id . ' | iMarket';
                $mail->Body = $email_body;
                
                // Send the email
                $mail->send();
                
            } catch (Exception $e) {
                // Log error but don't block the order completion
                error_log("Email sending failed: " . $mail->ErrorInfo);
            }
        }
    }
    
    // =====================================================
    // CREATE ADMIN ORDER FILES
    // =====================================================
    if ($order_id) {
        $order_data = [
            'order_id' => $order_id,
            'reference' => $ref_id,
            'tracking' => $master_tracking_num ?? $tracking_num,
            'customer' => $fname,
            'products' => count($items_ordered) > 1 ? count($items_ordered) . ' items' : $pname,
            'total_amount' => $total,
            'payment_method' => $method,
            'date' => date('Y-m-d H:i:s')
        ];
        
        // --- CORE 1: CREATE FILE FOR ADMIN ---
        $admin_order_dir = "../Admin/Orders/";
        if (!is_dir($admin_order_dir)) mkdir($admin_order_dir, 0777, true);
        file_put_contents($admin_order_dir . $ref_id . ".json", json_encode($order_data, JSON_PRETTY_PRINT));

        // --- CORE 2 INTEGRATION ---
        $core2_integration_dir = "../../CORE2/Admin/Orders/";
        if (is_dir("../../CORE2")) {
            if (!is_dir($core2_integration_dir)) mkdir($core2_integration_dir, 0777, true);
            file_put_contents($core2_integration_dir . $ref_id . ".json", json_encode($order_data, JSON_PRETTY_PRINT));
        } else {
            $fallback_core2_dir = "../integration/core2/Orders/";
            if (!is_dir($fallback_core2_dir)) mkdir($fallback_core2_dir, 0777, true);
            file_put_contents($fallback_core2_dir . $ref_id . ".json", json_encode($order_data, JSON_PRETTY_PRINT));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - iMarket</title>
    <link rel="stylesheet" href="../css/components/header.css">
    <link rel="stylesheet" href="../css/components/footer.css">
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
            background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ff 50%, #f8fafc 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .confirmation-container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 50px 80px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.15);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
            }
            50% {
                box-shadow: 0 0 40px rgba(16, 185, 129, 0.4);
            }
        }

        .success-icon {
            font-size: 120px;
            color: var(--success-green);
            margin-bottom: 25px;
            animation: popIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: inline-block;
            width: 100%;
            text-align: center;
            filter: drop-shadow(0 4px 12px rgba(16, 185, 129, 0.3));
        }

        .success-badge {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            animation: slideUp 0.6s ease-out 0.1s both;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        h1 {
            font-size: 42px;
            font-weight: 800;
            color: var(--primary-navy);
            margin-bottom: 12px;
            letter-spacing: -1px;
            animation: slideUp 0.6s ease-out 0.2s both;
        }

        .confirmation-subtitle {
            font-size: 18px;
            color: var(--text-secondary);
            margin-bottom: 35px;
            animation: slideUp 0.6s ease-out 0.3s both;
            font-weight: 400;
        }

        /* Order Info Cards Grid */
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 35px;
            animation: slideUp 0.6s ease-out 0.4s both;
        }

        .order-info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            border: 1px solid var(--light-border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .order-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .order-info-card:hover::before {
            left: 100%;
        }

        .order-info-card:hover {
            border-color: var(--accent-blue);
            box-shadow: 0 8px 20px rgba(42, 59, 126, 0.1);
            transform: translateY(-4px);
        }

        .order-info-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .order-info-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary-navy);
            word-break: break-word;
        }

        .tracking-copy-btn {
            font-size: 12px;
            color: var(--accent-blue);
            background: transparent;
            border: none;
            cursor: pointer;
            font-weight: 600;
            margin-left: 8px;
            transition: color 0.3s ease;
        }

        .tracking-copy-btn:hover {
            color: var(--primary-dark);
        }

        /* Order Summary Cards */
        .cart-items-preview {
            background: linear-gradient(135deg, #f9fafb 0%, #f0f4f8 100%);
            border: 2px solid var(--light-border);
            border-radius: 16px;
            padding: 24px;
            margin: 35px 0;
            max-height: 380px;
            overflow-y: auto;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.03);
            animation: slideUp 0.6s ease-out 0.5s both;
        }

        .cart-items-preview::-webkit-scrollbar {
            width: 8px;
        }

        .cart-items-preview::-webkit-scrollbar-track {
            background: transparent;
        }

        .cart-items-preview::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .cart-items-preview::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .cart-item-preview {
            display: flex;
            gap: 16px;
            padding: 18px;
            border-bottom: 1px solid #e2e8f0;
            align-items: center;
            font-size: 14px;
            border-radius: 12px;
            margin-bottom: 12px;
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .cart-item-preview:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .cart-item-preview:hover {
            background: linear-gradient(135deg, #fff 0%, #f9fafb 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateX(4px);
        }

        .cart-item-preview img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid var(--light-border);
            transition: transform 0.3s ease;
        }

        .cart-item-preview:hover img {
            transform: scale(1.05);
        }

        .cart-item-preview > div:nth-child(2) {
            flex: 1;
        }

        .cart-item-preview h4 {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
            font-size: 15px;
        }

        .cart-item-preview p {
            color: var(--text-secondary);
            font-size: 13px;
            margin: 0;
        }

        .cart-item-price {
            font-weight: 800;
            color: var(--success-green);
            white-space: nowrap;
            font-size: 16px;
        }

        /* Single/Multiple Item Display */
        .item-display-card {
            margin: 35px 0;
            padding: 28px;
            border: 2px solid var(--light-border);
            border-radius: 16px;
            background: white;
            transition: all 0.3s ease;
            animation: slideUp 0.6s ease-out 0.5s both;
        }

        .item-display-card:hover {
            border-color: var(--accent-blue);
            box-shadow: 0 12px 32px rgba(42, 59, 126, 0.15);
            transform: translateY(-6px);
        }

        .item-display-card.multiple {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            border: 2px dashed var(--light-border);
        }

        .summary-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--light-border);
        }

        .summary-title i {
            color: var(--accent-blue);
            font-size: 24px;
        }

        /* Single Item Details */
        .single-item-wrapper {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .single-item-image {
            position: relative;
        }

        .single-item-image img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid var(--light-border);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .single-item-image:hover img {
            transform: scale(1.08);
        }

        .single-item-details {
            flex: 1;
        }

        .single-item-details h4 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .single-item-details p {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .single-item-details .price {
            font-size: 28px;
            font-weight: 900;
            color: var(--success-green);
            margin-top: 16px;
        }

        /* Action Buttons */
        .btn-action-group {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 45px;
            padding-top: 35px;
            border-top: 2px solid var(--light-border);
            animation: slideUp 0.6s ease-out 0.6s both;
        }

        .btn-continue {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 16px 36px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            gap: 10px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
            position: relative;
            overflow: hidden;
        }

        .btn-continue::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-continue:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-continue:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
        }

        .btn-continue:active {
            transform: translateY(-1px);
        }

        .btn-continue.primary {
            background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%);
            color: white;
        }

        .btn-continue.secondary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .btn-continue.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-continue i {
            relative: relative;
            z-index: 1;
        }

        /* Footer Text */
        .terms-text {
            font-size: 13px;
            color: var(--text-secondary);
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--light-border);
            animation: slideUp 0.6s ease-out 0.7s both;
        }

        .terms-text a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
        }

        .terms-text a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-blue);
            transition: width 0.3s ease;
        }

        .terms-text a:hover::after {
            width: 100%;
        }

        .terms-text a:hover {
            color: var(--primary-navy);
        }

        /* Responsive adjustments */
        @media (max-width: 800px) {
            .order-info-grid {
                grid-template-columns: 1fr;
            }

            .single-item-wrapper {
                flex-direction: column;
                align-items: center;
            }

            h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <?php include '../Components/header.php'; ?>

    <main class="confirmation-container">
        <div style="text-align: center;">
            <span class="success-badge">✓ Order Confirmed</span>
            <h1>Thank You for Your Order!</h1>
            <p class="confirmation-subtitle">Your order has been successfully received and is being prepared for shipment.</p>
        </div>

        <!-- Order Info Grid -->
        <div class="order-info-grid">
            <div class="order-info-card">
                <div class="order-info-label"><i class="fas fa-tag"></i> Order Reference</div>
                <div class="order-info-value">#<?php echo $ref_id; ?></div>
            </div>
            
            <div class="order-info-card">
                <div class="order-info-label"><i class="fas fa-barcode"></i> Tracking Number</div>
                <div class="order-info-value" style="display: flex; align-items: center; justify-content: space-between;">
                    <span><?php echo htmlspecialchars($master_tracking_num ?? $tracking_num); ?></span>
                    <button class="tracking-copy-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($master_tracking_num ?? $tracking_num); ?>')">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
            </div>
            
            <div class="order-info-card">
                <div class="order-info-label"><i class="fas fa-clock"></i> Order Date & Time</div>
                <div class="order-info-value"><?php echo date('F d, Y'); ?></div>
                <div style="color: var(--text-secondary); font-size: 12px; margin-top: 4px;"><?php echo date('g:i A'); ?></div>
            </div>
            
            <div class="order-info-card">
                <div class="order-info-label"><i class="fas fa-credit-card"></i> Payment Method</div>
                <div class="order-info-value"><?php echo ucwords(str_replace('_', ' ', $method)); ?></div>
            </div>
        </div>

        <!-- Product Summary -->
        <?php if (count($items_ordered) > 1): ?>
            <!-- Multiple Items Display -->
            <div class="item-display-card multiple">
                <div class="summary-title">
                    <i class="fas fa-shopping-bag"></i> Order Summary (<?php echo count($items_ordered); ?> items)
                </div>
                <div class="cart-items-preview">
                    <?php foreach ($items_ordered as $item): ?>
                        <div class="cart-item-preview">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product">
                            <div>
                                <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p>Qty: <strong><?php echo $item['quantity']; ?></strong> × ₱<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="cart-item-price">₱<?php echo number_format($item['item_total'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="padding: 20px; background: linear-gradient(135deg, #f0f4f8 0%, #f8fafc 100%); border-radius: 12px; text-align: right; border: 2px solid var(--light-border); margin-top: 20px;">
                    <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">Total Amount</div>
                    <span style="font-size: 24px; font-weight: 900; color: var(--success-green);">₱<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        <?php elseif (count($items_ordered) > 0): ?>
            <!-- Single Item Display -->
            <div class="item-display-card">
                <div class="summary-title">
                    <i class="fas fa-box"></i> Your Item
                </div>
                <div class="single-item-wrapper">
                    <div class="single-item-image">
                        <img src="<?php echo htmlspecialchars($items_ordered[0]['image']); ?>" alt="Product">
                    </div>
                    <div class="single-item-details">
                        <h4><?php echo htmlspecialchars($items_ordered[0]['product_name']); ?></h4>
                        <p><i class="fas fa-cube" style="color: var(--accent-blue);"></i> <strong>Quantity:</strong> <?php echo $items_ordered[0]['quantity']; ?></p>
                        <p><i class="fas fa-tag" style="color: var(--accent-blue);"></i> <strong>Unit Price:</strong> ₱<?php echo number_format($items_ordered[0]['price'], 2); ?></p>
                        <div class="price"><i class="fas fa-money-bill-wave" style="color: var(--success-green); margin-right: 8px;"></i>₱<?php echo number_format($items_ordered[0]['item_total'], 2); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="btn-action-group">
            <a href="../Shop/index.php" class="btn-continue secondary">
                <i class="fas fa-shopping-cart"></i> Continue Shopping
            </a>
            <a href="user-account.php?view=orders" class="btn-continue primary">
                <i class="fas fa-list-check"></i> View My Orders
            </a>
            <a href="Tracking.php?order_id=<?php echo $items_ordered[0]['order_id'] ?? ''; ?>" class="btn-continue success">
                <i class="fas fa-truck"></i> Track Order
            </a>
        </div>

        <p class="terms-text">
            📧 A confirmation email has been sent to your inbox. Haven't received it? Check your spam folder or <a href="#">contact support</a>. Thank you for shopping with us!
        </p>
    </main>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.style.color = 'var(--success-green)';
                
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.style.color = 'var(--accent-blue)';
                }, 2000);
            });
        }
    </script>

    <?php include '../Components/footer.php'; ?>
</body>
</html>
