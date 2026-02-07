<?php
// =========================================================================
// core1admin/functions.php - All Application Logic Functions
// =========================================================================
// This file contains all business logic functions for the admin portal
// All functions expect a PDO connection object as their first parameter
// =========================================================================

// Ensure database connection is available
if (!function_exists('get_db_connection')) {
    require_once 'connection.php';
}

// ===========================================
// HELPER FUNCTIONS FOR DATABASE ACCESS
// ===========================================

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

/**
 * Get database connection - wrapper function
 * Returns PDO connection object from connection.php
 */
if (!function_exists('get_pdo')) {
    function get_pdo()
    {
        if (function_exists('get_db_connection')) {
            return get_db_connection();
        }
        // Fallback to global if function doesn't exist
        global $pdo;
        if ($pdo === null && isset($GLOBALS['pdo'])) {
            return $GLOBALS['pdo'];
        }
        return $pdo;
    }
}

// ===========================================
// MODULE 0: AUTHENTICATION & OTP 
// ===========================================

/**
 * Fetches admin user data by username. Uses prepared statements.
 */
if (!function_exists('get_admin_user_by_username')) {
    function get_admin_user_by_username($pdo, $username)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_admin_user_by_username: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Fetches admin user profile details by ID, including phone_number.
 */
if (!function_exists('get_admin_user_details')) {
    function get_admin_user_details($pdo, $id)
    {
        try {
            $stmt = $pdo->prepare("SELECT id, username, role, full_name, email, phone_number, password_hash, otp_code, otp_expiry, created_at, updated_at FROM admin_users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_admin_user_details: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Generates a 6-digit cryptographically secure OTP.
 */
if (!function_exists('generate_otp')) {
    function generate_otp()
    {
        return strval(random_int(100000, 999999));
    }
}

/**
 * Saves the OTP and expiry time (5 minutes) to the database.
 */
if (!function_exists('save_otp')) {
    function save_otp($pdo, $user_id, $otp)
    {
        $expiry = date('Y-m-d H:i:s', time() + 300); // 300 seconds (5 minutes)
        try {
            // [SECURITY: USING PREPARED STATEMENT]
            $stmt = $pdo->prepare("UPDATE admin_users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
            $stmt->execute([$otp, $expiry, $user_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Database Error in save_otp: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Simulates sending the OTP via Email to the provided email address.
 */
/**
 * Sends the OTP via Email using PHPMailer to the provided email address.
 */
if (!function_exists('send_otp_email')) {
    function send_otp_email($email, $otp, $username)
    {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = gethostbyname('smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = 'linbilcelestre31@gmail.com';
            $mail->Password = 'ptkm lwud sfgh twdh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->Timeout = 20;

            // Fix for XAMPP SSL issues
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom('linbilcelestre31@gmail.com', 'IMarket PH Admin Portal');
            $mail->addAddress($email, $username);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Admin Login Verification Code';
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #2A3B7E;'>Admin Login Verification</h2>
                <p>Hello <b>{$username}</b>,</p>
                <p>Your One-Time Password (OTP) for admin access is:</p>
                <h1 style='background: #f4f4f4; padding: 10px; display: inline-block; border-radius: 5px; color: #2A3B7E;'>{$otp}</h1>
                <p>This code is valid for 5 minutes. Do not share this code with anyone.</p>
                <br>
                <p>If you did not request this, please contact the system administrator immediately.</p>
            </div>
        ";
            $mail->AltBody = "Hello {$username}, Your Admin OTP code is: {$otp}. Do not share this.";

            $mail->send();
            // Mask the email for privacy in the message
            $at_pos = strpos($email, '@');
            $masked_email = substr($email, 0, 3) . '****' . substr($email, $at_pos);
            return "A verification code has been sent to your email <b>{$masked_email}</b>.";
        } catch (Exception $e) {
            error_log("OTP Email Sending Error: {$mail->ErrorInfo}");
            return "Error sending OTP email. Check logs.";
        }
    }
}

/**
 * PHASE 1: Authenticates the user and initiates OTP via Email.
 */
if (!function_exists('authenticate_admin')) {
    function authenticate_admin($pdo, $username, $password)
    {
        // 1. Try finding in admin_users first (by username or email)
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fallback for explicitly requested admin@gmail.com
            if (!$user && $username === 'admin@gmail.com') {
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $user = false;
        }

        if ($user && (password_verify($password, $user['password_hash']) || $password === $user['password_hash'])) {
            $recipient = $user['email'];
            
            // If they explicitly used admin@gmail.com to login, prioritize it for OTP
            if ($username === 'admin@gmail.com') {
                $recipient = 'admin@gmail.com';
            }

            if (empty($recipient)) {
                return ['success' => false, 'message' => "Login failed: No registered email address for OTP. Please contact support."];
            }

            $otp = generate_otp();

            if (save_otp($pdo, $user['id'], $otp)) {
                $otp_message = send_otp_email($recipient, $otp, $user['username']);
                $_SESSION['admin_awaiting_otp'] = true;
                $_SESSION['temp_admin_id'] = $user['id'];
                $_SESSION['temp_admin_username'] = $user['username'];

                return [
                    'success' => true,
                    'redirect_view' => 'otp',
                    'message' => $otp_message
                ];
            } else {
                return ['success' => false, 'message' => "Login failed: Could not save OTP. Please check database permissions."];
            }
        }

        // 2. Fallback: Try finding in 'users' (Customer table)
        // Check both email and fullname since the login form asks for "Username"
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR fullname = ?");
            $stmt->execute([$username, $username]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($customer && password_verify($password, $customer['password'])) {
                // Login successful as Customer-Admin
                // Direct login, skipping OTP for customers as they lack otp_code/otp_expiry columns in this context
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $customer['id'];
                $_SESSION['admin_username'] = $customer['fullname']; // Use fullname as username
                $_SESSION['admin_role'] = 'Customer-Admin'; // Distinguish role

                return [
                    'success' => true,
                    'redirect_view' => 'dashboard', // Direct to dashboard
                    'message' => "Logged in successfully as " . $customer['fullname']
                ];
            }
        } catch (PDOException $e) {
            // Ignore error and fall through to failure message
        }

        return ['success' => false, 'message' => "Login failed: Incorrect username or password."];
    }
}

/**
 * PHASE 2: Verifies the entered OTP and completes login.
 */
if (!function_exists('verify_otp_and_login')) {
    function verify_otp_and_login($pdo, $user_id, $otp_input)
    {
        $user = get_admin_user_details($pdo, $user_id);

        if (!$user) {
            return "Error: User data not found during OTP check.";
        }

        if (empty($user['otp_code'])) {
            return "Error: OTP expired or not generated. Please login again.";
        }

        $current_time = new DateTime();
        $expiry_time = new DateTime($user['otp_expiry']);

        // 1. Check if OTP matches
        if ($user['otp_code'] !== $otp_input) {
            return "Invalid OTP. Please try again.";
        }

        // 2. Check if OTP has expired
        if ($current_time > $expiry_time) {
            $pdo->prepare("UPDATE admin_users SET otp_code = NULL, otp_expiry = NULL WHERE id = ?")->execute([$user_id]);
            return "OTP expired. Please log in again to generate a new code.";
        }

        // OTP is valid! Clear the OTP fields and log in.
        $pdo->prepare("UPDATE admin_users SET otp_code = NULL, otp_expiry = NULL WHERE id = ?")->execute([$user_id]);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];

        unset($_SESSION['admin_awaiting_otp']);
        unset($_SESSION['temp_admin_id']);
        unset($_SESSION['temp_admin_username']);

        return "Successful login! Welcome, " . $user['username'] . ".";
    }
}


/**
 * Creates a new admin user account.
 */
if (!function_exists('create_admin_account')) {
    function create_admin_account($pdo, $username, $password, $email, $phone_number, $full_name)
    {
        $role = 'Admin';

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => "Registration failed: Password must be at least 6 characters long."];
        }
        if (get_admin_user_by_username($pdo, $username)) {
            return ['success' => false, 'message' => "Registration failed: Username is already taken."];
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            // [SECURITY: USING PREPARED STATEMENT]
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash, role, email, phone_number, full_name) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password_hash, $role, $email, $phone_number, $full_name]);

            // Retrieve the ID of the newly created user for OTP initialization
            $user = get_admin_user_by_username($pdo, $username);
            return [
                'success' => true,
                'user_id' => $user['id'],
                'username' => $username,
                'email' => $email, // Used for OTP initialization
                'message' => "Account for '{$username}' successfully created. Now, OTP verification is required."
            ];
        } catch (PDOException $e) {
            error_log("Database Error in create_admin_account: " . $e->getMessage());
            return ['success' => false, 'message' => "Registration failed due to a database error. Please ensure 'phone_number', 'full_name', 'otp_code', and 'otp_expiry' columns exist."];
        }
    }
}

/**
 * Function to handle redirect back to Login/Register from OTP screen.
 */
if (!function_exists('handle_login_redirect')) {
    function handle_login_redirect()
    {
        // Clear temporary session data
        unset($_SESSION['admin_awaiting_otp']);
        unset($_SESSION['temp_admin_id']);
        unset($_SESSION['temp_admin_username']);

        // Redirect to main page (login view)
        header("Location: " . basename(__FILE__) . "?msg=" . urlencode("Your session has been cleared. Log in again to generate a new OTP."));
        exit();
    }
}

if (!function_exists('handle_logout')) {
    function handle_logout()
    {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        header("Location: " . basename(__FILE__) . "?msg=" . urlencode("Successfully logged out."));
        exit();
    }
}

if (!function_exists('get_profile_upload_directory')) {
    function get_profile_upload_directory()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile';
    }
}

if (!function_exists('ensure_profile_upload_directory')) {
    function ensure_profile_upload_directory()
    {
        $directory = get_profile_upload_directory();
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException("Failed to create profile upload directory at {$directory}");
            }
        }
        return $directory;
    }
}

if (!function_exists('handle_profile_image_upload')) {
    function handle_profile_image_upload($file, $admin_id)
    {
        if (!$file || !isset($file['error'])) {
            return ['success' => false, 'message' => 'Invalid file payload.'];
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'message' => 'No file uploaded.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error code: ' . $file['error']];
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Invalid upload source detected.'];
        }

        if (!is_numeric($admin_id) || intval($admin_id) <= 0) {
            return ['success' => false, 'message' => 'Invalid admin reference for upload.'];
        }

        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Profile image must be 2MB or smaller.'];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        if (!in_array($extension, $allowedExtensions, true)) {
            return ['success' => false, 'message' => 'Only JPG, PNG, or WEBP files are allowed.'];
        }

        if (function_exists('mime_content_type')) {
            $detectedMime = mime_content_type($file['tmp_name']);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if ($detectedMime && !in_array($detectedMime, $allowedMimes, true)) {
                return ['success' => false, 'message' => 'Unsupported image format uploaded.'];
            }
        }

        try {
            $directory = ensure_profile_upload_directory();
        } catch (RuntimeException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        $cleanAdminId = intval($admin_id);
        foreach (glob($directory . DIRECTORY_SEPARATOR . $cleanAdminId . '_*.*') as $oldFile) {
            @unlink($oldFile);
        }

        $filename = $cleanAdminId . '_' . time() . '.' . $extension;
        $targetPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Failed to save profile image.'];
        }

        $relativePath = str_replace('\\', '/', str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $targetPath));

        return [
            'success' => true,
            'path' => $relativePath,
            'message' => 'Profile image updated successfully.'
        ];
    }
}

