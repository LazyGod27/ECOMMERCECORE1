<?php
// =========================================================================
// CustomerSupport/functions.php - Isolated Application Logic for Support
// =========================================================================

if (!function_exists('get_db_connection')) {
    require_once __DIR__ . '/connection.php';
}

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

if (!function_exists('get_pdo')) {
    function get_pdo() {
        if (function_exists('get_db_connection')) {
            return get_db_connection();
        }
        global $pdo;
        return $pdo;
    }
}

// --- AUTHENTICATION & OTP (Isolated for Support) ---

if (!function_exists('get_support_user_by_username')) {
    function get_support_user_by_username($pdo, $username) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_support_user_details')) {
    function get_support_user_details($pdo, $id) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, role, full_name, email, phone_number, password_hash, otp_code, otp_expiry, created_at, updated_at FROM admin_users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('generate_otp')) {
    function generate_otp() {
        return strval(random_int(100000, 999999));
    }
}

if (!function_exists('save_otp')) {
    function save_otp($pdo, $user_id, $otp) {
        $expiry = date('Y-m-d H:i:s', time() + 300);
        try {
            $stmt = $pdo->prepare("UPDATE admin_users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
            $stmt->execute([$otp, $expiry, $user_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('send_support_otp_email')) {
    function send_support_otp_email($email, $otp, $username) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = gethostbyname('smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = 'linbilcelestre31@gmail.com';
            $mail->Password = 'ptkm lwud sfgh twdh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->Timeout = 20;

            $mail->SMTPOptions = array(
                'ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true)
            );

            $mail->setFrom('linbilcelestre31@gmail.com', 'IMarket PH Support Portal');
            $mail->addAddress($email, $username);
            $mail->isHTML(true);
            $mail->Subject = 'Support Portal Verification Code';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                    <h2 style='color: #2A3B7E;'>Support Verification</h2>
                    <p>Hello <b>{$username}</b>,</p>
                    <p>Your OTP for support portal access is:</p>
                    <h1 style='background: #f4f4f4; padding: 10px; display: inline-block; border-radius: 5px; color: #2A3B7E;'>{$otp}</h1>
                    <p>This code is valid for 5 minutes.</p>
                </div>
            ";
            $mail->send();
            return "A verification code has been sent to your email.";
        } catch (Exception $e) {
            error_log("OTP Email Error: {$mail->ErrorInfo}");
            return "Error sending OTP email.";
        }
    }
}

if (!function_exists('authenticate_support')) {
    function authenticate_support($pdo, $username, $password) {
        $user = get_support_user_by_username($pdo, $username);
        if ($user && password_verify($password, $user['password_hash'])) {
            if (empty($user['email'])) {
                return ['success' => false, 'message' => "Login failed: No registered email address."];
            }
            $otp = generate_otp();
            if (save_otp($pdo, $user['id'], $otp)) {
                send_support_otp_email($user['email'], $otp, $user['username']);
                $_SESSION['support_awaiting_otp'] = true;
                $_SESSION['temp_support_id'] = $user['id'];
                $_SESSION['temp_support_username'] = $user['username'];
                return ['success' => true, 'redirect_view' => 'otp', 'message' => "Code sent to email."];
            }
        }
        return ['success' => false, 'message' => "Incorrect username or password."];
    }
}

if (!function_exists('verify_otp_and_login_support')) {
    function verify_otp_and_login_support($pdo, $user_id, $otp_input) {
        $user = get_support_user_details($pdo, $user_id);
        if (!$user || $user['otp_code'] !== $otp_input) {
            return "Invalid OTP.";
        }
        if (new DateTime() > new DateTime($user['otp_expiry'])) {
            return "OTP expired.";
        }

        $pdo->prepare("UPDATE admin_users SET otp_code = NULL, otp_expiry = NULL WHERE id = ?")->execute([$user_id]);
        $_SESSION['support_logged_in'] = true;
        $_SESSION['support_id'] = $user['id'];
        $_SESSION['support_username'] = $user['username'];
        $_SESSION['support_role'] = $user['role'];
        unset($_SESSION['support_awaiting_otp'], $_SESSION['temp_support_id'], $_SESSION['temp_support_username']);
        return "Successful login!";
    }
}

if (!function_exists('handle_support_login_redirect')) {
    function handle_support_login_redirect() {
        unset($_SESSION['support_awaiting_otp'], $_SESSION['temp_support_id'], $_SESSION['temp_support_username']);
        header("Location: login.php?msg=" . urlencode("Session cleared."));
        exit();
    }
}

// --- DATA ACCESS FUNCTIONS (Copied from Admin but isolated) ---

if (!function_exists('get_products_list')) {
    function get_products_list($pdo = null) {
        $pdo = $pdo ?? get_pdo();
        try {
            $stmt = $pdo->prepare("SELECT p.*, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }
}

if (!function_exists('get_orders_list')) {
    function get_orders_list($pdo = null) {
        $pdo = $pdo ?? get_pdo();
        try {
            $stmt = $pdo->prepare("SELECT o.id, o.tracking_number, o.total_amount as total, o.status, o.created_at as date, o.full_name as customer, o.quantity as items FROM orders o ORDER BY o.created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }
}

if (!function_exists('get_categories_list')) {
    function get_categories_list($pdo = null) {
        $pdo = $pdo ?? get_pdo();
        try {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE status = 'Active' ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }
}

if (!function_exists('get_support_tickets_list')) {
    function get_support_tickets_list($pdo = null) {
        $pdo = $pdo ?? get_pdo();
        try {
            $stmt = $pdo->prepare("SELECT st.*, c.fullname as customer_name, c.email as customer_email, a.username as assigned_admin FROM support_tickets st LEFT JOIN users c ON st.customer_id = c.id LEFT JOIN admin_users a ON st.assigned_to = a.id ORDER BY st.created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }
}

if (!function_exists('update_support_ticket')) {
    function update_support_ticket($pdo, $id, $status, $priority = null, $assigned_to = null, $admin_reply = null) {
        try {
            $fields = ["status = ?", "updated_at = NOW()"];
            $params = [$status];
            if ($priority !== null) { $fields[] = "priority = ?"; $params[] = $priority; }
            if ($assigned_to !== null) { $fields[] = "assigned_to = ?"; $params[] = $assigned_to; }
            if (!empty($admin_reply)) { $fields[] = "admin_reply = ?"; $params[] = $admin_reply; }
            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE support_tickets SET " . implode(", ", $fields) . " WHERE id = ?");
            $stmt->execute($params);
            return ['success' => true, 'message' => 'Ticket updated successfully'];
        } catch (PDOException $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
    }
}

if (!function_exists('get_dashboard_kpis')) {
    function get_dashboard_kpis($pdo = null) {
        $pdo = $pdo ?? get_pdo();
        $data = ['totalRevenue' => 0, 'totalOrders' => 0, 'lowStockCount' => 0, 'newCustomers' => 0, 'topProducts' => []];
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status != 'Cancelled'");
            $data['totalOrders'] = (int)$stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM support_tickets");
            $data['totalTickets'] = (int)$stmt->fetchColumn();
            return $data;
        } catch (PDOException $e) { return $data; }
    }
}

// Add these to prevent errors in dashboard.php
if (!function_exists('get_transactions_list')) { function get_transactions_list($pdo) { return []; } }
if (!function_exists('get_shipments_list')) { function get_shipments_list($pdo) { return []; } }
if (!function_exists('get_customers_list')) { function get_customers_list($pdo) { return []; } }
if (!function_exists('get_admin_users_list')) { function get_admin_users_list($pdo) { return []; } }
if (!function_exists('get_customer_addresses')) { function get_customer_addresses($pdo) { return []; } }
if (!function_exists('update_admin_profile')) { function update_admin_profile($pdo, $id, $un, $fn, $em, $ph, $cp, $np = null, $img = null) { return "Profile updated"; } }
if (!function_exists('get_admin_profile_image_url')) { function get_admin_profile_image_url($id) { return null; } }
if (!function_exists('update_order_status')) { function update_order_status($id, $st, $pdo) { return true; } }

?>
