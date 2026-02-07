<?php
session_start();
include '../Database/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

// Get order details
// Supports single product (from Buy Now) or multiple (from Cart)
$product_name = isset($_GET['product_name']) ? $_GET['product_name'] : 'Multiple Items';
$item_price = isset($_GET['price']) ? floatval($_GET['price']) : 0;
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
$image = isset($_GET['image']) ? $_GET['image'] : '../image/logo.png';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

$subtotal = $item_price * $quantity;

// If amount is passed directly (from cart total)
if (isset($_GET['amount'])) {
    $subtotal = floatval($_GET['amount']);
}

$shipping_fee = 50.00;
$vat_rate = 0.12;
$vat_amount = $subtotal * $vat_rate;
$total_payment = $subtotal + $shipping_fee + $vat_amount;

$user_id = $_SESSION['user_id'];
$full_addr_details = null;

// Fetch Address logic
// 1. Try user_addresses table (Default)
$check_addr = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id='$user_id' AND is_default=1 LIMIT 1");
if (mysqli_num_rows($check_addr) > 0) {
    $full_addr_details = mysqli_fetch_assoc($check_addr);
} else {
    // 2. Try any address
    $check_addr_any = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id='$user_id' LIMIT 1");
    if (mysqli_num_rows($check_addr_any) > 0) {
        $full_addr_details = mysqli_fetch_assoc($check_addr_any);
    } else {
        // 3. Try users table (Legacy)
        $user_sql = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
        $u_row = mysqli_fetch_assoc($user_sql);
        if (!empty($u_row['address'])) {
             $full_addr_details = [
                'fullname' => $u_row['fullname'],
                'phone' => $u_row['phone'],
                'address' => $u_row['address'],
                'city' => $u_row['city'],
                'zip' => $u_row['zip']
             ];
        }
    }
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
            --accent-blue: #3b82f6;
            --soft-gray: #f8fafc;
            --border-color: #e2e8f0;
        }

        .payment-option-details {
            display: none;
            padding: 20px;
            background: #f1f5f9;
            margin-top: 15px;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown { 
            from { opacity: 0; transform: translateY(-10px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        
        .payment-card-label {
            cursor: pointer;
            position: relative;
        }

        .payment-card-content {
            border: 2px solid #f1f5f9;
            border-radius: 16px;
            padding: 20px 15px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
            height: 100%;
        }

        .payment-card-label:hover .payment-card-content {
            border-color: var(--accent-blue);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.1);
        }

        .payment-card-label.active .payment-card-content {
            border: 2px solid var(--primary-navy);
            background-color: #f0f7ff;
            box-shadow: 0 4px 12px rgba(42, 59, 126, 0.1);
        }

        .payment-card-label.active .payment-card-content::after {
            content: '\f058';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--primary-navy);
            font-size: 1.2rem;
        }

        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .payment-icon img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            filter: grayscale(10%);
            transition: filter 0.3s;
        }

        .payment-card-label:hover .payment-icon img {
            filter: grayscale(0%);
        }

        .summary-card {
            position: sticky;
            top: 100px;
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
                            <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                                <div style="font-weight: 700; color: #1e293b; min-width: 140px; font-size: 1rem;">
                                    <?php echo htmlspecialchars($full_addr_details['fullname']); ?>
                                    <div style="font-weight: 400; color: #64748b; font-size: 0.9rem; margin-top: 4px;">
                                        <?php echo htmlspecialchars($full_addr_details['phone']); ?>
                                    </div>
                                </div>
                                <div style="color: #334155; font-size: 0.95rem; flex: 1; line-height: 1.5;">
                                    <div style="margin-bottom: 8px;">
                                        <?php echo htmlspecialchars($full_addr_details['address']); ?><br>
                                        <?php echo htmlspecialchars($full_addr_details['city']); ?>, <?php echo htmlspecialchars($full_addr_details['zip']); ?>
                                    </div>
                                    <span style="display: inline-block; border: 1px solid #2A3B7E; color: #2A3B7E; background: #eff6ff; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; font-weight: 600; letter-spacing: 0.02em;">Default</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="padding: 30px; border: 2px dashed #e2e8f0; text-align: center; border-radius: 8px; background: #f8fafc;">
                                <div style="color: #94a3b8; margin-bottom: 12px; font-size: 0.95rem;">No delivery address found for your account.</div>
                                <a href="user-account.php?view=address" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 0.9rem; text-decoration: none; color: white; background-color: #2A3B7E; border-radius: 6px; font-weight: 500; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(42, 59, 126, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <i class="fas fa-plus"></i> Add New Address
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
                                    <img src="../image/Banks/Gcash.jpeg" alt="GCash" style="width: 50px; height: 50px; object-fit: contain;">
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
                                    <img src="../image/Banks/Paymaya.jpeg" alt="PayMaya" style="width: 50px; height: 50px; object-fit: contain;">
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
                                    <img src="../image/Banks/Master-card.png" alt="Card" style="width: 50px; height: 50px; object-fit: contain;">
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
                                    <img src="../image/Banks/Maya.png" alt="Maya" style="width: 50px; height: 50px; object-fit: contain;">
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
                                    <img src="../image/Banks/BDO.png" alt="BDO">
                                </div>
                                <div class="payment-title" style="font-weight: 700; font-size: 0.95rem; margin-top: 10px;">BDO</div>
                                <div class="payment-subtitle" style="font-size: 0.75rem; color: #64748b;">Bank Transfer</div>
                            </div>
                        </label>

                        <!-- Cash on Delivery -->
                        <label class="payment-card-label" onclick="selectMethod('cod')">
                            <input type="radio" name="payment_method" value="cod" id="radio-cod" style="display:none;">
                            <div class="payment-card-content">
                                <div class="payment-icon">
                                    <i class="fas fa-hand-holding-dollar" style="font-size: 40px; color: #10b981;"></i>
                                </div>
                                <div class="payment-title" style="font-weight: 700; font-size: 0.95rem; margin-top: 10px;">COD</div>
                                <div class="payment-subtitle" style="font-size: 0.75rem; color: #64748b;">Cash on Delivery</div>
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
                                <div class="form-group grid-row" style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div>
                                        <label style="display:block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">Card Number</label>
                                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" style="width:100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                    </div>
                                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                        <div>
                                            <label style="display:block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">Expiry</label>
                                            <input type="text" class="form-control" placeholder="MM/YY" style="width:100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                        </div>
                                        <div>
                                            <label style="display:block; font-size: 13px; font-weight: 600; margin-bottom: 5px;">CVV</label>
                                            <input type="text" class="form-control" placeholder="123" style="width:100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cod-info" style="display:none;">
                            <div style="display:flex; gap:12px; align-items:center;">
                                <i class="fas fa-info-circle" style="color: #10b981; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.9rem; color: #475569;">You will pay for your order via cash when it arrives at your doorstep. Please prepare the exact amount if possible.</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

                <div class="summary-card">
                    <h3>Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping Fee</span>
                        <span>₱<?php echo number_format($shipping_fee, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>VAT (12%)</span>
                        <span>₱<?php echo number_format($vat_amount, 2); ?></span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total Payment</span>
                        <span style="color: #2A3B7E; font-size: 1.4rem;">₱<?php echo number_format($total_payment, 2); ?></span>
                    </div>

                    <form id="paymentForm" action="Confirmation.php" method="POST" style="display:none;">
                        <input type="hidden" name="action" value="complete_purchase">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>">
                        <input type="hidden" name="price" value="<?php echo $item_price; ?>">
                        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
                        <input type="hidden" name="total_amount" value="<?php echo $total_payment; ?>">
                        <input type="hidden" name="payment_method" id="form-payment-method" value="">
                        <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($image); ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="full_name" value="<?php echo isset($full_addr_details['fullname']) ? htmlspecialchars($full_addr_details['fullname']) : ''; ?>">
                        <input type="hidden" name="phone_number" value="<?php echo isset($full_addr_details['phone']) ? htmlspecialchars($full_addr_details['phone']) : ''; ?>">
                        <input type="hidden" name="address" value="<?php echo isset($full_addr_details['address']) ? htmlspecialchars($full_addr_details['address']) : ''; ?>">
                        <input type="hidden" name="city" value="<?php echo isset($full_addr_details['city']) ? htmlspecialchars($full_addr_details['city']) : ''; ?>">
                        <input type="hidden" name="postal_code" value="<?php echo isset($full_addr_details['zip']) ? htmlspecialchars($full_addr_details['zip']) : ''; ?>">
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
            
            // Remove active class from all
            document.querySelectorAll('.payment-card-label').forEach(label => {
                label.classList.remove('active');
            });
            
            // Add active class to selected
            const radio = document.getElementById('radio-' + method);
            if (radio) {
                radio.checked = true;
                radio.closest('.payment-card-label').classList.add('active');
            }

            // Show relevant info box
            const detailsBox = document.getElementById('method-details-box');
            detailsBox.style.display = 'block';
            
            // Hide all sub-infos
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
            
            // Set payment method in hidden form
            document.getElementById('form-payment-method').value = selectedMethod;

            // Show loading state
            const btn = document.querySelector('.btn-place-order');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            setTimeout(() => {
                document.getElementById('paymentForm').submit();
            }, 1500);
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