if (!function_exists('get_admin_profile_image_url')) {
    function get_admin_profile_image_url($admin_id)
    {
        if (!$admin_id) {
            return null;
        }

        $directory = get_profile_upload_directory();
        $pattern = $directory . DIRECTORY_SEPARATOR . intval($admin_id) . '_*.*';
        $files = glob($pattern);

        if (!$files) {
            return null;
        }

        rsort($files);
        $latest = $files[0];
        $relativePath = str_replace('\\', '/', str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $latest));
        $version = filemtime($latest) ?: time();

        return $relativePath . '?v=' . $version;
    }
}

/**
 * Handles profile updates, including phone_number.
 */
if (!function_exists('update_admin_profile')) {
    function update_admin_profile($pdo, $id, $new_username, $full_name, $email, $phone_number, $current_password, $new_password = null, $profile_image_file = null)
    {
        $user = get_admin_user_details($pdo, $id);

        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            return "Profile update failed: Invalid current password.";
        }

        $fields_to_update = ["username = ?", "full_name = ?", "email = ?", "phone_number = ?"];
        $params = [$new_username, $full_name, $email, $phone_number];
        $message = "Profile details successfully updated!";

        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                return "Profile update failed: New password must be at least 6 characters long.";
            }
            $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $fields_to_update[] = "password_hash = ?";
            $params[] = $password_hash;
            $message = "Profile and password successfully updated!";
        }

        $update_query = "UPDATE admin_users SET " . implode(", ", $fields_to_update) . " WHERE id = ?";
        $params[] = $id;

        try {
            $stmt = $pdo->prepare($update_query);
            $stmt->execute($params);
            $_SESSION['admin_username'] = $new_username;

            $imageMessage = '';
            if ($profile_image_file && isset($profile_image_file['error']) && $profile_image_file['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_result = handle_profile_image_upload($profile_image_file, $id);
                if (!$upload_result['success']) {
                    return "Profile update failed: " . $upload_result['message'];
                }
                $imageMessage = " Profile photo updated!";
            }

            return $message . $imageMessage;
        } catch (PDOException $e) {
            error_log("Database Error in update_admin_profile: " . $e->getMessage());
            return "Profile update failed due to a database error.";
        }
    }
}

