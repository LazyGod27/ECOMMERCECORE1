<?php
session_start();
include("../Database/config.php");

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

// 2. Handle POST from Payment.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_purchase') {
    $pname = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);
    $total = floatval($_POST['total_amount']);
    $method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $img = mysqli_real_escape_string($conn, $_POST['image_url']);
    $pid = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    $fname = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $addr = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $zip = mysqli_real_escape_string($conn, $_POST['postal_code']);

    // Insert Order
    $sql = "INSERT INTO orders (user_id, tracking_number, product_id, product_name, quantity, price, total_amount, full_name, phone_number, address, city, postal_code, payment_method, status, image_url, created_at) 
            VALUES ('$user_id', '$tracking_num', '$pid', '$pname', '$qty', '$price', '$total', '$fname', '$phone', '$addr', '$city', '$zip', '$method', 'Pending', '$img', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);
        $ref_id = "ORD-" . str_pad($order_id, 6, '0', STR_PAD_LEFT);
        
        // Clear Cart if it was a cart purchase
        if (isset($_GET['from_cart']) || $pname === 'Multiple Items') {
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
        }

        // --- CORE 1: CREATE FILE FOR ADMIN ---
        $admin_order_dir = "../Admin/Orders/";
        if (!is_dir($admin_order_dir)) mkdir($admin_order_dir, 0777, true);
        
        $order_data = [
            'order_id' => $order_id,
            'reference' => $ref_id,
            'tracking' => $tracking_num,
            'customer' => $fname,
            'product' => $pname,
            'amount' => $total,
            'date' => date('Y-m-d H:i:s')
        ];
        file_put_contents($admin_order_dir . $ref_id . ".json", json_encode($order_data, JSON_PRETTY_PRINT));

        // --- CORE 2 INTEGRATION ---
        // As requested: "Ayon dapat mapupunta sa core 2"
        // We create a directory for Core 2 integration if it doesn't exist
        $core2_integration_dir = "../../CORE2/Admin/Orders/";
        // Check if Desktop/CORE2 exists. If not, we might need to adjust path or just create it for demonstration.
        // For now, we use a relative path that assumes CORE1 and CORE2 are siblings.
        if (is_dir("../../CORE2")) {
            if (!is_dir($core2_integration_dir)) mkdir($core2_integration_dir, 0777, true);
            file_put_contents($core2_integration_dir . $ref_id . ".json", json_encode($order_data, JSON_PRETTY_PRINT));
        } else {
            // If CORE2 directory is not found, we create a local integration folder as fallback
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
        .confirmation-container {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .success-icon {
            font-size: 80px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        .btn-continue {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-continue:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <?php include '../Components/header.php'; ?>

    <main class="confirmation-container">
        <div class="success-icon">✓</div>
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for your purchase. Your order is now being processed.</p>
        <p>Reference ID: #<?php echo $ref_id; ?></p>
        
        <!-- FIXED: Link back to the main Shop page and Track Orders -->
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 30px;">
            <a href="../Shop/index.php" class="btn-continue" style="margin-top: 0;">Continue Shopping</a>
            <a href="user-account.php?view=orders" class="btn-continue" style="margin-top: 0; background-color: #2A3B7E;">My Purchase</a>
        </div>
    </main>

    <?php include '../Components/footer.php'; ?>
</body>
</html>