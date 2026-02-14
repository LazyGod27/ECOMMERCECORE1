<?php
/**
 * AJAX Content Loader for User Account Dashboard
 * Loads individual view content without page reload
 * Returns: JSON with HTML content for the requested view
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized - Please login'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$user_id = intval($_SESSION['user_id']); // Ensure it's an integer
$view = isset($_GET['view']) ? sanitize_input($_GET['view']) : 'profile';
$tab = isset($_GET['tab']) ? sanitize_input($_GET['tab']) : 'All';

// Database connection
include('../Database/config.php');

// Verify connection
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Helper function to generate filter SQL for orders
function get_filter_sql($tab) {
    $filter_sql = '';
    if ($tab == 'To Pay') $filter_sql = " AND status='Pending'";
    elseif ($tab == 'To Ship') $filter_sql = " AND status='Paid'";
    elseif ($tab == 'To Receive') $filter_sql = " AND status='Shipped'";
    elseif ($tab == 'Completed') $filter_sql = " AND status='Delivered' OR status='Completed'";
    elseif ($tab == 'Cancelled') $filter_sql = " AND status='Cancelled'";
    return $filter_sql;
}

try {
    ob_start();
    
    // Determine which view to load
    switch ($view) {
        case 'profile':
            load_profile_view($conn, $user_id);
            break;
        case 'orders':
            load_orders_view($conn, $user_id, $tab);
            break;
        case 'tracking':
            load_tracking_view($conn, $user_id);
            break;
        case 'banks':
            load_banks_view();
            break;
        case 'address':
            load_address_view($conn, $user_id);
            break;
        case 'password':
            load_password_view();
            break;
        case 'notifications':
            load_notifications_view($conn, $user_id);
            break;
        default:
            throw new Exception('Invalid view: ' . $view);
    }
    
    $content = ob_get_clean();
    
    // Validate content is not empty
    if (empty($content)) {
        throw new Exception('No content generated for view: ' . $view);
    }
    
    // Ensure content is properly encoded for JSON
    echo json_encode([
        'success' => true, 
        'html' => $content
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// ======================== VIEW LOADERS ========================

function load_profile_view($conn, $user_id) {
    $user_sql = "SELECT * FROM users WHERE id = '" . mysqli_real_escape_string($conn, $user_id) . "'";
    $user_res = mysqli_query($conn, $user_sql);
    
    if (!$user_res) {
        throw new Exception('Error fetching user data: ' . mysqli_error($conn));
    }
    
    $user = mysqli_fetch_assoc($user_res);
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    $fullname = htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($user['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars($user['city'] ?? '', ENT_QUOTES, 'UTF-8');
    $zip = htmlspecialchars($user['zip'] ?? '', ENT_QUOTES, 'UTF-8');
    $gender = htmlspecialchars($user['gender'] ?? '', ENT_QUOTES, 'UTF-8');
    $birthdate = htmlspecialchars($user['birthdate'] ?? '', ENT_QUOTES, 'UTF-8');
    $profile_pic = htmlspecialchars($user['profile_pic'] ?? '', ENT_QUOTES, 'UTF-8');
    ?>
    <div class="content-header">
        <div class="content-title">My Profile</div>
        <div class="content-subtitle">Manage your personal information</div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" style="max-width: 700px;">
        <input type="hidden" name="action" value="update_profile">

        <div class="profile-picture-section" style="display: flex; align-items: center; gap: 25px; padding: 30px; background: #f8f9fa; border-radius: 12px; margin-bottom: 30px;">
            <div style="position: relative;">
                <img id="profilePreview" src="<?php echo $profile_pic ? '../uploads/profile/' . $profile_pic : '../image/logo.png'; ?>" 
                    alt="Profile" style="width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 3px solid #2A3B7E; background: white;">
                <label for="profileInput" style="position: absolute; bottom: 0; right: 0; background: #2A3B7E; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white; font-size: 18px;">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="profileInput" name="profile_pic" accept="image/*" style="display: none;" onchange="previewImage(this)">
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 700; font-size: 18px; color: #1a1a1a; margin-bottom: 6px;">Profile Picture</div>
                <p style="color: #666; font-size: 14px; margin: 0; margin-bottom: 8px;">Click the camera icon to upload a new photo</p>
                <p style="color: #999; font-size: 12px; margin: 0;">Supported formats: JPG, PNG (Max 5MB)</p>
            </div>
        </div>

        <div class="profile-input-group">
            <div class="profile-input-label">Full Name</div>
            <div class="profile-input-field">
                <input type="text" name="fullname" value="<?php echo $fullname; ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
            </div>
        </div>

        <div class="profile-input-group">
            <div class="profile-input-label">Email Address</div>
            <div class="profile-input-field">
                <input type="email" value="<?php echo $email; ?>" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: #f8f9fa; opacity: 0.7; cursor: not-allowed;">
                <small style="color: #999; display: block; margin-top: 6px;">Email cannot be changed</small>
            </div>
        </div>

        <div class="profile-input-group">
            <div class="profile-input-label">Phone Number</div>
            <div class="profile-input-field">
                <input type="tel" name="phone" value="<?php echo $phone; ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
            </div>
        </div>

        <div class="profile-input-group">
            <div class="profile-input-label">Address</div>
            <div class="profile-input-field">
                <textarea name="address" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit;"><?php echo $address; ?></textarea>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
            <div class="profile-input-group">
                <div class="profile-input-label">City / Province</div>
                <div class="profile-input-field">
                    <input type="text" name="city" value="<?php echo $city; ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                </div>
            </div>
            <div class="profile-input-group">
                <div class="profile-input-label">Postal Code</div>
                <div class="profile-input-field">
                    <input type="text" name="zip" value="<?php echo $zip; ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
            <div class="profile-input-group">
                <div class="profile-input-label">Gender</div>
                <div class="profile-input-field">
                    <select name="gender" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                        <option value="">Not Specified</option>
                        <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="profile-input-group">
                <div class="profile-input-label">Birth Date</div>
                <div class="profile-input-field">
                    <input type="date" name="birthdate" value="<?php echo $birthdate; ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                </div>
            </div>
        </div>

        <div class="profile-actions" style="display: flex; gap: 12px; margin-top: 35px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
            <button type="submit" style="padding: 12px 35px; background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.3px;">Save Changes</button>
            <button type="reset" style="padding: 12px 35px; background: white; color: #2A3B7E; border: 2px solid #2A3B7E; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.3px;">Reset</button>
        </div>
    </form>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <?php
}

function load_orders_view($conn, $user_id, $tab) {
    $filter_sql = get_filter_sql($tab);
    
    // Escape user_id for safety
    $user_id_safe = mysqli_real_escape_string($conn, $user_id);
    
    // Check if orders table exists first
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
    if (mysqli_num_rows($check_table) == 0) {
        ?>
        <div class="content-header">
            <div class="content-title">My Purchases</div>
            <div class="content-subtitle">View and track your orders</div>
        </div>

        <div class="empty-state" style="background: #f8f9fa; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 50px 20px; text-align: center;">
            <i class="fas fa-shopping-bag" style="font-size: 45px; color: #cbd5e0; margin-bottom: 20px; display: block;"></i>
            <p style="font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px;">No orders yet</p>
            <p style="font-size: 14px; color: #666; margin: 0;">Start shopping to see your orders here</p>
        </div>
        <?php
        return;
    }
    
    // Fetch orders directly from orders table
    $orders_sql = "SELECT 
        id, order_number, created_at, status, total_amount, product_name, quantity, image_url, product_id
    FROM orders
    WHERE user_id = '$user_id_safe' $filter_sql
    ORDER BY created_at DESC
    LIMIT 50";
    
    $orders_res = mysqli_query($conn, $orders_sql);
    
    if (!$orders_res) {
        throw new Exception('Error fetching orders: ' . mysqli_error($conn));
    }
    
    ?>
    <div class="content-header">
        <div class="content-title">My Purchases</div>
        <div class="content-subtitle">View and track your orders</div>
    </div>

    <!-- Tab Navigation -->
    <div class="order-tabs" style="display: flex; gap: 0; margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; background: #fafbfc; border-radius: 10px 10px 0 0; overflow: hidden;">
        <?php $tabs = ['All', 'To Pay', 'To Ship', 'To Receive', 'Completed', 'Cancelled']; ?>
        <?php foreach ($tabs as $t): ?>
            <button class="order-tab-btn <?php echo $tab == $t ? 'active' : ''; ?>" 
                data-tab="<?php echo $t; ?>"
                style="padding: 14px 20px; border: none; background: transparent; cursor: pointer; font-weight: 600; color: <?php echo $tab == $t ? '#2A3B7E' : '#888'; ?>; border-bottom: 3px solid <?php echo $tab == $t ? '#2A3B7E' : 'transparent'; ?>; transition: all 0.3s ease; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;" onmouseover="if(this.dataset.tab !== '<?php echo $tab; ?>') this.style.color='#666'" onmouseout="if(this.dataset.tab !== '<?php echo $tab; ?>') this.style.color='#888'">
                <?php echo $t; ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Orders List -->
    <div class="orders-list">
        <?php if (mysqli_num_rows($orders_res) === 0): ?>
            <div class="empty-state" style="background: #fff; border: 2px dashed #e8e8e8; border-radius: 12px; padding: 50px 20px; text-align: center;">
                <i class="fas fa-shopping-bag" style="font-size: 45px; color: #ddd; margin-bottom: 20px;"></i>
                <p style="font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px;">No orders yet</p>
                <p style="font-size: 14px; color: #888;">Start shopping to see your orders here</p>
            </div>
        <?php else: ?>
            <?php while ($order = mysqli_fetch_assoc($orders_res)): ?>
                <div class="order-card" style="background: #fff; border: 2px solid #f0f0f0; padding: 24px; margin-bottom: 18px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 20px rgba(42,59,126,0.1)'; this.style.borderColor='rgba(42,59,126,0.2)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.borderColor='#f0f0f0'">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;">
                        <div>
                            <div style="font-weight: 700; font-size: 16px; color: #1a1a1a; margin-bottom: 4px;">Order <span style="color: #2A3B7E;">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span></div>
                            <div style="font-size: 13px; color: #888; font-weight: 500;">
                                <i class="fas fa-calendar-alt" style="margin-right: 6px;"></i><?php echo date('M d, Y • h:i A', strtotime($order['created_at'])); ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 12px; font-weight: 700; padding: 8px 14px; border-radius: 8px; display: inline-block;
                                background: <?php
                                    $status_colors = [
                                        'Pending' => '#fffaf0',
                                        'Paid' => '#f0f9ff',
                                        'Shipped' => '#faf5ff',
                                        'Delivered' => '#f0fdf4',
                                        'Completed' => '#f0fdf4',
                                        'Cancelled' => '#fef2f2'
                                    ];
                                    echo $status_colors[$order['status']] ?? '#f0f0f0';
                                ?>;
                                color: <?php
                                    $text_colors = [
                                        'Pending' => '#b45309',
                                        'Paid' => '#0369a1',
                                        'Shipped' => '#7c3aed',
                                        'Delivered' => '#16a34a',
                                        'Completed' => '#16a34a',
                                        'Cancelled' => '#dc2626'
                                    ];
                                    echo $text_colors[$order['status']] ?? '#333';
                                ?>; text-transform: uppercase; letter-spacing: 0.3px;">
                                <?php echo $order['status']; ?>
                            </div>
                        </div>
                    </div>
                    <div style="color: #888; font-size: 13px; margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid #f0f0f0;">
                        <i class="fas fa-box" style="margin-right: 6px; color: #2A3B7E;"></i><strong><?php echo htmlspecialchars($order['product_name']); ?></strong> (Qty: <?php echo $order['quantity']; ?>)
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 18px; font-weight: 700; color: #2A3B7E;">
                            ₱<?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="view-order-btn" data-order-id="<?php echo $order['id']; ?>"
                                style="padding: 8px 20px; border: 1px solid #2A3B7E; color: #2A3B7E; background: #fff; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.3px;" onmouseover="this.style.backgroundColor='rgba(42,59,126,0.05)'" onmouseout="this.style.backgroundColor='#fff'">
                                View Order
                            </button>
                            <?php if (in_array($order['status'], ['Pending', 'Paid', 'Shipped'])): ?>
                                <button class="track-order-btn" data-order-id="<?php echo $order['id']; ?>" onclick="trackOrder(<?php echo $order['id']; ?>)"
                                    style="padding: 8px 20px; background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%); color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.3px; box-shadow: 0 3px 10px rgba(42, 59, 126, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 16px rgba(42, 59, 126, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(42, 59, 126, 0.2)'">
                                    <i class="fas fa-truck" style="margin-right: 6px;"></i> Track
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <?php
}

function load_tracking_view($conn, $user_id) {
    $orders_sql = "SELECT * FROM orders WHERE customer_id = '$user_id' AND status IN ('Paid', 'Shipped') ORDER BY created_at DESC LIMIT 10";
    $orders_res = mysqli_query($conn, $orders_sql);
    
    ?>
    <div class="content-header">
        <div class="content-title">Order Tracking</div>
        <div class="content-subtitle">Real-time tracking of your active orders</div>
    </div>

    <div class="tracking-list">
        <?php if (mysqli_num_rows($orders_res) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-box" style="font-size: 40px; color: #ccc; margin-bottom: 20px;"></i>
                <p>No active orders to track</p>
            </div>
        <?php else: ?>
            <?php while ($order = mysqli_fetch_assoc($orders_res)): ?>
                <div style="background: #fff; border: 1px solid #eee; padding: 25px; margin-bottom: 20px; border-radius: 4px;">
                    <div style="font-weight: 600; font-size: 15px; margin-bottom: 20px;">
                        Order #<?php echo $order['order_number']; ?> - <?php echo $order['status']; ?>
                    </div>

                    <!-- Timeline -->
                    <div style="display: flex; gap: 0; margin-bottom: 30px; position: relative;">
                        <?php
                        $step_map = ['Pending' => 1, 'Paid' => 2, 'Shipped' => 3, 'Delivered' => 4, 'Completed' => 5];
                        $step_idx = $step_map[$order['status']] ?? 1;
                        ?>
                        <!-- Step 1 -->
                        <div class="track-step" style="flex: 1; text-align: center;">
                            <div style="margin-bottom: 10px;"><i class="fas fa-check-circle" 
                                style="font-size: 24px; color: <?php echo $step_idx >= 1 ? '#2A3B7E' : '#ddd'; ?>;"></i></div>
                            <div style="font-size: 12px; color: #666;">Confirmed</div>
                        </div>
                        <!-- Step 2 -->
                        <div class="track-step" style="flex: 1; text-align: center;">
                            <div style="margin-bottom: 10px;"><i class="fas fa-money-bill" 
                                style="font-size: 24px; color: <?php echo $step_idx >= 2 ? '#2A3B7E' : '#ddd'; ?>;"></i></div>
                            <div style="font-size: 12px; color: #666;">Payment</div>
                        </div>
                        <!-- Step 3 -->
                        <div class="track-step" style="flex: 1; text-align: center;">
                            <div style="margin-bottom: 10px;"><i class="fas fa-box-open" 
                                style="font-size: 24px; color: <?php echo $step_idx >= 3 ? '#2A3B7E' : '#ddd'; ?>;"></i></div>
                            <div style="font-size: 12px; color: #666;">Shipped</div>
                        </div>
                        <!-- Step 4 -->
                        <div class="track-step" style="flex: 1; text-align: center;">
                            <div style="margin-bottom: 10px;"><i class="fas fa-shipping-fast" 
                                style="font-size: 24px; color: <?php echo $step_idx >= 4 ? '#2A3B7E' : '#ddd'; ?>;"></i></div>
                            <div style="font-size: 12px; color: #666;">On the way</div>
                        </div>
                        <!-- Step 5 -->
                        <div class="track-step" style="flex: 1; text-align: center;">
                            <div style="margin-bottom: 10px;"><i class="fas fa-star" 
                                style="font-size: 24px; color: <?php echo $step_idx >= 5 ? '#2A3B7E' : '#ddd'; ?>;"></i></div>
                            <div style="font-size: 12px; color: #666;">Completed</div>
                        </div>
                    </div>

                    <!-- Progress Line -->
                    <div style="display: flex; margin-bottom: 25px; position: relative; height: 4px; background: #eee; border-radius: 2px;">
                        <div style="height: 100%; background: #2A3B7E; border-radius: 2px; width: <?php echo ($step_idx / 5) * 100; ?>%; transition: width 0.5s;"></div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <?php
}

function load_banks_view() {
    ?>
    <div class="content-header">
        <div class="content-title">Credit / Debit Cards</div>
        <div class="content-subtitle">Manage your payment methods</div>
    </div>

    <div class="empty-state">
        <i class="fas fa-credit-card" style="font-size: 40px; margin-bottom: 20px; color: #ccc;"></i>
        <p>You have not added any cards yet.</p>
        <button class="btn-primary" style="margin-top: 10px;">+ Add New Card</button>
    </div>
    <?php
}

function load_address_view($conn, $user_id) {
    $addr_sql = "SELECT * FROM user_addresses WHERE user_id = '" . mysqli_real_escape_string($conn, $user_id) . "' ORDER BY is_default DESC, created_at DESC";
    $addr_res = mysqli_query($conn, $addr_sql);
    
    if (!$addr_res) {
        throw new Exception('Error fetching addresses: ' . mysqli_error($conn));
    }
    
    $user_addresses = mysqli_fetch_all($addr_res, MYSQLI_ASSOC);
    
    ?>
    <div class="content-header">
        <div class="content-title">My Addresses</div>
        <div class="content-subtitle">Manage your shipping addresses</div>
    </div>

    <div class="address-list">
        <?php if (empty($user_addresses)): ?>
            <div class="empty-state" style="background: #f8f9fa; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 50px 20px; text-align: center;">
                <i class="fas fa-map-marker-alt" style="font-size: 45px; color: #cbd5e0; margin-bottom: 20px; display: block;"></i>
                <p style="font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px;">No saved addresses</p>
                <p style="font-size: 14px; color: #666; margin: 0;">Add your first address to get started</p>
            </div>
        <?php else: ?>
            <?php foreach ($user_addresses as $addr): 
                $addr_data = htmlspecialchars(json_encode($addr), ENT_QUOTES, 'UTF-8');
            ?>
                <div class="address-card" style="border: 2px solid #f0f0f0; padding: 25px; border-radius: 12px; position: relative; margin-bottom: 18px; background: #fff; transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 20px rgba(42,59,126,0.1)'; this.style.borderColor='rgba(42,59,126,0.2)'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#f0f0f0'">
                    
                    <div style="position: absolute; right: 20px; top: 20px; display: flex; gap: 15px;">
                        <button class="edit-address-btn" data-addr-id="<?php echo $addr['id']; ?>" onclick="editAddressModal(<?php echo htmlspecialchars(json_encode($addr), ENT_QUOTES, 'UTF-8'); ?>)" style="background: none; border: none; color: #2A3B7E; cursor: pointer; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 4px; padding: 6px 12px; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.3px;" onmouseover="this.style.backgroundColor='rgba(42,59,126,0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="delete-address-btn" data-id="<?php echo $addr['id']; ?>" onclick="deleteAddressConfirm(<?php echo $addr['id']; ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 4px; padding: 6px 12px; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.3px;" onmouseover="this.style.backgroundColor='rgba(239,68,68,0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>

                    <div style="font-weight: 700; font-size: 16px; margin-bottom: 8px; color: #1a1a1a;">
                        <?php echo htmlspecialchars($addr['fullname'], ENT_QUOTES, 'UTF-8'); ?>
                        <span style="font-weight: 500; color: #666; margin-left: 12px;">
                            <i class="fas fa-phone" style="margin-right: 6px;"></i><?php echo htmlspecialchars($addr['phone'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>

                    <div style="color: #555; font-size: 14px; line-height: 1.7;">
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #2A3B7E; width: 20px;"></i>
                            <?php echo htmlspecialchars($addr['address'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div>
                            <i class="fas fa-building" style="color: #2A3B7E; width: 20px;"></i>
                            <?php echo htmlspecialchars($addr['city'] . ', ' . $addr['zip'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>

                    <div style="margin-top: 18px; display: flex; gap: 12px; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 18px;">
                        <?php if ($addr['is_default']): ?>
                            <span style="background: linear-gradient(135deg, #dbeafe 0%, #dcfce7 100%); color: #065f46; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-check-circle"></i> Default Address
                            </span>
                        <?php else: ?>
                            <button class="set-default-btn" data-id="<?php echo $addr['id']; ?>" onclick="setDefaultAddress(<?php echo $addr['id']; ?>)" style="background: white; border: 1.5px solid #e2e8f0; color: #2A3B7E; font-size: 12px; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; transition: all 0.3s;" onmouseover="this.style.borderColor='#2A3B7E'; this.style.backgroundColor='rgba(42,59,126,0.05)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='white'">
                                <i class="fas fa-star"></i> Make Default
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button id="add-address-btn" class="btn-primary" onclick="openAddressModal()" style="margin-top: 25px; display: inline-flex; align-items: center; gap: 8px; padding: 12px 30px; background: linear-gradient(135deg, #2A3B7E 0%, #1a2657 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(42, 59, 126, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(42, 59, 126, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(42, 59, 126, 0.3)'">
        <i class="fas fa-plus"></i> Add New Address
    </button>

    <script>
        function editAddressModal(addr) {
            editAddress(addr);
        }
        function deleteAddressConfirm(id) {
            deleteAddress(id);
        }
    </script>
    <?php
}

function load_password_view() {
    ?>
    <div class="content-header">
        <div class="content-title">Change Password</div>
        <div class="content-subtitle">For your account's security, do not share your password with anyone.</div>
    </div>

    <form class="change-password-form" method="POST" style="max-width: 500px;">
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
    <?php
}

function load_notifications_view($conn, $user_id) {
    // Escape user_id for safety
    $user_id_safe = mysqli_real_escape_string($conn, $user_id);
    
    // Check if support_tickets table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'support_tickets'");
    if (mysqli_num_rows($check_table) == 0) {
        ?>
        <div class="content-header">
            <div class="content-title">Notifications</div>
            <div class="content-subtitle">Stay updated with your activities and support responses</div>
        </div>

        <div class="empty-state" style="background: #f8f9fa; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 50px 20px; text-align: center;">
            <i class="fas fa-bell-slash" style="font-size: 45px; color: #cbd5e0; margin-bottom: 20px; display: block;"></i>
            <p style="font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px;">No notifications yet</p>
            <p style="font-size: 14px; color: #666; margin: 0;">You'll see notifications here</p>
        </div>
        <?php
        return;
    }
    
    $notif_sql = "SELECT * FROM support_tickets WHERE customer_id = '$user_id_safe' AND (admin_reply IS NOT NULL OR status != 'Open') ORDER BY updated_at DESC LIMIT 10";
    $notif_res = mysqli_query($conn, $notif_sql);
    
    if (!$notif_res) {
        throw new Exception('Error fetching notifications: ' . mysqli_error($conn));
    }
    
    ?>
    <div class="content-header">
        <div class="content-title">Notifications</div>
        <div class="content-subtitle">Stay updated with your activities and support responses</div>
    </div>

    <div class="notification-list">
        <?php if (!$notif_res || mysqli_num_rows($notif_res) === 0): ?>
            <div style="text-align: center; padding: 50px 0; color: #999;">
                <i class="fas fa-bell-slash" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p>No new notifications yet.</p>
            </div>
        <?php else: ?>
            <?php while ($notif = mysqli_fetch_assoc($notif_res)): ?>
                <div class="notification-item" style="padding: 15px; border-bottom: 1px solid #f0f0f0; display: flex; gap: 15px; background-color: #f0f7ff;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #2A3B7E; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-weight: 600; color: #333;">Support Ticket Update</span>
                            <span style="font-size: 12px; color: #999;"><?php echo date('M d, H:i', strtotime($notif['updated_at'])); ?></span>
                        </div>
                        <div style="font-size: 14px; color: #555; margin-bottom: 8px;">
                            Ticket <strong>#<?php echo $notif['ticket_number']; ?></strong> has been updated to <strong><?php echo $notif['status']; ?></strong>.
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <?php
}
?>

<script>
function trackOrder(orderId) {
    // Redirect to Tracking.php with the order ID
    window.location.href = 'Tracking.php?order_id=' + orderId;
}
</script>