// ===========================================
// MODULE 1: PRODUCTS MANAGEMENT
// ===========================================

/**
 * Get all products with category information
 */
if (!function_exists('get_products_list')) {
    function get_products_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            $stmt = $pdo->prepare("
            SELECT p.*, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC
        ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_products_list: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Get all categories
 */
if (!function_exists('get_categories_list')) {
    function get_categories_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE status = 'Active' ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_categories_list: " . $e->getMessage());
            return [];
        }
    }
}

// ===========================================
// MODULE 2: ORDERS MANAGEMENT
// ===========================================

/**
 * Get all orders with customer information
 */
if (!function_exists('get_orders_list')) {
    function get_orders_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            $stmt = $pdo->prepare("
            SELECT o.id, o.tracking_number, o.total_amount as total, o.status, o.created_at as date, 
                   o.full_name as customer,
                   o.quantity as items
            FROM orders o 
            ORDER BY o.created_at DESC
        ");
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format for display
            $formatted = [];
            foreach ($orders as $order) {
                $formatted[] = [
                    'id' => $order['id'],
                    'tracking_number' => $order['tracking_number'] ?? 'N/A',
                    'customer' => $order['customer'] ?? 'N/A',
                    'total' => floatval($order['total']),
                    'status' => $order['status'],
                    'date' => date('Y-m-d', strtotime($order['date'])),
                    'items' => intval($order['items'])
                ];
            }
            return $formatted;
        } catch (PDOException $e) {
            error_log("Database Error in get_orders_list: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Update order status
 */
if (!function_exists('update_order_status')) {
    function update_order_status($order_id, $new_status, $pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return false;
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Database Error in update_order_status: " . $e->getMessage());
            return false;
        }
    }
}

// ===========================================
// MODULE 3: CUSTOMERS MANAGEMENT (Addresses)
// ===========================================

/**
 * Get customer addresses
 */
if (!function_exists('get_customer_addresses')) {
    function get_customer_addresses($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            // Fetch addresses from orders table as requested
            $stmt = $pdo->prepare("
            SELECT id, full_name, address, city, postal_code, phone_number
            FROM orders
            WHERE address IS NOT NULL AND address != ''
            GROUP BY address, city, postal_code -- Avoid duplicates
            ORDER BY id DESC
        ");
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format addresses for display
            $formatted = [];
            foreach ($orders as $order) {
                $full_address = $order['address'];
                if (!empty($order['city']))
                    $full_address .= ', ' . $order['city'];
                if (!empty($order['postal_code']))
                    $full_address .= ' ' . $order['postal_code'];

                $formatted[] = [
                    'id' => 'ADDR-' . $order['id'],
                    'customer' => $order['full_name'] ?? 'N/A',
                    'address' => $full_address,
                    'phone' => $order['phone_number'],
                    'status' => 'Verified'
                ];
            }
            return $formatted;
        } catch (PDOException $e) {
            error_log("Database Error in get_customer_addresses: " . $e->getMessage());
            return [];
        }
    }
}

// ===========================================
// PRODUCT CRUD OPERATIONS
// ===========================================

/**
 * Add a new product
 */
if (!function_exists('add_product')) {
    function add_product($pdo, $name, $slug, $description, $price, $stock, $category_id, $status = 'Active', $image_url = null)
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, stock, category_id, status, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $price, $stock, $category_id, $status, $image_url]);
            return ['success' => true, 'message' => 'Product added successfully', 'id' => $pdo->lastInsertId()];
        } catch (PDOException $e) {
            error_log("Database Error in add_product: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add product: ' . $e->getMessage()];
        }
    }
}

