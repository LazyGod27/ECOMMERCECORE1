<?php
// Fix trailing slash 404 issue
if (strpos($_SERVER['REQUEST_URI'], '/dashboard.php/') !== false) {
    header("Location: " . str_replace('/dashboard.php/', '/dashboard.php', $_SERVER['REQUEST_URI']));
    exit();
}

require_once 'connection.php';

// Get the database connection
try {
    $pdo = get_db_connection();
} catch (RuntimeException $e) {
    http_response_code(500);
    $safeMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $supportTips = [
        'Ensure that the MySQL server is running.',
        'Double-check the credentials in connection.php or your environment variables.',
        'Confirm that the correct port is open (defaults attempted: ' . htmlspecialchars(implode(', ', array_unique(array_filter([
            getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306',
            getenv('DB_FALLBACK_PORT') !== false ? getenv('DB_FALLBACK_PORT') : null,
            '3306',
        ]))), ENT_QUOTES, 'UTF-8') . ').',
    ];
    echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>System Configuration Error</title>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-card { background: #111827; padding: 2.5rem; border-radius: 1rem; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(30, 64, 175, 0.45); border: 1px solid rgba(59, 130, 246, 0.2); }
        h1 { font-size: 1.75rem; margin-top: 0; margin-bottom: 1rem; color: #93c5fd; }
        p { line-height: 1.6; margin-bottom: 1rem; }
        ul { padding-left: 1.25rem; margin-bottom: 1.5rem; }
        li { margin-bottom: 0.5rem; }
        code { background: rgba(59, 130, 246, 0.15); padding: 0.2rem 0.4rem; border-radius: 0.35rem; color: #bfdbfe; }
        .details { margin-top: 1.5rem; padding: 1rem; border-radius: 0.75rem; background: rgba(148, 163, 184, 0.1); border: 1px solid rgba(148, 163, 184, 0.2); color: #cbd5f5; font-size: 0.9rem; word-break: break-word; }
    </style>
</head>
<body>
    <div class=\"error-card\">
        <h1>Database Connection Required</h1>
        <p>We couldn’t reach the database server, so the admin portal is temporarily unavailable. Please review the configuration below and try again.</p>
        <ul>";
    foreach ($supportTips as $tip) {
        echo '<li>' . htmlspecialchars($tip, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo "</ul>
        <div class=\"details\"><strong>Last error message</strong><br>{$safeMessage}</div>
    </div>
</body>
</html>";
    exit();
}


// =========================================================================
// 2. SESSION AND FUNCTION DEFINITIONS
// =========================================================================

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (!$is_logged_in) {
    header("Location: login.php");
    exit();
}

// ===========================================

// Functions have been moved to functions.php
require_once 'functions.php';



// ===========================================
// FORM HANDLER (Handles all POST requests)
// ===========================================

function handle_form_submission($pdo, $action, $post_data, $files = [])
{
    $result_message = "";
    $redirect_base = basename(__FILE__);

    switch ($action) {


        // --- ADMIN PROFILE UPDATE LOGIC ---
        case 'update_profile':
            $admin_id = $_SESSION['admin_id'] ?? null;
            if (!$admin_id) {
                header("Location: " . $redirect_base . "?msg=" . urlencode("Authentication required to update profile."));
                exit();
            }

            $result_message = update_admin_profile(
                $pdo,
                $admin_id,
                $post_data['new_username'] ?? '',
                $post_data['full_name'] ?? '',
                $post_data['email'] ?? '',
                $post_data['phone_number'] ?? '', // Phone number is optional in profile update
                $post_data['current_password'] ?? '',
                $post_data['new_password'] ?? '',
                $files['profile_image'] ?? null
            );

            header("Location: " . $redirect_base . "?module=user&submodule=profile&msg=" . urlencode($result_message));
            exit();

        // --- PRODUCT CRUD ACTIONS ---
        case 'add_product':
            require_once 'functions.php';
            $name = $post_data['name'] ?? '';
            $slug = generate_slug($name);
            $description = $post_data['description'] ?? '';
            $price = floatval($post_data['price'] ?? 0);
            $stock = intval($post_data['stock'] ?? 0);
            $category_id = intval($post_data['category_id'] ?? 0);
            $status = $post_data['status'] ?? 'Active';
            $image_url = $post_data['image_url'] ?? null;

            $result = add_product($pdo, $name, $slug, $description, $price, $stock, $category_id, $status, $image_url);
            $result_message = $result['message'];
            break;

        case 'edit_product':
            require_once 'functions.php';
            $id = intval($post_data['id'] ?? 0);
            $name = $post_data['name'] ?? '';
            $slug = generate_slug($name);
            $description = $post_data['description'] ?? '';
            $price = floatval($post_data['price'] ?? 0);
            $stock = intval($post_data['stock'] ?? 0);
            $category_id = intval($post_data['category_id'] ?? 0);
            $status = $post_data['status'] ?? 'Active';
            $image_url = $post_data['image_url'] ?? null;

            $result = update_product($pdo, $id, $name, $slug, $description, $price, $stock, $category_id, $status, $image_url);
            $result_message = $result['message'];
            break;

        case 'delete_product':
            require_once 'functions.php';
            $id = intval($post_data['id'] ?? 0);
            $result = delete_product($pdo, $id);
            $result_message = $result['message'];
            break;

        case 'clear_all_products':
            require_once 'functions.php';
            try {
                $stmt = $pdo->prepare("DELETE FROM products");
                $stmt->execute();
                $deleted_count = $stmt->rowCount();
                $result_message = "Successfully deleted {$deleted_count} product(s) from database.";
                $module = 'product';
                $submodule = 'products';
            } catch (PDOException $e) {
                error_log("Error clearing products: " . $e->getMessage());
                $result_message = "Error clearing products: " . $e->getMessage();
                $module = 'product';
                $submodule = 'products';
            }
            break;

        // --- CATEGORY CRUD ACTIONS ---
        case 'add_category':
            require_once 'functions.php';
            $name = $post_data['name'] ?? '';
            $slug = generate_slug($name);
            $description = $post_data['description'] ?? '';
            $status = $post_data['status'] ?? 'Active';

            $result = add_category($pdo, $name, $slug, $description, $status);
            $result_message = $result['message'];
            $submodule = 'categories';
            break;

        case 'edit_category':
            require_once 'functions.php';
            $id = intval($post_data['id'] ?? 0);
            $name = $post_data['name'] ?? '';
            $slug = generate_slug($name);
            $description = $post_data['description'] ?? '';
            $status = $post_data['status'] ?? 'Active';

            $result = update_category($pdo, $id, $name, $slug, $description, $status);
            $result_message = $result['message'];
            $submodule = 'categories';
            break;

        case 'delete_category':
            require_once 'functions.php';
            $id = intval($post_data['id'] ?? 0);
            $result = delete_category($pdo, $id);
            $result_message = $result['message'];
            $submodule = 'categories';
            break;

        // --- SUPPORT TICKET ACTIONS ---
        case 'update_ticket':
            require_once 'functions.php';
            $id = intval($post_data['id'] ?? 0);
            $status = $post_data['status'] ?? 'Open';
            $priority = $post_data['priority'] ?? null;
            $assigned_to = !empty($post_data['assigned_to']) ? intval($post_data['assigned_to']) : null;
            $admin_reply = $post_data['reply_message'] ?? null;

            $result = update_support_ticket($pdo, $id, $status, $priority, $assigned_to, $admin_reply);
            $result_message = $result['message'];
            $module = 'support';
            break;

        // --- ADMIN USER ACTIONS ---
        case 'delete_admin':
            require_once 'functions.php';
            $id = intval($post_data['id'] ?? 0);
            $result = delete_admin_user($pdo, $id);
            $result_message = $result['message'];
            $module = 'user';
            $submodule = 'admins';
            break;

        case 'update_order_status':
            $order_id = $post_data['id'] ?? null;
            $new_status = $post_data['status'] ?? null;

            if ($order_id && $new_status) {
                if (update_order_status($order_id, $new_status, $pdo)) {
                    $result_message = "Order #$order_id status successfully updated to **$new_status**.";
                } else {
                    $result_message = "Failed to update order status. Please check database configuration.";
                }
            } else {
                $result_message = "Error: Missing order ID or status.";
            }
            break;

        default:
            $result_message = "Error: Unknown action requested.";
            break;
    }

    // Default redirect for non-auth actions
    $module = $post_data['module'] ?? 'dashboard';
    $submodule = $post_data['submodule'] ?? '';
    header("Location: " . $redirect_base . "?module=$module&submodule=$submodule&msg=" . urlencode($result_message));
    exit();
}

// =========================================================================
// 3. TOP-LEVEL REQUEST HANDLER (Handles POST and GET actions)
// =========================================================================

// NEW ACTION: Handles request to return to login/clear session
if (isset($_GET['action']) && $_GET['action'] === 'return_to_login') {
    handle_login_redirect();
}

// Handle LOGOUT request



require_once 'functions.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Re-check for required functions just in case the include failed earlier
    if (!function_exists('update_order_status')) {
        error_log("FATAL: Required functions were not loaded.");
        // Fallback to minimal response
        header("Location: " . basename(__FILE__) . "?msg=" . urlencode("FATAL: System functions failed to load."));
        exit();
    }
    handle_form_submission($pdo, $_POST['action'], $_POST, $_FILES ?? []);
}

// Check login status
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$is_awaiting_otp = isset($_SESSION['admin_awaiting_otp']) && $_SESSION['admin_awaiting_otp'] === true;

$message = $_GET['msg'] ?? '';
$view = $_GET['view'] ?? '';

$is_register_page = $view === 'register';
$is_otp_page = $view === 'otp';

// State management to ensure correct view is shown
if ($is_logged_in) {
    $is_register_page = false;
    $is_otp_page = false;
} else if ($is_awaiting_otp) {
    $is_otp_page = true;
    $is_register_page = false;
} else if ($is_otp_page && !$is_awaiting_otp) {
    header("Location: " . basename(__FILE__) . "?msg=" . urlencode("Please log in first."));
    exit();
}

// Fetch display details
$admin_username = htmlspecialchars($_SESSION['admin_username'] ?? ($is_awaiting_otp ? $_SESSION['temp_admin_username'] : 'Admin'));
$admin_role = htmlspecialchars($_SESSION['admin_role'] ?? 'User');

// Fetch admin details for the profile page if logged in
$admin_details = [];
if ($is_logged_in && isset($_SESSION['admin_id'])) {
    $admin_details = get_admin_user_details($pdo, $_SESSION['admin_id']) ?: [];
    unset($admin_details['password_hash'], $admin_details['otp_code'], $admin_details['otp_expiry']);

    $profileImageUrl = get_admin_profile_image_url($_SESSION['admin_id']);
    if ($profileImageUrl) {
        $admin_details['profile_image_url'] = $profileImageUrl;
    }
}

// Fetch data from database for display
if ($is_logged_in) {
    // Initialize all variables with defaults first
    $mock_products = [];
    $mock_orders = [];
    $mockAddresses = [];
    $mock_categories = [];
    $mock_transactions = [];
    $mock_shipments = [];
    $mock_customers = [];
    $mock_support_tickets = [];
    $mock_admin_users = [];
    $kpi_data = [
        'totalRevenue' => 0,
        'totalOrders' => 0,
        'lowStockCount' => 0,
        'newCustomers' => 0,
        'topProducts' => []
    ];

    // Fetch data individually with error handling
    try {
        $mock_products = get_products_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching products: " . $e->getMessage());
    }
    try {
        $mock_orders = get_orders_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching orders: " . $e->getMessage());
    }
    try {
        $kpi_data = get_dashboard_kpis($pdo);
    } catch (Exception $e) {
        error_log("Error fetching KPIs: " . $e->getMessage());
    }
    try {
        $mock_categories = get_categories_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching categories: " . $e->getMessage());
    }
    try {
        $mock_transactions = get_transactions_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching transactions: " . $e->getMessage());
    }
    try {
        $mock_shipments = get_shipments_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching shipments: " . $e->getMessage());
    }
    try {
        $mock_customers = get_customers_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching customers: " . $e->getMessage());
    }
    try {
        $mock_support_tickets = get_support_tickets_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching tickets: " . $e->getMessage());
    }
    try {
        $mock_admin_users = get_admin_users_list($pdo);
    } catch (Exception $e) {
        error_log("Error fetching admins: " . $e->getMessage());
    }
    try {
        $mockAddresses = get_customer_addresses($pdo);
    } catch (Exception $e) {
        error_log("Error fetching addresses: " . $e->getMessage());
    }
} else {
    $mock_products = [];
    $mock_orders = [];
    $mockAddresses = [];
    $mock_categories = [];
    $mock_transactions = [];
    $mock_shipments = [];
    $mock_customers = [];
    $mock_support_tickets = [];
    $mock_admin_users = [];
    $kpi_data = [];
}


// =========================================================================
// 4. CONDITIONAL HTML RENDERING (LOGIN/REGISTER/OTP PAGE or ADMIN DASHBOARD)
// =========================================================================
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../image/logo.png?v=3.5">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    if ($is_logged_in) {
        echo 'IMARKETPH  ADMIN PORTAL';
    } else if ($is_otp_page) {
        echo 'OTP Verification | IMARKETPH';
    } else if ($is_register_page) {
        echo 'Admin Registration | IMARKETPH';
    } else {
        echo 'Admin Login | IMARKETPH';
    }
    ?></title>
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin/dashboard.css">
    <style>

    </style>
</head>

<body>

    <!-- Sidebar / Navigation -->
    <?php include '../Components/Admin/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="main-content">
        <header class="main-header">
            <!-- Pangkalahatang Tanaw title area -->
            <div class="page-title-container">
                <!-- H1 is hidden on large screens to avoid layout duplication with H2 in content -->
                <h1 class="header-title" id="page-title">Dashboard & Analytics</h1>
            </div>

            <div class="header-right" style="flex: 1; justify-content: space-between;">
                <!-- Integrated Bar Style Search -->
                <div class="admin-search-container" style="position: relative; flex: 1; max-width: 600px; margin-left: 1rem; border-left: 1px solid #e2e8f0; padding-left: 1rem;">
                    <i data-lucide="search" style="position: absolute; left: 1.8rem; top: 50%; transform: translateY(-50%); width: 1.1rem; height: 1.1rem; color: #94a3b8; pointer-events: none;"></i>
                    <input type="text" placeholder="Search anything... (Orders, Products, Customers)" 
                        style="width: 100%; padding: 0.8rem 1rem 0.8rem 3.2rem; border: none; font-size: 0.95rem; background: transparent; transition: all 0.2s; outline: none; border-radius: 0;"
                        onfocus="this.style.background='rgba(248, 250, 252, 0.5)';"
                        onblur="this.style.background='transparent';">
                    
                    <!-- AI Assistant Button -->
                    <button onclick="openAdminAiChat()" title="Ask AI Assistant"
                        style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'"
                        onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">
                        <i data-lucide="sparkles" style="width: 1rem; height: 1rem; color: #8b5cf6;"></i>
                        AI Assistant
                    </button>
                </div>

                <div style="display: flex; align-items: center; gap: 1rem; margin-left: auto;">
                    <!-- Notification Bell -->
                    <div class="notification-bell-container" style="position: relative;">
                        <div class="notification-bell" onclick="toggleNotificationPanel()"
                            style="width: 2.8rem; height: 2.8rem; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; position: relative;"
                            onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                            <i data-lucide="bell"
                                style="width: 1.4rem; height: 1.4rem; color: #64748b; pointer-events: none;"></i>
                            <span id="notificationBellBadge" class="notification-bell-badge"
                                style="display: none; position: absolute; top: 0px; right: 0px; background: #ef4444; color: white; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); pointer-events: none; z-index: 1;"></span>
                        </div>

                    <!-- Notification Dropdown Panel -->
                    <div id="notificationPanel" class="notification-panel"
                        style="display: none; position: absolute; top: calc(100% + 12px); right: 0; width: 380px; max-height: 500px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; z-index: 1000; overflow: hidden;">
                        <div
                            style="padding: 16px 20px; border-bottom: 2px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);">
                            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;">Notifications</h3>
                            <button onclick="markAllAsRead()"
                                style="background: none; border: none; color: #3b82f6; font-size: 13px; font-weight: 600; cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='none'">
                                Mark all as read
                            </button>
                        </div>

                        <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                            <div style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                                <i data-lucide="inbox"
                                    style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                                <p style="margin: 0; font-size: 14px;">No new notifications</p>
                            </div>
                        </div>

                        <div
                            style="padding: 12px 20px; border-top: 1px solid #f1f5f9; text-align: center; background: #fafbfc;">
                            <a href="#"
                                onclick="showModule('alerts', this); toggleNotificationPanel(); event.preventDefault();"
                                style="color: #3b82f6; font-size: 13px; font-weight: 600; text-decoration: none; transition: color 0.2s;"
                                onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#3b82f6'">
                                View All Notifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="profile-menu-container" tabindex="0">
                    <div class="profile-avatar"
                        style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 0.2s; border: 2px solid rgba(255, 255, 255, 0.2);">
                        <?php echo strtoupper(substr($admin_username, 0, 1)); ?>
                    </div>

                    <div class="profile-dropdown">
                        <div
                            style="padding: 0.75rem 1rem; color: #1f2937; font-weight: 600; border-bottom: 1px solid #f9fafb;">
                            <?php echo $admin_username; ?>
                            <span class="text-xs font-medium"
                                style="display: block; color: var(--color-indigo-600); margin-top: 0.25rem;"><?php echo $admin_role; ?></span>
                        </div>
                        <a href="#" onclick="showSubModule('user', 'profile'); event.preventDefault();">
                            <i data-lucide="user-cog" style="width: 1.1rem; height: 1.1rem; margin-right: 0.75rem;"></i>
                            Admin Profile
                        </a>
                        <div class="divider"></div>
                        <a href="logout.php" style="color: var(--color-red-600);">
                            <i data-lucide="log-out" style="width: 1.1rem; height: 1.1rem; margin-right: 0.75rem;"></i>
                            Log Out
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Module Content Container -->
        <div id="content-container">
            <!-- Dynamic content will be loaded here -->
        </div>
    </main>

    <!-- Custom Modal/Message Box (Replaces alert()/confirm()) -->
    <div id="custom-modal-backdrop" class="modal-backdrop hidden">
        <div id="modal-container" class="modal-content">
            <!-- Content dynamically injected here -->
        </div>
    </div>

    <script>
        // Use PHP to safely inject the addresses array
        const mockAddresses = <?php echo json_encode($mockAddresses); ?>;

        // Inject data from PHP
        const kpiData = <?php echo json_encode($kpi_data); ?>;
        const productsData = <?php echo json_encode($mock_products); ?>;
        const ordersData = <?php echo json_encode($mock_orders); ?>;
        const adminDetails = <?php echo json_encode(!empty($admin_details) ? $admin_details : new stdClass()); ?>;
        const categoriesData = <?php echo json_encode($mock_categories); ?>;
        const transactionsData = <?php echo json_encode($mock_transactions); ?>;
        const shipmentsData = <?php echo json_encode($mock_shipments); ?>;
        const customersData = <?php echo json_encode($mock_customers); ?>;
        const supportTicketsData = <?php echo json_encode($mock_support_tickets); ?>;
        const adminUsersData = <?php echo json_encode($mock_admin_users); ?>;

        const formatCurrency = (amount) => {
            return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount);
        };

        // Additional configuration for JS
        const adminConfig = {
            username: "<?php echo $admin_username; ?>",
            role: "<?php echo $admin_role; ?>",
            currentAdminId: <?php echo $_SESSION['admin_id'] ?? 0; ?>
        };

        // Update chat notification badges
        async function updateChatNotifications() {
            try {
                const response = await fetch('get_unread_chat_count.php');
                const data = await response.json();

                if (data.success && data.unread_count > 0) {
                    const mainBadge = document.getElementById('chatNotificationBadge');
                    const subBadge = document.getElementById('chatSubNotificationBadge');

                    if (mainBadge) {
                        mainBadge.textContent = data.unread_count;
                        mainBadge.style.display = 'inline-block';
                    }
                    if (subBadge) {
                        subBadge.textContent = data.unread_count;
                        subBadge.style.display = 'inline-block';
                    }
                } else {
                    const mainBadge = document.getElementById('chatNotificationBadge');
                    const subBadge = document.getElementById('chatSubNotificationBadge');
                    if (mainBadge) mainBadge.style.display = 'none';
                    if (subBadge) subBadge.style.display = 'none';
                }
            } catch (error) {
                console.error('Error fetching chat notifications:', error);
            }
        }

        // Update notifications on page load and every 10 seconds
        updateChatNotifications();
        setInterval(updateChatNotifications, 10000);

        // Notification Panel Functions
        function toggleNotificationPanel() {
            const panel = document.getElementById('notificationPanel');
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                loadNotifications();
                lucide.createIcons(); // Refresh icons
            } else {
                panel.style.display = 'none';
            }
        }

        async function loadNotifications() {
            try {
                const response = await fetch('get_notifications.php');
                const data = await response.json();

                const notificationList = document.getElementById('notificationList');
                const bellBadge = document.getElementById('notificationBellBadge');
                if (bellBadge) bellBadge.style.pointerEvents = 'none'; // Fix: Badge shouldn't block clicks

                if (data.success && data.notifications.length > 0) {
                    notificationList.innerHTML = '';
                    let unreadCount = 0;

                    data.notifications.forEach(notif => {
                        if (!notif.is_read) unreadCount++;

                        const notifDiv = document.createElement('div');
                        notifDiv.style.cssText = `
                            padding: 14px 20px;
                            border-bottom: 1px solid #f1f5f9;
                            cursor: pointer;
                            transition: background 0.2s;
                            ${!notif.is_read ? 'background: #eff6ff;' : 'background: white;'}
                        `;
                        notifDiv.onmouseover = function () { this.style.background = '#f8fafc'; };
                        notifDiv.onmouseout = function () { this.style.background = notif.is_read ? 'white' : '#eff6ff'; };

                        const iconColor = notif.type === 'chat' ? '#3b82f6' : notif.type === 'order' ? '#8b5cf6' : notif.type === 'review' ? '#ffc107' : '#f59e0b';
                        const iconName = notif.type === 'chat' ? 'message-circle' : notif.type === 'order' ? 'shopping-cart' : notif.type === 'review' ? 'star' : 'alert-circle';

                        notifDiv.innerHTML = `
                            <div style="display: flex; gap: 12px; align-items: start;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: ${iconColor}15; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i data-lucide="${iconName}" style="width: 20px; height: 20px; color: ${iconColor};"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 14px; font-weight: ${!notif.is_read ? '600' : '500'}; color: #1e293b; margin-bottom: 4px; line-height: 1.4;">
                                        ${notif.title}
                                    </div>
                                    <div style="font-size: 13px; color: #64748b; line-height: 1.4; margin-bottom: 6px;">
                                        ${notif.message}
                                    </div>
                                    <div style="font-size: 12px; color: #94a3b8;">
                                        ${notif.time_ago}
                                    </div>
                                </div>
                                ${!notif.is_read ? '<div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; flex-shrink: 0; margin-top: 6px;"></div>' : ''}
                            </div>
                        `;

                        notifDiv.onclick = function () {
                            if (notif.type === 'chat') {
                                showSubModule('support', 'chat');
                                toggleNotificationPanel();
                            } else {
                                showNotificationDetails(notif);
                            }
                        };

                        notificationList.appendChild(notifDiv);
                    });

                    lucide.createIcons();

                    if (unreadCount > 0) {
                        bellBadge.textContent = unreadCount;
                        bellBadge.style.display = 'inline-block';
                    } else {
                        bellBadge.style.display = 'none';
                    }
                } else {
                    notificationList.innerHTML = `
                        <div style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                            <p style="margin: 0; font-size: 14px;">No new notifications</p>
                        </div>
                    `;
                    lucide.createIcons();
                    bellBadge.style.display = 'none';
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }

        function showNotificationDetails(notif) {
            const modal = document.getElementById('custom-modal-backdrop');
            const container = document.getElementById('modal-container'); let content = '';
            if (notif.type === 'review') {
                content = `
                    <div style="text-align: left;">
                        <h3 style="color: #1e293b; margin-top: 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="star" style="color: #ffc107; fill: #ffc107;"></i> Product Review
                        </h3>
                        <p style="color: #64748b; font-size: 14px;"><strong>${notif.title}</strong></p>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 15px 0;">
                            "${notif.message}"
                        </div>
                        <p style="color: #94a3b8; font-size: 12px; text-align: right;">${notif.time_ago}</p>
                        <div style="margin-top: 20px; text-align: right;">
                            <button onclick="closeCustomModal()" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer;">Close</button>
                        </div>
                    </div>
                `;
            } else if (notif.type === 'support') {
                content = `
                    <div style="text-align: left;">
                        <h3 style="color: #1e293b; margin-top: 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="life-buoy" style="color: #f59e0b;"></i> Support Ticket
                        </h3>
                        <p style="color: #64748b; font-size: 14px;"><strong>${notif.message}</strong></p>
                        <p style="color: #94a3b8; font-size: 12px;">Submitted ${notif.time_ago}</p>
                        <div style="margin-top: 20px; text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                            <button onclick="window.location.href='?module=support&submodule=tickets'" style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer;">View Tickets</button>
                            <button onclick="closeCustomModal()" style="padding: 8px 16px; background: #e2e8f0; color: #475569; border: none; border-radius: 6px; cursor: pointer;">Close</button>
                        </div>
                    </div>
                `;
            } else {
                content = `
                    <div style="text-align: center;">
                        <h3 style="color: #1e293b; margin-top: 0;">${notif.title}</h3>
                        <p style="color: #64748b;">${notif.message}</p>
                        <button onclick="closeCustomModal()" style="margin-top: 15px; padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer;">Close</button>
                    </div>
                `;
            }

            container.innerHTML = content;
            modal.classList.remove('hidden');
            lucide.createIcons();
            toggleNotificationPanel(); // Close the dropdown
        }

        function closeCustomModal() {
            const modal = document.getElementById('custom-modal-backdrop');
            modal.classList.add('hidden');
        }

        async function markAllAsRead() {
            try {
                const response = await fetch('mark_notifications_read.php', { method: 'POST' });
                const data = await response.json();
                if (data.success) {
                    loadNotifications();
                }
            } catch (error) {
                console.error('Error marking notifications as read:', error);
            }
        }

        // Close notification panel when clicking outside
        document.addEventListener('click', function (event) {
            const panel = document.getElementById('notificationPanel');
            const bellContainer = document.querySelector('.notification-bell-container');

            if (panel && bellContainer && !bellContainer.contains(event.target)) {
                panel.style.display = 'none';
            }
        });

        // Load notifications on page load
        loadNotifications();
        setInterval(loadNotifications, 30000); // Refresh every 30 seconds
    </script>
    <script src="../javascript/admin/Dashboard.js?v=<?php echo time(); ?>"></script>
</body>

</html>