/**
 * Update a product
 */
if (!function_exists('update_product')) {
    function update_product($pdo, $id, $name, $slug, $description, $price, $stock, $category_id, $status, $image_url = null)
    {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, stock = ?, category_id = ?, status = ?, image_url = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $price, $stock, $category_id, $status, $image_url, $id]);
            return ['success' => true, 'message' => 'Product updated successfully'];
        } catch (PDOException $e) {
            error_log("Database Error in update_product: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update product: ' . $e->getMessage()];
        }
    }
}

/**
 * Delete a product
 */
if (!function_exists('delete_product')) {
    function delete_product($pdo, $id)
    {
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Product deleted successfully'];
        } catch (PDOException $e) {
            error_log("Database Error in delete_product: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete product: ' . $e->getMessage()];
        }
    }
}

/**
 * Get product by ID
 */
if (!function_exists('get_product_by_id')) {
    function get_product_by_id($pdo, $id)
    {
        try {
            $stmt = $pdo->prepare("SELECT p.*, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_product_by_id: " . $e->getMessage());
            return false;
        }
    }
}

// ===========================================
// CATEGORY CRUD OPERATIONS
// ===========================================

/**
 * Add a new category
 */
if (!function_exists('add_category')) {
    function add_category($pdo, $name, $slug, $description = null, $status = 'Active')
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $status]);
            return ['success' => true, 'message' => 'Category added successfully', 'id' => $pdo->lastInsertId()];
        } catch (PDOException $e) {
            error_log("Database Error in add_category: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add category: ' . $e->getMessage()];
        }
    }
}

/**
 * Update a category
 */
if (!function_exists('update_category')) {
    function update_category($pdo, $id, $name, $slug, $description = null, $status = 'Active')
    {
        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $status, $id]);
            return ['success' => true, 'message' => 'Category updated successfully'];
        } catch (PDOException $e) {
            error_log("Database Error in update_category: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update category: ' . $e->getMessage()];
        }
    }
}

/**
 * Delete a category
 */
if (!function_exists('delete_category')) {
    function delete_category($pdo, $id)
    {
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Category deleted successfully'];
        } catch (PDOException $e) {
            error_log("Database Error in delete_category: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete category: ' . $e->getMessage()];
        }
    }
}

/**
 * Get category by ID
 */
if (!function_exists('get_category_by_id')) {
    function get_category_by_id($pdo, $id)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_category_by_id: " . $e->getMessage());
            return false;
        }
    }
}

// ===========================================
// TRANSACTIONS MANAGEMENT
// ===========================================

/**
 * Get all transactions
 */
if (!function_exists('get_transactions_list')) {
    function get_transactions_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            $stmt = $pdo->prepare("
        SELECT 
            o.id,
            CONCAT('#TRX-', o.id) as transaction_number,
            o.id as order_number,
            o.full_name as customer_name,
            o.total_amount as amount,
            o.payment_method,
            o.status,
            o.created_at as transaction_date
        FROM orders o
        ORDER BY o.created_at DESC
    ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_transactions_list: " . $e->getMessage());
            return [];
        }
    }
}

// ===========================================
// SHIPMENTS MANAGEMENT
// ===========================================

/**
 * Get all shipments
 */
if (!function_exists('get_shipments_list')) {
    function get_shipments_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            // Fetch tracking data directly from orders
            $stmt = $pdo->prepare("
            SELECT 
                tracking_number, 
                id as order_number, 
                full_name as customer_name,
                'Standard Courier' as courier, 
                status, 
                CONCAT(city, ', ', postal_code) as current_location, 
                DATE_ADD(created_at, INTERVAL 5 DAY) as estimated_delivery
            FROM orders
            WHERE tracking_number IS NOT NULL AND tracking_number != ''
            ORDER BY created_at DESC
        ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_shipments_list: " . $e->getMessage());
            return [];
        }
    }
}

// ===========================================
// CUSTOMERS MANAGEMENT
// ===========================================

/**
 * Get all customers
 */
if (!function_exists('get_customers_list')) {
    function get_customers_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            $stmt = $pdo->prepare("
            SELECT c.id, c.fullname as full_name, c.email, c.phone as phone_number, 'Active' as status, '2024-01-01' as created_at,
                   (SELECT COUNT(*) FROM orders WHERE user_id = c.id) as total_orders,
                   (SELECT SUM(total_amount) FROM orders WHERE user_id = c.id AND status != 'Cancelled') as total_spent
            FROM users c
            ORDER BY c.id DESC
        ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_customers_list: " . $e->getMessage());
            return [];
        }
    }
}

// ===========================================
// SUPPORT TICKETS MANAGEMENT
// ===========================================

/**
 * Get all support tickets
 */
if (!function_exists('get_support_tickets_list')) {
    function get_support_tickets_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            $stmt = $pdo->prepare("
            SELECT st.*, c.fullname as customer_name, c.email as customer_email,
                   a.username as assigned_admin
            FROM support_tickets st
            LEFT JOIN users c ON st.customer_id = c.id
            LEFT JOIN admin_users a ON st.assigned_to = a.id
            ORDER BY st.created_at DESC
        ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_support_tickets_list: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Update support ticket
 */
if (!function_exists('update_support_ticket')) {
    function update_support_ticket($pdo, $id, $status, $priority = null, $assigned_to = null, $admin_reply = null)
    {
        try {
            $fields = ["status = ?"];
            $params = [$status];

            if ($priority !== null) {
                $fields[] = "priority = ?";
                $params[] = $priority;
            }

            if ($assigned_to !== null) {
                $fields[] = "assigned_to = ?";
                $params[] = $assigned_to;
            }

            if (!empty($admin_reply)) {
                $fields[] = "admin_reply = ?";
                $params[] = $admin_reply;
            }

            $fields[] = "is_read = 0";
            $fields[] = "updated_at = NOW()";
            $params[] = $id;

            $stmt = $pdo->prepare("UPDATE support_tickets SET " . implode(", ", $fields) . " WHERE id = ?");
            $stmt->execute($params);
            return ['success' => true, 'message' => 'Ticket updated successfully'];
        } catch (PDOException $e) {
            error_log("Database Error in update_support_ticket: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update ticket: ' . $e->getMessage()];
        }
    }
}

// ===========================================
// ADMIN USERS MANAGEMENT
// ===========================================

/**
 * Get all admin users
 */
if (!function_exists('get_admin_users_list')) {
    function get_admin_users_list($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [];
        try {
            $stmt = $pdo->prepare("SELECT id, username, role, full_name, email, phone_number, created_at, updated_at FROM admin_users ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in get_admin_users_list: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Delete admin user
 */
if (!function_exists('delete_admin_user')) {
    function delete_admin_user($pdo, $id)
    {
        try {
            // Prevent deleting the last admin
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_users");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] <= 1) {
                return ['success' => false, 'message' => 'Cannot delete the last admin user'];
            }

            $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Admin user deleted successfully'];
        } catch (PDOException $e) {
            error_log("Database Error in delete_admin_user: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete admin user: ' . $e->getMessage()];
        }
    }
}

// ===========================================
// MODULE 7: DASHBOARD KPIs
// ===========================================

/**
 * Get dashboard KPI data from database
 */
if (!function_exists('get_dashboard_kpis')) {
    function get_dashboard_kpis($pdo = null)
    {
        if ($pdo === null) {
            $pdo = get_pdo();
        }
        if (!$pdo)
            return [
                'totalRevenue' => 0,
                'totalOrders' => 0,
                'lowStockCount' => 0,
                'newCustomers' => 0,
                'topProducts' => []
            ];
        try {
            // Total Revenue (from completed transactions this month)
            $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM transactions 
            WHERE status = 'Completed' 
            AND MONTH(transaction_date) = MONTH(CURRENT_DATE())
            AND YEAR(transaction_date) = YEAR(CURRENT_DATE())
        ");
            $stmt->execute();
            $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalRevenue = floatval($revenue['total']);

            // Total Orders (this month)
            $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM orders 
            WHERE MONTH(order_date) = MONTH(CURRENT_DATE())
            AND YEAR(order_date) = YEAR(CURRENT_DATE())
        ");
            $stmt->execute();
            $orders = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalOrders = intval($orders['total']);

            // Low Stock Count (stock < 10)
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE stock < 10 AND status != 'Inactive'");
            $stmt->execute();
            $lowStock = $stmt->fetch(PDO::FETCH_ASSOC);
            $lowStockCount = intval($lowStock['total']);

            // New Customers (this month)
            $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM users
        ");
            $stmt->execute();
            $customers = $stmt->fetch(PDO::FETCH_ASSOC);
            $newCustomers = intval($customers['total']);

            // Top Products (by order items sold)
            $stmt = $pdo->prepare("
            SELECT p.name, c.name as category, SUM(oi.quantity) as sold
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            LEFT JOIN categories c ON p.category_id = c.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status != 'Cancelled'
            GROUP BY p.id, p.name, c.name
            ORDER BY sold DESC
            LIMIT 5
        ");
            $stmt->execute();
            $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'totalRevenue' => $totalRevenue,
                'totalOrders' => $totalOrders,
                'lowStockCount' => $lowStockCount,
                'newCustomers' => $newCustomers,
                'topProducts' => $topProducts
            ];
        } catch (PDOException $e) {
            error_log("Database Error in get_dashboard_kpis: " . $e->getMessage());
            // Return default values if database error
            return [
                'totalRevenue' => 0,
                'totalOrders' => 0,
                'lowStockCount' => 0,
                'newCustomers' => 0,
                'topProducts' => []
            ];
        }
    }
}

/**
 * Generate slug from string
 */
if (!function_exists('generate_slug')) {
    function generate_slug($string)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug;
    }
}
?>